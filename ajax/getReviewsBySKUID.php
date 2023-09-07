<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent(
"onlineservice:mneniya.view",
".default",
    Array(
        "SKU_ID" => $_GET['sku_id'],
        "CLIENT_ID" => '0A50DACD-1CC5-446B-9D61-89199F6BD08D',
        "TYPE_REVIEWS" => 'All',
        "START" => isset($_GET['start'])?$_GET['start']:0,
        "COUNT" => 10,
        "ORDER_BY" => '',
        "SORTING_ORDER" => '',
        "FILTER_BY" => '',
        "FILTER_VALUES" => '',
        "DOP_REVIEWS" => isset($_GET['dop_reviews'])?$_GET['dop_reviews']:'N',
    ),
    false
);
