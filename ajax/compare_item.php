<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:catalog.compare.list", ".default", Array(
	"AJAX_MODE" => "N",
	"IBLOCK_TYPE" => CATALOG_IBLOCK_ID,
	"IBLOCK_ID" => CATALOG_IBLOCK_ID,
	"DETAIL_URL" => "",
	"COMPARE_URL" => SITE_DIR."catalog/compare/",
	"NAME" => "CATALOG_COMPARE_LIST",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<?php
    $json["id"] = (int)$_REQUEST['id'];
    if (!(int)$_REQUEST['id'] || !isset($_REQUEST['id']))
        $json["status"] = 'error';
?>