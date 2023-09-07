<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	function getFinalPriceInCurrency($arItem, $cnt = 1, $sale_currency = 'RUB') {
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("sale");
		global $USER;

		// Проверяем, имеет ли товар торговые предложения?
		if (CCatalogSku::IsExistOffers($arItem['ID'])) {
			// Ищем все тогровые предложения
			$arrOffers = CIBlockPriceTools::GetOffersArray(array(
				'IBLOCK_ID'		 => $arItem['IBLOCK_ID'],
				'HIDE_NOT_AVAILABLE'=> 'Y',
				'CHECK_PERMISSIONS' => 'Y'
			), array($arItem['ID']), null, null, null, null, null, null, array('CURRENCY_ID' => $sale_currency), $USER->getId(), null);

			if (!empty($arrOffers))
				foreach($arrOffers AS $arOffer) {
					$price = CCatalogProduct::GetOptimalPrice($arOffer['ID'], $cnt, $USER->GetUserGroupArray(), 'N');
					if (isset($price['PRICE'])) {
						$final_price	= $price['PRICE']['PRICE'];
						$currency_code  = $price['PRICE']['CURRENCY'];

						// Ищем скидки и высчитываем стоимость с учетом найденных
						$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arItem['ID'], $USER->GetUserGroupArray(), "N");
						if (is_array($arDiscounts) && sizeof($arDiscounts) > 0)
							$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);

						// Конец цикла, используем найденные значения
						break;
					}
				}
		} else {
			// Простой товар, без торговых предложений (для количества равному $cnt)
			$price = CCatalogProduct::GetOptimalPrice($arItem['ID'], $cnt, $USER->GetUserGroupArray(), 'N');

			// Получили цену?
			if (!$price || !isset($price['PRICE']))
				return false;

			// Меняем код валюты, если нашли
			if (isset($price['CURRENCY']))
				$currency_code = $price['CURRENCY'];

			if (isset($price['PRICE']['CURRENCY']))
				$currency_code = $price['PRICE']['CURRENCY'];

			// Получаем итоговую цену
			$final_price = $price['PRICE']['PRICE'];

			// Ищем скидки и пересчитываем цену товара с их учетом
			$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arItem['ID'], $USER->GetUserGroupArray(), "N", 2);
			if (is_array($arDiscounts) && sizeof($arDiscounts) > 0)
				$final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
		}

		// Если необходимо, конвертируем в нужную валюту
		if ($currency_code != $sale_currency)
			$final_price = CCurrencyRates::ConvertCurrency($final_price, $currency_code, $sale_currency);

		return [
			"PRICE"	 => customFormatPrice(CurrencyFormat($price['PRICE']['PRICE'], $currency_code)),
			"FINAL_PRICE"=>customFormatPrice(CurrencyFormat($final_price, $currency_code)),
			"CURRENCY"  => $sale_currency,
			"DISCOUNT"  => $arDiscounts,
		];
	}


// -- Get product parent section -------------------------------------------- //
	function getParent($sectionID, $arSections){
		if (!$sectionID || empty($arSections) || !isset($arSections[$sectionID]))
			return FALSE;

		if ($arSections[$sectionID]['DEPTH_LEVEL'] == 1)
			return $arSections[$sectionID]['ID'];
		else
			return getParent($arSections[$sectionID]['IBLOCK_SECTION_ID'], $arSections);
	}

	CModule::IncludeModule("iblock");

	$arSections = CClass::Instance()->getCatalogSection();

	$arResult = array(
		"INITIAL" => $arResult,
		"ITEMS" => null,
		"PROMO" => null,
	);

	// restore tree structure
	$arItems = array();
	$tmp = array(0 => &$arItems);
	foreach ($arResult["INITIAL"] AS &$arItem){
		$arItem["ITEMS"] = [];
		$dl = (int) $arItem["PARAMS"]["DEPTH_LEVEL"];
		$tmp[$dl] =& $tmp[$dl - 1][array_push($tmp[$dl - 1], $arItem) - 1]["ITEMS"];
	}
	unset($tmp);
	if (!empty($arItems))
		$arResult["ITEMS"] = $arItems;


	$obCache = new CPHPCache();
	$cacheID = 'PROMO_MENU_'.CATALOG_IBLOCK_ID;
	$cachePath = '/'.$cacheID;

	if ($obCache->InitCache(CClass::CACHE_TIME, $cacheID, $cachePath) ){
	   $vars = $obCache->GetVars();
	   $arResult["PROMO"] = $vars['arAllItemsIDs'];
	}elseif( $obCache->StartDataCache()){
		$sectionIds = [];
		if (!empty($arResult["ITEMS"]))
			foreach($arResult["ITEMS"] AS $arItem){
				$sectionId = $arItem["PARAMS"]["ID"];
				if (!!$sectionId && !in_array($sectionId, $sectionIds))
					$sectionIds[] = $sectionId;
			}

		$arResult["PROMO"] = [];

		$arSelect   = [
			"ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_PICTURE",
			"PROPERTY_SECTION", "PROPERTY_URL", "PROPERTY_NEW_TAB", "PROPERTY_PRODUCT",
			"DETAIL_PAGE_URL"
		];
		$arFilter   = [
			"IBLOCK_ID"	 => CATALOG_IBLOCK_ID,
			"IBLOCK_TYPE"   => CATALOG_IBLOCK_TYPE,
			//"ID" => $IDS,
			"ACTIVE" => "Y",
			"!PROPERTY_SALELEADER" => FALSE
		];
		$arOrder =  ["SORT" => "RAND", "ID" => "ASC"];
		$ob = CIBlockElement::GetList($arOrder, $arFilter, FALSE, ['nTopCount' => 1000], $arSelect);
		while ($arItem = $ob->GetNext()){
			if (count($arResult["PROMO"][getParent($arItem['IBLOCK_SECTION_ID'], $arSections)]) > 5)
				continue;
			$arItem['PRICE'] = getFinalPriceInCurrency($arItem);

			$arResult["PROMO"][getParent($arItem['IBLOCK_SECTION_ID'], $arSections)][] = $arItem;
		}

		$obCache->EndDataCache(array('arAllItemsIDs' => $arResult["PROMO"]));
	}