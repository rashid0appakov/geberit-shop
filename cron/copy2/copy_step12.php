<?php

// ШАГ 12 - данные iblock каталога источник/получатель

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
	
	// для актуализации карты разделов
	if (file_exists(__DIR__."/".FILE_SOURCE_IBLOCK_SECTION_MAP))
		@unlink(__DIR__."/".FILE_SOURCE_IBLOCK_SECTION_MAP);
	
	@LogProcessInfo("Iblock catalog data recieving started.", true);
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
	
	// без карты свойств копирование невозможно
	if (!$bProcess)
	{
		$err = "Unknown error for iblocks properties map. Iblock catalog copy impossible.";
		
		LogOutString($err);
		LogProcessError($err, true);
	}
}

if ($bProcess)
{
	$ITERATION = date("His");
	
	$el = new \CIBlockElement;
	
	$id = $curIBlock;
	$bFinished = false;
	
	FillSectionsMap();
	
	// коды свойств с файлами - для вычисления хэша картинок
	$arFileTypeProperties = Array();
	foreach ($arIBlocksPropsMap[$id] as $propCode => $propValue)
	{
		if (!empty($propValue) && "F" == $propValue["TYPE"])
			$arFileTypeProperties[$propValue["CODE"]] = $propValue["CODE"];
	}
	
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
		
		$arSourceFilter = Array();
		
		// монобренд
		if (defined("SOURCE_MANUFACTURER_ID") && intval(SOURCE_MANUFACTURER_ID) > 0)
			$arSourceFilter["PROPERTY_MANUFACTURER"] = SOURCE_MANUFACTURER_ID;
		
		// разделы
		if (defined("SOURCE_SECTION_ID") && intval(SOURCE_SECTION_ID) > 0)
		{
			$arSourceFilter["SECTION_ID"] = SOURCE_SECTION_ID;
			$arSourceFilter["INCLUDE_SUBSECTIONS"] = "Y";
		}
		
		$arSource = getStepDataIBlock($id, $curItemId, false, $arSourceFilter);
		
		if (!empty($arSource))
		{
			// данные получателя
			$propCodeId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodeLinkId = IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodeIdentity = CATALOG_PRODUCTS_IDENTITY_PROPERTY;
			$propCodeHash = IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodePictureHash = IBLOCK_PROPERTY_HASH_PICTURE . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodePriceHash = IBLOCK_PROPERTY_HASH_PRICE . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			$propCodeQuantityHash = IBLOCK_PROPERTY_HASH_QUANTITY . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			
			$propCodeSource = IBLOCK_PROPERTY_IMPORT_SOURCE . IBLOCK_PROPERTY_SOURCE_SUFFIX;
			
			$arSourceID = array_keys($arSource);
			
			$arSourceCODE = Array();
			foreach ($arSource as $sid => $arSItem)
			{
				if (!empty($arSItem["CODE"]))
					$arSourceCODE[] = $arSItem["CODE"];
			}
			$arSourceCODE = array_unique($arSourceCODE);
			
			$arSourceARTICLES = Array();
			foreach ($arSource as $sid => $arSItem)
			{
				if (!empty($arSItem["PROPERTIES"]) && !empty($arSItem["PROPERTIES"][$propCodeIdentity]) && !empty($arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"]))
					$arSourceARTICLES[] = $arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"];
			}
			$arSourceARTICLES = array_unique($arSourceARTICLES);
			
			$arDest = Array();
			
			if (!empty($arSourceARTICLES)) // $arSourceCODE
				$arIBFilter = Array(
						"IBLOCK_ID" => $arIBlocksMap[$id],
						Array(
							"LOGIC" => "OR",
							Array("PROPERTY_".$propCodeId => $arSourceID,),
							Array("=PROPERTY_".$propCodeIdentity => $arSourceARTICLES,), // для идентификации по артикулу
							Array("=CODE" => $arSourceCODE,) // для идентификации по символьному коду
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
						("PROPERTY_".$propCodeIdentity),
						("PROPERTY_".$propCodeHash),
						("PROPERTY_".$propCodePictureHash),
						("PROPERTY_".$propCodePriceHash),
						("PROPERTY_".$propCodeQuantityHash),
					)
				);
			while ($ar_res = $res->Fetch())
			{
				if (!empty($ar_res[("PROPERTY_".$propCodeId."_VALUE")]) && in_array($ar_res[("PROPERTY_".$propCodeId."_VALUE")], $arSourceID))
					$destKey = $ar_res[("PROPERTY_".$propCodeId."_VALUE")];
				elseif (!empty($ar_res[("PROPERTY_".$propCodeIdentity."_VALUE")]) && in_array($ar_res[("PROPERTY_".$propCodeIdentity."_VALUE")], $arSourceARTICLES))
					$destKey = $ar_res[("PROPERTY_".$propCodeIdentity."_VALUE")];
				else // для идентификации по символьному коду
					$destKey = $ar_res["CODE"];
				
				$arDest[$destKey] = Array(
						"ID" => $ar_res["ID"], 
						"HASH" => $ar_res[("PROPERTY_".$propCodeHash."_VALUE")],
						"HASH_PICTURE" => $ar_res[("PROPERTY_".$propCodePictureHash."_VALUE")],
						"HASH_PRICE" => $ar_res[("PROPERTY_".$propCodePriceHash."_VALUE")],
						"HASH_QUANTITY" => $ar_res[("PROPERTY_".$propCodeQuantityHash."_VALUE")],
					);
			}
			
			unset($arSourceID, $arSourceCODE);
			
			// карта справочников
			// ATTENTION обрабатываются только ИБ справочники
			$arLinkProperties = Array();
			
			foreach ($arIBlocksPropsMap[$id] as $propCode => $arProperty)
			{
				if ("E" == $arProperty["TYPE"] && $arProperty["IBLOCK_ID"] != $arIBlocksMap[$id])
					$arLinkProperties[$arProperty["CODE"]] = $arProperty["CODE"];
			}
			
			$arLinkFilter = Array();
			
			foreach ($arSource as $sid => $arSItem)
			{
				if (!empty($arSItem["PROPERTIES"]))
				{
					foreach ($arSItem["PROPERTIES"] as $propCode => $propValue)
					{
						if (isset($arIBlocksPropsMap[$id][$propCode]))
						{
							$arProperty = $arIBlocksPropsMap[$id][$propCode];
							
							if (!empty($arLinkProperties[$arProperty["CODE"]]) && !empty($propValue["VALUE"]))
							{
								$linkIblockId = $arIBlocksPropsMap[$id][$arProperty["CODE"]]["IBLOCK_ID"];
								
								if (!isset($arLinkFilter[$linkIblockId]))
									$arLinkFilter[$linkIblockId] = Array();
								
								if (is_array($propValue["VALUE"]))
									$arLinkFilter[$linkIblockId] = array_merge($arLinkFilter[$linkIblockId], $propValue["VALUE"]);
								else
									$arLinkFilter[$linkIblockId][] = $propValue["VALUE"];
							}
						}
					}
				}
			}
			
			foreach ($arLinkFilter as $li => $arVal)
			{
				$arLinkFilter[$li] = array_unique($arVal);
			}
			
			$arLinkedIdMap = Array();
			
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
					
					$arLinkedIdMap[$li][$sourceKey] = $ar_res["ID"];
				}
			}
			
			unset($arLinkFilter, $li, $arVal, $sourceKey, $res, $ar_res);
			
			// данные источника
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				// ATTENTION монобренд!!!
				if (defined("SOURCE_MANUFACTURER_ID") && intval(SOURCE_MANUFACTURER_ID) > 0 && 
						(empty($arSItem["PROPERTIES"]["MANUFACTURER"]["VALUE"]) || SOURCE_MANUFACTURER_ID != $arSItem["PROPERTIES"]["MANUFACTURER"]["VALUE"]))
				{
					$curItemId = $sid;
					@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
					
					if (defined("LOG_ELEMENTS_SKIP") && "Y" == LOG_ELEMENTS_SKIP)
						LogDebugData(
							Array(
								"time" => date("H:i:s"),
								"action" => "skip iblock catalog element - not monobrend!!!",
								"iblock" => $arIBlocksMap[$id],
								"data" => Array("MANUFACTURER VALUE" => $arSItem["PROPERTIES"]["MANUFACTURER"]["VALUE"]),
								"elementId" => $curItemId,
						), ($ITERATION . "skip_monobrend"));
					
					continue;
				}
				
				$bItem = true;
				$curItemId = $sid;
				
				@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
				
				// ATTENTION для данных шага перепривязки товаров, связанных с другими товарами
				$arLinkedProducts = Array();
				
				// ATTENTION
				$IBLOCK_SECTION_ID = $arDestIBlockStructureSectionList[$arIBlocksMap[$id]];
				
				if (!empty($arSItem["IBLOCK_SECTION_ID"]) && isset($arSectionsMap[$id][$arSItem["IBLOCK_SECTION_ID"]]))
					$IBLOCK_SECTION_ID = $arSectionsMap[$id][$arSItem["IBLOCK_SECTION_ID"]];
				
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
						
						if ("L" == $arProperty["TYPE"])
							$val = $propValue["VALUE_ENUM_ID"];
						else
							$val = $propValue["VALUE"];
						
						// привязка к разделу/элементу
						if ("E" == $arProperty["TYPE"])
						{
							// справочники конвертируются, связанные товары сохраняются для шага перепривязки
							if (!empty($arLinkProperties[$arProperty["CODE"]]))
							{
								$linkIblockId = $arIBlocksPropsMap[$id][$propCode]["IBLOCK_ID"];
								
								if (is_array($val))
								{
									$tmp = Array();
									
									foreach ($val as $vv)
									{
										if (isset($arLinkedIdMap[$linkIblockId][$vv]))
											$tmp[] = $arLinkedIdMap[$linkIblockId][$vv];
										else
											$tmp[] = $vv;
									}
									
									if (!empty($tmp))
										$val = $tmp;
									else
										$val = false;
								}
								else
								{
									if (isset($arLinkedIdMap[$linkIblockId][$val]))
										$val = $arLinkedIdMap[$linkIblockId][$val];
									elseif (empty($val))
										$val = false;
								}
							}
							else
							{
								$bLinked = true;
								
								$arLinkedProducts[$arProperty["CODE"]] = $val;
							}
						}
						
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
						if ("E" == $arProperty["TYPE"]) // привязки
						{
							// ATTENTION на этом шаге сохраняются только привязки к справочникам
							if (!isset($arLinkedProducts[$arProperty["CODE"]]))
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
						elseif ("L" == $arProperty["TYPE"]) // список
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
				$bHashPictureChanged = false;
				$bHashPriceChanged = false;
				$bHashQuantityChanged = false;
				
				$elementId = false;
				$bDestItem = false;
				$bUpdate = true;
				
				// существующий элемент и обновление
				if (isset($arDest[$arSItem["ID"]]) ||
						(!empty($arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"]) && isset($arDest[$arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"]])) ||
						isset($arDest[$arSItem["CODE"]]))
				{
					if (isset($arDest[$arSItem["ID"]]))
					{
						$destKey = $arSItem["ID"];
					}
					elseif (!empty($arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"]) && isset($arDest[$arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"]]))
					{
						$destKey = $arSItem["PROPERTIES"][$propCodeIdentity]["VALUE"];
						
						$bDestItem = true;
					}
					else
					{
						$destKey = $arSItem["CODE"];
						
						$bDestItem = true;
					}
					
					$elementId = $arDest[$destKey]["ID"];
					
					$arTest = $arData;
					if (!empty($arProps))
						$arTest["PROPERTY_VALUES"] = $arProps;
					
					$HASH = GetHashProduct($arTest, $arFileTypeProperties);
					$HASH_PICTURE = GetHashPicture($arTest, $arFileTypeProperties);
					$HASH_PRICE = GetHashPrice($arSItem);
					$HASH_QUANTITY = GetHashQuantity($arSItem);
					
					unset($arTest);
					
					$bHashChanged = ($HASH != $arDest[$destKey]["HASH"]);
					$bHashPictureChanged = ($HASH_PICTURE != $arDest[$destKey]["HASH_PICTURE"]);
					$bHashPriceChanged = ($HASH_PRICE != $arDest[$destKey]["HASH_PRICE"]);
					$bHashQuantityChanged = ($HASH_QUANTITY != $arDest[$destKey]["HASH_QUANTITY"]);
					
					if (defined("OVERRIDE_DEST_CATALOG_ITEMS_HASH") && "Y" == OVERRIDE_DEST_CATALOG_ITEMS_HASH)
						$bHashChanged = true;
				}
				
				if (!empty($elementId))
				{
					// проверка на всякий случай
					$res = \CCatalogProduct::GetByID($elementId);
					
					// если товар не товар не в каталоге - исправляем
					if (!$res)
					{
						if (!IsTestMode())
						{
							@\CCatalogProduct::Add(Array("ID" => $elementId));
						}
					}
					
					if ($bHashChanged || 
							$bHashPictureChanged || 
							$bHashPriceChanged ||
							$bHashQuantityChanged)
					{
						$arProps[$propCodeHash] = $HASH;
						$arProps[$propCodePictureHash] = $HASH_PICTURE;
						$arProps[$propCodePriceHash] = $HASH_PRICE;
						$arProps[$propCodeQuantityHash] = $HASH_QUANTITY;
						
						$arProps[$propCodeSource] = IMPORT_SOURCE_KEY;
						
						if (!IsTestMode())
						{
							if ($bDestItem)
							{
								if (!(defined("UPDATE_DEST_CATALOG_ITEMS") && "Y" == UPDATE_DEST_CATALOG_ITEMS))
									$bUpdate = false;
							}
							
							if ($bUpdate)
							{
								// для гарантии наличия ИД у совпадающих элементов источник/получатель
								$arProps[$propCodeLinkId] = $arSItem["ID"];
									
								// картинки
								if ($bHashPictureChanged)
								{
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
								}
								
								// обновляем изменения в самом товаре
								if ($bHashChanged)
								{
									// нет изменений в картинках
									if (!$bHashPictureChanged && !empty($arFileTypeProperties))
									{
										foreach ($arFileTypeProperties as $propCode)
											unset($arProps[$propCode]);
									}
									
									@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], $arProps);
									
									$arFields = $arData;
									unset($arFields["IBLOCK_ID"]);
									// ATTENTION раздел НЕ обновляется!!!
									unset($arFields["IBLOCK_SECTION_ID"]);
									
									// нет изменений в картинках
									if (!$bHashPictureChanged)
									{
										unset($arFields["PREVIEW_PICTURE"]);
										unset($arFields["DETAIL_PICTURE"]);
									}
									
									$el->Update($elementId, $arFields);
									unset($arFields);
									
									//@\CIBlockElement::UpdateSearch($elementId);
								}
								
								// обновляем картинки если отдельно от товара
								if ($bHashPictureChanged && !$bHashChanged)
								{
									if (!empty($arFileTypeProperties))
									{
										$arProperties = Array();
										
										foreach ($arFileTypeProperties as $propCode)
										{
											if (!empty($arProps[$propCode]))
												$arProperties[$propCode] = $arProps[$propCode];
										}
										
										if (!empty($arProperties))
											@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], $arProperties);
										
										unset($arProperties);
									}
									
									$arFields = Array();
									
									if (!empty($arData["PREVIEW_PICTURE"]))
										$arFields["PREVIEW_PICTURE"] = $arData["PREVIEW_PICTURE"];
									if (!empty($arData["DETAIL_PICTURE"]))
										$arFields["DETAIL_PICTURE"] = $arData["DETAIL_PICTURE"];
									
									$el->Update($elementId, $arFields);
									unset($arFields);
								}
								
								// обновляем цены
								if (defined("UPDATE_DEST_CATALOG_PRICE") && "Y" == UPDATE_DEST_CATALOG_PRICE && $bHashPriceChanged)
								{
									if (!empty($arSItem["PRICES"]))
										UpdateProductPrices($elementId, $arSItem["PRICES"]);
									else
										UpdateProductPrices($elementId, 0);
								}
								
								// обновляем количество
								if (defined("UPDATE_DEST_CATALOG_QUANTITY") && "Y" == UPDATE_DEST_CATALOG_QUANTITY && $bHashQuantityChanged)
								{
									if (!empty($arSItem["STORES"]))
										UpdateStoreProductQuantity($elementId, $arSItem["STORES"]);
									else
										UpdateStoreProductQuantity($elementId, 0);
								}
								
								// обновляем хэш
								if (!$bHashChanged && ($bHashPictureChanged || $bHashPriceChanged || $bHashQuantityChanged))
								{
									$arHash = Array(
											$propCodePictureHash => $HASH_PICTURE,
											$propCodePriceHash => $HASH_PRICE,
											$propCodeQuantityHash => $HASH_QUANTITY,
										);
									
									@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], $arHash);
								}
							}
							else
							{
								// для гарантии наличия ИД у совпадающих элементов источник/получатель + хэш
								@\CIBlockElement::SetPropertyValuesEx($elementId, $arIBlocksMap[$id], 
											Array(
												$propCodeLinkId => $arSItem["ID"],
												$propCodeHash => $HASH,
												$propCodePictureHash => $HASH_PICTURE,
												$propCodePriceHash => $HASH_PRICE,
												$propCodeQuantityHash => $HASH_QUANTITY,
											));
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
										"action" => "update iblock catalog element",
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
										"action" => "update iblock catalog element",
										"iblock" => $arIBlocksMap[$id],
										"data" => $arData,
										"elementId" => $elementId,
										"element exists" => ($bDestItem ? "Y" : "N"),
										"element updated" => ($bUpdate ? "Y" : "N"),
								), ($ITERATION . "_update_catalog_elements"));
						}
					}
				}
				else
				{
					// новый элемент
					
					if (!empty($arProps))
						$arData["PROPERTY_VALUES"] = $arProps;
					
					$HASH = GetHashProduct($arData, $arFileTypeProperties);
					$HASH_PICTURE = GetHashPicture($arData, $arFileTypeProperties);
					$HASH_PRICE = GetHashPrice($arSItem);
					$HASH_QUANTITY = GetHashQuantity($arSItem);
					
					$arData["PROPERTY_VALUES"][$propCodeId] = $arSItem["ID"];
					$arData["PROPERTY_VALUES"][$propCodeLinkId] = $arSItem["ID"];
					$arData["PROPERTY_VALUES"][$propCodeHash] = $HASH;
					$arData["PROPERTY_VALUES"][$propCodePictureHash] = $HASH_PICTURE;
					$arData["PROPERTY_VALUES"][$propCodePriceHash] = $HASH_PRICE;
					$arData["PROPERTY_VALUES"][$propCodeQuantityHash] = $HASH_QUANTITY;
					
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
						{
							$elementId = $res;
							
							@\CCatalogProduct::Add(Array("ID" => $elementId));
							
							if (!empty($arSItem["PRICES"]))
								UpdateProductPrices($elementId, $arSItem["PRICES"]);
							else
								UpdateProductPrices($elementId, 0);
							
							if (!empty($arSItem["STORES"]))
								UpdateStoreProductQuantity($elementId, $arSItem["STORES"]);
							else
								UpdateStoreProductQuantity($elementId, 0);
						}
						else
						{
							// сбой!!! элемент не создан
							
							LogProcessError(print_r(Array(
																				"time" => date("H:i:s"),
																				"action" => "new iblock catalog element NOT added",
																				"source element ID" => $curItemId,
																				"error" => $el->LAST_ERROR,
																			), true), true);
						}
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
									"action" => "new iblock catalog elelemnt",
									"iblock" => $arIBlocksMap[$id],
									"data" => $arData,
									"elementId" => $elementId,
							), $ITERATION);
						if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
							LogDebugData(
								Array(
									"time" => date("H:i:s"),
									"action" => "new iblock catalog element",
									"iblock" => $arIBlocksMap[$id],
									"data" => $arData,
									"elementId" => $elementId,
							), ($ITERATION . "_new_catalog_elements"));
					}
					else
					{
						// какой-то сбой
						
						LogProcessError(print_r(Array(
																			"time" => date("H:i:s"),
																			"action" => "new iblock catalog element error",
																			"source element ID" => $curItemId,
																		), true), true);
					}
				}
				
				if (!empty($bLinked) && !empty($bSave) && !empty($elementId) && ($bDestItem ? ($bUpdate && $bHashChanged) : true))
				{
					@fputcsv($fhLinked, Array($elementId, serialize($arLinkedProducts)), FIELD_DELIMITER, TEXT_SEPARATOR);
					
					if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
						LogDebugData(
							Array(
								"time" => date("H:i:s"),
								"action" => "element has linked properties",
								"iblock" => $arIBlocksMap[$id],
								"elementId" => $elementId,
						), ($ITERATION . "_linked_elelemnts"));
				}
				
				unset($arProps, $arData, $arLinkedProducts);
				
				// проверка продолжения по времени/полям
				$bProcess = !IsTimeout();
				
				if (!$bProcess)
					break 1;
			}
			
		}
		
		unset($arSource, $arDest, $arLinkProperties, $arLinkedIdMap);
		
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
	
	unset($arSource, $arDest);
	
	$msg = "For Iblock catalog # " . $arIBlocksMap[$id] . " created " . $countNew . " new element(s), updated " . $countUpdate . " element(s).";
	@LogProcessInfo($msg);
}

unset($arSource, $arDest);

// копирование данных iblock catalog закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 13, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblock catalog data recieving finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>