<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$str_ids = $_REQUEST["str_ids"];

$arrIds = explode(",", $str_ids);


foreach($arrIds as $itemId){
	Add2BasketByProductID(
		$itemId, 
		1, 
		array(), 
		array()
	);
}


?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>