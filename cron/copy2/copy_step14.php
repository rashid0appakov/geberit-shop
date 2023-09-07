<?php

// ШАГ 14 - iblocks перепривязка связанных свойств

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
	
	@LogProcessInfo("Iblocks linked properties update started.", true);
}

$curIndex = intval($arStepParams["CURRENT_IBLOCK_INDEX"]);

// если всё было получено на предыдущей итерации
if (!array_key_exists($curIndex, $arSourceIBlockList))
	$bProcess = false;
else
	$bProcess = true;

if ($bProcess)
{
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
	
	// без карты свойств перелинковка невозможна
	if (!$bProcess)
	{
		$err = "Unknown error for iblocks properties map. Iblocks linked properties update impossible.";
		
		LogOutString($err);
		LogProcessError($err, true);
	}
}

if ($bProcess)
{
	$ITERATION = date("His");
	
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
		
		// счётчик
		$countUpdate = 0;
		
		// начало обхода элементов или продолжение
		if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
		{
			@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
		}
		
		$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
		$bItem = false; // флаг обработки данных - чтобы корректно пропустить пустые
		
		$fileLinked = __DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_LINKED.$id;
		
		if (file_exists($fileLinked) && filesize($fileLinked) && ($fhLinked = fopen($fileLinked, "r")))
		{
			// получаем все элементы на перепривязку
			$arUpdateId = Array();
			
			while (($data = fgetcsv($fhLinked, 0, FIELD_DELIMITER, TEXT_SEPARATOR)) !== false)
			{
				if (!empty($data[0]) && intval($data[0]) > 0)
					$arUpdateId[] = $data[0];
			}
			
			if (!empty($arUpdateId))
			{
				$arUpdateId = array_unique($arUpdateId);
				@sort($arUpdateId, SORT_NUMERIC);
				
				$stepLimit = 100;
				
				// свойства с привязкой
				$arLinkProperties = Array();
				
				foreach ($arIBlocksPropsMap[$id] as $propCode => $arProperty)
				{
					if ("E" == $arProperty["TYPE"])
						$arLinkProperties[] = $arProperty["CODE"];
				}
				
				// зацикливаем пока есть время и есть что обрабатывать
				do {
					$bItem = false; // флаг обработки данных
					
					// выборка данных частями
					$arStepId = Array();
					$arElements = Array();
					
					if (0 >= $curItemId)
						$arStepId = array_slice($arUpdateId, 0, $stepLimit);
					elseif (in_array($curItemId, $arUpdateId))
					{
						$stepKey = array_search($curItemId, $arUpdateId);
						
						if (false !== $stepKey)
							$arStepId = array_slice($arUpdateId, ($stepKey + 1), $stepLimit);
					}
					
					if (!empty($arStepId))
					{
						foreach ($arStepId as $ei)
						{
							$arElements[$ei] = Array("PROPERTIES" => Array(),);
						}
						
						\CIBlockElement::GetPropertyValuesArray(
								$arElements, 
								$arIBlocksMap[$id], 
								Array("ID" => $arStepId), 
								$arLinkProperties, 
								Array("PROPERTY_FIELDS" => Array("ID", "IBLOCK_ID", "CODE", "MULTIPLE", "LINK_IBLOCK_ID", "VALUE"), "GET_RAW_DATA" => "Y")
							);
						
						foreach ($arElements as $ei => $arItem)
						{
							if (empty($arItem["PROPERTIES"]))
								unset($arElements[$ei]);
								
							foreach ($arItem["PROPERTIES"] as $pc => $arProperty)
							{
								if (!in_array($pc, $arLinkProperties))
									unset($arElements[$ei]["PROPERTIES"][$pc]);
							}
						}
					}
					
					unset($arStepId);
					
					if (!empty($arElements))
					{
						// формируем карту ИД источника-получателя привязок
						$arLinkFilter = Array();
						
						foreach ($arElements as $arItem)
						{
							foreach ($arItem["PROPERTIES"] as $arProp)
							{
								if (!empty($arProp["LINK_IBLOCK_ID"]) && !empty($arProp["VALUE"]))
								{
									if (!isset($arLinkFilter[$arProp["LINK_IBLOCK_ID"]]))
										$arLinkFilter[$arProp["LINK_IBLOCK_ID"]] = Array();
									
									if (is_array($arProp["VALUE"]))
										$arLinkFilter[$arProp["LINK_IBLOCK_ID"]] = array_merge($arLinkFilter[$arProp["LINK_IBLOCK_ID"]], $arProp["VALUE"]);
									else
										$arLinkFilter[$arProp["LINK_IBLOCK_ID"]][] = $arProp["VALUE"];
								}
							}
						}
						
						foreach ($arLinkFilter as $li => $arVal)
						{
							$arLinkFilter[$li] = array_unique($arVal);
						}
						
						$arIdMap = Array();
						$propCodeId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX;
						$propCodeLinkId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX;
						
						foreach ($arLinkFilter as $li => $arVal)
						{
							$res = \CIBlockElement::GetList(
									Array("ID" => "ASC"),
									Array(
										"IBLOCK_ID" => $li,
										Array(
											"LOGIC" => "OR",
											Array("PROPERTY_".$propCodeId => $arVal,),
											Array("PROPERTY_".$propCodeLinkId => $arVal,),
										),
									),
									false, false,
									Array(
										"ID",
										"IBLOCK_ID",
										"PROPERTY_".$propCodeId,
										"PROPERTY_".$propCodeLinkId,
									)
								);
							while ($ar_res = $res->Fetch())
							{
								if (!empty($ar_res[("PROPERTY_".$propCodeId."_VALUE")]))
									$sourceKey = $ar_res[("PROPERTY_".$propCodeId."_VALUE")];
								else
									$sourceKey = $ar_res[("PROPERTY_".$propCodeLinkId."_VALUE")];
								
								$arIdMap[$li][$sourceKey] = $ar_res["ID"];
							}
						}
						
						foreach ($arElements as $ei => $arItem)
						{
							// пропускаем обработанные - на всякий случай
							if ($ei <= $curItemId)
								continue;
							
							$bItem = true;
							$curItemId = $ei;
							
							$arUpdateProps = Array();
							
							foreach ($arItem["PROPERTIES"] as $arProp)
							{
								if (!empty($arProp["LINK_IBLOCK_ID"]) && !empty($arProp["VALUE"]))
								{
									if ("Y" == $arProp["MULTIPLE"])
									{
										if (!is_array($arProp["VALUE"]))
											$arProp["VALUE"] = (array)$arProp["VALUE"];
										
										$tmp = Array();
										
										foreach ($arProp["VALUE"] as $vv)
										{
											if (isset($arIdMap[$arProp["LINK_IBLOCK_ID"]][$vv]))
												$tmp[] = $arIdMap[$arProp["LINK_IBLOCK_ID"]][$vv];
										}
										
										if (!empty($tmp))
											$arUpdateProps[$arProp["CODE"]] = $tmp;
										//else
											//$arUpdateProps[$arProp["CODE"]] = false;
									}
									else
									{
										if (is_array($arProp["VALUE"]))
											$vv = current($arProp["VALUE"]);
										else
											$vv = $arProp["VALUE"];
										
										if (isset($arIdMap[$arProp["LINK_IBLOCK_ID"]][$vv]))
											$arUpdateProps[$arProp["CODE"]] = $arIdMap[$arProp["LINK_IBLOCK_ID"]][$vv];
										//else
											//$arUpdateProps[$arProp["CODE"]] = false;
									}
								}
							}
							
							if (!empty($arUpdateProps))
							{
								if (!IsTestMode())
								{
									@\CIBlockElement::SetPropertyValuesEx($curItemId, $arIBlocksMap[$id], $arUpdateProps);
									
									usleep(250000);
									
									//@\CIBlockElement::UpdateSearch($curItemId);
								}
								
								if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
									LogProcessData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update iblock element linked properties",
											"iblock" => $arIBlocksMap[$id],
											"data" => $arUpdateProps,
											"elementId" => $curItemId,
									), $ITERATION);
								if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
									LogDebugData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update iblock element linked properties",
											"iblock" => $arIBlocksMap[$id],
											"data" => $arUpdateProps,
											"elementId" => $curItemId,
									), $ITERATION);
								
								$countUpdate++;
							}
							else
							{
								if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
									LogDebugData(
										Array(
											"time" => date("H:i:s"),
											"action" => "update iblock element linked properties not updated",
											"iblock" => $arIBlocksMap[$id],
											"data" => $arItem,
											"elementId" => $curItemId,
									), $ITERATION);
							}
							
							@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
							
							// проверка продолжения по времени/полям
							$bProcess = !IsTimeout();
							
							if (!$bProcess)
								break 1;
						}
						
					}
					
					unset($arElements, $arUpdateProps);
					
				} while($bProcess && $bItem);
				
			}
			
		}
		
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
		
		unset($arElements, $arUpdateProps, $arUpdateId);
		
		$msg = "For Iblock # " . $arIBlocksMap[$id] . " updated " . $countUpdate . " element(s).";
		@LogProcessInfo($msg);
	}
	
}

unset($arElements, $arUpdateProps, $arUpdateId);

// перепривязка завершена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 15, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblocks linked properties update finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>