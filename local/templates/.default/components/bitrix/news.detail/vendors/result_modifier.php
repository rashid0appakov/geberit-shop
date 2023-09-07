<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

	$arSection			  = CClass::getCatalogSection();
	
	foreach($arSection as &$arItem){
		unset($arItem['RESIZED']);
	}

	$arResult['SECTIONS']   = [];

	/* -- Get brand catalog section ----------------------------------------- */
	$arSelect   = [
		"ID", "IBLOCK_ID", "DETAIL_PICTURE", "IBLOCK_SECTION_ID"
	];
	$arFilter   = [
		'IBLOCK_ID'				 => CATALOG_IBLOCK_ID,
		'PROPERTY_MANUFACTURER'	 => $arResult['ID'],
		'ACTIVE'					=> 'Y'
	];

	$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 5000), $arSelect);
	while ($arItem = $dbItems->GetNext()){
		if(empty($arSection[$arItem['IBLOCK_SECTION_ID']]['NAME']))
		{
			continue;
		}
		if (!isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])){
			$arSection[$arItem['IBLOCK_SECTION_ID']]["SECTION_PAGE_URL"] .= 'manufacturer-is-'.$arResult["CODE"].'/';
				
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = $arSection[$arItem['IBLOCK_SECTION_ID']];
		}
		
		if(count($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['RESIZED']) < 10){
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['RESIZED'][] =
				CFile::ResizeImageGet(
					CFile::GetFileArray($arItem["DETAIL_PICTURE"]),
					array('width' => 178, 'height' => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
		}
		
		$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ITEMS_COUNT'] += 1;
	}

	/* -- Get brand collections --------------------------------------------- */
	$arSelect   = [
		"ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL"
	];
	$arFilter   = [
		'IBLOCK_ID' => SERIES_IBLOCK_ID,
		'ACTIVE'	=> "Y",
		'PROPERTY_BRAND'	=> $arResult['ID']
	];
	$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 500), $arSelect);
	while($arItem = $dbItems->GetNext())
		$arResult['SERIES'][] = $arItem;

	// -- Get brand info ---------------------------------------------------- //
	if ($arResult['PREVIEW_PICTURE'])
		$arResult['RESIZED'] =
			CFile::ResizeImageGet(
				is_array($arResult['PREVIEW_PICTURE']) ? $arResult['PREVIEW_PICTURE'] : CFile::GetFileArray($arResult['PREVIEW_PICTURE']),
				[
					'width' => 240, 'height' => 240
				],
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);

	$this->__component->arResultCacheKeys = array_merge($this->__component->arResultCacheKeys, array('SECTIONS', 'SERIES', 'DETAIL_PAGE_URL'));