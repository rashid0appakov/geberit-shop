<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?

$APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css");
if (!CModule::IncludeModule("search") || !CModule::IncludeModule("iblock"))
{
    die('Error include Module');
}

//pr($_REQUEST);
// поиск по части слова
// $q = trim($_REQUEST['q']);

// if($q > 0 and $q == $_REQUEST['q'])
// {
//     $arSelect = Array("ID", "IBLOCK_ID", "ACTIVE", "DETAIL_PAGE_URL", "NAME", 'PROPERTY_ARTNUMBER');
//     $arFilter = Array(array("LOGIC" => "OR", "PROPERTY_ARTNUMBER" => '%'.$q.'%', "NAME" => '%'.$q.'%'), "IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
// //    var_dump($arFilter);
//     $arElement = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect)->GetNext();
//     if(!empty($arElement['DETAIL_PAGE_URL']))
//     {
//         // var_dump($arElement['PROPERTY_ARTNUMBER_VALUE']);
//         // if ($arElement['PROPERTY_ARTNUMBER_VALUE'] == $_REQUEST['q']){
//         //     $_REQUEST['q'] = '"'. $_REQUEST['q'] . '"';
//         // }else{
//             $_REQUEST['q'] = $arElement['PROPERTY_ARTNUMBER_VALUE']; // $arElement['~NAME'];
// //        }
// //        var_dump($arElement['PROPERTY_ARTNUMBER_VALUE']);
//         //LocalRedirect($arElement['DETAIL_PAGE_URL'], true);
//         //die($arElement['DETAIL_PAGE_URL']);
//     }
// }

// //var_dump( $_REQUEST['q']);

// //var_dump($_REQUEST['q']);
// $_REQUEST['q'] = '"'. $_REQUEST['q'] . '"';


    $APPLICATION->IncludeComponent(
        "bitrix:catalog.compare.list",
        "",
        array(
            "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "NAME" => "CATALOG_COMPARE_LIST",
            "DETAIL_URL" => "/product/#ELEMENT_CODE#/",
            "COMPARE_URL" => "/compare/",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "",
            'POSITION_FIXED' => '',
            'POSITION' => ''
        ),
        false,
        array("HIDE_ICONS" => "Y")
    );



$arBreadcrumbs[0]['NAME'] = 'Поиск';
$arBreadcrumbs[0]['URL'] = '/catalog/?q='.$_GET['q'].'&how=r';
?>
<style>
.product {
    width: 25% !important;
        min-width: 25%;
}
</style>
<div class="goods">
    <div class="container goods__container">
        <div class="goods__breadcrumbs">
            <?$APPLICATION->IncludeComponent(
                "custom:catalog.breadcrumbs",
                "",
                array(
                    "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "CUSTOM_ITEMS" => $arBreadcrumbs,
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_GROUPS" => "N",
                ),
                false
            );?>
            <div class="breadcrumbs__need-help">
                <a href="#">Нужна помощь в выборе душевой кабины?</a>
            </div>
        </div>
        <div class="goods__title">
            <h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
        </div>
        <div class="goods__wrapper">
            <div class="goods__card">
                <?
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.search",
                    "catalog",
                    array(
                        "IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
                        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                        "ELEMENT_SORT_FIELD" => "PROPERTY_PRICE_UPDATE",
                        "ELEMENT_SORT_ORDER" => "desc",
                        "ELEMENT_SORT_FIELD2" => "SORT",
                        "ELEMENT_SORT_ORDER2" => "ASC",
                        "PAGE_ELEMENT_COUNT" => "24",
                        "LINE_ELEMENT_COUNT" => "",
                        "PROPERTY_CODE" => array(
                            0 => "",
                            1 => "ARTNUMBER",
                            2 => "MANUFACTURER",
                            3 => "ODDS",
                            4 => "MORE_PHOTO",
                            5 => "",
                        ),
                        "PROPERTY_CODE_MOBILE" => '',
                        "OFFERS_CART_PROPERTIES" => array(
                            0 => "COLOR,PROP2,PROP3",
                        ),
                        "OFFERS_FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "OFFERS_PROPERTY_CODE" => array(
                            0 => "ARTNUMBER",
                            1 => "",
                        ),
                        "OFFERS_SORT_FIELD" => "sort",
                        "OFFERS_SORT_ORDER" => "asc",
                        "OFFERS_SORT_FIELD2" => "id",
                        "OFFERS_SORT_ORDER2" => "desc",
                        "OFFERS_LIMIT" => "",
                        "SECTION_URL" => "",
                        "DETAIL_URL" => "",
                        "BASKET_URL" => "/personal/cart/",
                        "ACTION_VARIABLE" => "action",
                        "PRODUCT_ID_VARIABLE" => "",
                        "PRODUCT_QUANTITY_VARIABLE" => "",
                        "PRODUCT_PROPS_VARIABLE" => "",
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        'DISPLAY_COMPARE' => "Y",
                        "PRICE_CODE" => CClass::getCurrentPriceCode(),
                        "USE_PRICE_COUNT" => "N",
                        "SHOW_PRICE_COUNT" => "1",
                        "PRICE_VAT_INCLUDE" => "Y",
                        "ADD_PROPERTIES_TO_BASKET" => "N",
                        "PARTIAL_PRODUCT_PROPERTIES" => "N",
                        "PRODUCT_PROPERTIES" => array(),
                        "USE_PRODUCT_QUANTITY" => "N",
                        "CONVERT_CURRENCY" => "N",
                        "CURRENCY_ID" => "",
                        'HIDE_NOT_AVAILABLE' => "N",
                        'HIDE_NOT_AVAILABLE_OFFERS' => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "PAGER_TITLE" => "Товары",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_TEMPLATE" => "load_more",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "0",
                        "PAGER_SHOW_ALL" => "N",
                        "LAZY_LOAD" => '',
                        "MESS_BTN_LAZY_LOAD" => '',
                        "LOAD_ON_SCROLL" => '',
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

                        "PAGE_RESULT_COUNT" =>  "50",
                        
                        "NO_WORD_LOGIC" => "Y",
                        "USE_LANGUAGE_GUESS" => "N",
                        "CHECK_DATES" =>  "Y",

                        'LABEL_PROP' => "",
                        'LABEL_PROP_MOBILE' => '',
                        'LABEL_PROP_POSITION' => '',
                        'ADD_PICT_PROP' => "",
                        'PRODUCT_DISPLAY_MODE' => "",
                        'PRODUCT_BLOCKS_ORDER' => '',
                        'PRODUCT_ROW_VARIANTS' => '',
                        'ENLARGE_PRODUCT' => '',
                        'ENLARGE_PROP' => '',
                        'SHOW_SLIDER' => '',
                        'SLIDER_INTERVAL' => '',
                        'SLIDER_PROGRESS' =>  '',

                        'OFFER_ADD_PICT_PROP' => "",
                        'OFFER_TREE_PROPS' => "",
                        'PRODUCT_SUBSCRIPTION' => "",
                        'SHOW_DISCOUNT_PERCENT' => "",
                        'SHOW_OLD_PRICE' => "",
                        'SHOW_MAX_QUANTITY' => "",
                        'MESS_SHOW_MAX_QUANTITY' => '',
                        'RELATIVE_QUANTITY_FACTOR' => "",
                        'MESS_RELATIVE_QUANTITY_MANY' => "",
                        'MESS_RELATIVE_QUANTITY_FEW' => "",
                        'MESS_BTN_BUY' => "",
                        'MESS_BTN_ADD_TO_BASKET' => "",
                        'MESS_BTN_SUBSCRIBE' => "",
                        'MESS_BTN_DETAIL' => "",
                        'MESS_NOT_AVAILABLE' => "",
                        'MESS_BTN_COMPARE' => "",

                        'USE_ENHANCED_ECOMMERCE' => "",
                        'DATA_LAYER_NAME' => "",
                        'BRAND_PROPERTY' => "",

                        'TEMPLATE_THEME' => "",
                        'ADD_TO_BASKET_ACTION' => "",
                        'SHOW_CLOSE_POPUP' => "",
                        'COMPARE_PATH' => "/compare/",
                        'COMPARE_NAME' => "CATALOG_COMPARE_LIST",
                        'USE_COMPARE_LIST' => 'Y',
                        
                        "RESTART" => "Y",
                    ),
                    false,
                    array("HIDE_ICONS" => "Y")
                );
                ?>
            </div>
        </div>
    </div>
</div>
<?

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>