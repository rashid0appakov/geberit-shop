<?php

// ШАГ 4 - поля hlblocks источник/получатель

@ProcessOn();

$bProcess = false;
$bFinished = true;

// ATTENTION если не удалось создать нужное поле - отмена копирования!!!
$bFatal = false;

clearstatcache();

// начало или продолжение
if (!isset($arStepParams["CURRENT_HLBLOCK_INDEX"]) || intval($arStepParams["CURRENT_HLBLOCK_INDEX"]) < 0)
{
	@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => 0,));
	
	$bProcess = true;
	$bFinished = false;
	
	@LogProcessInfo("Hlblocks fields settings started.", true);
}

$curIndex = intval($arStepParams["CURRENT_HLBLOCK_INDEX"]);

// если всё было получено на предыдущей итерации
if (!array_key_exists($curIndex, $arSourceHLBlockList))
	$bProcess = false;
else
	$bProcess = true;

if ($bProcess)
{
	$ITERATION = date("His");
	
	foreach ($arSourceHLBlockList as $i => $id)
	{
		// пропускаем обработанные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex,));
		
		$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_FIELDS.$id);
		
		if (!empty($arSource))
			$arSource = unserialize($arSource);
		
		if (!empty($arSource))
		{
			$hlf = new \CUserTypeEntity();
			$hlfenum = new \CUserFieldEnum();
			
			// начало обхода элементов или продолжение
			if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
			{
				@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
			}
			
			$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
			
			// поля hlblock получателя
			$arDest = Array();
			
			$res = \CUserTypeEntity::GetList(Array(($by = "ID") => ($order = "ASC")), Array("ENTITY_ID" => "HLBLOCK_".$arHLBlocksMap[$id]));
			while ($ar_res = $res->Fetch())
			{
				$arDest[$ar_res["ID"]] = $ar_res;
			}
			
			$bItem = false; // флаг обработки поля
			
			// поля источник-получатель
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				$bItem = true;
				$curItemId = $sid;
				
				$bNew = true; // флаг нового поля
				$newCode = $arSItem["FIELD_NAME"]; // код нового поля
				$destFieldId = false; // id существующего поля
				
				// новое или существующее
				foreach ($arDest as $did => $arDItem)
				{
					// код совпадает - проверка типа и множественности
					if ($arSItem["FIELD_NAME"] == $arDItem["FIELD_NAME"])
					{
						// тип совпадает, множественность получателя >= множественности источника = существующее
						if ($arSItem["USER_TYPE_ID"] == $arDItem["USER_TYPE_ID"] && 
								($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"] || "Y" == $arDItem["MULTIPLE"]))
						{
							$bNew = false;
							$destFieldId = $arDItem["ID"];
							
							if ($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"])
								$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " has field CODE: " . $arDItem["FIELD_NAME"] . ".";
							else
								$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " has field CODE: " . $arDItem["FIELD_NAME"] . " differ MULTIPLE."; // логируем несовпадение множественности
							
							@LogProcessInfo($msg);
							
							break 1; // найдено - закончить проверку получателя
						}
						else
						{
							// тип и множественность не совпали - проверяем доп.вариант
							$secondFieldCode = $arSItem["FIELD_NAME"] . HLBLOCK_FIELD_SOURCE_SUFFIX;
							$newCode = $secondFieldCode;
							
							foreach ($arDest as $secondId => $arSecondItem)
							{
								// код совпадает - проверка типа и множественности
								if ($secondFieldCode == $arSecondItem["FIELD_NAME"])
								{
									// тип совпадает, множественность получателя >= множественности источника = существующее
									if ($arSItem["USER_TYPE_ID"] == $arSecondItem["USER_TYPE_ID"] && 
											($arSItem["MULTIPLE"] == $arSecondItem["MULTIPLE"] || "Y" == $arSecondItem["MULTIPLE"]))
									{
										$bNew = false;
										$destFieldId = $arSecondItem["ID"];
										
										if ($arSItem["MULTIPLE"] == $arSecondItem["MULTIPLE"])
											$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " has field CODE: " . $secondFieldCode . ".";
										else
											$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " has field CODE: " . $secondFieldCode . " differ MULTIPLE."; // логируем несовпадение множественности
										
										@LogProcessInfo($msg);
									}
									else
									{
										// проблема - в hlblock получателя есть и поле с таким же кодом и поле с кодом доп.варианта
										// при этом тип и множественность не совпадают - значения поля при копировании элементов будут проигнорированы
										$bNew = false;
										
										$err = "Destination HLBlock #" . $arHLBlocksMap[$id] . " has field CODE: " . $arDItem["FIELD_NAME"] . " and field with second CODE: " . $secondFieldCode . 
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
				
				// создание нового поля
				if ($bNew)
				{
					// проверка наличия iblock если привязка
					if (("iblock_section" == $arSItem["USER_TYPE_ID"] || "iblock_element" == $arSItem["USER_TYPE_ID"]) && empty($arIBlocksMap[$arSItem["SETTINGS"]["IBLOCK_ID"]]))
					{
						$bNew = false;
						
						$err = "No IBlock in destination for source IBlock #" . $arSItem["SETTINGS"]["IBLOCK_ID"] . " with field CODE: " . $arSItem["FIELD_NAME"] . 
								". Can't create field. Future values will be ignored.";
						
						LogProcessError($err);
					}
					elseif ("hlblock" == $arSItem["USER_TYPE_ID"] && empty($arHLBlocksMap[$arSItem["SETTINGS"]["HLBLOCK_ID"]]))
					{
						// проверка наличия hlblock если привязка
						$bNew = false;
						
						$err = "No Highloadblock in destination for source Highloadblock #" . $arSItem["SETTINGS"]["HLBLOCK_ID"] . " with field CODE: " . $arSItem["FIELD_NAME"] . 
								". Can't create field. Future values will be ignored.";
						
						LogProcessError($err);
					}
					
					if ($bNew)
					{
						$arParams = Array(
							"ENTITY_ID" => "HLBLOCK_" . $arHLBlocksMap[$id],
							"FIELD_NAME" => $newCode,
							"USER_TYPE_ID" => $arSItem["USER_TYPE_ID"],
							"XML_ID" => $arSItem["XML_ID"],
							"SORT" => $arSItem["SORT"],
							"MULTIPLE" => $arSItem["MULTIPLE"],
						);
						
						if ("hlblock" == $arSItem["USER_TYPE_ID"])
						{
							$arParams["SETTINGS"]["DISPLAY"] = $arSItem["SETTINGS"]["DISPLAY"];
							$arParams["SETTINGS"]["LIST_HEIGHT"] = $arSItem["SETTINGS"]["LIST_HEIGHT"];
							$arParams["SETTINGS"]["HLBLOCK_ID"] = $arHLBlocksMap[$arSItem["SETTINGS"]["HLBLOCK_ID"]];
						}
						elseif ("iblock_section" == $arSItem["USER_TYPE_ID"] || "iblock_element" == $arSItem["USER_TYPE_ID"])
						{
							$arParams["SETTINGS"]["DISPLAY"] = $arSItem["SETTINGS"]["DISPLAY"];
							$arParams["SETTINGS"]["LIST_HEIGHT"] = $arSItem["SETTINGS"]["LIST_HEIGHT"];
							$arParams["SETTINGS"]["IBLOCK_ID"] = $arIBlocksMap[$arSItem["SETTINGS"]["IBLOCK_ID"]];
						}
						elseif ("video" == $arSItem["USER_TYPE_ID"])
						{
							$arParams["SETTINGS"] = $arSItem["SETTINGS"];
						}
						elseif ("enumeration" == $arSItem["USER_TYPE_ID"])
						{
							$arParams["SETTINGS"] = $arSItem["SETTINGS"];
						}
						else
						{
							$arParams["SETTINGS"] = $arSItem["SETTINGS"];
						}
						
						if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
							LogProcessData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new hlblock field",
									"hlblock" => $arHLBlocksMap[$id],
									"data" => $arParams,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new hlblock field",
									"hlblock" => $arHLBlocksMap[$id],
									"data" => $arParams,
							), $ITERATION);
							
						if (!IsTestMode())
							$newId = $hlf->Add($arParams);
						else
							$newId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
						
						if (!empty($newId))
						{
							$arParams["ID"] = $newId;
							
							// если списочное
							if ("enumeration" == $arSItem["USER_TYPE_ID"] && !empty($arSItem["FIELD_ENUM"]))
							{
								foreach ($arSItem["FIELD_ENUM"] as $e => $arEnum)
								{
									$arValue = Array(
												"VALUE" => $arEnum["VALUE"],
												"DEF" => $arEnum["DEF"],
												"SORT" => $arEnum["SORT"],
												"XML_ID" => $arEnum["XML_ID"],
											);
									
									if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
										LogProcessData(
											Array(
												"time" => date("H:i:s"),
												"action" => "new hlblock field list value",
												"hlblock" => $arHLBlocksMap[$id],
												"fieldId" => $newId,
												"fieldCode" => $newCode,
												"data" => $arValue,
										), $ITERATION);
									if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
										LogDebugData(
											Array(
												"time" => date("H:i:s"),
												"action" => "new hlblock field list value",
												"hlblock" => $arHLBlocksMap[$id],
												"fieldId" => $newId,
												"fieldCode" => $newCode,
												"data" => $arValue,
										), $ITERATION);
									
									if (!IsTestMode())
									{
										$newEnId = $hlfenum->Add($newId, $arValue);
									}
									else
									{
										$newEnId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
									}
									
									if (!empty($newEnId))
									{
										$arSItem["FIELD_ENUM"][$e]["ID"] = $newEnId;
										$arSItem["FIELD_ENUM"][$e]["USER_FIELD_ID"] = $newId;
									}
									else
									{
										// ошибка при создании списочного пользовательского значения
										
										$err = "Error for destination Highloadblock #" . $arHLBlocksMap[$id] . " with field CODE: " . $newCode . 
												". Can't create list value: " . $hlfenum->LAST_ERROR;
										
										LogProcessError($err);
									}
								}
								
								$arParams["FIELD_ENUM"] = $arSItem["FIELD_ENUM"];
							}
							
							$arDest[$newId] = $arParams;
							
							$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " new field CODE: " . $newCode . ".";
							@LogProcessInfo($msg);
						}
						else
						{
							$err = "Destination HLBlock #" . $arHLBlocksMap[$id] . " can't create field CODE: " . $newCode . ", error: " . $hlf->LAST_ERROR;
							
							LogProcessError($err);
							
							// ATTENTION фатальная ошибка!!!
							$bFatal = true;
						}
					}
					
				}
				elseif (!empty($destFieldId) && "enumeration" == $arSItem["USER_TYPE_ID"] && !empty($arSItem["FIELD_ENUM"]))
				{
					// варианты списочного поля
					$arDestValues = Array();
					
					$enum_res = \CUserFieldEnum::GetList(Array(($eby = "ID") => ($eorder = "ASC")), Array("USER_FIELD_ID" => $destFieldId));
					while ($enum_ar_res = $enum_res->GetNext(true, false))
					{
						$arDestValues[$enum_ar_res["ID"]] = $enum_ar_res;
					}
					
					$arUpdate = Array();
					$n = 0;
					
					foreach ($arSItem["FIELD_ENUM"] as $val)
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
							elseif ($sourceKey == $destKey) // наличие варианта по значению
							{
								$bNewEnum = false;
							}
							
							if (!$bNewEnum)
								break 1;
						}
						
						if ($bNewEnum)
						{
							$arUpdate["n".$n] = Array("VALUE" => $val["VALUE"]);
							$n++;
						}
					}
					
					if (!empty($arUpdate))
					{
						if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
							LogProcessData(
								Array(
									"time" => date("H:i:s"),
									"action" => "update hlblock list field",
									"hlblock" => $arHLBlocksMap[$id],
									"fieldId" => $destFieldId,
									"fieldCode" => $arDest[$destFieldId]["FIELD_NAME"],
									"data" => $arUpdate,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "update hlblock list field",
									"hlblock" => $arHLBlocksMap[$id],
									"fieldId" => $destFieldId,
									"fieldCode" => $arDest[$destFieldId]["FIELD_NAME"],
									"data" => $arUpdate,
							), $ITERATION);
							
						if (!IsTestMode())
						{
							$hlfenum->SetEnumValues($destFieldId, $arUpdate);
							
							foreach ($arUpdate as $n => $val)
							{
								$arDest[$destFieldId]["FIELD_ENUM"][$n] = $val;
							}
						}
						else
						{
							foreach ($arUpdate as $n => $val)
							{
								$arDest[$destFieldId]["FIELD_ENUM"]["test_".$n] = $val;
							}
						}
						
						$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " updated field CODE: " . $arDest[$destFieldId]["FIELD_NAME"] . ".";
						@LogProcessInfo($msg);
					}
				}
				elseif (!empty($destFieldId))
				{
					// поле существует
					// TODO если нужны какие-то другие действия для не списочных полей
				}
				
				@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
				
				// проверка продолжения по времени/полям
				$bProcess = !IsTimeout();
				
				if (!$bProcess)
					break 1;
			}
			
			// поля источника пройдены полностью
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
		
		// итерация закончена или поля пройдены
		if (!$bProcess || !$bItem)
		{
			// следующий индекс или финиш
			$bNext = false;
			$bFound = false;
			foreach ($arSourceHLBlockList as $j => $tmp)
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
			
			// список hlblocks пройден до конца или нет
			if ($bFound)
			{
				@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex, "CURRENT_ITEM_ID" => false,));
			}
			else
			{
				$bFinished = true;
				
				// список hlblocks пройден - на всякий случай
				$curIndex = PHP_INT_MAX;
				@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex, "CURRENT_ITEM_ID" => false,));
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
	AbortProcess("Fatal error: can't create required field(s)");
}
elseif ($bFinished) // настройка полей hlblocks закончена
{
	$arStepParams = Array("STEP" => 5, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Hlblocks fields settings finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>