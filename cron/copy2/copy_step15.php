<?php

// ШАГ 15 - каталог перепривязка товаров

@ProcessOn();

$bProcess = false;
$bFinished = true;

clearstatcache();

// начало или продолжение
if (!isset($arStepParams["CURRENT_IBLOCK"]) || intval($arStepParams["CURRENT_IBLOCK"]) < 0)
{
	@SetStepParams(Array("CURRENT_IBLOCK" => IBLOCK_ID_SOURCE_CATALOG,));
	
	$bProcess = true;
	$bFinished = false;
	
	@LogProcessInfo("Iblock catalog linked products update started.", true);
}

$curIBlock = intval($arStepParams["CURRENT_IBLOCK"]);

// на всякий случай
if (!in_array($curIBlock, $arSourceIBlockList))
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
	
	unset($map);
	
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
	
	$id = $curIBlock;
	$bFinished = false;
	
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
		$arUpdateData = Array();
		
		while (($data = fgetcsv($fhLinked, 0, FIELD_DELIMITER, TEXT_SEPARATOR)) !== false)
		{
			if (!empty($data[1]))
				$linked = unserialize($data[1]);
			
			if (!empty($data[0]) && intval($data[0]) > 0 && !empty($linked))
			{
				$arUpdateId[] = $data[0];
				$arUpdateData[$data[0]] = $linked;
			}
		}
		
		unset($data, $linked);
		
		if (!empty($arUpdateId))
		{
			$arUpdateId = array_unique($arUpdateId);
			@sort($arUpdateId, SORT_NUMERIC);
			
			$stepLimit = 100;
			
			// свойства с привязкой к каталогу
			$arLinkProperties = Array();
			
			foreach ($arIBlocksPropsMap[$id] as $propCode => $arProperty)
			{
				if ("E" == $arProperty["TYPE"] && $arProperty["IBLOCK_ID"] == $arIBlocksMap[$id])
					$arLinkProperties[$arProperty["CODE"]] = $arProperty["CODE"];
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
						if (!empty($arUpdateData[$ei]))
						{
							$tmp = Array();
							
							foreach ($arUpdateData[$ei] as $propCode => $linked)
							{
								if (!empty($arLinkProperties[$propCode]))
									$tmp[$propCode] = $linked;
							}
							
							if (!empty($tmp))
								$arElements[$ei] = Array("PROPERTIES" => $tmp,);
						}
					}
					
					unset($tmp, $linked);
				}
				
				unset($arStepId);
				
				if (!empty($arElements))
				{
					// формируем карту ИД источника-получателя привязок
					$arLinkFilter[$arIBlocksMap[$id]] = Array();
					
					foreach ($arElements as $arItem)
					{
						foreach ($arItem["PROPERTIES"] as $propCode => $linked)
						{
							if (!empty($linked))
							{
								if (is_array($linked))
									$arLinkFilter[$arIBlocksMap[$id]] = array_merge($arLinkFilter[$arIBlocksMap[$id]], $linked);
								else
									$arLinkFilter[$arIBlocksMap[$id]][] = $linked;
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
						
						foreach ($arItem["PROPERTIES"] as $propCode => $linked)
						{
							if (!empty($linked))
							{
								if (isset($arIBlocksPropsMap[$id][$propCode]))
								{
									$arProperty = $arIBlocksPropsMap[$id][$propCode];
									
									if (!empty($arProperty["MULTIPLE"]))
									{
										if (!is_array($linked))
											$linked = (array)$linked;
										
										$tmp = Array();
										
										foreach ($linked as $vv)
										{
											if (isset($arIdMap[$arIBlocksMap[$id]][$vv]))
												$tmp[] = $arIdMap[$arIBlocksMap[$id]][$vv];
										}
										
										if (!empty($tmp))
											$arUpdateProps[$propCode] = $tmp;
										//else
											//$arUpdateProps[$propCode] = false;
									}
									else
									{
										if (is_array($linked))
											$vv = current($linked);
										else
											$vv = $linked;
										
										if (isset($arIdMap[$arIBlocksMap[$id]][$vv]))
											$arUpdateProps[$propCode] = $arIdMap[$arIBlocksMap[$id]][$vv];
										//else
											//$arUpdateProps[$propCode] = false;
									}
								}
							}
							else
							{
								$arUpdateProps[$propCode] = false;
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
	
	// итерация закончена или данные пройдены
	if (!$bItem)
	{
		$bFinished = true;
		
		// на всякий случай
		$curIBlock = PHP_INT_MAX;
		@SetStepParams(Array("CURRENT_IBLOCK" => $curIBlock, "CURRENT_ITEM_ID" => false,));
	}
	
	@fclose($fhLinked);
	
	unset($arElements, $arUpdateProps, $arUpdateId);
	
	$msg = "For Iblock catalog # " . $arIBlocksMap[$id] . " updated " . $countUpdate . " element(s).";
	@LogProcessInfo($msg);
}

unset($arElements, $arUpdateProps, $arUpdateId);

// перепривязка товаров завершена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 16, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblocks linked properties update finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>