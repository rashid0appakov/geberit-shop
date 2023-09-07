<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();
	include_once('functions.php');

	$arSections = getSeriesSectionItemsCount();

	if (!empty($arResult["ITEMS"]))
		foreach($arResult["ITEMS"] AS $k => &$arItem):
			if ($arItem["DETAIL_PICTURE"]){
				$arFile = (is_array($arItem["DETAIL_PICTURE"]) ? $arItem["DETAIL_PICTURE"] : CFile::GetFileArray($arItem["DETAIL_PICTURE"]));
				$arItem["RESIZED"] = CFile::ResizeImageGet(
					$arFile,
					[
						'width' => 170, 'height' => 170
					],
					BX_RESIZE_IMAGE_EXACT,
					true
				);
			}else
				$arItem["RESIZED"]['src'] = SITE_DEFAULT_PATH.'/images/series_blank.jpg';
			$arItem["SECTIONS"] = $arSections[$arItem['ID']];
			/*if (!$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']])
				$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']] = [
					'ID'	=>
				];*/
		endforeach;