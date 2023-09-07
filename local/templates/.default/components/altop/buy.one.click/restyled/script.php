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

$paramsString = $request->getPost("PARAMS_STRING");
if(!empty($paramsString))
	$params = unserialize(base64_decode(strtr($paramsString, "-_,", "+/=")));

$name = $request->getPost("NAME");
$phone = $request->getPost("PHONE");
$email = $request->getPost("EMAIL");
$message = $request->getPost("MESSAGE");

$captchaWord = $request->getPost("CAPTCHA_WORD");
$captchaSid = $request->getPost("CAPTCHA_SID");

$id = $request->getPost("ID");
$cons_ids = $request->getPost("CONS_IDS");
$props = $request->getPost("PROPS");
$selectProps = $request->getPost("SELECT_PROPS");
$qnt = $request->getPost("QUANTITY");

$buyMode = $request->getPost("BUY_MODE");

//CHECKS//
foreach($params["REQUIRED"] as $arCode) {
	$post = $request->getPost($arCode);
	if(empty($post))
		$error .= Loc::getMessage($arCode."_NOT_FILLED")."<br />";
}

//CHECKS_PERSONAL_DATA//
$personalData = $request->getPost("PERSONAL_DATA");
if($personalData === "N") {
	$error .= Loc::getMessage("FIELD_NOT_FILLED_PERSONAL_DATA")."<br />";
}

//VALIDATE_PHONE_MASK//
//if(!empty($phone))
	if(!preg_match($params["VALIDATE_PHONE_MASK"], $phone))
		$error .= Loc::getMessage("PHONE_INVALID")."<br />";

if(!empty($captchaSid) && !$APPLICATION->CaptchaCheckCode($captchaWord, $captchaSid))
	$error .= Loc::getMessage("WRONG_CAPTCHA")."<br />";

if(!empty($error)) {
	$result = array(
		"error" => array(
			"text" => $error,
			"captcha_code" => !empty($captchaSid) ? $APPLICATION->CaptchaGetCode() : ""
		)
	);
	echo Bitrix\Main\Web\Json::encode($result);
	return;
}

//PROPERTIES//
$name = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($name)));
$phone = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($phone)));
$email = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($email)));
$message = iconv("UTF-8", SITE_CHARSET, strip_tags(trim($message)));

//USER//
$newUser = false;
if($params["IS_AUTHORIZED"] != "Y") {
    if (CClass::clearPhone($phone) == '+71111111111')
        $email = 'test@test.ru';

    if(in_array("NAME",$params["REQUIRED"])){
        $newLogin = str_replace('+','',CClass::clearPhone($phone));
        $newEmail = $email;
        $newPass = randString(10);
        $newPhone = str_replace('+','',CClass::clearPhone($phone));

        $arFields = Array(
            "LOGIN" => $newLogin,
            "NAME" => $name,
            "EMAIL" => $newEmail,
            "PASSWORD" => $newPass,
            "CONFIRM_PASSWORD" => $newPass,
            "PERSONAL_PHONE" => $newPhone,
            "ACTIVE" => "Y",
            "LID" => SITE_ID
        );
    } else {
        $newLogin = $email;
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
    }
	$rsUser = $USER->GetList( ($by = "ID"), ($sort = "ASC"), ['LOGIN' => $newLogin], ["SELECT" => ["ID"]]);
	if ($arUser = $rsUser->Fetch()) {
		$registeredUserID = $arUser["ID"];
    } else {
		$registeredUserID = $USER->Add($arFields);
        $newUser = true;
    }
} else {
	$registeredUserID = $USER->GetID();
}


//BASKET//
$basketUserID = Sale\Fuser::getId();

DiscountCouponsManager::init();

if ($buyMode == "ONE") {
	$basket = Sale\Basket::loadItemsForFUser($basketUserID, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
	foreach($basket as $basketItem) {
		\CSaleBasket::Delete($basketItem->getId());
	}

	$arSets = CCatalogProductSet::getAllSetsByProduct(
		$id,
		CCatalogProductSet::TYPE_SET
	);

	$arSet = current($arSets);
	if (!empty($arSet['ITEMS']))
		foreach($arSet['ITEMS'] AS &$arItem)
			$arIDs[] = $arItem['ITEM_ID'];

	if (count($arIDs)>0){
		foreach($arIDs AS $pID){
			$item = $basket->createItem("catalog", $pID);
			$item->setFields(array(
				"QUANTITY" => $qnt,
				"CURRENCY" => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
				"LID" => \Bitrix\Main\Context::getCurrent()->getSite(),
				"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProviderCustom"
			));

		}
	}

	//Если покупаем в один клик из карточки товара обычные товар
	elseif($cons_ids == ''){
		$item = $basket->createItem("catalog", $id);
		$item->setFields(array(
			"QUANTITY" => $qnt,
			"CURRENCY" => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
			"LID" => \Bitrix\Main\Context::getCurrent()->getSite(),
			"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProviderCustom"
		));
	}
	//Если покупаем в один клик из карточки товара набор
	else {
		$cons_ids = explode(",", $cons_ids);
		foreach($cons_ids as $id){
			$item = $basket->createItem("catalog", $id);
			$item->setFields(array(
				"QUANTITY" => 1,
				"CURRENCY" => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
				"LID" => \Bitrix\Main\Context::getCurrent()->getSite(),
				"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProviderCustom"
			));
		}
	}
	$basket->save();

	if(!empty($props)) {
		$arProps = unserialize(base64_decode(strtr($props, "-_,", "+/=")));
		foreach($arProps as $arProp) {
			$arBasketProps[] = $arProp;
		}
	}
	if(!empty($selectProps)) {
		$arSelectProps = explode("||", $selectProps);
		foreach($arSelectProps as $arSelProp) {
			$arBasketProps[] = unserialize(base64_decode(strtr($arSelProp, "-_,", "+/=")));
		}
	}
	if(isset($arBasketProps) && !empty($arBasketProps)) {
		$basketPropertyCollection = $item->getPropertyCollection();
		$basketPropertyCollection->setProperty($arBasketProps);
		$basketPropertyCollection->save();
	}
}

$basketItems = $basket->getBasketItems();

foreach ($basketItems as $basketItem) {
	CGifts::setGiftToBasketItem($basketItem->getProductId());
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
	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_1", "PROPERTY_MANUFACTURER", "IBLOCK_SECTION_ID", "PROPERTY_ARTNUMBER");
	$arFilter = Array("IBLOCK_ID"=> $params['IBLOCK_ID'], "ACTIVE"=>"Y", "ID"=>$arItems["PRODUCT_ID"]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

	if($ob = $res->GetNextElement()){
		$arFields = $ob->GetFields();
		$arProps = $ob->GetProperties();

		$resBrands = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>13, "ACTIVE"=>"Y", "ID"=>$arProps['MANUFACTURER']['VALUE']), false, Array(), Array("ID", "NAME", "IBLOCK_ID"));
		if($obBrands = $resBrands->GetNextElement()){
			$arFieldsBrands = $obBrands->GetFields();
		}

		$resSection = CIBlockSection::GetList(Array(), Array('ID' => $arFields['IBLOCK_SECTION_ID']), true);
		$ar_resSection = $resSection->GetNext();

		$artnumber = $arProps['ARTNUMBER']['VALUE'];
		$brand = $arFieldsBrands['NAME'];
		$category = $ar_resSection['NAME'];
	}

	$arBasketItemsBox[$q]['articul'] = $artnumber;
	$arBasketItemsBox[$q]['name'] = $arItems["NAME"];
	$arBasketItemsBox[$q]['price'] = $arItems["PRICE"];
	$arBasketItemsBox[$q]['count'] = $arItems["QUANTITY"];
	$arBasketItemsBox[$q]['currency'] = "RUB";

	$arBasketItems[$q]['id'] = $arItems["PRODUCT_ID"];
	$arBasketItems[$q]['name'] = $arItems["NAME"];
	$arBasketItems[$q]['price'] = $arItems["PRICE"];
	$arBasketItems[$q]['brand'] = $brand;
	$arBasketItems[$q]['category'] = $category;
	$arBasketItems[$q]['quantity'] = $arItems["QUANTITY"];

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
		"DELIVERY_ID" => $deliveryObj->getId(),
		"DELIVERY_NAME" => $deliveryObj->isProfile() ? $deliveryObj->getNameWithParent() : $deliveryObj->getName()
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
		"PAY_SYSTEM_ID" => $arPaySystem["ID"],
		"PAY_SYSTEM_NAME" => $arPaySystem["NAME"]
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
if (!empty($emailProperty))
	$emailProperty->setValue($email);

//ORDER_SET_FIELDS//
$order->setField("CURRENCY", Option::get("sale", "default_currency"));

$order->setField("USER_DESCRIPTION", $message);
$order->setField("COMMENTS", Loc::getMessage("ORDER_COMMENT"));

$order->save();
$orderId = $order->GetId();

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
			"text" => Loc::getMessage("ORDER_CREATE_ERROR").' 2',
			"captcha_code" => !empty($captchaSid) ? $APPLICATION->CaptchaGetCode() : ""
		)
	);
    if($newUser){CUser::Delete($registeredUserID);}
}

$APPLICATION->RestartBuffer();
echo Bitrix\Main\Web\Json::encode($result);?>