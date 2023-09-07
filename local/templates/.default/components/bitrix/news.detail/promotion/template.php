<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<? if (is_array($arResult["DETAIL_PICTURE"])):?>
	<div class="descr-pic">
		<img
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	</div>
<?endif?>
<? if ($arResult['DETAIL_TEXT']):?>
<div class="descr"><?=$arResult['DETAIL_TEXT']?></div>
<? endif;?>
<?php
if ($arResult["PRODUCTS_FILTER"]):
	global $arrFilter;
	$arrFilter = $arResult["PRODUCTS_FILTER"];

    $sort   = $_REQUEST["sort"] ? $_REQUEST["sort"] : 'SORT';
	$order  = in_array(strtolower($_REQUEST["order"]), ['asc', 'desc']) ? ($_REQUEST["order"] == "desc" ? "DESC" : "ASC") : 'ASC';
?>
<div class="goods__wrapper">
    <? if (!empty($arResult['SECTIONS'])):?>
    <div class="goods__filter">
        <div class="goods__filter-container">
            <div class="goods__filter-content goods__filter-content--mobile">
                <div class="goods__filter-close-button"></div>
                <div class="filter">
                    <div class="filter__item" style="margin-bottom: 25px !important;">
                        <div class="filter__title--toggle is-expanded">
                            <div aria-expanded="true" aria-controls="accordion1" class="filter__title accordion-title accordionTitle js-accordionTrigger is-expanded"><?=GetMessage('CT_SELECT_SECTION')?></div>
                        </div>
                        <div class="filter__content accordion-content accordionItem is-expanded" id="accordion1" aria-hidden="false">
                            <ul class="filter__content-list">
                                <li class="filter__content-item filter__content-item--toggle active">
                                    <a href="/promotions/<?=$arResult['CODE']?>/">
                                        <span><?=GetMessage('CT_SELECT_ALL')?></span>
                                        <span><?=$arResult['SECTIONS']['ALL']?></span>
                                    </a>
                                </li>
                                <? foreach($arResult['SECTIONS'] AS $key => $arItem):
                                    if ($key == 'ALL') continue;
                                ?>
                                <li class="filter__content-item filter__content-item--toggle active">
                                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>?SECTION_ID=<?=$key?>" title="<?=$arItem['NAME']?>">
                                        <span><?=$arItem['NAME']?></span>
                                        <span><?=$arItem['COUNT']?></span>
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
    <? endif;?>
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
<? endif;?>