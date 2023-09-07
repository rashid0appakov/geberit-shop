<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
	use Bitrix\Main\Loader;
	use Bitrix\Main\Entity;
	use Bitrix\Highloadblock as HL;

	//$arCountry  = CClass::getNameCountry();
	$arResult['INTERIORS']  = CClass::Instance()->getNameInterior();

	if (!!$arResult["ITEM"]["PROPERTIES"]["PARAMS"]["VALUE"]){
		$arOdds =   CClass::Instance()->getProductOds();

		foreach($arResult["ITEM"]["PROPERTIES"]["PARAMS"]["VALUE"] AS &$value)
			$arResult["ITEM"]["DISPLAY_PROPERTIES"]["PARAMS"][] = array(
				"NAME"  => $arOdds[$value]["UF_NAME"],
				"IMAGE" => CFile::GetPath($arOdds[$value]["UF_FILE"])
			);
	}

	$arResult['ITEM']['uniqueId'] = uniqid();

	$prices = CCatalogProduct::GetOptimalPrice($arResult['ITEM']['ID']);
	$arResult['ITEM']["ITEM_PRICES"][0]['BASE_PRICE'] = $prices['RESULT_PRICE']['BASE_PRICE'];
	$arResult['ITEM']["ITEM_PRICES"][0]['PRICE'] = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
	// -- Product pictures ------------------------------------------------------ //
	if (empty($arResult['ITEM']['PHOTOS']))
		$arResult['ITEM']['PHOTOS'] = CClass::getPreviewPhotos($arResult['ITEM']["DETAIL_PICTURE"], $arResult['ITEM']["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0]);
	/*
	if($arResult['ITEM']["PROPERTIES"]["SYS_NAME"]["VALUE"])
	{
		$arResult['ITEM']["NAME"] = $arResult['ITEM']["PROPERTIES"]["SYS_NAME"]["VALUE"];
	}
	*/
	use Bitrix\Sale\Internals\DiscountTable;
	Loader::includeModule("iblock");
	
	$res = DiscountTable::getList()->fetchAll();
    $is_discount="N";
	foreach($res as $arItem){
		preg_match("/in_array\((\d*)\,.*\[\\'(.*)\\'\]\[\\'(.*)\\'\]\)/U", $arItem["APPLICATION"], $discountArr);
		if(!empty($discountArr) && $arItem['LID']==SITE_ID){
			$discount_el_id[]=$discountArr[1];
			$discount_iblock_type=$discountArr[2];
			$discount_property=explode("_",$discountArr[3]);
			$discount_name=$arItem["NAME"];
			$discount_value[]=$arItem['SHORT_DESCRIPTION_STRUCTURE']['VALUE'];
			$is_discount="Y";
		}
	}
	if ($is_discount=="Y"){
        $res1 = CIBlockProperty::GetByID($discount_property[1]);
        if($ar_res = $res1->GetNext())
            $CODE=$ar_res["CODE"];

    if(in_array($arResult["ITEM"]["PROPERTIES"][$CODE]["VALUE"],$discount_el_id)){
            $k_d=array_search($arResult["ITEM"]["PROPERTIES"][$CODE]["VALUE"],$discount_el_id);
        	$arResult["DISCOUNT_BASKET"]=array("IS_DISCOUNT"=>"Y","VALUE"=>$discount_value[$k_d]);
        	}
	}

	if(!isset($arResult["ITEM"]["GIFT"]))
	{
		$gifts=CGifts::getGifts([$arResult["ITEM"]["ID"]]);
		$arResult["ITEM"]["GIFT"]=$gifts[$arResult["ITEM"]["ID"]];

	}
	
	$this->__component->SetResultCacheKeys(array('DISCOUNT_BASKET'));