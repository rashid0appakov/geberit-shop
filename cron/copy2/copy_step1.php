<?php

// ШАГ 1 - исходные данные по полям hlblocks

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
	
	@LogProcessInfo("Receiving source hlblocks feilds started.", true);
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
	
	foreach ($arSourceHLBlockList as $i => $id)
	{
		// пропускаем полученные
		if ($i < $curIndex)
			continue;
		
		$bFinished = false;
		$curIndex = $i;
		@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex,));
		
		$arData = getSourceHLBlockFields($id);
		
		if (!empty($arData))
		{
			if (!empty($arData["success"]))
			{
				if (!empty($arData["data"]))
				{
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_FIELDS.$id, serialize($arData["data"]));
					
					if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
						LogDebugData($arData["data"], $ITERATION);
					
					$msg = "For hlblock id=" . $id . " received info for " . count($arData["data"]) . " field(s).";
					
					LogProcessInfo($msg, true);
				}
				else
				{
					$msg = "For hlblock id=" . $id . " received no info for fields.";
					
					LogProcessInfo($msg, true);
				}
			}
			else
			{
				if (empty($arData["message"]))
					$err = "Unknown error for hlblock id=" . $id . " request for fields.";
				else
					$err = implode("\r\n".PHP_EOL, $arData["message"]);
				
				LogOutString($err);
				LogProcessError($err, true);
			}
		}
		else
		{
			$err = "Empty response for hlblock id=" . $id . " request for fields.";
			
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
				@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex,));
			}
			else
			{
				$bFinished = true;
				
				// список hlblocks пройден - на всякий случай
				$curIndex = PHP_INT_MAX;
				@SetStepParams(Array("CURRENT_HLBLOCK_INDEX" => $curIndex,));
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

// получение исходных данных по полям hlblocks закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 2, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Receiving source hlblocks feilds finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>