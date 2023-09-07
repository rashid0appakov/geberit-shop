<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location;

global $APPLICATION;
global $USER;

Loader::includeModule("sale");

$request = Application::getInstance()->getContext()->getRequest();

$data = array(
    "error" => "Ошибка",
);

if (
    check_bitrix_sessid()
    &&
    $request->isPost()
)
{
    switch ($request->getPost("action"))
    {
        case "search": 
            $phrase = $request->getPost("text");
        
            $searchItems = array();
            $ob = \Bitrix\Sale\Location\Search\Finder::find(
                array(
                    "select" => array(
                        "ID",
                        //"LNAME",
                        "LEFT_MARGIN",
                        "RIGHT_MARGIN",
                    ),
                    "filter" => array(
                        "?NAME.NAME" => $phrase,
                        "=NAME.LANGUAGE_ID" => LANGUAGE_ID,
                        "=TYPE.ID" => "5",
                    ),
                ),
                array(
                    "FALLBACK_TO_NOINDEX_ON_NOTFOUND" => false,
                )
            );
            while ($item = $ob->fetch())
            {
                if(!isset($item['ID'])) $item['ID'] = $item['VALUE'];
        
                $searchItems[] = $item;        
            }
        
            $data["error"] = false;
            $data["items"] = array();
        
            if (count($searchItems) > 0)
            {
                $ob = \Bitrix\Sale\Location\LocationTable::getPathToMultipleNodes(
                    $searchItems, 
                    array(
                        'select' => array('ID', 'LNAME' => 'NAME.NAME'),
                        'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
                    )
                );
                while ($item = $ob->fetch())
                {
                    $id = $item["ID"];
                    $name = reset($item["PATH"])["LNAME"];
                    $path = array_map(function($item) {
                        return $item["LNAME"];
                    }, $item["PATH"]);
        
                    $data["items"][] = array(
                        "ID" => $id,
                        "NAME" => $name,
                        "PATH" => implode(", ", $path),
                    );
                }
            }
            break;
        case "select": 
            $locationId = intval($request->getPost("id"));
            if (!!$locationId)
            {
			    $locationRuntime = array(
			        "COUNTRY" => array(
			            'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
			            'reference' => array(
			                '=this.COUNTRY_ID' => 'ref.LOCATION_ID',
			            ),
			        ),
			        "REGION" => array(
			            'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
			            'reference' => array(
			                '=this.REGION_ID' => 'ref.LOCATION_ID',
			            ),
			        ),
			        "CITY" => array(
			            'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
			            'reference' => array(
			                '=this.CITY_ID' => 'ref.LOCATION_ID',
			            ),
			        ),
			    );
			    $locationSelect = array(
			        "ID",
			        "COUNTRY_NAME" => "COUNTRY.NAME",
			        "REGION_NAME" => "REGION.NAME",
			        "CITY_NAME" => "CITY.NAME",
			    );
		        $locationData = Location\LocationTable::getRow(array(
		            "runtime" => $locationRuntime,
		            "select" => $locationSelect,
		            "filter" => array(
		                "ID" => $locationId,
		            ),
		        ));
				if(!empty($locationData))
				{
	                $APPLICATION->set_cookie("GEOLOCATION_ID", $locationId);
                    $_SESSION['GEOLOCATION_ID'] =  $locationId;
	                $data["error"] = false;
	                $data["locationID"] = $locationId;
	                $data["locationData"] = $locationData;
	            }
	            else
	            {
	            	$data["error"] = "Местоположение задано некорректно";
	            }
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