<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Сантехника по акции и со скидками!");
$APPLICATION->SetTitle("Акции и скидки на сантехнику от TipTop-Shop.ru!");?>
<?
$arr = explode('/', $_SERVER['REQUEST_URI']);
//var_dump(count($arr));
if (count($arr)>3){
	$APPLICATION->IncludeComponent(
		"bitrix:news", 
		"promotions", 
		array(
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "18",
			"NEWS_COUNT" => "300",
			"USE_SEARCH" => "N",
			"USE_RSS" => "N",
			"NUM_NEWS" => "20",
			"NUM_DAYS" => "180",
			"YANDEX" => "N",
			"USE_RATING" => "N",
			"USE_CATEGORIES" => "N",
			"USE_REVIEW" => "N",
			"USE_FILTER" => "N",
			"SORT_BY1" => "SORT",
			"SORT_ORDER1" => "ASC",
			"SORT_BY2" => "",
			"SORT_ORDER2" => "",
			"CHECK_DATES" => "N",
			"SEF_MODE" => "Y",
			"SEF_FOLDER" => "/promotions/",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_SHADOW" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "0",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_PANEL" => "Y",
			"SET_TITLE" => "Y",
			"SET_STATUS_404" => "Y",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"USE_PERMISSIONS" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"LIST_ACTIVE_DATE_FORMAT" => "j F Y",
			"LIST_FIELD_CODE" => array(
				0 => "",
				1 => "",
			),
			"LIST_PROPERTY_CODE" => array(
				0 => "TIMER",
				1 => "",
			),
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"DISPLAY_NAME" => "Y",
			"META_KEYWORDS" => "-",
			"META_DESCRIPTION" => "-",
			"BROWSER_TITLE" => "-",
			"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
			"DETAIL_FIELD_CODE" => array(
				0 => "ACTIVE_TO",
				1 => "",
			),
			"DETAIL_PROPERTY_CODE" => array(
				0 => "TIMER",
				1 => "BRANDS",
				2 => "PRODUCTS",
				3 => "SECTIONS",
				4 => "",
			),
			"DETAIL_DISPLAY_TOP_PAGER" => "N",
			"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
			"DETAIL_PAGER_TITLE" => "Страница",
			"DETAIL_PAGER_TEMPLATE" => "arrows",
			"DETAIL_PAGER_SHOW_ALL" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Акции и скидки",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "arrows",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
			"PAGER_SHOW_ALL" => "N",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"AJAX_OPTION_ADDITIONAL" => "",
			"COMPONENT_TEMPLATE" => "promotions",
			"SET_LAST_MODIFIED" => "N",
			"ADD_ELEMENT_CHAIN" => "Y",
			"DETAIL_SET_CANONICAL_URL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SHOW_404" => "Y",
			"FILE_404" => "",
			"COMPOSITE_FRAME_MODE" => "A",
			"COMPOSITE_FRAME_TYPE" => "STATIC",
			"STRICT_SECTION_CHECK" => "N",
			"SEF_URL_TEMPLATES" => array(
				"news" => "",
				"section" => "",
				"detail" => "#ELEMENT_CODE#/",
			)
		),
		false
	);
}else{
?>
	<?
	function getProductSections_new($arFilter, $ID){
	    if (empty($arFilter) || !$ID)
	        return FALSE;

	    $arResult   = [];

	    $cache = new CPHPCache();
	    $cache_id = 'PROMOTION_ITEM_'.$ID;
	    if ($cache->InitCache(CClass::CACHE_TIME, $cache_id, "/promotions_new/")){
	        $res = $cache->GetVars();
	        if (is_array($res["arSections"]) && (count($res["arSections"]) > 0))
	           $arResult = $res["arSections"];
	    }

	    if (empty($arResult)){
	    	$filt = array_merge([
	                'IBLOCK_ID' => CATALOG_IBLOCK_ID,
	                'ACTIVE'    => 'Y',
	                'SECTION_GLOBAL_ACTIVE'    => 'Y'
	            ], $arFilter);

	        $rsElements = CIBlockElement::GetList(
	            [],
	            $filt,
	            false,
	            [],
	            array("ID", "IBLOCK_SECTION_ID")
	        );

	//        var_dump($filt);

	        while($arItem = $rsElements->GetNext()){
	            if ($arItem['IBLOCK_SECTION_ID'])
	                $arResult[$arItem['IBLOCK_SECTION_ID']]['COUNT']++;
	        }

	        if (!empty($arResult)){
	            $arSections = CClass::getCatalogSection();
	            foreach($arResult AS $sID  => &$arItem){
	                if (!$arSections[$sID])
	                    continue;
	                $arItem['NAME']     = $arSections[$sID]['NAME'];
	                $arResult['ALL']    += $arItem['COUNT'];
	            }
	        }

	        $cache->StartDataCache(CClass::CACHE_TIME, $cache_id, "/");
	        $cache->EndDataCache(array("arSections" => $arResult));
	    }

	    return $arResult;
	}

	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");

	//var_dump($geo_id);

	$arrFilter = array();
	$arrFilter['PROPERTY_SALEGOODS'] = 'Y';

	$STORE_ID = 4;
	if ($geo_id == 817){ // питер
		$STORE_ID = 1;
		unset($arrFilter['PROPERTY_SALEGOODS']);
		$arrFilter['PROPERTY_SALEGOODS_SPB'] = 'Y';
	}

	if ($geo_id == 2201){ // екб
		$STORE_ID = 2;
		unset($arrFilter['!CATALOG_PRICE_5']);
		unset($arrFilter['!CATALOG_PRICE_6']);
	}

	$arrFilter['ACTIVE'] = 'Y';
	$arrFilter['>CATALOG_STORE_AMOUNT_'.$STORE_ID] = 'Y';
	$arrFilter['SECTION_ACTIVE'] = 'Y';

	$arResult['SECTIONS'] = getProductSections_new($arrFilter, 1000000000);

	if (isset($_REQUEST['SECTION_ID']) && (int)$_REQUEST['SECTION_ID'])
	    $arrFilter["SECTION_ID"] = [(int)$_REQUEST['SECTION_ID']];

	//var_dump($arrFilter);
	?>
	<div class="goods">
		<div class="container goods__container">
			<div class="goods__breadcrumbs">
				<?$APPLICATION->IncludeComponent("bitrix:breadcrumb","main",Array(
					"START_FROM" => "0",
					"PATH" => "",
					"SITE_ID" => SITE_ID
					)
				);?>
			</div>
	        <div class="goods__title">
		            <h1 class="goods__title-title">Акции и скидки</h1>
	        </div>

			<div class="goods__wrapper">
			    <div class="goods__filter">
			        <div class="goods__filter-container">
			            <div class="goods__filter-content goods__filter-content--mobile">
			                <div class="goods__filter-close-button"></div>
			                <div class="filter">
			                    <div class="filter__item" style="margin-bottom: 25px !important;">
			                        <div class="filter__title--toggle is-expanded">
			                            <div aria-expanded="true" aria-controls="accordion1" class="filter__title accordion-title accordionTitle js-accordionTrigger is-expanded">Выберите категорию</div>
			                        </div>
			                        <div class="filter__content accordion-content accordionItem is-expanded" id="accordion1" aria-hidden="false">
			                            <ul class="filter__content-list">
			                                <li class="filter__content-item filter__content-item--toggle active">
			                                    <a href="./">
			                                        <span>Все</span>
			                                        <?/*
			                                        <span><?=$arResult['SECTIONS']['ALL']?></span>
			                                        */?>
			                                    </a>
			                                </li>
			                                <? foreach($arResult['SECTIONS'] AS $key => $arItem):
			                                    if ($key == 'ALL') continue;
			                                ?>
			                                <li class="filter__content-item filter__content-item--toggle active">
			                                    <a href="./?SECTION_ID=<?=$key?>" title="<?=$arItem['NAME']?>">
			                                        <span><?=$arItem['NAME']?></span>
			                                        <?/*
			                                        <span><?=$arItem['COUNT']?></span>
			                                        */
			                                        ?>
			                                    </a>
			                                </li>
			                                <? endforeach;?>
			                            </ul>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			    <div class="goods__card">
			        <div class="goods__card-sort--container hide mobile-show">
			            <?$APPLICATION->IncludeFile(
			                SITE_DEFAULT_PATH."/include/catalog_filter.php",
			                [
			                    "SORT"  => $sort,
			                    "ORDER" => $order,
			                    "MOBILE" => true
			                ],
			                array(
			                    "MODE"	  => "file",
			                    "SHOW_BORDER"   => FALSE
			                )
			            );?>
			        </div>
			        <div class="goods__card-sort--container hide-mobile">
			            <?$APPLICATION->IncludeFile(
			                SITE_DEFAULT_PATH."/include/catalog_filter.php",
			                [
			                    "SORT"  => $sort,
			                    "ORDER" => $order,
			                    "MOBILE" => false
			                ],
			                array(
			                    "MODE"	  => "file",
			                    "SHOW_BORDER"   => FALSE
			                )
			            );?>
			        </div>
			    <?php
			        $sortOptions = array(
			            "price" => "CATALOG_PRICE_1",
			            "name" => "NAME",
			            "rating" => "PROPERTY_rating",
			        );
			        $sort = $sortOptions[$_REQUEST["sort"]] ? $sortOptions[$_REQUEST["sort"]] : $sort;

			    ?>
			    <? $APPLICATION->IncludeComponent(
			            "bitrix:catalog.section",
			            "catalog",
			            array(
			                "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
			                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
			                "ELEMENT_SORT_FIELD" => $sort,
			                "ELEMENT_SORT_ORDER" => $order,
			                "ELEMENT_SORT_FIELD2" => "",
			                "ELEMENT_SORT_ORDER2" => "",
			                "PROPERTY_CODE" => array(
			                    0 => "NEWPRODUCT",
			                    1 => "SALELEADER",
			                    2 => "DISCOUNT",
			                    3 => "",
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
			                "CACHE_TYPE" => "N",
			                "CACHE_TIME" => "36000000",
			                "CACHE_FILTER" => "Y",
			                "CACHE_GROUPS" => "Y",
			                "SET_TITLE" => "N",
			                "MESSAGE_404" => "",
			                "SET_STATUS_404" => "N",
			                "SHOW_404" => "N",
			                "FILE_404" => "",
			                "DISPLAY_COMPARE" => "Y",
			                "PAGE_ELEMENT_COUNT" => "12",
			                "LINE_ELEMENT_COUNT" => "3",
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
			                "PAGER_TEMPLATE" => "load_more",
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
			                "COMPATIBLE_MODE" => "Y"
			            ),
			            false
			        );?>
			    </div>
			</div>
		</div>
	</div>
<?	
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>