<?
// $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../..");
// $DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define("CUSTOM_USER_ID", 0);
define("CUSTOM_USER_GROUP", 2);
define("CONTACTS_IBLOCK_ID", 47);
define("PROMO_IBLOCK_ID", 18);
define("DELIVERY_IBLOCK_ID", 23);



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
	echo $str.($time_step ? ' (затрачено '.$time_step.($time_all != $time_step ? ', всего '.$time_all : '').')' : '').';<br>'."\n";
}

define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

ipr();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Catalog;

Loader::includeModule("iblock");
Loader::includeModule('asd.iblock');
Loader::includeModule("catalog");
Loader::includeModule("sale");
Loader::includeModule("highloadblock");


define("CUSTOM_CACHE_FILE_PREFIX", $_SERVER["DOCUMENT_ROOT"].'/local/cache/'.SITE_ID.'-');

$GLOBALS['PAGE_DATA'] = [
	'IBLOCK_CODE' => [],
	'IBLOCK_TYPE' => [],
	'IBLOCK_TYPE_CAT' => [],
	'IBLOCK' => [],
	'MENU' => [],
	'CATALOG_SECTIONS_ALL' => [],
	'CATALOG_SECTIONS_MENU' => [],
	'PROMO' => [],
	'HL' => [],
	'HL_ID' => [],
	'HL_BLOCK' => [],
	'HL_TABLE' => [],
	'BRAND_ITEMS_COUNT' => [],
	'PRODUCT_ODS' => [],
	'DELIVERY_DATA' => [],
	'INFO_BRAND' => [],
	'INFO_SERIES' => [],
	'PRICE' => [],
	'PRICE_TYPE' => [],
	'LIST_PROP' => [],
	'ENUM_LIST_PROP' => [],
	'SHOW_PROPERTIES' => [],
	'SEO_FILTER' => [],
	'CONFIG' => [],
];

$dbPriceType = CCatalogGroup::GetList(
        [
			"SORT" => "ASC"
		],
        []
    );
while ($arPriceType = $dbPriceType->Fetch())
{
	$GLOBALS['PAGE_DATA']['PRICE'][$arPriceType['ID']] = $arPriceType;
	$GLOBALS['PAGE_DATA']['PRICE_TYPE'][$arPriceType['NAME']] = $arPriceType['ID'];
}

ipr('Типы цен');

$resIBlock = CIBlock::GetList(Array(), Array('ACTIVE'=>'Y'), true);
while($arIBlock = $resIBlock->Fetch())
{
	$arFields = CASDiblockTools::GetIBUF($arIBlock['ID']);
	$arIBlock += $arFields;
	$GLOBALS['PAGE_DATA']['IBLOCK'][$arIBlock['ID']] = $arIBlock;
	$GLOBALS['PAGE_DATA']['IBLOCK_TYPE'][$arIBlock['IBLOCK_TYPE_ID']][] = $arIBlock['ID'];
	if($arIBlock['UF_CATALOG'])
	{
		$GLOBALS['PAGE_DATA']['IBLOCK_TYPE_CAT'][$arIBlock['IBLOCK_TYPE_ID']] = $arIBlock['ID'];
	}
	$GLOBALS['PAGE_DATA']['IBLOCK_CODE'][$arIBlock['CODE']] = $arIBlock['ID'];
}

ipr('Типы инфоблоков');

$files = scandir($_SERVER['DOCUMENT_ROOT']);
foreach($files as $file)
{
	if(strpos($file, '.menu.php') > 2)
	{
		$aMenuLinks = [];
		include($_SERVER['DOCUMENT_ROOT'].'/'.$file);
		list(,$key) = explode('.', $file, 3);
		$key = strtoupper($key);
		$GLOBALS['PAGE_DATA']['MENU'][$key]['ITEMS'] = $aMenuLinks;
	}
}

ipr('Считывание меню');

$arDPFilter = [
    'XML_ID' => 'UF_DISPLAY_PARAMS',
    'ENTITY_ID' => 'IBLOCK_'.CATALOG_IBLOCK_ID.'_SECTION'
];

$arDPUF = [];

$rsData = CUserTypeEntity::GetList([], $arDPFilter);
if($arRes = $rsData->Fetch())
{
    $DPID   = $arRes['ID'];

    $obEnum = new \CUserFieldEnum;
    $ufFields = $obEnum->GetList(['SORT' => 'ASC'], ['USER_FIELD_ID' => $DPID]);
    while($arProp = $ufFields->Fetch())
	{
        $arDPUF[$arProp['ID']] = $arProp;
	}
}

$arSelect = [
    "ID", "PICTURE", "IBLOCK_ID", "NAME", "SECTION_PAGE_URL", "CODE", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "UF_*"
];
$arFilter = [
    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
    "ACTIVE" => "Y",
    "GLOBAL_ACTIVE" => "Y",
	"ELEMENT_SUBSECTIONS" => "Y",
	"CNT_ACTIVE" => "Y"
];

$resItems = CIBlockSection::GetList(['LEFT_MARGIN' => 'ASC'], $arFilter, true, $arSelect);
while($arItem = $resItems->GetNext())
{
    if($arItem['UF_DISPLAY_PARAMS'])
	{
        foreach($arItem['UF_DISPLAY_PARAMS'] AS &$propID)
		{
            $arItem['DISPLAY_PARAMS'][] = $arDPUF[$propID]['XML_ID'];
		}
	}

	$arItem['RESIZED'] = [];
	if ($arItem['PICTURE'])
	{
		$arItem['RESIZED'][] =
			CFile::ResizeImageGet(
				CFile::GetFileArray($arItem['PICTURE']),
				array('width' => 178, 'height' => 178),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
	}

	$arSelect   = [
		"ID", "IBLOCK_ID", "DETAIL_PICTURE", "IBLOCK_SECTION_ID"
	];
	$arFilter   = [
		'IBLOCK_ID' => CATALOG_IBLOCK_ID,
		'SECTION_ID' => $arItem['ID'],
		'ACTIVE' => 'Y'
	];

	$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 10), $arSelect);
	while ($arItem2 = $dbItems->GetNext())
	{
		$arItem['RESIZED'][] =
			CFile::ResizeImageGet(
				CFile::GetFileArray($arItem2["DETAIL_PICTURE"]),
				array('width' => 178, 'height' => 178),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
	}

    $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['ID']]  = $arItem;
	
	if($arItem['DEPTH_LEVEL'] <= 2)
	{
		$GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_MENU'][] = [
			"ID" => $arItem['ID'],
			"IBLOCK_ID" => $arItem['IBLOCK_ID'],
			"DEPTH_LEVEL" => $arItem['DEPTH_LEVEL'],
			"NAME" => $arItem['NAME'],
			"SECTION_PAGE_URL" => $arItem['SECTION_PAGE_URL'],
			"UF_MENU_HIGHLIGHT" => $arItem['UF_MENU_HIGHLIGHT'],
		];
	}
}

$aMenuLinks = [];
$cnt = count($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_MENU']);
for($i = 0; $i < $cnt; $i++)
{
    $arSection = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_MENU'][$i]['ID']];
	
	$name = htmlspecialcharsbx($arSection["~NAME"]);
	if(!empty($arSection["UF_MENU_NAME"])){
		$name = $arSection["UF_MENU_NAME"];
	}
	
    $aMenuLinks[$i] = [
        $name,
        $arSection["~SECTION_PAGE_URL"],
        [],
        [
            "FROM_IBLOCK" => true,
            "ID" => $arSection["ID"],
            "IS_PARENT" => 0,
            "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
            "HIGHLIGHT" => !!$arSection["UF_MENU_HIGHLIGHT"],
        ]
    ];

    if($i > 0)
    {
        $previous = $rows[$i - 1];
        $aMenuLinks[$i - 1][3]["IS_PARENT"] = intval($arSection["DEPTH_LEVEL"] > $aMenuLinks[$i - 1][3]["DEPTH_LEVEL"]);
    }
}

ipr('Список разделов');

$ob = \CIBlockElement::GetList(
	array(),
	array(
		'IBLOCK_ID' => BRANDS_IBLOCK_ID,
		'ACTIVE' => 'Y'
	),
	false,
	false,
	array(
		'ID',
		'IBLOCK_ID',
		'NAME',
		'SORT',
		'CODE',
		'PROPERTY_OTHER_NAME',
		'PROPERTY_PRIORITY',
	)
);
while ($row = $ob->Fetch())
{
	$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['ID']]['NAME'] = $row['NAME'];
	$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['ID']]['SORT'] = $row['SORT'];
	$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['ID']]['CODE'] = $row['CODE'];
	$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['ID']]['OTHER_NAME'] = $row['PROPERTY_OTHER_NAME_VALUE'];
	$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['ID']]['PRIORITY'] = ($row['PROPERTY_PRIORITY_VALUE'] == 'Y' ? 1 : 0);
}

ipr('Кеширование брендов');

global $man_show;

$ob = \CIBlockElement::GetList(
	array(),
	array(
		'IBLOCK_ID' => SERIES_IBLOCK_ID,
		"PROPERTY_MANUFACTURER" => $man_show
//		'ACTIVE' => 'Y'
	),
	false,
	false,
	array(
		'ID',
		'IBLOCK_ID',
		'NAME',
		'SORT',
		'CODE',
		'PROPERTY_BRAND'
	)
);
while ($row = $ob->Fetch())
{
	$GLOBALS['PAGE_DATA']['INFO_SERIES'][$row['ID']]['NAME'] = $row['NAME'];
	$GLOBALS['PAGE_DATA']['INFO_SERIES'][$row['ID']]['SORT'] = $row['SORT'];
	$GLOBALS['PAGE_DATA']['INFO_SERIES'][$row['ID']]['CODE'] = $row['CODE'];
	if($row['PROPERTY_BRAND_VALUE'])
	{
		$GLOBALS['PAGE_DATA']['INFO_SERIES'][$row['ID']]['BRAND'] = $row['PROPERTY_BRAND_VALUE'];
		$GLOBALS['PAGE_DATA']['INFO_BRAND'][$row['PROPERTY_BRAND_VALUE']]['SERIES'][] = $row['ID'];
	}
}

// echo "<pre>";
// var_dump($GLOBALS['PAGE_DATA']['INFO_SERIES']);
// echo "</pre>";

ipr('Кеширование коллекций');

$GLOBALS['PAGE_DATA']['MENU']['CATALOG']['PROMO'] = [];

$sectionIds = [];
if(!empty($aMenuLinks))
{
	foreach($aMenuLinks as $arItem)
	{
		$sectionId = $arItem[3]["ID"];
		if(!!$sectionId && !in_array($sectionId, $sectionIds))
		{
			$sectionIds[] = $sectionId;
		}
	}
}

$arSelect   = [
	"ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_PICTURE", "PROPERTY_SECTION", "PROPERTY_URL", "PROPERTY_NEW_TAB", "PROPERTY_PRODUCT", "PROPERTY_MANUFACTURER", "DETAIL_PAGE_URL"
];
$arFilter   = [
	"IBLOCK_ID"	 => CATALOG_IBLOCK_ID,
	"IBLOCK_TYPE"   => CATALOG_IBLOCK_TYPE,
	//"ID" => $IDS,
	"ACTIVE" => "Y",
	"!PROPERTY_SALELEADER" => false
];
$arOrder =  ["SORT" => "RAND", "ID" => "ASC"];
$ob = CIBlockElement::GetList($arOrder, $arFilter, false, ['nPageSize' => 1000], $arSelect);
while ($arItem = $ob->GetNext())
{
	$arItem["DETAIL_PICTURE"] = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 70, "height" => 70));

	if (count($GLOBALS['PAGE_DATA']['MENU']['CATALOG']["PROMO"][getParent($arItem['IBLOCK_SECTION_ID'], $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'])]) > 5)
	{
		continue;
	}
	$arItem['PRICE'] = getFinalPriceInCurrency($arItem);
	$arItem['CODE_BRAND'] = $GLOBALS['PAGE_DATA']['INFO_BRAND'][$arItem['PROPERTY_MANUFACTURER_VALUE']]['CODE'];

	$GLOBALS['PAGE_DATA']['MENU']['CATALOG']["PROMO"][getParent($arItem['IBLOCK_SECTION_ID'], $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'])][] = $arItem;
}

$GLOBALS['PAGE_DATA']['MENU']['CATALOG']['ITEMS'] = array_merge($GLOBALS['PAGE_DATA']['MENU']['CATALOG']['ITEMS'], $aMenuLinks);

$GLOBALS['PAGE_DATA']['MENU']['CATALOG']["ITEMS_CHUNK"] = array_chunk($GLOBALS['PAGE_DATA']['MENU']['CATALOG']["ITEMS"], 70);

ipr('Промо-товары');

if(defined('PROMOTIONS_SITE_PROPERTY_ID') and PROMOTIONS_SITE_PROPERTY_ID)
{
	$rsEnum = \Bitrix\Iblock\PropertyEnumerationTable::getList([
		'filter' => [
			'PROPERTY_ID'=> PROMOTIONS_SITE_PROPERTY_ID,
		]
	]);

	$filterVal = '';
	while($arEnum = $rsEnum->fetch())
	{
		if($arEnum['XML_ID'] == SITE_ID)
		{
			$filterVal = $arEnum['VALUE'];
			break;
		}
	}

	$arSelect   = [
		"ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_MAIN_TYPE_ADMIN"
	];
	if($filterVal)
	{
		$arFilter = [
			"PROPERTY_MAIN_TYPE_ADMIN_VALUE" => $filterVal,
			"IBLOCK_ID" => PROMO_IBLOCK_ID,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
		];

		$arOrder =  ["SORT" => "ASC", "DATE_ACTIVE_FROM" => "DESC"];
		$ob = CIBlockElement::GetList($arOrder, $arFilter, false, ['nPageSize' => 10], $arSelect);
		while ($arItem = $ob->GetNext())
		{
			$GLOBALS['PAGE_DATA']['PROMO'][] = $arItem;
		}
	}
	
	ipr('Акции магазина');
}

$arSelect = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CODE"];
$arFilter = [
    "IBLOCK_ID" => CONTACTS_IBLOCK_ID,
    "ACTIVE" => "Y",
    "PROPERTY_SITE_ID"  => SITE_ID
];

$rsElement = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, ['nPageSize' => 20], $arSelect);
while($obItem = $rsElement->GetNextElement())
{
    $arItem = $obItem->GetFields();
    $arProps = $obItem->GetProperties();
    $arSchedule = [];
    if (!empty($arProps['SCHEDULE']['VALUE']))
	{
        foreach($arProps['SCHEDULE']['VALUE'] AS $key => &$item)
		{
            $arSchedule[($key + 1)] = [
                'NAME'  => $item,
                'VALUE' => $arProps['SCHEDULE']['DESCRIPTION'][$key]
            ];
		}
	}

    //$regionID   = (is_array($arProps['LOCATION']['VALUE']) ? join(',',$arProps['LOCATION']['VALUE']) : $arProps['LOCATION']['VALUE']);

    foreach($arProps['LOCATION']['VALUE'] AS $rID)
	{
		if($rID==768){
			$arProps['LOCATION']['VALUE'][] = '817';
		}
        $GLOBALS['PAGE_DATA']['LOCATION'][$rID] = [
            'ID' => $arItem['ID'],
            'NAME' => $arItem['NAME'],
            'LINK' => $arItem['DETAIL_PAGE_URL'],
            'SCHEDULE' => $arSchedule,
            'LOCATION_ID' => $arProps['LOCATION']['VALUE'],
            'TIME_ZONE' => $arProps['TIME_ZONE']['VALUE'],
            'PHONE' => $arProps['PHONE']['VALUE'],
			'CLASS_ROISTAT' => $arProps['CLASS_ROISTAT']['VALUE'],
            'ADD_PHONE' => $arProps['ADD_PHONE']['VALUE'],
            'CODE' => $arItem['CODE'],
            'ADRESS' => is_array($arProps['ADRESS']['VALUE']) ? $arProps['ADRESS']['~VALUE']['TEXT'] : $arProps['ADRESS']['VALUE'],
            'STOCK' => is_array($arProps['TIME_SKLAD']['VALUE']) ? $arProps['TIME_SKLAD']['~VALUE']['TEXT'] : $arProps['TIME_SKLAD']['VALUE'],
            'MAPS' => $arProps['MAPS']['VALUE'],
            'SKLAD' => is_array($arProps['SKLAD']['VALUE']) ? $arProps['SKLAD']['~VALUE']['TEXT'] : $arProps['SKLAD']['VALUE'],
        ];
	}
	echo "<pre>";
	var_dump($GLOBALS['PAGE_DATA']['LOCATION']);
	echo "</pre>";
}

ipr('Контакты магазинов');

$GLOBALS['PAGE_DATA']['SITE'] = CSite::GetByID(SITE_ID)->Fetch();

ipr('Опции сайта');


$hlList = HL\HighloadBlockTable::getList(); 
while($arBlock = $hlList->Fetch())
{
	if($arBlock['ID'] == 8)
	{
		continue;
	}
	
	$GLOBALS['PAGE_DATA']['HL_BLOCK'][$arBlock['ID']] = $arBlock;
	$GLOBALS['PAGE_DATA']['HL_TABLE'][$arBlock['TABLE_NAME']] = $arBlock['ID'];
	
	$hlblock = HL\HighloadBlockTable::getById($arBlock['ID'])->fetch(); 

	$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
	$entity_data_class = $entity->getDataClass(); 

	$rsData = $entity_data_class::getList([
		"select" => [
			"*"
		],
		"order" => [
			//"UF_SORT" => "ASC",
			"UF_NAME" => "ASC",
			"ID" => "ASC",
		]
	]);
	
	$GLOBALS['PAGE_DATA']['HL'][$arBlock['ID']] = $GLOBALS['PAGE_DATA']['HL_ID'][$arBlock['ID']] = [];
	while($arData = $rsData->Fetch())
	{
		foreach($arData as $key=>$value)
		{
			if(is_object($value) and method_exists($value, 'toString'))
			{
				$value = $value->toString();
			}
		}
		$GLOBALS['PAGE_DATA']['HL'][$arBlock['ID']][$arData['UF_XML_ID']] = $arData;
		$GLOBALS['PAGE_DATA']['HL_ID'][$arBlock['ID']][$arData['ID']] = $arData['UF_XML_ID'];
	}
}

$GLOBALS['PAGE_DATA']['PRODUCT_ODS'] = [];
foreach($GLOBALS['PAGE_DATA']['HL'][3] as $arItem)
{
	$fileUrl = CFile::GetPath($arItem["UF_FILE"]);

	$GLOBALS['PAGE_DATA']['PRODUCT_ODS'][$arItem["UF_XML_ID"]] = [
		"NAME"      => $arItem["UF_NAME"],
		"XML_ID"    => $arItem["UF_XML_ID"],
		"IMAGE"     => $fileUrl
	];
}	

$GLOBALS['PAGE_DATA']['NAME_COUNTRY'] = [];
foreach($GLOBALS['PAGE_DATA']['HL'][4] as $arItem)
{
	$GLOBALS['PAGE_DATA']['NAME_COUNTRY'][$arItem["UF_XML_ID"]] = $arItem["UF_NAME"];
}	

$GLOBALS['PAGE_DATA']['NAME_INTERIOR'] = [];
foreach($GLOBALS['PAGE_DATA']['HL'][6] as $arItem)
{
	$GLOBALS['PAGE_DATA']['NAME_INTERIOR'][$arItem["UF_XML_ID"]] = $arItem["UF_NAME"];
}	

ipr('Кеширование справочников');

$arSelect   = [
    "ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_MANUFACTURER"
];
$arFilter   = [
    'IBLOCK_ID' => CATALOG_IBLOCK_ID,
    'ACTIVE'    => "Y"
];

$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 50000), $arSelect);
while ($arItem = $dbItems->GetNext())
{
	if (!empty($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]))
	{
        if (!isset($GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT'][$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']])){
            $GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT'][$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']] = [
                'NAME'              => $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]['NAME'],
                'SECTION_PAGE_URL'  => $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']
            ] ;
        }
        $GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT'][$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']]['ITEMS_COUNT'] += 1;
    }
}

ipr('Кеширование количества брендов по разделам');

$arSelect   = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CODE"];
$arFilter = [
    "IBLOCK_ID" => DELIVERY_IBLOCK_ID,
    "ACTIVE"    => "Y",
    "PROPERTY_SITE_ID"  => SITE_ID
];

$rsElement = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, ['nPageSize' => 20], $arSelect);
while($obItem   = $rsElement->GetNextElement())
{
    $arItam = $obItem->GetFields();
    $arProps = $obItem->GetProperties();
	$regionsID = $arProps['LOCATION']['VALUE'] ? $arProps['LOCATION']['VALUE'] : 'OTHER';
	if(!is_array($regionsID))
	{
		$regionsID = [$regionsID];
	}
	foreach($regionsID as $regionID)
	{
		$GLOBALS['PAGE_DATA']['DELIVERY_DATA'][$regionID] = [
			'ID'        => $arItam['ID'],
			'NAME'      => $arItam['NAME'],
			'LINK'      => $arItam['DETAIL_PAGE_URL'],
			'LOCATION_ID'   => $arProps['LOCATION']['VALUE'],
			'CODE'      => $arItam['CODE']
		];
	}
}

ipr('Кеширование служб доставок');

$res = CIBlock::GetProperties(CATALOG_IBLOCK_ID, Array(), Array());
while($arItem = $res->Fetch())
{
	$GLOBALS['PAGE_DATA']['LIST_PROP'][$arItem['ID']] = $arItem;
}

ipr('Список свойств');

foreach($GLOBALS['PAGE_DATA']['LIST_PROP'] as $idProp => $arItem)
{
	if($arItem['PROPERTY_TYPE'] == 'L')
	{
		$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "CODE"=>$arItem['CODE']));
		while($enum_fields = $property_enums->GetNext())
		{
			$GLOBALS['PAGE_DATA']['LIST_PROP'][$idProp]['LIST_VALUES_CUSTOM'][$enum_fields['ID']] = $enum_fields;
			$GLOBALS['PAGE_DATA']['ENUM_LIST_PROP'][$enum_fields['ID']] = $idProp;
		}
	}
}

ipr('Значение свойств типа список');

$ex = '';
if(defined('MY_SYSTEM_PROPERTY'))
{
	$ex = MY_SYSTEM_PROPERTY;
}
$ex = COption::GetOptionString("zloj.properties", "ex_props_".CATALOG_IBLOCK_ID, $ex);
$arEx = explode(',', $ex);

$properties = CIBlockProperty::GetList(Array("SORT" => "ASC"), Array(
	"ACTIVE" => "Y",
	"IBLOCK_ID" => CATALOG_IBLOCK_ID
));
while ($arField = $properties->GetNext())
{
	if($arField['CODE'] and !in_array($arField['CODE'], $arEx))
	{
		if(strpos($arField['CODE'], "OLD_") !== 0 and strpos($arField['CODE'], "SYS_") !== 0 and strpos($arField['CODE'], "_CACHE") === false)
		{
			$GLOBALS['PAGE_DATA']['SHOW_PROPERTIES'][] = $arField['CODE'];
		}
		
	}
	if($arField['CODE'] == 'SERIES')
	{
		$GLOBALS['PAGE_DATA']['CONFIG']['SERIES'] = $arField['ID'];
	}
	if($arField['CODE'] == 'MANUFACTURER')
	{
		$GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'] = $arField['ID'];
	}
}

ipr('Кеширование свойств для отображения');

$configFields = [
	'UF_FILTER_URL_PAGE' => [
		'string',
		'URL страницы',
		'',
	],
	'UF_FILTER_TITLE' => [
		'string',
		'Title',
		'',
	],
	'UF_FILTER_HEADER' => [
		'string',
		'H1',
		'',
	],
	'UF_FILTER_KEYWORDS' => [
		'string',
		'Keywords',
		'',
	],
	'UF_FILTER_DESC' => [
		'string',
		'Description',
		'3',
	],
	'UF_FILTER_FOR_SEC' => [
		'iblock_section',
		'Раздел каталога',
		'',
	],
	'UF_BACKGROUND_IMAGE' => [
		'file',
		'Фон',
		'',
	],
];
$ncc = false;
$GLOBALS['PAGE_DATA']['SEO_FILTER']   = [
	'PAGE_SEO'  => [],
	'TAGS'	  => []
];
$arSelect   = [
	"ID", "IBLOCK_ID", "CODE", "IBLOCK_SECTION_ID", "SORT", "DESCRIPTION", "PICTURE", "NAME", "UF_*"
];
$arFilter   = Array(
	"IBLOCK_ID" => SEO_FILTER_IBLOCK_ID,
	"ACTIVE"	=> "Y",
	"GLOBAL_ACTIVE" => "Y",
);
$obResult = CIBlockSection::GetList(Array("left_margin"=>"asc"), $arFilter, false, $arSelect);;
$arParents = $arSections = [];
while($arFields = $obResult->GetNext())
{
	$code = $arFields["CODE"];
	$arFields["CODE_OLD"] = $arFields["CODE"];
	//берем урл раздела и плюсуем сим код
	$res = CIBlockSection::GetByID($arFields["UF_FILTER_FOR_SEC"]);
	if($ar_res = $res->GetNext())
	  $section_page_url = $ar_res['SECTION_PAGE_URL'];
		//собираем символьный код
		$code = '';
		$nav = CIBlockSection::GetNavChain(SEO_FILTER_IBLOCK_ID,$arFields['ID']);
		while($arSectionPath = $nav->GetNext()){
		   $code .= $arSectionPath["CODE"].'/';
		} 
		$code = $section_page_url.$code;

	$arFields["CODE"] = $code;
	$arSections[] = $arFields;

	$arParents[$arFields['ID']] = $code;
}
$i = 0;
foreach($arSections as $arFields)
{
	if(!$i)
	{
		$n = 0;
		$add = false;
		foreach($configFields as $code=>$arConf)
		{
			$n ++;
			if(!array_key_exists($code, $arFields))
			{
				$add = true;
				if(!isset($oUserTypeEntity))
				{
					$oUserTypeEntity = new CUserTypeEntity();
				}
				$aUserFields = [
					'ENTITY_ID' => 'IBLOCK_'.SEO_FILTER_IBLOCK_ID.'_SECTION',
					'FIELD_NAME' => $code,
					'USER_TYPE_ID' => $arConf[0],
					'SORT' => $n,
					'MULTIPLE' => 'N',
					'MANDATORY' => 'N',
					'SHOW_FILTER' => 'S',
					'IS_SEARCHABLE' => 'N',
					'SETTINGS' => [
						'SIZE' => '60',
						'ROWS' => ($arConf[2] ? : 1),
					],
					'EDIT_FORM_LABEL' => [
						'ru' => $arConf[1],
					],
					'LIST_COLUMN_LABEL' => [
						'ru' => $arConf[1],
					],
					'LIST_FILTER_LABEL' => [
						'ru' => $arConf[1],
					],
				];
				if($arConf[0] == 'iblock_section')
				{
					$aUserFields['SETTINGS']['IBLOCK_ID'] = CATALOG_IBLOCK_ID;
				}
				//pr($aUserFields);
				if(!$iUserFieldId = $oUserTypeEntity->Add($aUserFields))
				{
					$error = $APPLICATION->GetException();
					//pr($error);
				}
			}
		}
		if($add)
		{
			$arIblockFields = CIBlock::getFields($IBLOCK_ID);
			$arIblockFields["SECTION_CODE"]["IS_REQUIRED"] = "Y";
			$arIblockFields["SECTION_CODE"]["DEFAULT_VALUE"]["TRANSLITERATION"] = "Y";
			$arIblockFields["SECTION_CODE"]["DEFAULT_VALUE"]["UNIQUE"] = "Y";
			CIBlock::setFields($IBLOCK_ID, $arIblockFields);
			$optionView = [
				'tabs' => 'edit1--#--Раздел--,--SORT--#--Сортировка--,--ACTIVE--#--Раздел активен--,--IBLOCK_SECTION_ID--#--Родительский раздел--,--UF_FILTER_FOR_SEC--#--Раздел каталога--,--UF_FILTER_URL_PAGE--#--URL страницы--,--NAME--#--*Название--,--CODE--#--*Символьный код--,--PICTURE--#--Изображение--,--DESCRIPTION--#--Описание--;--edit5--#--SEO--,--UF_FILTER_TITLE--#--Title--,--UF_FILTER_HEADER--#--H1--,--UF_FILTER_KEYWORDS--#--Keywords--,--UF_FILTER_DESC--#--Description--,--UF_BACKGROUND_IMAGE--#--Фон--;--',
			];
			CUserOptions::SetOption('form', 'form_section_'.$IBLOCK_ID, $optionView, true, 0);
			$ib = new CIBlock;
			$arIblockFields = [
				'INDEX_ELEMENT' => 'N',
				'INDEX_SECTION' => 'N',
				'LIST_MODE' => 'C',
			];
			$ib->Update($IBLOCK_ID, $arIblockFields);
		}
	}
	$i ++;


	$GLOBALS['PAGE_DATA']['SEO_FILTER']['PAGE_SEO'][$arFields["CODE"]] = [
		"PARENT" => $arFields["IBLOCK_SECTION_ID"],
		"NAME" => $arFields["NAME"],
		"TITLE" => $arFields["UF_FILTER_TITLE"],
		"KEYWORDS" => $arFields["UF_FILTER_KEYWORDS"],
		"DESCRIPTION" => $arFields["UF_FILTER_DESC"],
		"HEADER" => $arFields["UF_FILTER_HEADER"],
		"SEO_TEXT" => $arFields["DESCRIPTION"],
		"BACKGROUND_IMAGE" => $arFields["UF_BACKGROUND_IMAGE"],
		"FILTER_URL_PAGE" => $arFields["UF_FILTER_URL_PAGE"],
		"UF_MAIN_CHECK" => $arFields["UF_MAIN_CHECK"],
		"UF_DONT_SHOW_PUBLIC" => $arFields["UF_DONT_SHOW_PUBLIC"],
		"UF_HIDE_IN_MENU" => $arFields["UF_HIDE_IN_MENU"],
		"UF_HITS_TITLES" => $arFields["UF_HITS_TITLES"]
	];
	if($arFields["CODE"] != '')
	{
		$arFields["IBLOCK_SECTION_ID"] = intval($arFields["IBLOCK_SECTION_ID"]);
		// -- Tag information --------------------------------------- //
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["ID"] = $arFields["ID"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME"] = $arFields["NAME"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["CODE_NEW"] = $arFields["CODE"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["CODE"] = $arFields["CODE_OLD"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["SORT"] = $arFields["SORT"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_MAIN_CHECK"] = $arFields["UF_MAIN_CHECK"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_DONT_SHOW_PUBLIC"] = $arFields["UF_DONT_SHOW_PUBLIC"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_HIDE_IN_MENU"] = $arFields["UF_HIDE_IN_MENU"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_HITS_TITLES"] = $arFields["UF_HITS_TITLES"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["IMG"] = ($arFields['PICTURE'] ? CFile::ResizeImageGet(
				$arFields['PICTURE'],
				['width' => 38, 'height' => 38],
				BX_RESIZE_IMAGE_EXACT,
				true
			) : []);
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["IBLOCK_SECTION_ID"] = $arFields["IBLOCK_SECTION_ID"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_FILTER_FOR_SEC"] = $arFields["UF_FILTER_FOR_SEC"];
		if($arFields['IBLOCK_SECTION_ID'] and $arParents[$arFields['IBLOCK_SECTION_ID']])
		{
			$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arParents[$arFields['IBLOCK_SECTION_ID']]]["CHILD"][] =  $arFields["CODE"];
			$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]['THIS_PARENT'] = $arParents[$arFields["IBLOCK_SECTION_ID"]];
		}

		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME_BREAD"] = $arFields["UF_FILTER_BREAD_NAME"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME_MENU"] = $arFields["UF_FILTER_MENU_NAME"];
	}
}

// var_dump('SEO_FILTER');
// var_dump($GLOBALS['PAGE_DATA']['SEO_FILTER']);
//die;

/*$GLOBALS['PAGE_DATA']['SEO_FILTER']   = [
	'PAGE_SEO'  => [],
	'TAGS'	  => []
];
$arSelect   = [
	"ID", "IBLOCK_ID", "CODE", "IBLOCK_SECTION_ID", "SORT", "DESCRIPTION", "PICTURE", "NAME", "UF_*"
];
$arFilter   = Array(
	"IBLOCK_ID" => SEO_FILTER_IBLOCK_ID,
	"ACTIVE"	=> "Y",
	"GLOBAL_ACTIVE" => "Y",
);

$obResult = CIBlockSection::GetList(Array("left_margin"=>"asc"), $arFilter, false, $arSelect);;
$arParents = $arSections = [];
while($arFields = $obResult->GetNext())
{
	$arSections[] = $arFields;
	$arParents[$arFields['ID']] = $arFields['CODE'];
}
$i = 0;
foreach($arSections as $arFields)
{
	if(!$i)
	{
		$n = 0;
		$add = false;
		foreach($configFields as $code=>$arConf)
		{
			$n ++;
			if(!array_key_exists($code, $arFields))
			{
				$add = true;
				if(!isset($oUserTypeEntity))
				{
					$oUserTypeEntity = new CUserTypeEntity();
				}
				$aUserFields = [
					'ENTITY_ID' => 'IBLOCK_'.SEO_FILTER_IBLOCK_ID.'_SECTION',
					'FIELD_NAME' => $code,
					'USER_TYPE_ID' => $arConf[0],
					'SORT' => $n,
					'MULTIPLE' => 'N',
					'MANDATORY' => 'N',
					'SHOW_FILTER' => 'S',
					'IS_SEARCHABLE' => 'N',
					'SETTINGS' => [
						'SIZE' => '60',
						'ROWS' => ($arConf[2] ? : 1),
					],
					'EDIT_FORM_LABEL' => [
						'ru' => $arConf[1],
					],
					'LIST_COLUMN_LABEL' => [
						'ru' => $arConf[1],
					],
					'LIST_FILTER_LABEL' => [
						'ru' => $arConf[1],
					],
				];
				if($arConf[0] == 'iblock_section')
				{
					$aUserFields['SETTINGS']['IBLOCK_ID'] = CATALOG_IBLOCK_ID;
				}
				//pr($aUserFields);
				if(!$iUserFieldId = $oUserTypeEntity->Add($aUserFields))
				{
					$error = $APPLICATION->GetException();
					//pr($error);
				}
			}
		}
		if($add)
		{
			$arIblockFields = CIBlock::getFields($IBLOCK_ID);
			$arIblockFields["SECTION_CODE"]["IS_REQUIRED"] = "Y";
			$arIblockFields["SECTION_CODE"]["DEFAULT_VALUE"]["TRANSLITERATION"] = "Y";
			$arIblockFields["SECTION_CODE"]["DEFAULT_VALUE"]["UNIQUE"] = "Y";
			CIBlock::setFields($IBLOCK_ID, $arIblockFields);
			$optionView = [
				'tabs' => 'edit1--#--Раздел--,--SORT--#--Сортировка--,--ACTIVE--#--Раздел активен--,--IBLOCK_SECTION_ID--#--Родительский раздел--,--UF_FILTER_FOR_SEC--#--Раздел каталога--,--UF_FILTER_URL_PAGE--#--URL страницы--,--NAME--#--*Название--,--CODE--#--*Символьный код--,--PICTURE--#--Изображение--,--DESCRIPTION--#--Описание--;--edit5--#--SEO--,--UF_FILTER_TITLE--#--Title--,--UF_FILTER_HEADER--#--H1--,--UF_FILTER_KEYWORDS--#--Keywords--,--UF_FILTER_DESC--#--Description--,--UF_BACKGROUND_IMAGE--#--Фон--;--',
			];
			CUserOptions::SetOption('form', 'form_section_'.$IBLOCK_ID, $optionView, true, 0);
			$ib = new CIBlock;
			$arIblockFields = [
				'INDEX_ELEMENT' => 'N',
				'INDEX_SECTION' => 'N',
				'LIST_MODE' => 'C',
			];
			$ib->Update($IBLOCK_ID, $arIblockFields);
		}
	}
	$i ++;
	$GLOBALS['PAGE_DATA']['SEO_FILTER']['PAGE_SEO'][$arFields["CODE"]] = [
		"PARENT" => $arFields["IBLOCK_SECTION_ID"],
		"NAME" => $arFields["NAME"],
		"TITLE" => $arFields["UF_FILTER_TITLE"],
		"KEYWORDS" => $arFields["UF_FILTER_KEYWORDS"],
		"DESCRIPTION" => $arFields["UF_FILTER_DESC"],
		"HEADER" => $arFields["UF_FILTER_HEADER"],
		"SEO_TEXT" => $arFields["DESCRIPTION"],
		"BACKGROUND_IMAGE" => $arFields["UF_BACKGROUND_IMAGE"],
		"FILTER_URL_PAGE" => $arFields["UF_FILTER_URL_PAGE"],
		"UF_MAIN_CHECK" => $arFields["UF_MAIN_CHECK"],
		"UF_DONT_SHOW_PUBLIC" => $arFields["UF_DONT_SHOW_PUBLIC"],
		"UF_HIDE_IN_MENU" => $arFields["UF_HIDE_IN_MENU"],
		"UF_HITS_TITLES" => $arFields["UF_HITS_TITLES"]

	];
	if($arFields["CODE"] != '')
	{
		$arFields["IBLOCK_SECTION_ID"] = intval($arFields["IBLOCK_SECTION_ID"]);
		// -- Tag information --------------------------------------- //
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["ID"] = $arFields["ID"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME"] = $arFields["NAME"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["CODE"] = $arFields["CODE"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["SORT"] = $arFields["SORT"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_MAIN_CHECK"] = $arFields["UF_MAIN_CHECK"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_DONT_SHOW_PUBLIC"] = $arFields["UF_DONT_SHOW_PUBLIC"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_HIDE_IN_MENU"] = $arFields["UF_HIDE_IN_MENU"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_HITS_TITLES"] = $arFields["UF_HITS_TITLES"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["IMG"] = ($arFields['PICTURE'] ? CFile::ResizeImageGet(
				$arFields['PICTURE'],
				['width' => 38, 'height' => 38],
				BX_RESIZE_IMAGE_EXACT,
				true
			) : []);
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["IBLOCK_SECTION_ID"] = $arFields["IBLOCK_SECTION_ID"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["UF_FILTER_FOR_SEC"] = $arFields["UF_FILTER_FOR_SEC"];
		if($arFields['IBLOCK_SECTION_ID'] and $arParents[$arFields['IBLOCK_SECTION_ID']])
		{
			$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arParents[$arFields['IBLOCK_SECTION_ID']]]["CHILD"][] = $arFields["CODE"];
			$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]['THIS_PARENT'] = $arParents[$arFields["IBLOCK_SECTION_ID"]];
		}
		
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME_BREAD"] = $arFields["UF_FILTER_BREAD_NAME"];
		$GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arFields["UF_FILTER_FOR_SEC"]][$arFields['CODE']]["NAME_MENU"] = $arFields["UF_FILTER_MENU_NAME"];
	}
}
*/
ipr('Кеширование сео-фильтра');





$json = json_encode($GLOBALS['PAGE_DATA'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$json = '<?die();?>'."\n".$json;

file_put_contents(CUSTOM_CACHE_FILE_PREFIX.'cache_page.php', $json); 

ipr();

pr($GLOBALS['PAGE_DATA']);

function getFinalPriceInCurrency(&$arItem, $cnt = 1, $sale_currency = 'RUB')
{
	$priceCode = [
		'BASE' => [],
		'SPB' => [],
		'EKB' => [],
	];
	foreach($priceCode as $code=>&$arPrice)
	{
		// Проверяем, имеет ли товар торговые предложения?
		if(CCatalogSku::IsExistOffers($arItem['ID']))
		{
			// Ищем все тогровые предложения
			$arrOffers = CIBlockPriceTools::GetOffersArray(
				[
					'IBLOCK_ID'		 => $arItem['IBLOCK_ID'],
					'HIDE_NOT_AVAILABLE'=> 'Y',
					'CHECK_PERMISSIONS' => 'Y'
				],
				[
					$arItem['ID']
				],
				null,
				null,
				null,
				null,
				null,
				null,
				[
					'CURRENCY_ID' => $sale_currency
				],
				CUSTOM_USER_ID,
				null
			);

			if (!empty($arrOffers))
			{
				foreach($arrOffers AS $arOffer)
				{
					$price = CCatalogProduct::GetOptimalPrice($arOffer['ID'], $cnt, [CUSTOM_USER_GROUP], 'N');
					if (isset($price['PRICE']))
					{
						$final_price	= $price['PRICE']['PRICE'];
						$currency_code  = $price['PRICE']['CURRENCY'];

						// Ищем скидки и высчитываем стоимость с учетом найденных
						$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arItem['ID'], [CUSTOM_USER_GROUP], "N");
						if (is_array($arDiscounts) && sizeof($arDiscounts) > 0)
						{
							$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
						}

						// Конец цикла, используем найденные значения
						break;
					}
				}
			}
		}
		else
		{
			$priceList = [];
			
			$iterator = Catalog\PriceTable::getList(array(
                'select' => array('ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY'),
                'filter' => array(
                    '=PRODUCT_ID' => $arItem['ID'],
                    'CATALOG_GROUP_ID' => $GLOBALS['PAGE_DATA']['PRICE_TYPE'][$code],
                    array(
                        'LOGIC' => 'OR',
                        '<=QUANTITY_FROM' => $cnt,
                        '=QUANTITY_FROM' => null
                    ),
                    array(
                        'LOGIC' => 'OR',
                        '>=QUANTITY_TO' => $cnt,
                        '=QUANTITY_TO' => null
                    )
                ),
                'order' => array('CATALOG_GROUP_ID' => 'ASC')
            ));
            while ($row = $iterator->fetch())
            {
                $row['ELEMENT_IBLOCK_ID'] = CATALOG_IBLOCK_ID;
                $priceList[] = $row;
            }
			
			if(!empty($priceList))
			{
				// Простой товар, без торговых предложений (для количества равному $cnt)
				$price = CCatalogProduct::GetOptimalPrice($arItem['ID'], $cnt, [CUSTOM_USER_GROUP], 'N', $priceList);

				// Получили цену?
				if (!$price || !isset($price['PRICE']))
				{
					return false;
				}

				// Меняем код валюты, если нашли
				if (isset($price['CURRENCY']))
				{
					$currency_code = $price['CURRENCY'];
				}

				if (isset($price['PRICE']['CURRENCY']))
				{
					$currency_code = $price['PRICE']['CURRENCY'];
				}

				// Получаем итоговую цену
				$final_price = $price['PRICE']['PRICE'];

				// Ищем скидки и пересчитываем цену товара с их учетом
				$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arItem['ID'], [CUSTOM_USER_GROUP], "N", 2, []);
				if (is_array($arDiscounts) && sizeof($arDiscounts) > 0)
				{
					$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
				}
				// Если необходимо, конвертируем в нужную валюту
				if ($currency_code != $sale_currency)
				{
					$final_price = CCurrencyRates::ConvertCurrency($final_price, $currency_code, $sale_currency);
				}
				
				$arPrice = [
					"PRICE"	 => CurrencyFormat($price['PRICE']['PRICE'], $currency_code),
					"FINAL_PRICE"=>CurrencyFormat($final_price, $currency_code),
					"CURRENCY"  => $sale_currency,
					"DISCOUNT"  => $arDiscounts,
				];
			}
		}
	}

	return $priceCode;
}


// -- Get product parent section -------------------------------------------- //
function getParent($sectionID, &$arSections)
{
	if (!$sectionID || empty($arSections) || !isset($arSections[$sectionID]))
	{
		return FALSE;
	}
	
	if ($arSections[$sectionID]['DEPTH_LEVEL'] == 1)
	{
		return $arSections[$sectionID]['ID'];
	}
	else
	{
		return getParent($arSections[$sectionID]['IBLOCK_SECTION_ID'], $arSections);
	}
}
