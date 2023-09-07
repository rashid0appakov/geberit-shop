<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult = array(
	"INITIAL" => $arResult,
	"ITEMS" => [],
	"PROMO" => (!empty($GLOBALS['PAGE_DATA']['MENU'][$arParams['KEY']]['PROMO']) ? $GLOBALS['PAGE_DATA']['MENU'][$arParams['KEY']]['PROMO'] : []),
);

$price = current(CClass::getCurrentPriceCode());
foreach($arResult['PROMO'] as $sectionId=>$arSection)
{
	foreach($arSection as $key=>$arItem)
	{
		$arResult['PROMO'][$sectionId][$key]['PRICE'] = $arItem['PRICE'][$price];
	}
}

// restore tree structure
$arItems = array();
$tmp = array(0 => &$arItems);
foreach ($arResult["INITIAL"] as $arItem)
{
	$arItem["ITEMS"] = array();
	$dl = (int) $arItem["PARAMS"]["DEPTH_LEVEL"];
	$tmp[$dl] =& $tmp[$dl - 1][array_push($tmp[$dl - 1], $arItem) - 1]["ITEMS"];
}
unset($tmp);
$arResult["ITEMS"] = $arItems;

//pr($arResult);