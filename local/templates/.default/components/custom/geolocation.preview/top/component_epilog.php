<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (CModule::IncludeModule('grishchenko.mask')) {
	hashTags::getInstance()->set('#geo#', $arResult["GEOLOCATION_INFO"]["CITY_NAME"]);
}