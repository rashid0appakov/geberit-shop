<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule('sale');
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$arProducts = array();
$orderId = $_POST['id'];
$type = $_POST['type'];
$newTotalPrice = 0;

//Информания о заказе
$arOrder = CSaleOrder::GetByID($orderId);
$arPaySys = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID']);
$arDeliv = CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);
$dbOrderProps = CSaleOrderPropsValue::GetList(
	array("SORT" => "ASC"),
	array("ORDER_ID" => $orderId, "CODE"=>array("PHONE", "ADDRESS"))
);
while ($arOrderProps = $dbOrderProps->GetNext()):
	if($arOrderProps['CODE'] == 'PHONE'){
		$phone = $arOrderProps['VALUE'];
	}
	if($arOrderProps['CODE'] == 'ADDRESS'){
		$adddres = $arOrderProps['VALUE'];
	}
endwhile;

if($arDeliv['NAME'] == 'Курьером'){
	$delId = 4;
}
elseif($arDeliv['NAME'] == 'Самовывоз'){
	$delId = 12;
}
elseif($arDeliv['NAME'] == 'До транспортной компании'){
	$delId = 13;
}

if($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 129){
	$strCity = 'М';
}
elseif($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 817){
	$strCity = 'П';
}
else{
	$strCity = 'Н/Д';
}


//Товары в заказе
$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $orderId), false, false, array());
$i = 0;
while ($arItems = $dbBasketItems->Fetch()) {

	$productId = $arItems['PRODUCT_ID'];

	$resProductInfo = CIBlockElement::GetList(Array(), Array("ID" => $productId), false, Array("nPageSize"=>1), Array("ID", "IBLOCK_ID", "PROPERTY_ARTNUMBER"));
	$arProductInfo = $resProductInfo->Fetch();

	$arProducts[$i]['articul'] = $arProductInfo['PROPERTY_ARTNUMBER_VALUE'];
	$arProducts[$i]['name'] = $arItems['NAME'];
	$arProducts[$i]['price'] = $arItems['PRICE'];
	$arProducts[$i]['count'] = $arItems['QUANTITY'];
	$arProducts[$i]['currency'] = 'RUB';

	$newTotalPrice += $arItems['PRICE'] * $arItems['QUANTITY'];

	$i++;
}


$arData = array();
$arData['login'] = 'restapitiptopshop';
$arData['password'] = 'fff7949d40fb239751e18bcfb85c6517';
$arData['statusid'] = 1078;
$arData['clientnamefirst'] = $arOrder['USER_NAME'];
$arData['clientemail'] = $arOrder['USER_EMAIL'];
$arData['clientphone'] = $phone;
$arData['name'] = $orderId.'-TTS';
$arData['clientaddress'] = $adddres;
$arData['comments'] = $arOrder['USER_DESCRIPTION'];
$arData['custom_kommentariyposetitelya17'] = $arOrder['USER_DESCRIPTION'];
$arData['custom_idzakazanasayte21'] = $orderId;
$arData['custom_citi'] = $strCity;
$arData['deliveryid'] = $delId;
$arData['deliverynote'] = $arDeliv['NAME'];
$arData['paymentname'] = $arPaySys['NAME'];
$arData['productArray'] = $arProducts;
$arData['sum'] = $newTotalPrice;

echo json_encode($arData);

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>