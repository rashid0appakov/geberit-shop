<?
//die('test');
$dev = 0;//time();
if(isset($_REQUEST['skip']))
{
	$_REQUEST['PAGEN_1'] = $_POST['PAGEN_1'] = $_GET['PAGEN_1'] = $_REQUEST['skip'] + 1;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$tab = $_REQUEST['tab'];
$filter_key = $_REQUEST['filter_key'];
$type = $_REQUEST['type'];
$id = $_REQUEST['id'];
$element = intval($_REQUEST['element']);
$pp = intval($_REQUEST['pp']);
if($pp < 1 or $pp > 4)
{
	$pp = 4;
}
$section = intval($_REQUEST['section']);

$arResult = [
	'success' => 'stop',
	'slides' => [],
	'skip' => $_REQUEST['PAGEN_1'],
	'id' => str_replace('carousel_', '', $id),
	'type' => $type,
	'element' => $element,
];

$lang = LangSubst(LANGUAGE_ID);
__IncludeLang($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/lang/$lang/header.php");

include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/template_filters.php');
include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_functions.php');
if(($filter_key == 'ELEMENT' and $element > 0) or isset($GLOBALS['CUSTOM_FILTER'][$filter_key]))
{
	global $arrFilter;
	if($filter_key == 'ELEMENT' and $element > 0)
	{
		$cacheProps = [
			'KOMLP' => 'KOMLP_CACHE',
			'ADDITIONAL' => 'ADDITIONAL_CACHE',
			'SPARE' => 'SPARE_PARTS_CACHE',
			'COLLECTION' => 'COLLECTION_ITEMS',
			'SIMILAR' => 'SIMILAR_ITEMS',
			'IN_SET' => 'IN_SET_CACHE'
		];
		$arSelect = [
			"ID", "IBLOCK_ID", "ACTIVE", "PROPERTY_ADDITIONAL"
		];
		foreach($cacheProps as $prop1=>$prop2)
		{
			$arSelect[] = 'PROPERTY_'.$prop2;
		}
		$arElementFilter = [
			'ACTIVE' => 'Y',
			"CATALOG_AVAILABLE" => 'Y',
			'SECTION_GLOBAL_ACTIVE' => 'Y',
			"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
			"IBLOCK_ID" => CATALOG_IBLOCK_ID,
			'ID' => $element
		];
		//$arResult['filter_element'] = $arElementFilter;
		$res = CIBlockElement::GetList(["SORT" => "ASC"], $arElementFilter, false, Array("nPageSize"=>1), $arSelect);
		if($obItem = $res->GetNextElement())
		{
			$arFields = $obItem->GetFields();
			$arFields['PROPERTIES'] = $obItem->GetProperties();
			//$arResult['item'] = $arFields;
			
			$arAddItemsId = [];
			if(isset($cacheProps[$type]))
			{
				if(!empty($arFields['PROPERTIES'][$cacheProps[$type]]['VALUE']))
				{
					$arFields['PROPERTIES'][$cacheProps[$type]]['VALUE'] = json_decode($arFields['PROPERTIES'][$cacheProps[$type]]['~VALUE'], true);
					$arAddItemsId[$type] = [];
					foreach($arFields['PROPERTIES'][$cacheProps[$type]]['VALUE'] as &$sec)
					{
						$i = 0;
						foreach($sec as $id)
						{
							$i ++;
//							if($i > 8)
//							{
//								break;
//							}
							$arAddItemsId[$type][] = $id;
						}
					}
				}
			}
			//pr($arAddItemsId);
			if(!empty($arAddItemsId[$type]))
			{
				$arOdds	 = CClass::getProductOds();
				$arSections = CClass::getCatalogSection();			
				$arResAddItems = GetAdditionalsNew($arAddItemsId, $arOdds, $arSections, [], CATALOG_IBLOCK_ID);
				if(
					(!empty($arResAddItems[$type][$tab]) and is_array($arResAddItems[$type][$tab])) or 
					!$tab
				)
				{
					if($dev)
					{
						$arResult['all_items'] = [];
						foreach($arResAddItems as $key1=>$val1)
						{
							$arResult['all_items'][$key1] = [];
							foreach($val1 as $key2=>$val2)
							{
								$arResult['all_items'][$key1][$key2] = $key2;
							}
						}
						//CClass::Dump($arResult); die();
					}
					if($tab)
					{
//						echo '---';
						foreach($arResAddItems[$type][$tab] as $arSubElement)
						{
							$arrFilter['ID'][] = $arSubElement['ID'];
						}
					}
					else
					{
//						echo '+++';
						foreach($arResAddItems[$type] as $arSub)
						{
							foreach($arSub as $arSubElement)
							{
								$arrFilter['ID'][] = $arSubElement['ID'];
							}
						}
					}
//					pr($arrFilter);
				}
			}
		}
	}
	else
	{
		$arrFilter = $GLOBALS['CUSTOM_FILTER'][$filter_key];
		if($section)
		{
			$arrFilter['SECTION_ID'] = $section;
			$arrFilter['INCLUDE_SUBSECTIONS'] = 'Y';
		}
		else
		{
			unset($arrFilter['INCLUDE_SUBSECTIONS']);
		}
	}
	if($dev)
	{
		$arResult['filter'] = $arrFilter;
	}
	if(count($arrFilter))
	{
		$arrFilter['SECTION_GLOBAL_ACTIVE'] = 'Y';
		$GLOBALS['NavNum'] = 0;
		$APPLICATION->IncludeComponent(
			"bitrix:catalog.section",
			"carousel_json",
			array(
				"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
				"IBLOCK_ID" => CATALOG_IBLOCK_ID,
				"ELEMENT_SORT_FIELD" =>  GetSortField(),
				"ELEMENT_SORT_ORDER" => "DESC",
				"ELEMENT_SORT_FIELD2" => "SORT",
				"ELEMENT_SORT_ORDER2" => "ASC",
				"PROPERTY_CODE" => array(
					0 => "OLD_ID",
				),
				"SET_META_KEYWORDS" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_SUBSECTIONS" => "Y",
				"SHOW_ALL_WO_SECTION" => "Y",
				"BASKET_URL" => "/personal/cart/",
				"ACTION_VARIABLE" => "action",
				"PRODUCT_ID_VARIABLE" => "id",
				"SECTION_ID_VARIABLE" => "SECTION_ID",
				"PRODUCT_QUANTITY_VARIABLE" => "quantity",
				"PRODUCT_PROPS_VARIABLE" => "prop",
				"FILTER_NAME" => "arrFilter",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "Y",
				"SET_TITLE" => "N",
				"MESSAGE_404" => "",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"FILE_404" => "",
				"DISPLAY_COMPARE" => "Y",
				"PAGE_ELEMENT_COUNT" => $pp,
				"LINE_ELEMENT_COUNT" => "",
				"PRICE_CODE" => CClass::getCurrentPriceCode(),
				"USE_PRICE_COUNT" => "Y",
				"SHOW_PRICE_COUNT" => "1",
				"PRICE_VAT_INCLUDE" => "Y",
				"USE_PRODUCT_QUANTITY" => "Y",
				"ADD_PROPERTIES_TO_BASKET" => "N",
				"PARTIAL_PRODUCT_PROPERTIES" => "N",
				"PRODUCT_PROPERTIES" => array(
				),
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => "",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_BASE_LINK" => "",
				"PAGER_PARAMS_NAME" => "",
				"SECTION_ID" => "",
				"SECTION_CODE" => "",
				"SECTION_URL" => "",
				"DETAIL_URL" => "",
				"USE_MAIN_ELEMENT_SECTION" => "Y",
				"CONVERT_CURRENCY" => "N",
				"CURRENCY_ID" => "",
				"HIDE_NOT_AVAILABLE" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"COMPARE_PATH" => "",
				"BACKGROUND_IMAGE" => "",
				"DISABLE_INIT_JS_IN_COMPONENT" => "N",
				"PROPERTY_CODE_MOD" => array(
					0 => "GUARANTEE",
					1 => "",
				),
				"COMPONENT_TEMPLATE" => "filtered",
				"SECTION_USER_FIELDS" => array(
					0 => "",
					1 => "",
				),
				"CUSTOM_FILTER" => "",
				"HIDE_NOT_AVAILABLE_OFFERS" => "N",
				"SEF_MODE" => "N",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"BROWSER_TITLE" => "-",
				"META_KEYWORDS" => "-",
				"META_DESCRIPTION" => "-",
				"COMPATIBLE_MODE" => "Y",
		
				"SHOW_COUNT" => $pp,
				"FILTER_KEY" => $filter_key,
				
				"CURRENT_BASE_PAGE" => "/",
				"PAGE" => $_REQUEST['PAGEN_1'],
				"NCC" => $dev,
			),
			false
		);
		if(!empty($GLOBALS['SLIDES_RESULT']))
		{
			$arResult['success'] = 'ok';
			$arResult['slides'] = $GLOBALS['SLIDES_RESULT'];
			$arResult['pager'] = $GLOBALS['SLIDES_PAGER'];
		}
	}
}
//pr($arResult, true); die();
header('Content-Type: application/json');
echo json_encode($arResult);
?>