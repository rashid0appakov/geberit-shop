<?
if(empty($_SERVER["HTTP_REFERER"]))
	die();

define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\Config\Option,
	Bitrix\Sale,
	Bitrix\Sale\Order,
	Bitrix\Sale\DiscountCouponsManager;

if(!Loader::IncludeModule("sale"))
	return;

Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;

$request = Application::getInstance()->getContext()->getRequest();

$arPosts = $request->getPostList()->toArray();

foreach($arPosts as $key => $value){

	if(preg_match('/itemQuantity/', $key)){
		$quantity = $value;
	}

	if(preg_match('/itemProductId/', $key)){
		$arProductsIds[$quantity] = $value;
	}
}

$phone = $request->getPost("customerPhone");
$email = $request->getPost("customerEmail");
$buyMode = $request->getPost("mode");

//PROPERTIES//
$phone = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($phone)));
$email = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($email)));

//USER//
if(!$USER->IsAuthorized()) {

	$newLogin = randString(5);
	$newEmail = $email;
	$newPass = randString(10);

	$arFields = Array(
		"LOGIN" => $newLogin,
		"NAME" => $name,
		"EMAIL" => $newEmail,
		"PASSWORD" => $newPass,
		"CONFIRM_PASSWORD" => $newPass,
		"ACTIVE" => "Y",
		"LID" => SITE_ID
	);
	$registeredUserID = $USER->Add($arFields);

} else {
	$registeredUserID = $USER->GetID();
}

if(intval($registeredUserID) == 0){
	echo $USER->LAST_ERROR;
}

//BASKET//
$basketUserID = Sale\Fuser::getId();

DiscountCouponsManager::init();

if($buyMode == 'ONE'){
	$basket = Sale\Basket::loadItemsForFUser($basketUserID, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
	foreach($basket as $basketItem) {
		\CSaleBasket::Delete($basketItem->getId());
	}

	foreach($arProductsIds as $quantity => $id){

		$item = $basket->createItem("catalog", $id);
		$item->setFields(array(
			"QUANTITY" => $quantity,
			"CURRENCY" => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
			"LID" => \Bitrix\Main\Context::getCurrent()->getSite(),
			"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProviderCustom"
		));
	}

	$basket->save();
}
//CREATE_ORDER//
$order = Order::create(Bitrix\Main\Context::getCurrent()->getSite(), $registeredUserID);

//PERSON_TYPE//
$arPersonTypes = Sale\PersonType::load(Bitrix\Main\Context::getCurrent()->getSite());
reset($arPersonTypes);
$arPersonType = current($arPersonTypes);
if(!empty($arPersonType))
	$order->setPersonTypeId($arPersonType["ID"]);

//ORDER_SET_BASKET//
$basket = Sale\Basket::loadItemsForFUser($basketUserID, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
$order->setBasket($basket);

//Получаем товары которые покупают в один клик
$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(
	 array(
			"NAME" => "ASC",
			"ID" => "ASC"
		 ),
	 array(
			"FUSER_ID" => $basketUserID,
			"LID" => Bitrix\Main\Context::getCurrent()->getSite(),
			"ORDER_ID" => "NULL"
		 ),
	 false,
	 false,
	 array()
 );
 $q = 0;
while ($arItems = $dbBasketItems->Fetch())
{
	//Получаем доп. инфу о товарах которые покупают
	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_1", "PROPERTY_MANUFACTURER", "IBLOCK_SECTION_ID", "PROPERTY_ARTNUMBER", "PROPERTY_SERIES");
	$arFilter = Array("IBLOCK_ID"=> ($_POST['IBLOCK_ID'] ? $_POST['IBLOCK_ID'] : CATALOG_IBLOCK_ID), "ACTIVE"=>"Y", "ID"=>$arItems["PRODUCT_ID"]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

	if($arFields = $res->GetNext()){
		$resBrands = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>BRANDS_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$arFields['PROPERTY_MANUFACTURER_VALUE']), false, Array(), Array("ID", "NAME", "IBLOCK_ID"));
		if($obBrands = $resBrands->GetNextElement())
			$arFieldsBrands = $obBrands->GetFields();

		$resSection = CIBlockSection::GetList(Array(), Array('ID' => $arFields['IBLOCK_SECTION_ID']), true);
		$ar_resSection = $resSection->GetNext();

		$brand = $arFieldsBrands['NAME'];
		$category = $ar_resSection['NAME'];
	}

	$arBasketItems[$q]['id']    = $arFields['PROPERTY_ARTNUMBER_VALUE']; //$arItems["PRODUCT_ID"];
	$arBasketItems[$q]['name'] = $arItems["NAME"];
	$arBasketItems[$q]['price'] = $arItems["PRICE"];
	$arBasketItems[$q]['brand'] = $brand;
	$arBasketItems[$q]['category'] = $category;
	$arBasketItems[$q]['quantity'] = $arItems["QUANTITY"];
    $arBasketItems[$q]['variant'] = $GLOBALS['PAGE_DATA']['INFO_SERIES'][$arFields['PROPERTY_SERIES_VALUE']]['NAME'];
    $arBasketItems[$q]['dimension7'] = (int)$arItems["DISCOUNT_PRICE"] ? 'yes' : 'no';

	$q++;
}

//SHIPMENT//
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$shipment->setField("CURRENCY", $order->getCurrency());

$shipmentItemCollection = $shipment->getShipmentItemCollection();

foreach($order->getBasket() as $item) {

	$shipmentItem = $shipmentItemCollection->createItem($item);
	$shipmentItem->setQuantity($item->getQuantity());
}

$arDeliveryServiceAll = Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);
reset($arDeliveryServiceAll);
$deliveryObj = current($arDeliveryServiceAll);
if(!empty($deliveryObj)) {
	$shipment->setFields(array(
		"DELIVERY_ID" => 3,
		"DELIVERY_NAME" => 'Самовывоз со склада'
	));
	$shipment->getCollection()->calculateDelivery();
} else
	$shipment->delete();

//PAYMENT//
$paymentCollection = $order->getPaymentCollection();
$extPayment = $paymentCollection->createItem();
$extPayment->setField("SUM", $order->getPrice());
$arPaySystemServiceAll = Sale\PaySystem\Manager::getListWithRestrictions($extPayment);
reset($arPaySystemServiceAll);
$arPaySystem = current($arPaySystemServiceAll);
if(!empty($arPaySystem)) {
	$extPayment->setFields(array(
		"PAY_SYSTEM_ID" => 16,
		"PAY_SYSTEM_NAME" => 'Купить в рассрочку'
	));
} else
	$extPayment->delete();

$order->doFinalAction(true);

//ORDER_SET_PROPERTIES//
function getPropertyByCode($propertyCollection, $code) {
	foreach($propertyCollection as $property) {
		if($property->getField("CODE") == $code)
			return $property;
	}
}

$propertyCollection = $order->getPropertyCollection();

$fioProperty = getPropertyByCode($propertyCollection, "FIO");
if(!empty($fioProperty))
	$fioProperty->setValue($name);

$phoneProperty = getPropertyByCode($propertyCollection, "PHONE");
if(!empty($phoneProperty))
	$phoneProperty->setValue($phone);

$emailProperty = getPropertyByCode($propertyCollection, "EMAIL");
if(!empty($emailProperty))
	$emailProperty->setValue($email);

//ORDER_SET_FIELDS//
$order->setField("CURRENCY", Option::get("sale", "default_currency"));

$order->setField("USER_DESCRIPTION", 'ПОКУПКА В РАССРОЧКУ');
$order->setField("COMMENTS", 'ПОКУПКА В РАССРОЧКУ');

$order->save();

$orderId = $order->GetId();
$result = array(
    "order_id" => $orderId,
    "media" => array(
        "order_id" => $orderId,
        "order_sum" => $order->getPrice(),
        "products" => $arBasketItems
    )
);
echo Bitrix\Main\Web\Json::encode($result);

/*
//MESSAGE//
if($orderId > 0) {
	$result = array(
		"success" => array(
			"text" => Loc::getMessage("ORDER_CREATE_SUCCESS")
		),
		"media" => array(
			"city" => $_COOKIE["BITRIX_SM_GEOLOCATION_CITY"],
			"order_id" => $orderId,
			"user_fio" => $name,
			"user_email" => $email,
			"user_phone" => $phone,
			"user_comment" => $message,
			"order_sum" => $order->getPrice(),
			"products_box" => $arBasketItemsBox,
			"products" => $arBasketItems
		)
	);
} else {
	$result = array(
		"error" => array(
			"text" => Loc::getMessage("ORDER_CREATE_ERROR"),
			"captcha_code" => !empty($captchaSid) ? $APPLICATION->CaptchaGetCode() : ""
		)
	);
}
*/
?>