<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

    /**
     * @var array $arParams
     * @var array $arResult
     * @var SaleOrderAjax $component
     */

    $component = $this->__component;
    $component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

    // -- Get delivery section id ------------------------------------------- //
    /*if (!empty($arResult['DELIVERY'])){
        $arIDs      = [];

        foreach($arResult['DELIVERY'] AS $ID => $arDelivery)
            $arIDs[] = $ID;

        $arFilter   = [
            "DELIVERY_ID" => $arIDs
        ];
        $dbRes = CSaleDelivery::GetLocationList($arFilter);
        while ($arLocation = $dbRes->fetch())
            $arLocations[$arLocation['DELIVERY_ID']][] = $arLocation['LOCATION_ID'];

        foreach($arResult['DELIVERY'] AS $ID => &$arDelivery)
            $arDelivery['IS_MKAD'] = in_array(DEFAULT_GEOLOCATION_ID, $arLocations[$ID]);
    }*/