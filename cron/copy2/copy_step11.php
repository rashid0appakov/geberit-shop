<?php

// ШАГ 11 - данные iblocks источник/получатель

@ProcessOn();

$bProcess = false;
$bFinished = true;

clearstatcache();

// начало или продолжение
if (!isset($arStepParams["CURRENT_IBLOCK_INDEX"]) || intval($arStepParams["CURRENT_IBLOCK_INDEX"]) < 0)
{
	@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => 0,));
	
	$bProcess = true;
	$bFinished = false;
	
	@LogProcessInfo("Iblocks data recieving started.", true);
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
	
	if (file_exists(__DIR__."/".FILE_SOURCE_IBLOCK_MAP))
	{
		$map = file_get_contents(__DIR__."/".FILE_SOURCE_IBLOCK_MAP);
		
		if (!empty($map))
			$arIBlocksPropsMap = unserialize($map);
		else
			$bProcess = false;
	}
	else
		$bProcess = false;
	
	unset($map);
	
	// без карты свойств копирование невозможно
	if (!$bProcess)
	{
		$err = "Unknown error for iblocks properties map. Iblocks copy impossible.";
		
		LogOutString($err);
		LogProcessError($err, true);
	}
}

if ($bProcess)
{
	$el = new \CIBlockElement;
	
	foreach ($arSourceIBlockList as $i => $id)
	{
		// пропускаем обработанные
		if ($i < $curIndex)
			continue;
		
		// пропускаем каталог
		if (IBLOCK_ID_SOURCE_CATALOG == $id)
		{
			$curIndex = $i;
			@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
			
			continue;
		}
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
		
		// счётчики
		$countNew = 0;
		$countUpdate = 0;
		
		// начало обхода элементов или продолжение
		if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
		{
			@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
		}
		
		$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
		
		$fhLinked = fopen(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_LINKED.$id, "a");
		
		// зацикливаем пока есть время и есть что обрабатывать
		do {
			$bItem = false; // флаг обработки данных
			
			$arSource = getStepDataIBlock($id, $curItemId);
			
			if (!empty($arSource))
			{
				// данные получателя
				$propCodeId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX;
				$propCodeLinkId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX;
				$propCodeHash = IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX;
				$arSourceID = array_keys($arSource);
				$arSourceCODE = Array();
				foreach ($arSource as $sid => $arSItem)
				{
					if (!empty($arSItem["CODE"]))
						$arSourceCODE[] = $arSItem["CODE"];
				}
				$arSourceCODE = array_unique($arSourceCODE);
				$arDest = Array();
				
				if (!empty($arSourceCODE))
					$arIBFilter = Array(
							"IBLOCK_ID" => $arIBlocksMap[$id],
							Array(
								"LOGIC" => "OR",
								Array("PROPERTY_".$propCodeId => $arSourceID,),
								Array("=CODE" => $arSourceCODE,)
						 ),
						);
				else
					$arIBFilter = Array(
							"IBLOCK_ID" => $arIBlocksMap[$id],
							"PROPERTY_".$propCodeId => $arSourceID,
						);
				
				$res = \CIBlockElement::GetList(
						Array("ID" => "ASC"),
						$arIBFilter,
						false, false,
						Array(
							"ID",
							"IBLOCK_ID",
							"CODE",
							("PROPERTY_".$propCodeId),
							("PROPERTY_".$propCodeHash),
						)
					);
				while ($ar_res = $res->Fetch())
				{
					if (!empty($ar_res[("PROPERTY_".$propCodeId."_VALUE")]) && in_array($ar_res[("PROPERTY_".$propCodeId."_VALUE")], $arSourceID))
						$destKey = $ar_res[("PROPERTY_".$propCodeId."_VALUE")];
					else
						$destKey = $ar_res["CODE"];
					
					$arDest[$destKey] = Array("ID" => $ar_res["ID"], "HASH" => $ar_res[("PROPERTY_".$propCodeHash."_VALUE")]);
				}
				
				unset($arSourceID, $arSourceCODE);
				
				// данные источника
				foreach ($arSource as $sid => $arSItem)
				{
					// пропускаем обработанные
					if ($sid <= $curItemId)
						continue;
					
					$bItem = true;
					$curItemId = $sid;
					
					// ATTENTION
					$IBLOCK_SECTION_ID = false;
					
					$arData = Array(
							"IBLOCK_ID" => $arIBlocksMap[$id],
							"SORT" => 999999,
							"ACTIVE" => "Y",
							"IBLOCK_SECTION_ID" => $IBLOCK_SECTION_ID,
							"NAME" => false,
							"CODE" => false,
						);
					
					$arProps = Array();
					$bLinked = false;
					$bSave = false;
					
					// формируем данные
					if (!empty($arSItem["NAME"]))
					{
						$arData["NAME"] = $arSItem["NAME"];
						
						if (!empty($arSItem["CODE"]))
							$arData["CODE"] = $arSItem["CODE"];
						else
							$arData["CODE"] = MakeCode($arSItem["NAME"]);
						
						if (!empty($arSItem["ACTIVE"]))
							$arData["ACTIVE"] = $arSItem["ACTIVE"];
						if (!empty($arSItem["SORT"]))
							$arData["SORT"] = $arSItem["SORT"];
						if (!empty($arSItem["XML_ID"]))
							$arData["XML_ID"] = $arSItem["XML_ID"];
						
						if (!empty($arSItem["PREVIEW_TEXT"]))
							$arData["PREVIEW_TEXT"] = $arSItem["PREVIEW_TEXT"];
						if (!empty($arSItem["PREVIEW_TEXT_TYPE"]))
							$arData["PREVIEW_TEXT_TYPE"] = $arSItem["PREVIEW_TEXT_TYPE"];
						if (!empty($arSItem["DETAIL_TEXT"]))
							$arData["DETAIL_TEXT"] = $arSItem["DETAIL_TEXT"];
						if (!empty($arSItem["DETAIL_TEXT_TYPE"]))
							$arData["DETAIL_TEXT_TYPE"] = $arSItem["DETAIL_TEXT_TYPE"];
						
						if (!empty($arSItem["PREVIEW_PICTURE"]))
							$arData["PREVIEW_PICTURE"] = $arSItem["PREVIEW_PICTURE"];
						if (!empty($arSItem["DETAIL_PICTURE"]))
							$arData["DETAIL_PICTURE"] = $arSItem["DETAIL_PICTURE"];
					}
					
					// свойства
					foreach ($arSItem["PROPERTIES"] as $propCode => $propValue)
					{
						if (isset($arIBlocksPropsMap[$id][$propCode]))
						{
							$arProperty = $arIBlocksPropsMap[$id][$propCode];
							
							// привязка к разделу/элементу
							if ("E" == $arProperty["TYPE"])
								$bLinked = true;
							
							if ("L" == $arProperty["TYPE"])
								$val = $propValue["VALUE_ENUM_ID"];
							else
								$val = $propValue["VALUE"];
							
							// списочные значения конвертируются
							if ("L" == $arProperty["TYPE"])
							{
								if (is_array($val))
								{
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (isset($arProperty["LIST"][$vv]))
											$tmp[] = $arProperty["LIST"][$vv];
									}
									
									if (!empty($tmp))
										$val = $tmp;
									else
										$val = false;
								}
								else
								{
									if (isset($arProperty["LIST"][$val]))
										$val = $arProperty["LIST"][$val];
									else
										$val = false;
								}
							}
							
							// форматируем
							if ("L" == $arProperty["TYPE"]) // список
							{
								if (!empty($arProperty["MULTIPLE"]))
									$arProps[$arProperty["CODE"]] = (is_array($val) ? $val : (array)$val);
								else
									$arProps[$arProperty["CODE"]] = Array("VALUE" => $val);
							}
							elseif ("F" == $arProperty["TYPE"]) // файл
							{
								if (!empty($arProperty["MULTIPLE"]))
								{
									if (!is_array($val))
										$val = (array)$val;
									
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (!empty($vv))
											$tmp[] = Array("VALUE" => $vv);
									}
									
									if (!empty($tmp))
										$arProps[$arProperty["CODE"]] = $tmp;
									else
										$arProps[$arProperty["CODE"]] = false;
								}
								else
								{
									if (!empty($val))
										$arProps[$arProperty["CODE"]] = Array("VALUE" => $val);
									else
										$arProps[$arProperty["CODE"]] = false;
								}
							}
							elseif ("S" == $arProperty["TYPE"] && "HTML" == $arProperty["USER_TYPE"])
							{
								if (!empty($arProperty["MULTIPLE"]))
								{
									if (!is_array($val))
										$val = (array)$val;
									
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (!empty($vv["TEXT"]))
											$tmp[] = Array("VALUE" => Array("TEXT" => $vv["TEXT"], "TYPE" => $vv["TYPE"]));
									}
									
									if (!empty($tmp))
										$arProps[$arProperty["CODE"]] = $tmp;
									else
										$arProps[$arProperty["CODE"]] = false;
								}
								else
								{
									if (!empty($val["TEXT"]))
										$arProps[$arProperty["CODE"]] = Array("VALUE" => Array("TEXT" => $val["TEXT"], "TYPE" => $val["TYPE"]));
									else
										$arProps[$arProperty["CODE"]] = false;
								}
							}
							else // стандартные типы
							{
								if (!empty($arProperty["MULTIPLE"]))
								{
									if (!is_array($val))
										$val = (array)$val;
									
									$tmp = Array();
									
									foreach ($val as $vi => $vv)
									{
										if (!empty($vv))
											$tmp[] = $vv;
									}
									
									if (!empty($tmp))
										$arProps[$arProperty["CODE"]] = $tmp;
									else
										$arProps[$arProperty["CODE"]] = false;
								}
								else
								{
									if (!empty($val))
										$arProps[$arProperty["CODE"]] = $val;
									else
										$arProps[$arProperty["CODE"]] = false;
								}
							}
						}
					}
					
					$bHashChanged = false;
					
					$elementId = false;
					$bDestItem = false;
					$bUpdate = true;
					
					// существующий элемент и обновление
					if (isset($arDest[$arSItem["ID"]]))
					{
						$elementId = $arDest[$arSItem["ID"]]["ID"];
						
						$arTest = $arData;
						if (!empty($arProps))
							$arTest["PROPERTY_VALUES"] = $arProps;
						
						$HASH = GetHashIBlockElement($arTest);
						unset($arTest);
						
						$bHashChanged = ($HASH != $arDest[$arSItem["ID"]]["HASH"]);
					}
					elseif (isset($arDest[$arSItem["CODE"]]))
					{
						$elementId = $arDest[$arSItem["CODE"]]["ID"];
						
						$arTest = $arData;
						if (!empty($arProps))
							$arTest["PROPERTY_VALUES"] = $arProps;
						
						$HASH = GetHashIBlockElement($arTest);
						unset($arTest);
						
						$bHashChanged = ($HASH != $arDest[$arSItem["CODE"]]["HASH"]);
						$bDestItem = true;
					}
					
					if (!empty($elementId))
					{
						if ($bHashChanged)
						{
							$arProps[$propCodeHash] = $HASH;
							
							if (!IsTestMode())
							{
								if ($bDestItem)
								{
									if (!(defined("UPDATE_DEST_IBLOCK_ITEMS") && "Y" == UPDATE_DEST_IBLOCK_ITEMS))
										$bUpdate = false;
								}
								
								if ($bUpdate)
								{
									// для гарантии наличия ИД у совпадающих элементов источник/получатель
									$arProps[$propCodeLinkId] = $arSItem["ID"];
									
									// картинки
									if (!empty($arData["PREVIEW_PICTURE"]))
										$arData["PREVIEW_PICTURE"] = \CFile::MakeFileArray($arData["PREVIEW_PICTURE"]);
									if (!empty($arData["DETAIL_PICTURE"]))
										$arData["DETAIL_PICTURE"] = \CFile::MakeFileArray($arData["DETAIL_PICTURE"]);
									
									// картинки в свойствах
									foreach ($arSItem["PROPERTIES"] as $propCode => $propValue)
									{
										if (isset($arIBlocksPropsMap[$id][$propCode]) && "F" == $arIBlocksPropsMap[$id][$propCode]["TYPE"] &&
												!empty($arProps[$arIBlocksPropsMap[$id][$propCode]["CODE"]]))
										{
											$pc = $arIBlocksPropsMap[$id][$propCode]["CODE"];
											
											if (!empty($arIBlocksPropsMap[$id][$propCode]["MULTIPLE"]))
											{
												foreach ($arProps[$pc] as $fi => $fval)
												{
													if (!empty($fval["VALUE"]))
														$arProps[$pc][$fi]["VALUE"] = \CFile::MakeFileArray($fval["VALUE"]);
												}
											}
											else
											{
												if (!empty($arProps[$pc]["VALUE"]))
													$arProps[$pc]["VALUE"] = \CFile::MakeFileArray($arProps[$pc]["VALUE"]);
											}
										}
									}
									
									@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], $arProps);
									
									$arFields = $arData;
									unset($arFields["IBLOCK_ID"]);
									
									$el->Update($elementId, $arFields);
									unset($arFields);
									
									@\CIBlockElement::UpdateSearch($elementId);
								}
								else
								{
									// для гарантии наличия ИД у совпадающих элементов источник/получатель
									@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], Array($propCodeLinkId => $arSItem["ID"],));
								}
								
								$res = true;
							}
							else
								$res = true;
							
							if (!empty($res))
							{
								$bSave = true;
								
								$countUpdate++;
								
								if (!empty($arProps))
									$arData["PROPERTY_VALUES"] = $arProps;
								
								if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
									LogProcessData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update iblock element",
											"iblock" => $arIBlocksMap[$id],
											"data" => $arData,
											"elementId" => $elementId,
											"element exists" => ($bDestItem ? "Y" : "N"),
											"element updated" => ($bUpdate ? "Y" : "N"),
									), $ITERATION);
								if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
									LogDebugData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update iblock element",
											"iblock" => $arIBlocksMap[$id],
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
						
						if (!empty($arProps))
							$arData["PROPERTY_VALUES"] = $arProps;
						
						$HASH = GetHashIBlockElement($arData);
						
						$arData["PROPERTY_VALUES"][$propCodeId] = $arSItem["ID"];
						$arData["PROPERTY_VALUES"][$propCodeLinkId] = $arSItem["ID"];
						$arData["PROPERTY_VALUES"][$propCodeHash] = $HASH;
						
						if (!IsTestMode())
						{
							// картинки
							if (!empty($arData["PREVIEW_PICTURE"]))
								$arData["PREVIEW_PICTURE"] = \CFile::MakeFileArray($arData["PREVIEW_PICTURE"]);
							if (!empty($arData["DETAIL_PICTURE"]))
								$arData["DETAIL_PICTURE"] = \CFile::MakeFileArray($arData["DETAIL_PICTURE"]);
							
							// картинки в свойствах
							foreach ($arSItem["PROPERTIES"] as $propCode => $propValue)
							{
								if (isset($arIBlocksPropsMap[$id][$propCode]) && "F" == $arIBlocksPropsMap[$id][$propCode]["TYPE"] &&
										!empty($arData["PROPERTY_VALUES"][$arIBlocksPropsMap[$id][$propCode]["CODE"]]))
								{
									$pc = $arIBlocksPropsMap[$id][$propCode]["CODE"];
									
									if (!empty($arIBlocksPropsMap[$id][$propCode]["MULTIPLE"]))
									{
										foreach ($arData["PROPERTY_VALUES"][$pc] as $fi => $fval)
										{
											if (!empty($fval["VALUE"]))
												$arData["PROPERTY_VALUES"][$pc][$fi]["VALUE"] = \CFile::MakeFileArray($fval["VALUE"]);
										}
									}
									else
									{
										if (!empty($arData["PROPERTY_VALUES"][$pc]["VALUE"]))
											$arData["PROPERTY_VALUES"][$pc]["VALUE"] = \CFile::MakeFileArray($arData["PROPERTY_VALUES"][$pc]["VALUE"]);
									}
								}
							}
							
							$res = $el->Add($arData);
							
							if (!empty($res))
								$elementId = $res;
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
										"action" => "new iblock elelemnt",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arData,
										"elementId" => $elementId,
								), $ITERATION);
							if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
								LogDebugData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new iblock element",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arData,
										"elementId" => $elementId,
								), $ITERATION);
						}
					}
					
					if (!empty($bLinked) && !empty($bSave) && !empty($elementId) && ($bDestItem ? $bUpdate : true))
					{
						@fputcsv($fhLinked, Array($elementId), FIELD_DELIMITER, TEXT_SEPARATOR);
						
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "element has linked properties",
									"iblock" => $arIBlocksMap[$id],
									"elementId" => $elementId,
							), $ITERATION);
					}
					
					@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
					
					unset($arProps, $arData);
					
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
		
		@fclose($fhLinked);
		
		unset($arSource, $arDest);
		
		$msg = "For Iblock # " . $arIBlocksMap[$id] . " created " . $countNew . " new element(s), updated " . $countUpdate . " element(s).";
		@LogProcessInfo($msg);
	}
	
}

unset($arSource, $arDest);

// копирование данных iblocks закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 12, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblocks data recieving finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>