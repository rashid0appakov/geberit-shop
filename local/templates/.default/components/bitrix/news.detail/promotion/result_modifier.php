<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    include_once(__DIR__.'/functions.php');
    $arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
    $arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 958;
    $arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 304;

    //DISPLAY_ACTIVE_TO//
    if (!isset($arResult["DISPLAY_ACTIVE_TO"]) && !empty($arResult["ACTIVE_TO"]))
        $arResult["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["ACTIVE_TO"], CSite::GetDateFormat()));

    //DETAIL_PICTURE//
    if (is_array($arResult["DETAIL_PICTURE"])) {
        if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
            $arFileTmp = CFile::ResizeImageGet(
                $arResult["DETAIL_PICTURE"],
                array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true
            );
            $arResult["DETAIL_PICTURE"] = array(
                "SRC" => $arFileTmp["src"],
                "WIDTH" => $arFileTmp["width"],
                "HEIGHT" => $arFileTmp["height"],
            );
        }
    }

    /* -- Get products filter params ---------------------------------------- */
    $arResult["PRODUCTS_FILTER"] = [];

    if (!empty($arResult["PROPERTIES"]['SECTIONS_'.strtoupper(SITE_ID)]["VALUE"]))
        $arResult["PRODUCTS_FILTER"]["SECTION_ID"][] = $arResult["PROPERTIES"]['SECTIONS_'.strtoupper(SITE_ID)]["VALUE"];

    if (!empty($arResult['PROPERTIES']['BRANDS_'.strtoupper(SITE_ID)]["VALUE"]))
        $arResult["PRODUCTS_FILTER"]["PROPERTY_MANUFACTURER"] = $arResult['PROPERTIES']['BRANDS_'.strtoupper(SITE_ID)]["VALUE"];

    if (!empty($arResult["PROPERTIES"]['PRODUCTS_'.strtoupper(SITE_ID)]["VALUE"]))
        $arResult["PRODUCTS_FILTER"]["ID"]  = $arResult["PROPERTIES"]['PRODUCTS_'.strtoupper(SITE_ID)]["VALUE"];

    if (count($arResult["PRODUCTS_FILTER"]) > 1)
        $arResult["PRODUCTS_FILTER"]= [array_merge(
            ['LOGIC' => 'OR'],
            $arResult["PRODUCTS_FILTER"]
        )];
		
	$arResult["PRODUCTS_FILTER"]['ACTIVE'] = 'Y';
	//$arResult["PRODUCTS_FILTER"]['>CATALOG_QUANTITY'] = 0;
	$arResult["PRODUCTS_FILTER"]['SECTION_ACTIVE'] = 'Y';

    if (!empty($arResult["PROPERTIES"]['FILTER_JSON']["~VALUE"])) {
        $arrayJson = json_decode($arResult["PROPERTIES"]['FILTER_JSON']["~VALUE"], true);
        if ($arrayJson['section'])
            $arResult["PRODUCTS_FILTER"]["SECTION_ID"][]   = $arrayJson['section'];
        if ($arrayJson['filter'])
            $arResult["PRODUCTS_FILTER"] = array_merge($arResult["PRODUCTS_FILTER"], $arrayJson['filter']);
    }

    /* -- Get products sections --------------------------------------------- */
    if (!empty($arResult["PRODUCTS_FILTER"]))
        $arResult['SECTIONS']   = getProductSections($arResult["PRODUCTS_FILTER"], $arResult['ID']);

    if (isset($_REQUEST['SECTION_ID']) && (int)$_REQUEST['SECTION_ID'])
        $arResult["PRODUCTS_FILTER"]["SECTION_ID"] = [(int)$_REQUEST['SECTION_ID']];

    //CACHE_KEYS//
    $this->__component->SetResultCacheKeys(
        array(
            "ID",
            "ACTIVE_TO",
            "DISPLAY_ACTIVE_TO",
            "DETAIL_TEXT",
            "PRODUCT_FILTER",
            "SECTIONS"
        )
    );?>