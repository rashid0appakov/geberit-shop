<?
use \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc;

Loader::includeModule('catalog');
Loader::includeModule('sale');

class CCatalogProductProviderCustom extends \CCatalogProductProvider {
	
    public static function GetProductData($arParams){

		global $USER;
	
		//Текущие параметры товара
        $arResult = parent::GetProductData($arParams);
		
		//Достаем цену в зависимости от города
		$arPriceCodeMap = array('BASE' => 1, 'SPB' => 2, 'EKB' => 4);
		$arPriceCode = CClass::getCurrentPriceCode();
		
		$ar_res = CCatalogProduct::GetByIDEx($arParams['PRODUCT_ID']);

		$price = $ar_res['PRICES'][$arPriceCodeMap[$arPriceCode[0]]]['PRICE'];
		// var_dump($_REQUEST["ID"]);
		// var_dump($ar_res);
//		var_dump($price);
//		var_dump($arPriceCodeMap[$arPriceCode[0]]);
//		die;

		$arFilter = array(
			"PRODUCT_ID" => $arResult['PRODUCT_XML_ID'],
			"CATALOG_GROUP_ID" => $arPriceCodeMap[$arPriceCode[0]],
		);	

		// var_dump($arFilter);
		// die;
		$db_res = CPrice::GetListEx(array(), $arFilter);
		if ($ar_res = $db_res->Fetch()){

			if ($price <= 0) 
				$price = $ar_res["PRICE"];

			//Заменяем текущие значения на нужные
			$arPrice[] = array(
				"ID" => $ar_res["ID"],
				//"PRICE" => $ar_res["PRICE"],
				"PRICE" => $price,
				"CURRENCY" => $ar_res["CURRENCY"],
				"CATALOG_GROUP_ID" => $ar_res["CATALOG_GROUP_ID"]
			);

			//var_dump($arPrice);
			$arNewRes = CCatalogProduct::GetOptimalPrice($arResult['PRODUCT_XML_ID'], 1, $USER->GetUserGroupArray(), 'N', $arPrice);
			
			$arResult['PRICE_TYPE_ID'] = $arNewRes['RESULT_PRICE']['PRICE_TYPE_ID'];
			$arResult['BASE_PRICE'] = $arNewRes['RESULT_PRICE']['BASE_PRICE'];
			$arResult['PRICE'] = $arNewRes['RESULT_PRICE']['DISCOUNT_PRICE'];

		}

		// var_dump($arResult);
		// die;
		// global $USER;
		// if ($USER->IsAdmin()){
		// 	var_dump($arResult);	
		// 	die;
		// }

		//Возвращаем готовый массив
        return $arResult;
    }
}
?>