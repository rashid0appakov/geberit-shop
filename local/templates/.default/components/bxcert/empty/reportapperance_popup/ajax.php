<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Application::getInstance()->getContext()->getRequest();

if (!check_bitrix_sessid() || !$request->isAjaxRequest())
{
	__ReturnError("Ошибка");
}

if (!Loader::includeModule("sale") || !Loader::includeModule("catalog") || !Loader::includeModule("iblock"))
{
	__ReturnError("Ошибка");
}


switch ($request["action"])
{
	case "getinfo":
		$productId = (int) $request["productId"];

		if (!$productId) __ReturnError("Не указан ID товара");

		$ob = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => 15,
				"ID" => $productId,
			),
			false,
			false,
			array(
				"ID",
				"IBLOCK_ID",
				"NAME",
				"DETAIL_PICTURE",
			)
		);
		if ($arItem = $ob->Fetch())
		{
			__ReturnAnswer(array(
				"ID" => $arItem["ID"],
				"NAME" => $arItem["NAME"],
				"IMAGE" => CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 300, "height" => 300))["src"],
			));
		}
		else
		{
			__ReturnError("Товар не найден");
		}
		break;
	case "submit":
		$name = $request["name"];
		$email = $request["email"];
		$phone = $request["phone"];
		$productId = $request["productId"];

		if (!$name) __ReturnError("Не указано имя");
		if (!$email) __ReturnError("Не указан email");
		if (!$phone) __ReturnError("Не указан телефон");
		if (!$productId) __ReturnError("Не указан ID товара");

		$el = new \CIBlockElement();
		$elementId = $el->Add(array(
			"IBLOCK_ID" => 40,
			"NAME" => date("d.m.Y H:i:s"),
			"PROPERTY_VALUES" => array(
				"PRODUCT_ID" => $productId,
				"NAME" => $name,
				"EMAIL" => $email,
				"PHONE" => $phone,
			),
		));
		if (!$elementId)
		{
			__ReturnError($el->LAST_ERROR);
		}
		else
		{
			__ReturnAnswer(array());
		}

		break;
	default:
		__ReturnError("Действие не поддерживается");
		break;
}
















function __ReturnAnswer($data)
{
	echo json_encode($data);
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
	die();
}

function __ReturnError($message)
{
	__ReturnAnswer(array(
		"error" => $message,
	));
}