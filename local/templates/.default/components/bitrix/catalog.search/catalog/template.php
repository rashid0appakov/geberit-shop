<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$isAjax = $arParams["IS_AJAX"] == "Y" || $_REQUEST['IS_AJAX'] == 'Y';
global $arContact;
?>

<?
$arElements = $APPLICATION->IncludeComponent(
	"bitrix:search.page",
	".default",
	Array(
		"RESTART" => $arParams["RESTART"],
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
		"USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
		"arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"SHOW_WHERE" => "N",
		"arrWHERE" => array(),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => 350,
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "N",
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);
if ($isAjax) {
	$APPLICATION->RestartBuffer();
}

$q = $_REQUEST['q'];
$q=preg_replace('/([\d])[x,х]([\d])/ismU','$1*$2',$_REQUEST["q"]);



$arElements = [];
/*$obSearch = new CSearch;
$obSearch->Search([
	"QUERY" => 	$q, // tis a trick to search by part of word
	"MODULE_ID" => 'iblock',
	"CHECK_DATES" => 'Y',
	"PARAM1" => CATALOG_IBLOCK_TYPE,
	"PARAM2" => CATALOG_IBLOCK_ID,
	'SITE_ID' => SITE_ID,
]);*/


$arSphinx = new CSearchSphinx;
$arSphinx->connect(
	COption::GetOptionString("search", "sphinx_connection"),
	COption::GetOptionString("search", "sphinx_index_name")
);

$aSort = array("TITLE_RANK" => "DESC", "TITLE" => "DESC", "CUSTOM_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC");


$obSearch = $arSphinx->search(array("SITE_ID" => SITE_ID, "QUERY" => str_replace('*', '-', $q)), $aSort, array(), false);

if(count($obSearch)==0)
{
	$arResult["alt_query"] = "";
	$arLang = CSearchLanguage::GuessLanguage($q);
	if(is_array($arLang) && $arLang["from"] != $arLang["to"])
	{
		$qq=str_replace('.', '', $q);
		$arResult["alt_query"] = CSearchLanguage::ConvertKeyboardLayout($qq, $arLang["from"], $arLang["to"]);
	}
	$arResult["q"] = $q;
	$arResult["phrase"] = stemming_split($q, LANGUAGE_ID);
	$q=strlen($arResult["alt_query"])>0?$arResult["alt_query"]:$q;
	$obSearch = $arSphinx->search(array("SITE_ID" => SITE_ID, "QUERY" => str_replace('*', '-', $q)), $aSort, array(), false);
}

$APPLICATION->SetTitle('Поиск по запросу «'.$q.'»');
$APPLICATION->SetPageProperty('title', 'Поиск по запросу «'.$q.'»');


foreach ($obSearch as $res) {
	if($res["param1"]==$arParams["IBLOCK_TYPE"] && $res["param2"]==$arParams["IBLOCK_ID"] && is_numeric($res['item']))
	{
		$arElements[] = $res['item'];
	}
}





if (!empty($arElements) && is_array($arElements)){
	
	global $searchFilter;
	$searchFilter = array(
		"=ID" => $arElements,
	);

	
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"search",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD"        => GetSortField(),
            "ELEMENT_SORT_ORDER"        => "ASC",
			"ELEMENT_SORT_FIELD2" => "ID",
			"ELEMENT_SORT_ORDER2" => $arElements,
			"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
			"SECTION_URL" => $arParams["SECTION_URL"],
			"DETAIL_URL" => $arParams["DETAIL_URL"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"FILTER_NAME" => "searchFilter",
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"META_KEYWORDS" => "",
			"META_DESCRIPTION" => "",
			"BROWSER_TITLE" => "",
			"ADD_SECTIONS_CHAIN" => "N",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "N",
		),
		$arResult["THEME_COMPONENT"],
		array('HIDE_ICONS' => 'Y')
	);
	
}
else{
	?>
	<div class="gray-block empty-search">
		<div class="sub-block">
			<div class="sub-title">К сожалению по вашему запросу ничего не найдено, попробуйте новый поиск</div>
			<form method="get" action="/search/">
				<div class="b-empty__search-form columns">
					<div class="column col1">
						<input type="text" class="input" name="q" value="" autocomplete="off" placeholder="Поиск товаров">
					</div>
					<div class="column submitExpressForm">
						<button class="btn is-primary buy__item-button buy__item-button--blue" type="submit">Найти</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<style>
		.goods__card {
			width: 100%;
		}
	</style>
	<?
}
if ($isAjax) {
	die;
}
?>