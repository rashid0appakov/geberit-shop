<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();
$server = Application::getInstance()->getContext()->getServer();

$data = array(
	"error" => true,
	"message" => "Ошибка запроса",
);

require_once(__DIR__.'/_antispam.php');
require_once(__DIR__.'/vendor/autoload.php');

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
    $host = $server['SERVER_NAME'];
	$name = $request->getPost("name");
	$phone = $request->getPost("phone");
	$text = $request->getPost("text");
	$file = $request->getFile("file");
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
		
		//print_r($params);exit;

		if (!!$params)
		{
			$arFields = array(
				"NAME" => date("d.m.Y H:i:s"),
				"IBLOCK_ID" => 38,
				"IBLOCK_SECTION_ID" => false,
				"PROPERTY_VALUES" => array(
					"NAME" => $name,
					"PHONE" => $phone,
					"TEXT" => $text,
					"FILE" => !!$file ? $file : false,
				),
			);
			            
			$el = new CIBlockElement;
			$elemenetId = $el->Add($arFields);

            $retailCRM = new \RetailCrm\ApiClient(
                'https://tiptop-shop.retailcrm.ru',
                'UtWdg9Mahv1rSeZhloofpIYOlorjo5ff'
            );
            
            $postData = [
                'orderType' => 'eshop-individual',
                'orderMethod' => '61564',
                'firstName' => $name,
                'phone' => $phone,
                'customerComment' => $text, 
                'items' => []
            ];

            if ($host == 'tiptop-shop.ru') {
                $host = 'www-tiptop-shop-ru';
            }

            $retailCRM->request->ordersCreate($postData, $host);

			if ($elemenetId)
			{
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