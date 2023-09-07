<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

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

$data = array(
	"error" => true,
	"message" => "Ошибка запроса",
);

require_once(__DIR__.'/_antispam.php');

$arPosts = $request->getPostList()->toArray();

foreach($arPosts as $key => $value){
	
	if(preg_match('/itemQuantity/', $key)){
		$arPreQuant[]['quantity'] = $value;
	}
	
	if(preg_match('/itemProductId/', $key)){
		$arPreIds[]['id'] = $value;
	}
	
	
}

foreach($arPreIds as $key => $value){
	$arProductsIds[$key]['quantity'] = $arPreQuant[$key]['quantity'];
	$arProductsIds[$key]['id'] = $arPreIds[$key]['id'];
}

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
	$mail = $request->getPost("mail");
	$phone = $request->getPost("phone");
	$params = $request->getPost("params");
	
	if (strlen($mail) <= 0){
		$data["message"] = "Заполните почту";
	}
	elseif(strlen($phone) <= 0){
		$data["message"] = "Заполните телефон";
	}
	elseif (Loader::includeModule("iblock")){
		$params = unserialize(base64_decode($params));

		if (!!$params)
		{
			$arFields = array(
				"IBLOCK_ID" => 99,
				"IBLOCK_SECTION_ID" => false,
				"NAME" => "Заявка на рассрочку",
				"PROPERTY_VALUES" => array(
					"MAIL" => $mail,
					"PHONE" => $phone,
				),
			);
			
			$el = new CIBlockElement;
			$elemenetId = $el->Add($arFields);

			if ($elemenetId)
			{
				//Создаем заказ
				//Пользователь
				if (CClass::clearPhone($phone) == '+71111111111'){
					$email = 'test@test.ru';
				}

				$newLogin = $mail;
				$newEmail = $mail;
				$newName = $mail;
				$newPass = randString(10);

				$arFields = Array(
					"LOGIN" => $newLogin,
					"NAME" => $newName,
					"EMAIL" => $newEmail,
					"PASSWORD" => $newPass,
					"CONFIRM_PASSWORD" => $newPass,
					"ACTIVE" => "Y",
					"LID" => SITE_ID
				);
				$rsUser = $USER->GetList( ($by = "ID"), ($sort = "ASC"), ['LOGIN' => $newLogin], ["SELECT" => ["ID"]]);
				if ($arUser = $rsUser->Fetch()){
					$registeredUserID = $arUser["ID"];
				}
				else{
					$registeredUserID = $USER->Add($arFields);
				}
				
				//Корзина
				$basketUserID = Sale\Fuser::getId();

				DiscountCouponsManager::init();
				
				//Получаем товары из корзины
				$basket = Sale\Basket::loadItemsForFUser($basketUserID, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
				
				//Удаляем все товары из корзины
				foreach($basket as $basketItem) {
					\CSaleBasket::Delete($basketItem->getId());
				}
				
				//Добавляем нужный товар в корзину
				foreach($arProductsIds as $key => $arItem){

					$item = $basket->createItem("catalog", $arItem['id']);
					$item->setFields(array(
						"QUANTITY" => $arItem['quantity'],
						"CURRENCY" => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
						"LID" => \Bitrix\Main\Context::getCurrent()->getSite(),
						"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProviderCustom"
					));
				}
				
				//Заказ
				//Создаем заказ
				$order = Order::create(Bitrix\Main\Context::getCurrent()->getSite(), $registeredUserID);
				
				//Привязываем пользователя к заказу
				$arPersonTypes = Sale\PersonType::load(Bitrix\Main\Context::getCurrent()->getSite());
				reset($arPersonTypes);
				$arPersonType = current($arPersonTypes);
				if(!empty($arPersonType)){
					$order->setPersonTypeId($arPersonType["ID"]);
				}
				
				//Передаем корзину в заказ
				$basket = Sale\Basket::loadItemsForFUser($basketUserID, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
				$order->setBasket($basket);
				
				//Доставка
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
						"DELIVERY_NAME" => "Самовывоз со склада"
					));
					$shipment->getCollection()->calculateDelivery();
				} 
				else{
					$shipment->delete();
				}
				
				//Оплата
				$paymentCollection = $order->getPaymentCollection();
				$extPayment = $paymentCollection->createItem();
				$extPayment->setField("SUM", $order->getPrice());
				$arPaySystemServiceAll = Sale\PaySystem\Manager::getListWithRestrictions($extPayment);
				reset($arPaySystemServiceAll);
				$arPaySystem = current($arPaySystemServiceAll);
				if(!empty($arPaySystem)) {
					$extPayment->setFields(array(
						"PAY_SYSTEM_ID" => 16,
						"PAY_SYSTEM_NAME" => "Купить в рассрочку"
					));
				} else
					$extPayment->delete();

				$order->doFinalAction(true);
				
				//Свойства заказа
				function getPropertyByCode($propertyCollection, $code) {
					foreach($propertyCollection as $property) {
						if($property->getField("CODE") == $code)
							return $property;
					}
				}

				$propertyCollection = $order->getPropertyCollection();

				$fioProperty = getPropertyByCode($propertyCollection, "FIO");
				if(!empty($fioProperty))
					$fioProperty->setValue($newName);

				$phoneProperty = getPropertyByCode($propertyCollection, "PHONE");
				if(!empty($phoneProperty))
					$phoneProperty->setValue($phone);

				$emailProperty = getPropertyByCode($propertyCollection, "EMAIL");
				if(!empty($emailProperty))
					$emailProperty->setValue($mail);
				
				$order->setField("CURRENCY", Option::get("sale", "default_currency"));

				$order->setField("USER_DESCRIPTION", 'ПОКУПКА В РАССРОЧКУ');
				$order->setField("COMMENTS", 'ПОКУПКА В РАССРОЧКУ');

				$order->save();
				
				$data["error"] = false;
				unset($data["message"]);
			}
			else
			{
				$data["message"] = $el->LAST_ERROR;
			}
		}		
	}
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo json_encode($data);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();