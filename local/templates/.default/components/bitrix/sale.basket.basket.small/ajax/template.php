<?php
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;

$request = Application::getInstance()->getContext()->getRequest();

if (!check_bitrix_sessid() || !$request->isAjaxRequest()) return;
if (!Loader::includeModule("sale") || !Loader::includeModule("catalog")) return;

/*$signer = new \Bitrix\Main\Security\Sign\Signer;

try
{
	$params = $signer->unsign($request["params"], "sale.basket.basket.small");
	$params = unserialize(base64_decode($params));
}
catch (\Bitrix\Main\Security\Sign\BadSignatureException $e)
{
	return;
}*/

$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.small",
	"footer",
	[
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_ORDER" => "/personal/order/",
		"SHOW_DELAY" => "N",
		"SHOW_NOTAVAIL" => "N",
		"SHOW_SUBSCRIBE" => "N",
		"IS_AJAX" => "Y"
	]
);