<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Service\GeoIp;
use Bitrix\Sale\Location;

Loader::includeModule("sale");

if (!isset($arParams["CACHE_TIME"])) $arParams["CACHE_TIME"] = 36000000;

$arResult = array();

$request = Application::getInstance()->getContext()->getRequest();

$arResult["GEOLOCATION_ID"] = $request->getCookie("GEOLOCATION_ID");
$arResult["IS_BOT"] = !!preg_match("~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", $_SERVER['HTTP_USER_AGENT']);


$cacheKey = serialize(array($arResult["GEOLOCATION_ID"]));
if (!$arResult["GEOLOCATION_ID"] || $this->StartResultCache(false, $cacheKey))
{
    $arResult["GEOLOCATION_INFO"] = false;

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

    $locationData = null;

    // fetch saved
    if (!$locationData && !!$arResult["GEOLOCATION_ID"])
    {
        $locationData = Location\LocationTable::getRow(array(
            "runtime" => $locationRuntime,
            "select" => $locationSelect,
            "filter" => array(
                "ID" => $arResult["GEOLOCATION_ID"],
            ),
        ));
    }

    // fetch geolocation
    if (!$locationData && !$arResult["IS_BOT"])
    {
        $ipUser = GeoIp\Manager::getRealIp();
        $geoResult = GeoIp\Manager::getDataResult($ipUser, "ru", array('countryName', 'regionName', 'cityName'));

        if ($geoResult && $geoResult->isSuccess())
        {
            $arResultGeoIP = $geoResult->getGeoData();

            $locationData = Location\LocationTable::getRow(array(
                "runtime" => $locationRuntime,
                "select" => $locationSelect,
                "filter" => array(
                    "COUNTRY.NAME" => $arResultGeoIP->countryName,
                    "REGION.NAME" => $arResultGeoIP->regionName,
                    "CITY.NAME" => $arResultGeoIP->cityName,
                ),
            ));
        }
    }

    // fetch default
    if (!$locationData || !$locationData['CITY_NAME'])
    {
        $locationData = Location\LocationTable::getRow(array(
            "runtime" => $locationRuntime,
            "select" => $locationSelect,
            "filter" => array(
                "ID" => $arParams["DEFAULT_GEOLOCATION_ID"],
            ),
        ));
    }

    if (!!$locationData)
    {
        $arResult["GEOLOCATION_INFO"] = array(
            "ID" => $locationData["ID"],
            "COUNTRY_NAME" => $locationData["COUNTRY_NAME"],
            "REGION_NAME" => $locationData["REGION_NAME"],
            "CITY_NAME" => $locationData["CITY_NAME"],
        );
    }

    $this->IncludeComponentTemplate();
}