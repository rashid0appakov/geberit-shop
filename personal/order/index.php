<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Личный кабинет");
?>
    <?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.detail",
            "confirm_order",
        Array(
            "PATH_TO_LIST" => "order_list.php",
            "PATH_TO_CANCEL" => "order_cancel.php",
            "PATH_TO_PAYMENT" => "payment.php",
            "PATH_TO_COPY" => "",
            "ID" => $_GET['ORDER_ID'],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_GROUPS" => "Y",
            "SET_TITLE" => "Y",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "PICTURE_WIDTH" => "110",
            "PICTURE_HEIGHT" => "110",
            "PICTURE_RESAMPLE_TYPE" => "1",
            "CUSTOM_SELECT_PROPS" => array(),
            "PROP_1" => Array(),
            "PROP_2" => Array()
        )
    );?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>