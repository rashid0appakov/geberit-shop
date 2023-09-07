<?php

// ШАГ 2 - исходные данные по свойствам iblocks

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
	
	@LogProcessInfo("Receiving source iblocks props started.", true);
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
		// пропускаем полученные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
		
		// ATTENTION для каталога свойства только нужной категории
		$bPropCategory = false;
		if (IBLOCK_ID_SOURCE_CATALOG == $id)
			$bPropCategory = true;
		
		$arData = getSourceIBlockProps($id, $bPropCategory);
		
		if (!empty($arData))
		{
			if (!empty($arData["success"]))
			{
				if (!empty($arData["data"]))
				{
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_PROPERTIES.$id, serialize($arData["data"]));
					
					if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
						LogDebugData($arData["data"], $ITERATION);
					
					$msg = "For iblock id=" . $id . " received info for " . count($arData["data"]) . " property(ies).";
					
					LogProcessInfo($msg, true);
				}
				else
				{
					$msg = "For iblock id=" . $id . " received no info for properties.";
					
					LogProcessInfo($msg, true);
				}
			}
			else
			{
				if (empty($arData["message"]))
					$err = "Unknown error for iblock id=" . $id . " request for properties.";
				else
					$err = implode("\r\n".PHP_EOL, $arData["message"]);
				
				LogOutString($err);
				LogProcessError($err, true);
			}
		}
		else
		{
			$err = "Empty response for iblock id=" . $id . " request for properties.";
			
			LogOutString($err);
			LogProcessError($err, true);
		}
		
		// продолжать итерацию
		$bProcess = !IsTimeout();
		
		if (!$bProcess)
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
				@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
			}
			else
			{
				$bFinished = true;
				
				// список iblocks пройден - на всякий случай
				$curIndex = PHP_INT_MAX;
				@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
			}
			
			break 1;
		}
		else
		{
			$bFinished = true; // если последний элемент - перебор завершится с выставленным флагом
		}
		
		unset($arData);
	}
	
}

unset($arData);

// получение исходных данных по полям iblocks закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 3, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Receiving source iblocks properties finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>