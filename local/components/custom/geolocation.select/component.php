<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Application;
use Bitrix\Main\Service\GeoIp;

if(!isset($arParams["CACHE_TIME"])) $arParams["CACHE_TIME"] = 36000000;

$arResult = array();

$cacheKey = "";
if ($this->StartResultCache(false, $cacheKey))
{
    $arResult["DEFAULT_CITY"] = array();

    if (!empty($arParams["DEFAULT_CITY"]))
    {
        $arLocations = array();
        $dbLocation = \Bitrix\Sale\Location\LocationTable::getList(array(
            "filter" => array(
                "ID" => $arParams["DEFAULT_CITY"]
            ),
        ));
        while ($arLocation = $dbLocation->fetch())
        {
            $arLocations[] = $arLocation;
        }

        $dbLocation = \Bitrix\Sale\Location\LocationTable::getPathToMultipleNodes(
            $arLocations, 
            array(
                'select' => array('ID', 'LNAME' => 'NAME.NAME'),
                'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
            )
        );
		$ar1 = $ar2 = $ar3 = [];
        while ($arLocation = $dbLocation->fetch())
        {
            $id = $arLocation["ID"];
            $name = reset($arLocation["PATH"])["LNAME"];
            $path = array_column($arLocation["PATH"], "LNAME");

			$city = array(
                "ID" => $id,
                "NAME" => $name,
                "PATH" => implode(", ", $path),
            );
			if(in_array($id, $arParams["STORE_CITY"]))
			{
				$ar1[$id] = $city;
			}
			else
			{
				$ar2[$name] = $city;
			}
        }
        foreach($arParams['STORE_CITY'] as $id)
		{
			$ar3[$id] = $ar1[$id];
		}
        ksort($ar2);
		$arResult["DEFAULT_CITY"] = array_values($ar3 + $ar2);
		$ar1 = $ar2 = [];
		


		//pr($arResult["DEFAULT_CITY"]);
    }

    $this->IncludeComponentTemplate();
}