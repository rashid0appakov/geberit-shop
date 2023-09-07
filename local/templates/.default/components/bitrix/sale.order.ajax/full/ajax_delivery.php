<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?php
    $isAjax     = ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        $_REQUEST["AJAX_CALL"] == "Y");

	if (!$isAjax || !check_bitrix_sessid())
        die();
    $_REQUEST["AJAX_CALL"] == "Y";

    $price      = (int)$_POST['PRICE'];
    $locationID = (int)$_POST['REGION'] ? (int)$_POST['REGION'] : DEFAULT_GEOLOCATION_ID;
    $json = [
        "status"    => 'success'
    ];
    if (!$price || !$locationID){
        $json = [
            "status"    => 'error',
            "msg"       => 'Delivery params error!'
        ];
    }else{
        CModule::IncludeModule("sale");
        $arResult   = [];
        $arIDs      = [];
        $arFilter   = [
            "+<=ORDER_PRICE_FROM" => $price,
            "+>=ORDER_PRICE_TO" => $price,
            "LOCATION"          => $locationID,
            "ACTIVE"            => 'Y',
            "LID"  => NULL
        ];
        $arOrder    = [
            "SORT" => "ASC",
            "NAME" => "ASC"
        ];
        $arSelect   = ["*"];

        // -- Get delivery services for all site ---------------------------- //
        $dbRes = CSaleDelivery::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelect);
        while ($arDelivery = $dbRes->fetch())
            $arResult[$arDelivery["ID"]] = $arDelivery;

        $arFilter['LID']= SITE_ID;
        // -- Get delivery services for current site ------------------------ //
        $dbRes = CSaleDelivery::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelect);
        while ($arDelivery = $dbRes->fetch())
            $arResult[$arDelivery["ID"]] = $arDelivery;

        if (!empty($arResult)){
            foreach($arResult AS &$arDelivery){
                if ($arDelivery['LOGOTIP']){
                    $arFile = CFile::GetFileArray($arDelivery['LOGOTIP']);
                    $arDelivery['LOGOTIP_SRC']  = $arFile['SRC'];
                }
                $arDelivery['PRICE_FORMATED']    =
                    !$arDelivery['PRICE'] ?
                    CClass::mb_ucfirst(GetMessage('PRICE_FREE_DEFAULT')) :
                    $arDelivery['PRICE'];
            }

            $arIDs  = array_keys($arResult);

            // -- Get delivery service section id --------------------------- //
            $arSections = [];

            $arFilter   = [
                "ID" => $arIDs
            ];
            $arSelect   = [
                "ID", "PARENT_ID"
            ];
            $arOrder    = [
                "PARENT_ID" => "ASC",
                "NAME" => "ASC"
            ];
            $dbRes = \Bitrix\Sale\Delivery\Services\Table::getList(array("filter" => $arFilter, "select" => $arSelect, "order" => $arOrder));
            while ($arSection = $dbRes->fetch())
                $arSections[$arSection["ID"]] = $arSection;

            foreach($arResult AS $ID => &$arDelivery)
                $arDelivery['PARENT_ID'] = $ID == 3 ? 3 : $arSections[$ID]['PARENT_ID'];

            // -- Get delivery restrictions for locations ------------------- //
            if ($locationID == DEFAULT_GEOLOCATION_ID)
                foreach($arResult AS $ID => &$arDelivery){
                    if ($arDelivery['PARENT_ID'] == CClass::DELIVERY_SELF_SECTION_ID)
                        $arDelivery['IS_MKAD'] = \Bitrix\Sale\Delivery\DeliveryLocationTable::checkConnectionExists($ID, $locationID,['LOCATION_LINK_TYPE' => 'ID']);
                }

            // -- Set service title ----------------------------------------- //
            foreach($arResult AS $ID => &$arDelivery)
                $arDelivery['NAME'] = CClass::getDeliveryServiceName($ID, $arDelivery['NAME'], $locationID, $arDelivery['PARENT_ID']);
        }
        $json['ITEMS']= $arResult;
        $json['locationID'] = $locationID;
    }

    CClass::Instance()->RenderJSON($json);
    ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>