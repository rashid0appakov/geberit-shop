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
	$text = $request->getPost("text");
	$select = $request->getPost("select");
	$page = $request->getPost("page");
	$params = $request->getPost("params");
	
	if (strlen($text) <= 0){
		$data["message"] = "Не указано сообщение";
	}
	elseif(strlen($select) <= 0){
		$data["message"] = "Не указан тип";
	}
	elseif (Loader::includeModule("iblock")){
		$params = unserialize(base64_decode($params));

		if (!!$params)
		{
			$arFields = array(
				//"NAME" => date("d.m.Y H:i:s"),
				"IBLOCK_ID" => 98,
				"IBLOCK_SECTION_ID" => false,
				"NAME" => "Нашли ошибку",
				"PROPERTY_VALUES" => array(
					"TEXT" => $text,
					"SELECT" => $select,
					"PAGE" => $page,
				),
			);
			
			$el = new CIBlockElement;
			$elemenetId = $el->Add($arFields);

			if ($elemenetId)
			{
				$arEventFields = array(
					"TEXT" => $text,
					"SELECT" => $select,
					"PAGE" => $page
				);
				CEvent::Send("SEARCH_ERROR", SITE_ID, $arEventFields);
				
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