<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?>
<?
$name_this_col = explode("/", $APPLICATION->GetCurPage());

//В этом разделе только страница бреда+коллекции
if(count($name_this_col) != 5){
	LocalRedirect("/404.php");
}

$arSelectBrand = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_*");
$arFilterBrand = Array("IBLOCK_ID"=>13, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE"=>$name_this_col[2]);
$resBrand = CIBlockElement::GetList(Array(), $arFilterBrand, false, false, $arSelectBrand);

if (intval($resBrand->SelectedRowsCount()) == 0){
	LocalRedirect("/404.php");
}

while($obBrand = $resBrand->GetNextElement()){
    $arFieldsBrand = $obBrand->GetFields();
	$arProps = $obBrand->GetProperties();
	
	$nameBrand = $arFieldsBrand['NAME'];
	$countryBrand = $arProps['COUNTRY']['VALUE'];
	$desc = $arFieldsBrand['NAME'];
}

$arSelectSeries = Array("ID", "NAME", "PREVIEW_TEXT");
$arFilterSeries = Array("IBLOCK_ID"=>22, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE"=>$name_this_col[3]);
$resSeries = CIBlockElement::GetList(Array(), $arFilterSeries, false, false, $arSelectSeries);


if (intval($resSeries->SelectedRowsCount()) == 0){
	LocalRedirect("/404.php");
}

while($obSeries = $resSeries->GetNextElement()){
    $arFieldsSeries = $obSeries->GetFields();
	$nameSeries = $arFieldsSeries['NAME'];
	$description = $arFieldsSeries['PREVIEW_TEXT'];
}

//Вставляем метатеги и хлебные крошки
$APPLICATION->SetTitle("Товары ".$nameBrand." из коллекции ".$nameSeries);
$APPLICATION->SetPageProperty("title", $nameSeries." ".$nameBrand." (".$countryBrand.") купить в ТипТоп-Шоп.ру, цены, фото");
$APPLICATION->SetPageProperty("description", $nameBrand." ".$nameSeries." приобрести в нашем интернет-магазине по выгодным ценам или со скидками!");
//$APPLICATION->SetPageProperty("keywords", );
$APPLICATION->AddChainItem($nameBrand, "/vendors/".$name_this_col[2]."/");
$APPLICATION->AddChainItem($nameSeries, $name_this_col[3]);
//echo "<a href='/vendors/".$name_this_col[2]."/'>Назад к производителю</a>";

//Выводим товары бренда+коллекции
global $arrFilterManSer;
$arrFilterManSer = Array("PROPERTY_MANUFACTURER.CODE" => $name_this_col[2], "PROPERTY_series.CODE" => $name_this_col[3]);
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"sections_slider_series",
	Array(
		"CODE_BRAND" => $name_this_col[2],
		"NAME_BRAND" => $nameBrand,
		"CODE_SERIES" => $name_this_col[3],
		"NAME_SERIES" => $nameSeries,
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/basket.php",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPATIBLE_MODE" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_IMG_HEIGHT" => "178",
		"DISPLAY_IMG_WIDTH" => "178",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "shows",
		"ELEMENT_SORT_FIELD2" => "shows",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "asc",
		"ENLARGE_PRODUCT" => "STRICT",
		"FILTER_NAME" => "arrFilterManSer",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "Y",
		"HIDE_SECTION" => "N",
		"IBLOCK_ID" => "15",
		"IBLOCK_TYPE" => "catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => array(),
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "3",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(),
		"OFFERS_FIELD_CODE" => array("",""),
		"OFFERS_LIMIT" => "5",
		"OFFERS_PROPERTY_CODE" => array("",""),
		"OFFERS_SORT_FIELD" => "shows",
		"OFFERS_SORT_FIELD2" => "shows",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "asc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "",
		"PAGE_ELEMENT_COUNT" => "400",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array("BASE"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false}]",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE" => array("",""),
		"PROPERTY_CODE_MOBILE" => array(),
		"PROPERTY_CODE_MOD" => array("",""),
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array("",""),
		"SEF_MODE" => "N",
		"SEF_RULE" => "",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_SLIDER" => "Y",
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?>

<div class="catalog_description">
<?=$description?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>