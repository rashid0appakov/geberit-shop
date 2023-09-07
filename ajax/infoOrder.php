<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$arProducts = array();
$arGood_ids = array();
$numberOrder = $_REQUEST['id'];

$rsSales = CSaleOrder::GetList(array(), array("ACCOUNT_NUMBER" => $numberOrder));
if($arSales = $rsSales->Fetch()){
	$orderId = $arSales["ID"];
	$orderPrice = $arSales["PRICE"];
}

//Товары в заказе
$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $orderId), false, false, array());
$i = 0;
while ($arItems = $dbBasketItems->Fetch()) {

	$productId = $arItems['PRODUCT_ID'];

	$resProductInfo = CIBlockElement::GetByID($productId);
	if($arProductRes = $resProductInfo->GetNext()){
		
		$arProducts[$i]['NAME'] = $arProductRes['NAME'];
		$arProducts[$i]['ID'] = $arProductRes['ID'];
		$arProducts[$i]['PRICE'] = $arItems['PRICE'];
		$arProducts[$i]['QUANTITY'] = $arItems['QUANTITY'];
		
		
		$arGood_ids[$i] = $productId;
		$i++;
	}
}


$arData = array();
$arData['good_ids'] = $arGood_ids;
$arData['products'] = $arProducts;
$arData['sum'] = $orderPrice;

echo json_encode($arData);
?>