<?php

// ШАГ 9 - данные hlblocks источник/получатель

@ProcessOn();

$bProcess = false;
$bFinished = true;

clearstatcache();

// начало или продолжение
if (!isset($arStepParams["CURRENT_HLBLOCK_INDEX"]) || intval($arStepParams["CURRENT_HLBLOCK_INDEX"]) < 0)
{
	@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => 0,));
	
	$bProcess = true;
	$bFinished = false;
	
	@LogProcessInfo("Hlblocks data recieving started.", true);
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
	
	if (file_exists(__DIR__."/".FILE_SOURCE_HLBLOCK_MAP))
	{
		$map = file_get_contents(__DIR__."/".FILE_SOURCE_HLBLOCK_MAP);
		
		if (!empty($map))
			$arHLBlocksFieldMap = unserialize($map);
		else
			$bProcess = false;
	}
	else
		$bProcess = false;
	
	// без карты полей копирование невозможно
	if (!$bProcess)
	{
		$err = "Unknown error for hlblocks fields map. Hlblocks copy impossible.";
		
		LogOutString($err);
		LogProcessError($err, true);
	}
}

if ($bProcess)
{
	foreach ($arSourceHLBlockList as $i => $id)
	{
		// пропускаем обработанные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex,));
		
		// счётчики
		$countNew = 0;
		$countUpdate = 0;
		
		// начало обхода элементов или продолжение
		if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
		{
			@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
		}
		
		$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
		
		$fhLinked = fopen(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_LINKED.$id, "a");
		
		// зацикливаем пока есть время и есть что обрабатывать
		do {
			$bItem = false; // флаг обработки данных
			
			$arSource = getStepDataHLBlock($id, $curItemId);
			
			if (!empty($arSource))
			{
				// данные получателя
				$fieldCodeId = HLBLOCK_FIELD_SOURCE_ELEMENT_ID . HLBLOCK_FIELD_SOURCE_SUFFIX;
				$fieldCodeLinkId = HLBLOCK_FIELD_SOURCE_ELEMENT_ID_LINK . HLBLOCK_FIELD_SOURCE_SUFFIX;
				$fieldCodeHash = HLBLOCK_FIELD_HASH_DATA . HLBLOCK_FIELD_SOURCE_SUFFIX;
				$DC = GetEntityDataClass($arHLBlocksMap[$id]);
				$arSourceID = array_keys($arSource);
				$arSourceXML_ID = Array();
				foreach ($arSource as $sid => $arSItem)
				{
					if (!empty($arSItem["UF_XML_ID"]))
						$arSourceXML_ID[] = $arSItem["UF_XML_ID"];
				}
				$arSourceXML_ID = array_unique($arSourceXML_ID);
				$arDest = Array();
				
				if (!empty($arSourceXML_ID))
					$arHLFilter = Array("LOGIC" => "OR", Array($fieldCodeId => $arSourceID), Array("=UF_XML_ID" => $arSourceXML_ID));
				else
					$arHLFilter = Array($fieldCodeId => $arSourceID);
				
				$res = $DC::getList(Array(
					"select" => Array("ID", $fieldCodeId, $fieldCodeHash, "UF_XML_ID",),
					"order" => Array("ID" => "ASC"),
					"filter" => $arHLFilter,
				));
				while ($ar_res = $res->fetch())
				{
					if (!empty($ar_res[$fieldCodeId]) && in_array($ar_res[$fieldCodeId], $arSourceID))
						$destKey = $ar_res[$fieldCodeId];
					else
						$destKey = $ar_res["UF_XML_ID"];
					
					$arDest[$destKey] = Array("ID" => $ar_res["ID"], "HASH" => $ar_res[$fieldCodeHash],);
				}
				
				unset($arSourceID, $arSourceXML_ID);
				
				// данные источника
				foreach ($arSource as $sid => $arSItem)
				{
					// пропускаем обработанные
					if ($sid <= $curItemId)
						continue;
					
					$bItem = true;
					$curItemId = $sid;
					
					$arData = Array();
					$bLinked = false;
					$bSave = false;
					
					// формируем данные
					foreach ($arSItem as $fieldCode => $fieldValue)
					{
						if (isset($arHLBlocksFieldMap[$id][$fieldCode]))
						{
							$arField = $arHLBlocksFieldMap[$id][$fieldCode];
							
							if ("hlblock" == $arField["TYPE"] || "iblock_section" == $arField["TYPE"] || "iblock_element" == $arField["TYPE"])
								$bLinked = true;
							
							$val = $fieldValue;
							
							// списочные значения конвертируются
							if ("enumeration" == $arField["TYPE"])
							{
								if (is_array($val))
								{
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (isset($arField["LIST"][$vv]))
											$tmp[] = $arField["LIST"][$vv];
									}
									
									if (!empty($tmp))
										$val = $tmp;
									else
										$val = false;
								}
								else
								{
									if (isset($arField["LIST"][$val]))
										$val = $arField["LIST"][$val];
									else
										$val = false;
								}
							}
							elseif ("file" == $arField["TYPE"])
							{
								if (is_array($val))
								{
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (!empty($vv))
											$tmp[] = $vv;
									}
									
									if (!empty($tmp))
										$val = $tmp;
									else
										$val = false;
								}
								else
								{
									if (empty($val))
										$val = false;
								}
							}
							
							// если поле множественное но значение не множественное
							if (!empty($arField["MULTIPLE"]) && !is_array($val) && !empty($val))
								$arData[$arField["CODE"]] = (array)$val;
							else
								$arData[$arField["CODE"]] = $val;
						}
					}
					
					$HASH = GetHashHLBlockElement($arData);
					$bHashChanged = false;
					
					$elementId = false;
					$bDestItem = false;
					$bUpdate = true;
					
					// существующий элемент и обновление
					if (isset($arDest[$arSItem["ID"]]))
					{
						$elementId = $arDest[$arSItem["ID"]]["ID"];
						$bHashChanged = ($HASH != $arDest[$arSItem["ID"]]["HASH"]);
					}
					elseif (isset($arDest[$arSItem["UF_XML_ID"]]))
					{
						$elementId = $arDest[$arSItem["UF_XML_ID"]]["ID"];
						$bHashChanged = ($HASH != $arDest[$arSItem["UF_XML_ID"]]["HASH"]);
						$bDestItem = true;
					}
					
					if (!empty($elementId))
					{
						if ($bHashChanged)
						{
							$arData[$fieldCodeHash] = $HASH;
							
							if (!IsTestMode())
							{
								if ($bDestItem)
								{
									if (!(defined("UPDATE_DEST_HLBLOCK_ITEMS") && "Y" == UPDATE_DEST_HLBLOCK_ITEMS))
										$bUpdate = false;
								}
								
								if ($bUpdate)
								{
									// для гарантии наличия ИД у совпадающих элементов источник/получатель
									$arData[$fieldCodeLinkId] = $arSItem["ID"];
									
									// картинки
									foreach ($arSItem as $fieldCode => $fieldValue)
									{
										if (isset($arHLBlocksFieldMap[$id][$fieldCode]) && "file" == $arHLBlocksFieldMap[$id][$fieldCode]["TYPE"] &&
												!empty($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]))
										{
											if (is_array($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]))
											{
												foreach ($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]] as $fi => $fval)
												{
													if (!empty($fval))
														$arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]][$fi] = \CFile::MakeFileArray($fval);
												}
											}
											else
											{
												$arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]] = \CFile::MakeFileArray($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]);
											}
										}
									}
								}
								
								if ($bUpdate)
									$res = $DC::update($elementId, $arData);
								else
								{
									// для гарантии наличия ИД у совпадающих элементов источник/получатель
									$res = $DC::update($elementId, Array($fieldCodeLinkId => $arSItem["ID"],));
									
									$res = true;
								}
							}
							else
								$res = true;
							
							if (!empty($res))
							{
								$bSave = true;
								
								$countUpdate++;
								
								if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
									LogProcessData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update hlblock element",
											"hlblock" => $arHLBlocksMap[$id],
											"data" => $arData,
											"elementId" => $elementId,
											"element exists" => ($bDestItem ? "Y" : "N"),
											"element updated" => ($bUpdate ? "Y" : "N"),
									), $ITERATION);
								if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
									LogDebugData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update hlblock element",
											"hlblock" => $arHLBlocksMap[$id],
											"data" => $arData,
											"elementId" => $elementId,
											"element exists" => ($bDestItem ? "Y" : "N"),
											"element updated" => ($bUpdate ? "Y" : "N"),
									), $ITERATION);
							}
						}
					}
					else
					{
						// новый элемент
						
						$arData[$fieldCodeId] = $arSItem["ID"];
						$arData[$fieldCodeLinkId] = $arSItem["ID"];
						$arData[$fieldCodeHash] = $HASH;
						
						if (!IsTestMode())
						{
							// картинки
							foreach ($arSItem as $fieldCode => $fieldValue)
							{
								if (isset($arHLBlocksFieldMap[$id][$fieldCode]) && "file" == $arHLBlocksFieldMap[$id][$fieldCode]["TYPE"] &&
										!empty($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]))
								{
									if (is_array($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]))
									{
										foreach ($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]] as $fi => $fval)
										{
											if (!empty($fval))
												$arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]][$fi] = \CFile::MakeFileArray($fval);
										}
									}
									else
									{
										$arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]] = \CFile::MakeFileArray($arData[$arHLBlocksFieldMap[$id][$fieldCode]["CODE"]]);
									}
								}
							}
							
							$res = $DC::add($arData);
							$elementId = $res->getID();
						}
						else
						{
							$elementId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
						}
						
						if (!empty($elementId))
						{
							$bSave = true;
							
							$countNew++;
							
							if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
								LogProcessData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new hlblock elelemnt",
										"hlblock" => $arHLBlocksMap[$id],
										"data" => $arData,
										"elementId" => $elementId,
								), $ITERATION);
							if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
								LogDebugData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new hlblock element",
										"hlblock" => $arHLBlocksMap[$id],
										"data" => $arData,
										"elementId" => $elementId,
								), $ITERATION);
						}
					}
					
					if (!empty($bLinked) && !empty($bSave) && !empty($elementId) && ($bDestItem ? $bUpdate : true))
					{
						@fputcsv($fhLinked, Array($elementId), FIELD_DELIMITER, TEXT_SEPARATOR);
					}
					
					@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
					
					// проверка продолжения по времени/полям
					$bProcess = !IsTimeout();
					
					if (!$bProcess)
						break 1;
				}
				
			}
			
			unset($arSource, $arDest);
			
		} while($bProcess && $bItem);
		
		// продолжать итерацию
		$bProcess = !IsTimeout();
		
		// итерация закончена или данные пройдены
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
		
		@fclose($fhLinked);
		
		unset($arSource, $arDest);
		
		$msg = "For Hlblock # " . $arHLBlocksMap[$id] . " created " . $countNew . " new element(s), updated " . $countUpdate . " element(s).";
		@LogProcessInfo($msg);
	}
	
}

unset($arSource, $arDest);

// настройка полей hlblocks закончена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 10, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Hlblocks data recieving finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>