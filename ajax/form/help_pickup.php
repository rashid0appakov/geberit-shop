<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
	"error" => true,
	"message" => "Ошибка запроса",
);

require_once(__DIR__.'/_antispam.php');

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
	$name = $request->getPost("name");
	$phone = $request->getPost("phone");
	$text = $request->getPost("text");
	$params = $request->getPost("params");

	if (strlen($name) <= 0)
	{
		$data["message"] = "Не указано имя";
	}
	elseif (strlen($phone) <= 0)
	{
		$data["message"] = "Не указан телефон";
	}
	elseif (strlen($text) <= 0)
	{
		$data["message"] = "Не указано сообщение";
	}
	elseif (Loader::includeModule("iblock"))
	{
		$params = unserialize(base64_decode($params));

		if (!!$params)
		{
			$arFields = array(
				"NAME" => date("d.m.Y H:i:s"),
				"IBLOCK_ID" => 37,
				"IBLOCK_SECTION_ID" => false,
				"PROPERTY_VALUES" => array(
					"NAME" => $name,
					"PHONE" => $phone,
					"TEXT" => $text,
				),
			);
			
			$el = new CIBlockElement;
			$elemenetId = $el->Add($arFields);

			if ($elemenetId)
			{
				$data["error"] = false;
				unset($data["message"]);
			}
		}		
	}
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo json_encode($data);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();