<?
$arResult['CHECKED'] = false;

foreach( $arResult["ITEMS"] as $key=>$arItem ) {
	foreach($arItem["VALUES"] as $val => $ar) {
		if ( $ar["CHECKED"] ) {
			$arResult["ITEMS"][$key]["DISPLAY_EXPANDED"] = 'Y';
			$arResult["ITEMS"][$key]["DISPLAY_CHECKED"] = 'Y';
			$arResult['CHECKED'] = true;
			//break;
		}

		if ( $ar["DISABLED"] )
			unset( $arResult["ITEMS"][$key]["VALUES"][$val] );
	}
}

foreach($arResult["ITEMS"] as $key => $arItem) {
	$matches = [];
	$returnResult = preg_match('/\[(.*)\]/', $arItem['NAME'], $matches);

	if (count($matches) > 1) {
		$arItem['NAME'] = CClass::getNormalNameProp($arItem['NAME']);
		$arItem['PROPERTY_GROUP'] = $matches[1];
		$arPropertyGroups[$matches[1]][] = $arItem;
		unset($arResult["ITEMS"][$key]);
	}

	unset($matches);
	unset($returnResult);
}

$arResult["PROPERTY_GROUPS"] = $arPropertyGroups;

$arResult["IS_ORDERED_PARAMS"] = "N";

if (isset($arParams["SECTION_ID"]) && isset($arParams["FILTER_SECTION_PROPERTIES_ORDER"]) && 
			isset($arParams["FILTER_SECTION_PROPERTIES_ORDER"][$arParams["SECTION_ID"]]) && is_array($arParams["FILTER_SECTION_PROPERTIES_ORDER"][$arParams["SECTION_ID"]]))
{
	$arSectionPropertiesOrder = $arParams["FILTER_SECTION_PROPERTIES_ORDER"][$arParams["SECTION_ID"]];
	$arOrderedItems = Array();
	
	foreach ($arSectionPropertiesOrder as $code)
	{
		$arOrderedItems[$code] = Array();
	}
	
	foreach ($arResult["ITEMS"] as $key => $arItem)
	{
		$code = $arItem["CODE"];
		
		if (in_array($code, $arSectionPropertiesOrder))
		{
			$arOrderedItems[$code] = $arItem;
			unset($arResult["ITEMS"][$key]);
		}
	}
	
	foreach ($arOrderedItems as $code => $arItem)
	{
		if (empty($arItem))
			unset($arOrderedItems[$key]);
	}
	
	if (!empty($arOrderedItems))
	{
		$arResult["ORDERED_ITEMS"] = Array();
		
		foreach ($arOrderedItems as $arItem)
			$arResult["ORDERED_ITEMS"][$arItem["ID"]] = $arItem;
	}
	
	if (!empty($arResult["ORDERED_ITEMS"]))
		$arResult["IS_ORDERED_PARAMS"] = "Y";
	
	unset($arSectionPropertiesOrder, $arOrderedItems, $key, $arItem, $code);
}
?>