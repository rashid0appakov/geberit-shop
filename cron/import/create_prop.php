<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');

$IBLOCK_ID = 15;
$arParams = array("replace_space"=>"_","replace_other"=>"_", 'change_case' => 'U');

$dataHandler = fopen(__DIR__."/data/shop_product_properties_i18n.csv", "r");
while (($data = fgetcsv($dataHandler, 0, ",")) !== FALSE) {
	$name = $data[1];

	$arFields = Array(
		"NAME" => $name,
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => Cutil::translit($name,"ru",$arParams),
		"PROPERTY_TYPE" => "L",
		"IBLOCK_ID" => $IBLOCK_ID
	);
	$ibp = new CIBlockProperty;
	$ibp->Add($arFields);
}
fclose($dataHandler);
?>