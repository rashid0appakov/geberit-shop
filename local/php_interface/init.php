<?php
global $man_show, $locId;

$man_show = array(420764, 442147, 442617, 443339, 443767);

define("IS_NEW_YEAR", false); // Вывод новогоднего баннера
define("IS_SALE", false);
define("CREDIT_ENABLE", true); // Рассрочка
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/log.txt");
if(!isset($_GET['dev']))
{
//	define("T731", true); // для включения ф-ла - удалить
}

use GeoIp2\Database\Reader; 


if($_SERVER["SERVER_NAME"]=='novosibirsk.geberit-shop.ru'){
	define("DEFAULT_GEOLOCATION_ID", 2622); // 
}elseif($_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru'){
	define("DEFAULT_GEOLOCATION_ID", 1095); // 
}elseif($_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru'){
	define("DEFAULT_GEOLOCATION_ID", 2201); // 
}elseif($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'){
	define("DEFAULT_GEOLOCATION_ID", 817); // 
}else{
	define("DEFAULT_GEOLOCATION_ID", 129); // Москва
}

$locationId = $APPLICATION->get_cookie("GEOLOCATION_ID");
if(!$locationId)
{
	$user_ip = current(explode(':', $_SERVER['REMOTE_ADDR'], 2));
	if($user_ip /*and filter_var($user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)*/)
	{
		$reader = new Reader($_SERVER["DOCUMENT_ROOT"]."/upload/db/GeoLite2-City.mmdb");
		$record = $reader->city($user_ip);
		$reader->close();
		//pr($record->raw, 1);
		$city = $record->raw['city']['names']['ru'];
		$locationId = DEFAULT_GEOLOCATION_ID;
		$APPLICATION->set_cookie("GEOLOCATION_ID", $locationId);
		$locId =  $locationId;
		/*if($city)
		{
			CModule::IncludeModule("sale");
			$oLocationTable = new \Bitrix\Sale\Location\LocationTable();
			$res = $oLocationTable->getList(array(
				'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
				'filter' => array(
					'=NAME.LANGUAGE_ID' => 'ru',
					'%=NAME.NAME' => $city . '%',
					//'TYPE.CODE' => $arLocationTypeCode
					//'!CITY_ID' => false
				),
				'order' => array('NAME.NAME' => 'ASC'),
				'limit' => 1
			));
			if($arLocation = $res->fetch())
			{
				$locationId = $arLocation['ID'];
				$APPLICATION->set_cookie("GEOLOCATION_ID", $locationId);
				$locId =  $locationId;
				$_SESSION['GEOLOCATION_ID'] =  $locationId;
			}
		}*/
	}
}



function validate_collection($collection_id, $section_id=0, $region_id = 0) {	
	global $man_show;
	$series_ids = array();
	$arSelect = Array("ID", "NAME", "CODE");
	$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_SERIES' => $collection_id, "!PROPERTY_DISCONTINUED" => "Y");

	if ($section_id>0){
		$arFilter['SECTION_ID'] = $section_id;
		$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
	}

	$res = CIBlockElement::GetList(Array(), $arFilter, false, array('nTopCount'=>1), $arSelect);
	if($ob = $res->GetNextElement())
	{
	    $arFields = $ob->GetFields();
	    // var_dump($arFields['NAME']);
	    // var_dump($arFields['CODE']);
	    return true;
	}

	return false;

}


spl_autoload_register('func888');
function func888($class)
{
    if(strpos($class, 'Bitrix\\Iblock')===false) {
        include_once(str_replace(['/','\\'], DIRECTORY_SEPARATOR, $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/$class.php"));
    }
}
function reduce($search, $cnt, $limit) {	
	$a = 1;
	for ($i = 0; $i <= $cnt; $i++) {
		if (!empty($search) && $i != 0 && $i % $limit == 0 && $search >= $i) {
			$a++;
		}
	}
	return $a;
}
function GetSortField(){
	global $APPLICATION;
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$STORE_SORT = "PROPERTY_IS_AVAILABLE_4";
	if ($geo_id == 817  || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){ // питер
	    $STORE_SORT = "PROPERTY_IS_AVAILABLE_1";
	}
	/*if ($geo_id == 2201 || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){ // Екатеринбург
	    $STORE_SORT = "PROPERTY_IS_AVAILABLE_2";
	}
	if ($geo_id == 1095){ // Краснодар
	    $STORE_SORT = "PROPERTY_IS_AVAILABLE_7";
	}*/
	return $STORE_SORT;
}
function GetGeoText(){
	global $APPLICATION;
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$geo = "в Москве";
	if ($geo_id == 817 || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){$geo = "в Санкт-Петербурге";}
	if ($geo_id == 2201 || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ) {$geo = "в Екатеринбурге";}
	if ($geo_id == 2622 || strpos($_SERVER["SERVER_NAME"], 'novosibirsk.')!==false ) {$geo = "в Новосибирске";}
	if ($geo_id == 1095 || strpos($_SERVER["SERVER_NAME"], 'krasnodar.')!==false ) {$geo = "в Краснодаре";}
	
	//if ($geo_id == 1095){$geo = "в Краснодаре";}
	return $geo;
}
function PriceId(){
	global $APPLICATION;
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$STORE_ID = 1;
	if ($geo_id == 817 || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){ // питер
		$STORE_ID = 2;
	}
	/*if ($geo_id == 2201 || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){ // екатеринбург
		$STORE_ID = 1;
	}*/
	return $STORE_ID;
}
function GetStoreId(){
	global $APPLICATION;
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$STORE_ID = 4;
	if ($geo_id == 817 || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){ // питер
		$STORE_ID = 1;
	}
	/*if ($geo_id == 2201 || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){ // екатеринбург
		$STORE_ID = 4;
	}*/
	return $STORE_ID;
}

function GetDeliveryDataForElementByArr($arResult){
	global $APPLICATION;

	//var_dump($arResult['PROPERTIES']['DISCONTINUED']['VALUE']);
	if ($arResult['PROPERTIES']['DISCONTINUED']['VALUE'] == 'Y'){
		$res = array(
						'delivery_title' => '',
						'delivery_days_title' => '',
						//'q' => $arResult["PRODUCT"]['QUANTITY'],
	//					'peremeshen' => $peremeshen
					);
		return $res;
	}

	$STORE_ID = GetStoreId();


	$delivery_days = 45;
	//$delivery_title = 'В наличии на центральном складе';
	$delivery_title = 'Нет в наличии';

	$delivery_days_title = ''; // 35-60 дней
	$q = 10000000;

	$db_props = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], array("sort" => "asc"), Array("CODE"=>"DeliveryDays"));
	$v_d = array();
	while($ar_props = $db_props->Fetch()){
		$v_d[$ar_props['VALUE']] = $ar_props['DESCRIPTION'];
	}


	$find = false;
	foreach ($v_d as $delivery_store_id => $des){
		if (intval($delivery_store_id) == $STORE_ID){
			$delivery_days = $des;
			$find = true;
		}
	}

	// перемещение по питеру выключено

	if (!$find){
		foreach ($v_d as $delivery_store_id => $des){
			// Включить инструкцию если будет больше одного клада оснонвного для перемещения
//			if ($delivery_store_id != 1){
				if (strlen($des)>0){
					$delivery_days = $des;
				}
//			}
			//$delivery_title = 'В наличие';
		}
	}

if (($delivery_days<=0) || $delivery_days==45){
		$db_props = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], array("sort" => "asc"), Array("CODE"=>'IS_AVAILABLE_'.GetStoreId()));
		$delivery_days = 0;
		while($ar_props = $db_props->Fetch()){
			//var_dump($ar_props);
			$delivery_days = $ar_props['VALUE'];
			//$v_d[$ar_props['VALUE']] = $ar_props['DESCRIPTION'];
		}
	}
	// global $USER;
	// if ($USER->IsAdmin()){
	// 	var_dump($v_d);
	// 	var_dump($delivery_days);
	// 	var_dump($arResult['PRODUCT']['QUANTITY']);
	// 	//var_dump($find);
	// }

	$find_arr = array();
	global $USER;
	if (false){ // true // $USER->IsAdmin()
		$SET_ITEMS = $arResult['SET_ITEMS'];
		if (count($SET_ITEMS)<=0){
			$SET_ITEMS = CCatalogProductSet::getAllSetsByProduct(intval($arResult['ID']), CCatalogProductSet::TYPE_GROUP);
			$SET_ITEMS = current($SET_ITEMS);
			$SET_ITEMS = $SET_ITEMS['ITEMS'];
		}
		// echo "<pre>";
		// var_dump($SET_ITEMS);
		// echo "</pre>";
		// die;

		//var_dump($arResult['SET_ITEMS']);

		if (count($SET_ITEMS)>0){
//			var_dump($SET_ITEMS);
			// die;

			$delivery_day_arr = array($delivery_days);

			foreach ($SET_ITEMS as $item){

				$arResult = GetIBlockElement($item['ITEM_ID']);
				$arResult2 = CCatalogProduct::GetByIDEx($item['ITEM_ID']);
				$arResult['PRODUCT'] = $arResult2['PRODUCT'];
				//var_dump($arResult['PRODUCT']['QUANTITY']);
				$delivery_days = 45;
				//$arResult["PRODUCT"]['QUANTITY'] = 1;

				//var_dump($item);
				$delivery_day_arr[] = $arResult['PROPERTIES']['IS_AVAILABLE_'.GetStoreId()]['VALUE'];
				// var_dump($arResult['PROPERTIES']['IS_AVAILABLE_'.GetStoreId()]['VALUE']);

				// $find = false;
				// foreach ($arResult['PROPERTIES']['DeliveryDays']['VALUE'] as $k => $delivery_store_id){
				// 	if (intval($delivery_store_id) == $STORE_ID){
				// 		$des = $arResult['PROPERTIES']['DeliveryDays']['DESCRIPTION'][$k];
				// 		if (strlen($des) > 0){
				// 			$delivery_days = $des;
				// 			$find = true;

				// 		}
				// 	}
				// }

				// if (!$find){
				// 	foreach ($arResult['PROPERTIES']['DeliveryDays']['VALUE'] as $k => $delivery_store_id){
				// 		$des = $arResult['PROPERTIES']['DeliveryDays']['DESCRIPTION'][$k];
				// 		if (strlen($des) > 0){
				// 			$delivery_days = $des;
				// 		}

				// 		//var_dump($delivery_days);
				// 		//$delivery_title = 'В наличие';
				// 	}

					// if ($arResult["PRODUCT"]['QUANTITY']>2){
					// 	$delivery_days = 1;
					// }

					// if ($arResult["PRODUCT"]['QUANTITY']==2){
					// 	$delivery_days = 2;
					// }
				//}

				//$delivery_day_arr[] = $delivery_days;
				//$find_arr[] = $find;
			}

			//var_dump($delivery_day_arr);


			
			// $find = true;
			
			// foreach ($find_arr as $find2){
			// 	if (!$find2){
			// 		$find = $find2;
			// 	}
			// }

			// если хотя бы одно не в наличии - то все не в наличии
			if (in_array(45, $delivery_day_arr)){
				$find = true;
			}

			$delivery_days = max($delivery_day_arr);

			//var_dump($delivery_days);

			// echo "<pre>";
			// 	var_dump($delivery_day_arr);
			// 	var_dump($delivery_days);
			// 	var_dump($find_arr);
			// echo "</pre>";
		}
	}

//	global $USER;
	// if ($USER->IsAdmin()){
	// 	var_dump($v_d);
	// 	var_dump($delivery_day_arr);
	// 	var_dump($delivery_days);
	// }


	// var_dump($delivery_days);
	// var_dump($delivery_title);
	// var_dump($delivery_days_title);

	//if (!$peremeshen){
	if (($delivery_days == 1) || ($delivery_days == 2)){
		$delivery_title = 'В наличии';
		// if ($arResult["PRODUCT"]['QUANTITY'] == 2){
		// 	$delivery_title = 'В наличии мало';
		// }
		$delivery_days_title = '1-3 дня';
	}

	if ($delivery_days == 30){
		$delivery_title = 'В наличии мало';
		$delivery_days_title = '1-3 дня';
		//$delivery_days_title = '35-60 дней';
	}

	if ($delivery_days == 45){
		//$delivery_title = 'В наличии на центральном складе';
		//$delivery_title = 'Нет в наличии'; // Наличие уточняйте
		$delivery_title = 'Наличие уточняйте'; // 
		$delivery_days_title = '35-60 дней';
	}

	if ($delivery_days == 360){
		//$delivery_title = 'Нет в наличии'; // Наличие уточняйте
		$delivery_title = 'Наличие уточняйте'; // Наличие уточняйте
		
		$arResult["PRODUCT"]['QUANTITY'] = 0;
		$delivery_days_title = '35-60 дней';
	}


	if (!$find){
		if ($arResult["PRODUCT"]['QUANTITY']>0){
// 			if ($arResult["PRODUCT"]['QUANTITY'] == 2){
// //				$delivery_title = 'В наличии мало';
// 			}else{
				$delivery_title = 'В наличии';
//			}
		}
	}


	// global $USER;
	// if ($USER->IsAdmin()){
	// 	var_dump($v_d);
	// 	var_dump($delivery_days);
	// 	var_dump($delivery_title);
	// 	var_dump($delivery_days_title);
	// 	var_dump($find);
	// }

	// var_dump($delivery_days);
	// var_dump($delivery_title);
	// var_dump($delivery_days_title);
	// var_dump($find);

	// var_dump($arResult["PRODUCT"]['QUANTITY']);

	// var_dump($delivery_title);
	// var_dump($delivery_days_title);


	if ($find){
		// логика перемещения
	}else{
		if (!isset($arResult["PRODUCT"]['QUANTITY'])){
			$arResult = CCatalogProduct::GetByIDEx($arResult['ID']);
		}

		// global $USER;
		// if ($USER->IsAdmin()){
		// 	var_dump($delivery_days);
		// 	var_dump($find);
		// 	var_dump($arResult["PRODUCT"]['QUANTITY']);
		// 	var_dump($arResult["PROPERTIES"]['delivery_from_msk']['VALUE']);
		// 	// var_dump($arResult['PROPERTIES']['DeliveryDays']['DESCRIPTION']);
		// 	// var_dump($arResult['IBLOCK_ID']);
		// }

		if($arResult["PRODUCT"]['QUANTITY']>0){
			if (($arResult["PROPERTIES"]['PRICE_UPDATE'.CClass::getCurrentAvalCode()]['VALUE'] > 0 || $arResult["PROPERTIES"]['delivery_from_msk']['VALUE']=='Y') || $arResult["PROPERTIES"]['delivery_from_ptr']['VALUE']=='Y'){


				$delivery_days_title = '1-3 дня';
				if((date('w')==5) && date('H'>16)){
					$delivery_days_title = '3-5 дней';
				}elseif(date('w')==6){ // суббота
					$delivery_days_title = '2-4 дня';
				}

				$product_id = $arResult['ID'];
				$delivery = false;
				$obPrice = \Bitrix\Catalog\PriceTable::getList(array(
		            'filter' => array(
		                'PRODUCT_ID' => $product_id,
		                'CATALOG_GROUP_ID' => PriceId(),
		            ),
		            'select' => array(
		                'ID',
		                'PRICE',
		                'PRODUCT_ID',
		                'CURRENCY',
		                'CATALOG_GROUP_ID',
		            ),
		        ));
		        if($rowPrice = $obPrice->fetch()){
		        	if ($rowPrice['PRICE']>0){
		        		$delivery = true;
		        	}
			    }

				// global $USER;
				// if ($USER->IsAdmin()){
				// 	var_dump($arResult['ID']);
				// 	var_dump($arResult["PROPERTIES"]['delivery_from_msk']['VALUE']);
				// 	var_dump($arResult["PROPERTIES"]['delivery_from_ptr']['VALUE']);
				// }

				// перемещение для ЕКБ и СПБ
				//if (in_array($STORE_ID, array(7, 2))){
			    if (($arResult["PROPERTIES"]['delivery_from_msk']['VALUE'] == 'Y') && ($arResult["PROPERTIES"]['delivery_from_ptr']['VALUE'] == 'Y')){
			    	if (in_array($STORE_ID, array(7, 2))){
			    		if($arResult["PROPERTIES"]['delivery_from_msk']['VALUE']=='Y'){ // && !$delivery
							$geo = $APPLICATION->get_cookie("GEOLOCATION_ID");
							$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_DELIVERY_FROM","PROPERTY_DELIVERY_TO");
							$arFilter = Array("IBLOCK_ID"=>119, "ACTIVE"=>"Y", "PROPERTY_SITE"=>SITE_ID, 'PROPERTY_CITY'=>$geo, 'PROPERTY_CITY_FROM'=>129);
							$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
							while($ob = $res->GetNextElement())
							{
							 $arFields = $ob->GetFields();
							 $delivery_days_title = $arFields['PROPERTY_DELIVERY_FROM_VALUE'].'-'.$arFields['PROPERTY_DELIVERY_TO_VALUE'].' дня';
							}
						}

						if($arResult["PROPERTIES"]['delivery_from_ptr']['VALUE']=='Y'){ //  && !$delivery
							$geo = $APPLICATION->get_cookie("GEOLOCATION_ID");
							$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_DELIVERY_FROM","PROPERTY_DELIVERY_TO");
							$arFilter = Array("IBLOCK_ID"=>119, "ACTIVE"=>"Y", "PROPERTY_SITE"=>SITE_ID, 'PROPERTY_CITY'=>$geo, 'PROPERTY_CITY_FROM'=>817);
							$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
							while($ob = $res->GetNextElement())
							{
							 $arFields = $ob->GetFields();
							 $delivery_days_title = $arFields['PROPERTY_DELIVERY_FROM_VALUE'].'-'.$arFields['PROPERTY_DELIVERY_TO_VALUE'].' дня';
							}
						}
			    	}
			    }else{
					if($arResult["PROPERTIES"]['delivery_from_msk']['VALUE']=='Y'){ // && !$delivery
						$geo = $APPLICATION->get_cookie("GEOLOCATION_ID");
						$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_DELIVERY_FROM","PROPERTY_DELIVERY_TO");
						$arFilter = Array("IBLOCK_ID"=>119, "ACTIVE"=>"Y", "PROPERTY_SITE"=>SITE_ID, 'PROPERTY_CITY'=>$geo, 'PROPERTY_CITY_FROM'=>129);
						$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
						while($ob = $res->GetNextElement())
						{
						 $arFields = $ob->GetFields();
						 $delivery_days_title = $arFields['PROPERTY_DELIVERY_FROM_VALUE'].'-'.$arFields['PROPERTY_DELIVERY_TO_VALUE'].' дня';
						}
					}

					if($arResult["PROPERTIES"]['delivery_from_ptr']['VALUE']=='Y'){ //  && !$delivery
						$geo = $APPLICATION->get_cookie("GEOLOCATION_ID");
						$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_DELIVERY_FROM","PROPERTY_DELIVERY_TO");
						$arFilter = Array("IBLOCK_ID"=>119, "ACTIVE"=>"Y", "PROPERTY_SITE"=>SITE_ID, 'PROPERTY_CITY'=>$geo, 'PROPERTY_CITY_FROM'=>817);
						$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
						while($ob = $res->GetNextElement())
						{
						 $arFields = $ob->GetFields();
						 $delivery_days_title = $arFields['PROPERTY_DELIVERY_FROM_VALUE'].'-'.$arFields['PROPERTY_DELIVERY_TO_VALUE'].' дня';
						}
					}
				}

				// global $USER;
				// if ($USER->IsAdmin()){
				// 	var_dump($delivery_title);
				// 	var_dump($delivery_days_title);
				// }

			}
		}
	}
	

	$res = array(
					'delivery_title' => $delivery_title,
					'delivery_days_title' => $delivery_days_title,
					'q' => $arResult["PRODUCT"]['QUANTITY'],
//					'peremeshen' => $peremeshen
				);
	return $res;
}


function GetSaleStoreId(){
	global $APPLICATION;
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$sale_price_id = 5;
	if ($geo_id == 817 || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){ // питер
		$sale_price_id = 6;
	}
	/*if ($geo_id == 2201 || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){ // екатеринбург
		$sale_price_id = 7;
	}*/
	/*if ($geo_id == 1095){ // Краснодар
	    $sale_price_id = 9;
	}*/
	return $sale_price_id;
}
$GLOBALS['customCacheProps'] = [
	'KOMLP' => 'KOMLP_CACHE',
	'ADDITIONAL' => 'ADDITIONAL_CACHE',
	'SPARE' => 'SPARE_PARTS_CACHE',
	'COLLECTION' => 'COLLECTION_ITEMS',
	'SIMILAR' => 'SIMILAR_ITEMS',
	'IN_SET' => 'IN_SET_CACHE'
];

// NOTE для фильтрации доступных товаров
GLOBAL $arFilterProductAvailable;
$arFilterProductAvailable = Array(
		"!PROPERTY_DISCONTINUED" => "Y",
	);
function customFormatPriceClear($price)
{
	return str_replace('₽', '', $price);
}
function GetProductAvailableFilter($arFilter)
{
	$result = Array(
		"!PROPERTY_DISCONTINUED" => "Y",
	);
	
	if (isset($arFilter) && is_array($arFilter) && !empty($arFilter))
		$result = array_merge($arFilter, $result);
	
	return $result;
}

function IsSetProductAvailableFilter($arFilter)
{
	$result = false;
	
	if (isset($arFilter) && is_array($arFilter) && 
				isset($arFilter["!PROPERTY_DISCONTINUED"]) && "Y" == $arFilter["!PROPERTY_DISCONTINUED"])
		$result = true;
	
	return $result;
}

require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CClass.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CDelivery.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CEvent.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CCatalogProductProvider.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/SimpleImage.php");

if(empty($GLOBALS['PAGE_DATA']))
{
	$file = $_SERVER['DOCUMENT_ROOT'].'/local/cache/'.SITE_ID.'-cache_page.php';
	if(file_exists($file))
	{
		$data = file_get_contents($file);
		list($php, $json) = explode("\n", $data, 2);
		$GLOBALS['PAGE_DATA'] = json_decode($json, true);
	}
}
//pr($GLOBALS['PAGE_DATA']);
/************сортировка ************/
AddEventHandler("catalog", "OnBeforeProductAdd", "OnBeforeIBlockElement");
AddEventHandler("catalog", "OnBeforeProductUpdate", "OnBeforeIBlockElement");
function OnBeforeIBlockElement($ID, $arFields = false)
{
	if(is_array($ID) && isset($arFields["QUANTITY"]))
	{
		$arFields = $ID;
		$IS_AVAILABLE = $arFields["QUANTITY"] > 0? 1: 0;
		$ELEMENT_ID = $arFields["ID"];
		CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, array("IS_AVAILABLE" => $IS_AVAILABLE));
	}
	elseif(is_int($ID) && is_array($arFields) && isset($arFields["QUANTITY"]))
	{
		$IS_AVAILABLE = $arFields["QUANTITY"] > 0? 1: 0;
		$ELEMENT_ID = $ID;
		CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, array("IS_AVAILABLE" => $IS_AVAILABLE));
	}
}
/************сортировка ************/
//AddEventHandler("main",     "OnEpilog",             Array("CEventHandler", "Redirect404"));
//AddEventHandler("main",     "OnEndBufferContent",   Array("CEventHandler", "deleteKernelCss"));

AddEventHandler("iblock",   "OnBeforeIBlockElementAdd",         Array("CEventHandler", "OnBeforeIBlockElementAdd"));
AddEventHandler("sale",     "OnSaleComponentOrderProperties",   Array("CEventHandler", "OnSaleComponentOrderProperties"));

//AddEventHandler("sale",     "OnBeforeOrderAdd",     Array("CEventHandler", "OnBeforeOrderAdd"));
//AddEventHandler("iblock",   "OnAfterIBlockElementAdd",      Array("CEventHandler", "OnAfterIBlockElementAdd"));

AddEventHandler("iblock",   "OnBeforeIBlockElementUpdate",   Array("CEventHandler", "OnBeforeIBlockElementUpdate"));
AddEventHandler("iblock",   "OnAfterIBlockElementUpdate",   Array("CEventHandler", "OnAfterIBlockElementUpdate"));


AddEventHandler("iblock", "OnBeforeIBlockPropertyUpdate", Array("CEventHandler", "OnBeforeIBlockPropertyUpdate"));
AddEventHandler("iblock", "OnAfterIBlockPropertyUpdate", Array("CEventHandler", "OnAfterIBlockPropertyUpdate")); 

//AddEventHandler("catalog",  "OnProductSetAdd",     Array("CEventHandler", "UpdateSetsProductPrice"));
//AddEventHandler("catalog",  "OnProductSetUpdate",  Array("CEventHandler", "UpdateSetsProductPrice"));
//AddEventHandler("catalog",  "OnBeforePriceUpdate",  Array("CEventHandler", "OnBeforePriceUpdate"));

use \Bitrix\Main\EventManager;


$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler("main", "OnBuildGlobalMenu", function (&$adminMenu, &$moduleMenu)
{
    $moduleMenu[] = array(
        "parent_menu" => "global_menu_content",
        "sort" => 1000,
        "url"  => "/bitrix/admin/template_settings.php",
        "text" => "Настройки шаблона",
        "items" => array(),
    );
    $moduleMenu[] = array(
        "parent_menu" => "global_menu_content",
        "sort" => 2,
        "url"  => "/bitrix/admin/pretty_sort.php",
        "text" => "Настройки сортировки",
        "items" => array(),
    );
});

/*gifts*/
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CGifts.class.php");

\Bitrix\Main\EventManager::getInstance()->addEventHandler( 
    'sale', 
    'OnSaleOrderBeforeSaved', 
    'CGifts::onSaleOrderBeforeSaved'
);
/*endd gifts*/

/*calltouth*/
\Bitrix\Main\EventManager::getInstance()->addEventHandler( 
    'sale', 
    'OnSaleOrderBeforeSaved', 
    'onSaleOrderBeforeSavedCallTouth'
);

function onSaleOrderBeforeSavedCallTouth(\Bitrix\Main\Event $event)
{
    /** @var Order $order */
    $obOrder = $event->getParameter("ENTITY");

    $propertyCollection = $obOrder->getPropertyCollection();
    $sEmail = $propertyCollection->getUserEmail()->getValue();
    $sName = $propertyCollection->getPayerName()->getValue();
    $sPhone = $propertyCollection->getPhone()->getValue();

    $call_value = $_COOKIE['_ct_session_id']; // ID сессии Calltouch, полученный из cookie

    $sContent = "fio=" . (!empty($sName) ? $sName : '')
        . "&phoneNumber=" . (!empty($sPhone) ? $sPhone : '')
        . "&email=" . (!empty($sEmail) ? $sEmail : '')
        . "&subject=" . urlencode('Форма оформления заказа')
        . "" . ($call_value != 'undefined' ? "&sessionId=" . $call_value : "");

    $ct_site_id = '43528';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded;charset=utf-8"));
    curl_setopt($ch, CURLOPT_URL, 'https://api.calltouch.ru/calls-service/RestAPI/requests/' . $ct_site_id . '/register/');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sContent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (!$result = curl_exec($ch)) {
        $sContent = '';
    }
    curl_close($ch);

    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/onSaleOrderBeforeSavedCallTouth.txt', 'onSaleOrderBeforeSavedCallTouth');
}

/*end calltouth*/


$eventManager->addEventHandler('', 'CommentsOnBeforeAdd', 'OnBeforeUpdateComments');
$eventManager->addEventHandler('', 'CommentsOnBeforeUpdate', 'OnBeforeUpdateComments');



function OnBeforeUpdateComments(\Bitrix\Main\Entity\Event $event)
{
	global $USER;
	CModule::IncludeModule("iblock");

	$eventType = $event->getEventType();
	if($eventType == 'CommentsOnBeforeUpdate')
	{
	    $id = $event->getParameter("id");
	    $id = $id["ID"];
    }

    $entity = $event->getEntity();
    $entityDataClass = $entity->GetDataClass();
    $arFields = $event->getParameter("fields");

    $result = new \Bitrix\Main\Entity\EventResult();

    foreach($arFields as $name=>$value)
    {
    	if(strpos($name, 'UF_ELEMENT_') === 0 and $value > 0)
    	{
    		$suffix = str_replace('UF_ELEMENT_', '', $name);
    		$arElement = CIBlockElement::GetByID($value)->GetNext();
    		if(!empty($arElement['IBLOCK_SECTION_ID']))
    		{
    			$arFields['UF_SECTION_'.$suffix] = $arElement['IBLOCK_SECTION_ID'];
    		}
    	}
    }

    if($arFields['UF_RATING'] > 5)
    {
    	$arFields['UF_RATING'] = 5;
    }
    elseif($arFields['UF_RATING'] < 0)
    {
    	$arFields['UF_RATING'] = 0;
    }

    if(!$arFields['UF_USER'])
    {
    	$arFields['UF_USER'] = $USER->GetID();
    }

    //pr($arFields); die();

    $result->modifyFields($arFields);

    return $result;
}

AddEventHandler('search', 'BeforeIndex', "onBeforeIndexHandler");
function onBeforeIndexHandler($arFields) {
    if ($arFields["MODULE_ID"] == "iblock") {
		
		//Удаляем из поиска товары которые в не активном или корневом разделе
		$index = true;
        $check = substr($arFields["ITEM_ID"], 0, 1);
        if ($check == "S") {
			$secId = substr($arFields["ITEM_ID"], 1);
			if(!empty($secId)){
				$resActive = getActiveSection($secId, $arFields["PARAM2"]);
				if (!$resActive) {
					$index = false;
				}
			}
			else{
				$index = false;
			}
			
        } 
		else {
            $res = CIBlockElement::GetList(
                Array()
                , Array("IBLOCK_ID" => $arFields["PARAM2"], "ID" => $arFields["ITEM_ID"])
                , false
                , false
                , Array("ID", "IBLOCK_ID", "ACTIVE", "IBLOCK_SECTION_ID")
            );
			if ($item = $res->Fetch()) {
				if(!empty($item['IBLOCK_SECTION_ID'])){
					$resActive = getActiveSection($item['IBLOCK_SECTION_ID'], $arFields["PARAM2"]);
					if (!$resActive) {
						$index = false;
					}
				}
				else{
					$index = false;
				}
			}
        }


        
		if (!$index) {
			$arFields["BODY"] = '';
			$arFields["TITLE"] = '';
		}
		//В поисковый индекс добавляем ID 
		else{
			$res = CIBlockElement::GetList(
                Array()
                , Array("IBLOCK_ID" => $arFields["PARAM2"], "ID" => $arFields["ITEM_ID"])
                , false
                , false
                , Array("PROPERTY_ARTNUMBER")
            );

            if($article = $res->fetch()) {
                $article = $article['PROPERTY_ARTNUMBER_VALUE'];
                $ru=	["А","В","С","Е","Н","Х","М","К","У","Р","О","а","в","с","е","н","х","м","к","у","р","о"];
				$lat=	["A","B","C","E","H","X","M","K","Y","P","O","a","b","c","e","h","x","m","k","y","p","o"];
				$article.=" ".str_replace($lat,$ru,$article)." ".str_replace($ru,$lat,$article);
                $article.=' '.CSearchLanguage::ConvertKeyboardLayout($article, "ru", "en").' '.CSearchLanguage::ConvertKeyboardLayout($article, "en", "ru");

                $arFields["BODY"] .= ' '.$article;
                $article = str_replace('.', '', $article);
                $article = str_replace('-', '', $article);
                $arFields["BODY"] .= ' '.$article;
            }

			$arFields["BODY"] .= ' '.$arFields["ITEM_ID"];
		}
		
    }

    return $arFields;
}

function getActiveSection($idSection, $IBLOCK_ID) {
	
	$result = true;
	
	$resSection = CIBlockSection::GetList(
		Array('SORT' => 'ASC')
		, Array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "ID" => $idSection, "GLOBAL_ACTIVE" => "Y")
		, false
		, Array("ID")
	);
	if (!$resSection->Fetch()) {
		$result = false;
	}
	
	return $result;
}

function getShowPropertyValues()
{
	return $GLOBALS['PAGE_DATA']['SHOW_PROPERTIES'];
}

function getSEOFilterValues()
{
	return $GLOBALS['PAGE_DATA']['SEO_FILTER'];
}

function getBreadcrumbTags(&$arBreadcrumbsTags, $arTags, $codeTags, $pageUrl){

	foreach($arTags as $code => $arItem){
		if($code == $codeTags){
			$arBreadcrumbsTags[$pageUrl][] = array('NAME' => $arItem['NAME'], 'CODE' => $arItem['CODE'], 'NAME_BREAD' => $arItem['NAME_BREAD']);
			
			if(!empty($arItem['THIS_PARENT'])){
				getBreadcrumbTags($arBreadcrumbsTags, $arTags, $arItem['THIS_PARENT'], $pageUrl);
			}
			
		}
	}
}

function pr($ar, $f = false)
{
	global $USER;
	static $is_console;
	if(!isset($is_console))
	{
		$is_console = PHP_SAPI == 'cli' or !isset($_SERVER['REQUEST_URI']);
	}
	if(!$is_console and (!$f and !empty($USER) and !$USER->IsAdmin()))
	{
		return '';
	}
	echo '<pre style="overflow: auto; max-width: 100%; max-height: 600px;">'.print_r($ar, 1).'</pre>';
}



function CustomUpdateProductCache($item, $IBLOCK_ID)
{
	$productId = $item['ID'];
	
	$arProps = array();

	// Наборы
	$arProps['KOMLP_CACHE'] = false;
	$arSetItems = CCatalogProductSet::getAllSetsByProduct($item['ID'], CCatalogProductSet::TYPE_GROUP);
	$arSetItems = current( $arSetItems );
	$kompl = $arSetItems['ITEMS'];

	if (!empty($kompl))
	{
		$ids = $sort = [];
		foreach($kompl AS $t)
		{
			$ids[] = $t['ITEM_ID'];
			$sort[$t['ITEM_ID']] = $t['SORT'];
		}
		
		$arProps['KOMLP_CACHE'] = GetAdditionals(['ID' => array_unique($ids)], 500, $IBLOCK_ID);
	}

	// Доп товары
	$arProps['ADDITIONAL_CACHE'] = false;
	if (!empty($item['PROPERTY_ADDITIONAL_VALUE']))
	{
		//pr($item['PROPERTY_ADDITIONAL_VALUE']); die();
		$arProps['ADDITIONAL_CACHE'] = GetAdditionals(['ID' => $item['PROPERTY_ADDITIONAL_VALUE']], 500, $IBLOCK_ID);
	}

	// Запасные детали
	$arProps['SPARE_PARTS_CACHE'] = false;
	if (!empty($item['PROPERTY_SPARE_PARTS_VALUE']))
	{
		
		$arProps['SPARE_PARTS_CACHE'] = GetAdditionals(['ID' => $item['PROPERTY_SPARE_PARTS_VALUE']], 500, $IBLOCK_ID);
	}

	// Для текущего товара достаем Товары из коллекции
	$arProps['COLLECTION_ITEMS'] = false;
	if ($item['PROPERTY_SERIES_VALUE'])
	{
		$arrFilterCollection = [
			"PROPERTY_SERIES" => $item['PROPERTY_SERIES_VALUE'],
			"!ID" => $productId,
		];
		if($item['PROPERTY_MANUFACTURER_VALUE'])
		{
			$arrFilterCollection['PROPERTY_MANUFACTURER'] = $item['PROPERTY_MANUFACTURER_VALUE'];
		}

		$arProps['COLLECTION_ITEMS'] = GetAdditionals($arrFilterCollection, 500, $IBLOCK_ID);
	}
	
	// Для текущего товара достаем Похожие товары
	if($IBLOCK_ID == 50)
	{	
		$arProps['SIMILAR_ITEMS'] = false;
		$goSearch = false;
		$arFilterSimilar = [
			"!ID" => $productId
		];
		$arSimilarProps = [
			'MATERIAL_ARMATURY_TSVET_ARMATURY',
			'PARAMETRY_PLAFONA_TSVET_PLAFONOV',
			'PARAMETRY_PLAFONA_FORMA_PLAFONA',
			'PARAMETRY_PLAFONA_KOLICHESTVO_PLAFONOV',
			'STIL',
			'VID_SVETILNIKA',
		];
		foreach($arSimilarProps as $code)
		{
			if($item['PROPERTY_'.$code.'_VALUE']){
				
				if($code == 'PARAMETRY_PLAFONA_KOLICHESTVO_PLAFONOV'){
					$arFilterSimilar['PROPERTY_'.$code] = $item['PROPERTY_'.$code.'_VALUE'];
				}
				else{
					$arValItem = $item['PROPERTY_'.$code.'_VALUE'];
					
					$arValItemId = "";
					$a = 0;
					foreach($arValItem as $arItemKey => $arItemValue){
						if($a == 0){
							$arValItemId = $arItemKey;
						}
						$a++;
					}
					
					$arFilterSimilar['PROPERTY_'.$code] = $arValItemId;
				}
				
				$goSearch = true;
			}
		}
		
		if($goSearch)
		{
			$arProps['SIMILAR_ITEMS'] = GetAdditionals($arFilterSimilar, 500, $IBLOCK_ID);
		}
	}
	
	if($IBLOCK_ID == 54)
	{
		$arProps['IN_SET_CACHE'] = false;
		//Товар используется в наборах
		$arIdsToKits = array();
		$rsElem = CCatalogProductSet::getList(
			array(),
			array(
				array(
					'LOGIC' => 'OR',
					'TYPE' => CCatalogProductSet::TYPE_GROUP,
					'TYPE' => CCatalogProductSet::TYPE_SET
				),
				'ITEM_ID' => $item['ID']),
			false,
			false,
			array('SET_ID', 'OWNER_ID', 'ITEM_ID', 'TYPE')
		);
		while($set = $rsElem->Fetch())
		{
			if($set['SET_ID'] != '0')
			{
				$arIdsToKits[] = $set['OWNER_ID'];
			}
		}
		if(count($arIdsToKits) > 0)
		{
			$arProps['IN_SET_CACHE'] = $arIdsToKits;
		}
	}
	
	//Добавляем свойства к товару
	CIBlockElement::SetPropertyValuesEx($productId, $IBLOCK_ID, $arProps);
	
	return $arProps;
}

AddEventHandler("iblock",   "OnAfterIBlockElementUpdate", ProductCache);

function ProductCache(&$arFields){
    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_ADDITIONAL","PROPERTY_SPARE_PARTS","PROPERTY_SERIES","PROPERTY_MANUFACTURER");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arFilter = Array("IBLOCK_ID"=>$arFields['IBLOCK_ID'], "ID"=>$arFields['ID']);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ar_res = $res->Fetch()){
        CustomUpdateProductCache($ar_res, $arFields['IBLOCK_ID']);
    }

}


function GetAdditionals($arFilter, $limit, $IBLOCK_ID) {
	if (empty($arFilter))
		return FALSE;

	$arFilter["IBLOCK_ID"] = $IBLOCK_ID;
	$arFilter["ACTIVE"]	= "Y";
	$arFilter["CATALOG_AVAILABLE"] = 'Y';
	$arFilter["SECTION_GLOBAL_ACTIVE"] = 'Y';
	
	$arResult = array();

	$arSelect = ["ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"];
	$res = CIBlockElement::GetList(["SORT" => "ASC"], $arFilter, false, Array("nPageSize"=>$limit), $arSelect);
	while($arItem = $res->Fetch())
	{
		$arResult[$arItem["IBLOCK_SECTION_ID"]][] = $arItem['ID'];
	}
	
	return json_encode($arResult);
}

function customFormatPrice($price)
{
	return str_replace('₽', '<i class="znakrub">c</i>', $price);
}

function d($var, $title = '')
{
    if (in_array($_SERVER['REMOTE_ADDR'], ['217.170.124.3', '89.178.226.14', '217.170.124.7'])) {
        print '<pre style="width:9000px;background: #000; color: #0f0; border: 1px solid #0f0;">';
        if ($title != '') {
            $ret = '<p style="margin-top:-0px;background-color:#DD0000;font-size:17px;padding:5px 5px 5px 5px; border:1px solid green;color:black;font-weight:bold">'
                . $title . '</p>';
        }
        $trace = debug_backtrace();
        $ret .= $trace[0]['file'] . ':' . $trace[0]['line'] . '</br></br>';

        $ret .= print_r($var, true);
        $ret = str_replace('=>', '<font color="#ffffff">=></font>', $ret);
        $ret = str_replace('[', '[ <font color="#FFFF00">', $ret);
        $ret = str_replace(']', '</font> ]', $ret);

        print $ret;
        print '</pre>';
        print "<hr>";
    }
}

function isPriceManager($user)
{
  $result = false;

  if(!is_a($user, 'CUser')) {
    return $result;
  }
  
  $arGroupAvalaible = array(1, 7, 8); // массив групп, которые могут редактировать цены через скрипты в /update_price/
  $arGroups = CUser::GetUserGroup($user->GetID());
  $result_intersect = array_intersect($arGroupAvalaible, $arGroups);
  if(!empty($result_intersect)) {
    $result = true;
  }
  
  return $result;
}

// =========== REVIEWS ========
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("Reviews", "OnBeforeIBlockElementAddHandler"));

class Reviews
{
    // создаем обработчик события "OnBeforeIBlockElementAdd"
    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        if ((int)$arFields["IBLOCK_ID"] === 111){
            GLOBAL $USER;
            $arFields["PROPERTY_VALUES"]["user"] = $USER->GetID();
        }
    }
}
// =========== REVIEWS ========
// redirects tag - begin
AddEventHandler("main", "OnBeforeProlog", "MyOnBeforePrologHandler", 50);

function MyOnBeforePrologHandler()
{
	if (strpos($_SERVER['REQUEST_URI'], '/shop/product/') !== false) {
		$url = str_replace('/shop/product/', '/product/', $_SERVER['REQUEST_URI']);
		if (substr($url, -1) != '/'){
			$url .= '/';
		}

		LocalRedirect($url, false, 301);
		//echo "123123";
	}

	if (strpos($_SERVER['REQUEST_URI'], '/shop/category/') !== false) {
		$url = str_replace('/shop/category/', '/catalog/', $_SERVER['REQUEST_URI']);
		if (substr($url, -1) != '/'){
			$url .= '/';
		}

		LocalRedirect($url, false, 301);
	}


	if (strpos($_SERVER['REQUEST_URI'], '/shop/brand/') !== false) {
		$url = '/';

		LocalRedirect($url, false, 301);
	}

	if (strpos($_SERVER['REQUEST_URI'], '/vendors/') !== false) {
		$url = '/';

		LocalRedirect($url, false, 301);
	}
   global $USER;
	$IB_TAGS = 83;

	$arFilter = Array('IBLOCK_ID'=>$IB_TAGS, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE'=>'Y', "INCLUDE_SUBSECTIONS" => "Y");
	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('UF_*'));
	$links = array();
	while($ar_result = $db_list->GetNext())
	{
		$url = '';
		$res = CIBlockSection::GetByID($ar_result['UF_FILTER_FOR_SEC']);
		if($ar_res = $res->GetNext()){
			$url = $ar_res['SECTION_PAGE_URL'].$ar_result['CODE'].'/';
			$url2 = $ar_res['SECTION_PAGE_URL'].$ar_result['UF_FILTER_URL_PAGE'].'/';

			$links[$url2] = $url;
		}
	}

	$arSelect = Array("ID", "NAME",  "CODE", "PROPERTY_FILTER_FOR_SECTION", "PROPERTY_FILTER_URL_PAGE");
	$arFilter = Array("IBLOCK_ID"=>$IB_TAGS, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "INCLUDE_SUBSECTIONS" => "Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	//$links = array();
	//var_dump(123);
	while($ob = $res->GetNextElement())
	{
		$ar_result = $ob->GetFields();

		//var_dump($ar_result);
		$url = '';
		$res = CIBlockSection::GetByID($ar_result['PROPERTY_FILTER_FOR_SECTION_VALUE']);
		if($ar_res = $res->GetNext()){
			$url = $ar_res['SECTION_PAGE_URL'].$ar_result['CODE'].'/';
			$url2 = $ar_res['SECTION_PAGE_URL'].$ar_result['PROPERTY_FILTER_URL_PAGE_VALUE'].'/';

			$links[$url2] = $url;
		}
	}

	//var_dump($links);

	$url = $_SERVER['REQUEST_URI'];
	foreach ($links as $link => $redirect_link){
		if (strpos($url, $link) !== false){
			$url = str_replace($link, $redirect_link, $url);

			LocalRedirect($url, false, '301 Moved permanently');
		}
	}


}
// redirects tag - end

//for all of the following functions ONLY include.php->you_file
//p.s. if you are not lazy rewrite code above and remove this comment
require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include.php");
if(isset($_GET['a'])){
	
#file stopsovetnik # http://marketplace.1c-bitrix.ru/solutions/bart.stopsovetnik/
# http://marketplace.1c-bitrix.ru/solutions/bart.stopsovetnik/
#
$utm_source = $_GET['utm_source'];
$utm_campaign = $_GET['utm_campaign'];

$url = base64_decode($_GET['a']);

$ymclid = $_GET['ymclid'];

$dop = (strpos($url, '?') === false ? '?' : '&')."utm_source=".$utm_source."&utm_campaign=".$utm_campaign."&ymclid=".$ymclid;
	$dop = str_replace('&amp;','&',$dop);

if($url){
header("Location: ".htmlspecialchars_decode($url.$dop));
}else{
exit('no hack');
}
	
}

require($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/retail_crm.php");