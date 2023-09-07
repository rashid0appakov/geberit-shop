<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

global $man_show;


// проверка 
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");


//$man = array(420764, 442147, 442617, 443339, 443767);

$arFilter = Array("IBLOCK_ID" => 82, 'INCLUDE_SUBSECTION' => "Y", "ACTIVE" => "Y", "ID" => $arResult['ID'], 'PROPERTY_BRAND' => $man_show); // , array(420764, 442147, 442617, 443339, 443767)

//var_dump($arFilter);

$res = CIBlockElement::GetList(Array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
if ($ob = $res->GetNextElement()) {
	//var_dump(123);
}else{
	@define("ERROR_404","Y");
	CHTTP::SetStatus('404 Not Found');
	include($_SERVER["DOCUMENT_ROOT"]."/404/index2.php");
	die;
}


	$bIsBrandSite = false;
	if (isset($arParams["IS_BRAND_SITE"]) && "Y" == $arParams["IS_BRAND_SITE"])
		$bIsBrandSite = true;
	
	$arSection			  = CClass::getCatalogSection();
	$arResult['SECTIONS']   = [];
	
	// -- Get other items --------------------------------------------------- //
	$arSelect   = [
		"ID", "IBLOCK_ID", "DETAIL_PICTURE", "IBLOCK_SECTION_ID"
	];
	$arFilter   = [
		'IBLOCK_ID'		 => CATALOG_IBLOCK_ID,
		'PROPERTY_SERIES'   => $arResult['ID'],
		'ACTIVE'		=>'Y',
		'!PROPERTY_DISCONTINUED' => 'Y'
	];

	$dbItems = CIBlockElement::GetList(array("ACTIVE_FROM" => "DESC"), $arFilter, FALSE, Array("nPageSize" => 5000), $arSelect);
	while ($arItem = $dbItems->GetNext()){
		if(empty($arSection[$arItem['IBLOCK_SECTION_ID']]))
		{
			continue;
		}
		
		//pr($arItem);
		if (!isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]))
		{
			$arSection[$arItem['IBLOCK_SECTION_ID']]["SECTION_PAGE_URL"] .=
				(!$bIsBrandSite && $arResult['PROPERTIES']['BRAND']['VALUE'] ? 'manufacturer-is-'.$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arResult['PROPERTIES']['BRAND']['VALUE']]['CODE'].'/' : '').
				strtolower($arResult["CODE"]).'/';
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = $arSection[$arItem['IBLOCK_SECTION_ID']];
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['RESIZED'] = [];
		}

		if (count($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['RESIZED']) < 10)
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['RESIZED'][] =
				CFile::ResizeImageGet(
					CFile::GetFileArray($arItem["DETAIL_PICTURE"]),
					array('width' => 178, 'height' => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);

		$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ITEMS_COUNT'] += 1;
	}

	// -- Get brand info ---------------------------------------------------- //
	if ($arResult['PROPERTIES']['BRAND']['VALUE']){
		$arSelect   = [
			"ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "NAME", "LIST_PAGE_URL"
		];
		$arFilter   = [
			'IBLOCK_ID' => BRANDS_IBLOCK_ID,
			'ID'		=> $arResult['PROPERTIES']['BRAND']['VALUE']
		];
		$dbItems = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 1), $arSelect);
		if ($arResult['BRAND'] = $dbItems->GetNext())
			if ($arResult['BRAND']['PREVIEW_PICTURE'])
				$arResult['BRAND']['RESIZED'] =
					CFile::ResizeImageGet(
						is_array($arResult['BRAND']['PREVIEW_PICTURE']) ? $arResult['BRAND']['PREVIEW_PICTURE'] : CFile::GetFileArray($arResult['BRAND']['PREVIEW_PICTURE']),
						[
							'width' => 240, 'height' => 240
						],
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
	}

	// -- Gallery photos ---------------------------------------------------- //
	if (!empty($arResult['PROPERTIES']['PHOTO']['VALUE']))
		foreach($arResult['PROPERTIES']['PHOTO']['VALUE'] AS $photoID)
			$arResult['PHOTOS'][] = CFile::ResizeImageGet(
				CFile::GetFileArray($photoID),
				[
					'width' => 1920, 'height' => 500
				],
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);


	$this->__component->arResultCacheKeys = array_merge($this->__component->arResultCacheKeys, array('SECTIONS', 'BRAND', 'PHOTOS', 'DETAIL_PAGE_URL'));