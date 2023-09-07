<?php

// ШАГ 5 - свойства iblocks

@ProcessOn();

$bProcess = false;
$bFinished = true;

// ATTENTION если не удалось создать нужное свойство - отмена копирования!!!
$bFatal = false;

clearstatcache();

// начало или продолжение
if (!isset($arStepParams["CURRENT_IBLOCK_INDEX"]) || intval($arStepParams["CURRENT_IBLOCK_INDEX"]) < 0)
{
	@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => 0,));
	
	$bProcess = true;
	$bFinished = false;
	
	@LogProcessInfo("Iblocks properties settings started.", true);
}

$curIndex = intval($arStepParams["CURRENT_IBLOCK_INDEX"]);

// если всё было получено на предыдущей итерации
if (!array_key_exists($curIndex, $arSourceIBlockList))
	$bProcess = false;
else
	$bProcess = true;

if ($bProcess)
{
	$ITERATION = date("His");
	
	foreach ($arSourceIBlockList as $i => $id)
	{
		// пропускаем обработанные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
		
		$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_PROPERTIES.$id);
		
		if (!empty($arSource))
			$arSource = unserialize($arSource);
		
		if (!empty($arSource))
		{
			$ibp = new \CIBlockProperty;
			
			// начало обхода элементов или продолжение
			if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
			{
				@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
			}
			
			$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
			
			// свойства iblock получателя
			$arDest = Array();
			
			$res = \CIBlockProperty::GetList(Array("ID" => "ASC",), Array("IBLOCK_ID" => $arIBlocksMap[$id],));
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
			
			$bItem = false; // флаг обработки свойства
			
			// свойства источник-получатель
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				$bItem = true;
				$curItemId = $sid;
				
				$bNew = true; // флаг нового свойства
				$newCode = $arSItem["CODE"]; // код нового свойства
				$destPropId = false; // id существующего свойства
				
				// новое или существующее
				foreach ($arDest as $did => $arDItem)
				{
					// код совпадает - проверка типа и множественности
					if ($arSItem["CODE"] == $arDItem["CODE"])
					{
						// тип совпадает, множественность получателя >= множественности источника = существующее
						if ($arSItem["PROPERTY_TYPE"] == $arDItem["PROPERTY_TYPE"] && 
								($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"] || "Y" == $arDItem["MULTIPLE"]))
						{
							$bNew = false;
							$destPropId = $arDItem["ID"];
							
							if ($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"])
								$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " has property CODE: " . $arDItem["CODE"] . ".";
							else
								$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " has property CODE: " . $arDItem["CODE"] . " differ MULTIPLE."; // логируем несовпадение множественности
							
							@LogProcessInfo($msg);
							
							break 1; // найдено - закончить проверку получателя
						}
						else
						{
							// тип и множественность не совпали - проверяем доп.вариант
							$secondPropCode = $arSItem["CODE"] . IBLOCK_PROPERTY_SOURCE_SUFFIX;
							$newCode = $secondPropCode;
							
							foreach ($arDest as $secondId => $arSecondItem)
							{
								// код совпадает - проверка типа и множественности
								if ($secondPropCode == $arSecondItem["CODE"])
								{
									// тип совпадает, множественность получателя >= множественности источника = существующее
									if ($arSItem["PROPERTY_TYPE"] == $arSecondItem["PROPERTY_TYPE"] && 
											($arSItem["MULTIPLE"] == $arSecondItem["MULTIPLE"] || "Y" == $arSecondItem["MULTIPLE"]))
									{
										$bNew = false;
										$destPropId = $arSecondItem["ID"];
										
										if ($arSItem["MULTIPLE"] == $arSecondItem["MULTIPLE"])
											$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " has property CODE: " . $secondPropCode . ".";
										else
											$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " has property CODE: " . $secondPropCode . " differ MULTIPLE."; // логируем несовпадение множественности
										
										@LogProcessInfo($msg);
									}
									else
									{
										// проблема - в iblock получателя есть и свойство с таким же кодом и свойство с кодом доп.варианта
										// при этом тип и множественность не совпадают - значения свойства при копировании элементов будут проигнорированы
										$bNew = false;
										
										$err = "Destination IBlock #" . $arIBlocksMap[$id] . " has property CODE: " . $arDItem["CODE"] . " and property with second CODE: " . $secondPropCode . 
												" but differ TYPE and/or MULTIPLE. Future values will be ignored.";
										
										LogProcessError($err);
									}
								}
								
								// найдено - закончить проверку получателя
								if (!$bNew)
									break 2;
							}
							
						}
						
					}
					
				}
				
				// создание нового свойства
				if ($bNew)
				{
					// проверка наличия iblock если привязка
					if ("E" == $arSItem["PROPERTY_TYPE"] && empty($arIBlocksMap[$arSItem["LINK_IBLOCK_ID"]]))
					{
						$bNew = false;
						
						$err = "No IBlock in destination for source IBlock #" . $arSItem["LINK_IBLOCK_ID"] . " with property CODE: " . $arSItem["CODE"] . 
								". Can't create property. Future values will be ignored.";
						
						LogProcessError($err);
					}
					elseif ("S" == $arSItem["PROPERTY_TYPE"] && "directory" == $arSItem["USER_TYPE"] && empty($arHLBlocksMap[$arSItem["USER_TYPE_SETTINGS"]["HLBLOCK_ID"]]))
					{
						// проверка наличия hlblock если привязка
						$bNew = false;
						
						$err = "No Highloadblock in destination for source Highloadblock #" . $arSItem["USER_TYPE_SETTINGS"]["HLBLOCK_ID"] . " with property CODE: " . $arSItem["CODE"] . 
								". Can't create property. Future values will be ignored.";
						
						LogProcessError($err);
					}
					
					if ($bNew)
					{
						$arParams = Array(
								"IBLOCK_ID" => $arIBlocksMap[$id],
								"NAME" => $arSItem["NAME"],
								"ACTIVE" => $arSItem["ACTIVE"],
								"SORT" => $arSItem["SORT"],
								"CODE" => $newCode,
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
						
						if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
							LogProcessData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new iblock property",
									"iblock" => $arIBlocksMap[$id],
									"data" => $arParams,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new iblock property",
									"iblock" => $arIBlocksMap[$id],
									"data" => $arParams,
							), $ITERATION);
							
						if (!IsTestMode())
							$newId = $ibp->Add($arParams);
						else
							$newId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
						
						if (!empty($newId))
						{
							$arParams["ID"] = $newId;
							
							if (isset($arParams["VALUES"]))
							{
								$arParams["PROPERTY_ENUM"] = $arParams["VALUES"];
								
								unset($arParams["VALUES"]);
							}
							
							$arDest[$newId] = $arParams;
							
							$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " new property CODE: " . $newCode . ".";
							@LogProcessInfo($msg);
						}
						else
						{
							$err = "Destination IBlock #" . $arIBlocksMap[$id] . " can't create property CODE: " . $newCode . ", error: " . $ibp->LAST_ERROR;
							
							LogProcessError($err);
							
							// ATTENTION фатальная ошибка!!!
							$bFatal = true;
						}
					}
				}
				elseif (!empty($destPropId) && "L" == $arSItem["PROPERTY_TYPE"] && !empty($arSItem["PROPERTY_ENUM"]))
				{
					$ibpenum = new \CIBlockPropertyEnum;
					
					// варианты списочного свойства
					$arDestValues = Array();
					
					$enum_res = \CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arIBlocksMap[$id], "PROPERTY_ID" => $destPropId));
					while ($enum_ar_res = $enum_res->GetNext(true, false))
					{
						$arDestValues[$enum_ar_res["ID"]] = $enum_ar_res;
					}
					
					$arUpdate = Array();
					$arNew = Array();
					
					foreach ($arSItem["PROPERTY_ENUM"] as $val)
					{
						$sourceKey = MakeKey(htmlspecialcharsBack($val["VALUE"]));
						
						$bNewEnum = true;
						
						foreach ($arDestValues as $destVal)
						{
							$destKey = MakeKey(htmlspecialcharsBack($destVal["VALUE"]));
							
							// наличие варианта по XML_ID - обновить если изменилось
							if (!empty($val["XML_ID"]) && !empty($destVal["XML_ID"]) && $val["XML_ID"] == $destVal["XML_ID"])
							{
								$bNewEnum = false;
								
								if ($sourceKey != $destKey)
								{
									$arUpdate[$destVal["ID"]] = Array("VALUE" => $val["VALUE"]);
								}
							}
							elseif ($sourceKey == $destKey)
							{
								$bNewEnum = false;
							}
							
							if (!$bNewEnum)
								break 1;
						}
						
						if ($bNewEnum)
							$arNew[$val["ID"]] = $val;
					}
					
					if (!empty($arNew))
					{
						if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
							LogProcessData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new iblock enum property",
									"iblock" => $arIBlocksMap[$id],
									"propertyId" => $destPropId,
									"propertyCode" => $arDest[$destPropId]["CODE"],
									"data" => $arNew,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "update iblock enum property",
									"iblock" => $arIBlocksMap[$id],
									"propertyId" => $destPropId,
									"propertyCode" => $arDest[$destPropId]["CODE"],
									"data" => $arNew,
							), $ITERATION);
							
						if (!IsTestMode())
						{
							foreach ($arNew as $k => $val)
							{
								$newId = $ibpenum->Add(Array(
										"PROPERTY_ID" => $destPropId,
										"VALUE" => $val["VALUE"],
										"DEF" => $val["DEF"],
										"SORT" => $val["SORT"],
										"XML_ID" => $val["XML_ID"],
										"EXTERNAL_ID" => $val["EXTERNAL_ID"],
									));
								
								if (!empty($newId))
								{
									$val["ID"] = $newId;
									
									$arDest[$destPropId]["PROPERTY_ENUM"][$newId] = $val;
								}
								else
									unset($arNew[$k]);
							}
						}
						else
						{
							foreach ($arNew as $val)
							{
								$arDest[$destPropId]["PROPERTY_ENUM"]["test_".$val["ID"]] = $val;
							}
						}
					}
					
					if (!empty($arUpdate))
					{
						if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
							LogProcessData(
								Array(
									"time" => date("H:i:s"),
									"action" => "update iblock enum property",
									"iblock" => $arIBlocksMap[$id],
									"propertyId" => $destPropId,
									"propertyCode" => $arDest[$destPropId]["CODE"],
									"data" => $arUpdate,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "update iblock enum property",
									"iblock" => $arIBlocksMap[$id],
									"propertyId" => $destPropId,
									"propertyCode" => $arDest[$destPropId]["CODE"],
									"data" => $arUpdate,
							), $ITERATION);
							
						if (!IsTestMode())
						{
							foreach ($arUpdate as $k => $val)
							{
								$newId = $ibpenum->Update($k, $val);
								
								if (!empty($newId))
								{
									$arDest[$destPropId]["PROPERTY_ENUM"][$k]["VALUE"] = $val["VALUE"];
								}
								else
									unset($arUpdate[$k]);
							}
						}
						else
						{
							foreach ($arUpdate as $k => $val)
							{
								$arDest[$destPropId]["PROPERTY_ENUM"][$k]["VALUE"] = $val["VALUE"];
							}
						}
						
						$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " updated property CODE: " . $arDest[$destPropId]["CODE"] . ".";
						@LogProcessInfo($msg);
					}
				}
				elseif (!empty($destPropId))
				{
					// свойство существует
					// TODO если нужны какие-то другие действия для не списочных свойств
				}
				
				@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
				
				// проверка продолжения по времени/свойствам
				$bProcess = !IsTimeout();
				
				if (!$bProcess)
					break 1;
			}
			
			// свойства источника пройдены полностью
			if (!$bItem)
			{
				@SetStepParams(Array("CURRENT_ITEM_ID" => false,));
			}
			else
			{
				$arLast = end($arSource);
				
				if ($arLast["ID"] == $curItemId)
					@SetStepParams(Array("CURRENT_ITEM_ID" => false,));
			}
		}
		
		// продолжать итерацию
		$bProcess = !IsTimeout();
		
		// итерация закончена или свойства пройдены
		if (!$bProcess || !$bItem)
		{
			// следующий индекс или финиш
			$bNext = false;
			$bFound = false;
			foreach ($arSourceIBlockList as $j => $tmp)
			{
				if (!$bNext && $j == $curIndex)
				{
					$bNext = true;
				}
				elseif ($bNext)
				{
					$curIndex = $j;
					$bFound = true;
					$bNext = false;
					
					break 1;
				}
			}
			
			// список iblocks пройден до конца или нет
			if ($bFound)
			{
				@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex, "CURRENT_ITEM_ID" => false,));
			}
			else
			{
				$bFinished = true;
				
				// список iblocks пройден - на всякий случай
				$curIndex = PHP_INT_MAX;
				@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex, "CURRENT_ITEM_ID" => false,));
			}
			
			break 1;
		}
		else
		{
			$bFinished = true; // если последний элемент - перебор завершится с выставленным флагом
		}
		
		unset($arSource, $arDest);
	}
	
}

unset($arSource, $arDest);

// фатальная ошибка
if ($bFatal)
{
	AbortProcess("Fatal error: can't create required property(ies)");
}
elseif ($bFinished) // настройка свойств iblocks закончена
{
	$arStepParams = Array("STEP" => 6, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblocks properties settings finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>