<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\UserTable;

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
	"error" => true,
	"message" => "Ошибка регистрации",
);

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
	$email = $request->getPost("email");
	$name = $request->getPost("name");
	$lastName = $request->getPost("last_name");
	$phone = $request->getPost("phone");
	$password = $request->getPost("password");
	$confirm = $request->getPost("confirm");

	if (strlen($email) <= 0)
	{
		$data["message"] = "Не указан email";
	}
	elseif (strlen($name) <= 0)
	{
		$data["message"] = "Не указано имя";
	}
	elseif (strlen($lastName) <= 0)
	{
		$data["message"] = "Не указана фамилия";
	}
	elseif (strlen($phone) <= 0)
	{
		$data["message"] = "Не указан телефон";
	}
	elseif (strlen($password) <= 0)
	{
		$data["message"] = "Не указан пароль";
	}
	elseif (strlen($confirm) <= 0)
	{
		$data["message"] = "Не указано подтверждение пароля";
	}
	else
	{
		$login = $email;
		$phone = preg_replace("/\D/", "", $phone);

		if (strlen($phone) <= 0)
		{
			$data["message"] = "Неверный формат телефона";
		}
		else
		{
			$phoneUsers = UserTable::getList(array(
				"select" => array(
					"ID",
					"PERSONAL_PHONE",
				),
				"filter" => array(
					"=PERSONAL_PHONE" => $phone
				),
			))->fetchAll();

			if (count($phoneUsers) > 0)
			{
				$data["message"] = "Указанный телефон уже зарегестрирован";
			}
			else
			{
				$regResult = $USER->Register($login, $name, $lastName, $password, $confirm, $email);
				if ($regResult["TYPE"] == "ERROR")
				{
					$data["message"] = strip_tags($regResult["MESSAGE"]);
				}
				elseif ($regResult["TYPE"] == "OK")
				{
					$userId = $regResult["ID"];
					$USER->Update($userId, array("PERSONAL_PHONE" => $phone));

					$data["error"] = false;
					unset($data["message"]);
				}
				else
				{
					$data["message"] = "Ошибка";
				}
			}
		}
	}
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo json_encode($data);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();