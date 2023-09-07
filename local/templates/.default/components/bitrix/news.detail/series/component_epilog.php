<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
	throw new BitrixNotFoundException();

$bIsBrandSite = false;
if (isset($arParams["IS_BRAND_SITE"]) && "Y" == $arParams["IS_BRAND_SITE"])
	$bIsBrandSite = true;

if (LANGUAGE_ID != 'ru'){
	$APPLICATION->SetDirProperty('s_title', $arResult["TITLE"]);
	$APPLICATION->SetDirProperty('s_keywords', $arResult["KEYWORDS"]);
	$APPLICATION->SetDirProperty('s_description', $arResult["DESCRIPTION"]);
}

if (!$bIsBrandSite)
	$APPLICATION->AddChainItem('Бренды', ($arResult["BRAND"]['LIST_PAGE_URL'] ? $arResult["BRAND"]['LIST_PAGE_URL'] : '/vendors/'));

if (!empty($arResult["BRAND"]) && !$bIsBrandSite)
	$APPLICATION->AddChainItem($arResult["BRAND"]['NAME'], $arResult["BRAND"]['DETAIL_PAGE_URL']);

$APPLICATION->AddChainItem($arResult["NAME"], "/");

if(!empty($_SERVER['HTTP_IS_SUB_HEADER']) && SITE_ID == 's0'){
	$APPLICATION->SetPageProperty("canonical", 'https://'.SITE_SERVER_NAME.$arResult['DETAIL_PAGE_URL']);
}