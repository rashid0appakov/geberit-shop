<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?php
use Bitrix\Main\Application;

$request = Application::getInstance()->GetContext()->getRequest();
$regionId = $request->getCookie("GEOLOCATION_ID");

$locationID = $regionId ? $regionId : DEFAULT_GEOLOCATION_ID;

if (!empty($arResult['SHIPMENT'])){
    $arSections = [];
    $firstDS    = current($arResult['SHIPMENT']);
    $firstDS    = $firstDS['DELIVERY'];
    $arResult['DELIVERY']['NAME']   = CClass::getDeliveryServiceName($firstDS["ID"], $firstDS["NAME"], $locationID, $firstDS['PARENT_ID']);
}

if (empty($arResult['ERRORS']['FATAL']))
{
  $arServices = [];
  if (!empty($arResult['ORDER_PROPS']))
  {
		foreach($arResult['ORDER_PROPS'] AS $arProp){
			if ($arProp['CODE'] == 'PHONE')
				$userPhone  = $arProp['VALUE'];
			if (in_array($arProp['ID'], CClass::$arOrderServicesProps) && $arProp['VALUE'] == 'Y')
			   $arServices[] = $arProp['NAME'];
		}
  }

   $arResult['DATA'] = array(
    'SERVICES' => $arServices,
    'NAME' => !in_array($USER->GetEmail(), ['test@test.ru', CClass::getOneClickEmail()]) ? $USER->GetFirstName().', ' : '',
    'PAYS_YSTEM' => current($arResult['PAYMENT']),
    // 'DELIVERY_SERVICE' => current($arResult['SHIPMENT']),
    'ORDER_IMG' => (file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/images/order-ok.png') ?  SITE_TEMPLATE_PATH.'/images/order-ok.png' : $templateFolder.'/images/order-ok.png'),
    'CONTACTS' => CClass::Instance()->getLocationContacts(),
  );
}