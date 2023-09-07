<?php

// ШАГ 8 - карта полей hlblocks источник/получатель + списочных вариантов

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
	
	if (file_exists(__DIR__."/".FILE_SOURCE_HLBLOCK_MAP))
		@unlink(__DIR__."/".FILE_SOURCE_HLBLOCK_MAP);
	
	@LogProcessInfo("Hlblocks fields map started.", true);
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
	}
	
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
			
			if (!isset($arHLBlocksFieldMap[$id]))
				$arHLBlocksFieldMap[$id] = Array();
			
			$bItem = false; // флаг обработки поля
			
			// поля источник-получатель
			foreach ($arSource as $sid => $arSItem)
			{
				// пропускаем обработанные
				if ($sid <= $curItemId)
					continue;
				
				$bItem = true;
				$curItemId = $sid;
				
				$bPresent = false; // флаг поля
				$mainFieldCode = $arSItem["FIELD_NAME"]; // основной код поля
				$secondFieldCode = $arSItem["FIELD_NAME"] . HLBLOCK_FIELD_SOURCE_SUFFIX; // проверяем доп.вариант
				$destFieldId = false;
				
				// основное или доп.вариант
				foreach ($arDest as $did => $arDItem)
				{
					// код совпадает - проверка типа и множественности
					if ($mainFieldCode == $arDItem["FIELD_NAME"] || $secondFieldCode == $arDItem["FIELD_NAME"])
					{
						// тип совпадает, множественность получателя >= множественности источника
						if ($arSItem["USER_TYPE_ID"] == $arDItem["USER_TYPE_ID"] && 
								($arSItem["MULTIPLE"] == $arDItem["MULTIPLE"] || "Y" == $arDItem["MULTIPLE"]))
						{
							$bPresent = true;
							$destFieldId = $arDItem["ID"];
							
							$arHLBlocksFieldMap[$id][$arSItem["FIELD_NAME"]] = Array(
									"CODE" => ($mainFieldCode == $arDItem["FIELD_NAME"] ? $mainFieldCode : $secondFieldCode),
									"TYPE" => $arSItem["USER_TYPE_ID"],
									"MULTIPLE" => ("Y" == $arDItem["MULTIPLE"]),
								);
							
							break 1; // найдено - закончить проверку получателя
						}
					}
				}
				
				// данные для списка
				if ($bPresent && !empty($destFieldId) && "enumeration" == $arSItem["USER_TYPE_ID"] && !empty($arSItem["FIELD_ENUM"]))
				{
					// варианты списочного поля
					$arDestValues = Array();
					
					$enum_res = \CUserFieldEnum::GetList(Array(($eby = "ID") => ($eorder = "ASC")), Array("USER_FIELD_ID" => $destFieldId));
					while ($enum_ar_res = $enum_res->GetNext(true, false))
					{
						$arDestValues[$enum_ar_res["ID"]] = $enum_ar_res;
					}
					
					foreach ($arSItem["FIELD_ENUM"] as $val)
					{
						$sourceKey = MakeKey(htmlspecialcharsBack($val["VALUE"]));
						
						foreach ($arDestValues as $destVal)
						{
							$destKey = MakeKey(htmlspecialcharsBack($destVal["VALUE"]));
							
							// наличие варианта по XML_ID или наличие варианта по значению
							if ((!empty($val["XML_ID"]) && !empty($destVal["XML_ID"]) && $val["XML_ID"] == $destVal["XML_ID"]) || 
									$sourceKey == $destKey)
							{
								$arHLBlocksFieldMap[$id][$arSItem["FIELD_NAME"]]["LIST"][$val["ID"]] = $destVal["ID"];
								
								break 1;
							}
						}
					}
				}
				elseif ($bPresent && "hlblock" == $arSItem["USER_TYPE_ID"] && !empty($arSItem["SETTINGS"]["HLBLOCK_ID"])) // данные для привязки к хайлоаду
				{
					$arHLBlocksFieldMap[$id][$arSItem["FIELD_NAME"]]["HLBLOCK_ID"] = $arHLBlocksMap[$arSItem["SETTINGS"]["HLBLOCK_ID"]];
				}
				elseif ($bPresent && ("iblock_section" == $arSItem["USER_TYPE_ID"] || "iblock_element" == $arSItem["USER_TYPE_ID"]) && 
								!empty($arSItem["SETTINGS"]["IBLOCK_ID"])) // данные для привязки к разделу или элементу
				{
					$arHLBlocksFieldMap[$id][$arSItem["FIELD_NAME"]]["IBLOCK_ID"] = $arIBlocksMap[$arSItem["SETTINGS"]["IBLOCK_ID"]];
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
		
		$msg = "Hlblock # " . $arHLBlocksMap[$id] . " fields map info created.";
		@LogProcessInfo($msg);
	}
	
	@file_put_contents(__DIR__."/".FILE_SOURCE_HLBLOCK_MAP, serialize($arHLBlocksFieldMap));
}

unset($arSource, $arDest);

// настройка карты полей hlblocks источник/получатель + списочных вариантов закончена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 9, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Hlblocks fields map finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>