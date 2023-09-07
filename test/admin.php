<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?
//set_time_limit(0);
///test/admin.php
CModule::IncludeModule("iblock");

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "CATALOG_QUANTITY");
$arFilter = Array("IBLOCK_ID"=>84, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>5000), $arSelect);
while($ob = $res->GetNext())
{
	$IS_AVAILABLE = $ob["CATALOG_QUANTITY"] > 0? 1: 0;
	$ELEMENT_ID = $ob["ID"];
	CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, array("IS_AVAILABLE" => $IS_AVAILABLE));
	
}
?>
Готово
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>