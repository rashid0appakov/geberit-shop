<?

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Loader::includeModule("iblock");

if (count($arResult["ITEMS"]) <= 0) return;

$itemIds = array_column($arResult["ITEMS"], "PRODUCT_ID");
$ob = CIBlockElement::GetList(
	array(),
	array(
		"ID" => $itemIds,
	),
	false,
	false,
	array(
		"ID",
		"IBLOCK_ID",
		"NAME",
		"DETAIL_PICTURE",
	)
);
while ($arElement = $ob->Fetch())
{
	foreach ($arResult["ITEMS"] as &$arItem)
	{
		if ($arItem["PRODUCT_ID"] == $arElement["ID"])
		{
			$arItem["ELEMENT"] = $arElement;
		}
	}
	unset($arItem);
}