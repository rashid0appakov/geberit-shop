<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Коллекции Geberit - все коллекции производителя Геберит");
    $APPLICATION->SetTitle("Серии");
    $APPLICATION->SetPageProperty("description", "Коллекции производителей сантехники Geberit, большой выбор товаров для ванной комнаты!");

if($_GET['PAGEN_2']==1){
	$page = $APPLICATION->GetCurPageParam("", array("PAGEN_2")); 
	LocalRedirect($page, false, '301 Moved permanently');
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news",
	"series_new",
	Array(
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_STYLE" => "Y",
		"BIG_DATA_RCM_TYPE" => "any",
		"BROWSER_TITLE" => "TITLE",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_ACTIVE_DATE_FORMAT" => "",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array("",""),
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DETAIL_PAGER_TEMPLATE" => "blog",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PROPERTY_CODE" => array("",""),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_COMPARE" => "Y",
		"DISPLAY_IMG_HEIGHT" => "178",
		"DISPLAY_IMG_WIDTH" => "178",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PANEL" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "CATALOG_AVAILABLE",
		"ELEMENT_SORT_FIELD2" => "SORT",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_ORDER2" => "ASC",
		"FILE_404" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_SECTION" => "N",
		"IBLOCK_ID" => SERIES_IBLOCK_ID,
		"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "",
		"LIST_FIELD_CODE" => array("DETAIL_PICTURE",""),
		"LIST_PROPERTY_CODE" => array("BRAND",""),
		"META_DESCRIPTION" => "DESCRIPTION",
		"META_KEYWORDS" => "KEYWORDS",
		"NEWS_COUNT" => "10",
		"NUM_DAYS" => "180",
		"NUM_NEWS" => "20",
		"OFFERS_CART_PROPERTIES" => array("COLOR","PROP2","PROP3"),
		"OFFERS_FIELD_CODE" => array("",""),
		"OFFERS_PROPERTY_CODE" => array("COLOR","PROP2","PROP3",""),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "asc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "load_more",
		"PAGER_TITLE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PRICE_CODE" => array("BASE"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PROPERTY_CODE" => array("","CHASTOTA_H_H","MAX_KR_MOM","NAPRAJ_AKKUM","VES_S_AKKUM",""),
		"PROPERTY_CODE_MOD" => array("GUARANTEE",""),
		"SEF_FOLDER" => "/series/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => Array("detail"=>"#ELEMENT_CODE#/","news"=>"","section"=>""),
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "NAME",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"USE_BIG_DATA" => "Y",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "N",
		"USE_PERMISSIONS" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_RATING" => "N",
		"USE_REVIEW" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N",
		"YANDEX" => "N",
		"IS_AJAX" => ($isAjax or $_REQUEST["IS_AJAX2"] == 'Y') ? "Y" : "N",
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>