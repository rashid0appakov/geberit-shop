<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

if($templateData['robots'])
{
	$APPLICATION->SetPageProperty("robots", $templateData['robots']);
}

if(!empty($_SERVER['HTTP_IS_SUB_HEADER']) && SITE_ID == 's0'){
	$APPLICATION->SetPageProperty("canonical", 'https://'.SITE_SERVER_NAME.$arResult['DETAIL_PAGE_URL']);
}