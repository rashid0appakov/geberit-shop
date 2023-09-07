<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\UserTable;

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
	"error" => true,
	"message" => "Ошибка авторизации",
);

if (
	check_bitrix_sessid()
	&&
	$request->isPost()
)
{
	$login = $request->getPost("login");
	$password = $request->getPost("password");
	$remember = $request->getPost("remember");

	if (strlen($login) <= 0)
	{
		$data["message"] = "Не указан email или телефон";
	}
	elseif (strlen($password) <= 0)
	{
		$data["message"] = "Не указан пароль";
	}
	else
	{
		$email = $login;
		$phone = $login;
		$phone = preg_replace("/\D/", "", $phone);

		$filter = array("LOGIC" => "OR");
		$filter["=EMAIL"] = $email;
		$filter["=LOGIN"] = $login;
		if (!!$phone) $filter["=PERSONAL_PHONE"] = $phone;
		$users = UserTable::getList(array(
			"select" => array(
				"ID",
				"LOGIN",
			),
			"filter" => $filter,
		))->fetchAll();

		$data["message"] = "Неверный email, телефон или пароль";
		if (count($users) >= 1)
		{
			$user = $users[0];
			$userLogin = $user["LOGIN"];

			$arAuthResult = $USER->Login($userLogin, $password, $remember == "Y");
			if ($arAuthResult === true)
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