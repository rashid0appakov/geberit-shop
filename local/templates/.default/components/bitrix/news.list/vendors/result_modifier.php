<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
	throw new BitrixNotFoundException();

$arSeries   = [];

/* -- Get brands catalog section ---------------------------------------- */
$arResult['SECTIONS']   = CClass::getBrandsSectionItemsCount();

/* -- Get brand collections --------------------------------------------- */
$arSelect   = [
	"ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_BRAND", "DETAIL_PAGE_URL"
];
$arFilter   = [
	'IBLOCK_ID' => SERIES_IBLOCK_ID,
	'ACTIVE'	=> "Y"
];
$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 500000), $arSelect);
while ($arItem = $dbItems->GetNext())
	$arSeries[$arItem['PROPERTY_BRAND_VALUE']][] = $arItem;

$arResult["LETTERS"] = [];
if (!empty($arResult["ITEMS"])){
	foreach($arResult["ITEMS"] AS $k => &$arItem):
		$arItem['LETTER'] = ToLower(substr($arItem['NAME'], 0, 1));
		if(isset($arResult["LETTERS"][$arItem['LETTER']]))
		{
			$arResult["LETTERS"][$arItem['LETTER']] ++;
		}
		else
		{
			$arResult["LETTERS"][$arItem['LETTER']] = 1;
		}
		$arFile = (is_array($arItem["PREVIEW_PICTURE"]) ? $arItem["PREVIEW_PICTURE"] : CFile::GetFileArray($arItem["PREVIEW_PICTURE"]));
		$arItem["RESIZED"] = CFile::ResizeImageGet(
			$arFile,
			[
				'width' => 170, 'height' => 170
			],
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arItem["SECTIONS"] = $arResult['SECTIONS'][$arItem['ID']];
		$arItem["SERIES"]   = $arSeries[$arItem['ID']];
		/*if (!$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']])
			$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']] = [
				'ID'	=>
			];*/
	endforeach;
}
ksort($arResult["LETTERS"]);	