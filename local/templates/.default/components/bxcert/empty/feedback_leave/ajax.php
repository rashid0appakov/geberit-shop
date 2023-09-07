<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
Loader::includeModule("sale");

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
	"error" => true,
	"message" => "Ошибка запроса",
);

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
	$order = $request->getPost("order");
	$phone = $request->getPost("phone");
	$params = $request->getPost("params");

	if (strlen($order) <= 0)
	{
		$data["message"] = "Не указан номер";
	}
	elseif (strlen($phone) <= 0)
	{
		$data["message"] = "Не указан телефон";
	}
	elseif (Loader::includeModule("iblock"))
	{
		$params = unserialize(base64_decode($params));

		if ($arOrder = CSaleOrder::GetByID($order))
		{
			$orderNum = $order;
			$statusOrder = $arOrder["STATUS_ID"];
			$managerId = $arOrder["RESPONSIBLE_ID"];
			$sumOrder = $arOrder["PRICE"];
			$payment = $arOrder["PAY_SYSTEM_ID"];
			$data['statusOrder'] = $statusOrder;
			$data['managerId'] = $managerId;
			$data['sumOrder'] = $sumOrder;
			$data['orderNum'] = $orderNum;
			$data['payment'] = $payment;

			if ($arUser = CUser::GetByID($managerId)->Fetch()){
				$nameUser = $arUser["NAME"];
				$lastNameUser = $arUser["LAST_NAME"];
				$phoneUser = $arUser["WORK_PHONE"];
				$data["nameUser"] = $nameUser . " " . $lastNameUser;
				$data["phoneUser"] = $phoneUser;
			}
		}
		else
		{
			$data["message"] = "Не найден заказ";
		}
	}
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo json_encode($data);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();