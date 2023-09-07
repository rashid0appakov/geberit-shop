<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

global $APPLICATION;
global $USER;

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
    "error" => "Ошибка",
);

if (
    check_bitrix_sessid()
    &&
    $request->isAjaxRequest()
)
{
    switch ($request["action"])
    {
        case "confirm":
            $locationId = $request["locationId"];
            if (!!$locationId)
            {
                $APPLICATION->set_cookie("GEOLOCATION_ID", $locationId);
                $_SESSION['GEOLOCATION_ID'] =  $locationId;
                //$APPLICATION->set_cookie("GEOLOCATION_ID", $locationId,'','/','.geberit-shop.ru');
                $data["error"] = false;
                $data["locationID"] = $locationId;
            }
            else
            {
                $data["error"] = "Не передан ID";
            }

            break;
        default:
            $data["error"] = "Не поддерживается";
            break;
    }
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo json_encode($data);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
die();