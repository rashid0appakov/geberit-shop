<?php

// ШАГ 3 - исходные данные по структуре iblock

global $arSourceIBlockStructureList;

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
	
	@LogProcessInfo("Receiving source iblock structure sections started.", true);
}

$curIndex = intval($arStepParams["CURRENT_IBLOCK_INDEX"]);

// если всё было получено на предыдущей итерации
if (!array_key_exists($curIndex, $arSourceIBlockStructureList))
	$bProcess = false;
else
	$bProcess = true;

if ($bProcess)
{
	$ITERATION = date("His");
	
	foreach ($arSourceIBlockStructureList as $i => $id)
	{
		// пропускаем полученные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
		
		// ATTENTION для каталога структура только нужного раздела
		$bSourceSection = false;
		if (IBLOCK_ID_SOURCE_CATALOG == $id)
			$bSourceSection = true;
		
		$arData = getSourceIBlockStructure($id, $bSourceSection);
		
		if (!empty($arData))
		{
			if (!empty($arData["success"]))
			{
				if (!empty($arData["data"]))
				{
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_STRUCTURE.$id, serialize($arData["data"]));
					
					if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
						LogDebugData($arData["data"], $ITERATION);
					
					$msg = "For iblock id=" . $id . " received info for " . count($arData["data"]) . " structure section(s)";
					
					LogProcessInfo($msg, true);
				}
				else
				{
					$msg = "For iblock id=" . $id . " received no info for structure section(s).";
					
					LogProcessInfo($msg, true);
				}
			}
			else
			{
				if (empty($arData["message"]))
					$err = "Unknown error for iblock id=" . $id . " request for structure section(s).";
				else
					$err = implode("\r\n".PHP_EOL, $arData["message"]);
				
				LogOutString($err);
				LogProcessError($err, true);
			}
		}
		else
		{
			$err = "Empty response for iblock id=" . $id . " request for structure section(s).";
			
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
			foreach ($arSourceIBlockStructureList as $j => $tmp)
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
				
				$curIndex = PHP_INT_MAX;
				@SetStepParams(Array("CURRENT_IBLOCK_INDEX" => $curIndex,));
			}
			
			break 1;
		}
		else
		{
			$bFinished = true; // если последний элемент
		}
		
		unset($arData);
		
	}
	
}

unset($arData);

// получение исходных данных по структуре iblocks закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 4, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Receiving source iblocks structure sections finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>