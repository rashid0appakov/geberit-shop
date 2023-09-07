<div class="carousel carousel-tabs goods__carousel-tabs hero">
    <div class="container is-widescreen">
        <section class="tabs">
            <ul class="tabs__header">
                <li class="tabs__header--title js-tabs-title active" data-tab="#tab-1">ВСЕ ТОВАРЫ</li>
                <li class="tabs__header--title js-tabs-title" data-tab="#tab-2">ВЕНТИЛЯТОРЫ</li>
                <li class="tabs__header--title js-tabs-title" data-tab="#tab-3">УНИТАЗЫ</li>
                <li class="tabs__header--title js-tabs-title" data-tab="#tab-4">КРЕПЕЖИ</li>
                <li class="tabs__header--title js-tabs-title" data-tab="#tab-5">ЧИСТЯЩИЕ СРЕДСТВА</li>
            </ul>
            <div class="tabs__underline js-tabs-underline"></div>
        </section>
        <div class="content">
            <div class="content tabs__content js-tabs-content active" id="tab-1">
                <?global $arrFilter;
                $arrFilter = array(
                    "CATALOG_AVAILABLE" => 'Y',
                    "SECTION_ID" => $currentElement["IBLOCK_SECTION_ID"],
                );
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "carousel",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "RAND",
                        "ELEMENT_SORT_ORDER" => "ASC",
                        "ELEMENT_SORT_FIELD2" => "",
                        "ELEMENT_SORT_ORDER2" => "",
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
                        "PAGE_ELEMENT_COUNT" => "10",
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
                        "DISPLAY_BOTTOM_PAGER" => "N",
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
                        "COMPATIBLE_MODE" => "Y"
                    ),
                    false
                );?>
            </div>
            <div class="content tabs__content js-tabs-content" id="tab-2">
                <?global $arrFilter;
                $arrFilter = array(
                    "SECTION_ID" => $currentElement["IBLOCK_SECTION_ID"],
                    "CATALOG_AVAILABLE" => 'Y',
                );
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "carousel",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "RAND",
                        "ELEMENT_SORT_ORDER" => "ASC",
                        "ELEMENT_SORT_FIELD2" => "",
                        "ELEMENT_SORT_ORDER2" => "",
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
                        "PAGE_ELEMENT_COUNT" => "10",
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
                        "DISPLAY_BOTTOM_PAGER" => "N",
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
                        "COMPATIBLE_MODE" => "Y"
                    ),
                    false
                );?>
            </div>
            <div class="content tabs__content js-tabs-content" id="tab-2">
                <?global $arrFilter;
                $arrFilter = array(
                    "SECTION_ID" => $currentElement["IBLOCK_SECTION_ID"],
                    "CATALOG_AVAILABLE" => 'Y',
                );
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "carousel",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "RAND",
                        "ELEMENT_SORT_ORDER" => "ASC",
                        "ELEMENT_SORT_FIELD2" => "",
                        "ELEMENT_SORT_ORDER2" => "",
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
                        "PAGE_ELEMENT_COUNT" => "10",
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
                        "DISPLAY_BOTTOM_PAGER" => "N",
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
                        "COMPATIBLE_MODE" => "Y"
                    ),
                    false
                );?>
            </div>
            <div class="content tabs__content js-tabs-content" id="tab-2">
                <?global $arrFilter;
                $arrFilter = array(
                    "SECTION_ID" => $currentElement["IBLOCK_SECTION_ID"],
                    "CATALOG_AVAILABLE" => 'Y',
                );
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "carousel",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "RAND",
                        "ELEMENT_SORT_ORDER" => "ASC",
                        "ELEMENT_SORT_FIELD2" => "",
                        "ELEMENT_SORT_ORDER2" => "",
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
                        "PAGE_ELEMENT_COUNT" => "10",
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
                        "DISPLAY_BOTTOM_PAGER" => "N",
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
                        "COMPATIBLE_MODE" => "Y"
                    ),
                    false
                );?>
            </div>
            <div class="content tabs__content js-tabs-content" id="tab-2">
                <?global $arrFilter;
                $arrFilter = array(
                   "SECTION_ID" => $currentElement["IBLOCK_SECTION_ID"],
                    "CATALOG_AVAILABLE" => 'Y',
                );
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "carousel",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "RAND",
                        "ELEMENT_SORT_ORDER" => "ASC",
                        "ELEMENT_SORT_FIELD2" => "",
                        "ELEMENT_SORT_ORDER2" => "",
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
                        "PAGE_ELEMENT_COUNT" => "10",
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
                        "DISPLAY_BOTTOM_PAGER" => "N",
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
                        "COMPATIBLE_MODE" => "Y"
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</div>