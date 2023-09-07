<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    //if($APPLICATION->get_cookie("GEOLOCATION_ID")==817||$_SERVER["HTTP_IS_SUB_HEADER"]=='spb'){
    if(strpos($APPLICATION->GetCurDir(),'sankt-peterburg')!==false){
        $APPLICATION->SetPageProperty("title", "Контакты магазина Geberit Shop в Санкт-Петербурге");
    }
    elseif(strpos($APPLICATION->GetCurDir(),'ekaterinburg')!==false){
        $APPLICATION->SetPageProperty("title", "Контакты магазина Geberit Shop в Екатеринбурге");
    }
    elseif(strpos($APPLICATION->GetCurDir(),'krasnodar')!==false){
        $APPLICATION->SetPageProperty("title", "Контакты магазина Geberit Shop в Краснодаре");
    }
    else{
         $APPLICATION->SetPageProperty("title", "Контакты магазина Geberit Shop в Москве");
    }

//$APPLICATION->SetPageProperty("title", "Контакты магазина Grohe");
    $APPLICATION->SetTitle("Контакты");
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
                    "CHILD_MENU_TYPE" => "contacts",
                    "COMPONENT_TEMPLATE" => "sidebar",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "DELAY" => "N",
                    "MAX_LEVEL" => "2",
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
				<div class="goods__title">
					<h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
				</div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:news",
                    "contacts",
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
                            0=>"MAPS",1=>"FILIAL_NAME",2=>"ADRESS",3=>"TIME_OFFICE",4=>"TIME_SKLAD",5=>"FIZ_LICA",6=>"UR_LICA",7=>"MANAGERS",8=>"FOTO",9=>"MAPS_DESCRIPTION"
                        ),
                        "DETAIL_SET_CANONICAL_URL" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "DISPLAY_NAME" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "IBLOCK_ID" => "47",
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
                        "SEF_FOLDER" => "/contacts/",
                        "SEF_MODE" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "SET_STATUS_404" => "Y",
                        "SET_TITLE" => "Y",
                        "SHOW_404" => "Y",
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
                        "COMPONENT_TEMPLATE" => "contacts",
                        "SEF_URL_TEMPLATES" => array(
                            "news" => "",
                            "section" => "",
                            "detail" => "#ELEMENT_CODE#/",
                        )
                    ),
                    false
                );?>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>