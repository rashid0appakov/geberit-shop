<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($arResult['PROPERTIES']['DESCRIPTION']['~VALUE'])
	foreach($arResult['PROPERTIES']['DESCRIPTION']['~VALUE'] AS $k => &$arItem)
		if (preg_match('/#DELIVERY#/msi', $arItem['TEXT']))
			$arItem['TEXT'] = str_replace('#DELIVERY#', CDelivery::getRegionDeliveryData($arResult['PROPERTIES']['LOCATION']['VALUE']), $arItem['TEXT']);
?>