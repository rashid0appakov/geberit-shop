<?php
include_once(__DIR__.'/_header.php');

function ipr($str = '')
{
	static $i, $time_end, $time_start;
	$i ++;
	if(!$str)
	{
		$str = 'step-'.$i;
	}
	if(!$time_start)
	{
		$time_start = microtime(true);
	}
	$time_step = $time_all = 0;
	if(!$time_end)
	{
		$time_end = $time_start;
	}
	else
	{
		$time_current = microtime(true);
		$time_step = $time_current - $time_end;
		$time_step = round($time_step, 4);
		$time_end = $time_current;
		$time_all = $time_current - $time_start;
		$time = $time_end - $time_start;
		$time_all = round($time_all, 4);
	}
	echo $str.($time_step ? ' (затрачено '.$time_step.($time_all != $time_step ? ', всего '.$time_all : '').')' : '').';'."\n";
}

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
$ibp = new CIBlockProperty;
$el = new CIBlockElement;

set_time_limit(3000);

if(!defined('CATALOG_IBLOCK_ID') or CATALOG_IBLOCK_ID != 50)
{
	die('No IBLOCK_ID');
}

ipr('Start');

$arSections = $arParents = [];

$arSelect = [
    "ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "UF_SORT_PRICE", "UF_PRETTY_MIN", "UF_PRETTY_MAX", "UF_PRETTY_SORT"
];
$arFilter = [
    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
];

$obSections = CIBlockSection::GetTreeList($arFilter, $arSelect);
while($arSection = $obSections->GetNext())
{
	$sort = $arSection['UF_PRETTY_SORT'];
	if(!$sort)
	{
		$sort = 500;
	}
	if($sort >= 500)
	{
		$sort += 10000000;
	}
	$parent = intval($arSection['IBLOCK_SECTION_ID']);
	$arSections[$arSection['ID']] = [
		'NAME' => $arSection['NAME'],
		'SORT' => $sort,
		'ID' => $arSection['ID'],
		'DEPTH_LEVEL' => $arSection['DEPTH_LEVEL'],
		'SORT_PRICE' => $arSection['UF_SORT_PRICE'],
		'MIN_PRICE' => intval($arSection['UF_PRETTY_MIN']),
		'MAX_PRICE' => intval($arSection['UF_PRETTY_MAX']),
		'PARENT' => $parent,
	];
	$arParents[$parent][] = $arSection['ID'];
}

ipr();

$GLOBALS['FIX_SORT'] = 1;
customRecSort($arSections, $arParents, 0);
function customRecSort(&$arSections, $arParents, $id)
{
	$sort = [];
	foreach($arSections as $arSection)
	{
		if($arSection['PARENT'] == $id)
		{
			$sort[] = $arSection['SORT'];
		}
	}
	array_multisort($sort, SORT_ASC, SORT_NUMERIC, $arParents[$id]);

	foreach($arParents[$id] as $parent)
	{
		customRecSort($arSections, $arParents, $parent);
		if($arSections[$parent]['SORT'] < 10000000)
		{
			$GLOBALS['FIX_SORT'] ++;
			$arSections[$parent]['GROUP'] = $GLOBALS['FIX_SORT'];
		}
		else
		{
			$arSections[$parent]['GROUP'] = 0;
		}
	}
}
ipr();

$arGroups = $arGroupsPriceSort = [];
foreach($arSections as $arSection)
{
	$arGroups[$arSection['ID']] = $arSection['GROUP'];
}
$arGroups[0] = max($arGroups) + 1;

//pr($arGroups);

$arSelect = array(
	'ID',
	'IBLOCK_ID',
	'ACTIVE',
	'IBLOCK_SECTION_ID',
	'PROPERTY_PRIORITY',
	'PROPERTY_MANUFACTURER',
	'CATALOG_GROUP_1',
);
$arFilter = [
	'IBLOCK_ID' => CATALOG_IBLOCK_ID,
];
$ob = CIBlockElement::GetList(
	array(),
	$arFilter,
	false,
	false,
	$arSelect
);
$arGroupItems = $arCheck = [];
$i = 0;
while($arItem = $ob->GetNext())
{
	if(isset($arCheck[$arItem['ID']]))
	{
		continue;
	}
	
	$arCheck[$arItem['ID']] = $arItem['ID'];
	$i ++;
	if($i > 10)
	{
//		break;
	}
//	pr($arItem);
	$sec = intval($arItem['IBLOCK_SECTION_ID']);
	$price = intval($arItem['CATALOG_PRICE_1']);
	$group = 0;
	if($arItem['ACTIVE'] == 'Y')
	{
		if($arItem['PROPERTY_PRIORITY_VALUE'] > 0)
		{
			$group = 1;
			if(
				$arItem['CATALOG_QUANTITY'] < 2
			)
			{
				$group = 0;
			}
		}
		else
		{
			$group = $arGroups[$sec];
			if(
				$arItem['CATALOG_QUANTITY'] < 1 or 
				($arSections[$sec]['MIN_PRICE'] > 0 and $price < $arSections[$sec]['MIN_PRICE']) or 
				($arSections[$sec]['MAX_PRICE'] > 0 and $price > $arSections[$sec]['MAX_PRICE'])
			)
			{
				$group = 0;
			}
		}
	}
	$key = 1;
	$brand = intval($arItem['PROPERTY_MANUFACTURER_VALUE']);
	if(
		$group and 
		!$arSections[$sec]['SORT_PRICE'] and 
		(!$brand or !$GLOBALS['PAGE_DATA']['INFO_BRAND'][$brand]['PRIORITY'])
	)
	{
		$key = 2;
	}
	if(!$group)
	{
		$group = $arGroups[0];
	}
	elseif($sec and $arSections[$sec]['SORT_PRICE'])
	{
		$arGroupsPriceSort[$group] = ['CATALOG_PRICE_1' => ($arSections[$sec]['SORT_PRICE'] == 93 ? 'ASC' : 'DESC')];
	}
	$arGroupItems[$group][$key][$arItem['ID']] = $arItem['ID'];
	$DB->Query('UPDATE `b_iblock_element_prop_s50` SET `PROPERTY_6019`='.intval($group).', `PROPERTY_6020`='.intval($key).' WHERE `IBLOCK_ELEMENT_ID`='.$arItem['ID']);
	//CIBlockElement::SetPropertyValuesEx($arItem["ID"], false, ['SORT_GROUP' => $group, 'SORT_GROUP_SUB' => $key]);
}
//pr($arSections);
//pr($arGroupsPriceSort); die();
ksort($arGroupItems);
//pr($arGroupItems);

ipr('Set Groups');

$index = 1;
foreach($arGroupItems as $group=>$arSub)
{
	ksort($arSub);
	foreach($arSub as $keySub=>$arIds)
	{
		$arSelect = array(
			'ID',
			'IBLOCK_ID',
			'IBLOCK_SECTION_ID',
			'PROPERTY_PRIORITY',
			'PROPERTY_MANUFACTURER',
		);
		$arFilter = [
			'IBLOCK_ID' => CATALOG_IBLOCK_ID,
			'PROPERTY_SORT_GROUP' => $group,
			'PROPERTY_SORT_GROUP_SUB' => $keySub,
		];
		if($group == 1)
		{
			$sort = ['PROPERTY_PRIORITY' => 'DESC'];
		}
		elseif(isset($arGroupsPriceSort[$group]))
		{
			$sort = $arGroupsPriceSort[$group];
		}
		else
		{
			$sort = ['RAND' => 'ASC'];
		}
		$ob = CIBlockElement::GetList(
			$sort,
			$arFilter,
			false,
			false,
			$arSelect
		);
		$arElements = [];
		while($arItem = $ob->GetNext())
		{
			$arElements[$arItem['ID']] = $index;
			$index ++;
		}
		foreach($arElements as $elementId=>$elementSort)
		{
			$DB->Query('UPDATE `b_iblock_element` SET `SORT`='.intval($elementSort).' WHERE `ID`='.$elementId);
			//$el->Update($elementId, ['SORT' => $elementSort]);
		}
	}
}

ipr('End');

?>