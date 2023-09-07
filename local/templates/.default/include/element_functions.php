<?php
/*
function GetAdditionals($arFilter, $arOdds, $arSections, $sort = []) {
	if (empty($arFilter))
		return FALSE;

	$arSelect   = [
		"ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID", "CATALOG_QUANTITY",
		"PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL"
	];
	$sectionName= '';

	$res = CIBlockElement::GetList(["SORT" => "ASC"], $arFilter, false, Array("nPageSize"=>100), $arSelect);
	
	$arQueryResult = [];
	while($obItem = $res->GetNextElement())
	{
		$arFields = $obItem->GetFields();
		$arFields['PROPERTIES'] = $obItem->GetProperties();
		
		if(!empty($sort))
		{
			$arCustomSort[] = (!empty($sort[$arFields['ID']])) ? $sort[$arFields['ID']] : 100;
		}
		$arQueryResult[] = $arFields;
	}
	
	if(!empty($sort))
	{
		array_multisort($arQueryResult, SORT_ASC, SORT_NUMERIC, $arCustomSort);
	}
	
	foreach($arQueryResult as $arFields)
	{
		$sectionName = $arSections[$arFields['IBLOCK_SECTION_ID']]['NAME'];

		if(!$sectionName)
		{
			continue;
		}

		$prices = CPrice::GetList(['CATALOG_GROUP_ID' => 'ASC'], ["PRODUCT_ID" => $arFields['ID']]);
		while($arPrice = $prices->Fetch()){
			$arPrice['PRINT_PRICE'] = CurrencyFormat($arPrice['PRICE'], $arPrice['CURRENCY']);
			$arFields['ITEM_PRICES'][]  = $arPrice;
		}

		$arFields['PHOTOS'] = CClass::getPreviewPhotos($arFields["DETAIL_PICTURE"], $arFields['PROPERTIES']["MORE_PHOTO"]["VALUE"][0]);

		$arResult[$sectionName][$arFields['ID']] = $arFields;
	}
	
	return $arResult;
}
*/
function GetAdditionalsNew($arIds, $arOdds, $arSections, $sort = [], $IBLOCK_ID) {
	
	if (empty($arIds))
		return FALSE;
	
	global $USER;
	
	$arFilterIds = array();

	foreach($arIds as $type => $arItems){
		foreach($arItems as $arItem){
			$arFilterIds[] = $arItem;
		}
	}

	if (empty($arFilterIds))
		return FALSE;

	$arFilter = array(
		"ID"		=> array_unique($arFilterIds),
		"IBLOCK_ID" => $IBLOCK_ID,
		"ACTIVE"	=> "Y", 
		'>CATALOG_QUANTITY' => 0, 
		'!PROPERTY_DISCONTINUED' => 'Y'
	);

	$arSelect   = [
		"ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID", "CATALOG_QUANTITY",
		"PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_*"
	];
	$sectionName= '';

	$res = CIBlockElement::GetList([GetSortField()=>"desc","SORT" => "ASC"], $arFilter, false, Array(), $arSelect);	
	$arQueryResult = [];
	while($arFields = $res->GetNext())
	{			
		$arProp = array();
		foreach($arFields as $key => $arItem){
			if(preg_match("/PROPERTY_(\d+)/i", $key, $matches)){		
			
				$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']] = $GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]];
				
				//Если свойство список
				if($GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['PROPERTY_TYPE'] == 'L'){
					
					if(is_array($arItem)){
						foreach($arItem as $listValue){
							$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']]['VALUE'][$listValue] = $GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['LIST_VALUES_CUSTOM'][$listValue]['VALUE'];
						}
					}
					else{						
						$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']]['VALUE'] = $GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['LIST_VALUES_CUSTOM'][$arItem]['VALUE'];
					}
					
				}
				else{
					$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']]['VALUE'] = $arItem;
				}
				
				unset($arFields[$key]);
			}
			
			if(preg_match("/PROPERTY_VALUE_ID_(\d+)/i", $key, $matches)){
				$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']] = $GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]];
				$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']]['VALUE_ID'] = $arItem;
				
				unset($arFields[$key]);
			}
			
			if(preg_match("/DESCRIPTION_(\d+)/i", $key, $matches)){
				$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']] = $GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]];
				$arProp[$GLOBALS['PAGE_DATA']['LIST_PROP'][$matches[1]]['CODE']]['DESCRIPTION'] = $arItem;
				
				unset($arFields[$key]);
			}
		}
		
		$arFields['PROPERTIES'] = $arProp;

		if(!empty($sort))
		{
			$arCustomSort[] = (!empty($sort[$arFields['ID']])) ? $sort[$arFields['ID']] : 100;
		}
		$arQueryResult[] = $arFields;
	}
	
	if(!empty($sort))
	{
		array_multisort($arQueryResult, SORT_ASC, SORT_NUMERIC, $arCustomSort);
	}
	
	foreach($arQueryResult as $arFields)
	{
		$sectionName = $arSections[$arFields['IBLOCK_SECTION_ID']]['NAME'];

		if(!$sectionName)
		{
			continue;
		}
		
		$priceId = 1;
		if(in_array('SPB', CClass::getCurrentPriceCode())){
			$priceId = 2;
		}
		if(in_array('EKB', CClass::getCurrentPriceCode())){
			$priceId = 4;
		}
		
		$db_res = CPrice::GetList(
				array(),
				array(
						"PRODUCT_ID" => $arFields['ID'],
						"CATALOG_GROUP_ID" => $priceId
					),
				false,
				false,
				array()
			);
		if ($ar_res = $db_res->Fetch()){
			
			$arPrice['BASE_PRICE'] = $ar_res['PRICE'];
			$arPrice['PRINT_BASE_PRICE'] = CurrencyFormat($ar_res['PRICE'], $ar_res['CURRENCY']);
			
			$arPrice['PRICE'] = $ar_res['PRICE'];
			$arPrice['PRINT_PRICE'] = CurrencyFormat($ar_res['PRICE'], $ar_res['CURRENCY']);
			
			//Скидка
			$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arFields['ID'], [2], "N", $priceId);
			if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
				
				$discountRrice = CCatalogProduct::CountPriceWithDiscount($ar_res['PRICE'], $ar_res['CURRENCY'], $arDiscounts);
				
				$arPrice['PRICE'] = $discountRrice;
				$arPrice['PRINT_PRICE'] = CurrencyFormat($discountRrice, $ar_res['CURRENCY']);
			}

			$arFields['ITEM_PRICES'][]  = $arPrice;
		}		

		$arFields['PHOTOS'] = CClass::getPreviewPhotos($arFields["DETAIL_PICTURE"], $arFields['PROPERTIES']["MORE_PHOTO"]["VALUE"][0]);
		
		foreach($arIds as $type => $arItems){
			foreach($arItems as $arItem){
				if($arFields['ID'] == $arItem){
					$arResult[$type][$sectionName][$arFields['ID']] = $arFields;
				}
			}
		}		
	}
	
	return $arResult;
}
?>