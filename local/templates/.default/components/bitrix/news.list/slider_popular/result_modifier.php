<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();
	//include_once('functions.php');

	//$arSections = getSeriesSectionItemsCount();


// проверка 
if (!empty($arResult["ITEMS"])){
	foreach($arResult["ITEMS"] AS $k => &$arItem){
		$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
		$arFilter = Array("IBLOCK_ID" => 84, 'INCLUDE_SUBSECTION' => "Y", "ACTIVE" => "Y", "PROPERTY_SERIES" => $arItem['ID'], 'PROPERTY_BRAND' => array(420764)); // , 

//		var_dump($arFilter);

		$res = CIBlockElement::GetList(Array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
		if ($ob = $res->GetNextElement()) {
		}else{
			unset($arResult["ITEMS"][$k]);
		}
	}	
}

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
				unset($arResult["ITEMS"][$k]);
				// $arItem["RESIZED"]['src'] = SITE_DEFAULT_PATH.'/images/series_blank.jpg';
				
				
			//$arItem["SECTIONS"] = $arSections[$arItem['ID']];
			/*if (!$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']])
				$arResult['COUNTRIES'][$arItem["PROPERTIES"]['COUNTRY']['VALUE']] = [
					'ID'	=>
				];*/
		endforeach;
		
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y");

global ${$arParams["FILTER_NAME"]};
if (!empty(${$arParams["FILTER_NAME"]}['ID'])) {
    $arFilter['ID'] = ${$arParams["FILTER_NAME"]}['ID'];
}

$arrAlphabet = [];
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $albhabets[] = $arFields['NAME'];

    if (!empty($arFields['NAME'])) {
        $sLetter = mb_substr(trim($arFields['NAME']), 0, 1);
        if (!in_array($sLetter, $arrAlphabet)) {
            $arrAlphabet[] = $sLetter;
        }
    }
}

asort($arrAlphabet);
$arResult['LETTERS'] = $arrAlphabet;		