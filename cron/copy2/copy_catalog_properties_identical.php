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
	$arData = getSourceIBlockProps(IBLOCK_ID_SOURCE_CATALOG, true);
	
	if (!empty($arData))
	{
		if (!empty($arData["success"]))
		{
			if (!empty($arData["data"]))
			{
				$ibp = new \CIBlockProperty;
				
				$arSource = $arData["data"];
				
				unset($arData);
				
				$arDest = Array();
				
				$res = \CIBlockProperty::GetList(Array("ID" => "ASC",), Array("IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,));
				while ($ar_res = $res->GetNext(true, false))
				{
					if ("L" == $ar_res["PROPERTY_TYPE"])
					{
						$enum_res = \CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arIBlocksMap[$id], "PROPERTY_ID" => $ar_res["ID"]));
						while ($enum_ar_res = $enum_res->GetNext(true, false))
						{
							$ar_res["PROPERTY_ENUM"][$enum_ar_res["ID"]] = $enum_ar_res;
						}
					}
					
					$arDest[$ar_res["ID"]] = $ar_res;
				}
				
				$curItemId = 0;
				
				foreach ($arSource as $sid => $arSItem)
				{
					if ($sid <= $curItemId)
						continue;
					
					$curItemId = $sid;
					
					$bUpdate = false;
					$destPropId = false;
					
					// новое или существующее
					foreach ($arDest as $did => $arDItem)
					{
						// код совпадает - проверка типа и множественности
						if ($arSItem["CODE"] == $arDItem["CODE"])
						{
							// если тип или множественность не совпадают - установить по типтопу
							if ($arSItem["PROPERTY_TYPE"] != $arDItem["PROPERTY_TYPE"] || $arSItem["MULTIPLE"] != $arDItem["MULTIPLE"])
							{
								$bUpdate = true;
								$destPropId = $arDItem["ID"];
								
								$msg = "Destination IBlock #" . IBLOCK_ID_DEST_CATALOG . " has property CODE: " . $arDItem["CODE"] . ", TYPE or MULTIPLE are differ.";
								
								echo "<pre>";
								print_r($msg);
								echo "</pre>";
								
								@LogRestData($msg, "properties_identical");
								
								break 1; // найдено - закончить проверку получателя
							}
							else
							{
								$msg = "Destination IBlock #" . IBLOCK_ID_DEST_CATALOG . " has property CODE: " . $arDItem["CODE"] . ", TYPE and MULTIPLE are indentical.";
								
								echo "<pre>";
								print_r($msg);
								echo "</pre>";
								
								@LogRestData($msg, "properties_identical");
							}
						}
					}
					
					if ($bUpdate)
					{
						$arParams = Array(
								"NAME" => $arSItem["NAME"],
								"ACTIVE" => $arSItem["ACTIVE"],
								"SORT" => $arSItem["SORT"],
								"PROPERTY_TYPE" => $arSItem["PROPERTY_TYPE"],
								"ROW_COUNT" => $arSItem["ROW_COUNT"],
								"COL_COUNT" => $arSItem["COL_COUNT"],
								"LIST_TYPE" => $arSItem["LIST_TYPE"],
								"MULTIPLE" => $arSItem["MULTIPLE"],
								"MULTIPLE_CNT" => $arSItem["MULTIPLE_CNT"],
								"WITH_DESCRIPTION" => $arSItem["WITH_DESCRIPTION"],
								"IS_REQUIRED" => $arSItem["IS_REQUIRED"],
							);
						
						if (!empty($arSItem["USER_TYPE"]))
							$arParams["USER_TYPE"] = $arSItem["USER_TYPE"];
						if (!empty($arSItem["DEFAULT_VALUE"]))
							$arParams["DEFAULT_VALUE"] = $arSItem["DEFAULT_VALUE"];
						
						if ("E" == $arSItem["PROPERTY_TYPE"])
						{
							$arParams["LINK_IBLOCK_ID"] = $arIBlocksMap[$arSItem["LINK_IBLOCK_ID"]];
						}
						elseif ("S" == $arSItem["PROPERTY_TYPE"] && "directory" == $arSItem["USER_TYPE"])
						{
							$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($arHLBlocksMap[$arSItem["USER_TYPE_SETTINGS"]["HLBLOCK_ID"]])->fetch();
							
							$arParams["USER_TYPE_SETTINGS"] = Array(
									"size" => "1",
									"width" => "0",
									"group" => "N",
									"multiple" => "N",
									"TABLE_NAME" => $hlblock["TABLE_NAME"],
								);
						}
						elseif ("L" == $arSItem["PROPERTY_TYPE"] && !empty($arSItem["PROPERTY_ENUM"]))
						{
							$arParams["VALUES"] = Array();
							
							foreach ($arSItem["PROPERTY_ENUM"] as $val)
							{
								$arParams["VALUES"][] = Array(
										"VALUE" => $val["VALUE"],
										"DEF" => $val["DEF"],
										"SORT" => $val["SORT"],
										"XML_ID" => $val["XML_ID"],
										"EXTERNAL_ID" => $val["EXTERNAL_ID"],
									);
							}
						}
						
						echo "<pre>";
						print_r(Array("OPERATION" => "UPDATE PROPERTY", "PARAMS" => $arParams));
						echo "</pre>";
						
						@LogRestData(Array("OPERATION" => "UPDATE PROPERTY", "PARAMS" => $arParams), "properties_identical");
						
						if (!IsTestMode())
							$res = $ibp->Update($destPropId, $arParams);
						else
							$res = true; // ATTENTION для тестового режима
						
						if (!$res)
						{
							echo "<pre>";
							print_r(Array("ERROR" => $ibp->LAST_ERROR,));
							echo "</pre>";
							
							@LogRestData(Array("ERROR" => $ibp->LAST_ERROR,), "properties_identical");
						}
					}
					
				}
			}
		}
	}
	
}
else
{
	LogOutString("Copy is in process.");
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>