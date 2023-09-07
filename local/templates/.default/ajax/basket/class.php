<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Catalog\Product\Basket;

class BasketAjaxHelper
{
	private static function getRequest()
	{
		$request = Application::getInstance()->getContext()->getRequest();

		if (!check_bitrix_sessid() || !$request->isAjaxRequest())
		{
			static::ReturnError("Ошибка запроса");
		}

		if (
			!Loader::includeModule("sale")
			||
			!Loader::includeModule("catalog")
			||
			!Loader::includeModule("iblock")
		)
		{
			static::ReturnError("Ошибка");
		}

		return $request;
	}

	public static function Add2Basket()
	{
		$request = static::getRequest();

		$productId = $request["productId"];
		$quantity = $request["quantity"];

		if (empty($productId)) static::ReturnError("Не указан ID товара");
		if (empty($quantity)) static::ReturnError("Не указано количество");

		$res = Basket::addProduct(array(
			"PRODUCT_ID" => $productId,
			"QUANTITY" => $quantity,
		));
		if (!$res->isSuccess())
		{
			$errors = $res->getErrors();
			$errorMessages = array_map(function($error) { return $error->getMessage(); }, $errors);
			static::ReturnError(implode("; ", $errorMessages));
		}

		$resData = $res->getData();
		$addedBasketItemId = $resData["ID"];

		$basketInfo = static::GetBasketInfo();
		$productInfo = static::GetProductInfo($productId, $basketInfo['ITEMS'][$basketId]['QUANTITY']);

		static::ReturnAnswer(array(
			"PRODUCT_ID" => $productId,
			"QUANTITY" => $quantity,
			"BASKET_ID" => $addedBasketItemId,

			"PRODUCT" => $productInfo,
			"BASKET" => $basketInfo,
		));
	}

	public static function ChangeQuantity()
	{
		$request = static::getRequest();

		$basketId = $request["basketId"];
		$quantity = $request["quantity"];

		if (empty($basketId)) static::ReturnError("Не указан ID корзины");
		if (empty($quantity)) static::ReturnError("Не указано количество");
		if ($quantity <= 0) static::ReturnError("Неверное количество");

		$ob = \CSaleBasket::GetList(
			array(),
			array(
				"ID" => $basketId,
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL",
			),
			false,
			false,
			array(
				"ID",
				"PRODUCT_ID",
			)
		);
		if ($arBasket = $ob->Fetch())
		{
			$productId = $arBasket["PRODUCT_ID"];

			if (!\CSaleBasket::Update($arBasket["ID"], array("QUANTITY" => $quantity)))
			{
				static::ReturnError("Ошибка обновления");
			}
		}
		else
		{
			static::ReturnError("Элемент корзины не найден");
		}

		$basketInfo = static::GetBasketInfo();
		$productInfo = static::GetProductInfo($productId, $basketInfo['ITEMS'][$basketId]['QUANTITY']);

		static::ReturnAnswer(array(
			"PRODUCT_ID" => $productId,
			"QUANTITY" => $quantity,
			"BASKET_ID" => $basketId,

			"PRODUCT" => $productInfo,
			"BASKET" => $basketInfo,
		));
	}

	public static function DeleteFromBasket()
	{
		$request = static::getRequest();

		$basketId = $request["basketId"];

		if (empty($basketId)) static::ReturnError("Не указан ID корзины");

		$ob = \CSaleBasket::GetList(
			array(),
			array(
				"ID" => $basketId,
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL",
			),
			false,
			false,
			array(
				"ID",
				"PRODUCT_ID",
			)
		);
		if ($arBasket = $ob->Fetch())
		{
			$productId = $arBasket["PRODUCT_ID"];

			if (!\CSaleBasket::Delete($arBasket["ID"]))
			{
				static::ReturnError("Ошибка удаления");
			}
		}
		else
		{
			static::ReturnError("Элемент корзины не найден");
		}

		$basketInfo = static::GetBasketInfo();
		$productInfo = static::GetProductInfo($productId, $basketInfo['ITEMS'][$basketId]['QUANTITY']);

		static::ReturnAnswer(array(
			"BASKET_ID" => $basketId,

			"PRODUCT" => $productInfo,
			"BASKET" => $basketInfo,
			'PRODUCT_ID'=>$productId
		));
	}

	public static function checkInstallation()
	{
		$request = static::getRequest();

		$basketId = $request["basketId"];
		$value = $request['value'];

		$addFields['PROPS'][] = array("NAME"=> "Требуется установка", "CODE"=> "NEED_INSTALLATION", "VALUE" => $value, "SORT" => "ASC" );
		if (CSaleBasket::Update($basketId, $addFields)) {
			static::ReturnAnswer([
				'checked' => $value == 1
			]);
		} else {
			static::ReturnError("It cannot be done");
		}
	}


	public static function processCoupon()
	{
		$request = static::getRequest();
		if(isset($request["clear"]))
		{
			\Bitrix\Main\Loader::includeModule('sale');
	        \Bitrix\Sale\DiscountCouponsManager::init();
	        \Bitrix\Sale\DiscountCouponsManager::clear(true);
	        \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
			static::ReturnAnswer([]);

		}
		$applyCoupon = $request['apply'];
		$coupon = $request['coupon'];
		if (CCatalogDiscountCoupon::SetCoupon($coupon)) {
			if (CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID)) {
				static::ReturnAnswer([]);
			}
		}
		static::ReturnError("It cannot be done");
	}

	public static function emptyBasket()
	{
		static::getRequest();

		$res = CSaleBasket::GetList(array(), array(
							  'FUSER_ID' => CSaleBasket::GetBasketUserID(),
							  'LID' => SITE_ID,
							  'ORDER_ID' => 'null',
							  'DELAY' => 'N',
							  'CAN_BUY' => 'Y'));
		while ($row = $res->fetch()) {
		   CSaleBasket::Delete($row['ID']);
		}

		static::ReturnAnswer([]);
	}


	private static function GetProductInfo($productId, $quantity)
	{
		$ob = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $productId,
			),
			false,
			false,
			array(
				"ID",
				"IBLOCK_ID",
				"NAME",
				"PROPERTY_ARTNUMBER",
			)
		);
		if ($arItem = $ob->Fetch())
		{
			$productName = $arItem["NAME"];
			$productArtnum = $arItem["PROPERTY_ARTNUMBER_VALUE"];

			$arPrice = \CCatalogProduct::GetOptimalPrice($productId);
			$basePrice = $arPrice["RESULT_PRICE"]["BASE_PRICE"];
			$discountPrice = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
			$discount = $arPrice["RESULT_PRICE"]["PERCENT"];
			$currency = $arPrice["RESULT_PRICE"]["CURRENCY"];
			// $discountPrice = static::getFinalPriceInCurrency($productId, $currency);
			$basePriceFormatted = customFormatPrice(CurrencyFormat($basePrice, $currency));
			$discountPriceFormatted = customFormatPrice(CurrencyFormat($discountPrice, $currency));
			$fullPrice = $discountPrice * $quantity;
			$fullPrieFormatted = customFormatPrice(CurrencyFormat($fullPrice, $currency));

			return array(
				"ID" => $productId,
				"NAME" => $productName,
				"ARTNUM" => $productArtnum,
				"BASE_PRICE" => $basePrice,
				"DISCOUNT_PRICE" => $discountPrice,
				"DISCOUNT" => $discount,
				"CURRENCY" => $currency,
				"FULL_PRICE" => $fullPrice,
				"BASE_PRICE_FORMATTED" => $basePriceFormatted,
				"DISCOUNT_PRICE_FORMATTED" => $discountPriceFormatted,
				"FULL_PRICE_FORMATTED" => $fullPrieFormatted
			);
		}

		return false;
	}

	private static function GetBasketInfo()
	{
		$basketData = array(
			"PRODUCT_COUNT" => 0,
			"PRICE" => 0.0,
			"ITEMS" => array(),
		);

		$productCount = 0;
		$basketPrice = 0.0;
		$ob = CSaleBasket::GetList(
			array(),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL",
			)
		);
		while ($arItem = $ob->Fetch()){
			if ($arItem['CAN_BUY'] != 'Y')
				continue;
			$itemId = $arItem["ID"];
			$productId = $arItem["PRODUCT_ID"];
			$price = (float) $arItem["PRICE"];
			$quantity = $arItem["QUANTITY"];
			$currency = $arItem["CURRENCY"];

			$basketData["ITEMS"][$itemId] = array(
				"ID" => $itemId,
				"PRODUCT_ID" => $productId,
				"PRICE" => $price,
				"QUANTITY" => $quantity
			);

			$productCount++;
			$basketPrice += $price * $quantity;
		}

		$basketData["PRODUCT_COUNT"] = $productCount;
		$basketData["PRICE"] = $basketPrice;
		$basketData["PRICE_FORMATTED"] = customFormatPrice(CurrencyFormat($basketPrice, $currency));

		return $basketData;
	}




	private static function ReturnAnswer($data)
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo json_encode($data);
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
		die();
	}

	private static function ReturnError($message)
	{
		static::ReturnAnswer(array(
			"error" => $message,
		));
	}

	/***Как посчитать стоимость товара или предложения со всеми скидками***/
	private static function getFinalPriceInCurrency($item_id, $sale_currency = 'RUB') {

		global $USER;

		$currency_code = 'RUB';

		// Проверяем, имеет ли товар торговые предложения?
		if(CCatalogSku::IsExistOffers($item_id)) {

			// Пытаемся найти цену среди торговых предложений
			$res = CIBlockElement::GetByID($item_id);

			if($ar_res = $res->GetNext()) {

				if(isset($ar_res['IBLOCK_ID']) && $ar_res['IBLOCK_ID']) {

					// Ищем все тогровые предложения
					$offers = CIBlockPriceTools::GetOffersArray(array(
						'IBLOCK_ID' => $ar_res['IBLOCK_ID'],
						'HIDE_NOT_AVAILABLE' => 'Y',
						'CHECK_PERMISSIONS' => 'Y'
					), array($item_id), null, null, null, null, null, null, array('CURRENCY_ID' => $sale_currency), $USER->getId(), null);

					foreach($offers as $offer) {

						$price = CCatalogProduct::GetOptimalPrice($offer['ID'], 1, $USER->GetUserGroupArray(), 'N');
						if(isset($price['PRICE'])) {

							$final_price = $price['PRICE']['PRICE'];
							$currency_code = $price['PRICE']['CURRENCY'];

							// Ищем скидки и высчитываем стоимость с учетом найденных
							$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N");
							if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
								$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
							}

							// Конец цикла, используем найденные значения
							break;
						}

					}
				}
			}

		} else {

			// Простой товар, без торговых предложений (для количества равному 1)
			$price = CCatalogProduct::GetOptimalPrice($item_id, 1, $USER->GetUserGroupArray(), 'N');

			// Получили цену?
			if(!$price || !isset($price['PRICE'])) {
				return false;
			}

			// Меняем код валюты, если нашли
			if(isset($price['CURRENCY'])) {
				$currency_code = $price['CURRENCY'];
			}
			if(isset($price['PRICE']['CURRENCY'])) {
				$currency_code = $price['PRICE']['CURRENCY'];
			}

			// Получаем итоговую цену
			$final_price = $price['PRICE']['PRICE'];

			// Ищем скидки и пересчитываем цену товара с их учетом
			$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N", 2);
			if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
				$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
			}

		}

		// Если необходимо, конвертируем в нужную валюту
		if($currency_code != $sale_currency) {
			$final_price = CCurrencyRates::ConvertCurrency($final_price, $currency_code, $sale_currency);
		}

		return $final_price;

	}
}