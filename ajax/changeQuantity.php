<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
	return;


$arFields = array(
   "QUANTITY" => $_GET['quantity'],
);
CSaleBasket::Update($_GET['id'], $arFields);



?>