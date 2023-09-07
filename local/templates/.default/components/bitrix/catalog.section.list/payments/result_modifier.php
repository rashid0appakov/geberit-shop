<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$sectionId  = array();
$sectionItem= array();
if (!empty($arResult["SECTIONS"])){
	foreach($arResult["SECTIONS"] AS $key => $arSection)
		$sectionId[] = $arSection["ID"];

	$arSort	 = ["SORT" => "ASC", "NAME" => "ASC"];
	$arFilter   = [
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"SECTION_ID"=> $sectionId,
		"INCLUDE_SUBSECTIONS" => "N",
		"ACTIVE" => "Y"
	];
	$arSelect   = [
		"ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PROPERTY_LOGO_1", "PROPERTY_URL"
	];
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, FALSE, FALSE, $arSelect);
	while($arItem = $rsElements->GetNext()) {
		if (isset($arItem["PROPERTY_LOGO_1_VALUE"]) && $arItem["PROPERTY_LOGO_1_VALUE"] > 0)
			$arItem["LOGO_1"] = CFile::ResizeImageGet(
				$arItem["PROPERTY_LOGO_1_VALUE"],
				array("width" => 66, "height" => 66),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);

		$arResult['ITEMS'][$arItem["IBLOCK_SECTION_ID"]][] = $arItem;
	}
}

$this->__component->arResultCacheKeys = array_merge($this->__component->arResultCacheKeys, array('ITEMS'));