<?php

// ШАГ 16 - наборы/комплекты iblock каталога источник/получатель

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
	
	@LogProcessInfo("Iblock catalog sets/groups recieving started.", true);
}

$curIBlock = intval($arStepParams["CURRENT_IBLOCK"]);

// на всякий случай
if (!in_array($curIBlock, $arSourceIBlockList))
	$bProcess = false;
else
	$bProcess = true;

if ($bProcess)
{
	$ITERATION = date("His");
	
	$el = new \CIBlockElement;
	
	$id = $curIBlock;
	$bFinished = false;
	
	// начало обхода элементов или продолжение
	if (!isset($arStepParams["CURRENT_ITEM_ID"]) || intval($arStepParams["CURRENT_ITEM_ID"]) < 0)
	{
		@SetStepParams(Array("CURRENT_ITEM_ID" => 0,));
	}
	
	$curItemId = intval($arStepParams["CURRENT_ITEM_ID"]);
	
	// зацикливаем пока есть время и есть что обрабатывать
	do {
		$bItem = false; // флаг обработки данных
		
		$arSource = getStepDataCatalogProductSet($id, $curItemId);
		
		if (!empty($arSource))
		{
			$propCodeId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodeLinkId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodeSetHash = IBLOCK_PROPERTY_HASH_SET . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			
			$arSourceID = Array();
			
			foreach ($arSource as $sid => $arSItem)
			{
				$arSourceID[$arSItem["ITEM_ID"]] = $arSItem["ITEM_ID"];
				
				if (!empty($arSItem["ITEMS"]))
				{
					foreach ($arSItem["ITEMS"] as $arItem)
					{
						$arSourceID[$arItem["ITEM_ID"]] = $arItem["ITEM_ID"];
					}
				}
			}
			
			$arDest = Array();
			$arDestHash = Array();
			
			$arIBFilter = Array(
					"IBLOCK_ID" => $arIBlocksMap[$id],
					Array(
						"LOGIC" => "OR",
						Array("PROPERTY_".$propCodeId => $arSourceID,),
						Array("PROPERTY_".$propCodeLinkId => $arSourceID,),
					),
				);
			
			$res = \CIBlockElement::GetList(
					Array("ID" => "ASC"),
					$arIBFilter,
					false, false,
					Array(
						"ID",
						"IBLOCK_ID",
						"PROPERTY_".$propCodeId,
						"PROPERTY_".$propCodeLinkId,
						"PROPERTY_".$propCodeSetHash,
					)
				);
			while ($ar_res = $res->Fetch())
			{
				$destKey = intval($ar_res[("PROPERTY_".$propCodeId."_VALUE")]);
				
				if (0 < $destKey)
				{
					$arDest[$destKey] = $ar_res["ID"];
					$arDestHash[$destKey] = $ar_res[("PROPERTY_".$propCodeSetHash."_VALUE")];
				}
				else
				{
					$destKey = intval($ar_res[("PROPERTY_".$propCodeLinkId."_VALUE")]);
					
					if (0 < $destKey)
					{
						$arDest[$destKey] = $ar_res["ID"];
						$arDestHash[$destKey] = $ar_res[("PROPERTY_".$propCodeSetHash."_VALUE")];
					}
				}
			}
			
			unset($arSourceID);
			
			// данные источника
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				$bItem = true;
				$curItemId = $sid;
				
				@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
				
				if (isset($arDest[$sid]))
					$elementId = $arDest[$sid];
				else
					$elementId = false;
				
				if (!empty($elementId))
				{
					$arSet = Array(
							"TYPE" => $arSItem["TYPE"],
							"ITEM_ID" => $elementId,
							"ACTIVE" => $arSItem["ACTIVE"],
						);
					if (!empty($arSItem["QUANTITY"]))
						$arSet["QUANTITY"] = $arSItem["QUANTITY"];
					if (!empty($arSItem["SORT"]))
						$arSet["SORT"] = $arSItem["SORT"];
					if (!empty($arSItem["MEASURE"]))
						$arSet["MEASURE"] = $arSItem["MEASURE"];
					if (!empty($arSItem["DISCOUNT_PERCENT"]))
						$arSet["DISCOUNT_PERCENT"] = $arSItem["DISCOUNT_PERCENT"];
					
					if (!empty($arSItem["ITEMS"]))
					{
						$arSetItems = Array();
						
						foreach ($arSItem["ITEMS"] as $arItem)
						{
							if (isset($arDest[$arItem["ITEM_ID"]]))
							{
								$arEl = Array("ITEM_ID" => $arDest[$arItem["ITEM_ID"]]);
								if (!empty($arItem["SORT"]))
									$arEl["SORT"] = $arItem["SORT"];
								if (!empty($arItem["QUANTITY"]))
									$arEl["QUANTITY"] = $arItem["QUANTITY"];
								if (!empty($arItem["MEASURE"]))
									$arEl["MEASURE"] = $arItem["MEASURE"];
								if (!empty($arItem["DISCOUNT_PERCENT"]))
									$arEl["DISCOUNT_PERCENT"] = $arItem["DISCOUNT_PERCENT"];
								
								$arSetItems[] = $arEl;
							}
						}
						
						if (!empty($arSetItems))
							$arSet["ITEMS"] = $arSetItems;
					}
					
					// хэш набора/комплекта
					$HASH_SET = GetHashSet($arSet);
					$bHashSetChanged = ($HASH_SET != $arDestHash[$sid]);
					
					// набор/комплект у товара обновить или создать
					if (\CCatalogProductSet::isProductHaveSet($elementId))
					{
						$setId = false;
						
						$ar_set = \CCatalogProductSet::getAllSetsByProduct($elementId, \CCatalogProductSet::TYPE_SET);
						
						if (!empty($ar_set))
						{
							$ar_set = array_shift($ar_set);
							$setId = $ar_set["SET_ID"];
						}
						else
						{
							$ar_set = \CCatalogProductSet::getAllSetsByProduct($elementId, \CCatalogProductSet::TYPE_GROUP);
							
							if (!empty($ar_set))
							{
								$ar_set = array_shift($ar_set);
								$setId = $ar_set["SET_ID"];
							}
						}
						
						if (!empty($setId))
						{
							if (!IsTestMode())
							{
								if ($bHashSetChanged)
								{
									@\CCatalogProductSet::update($setId, $arSet);
								
									\CCatalogProductSet::recalculateSetsByProduct($elementId);
									
									@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], Array($propCodeSetHash => $HASH_SET));
								}
							}
							
							if (defined("LOG_ELEMENTS_UPDATE") && "Y" == LOG_ELEMENTS_UPDATE)
								LogProcessData(
									Array(
										"time" => date("H:i:s"),
										"action" => "update catalog product set",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arSet,
										"elementId" => $elementId,
										"setId" => $setId,
										"set updated" => ($bHashSetChanged ? "Y" : "N"),
								), $ITERATION);
							if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
								LogDebugData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new catalog product set",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arSet,
										"elementId" => $elementId,
										"setId" => $setId,
								), $ITERATION);
						}
						else
						{
							// сбой - товар должен иметь набор/комплект - но ИД не найден
							LogProcessError(print_r(Array(
																				"time" => date("H:i:s"),
																				"action" => "catalog product SHOULD be set, id NOT found",
																				"elementId" => $elementId,
																			), true), true);
							if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
								LogDebugData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new catalog product set",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arSet,
										"elementId" => $elementId,
										"setId" => $setId,
								), $ITERATION);
						}
					}
					else
					{
						// новый
						if (!IsTestMode())
						{
							$setId = \CCatalogProductSet::add($arSet);
							
							if (!empty($setId))
							{
								\CCatalogProductSet::recalculateSetsByProduct($elementId);
								
								@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], Array($propCodeSetHash => $HASH_SET));
							}
						}
						else
						{
							$setId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
						}
						
						if (!empty($setId))
						{
							if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
								LogProcessData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new catalog product set",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arSet,
										"elementId" => $elementId,
										"setId" => $setId,
								), $ITERATION);
							if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
								LogDebugData(
									Array(
										"time" => date("H:i:s"),
										"action" => "new catalog product set",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arSet,
										"elementId" => $elementId,
										"setId" => $setId,
								), $ITERATION);
						}
						else
						{
							// сбой - товар есть у источника, но нет у получателя
							LogProcessError(print_r(Array(
																				"time" => date("H:i:s"),
																				"action" => "new catalog product set NOT added",
																				"data" => $arSet,
																				"elementId" => $elementId,
																			), true), true);
						}
					}
				}
				else
				{
					// сбой - товар есть у источника, но нет у получателя
					LogProcessError(print_r(Array(
																		"time" => date("H:i:s"),
																		"action" => "source product NOT found in destination catalog",
																		"source element ID" => $curItemId,
																	), true), true);
				}
				
				// проверка продолжения по времени/полям
				$bProcess = !IsTimeout();
				
				if (!$bProcess)
					break 1;
			}
			
		}
		
		unset($arSource, $arDest);
		
	} while($bProcess && $bItem);
	
	// итерация закончена и данные пройдены
	if (!$bItem)
	{
		$bFinished = true;
		
		// на всякий случай
		$curIBlock = PHP_INT_MAX;
		@SetStepParams(Array("CURRENT_IBLOCK" => $curIBlock, "CURRENT_ITEM_ID" => false,));
	}
	
	@fclose($fhLinked);
	
	unset($arSource, $arDest, $arDestHash);
	
}

unset($arSource, $arDest);

// обновление наборов/комплектов завершено
if ($bFinished)
{
	@FinishProcess();
	
	@LogProcessInfo("Iblock catalog sets/groups recieving finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>