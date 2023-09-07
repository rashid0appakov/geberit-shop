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

require(__DIR__."/copy_defines.php");
require(__DIR__."/copy_data.php");
require(__DIR__."/copy_functions.php");

// ----------------------------------------------

// структура

foreach ($arSourceIBlockStructureList as $id)
{
	@SourceCatalogSections2Dest($id);
	
	echo "DONE #$id\r\n\r\n";
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>