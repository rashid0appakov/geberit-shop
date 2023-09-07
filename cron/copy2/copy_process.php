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

// логика пошаговой обработки
if (!IsProcessOn())
{
	$arStepParams = GetStepParams();
	
	// начало обработки без инициализации
	// NOTE включить, если планируется запуск по кругу
	/*
	if (empty($arStepParams) || !is_array($arStepParams) || !isset($arStepParams["STEP"]))
	{
		if (!is_array($arStepParams))
			$arStepParams = Array();
		
		@SetStepParams(Array("STEP" => 1, "PROCESS" => true,));
	}
	*/
	
	if (isset($arStepParams["PROCESS"]) && !empty($arStepParams["PROCESS"]))
	{
		if (!empty($arStepParams["STEP"]) && file_exists(__DIR__."/copy_step{$arStepParams["STEP"]}.php"))
		{
			LogOutString("Copy step #" . $arStepParams["STEP"]);
			
			require(__DIR__."/copy_step{$arStepParams["STEP"]}.php");
		}
		else
		{
			// сбой, всё сбросить и завершить
			AbortProcess("Copy error: wrong step.");
		}
	}
	else
	{
		LogOutString("Copy finished, nothing to run.");
	}
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