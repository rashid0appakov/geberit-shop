<?php

// ШАГ 10 - карта свойств iblocks источник/получатель + списочных вариантов

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
	
	if (file_exists(__DIR__."/".FILE_SOURCE_IBLOCK_MAP))
		@unlink(__DIR__."/".FILE_SOURCE_IBLOCK_MAP);
	
	@LogProcessInfo("Iblocks properties map started.", true);
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
	}
	
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
			
			if (!isset($arIBlocksPropsMap[$id]))
				$arIBlocksPropsMap[$id] = Array();
			
			$bItem = false; // флаг обработки свойства
			
			// свойства источник-получатель
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				$bItem = true;
				$curItemId = $sid;
				
				$bPresent = false; // флаг свойства
				$mainPropCode = $arSItem["CODE"]; // основной код свойства
				$secondPropCode = $arSItem["CODE"] . IBLOCK_PROPERTY_SOURCE_SUFFIX; // проверяем доп.вариант
				$destPropId = false;
				
				// основное или доп.вариант
				foreach ($arDest as $did => $arDItem)
				{
					// код совпадает - проверка типа и множественности
					if ($mainPropCode == $arDItem["CODE"] || $secondPropCode == $arDItem["CODE"])
					{
						// тип совпадает, множественность получателя >= множественности источника
						if ($arSItem["PROPERTY_TYPE"] == $arDItem["PROPERTY_TYPE"] && 
								($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"] || "Y" == $arDItem["MULTIPLE"]))
						{
							$bPresent = true;
							$destPropId = $arDItem["ID"];
							
							$arIBlocksPropsMap[$id][$arSItem["CODE"]] = Array(
									"CODE" => ($mainPropCode == $arDItem["CODE"] ? $mainPropCode : $secondPropCode),
									"TYPE" => $arSItem["PROPERTY_TYPE"],
									"USER_TYPE" => $arSItem["USER_TYPE"],
									"MULTIPLE" => ("Y" == $arDItem["MULTIPLE"]),
								);
							
							break 1; // найдено - закончить проверку получателя
						}
					}
				}
				
				// данные для списка
				if ($bPresent && !empty($destPropId) && "L" == $arSItem["PROPERTY_TYPE"] && !empty($arSItem["PROPERTY_ENUM"]))
				{
					// варианты списочного свойства
					$arDestValues = Array();
					
					if (!empty($arDest[$destPropId]["PROPERTY_ENUM"]))
						$arDestValues = $arDest[$destPropId]["PROPERTY_ENUM"];
					
					foreach ($arSItem["PROPERTY_ENUM"] as $val)
					{
						$sourceKey = MakeKey(htmlspecialcharsBack($val["VALUE"]));
						
						foreach ($arDestValues as $destVal)
						{
							$destKey = MakeKey(htmlspecialcharsBack($destVal["VALUE"]));
							
							// наличие варианта по XML_ID или наличие варианта по значению
							if ((!empty($val["XML_ID"]) && !empty($destVal["XML_ID"]) && $val["XML_ID"] == $destVal["XML_ID"]) || 
									$sourceKey == $destKey)
							{
								$arIBlocksPropsMap[$id][$arSItem["CODE"]]["LIST"][$val["ID"]] = $destVal["ID"];
								
								break 1;
							}
						}
					}
				}
				elseif ($bPresent && "S" == $arSItem["PROPERTY_TYPE"] && "directory" == $arSItem["USER_TYPE"] && 
								!empty($arSItem["USER_TYPE_SETTINGS"]["HLBLOCK_ID"])) // данные для привязки к хайлоаду
				{
					$arIBlocksPropsMap[$id][$arSItem["CODE"]]["HLBLOCK_ID"] = $arHLBlocksMap[$arSItem["USER_TYPE_SETTINGS"]["HLBLOCK_ID"]];
				}
				elseif ($bPresent && "E" == $arSItem["PROPERTY_TYPE"] && 
								!empty($arSItem["LINK_IBLOCK_ID"])) // данные для привязки к разделу или элементу
				{
					$arIBlocksPropsMap[$id][$arSItem["CODE"]]["IBLOCK_ID"] = $arIBlocksMap[$arSItem["LINK_IBLOCK_ID"]];
				}
				
				@SetStepParams(Array("CURRENT_ITEM_ID" => $curItemId,));
				
				// проверка продолжения по времени/полям
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
		
		$msg = "Iblock # " . $arIBlocksMap[$id] . " properties map info created.";
		@LogProcessInfo($msg);
	}
	
	@file_put_contents(__DIR__."/".FILE_SOURCE_IBLOCK_MAP, serialize($arIBlocksPropsMap));
}

unset($arSource, $arDest);

// настройка карты полей iblocks источник/получатель + списочных вариантов закончена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 11, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Iblocks properties map finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>