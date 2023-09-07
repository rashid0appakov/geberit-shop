<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetPageProperty("description", "Доставка любой сантехники по всем городам России с установкой");
    $APPLICATION->SetPageProperty("keywords", "сантехника, магазин, доставка");
    $APPLICATION->SetPageProperty("title", "Доставка сантехники GEBERIT");
    $APPLICATION->SetTitle("Доставка");
?>
<?

if (strpos($_SERVER['REQUEST_URI'], '/delivery/moskva/') !== false){
    $title = $APPLICATION->GetPageProperty("title").' в Москве';

    $APPLICATION->SetPageProperty("title", $title);
}

if (strpos($_SERVER['REQUEST_URI'], '/delivery/sankt-peterburg/') !== false){
    $title = $APPLICATION->GetPageProperty("title").' в Санкт-Петербурге';

    $APPLICATION->SetPageProperty("title", $title);
}

if (strpos($_SERVER['REQUEST_URI'], '/delivery/krasnodar/') !== false){
    $title = $APPLICATION->GetPageProperty("title").' в Краснодаре';

    $APPLICATION->SetPageProperty("title", $title);
}

if (strpos($_SERVER['REQUEST_URI'], '/delivery/ekaterinburg/') !== false){
    $title = $APPLICATION->GetPageProperty("title").' в Екатеринбурге';

    $APPLICATION->SetPageProperty("title", $title);
}

if (strpos($_SERVER['REQUEST_URI'], '/delivery/ekaterinburg/') !== false){
    $title = $APPLICATION->GetPageProperty("title").' по России';

    $APPLICATION->SetPageProperty("title", $title);
}


?>
<div class="goods">
	<div class="container goods__container">
		 <!-- page breadcrumbs -->
		<div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "main",
                Array(
                    "PATH"      => "",
                    "SITE_ID"   => SITE_ID,
                    "START_FROM"=> "0"
                )
            );?>
		</div>
		<div class="goods__wrapper">
			<div class="goods__sidebar">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "sidebar",
                    Array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "sidebar",
                        "COMPONENT_TEMPLATE" => "sidebar",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "3",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "sidebar",
                        "USE_EXT" => "Y"
                    )
                );?>
			</div>
			<div class="goods__card">
				 <!-- page title -->
				<h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:news",
                    "delivery",
                    array(
                        "ADD_ELEMENT_CHAIN" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "BROWSER_TITLE" => "-",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "CHECK_DATES" => "Y",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
                        "DETAIL_DISPLAY_TOP_PAGER" => "N",
                        "DETAIL_FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "DETAIL_PAGER_SHOW_ALL" => "Y",
                        "DETAIL_PAGER_TEMPLATE" => "",
                        "DETAIL_PAGER_TITLE" => "Страница",
                        "DETAIL_PROPERTY_CODE" => array(
                            0=>"DESCRIPTION"
                        ),
                        "DETAIL_SET_CANONICAL_URL" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "DISPLAY_NAME" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "IBLOCK_ID" => CClass::RU_DELIVERY_IBLOCK_ID,
                        "IBLOCK_TYPE" => "content",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "LIST_FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "LIST_PROPERTY_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "MESSAGE_404" => "",
                        "META_DESCRIPTION" => "-",
                        "META_KEYWORDS" => "-",
                        "NEWS_COUNT" => "1000",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_TEMPLATE" => ".default",
                        "PAGER_TITLE" => "Новости",
                        "PREVIEW_TRUNCATE_LEN" => "",
                        "SEF_FOLDER" => "/delivery/",
                        "SEF_MODE" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "SET_STATUS_404" => "Y",
                        "SET_TITLE" => "Y",
                        "SHOW_404" => "N",
                        "SORT_BY1" => "SORT",
                        "SORT_BY2" => "SORT",
                        "SORT_ORDER1" => "DESC",
                        "SORT_ORDER2" => "ASC",
                        "STRICT_SECTION_CHECK" => "N",
                        "USE_CATEGORIES" => "N",
                        "USE_FILTER" => "N",
                        "USE_PERMISSIONS" => "N",
                        "USE_RATING" => "N",
                        "USE_REVIEW" => "N",
                        "USE_RSS" => "N",
                        "USE_SEARCH" => "N",
                        "COMPONENT_TEMPLATE" => "delivery",
                        "SEF_URL_TEMPLATES" => array(
                            "news" => "",
                            "section" => "",
                            "detail" => "#ELEMENT_CODE#/",
                        )
                    ),
                    false
                );?>
                <?/*$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section.list",
                    "payments",
                    Array(
                        "ADD_SECTIONS_CHAIN" => "N",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "COUNT_ELEMENTS" => "N",
                        "IBLOCK_ID" => "23",
                        "IBLOCK_TYPE" => "content",
                        "SECTION_CODE" => "",
                        "SECTION_FIELDS" => array("CODE","NAME","DESCRIPTION",""),
                        "SECTION_ID" => "",
                        "SECTION_URL" => "",
                        "SECTION_USER_FIELDS" => array("",""),
                        "SHOW_PARENT_NAME" => "N",
                        "TOP_DEPTH" => "1",
                        "VIEW_MODE" => "LIST"
                    )
                );*/?>
			</div>
		</div>
	</div>
	 <!-- end tabs -->
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>