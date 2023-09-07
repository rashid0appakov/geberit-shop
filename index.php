<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
$APPLICATION->SetPageProperty("description", "Интернет-магазин №1 в России по продаже швейцарской сантехники Geberit. Официальный дилер компании Геберит ".GetGeoText());
$APPLICATION->SetPageProperty("title", "Geberit-shop — официальный интернет-магазин сантехники Геберит ".GetGeoText());
?>
<?php
    global $arSlideFilter;
    $arSlideFilter['PROPERTY_SITE_ID'] = SITE_ID;
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "main_slider",
    array(
        "ACTIVE_DATE_FORMAT" => "",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "N",
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "N",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(
            0 => "NAME",
            1 => "DETAIL_PICTURE",
            2 => "",
        ),
        "FILTER_NAME" => "arSlideFilter",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "28",
        "IBLOCK_TYPE" => "content",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "100",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(
            0 => "NEW_TAB",
            1 => "LINK",
            2 => "",
        ),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "ID",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "COMPONENT_TEMPLATE" => "main_slider"
    ),
    false
);?>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "benefits",
    array(
        "ACTIVE_DATE_FORMAT" => "",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "N",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(
            0 => "NAME",
            1 => "PREVIEW_PICTURE",
            2 => "PREVIEW_TEXT",
            3 => "",
        ),
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "29",
        "IBLOCK_TYPE" => "content",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "100",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(
            0 => "",
        ),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "ID",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "COMPONENT_TEMPLATE" => "main_slider"
    ),
    false
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main/new_products.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main/tabs.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>

<div class="container is-widescreen">
<?
    global $arrSFilterP;
    $arrSFilterP['PROPERTY_POPULAR'] = 'Y';
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list", 
    "slider_popular", 
    array(
        "IBLOCK_TYPE" => "catalog_geberit",
        "IBLOCK_ID" => "82",
        "NEWS_COUNT" => "400",
        "SORT_BY1" => "NAME",
        "SORT_ORDER1" => "ASC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "FIELD_CODE" => array(
            0 => "",
            1 => 'DETAIL_PICTURE',
            2 => "",
        ),
        "PROPERTY_CODE" => array(
            0 => "",
            1 => $arParams["LIST_PROPERTY_CODE"],
            2 => "",
        ),
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "SET_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "FILE_404" => $arParams["FILE_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
        "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
        "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
        "ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
        "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
        "FILTER_NAME" => "arrSFilterP",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "CHECK_DATES" => "N",
        "COMPONENT_TEMPLATE" => "slider_popular",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "SET_BROWSER_TITLE" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_META_DESCRIPTION" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",
        "STRICT_SECTION_CHECK" => "N"
    ),
    $component
);?>    
</div>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main/showroom.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main_about/include.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>
<?if($_SERVER["SERVER_NAME"] !='geberit-shop.ru'){}else{?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main_popular_category/include.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main_videos/include.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR."include/main_partners/include.php"
    ),
    false,
    array("HIDE_ICONS" => "Y")
);?>
<?}?>
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "WebSite",
    "url": "https://<?=SITE_SERVER_NAME?>/",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "https://<?=SITE_SERVER_NAME?>/catalog/?q={q}&how=r",
      "query": "<?=$_GET['q']?>",
	  "query-input": "required name=q"
    }
}
</script>

<?if($USER->GetID()==2422):?>  
<!-- <?=__FILE__?> -->
<script type="text/javascript">
    console.log('<? echo CClass::getNameSubdomain();?>');
</script>
<?endif?>

<?
$APPLICATION->SetTitle(str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetTitle()));
$APPLICATION->SetPageProperty('title', str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetPageProperty('title')));
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>