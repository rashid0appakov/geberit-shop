<?php
include_once(__DIR__.'/_header.php');

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
$ibp = new CIBlockProperty;

set_time_limit(86400);

if(!defined('CATALOG_IBLOCK_ID'))
{
	die('No IBLOCK_ID');
}
else
{
	$IBLOCK_ID = CATALOG_IBLOCK_ID;
}

file_put_contents(
	$_SERVER['DOCUMENT_ROOT'].'/local/log/set_any_items_'.$IBLOCK_ID.'.log', 
	''
);

$arSelect = array(
	'ID',
	'IBLOCK_ID',
	'PROPERTY_SERIES',
	'PROPERTY_MANUFACTURER',
	'PROPERTY_MATERIAL_ARMATURY_TSVET_ARMATURY',
	'PROPERTY_PARAMETRY_PLAFONA_TSVET_PLAFONOV',
	'PROPERTY_PLAFONA_FORMA_PLAFONA',
	'PROPERTY_PARAMETRY_PLAFONA_KOLICHESTVO_PLAFONOV',
	'PROPERTY_STIL',
	'PROPERTY_VID_SVETILNIKA',
);
foreach($GLOBALS['customCacheProps'] as $prop1=>$prop2)
{
	$arSelect[] = 'PROPERTY_'.$prop1;
	$arSelect[] = 'PROPERTY_'.$prop2;
}
$arMainFilter = [
	'IBLOCK_ID' => $IBLOCK_ID
];
if(!empty($argv[2]))
{
	$arMainFilter['ID'] = intval($argv[2]);
}
//pr($arMainFilter);
//Достаем все товары
$ob = CIBlockElement::GetList(
	array(),
	$arMainFilter,
	false,
	false,
	$arSelect
);
$arCheck = [];
$n = 0;
while($item = $ob->GetNext())
{
	if(isset($arCheck[$item['ID']]))
	{
		continue;
	}
	$arCheck[$item['ID']] = $item['ID'];
	//pr($item);
	$n ++;
	if($n == 1)
	{
		foreach($GLOBALS['customCacheProps'] as $prop1=>$prop2)
		{
			if(!array_key_exists('PROPERTY_'.$prop2.'_VALUE', $item))
			{
				$arFields = Array(
					"NAME" => "Кеш ".$prop2,
					"ACTIVE" => "Y",
					"SORT" => "999999",
					"CODE" => $prop2,
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $IBLOCK_ID,
				);
				$ibp->Add($arFields);				
			}
		}
	}
	
	$productId = $item['ID'];
	
	$arProps = CustomUpdateProductCache($item, $IBLOCK_ID);


	file_put_contents(
		$_SERVER['DOCUMENT_ROOT'].'/local/log/set_any_items_'.$IBLOCK_ID.'.log', 
		json_encode(array('IBLOCK_ID' => $IBLOCK_ID, 'productId' => $productId, 'arProps' => $arProps), JSON_UNESCAPED_UNICODE).PHP_EOL,
		FILE_APPEND
	);	
}

?>