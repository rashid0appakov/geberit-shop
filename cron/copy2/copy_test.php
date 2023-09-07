<?php
set_time_limit(600);
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("STOP_STATISTICS", true);
define("NO_AGENT_CHECK", true);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

// maxExecutionTime из настроек PHP
global $phpMaxExecutionTime;
$phpMaxExecutionTime = intval(ini_get("max_execution_time"));

// время начала итерации
global $start;
$start = microtime(true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::IncludeModule("iblock");
\Bitrix\Main\Loader::IncludeModule("highloadblock");
\Bitrix\Main\Loader::IncludeModule("catalog");
//use SimpleXMLElement;

// ----------------------------------------------

require(__DIR__."/copy_defines.php");
require(__DIR__."/copy_data.php");
require(__DIR__."/copy_functions.php");

// ----------------------------------------------

// логика пошаговой обработки
if (!IsProcessOn())
{
	$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_ibdata&iblock_id=" . IBLOCK_ID_SOURCE_CATALOG . "&limit=50" . 
		"&" . http_build_query(Array("filter" => Array("PROPERTY_MANUFACTURER" => SOURCE_MANUFACTURER_ID)));
	
	/*$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_ibprops&iblock_id=" . IBLOCK_ID_SOURCE_CATALOG . 
		"&category=" . IBLOCK_SOURCE_CATALOG_PROPERTY_CATEGORY;*/
	
	echo "<pre>";
	print_r($url);
	echo "</pre>";
	
	$opts = Array(
			"http" => Array(
				"method" => "POST",
			)
		);
	$context = stream_context_create($opts);
	
	$response = file_get_contents($url, false, $context);
	$result = json_decode($response, true);
	
	
	// FillSectionsMap();
	
	
	echo "<pre>";
	print_r($result);
	echo "</pre>";
}
else
{
	// если стоит больше максимального времени исполнения - продолжить принудительно
	if (ForceProcess())
		LogOutString("Copy will be continued on next run.");
	else
		LogOutString("Copy is in process.");
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>