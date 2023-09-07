<?php

// ШАГ 7 - структура iblock

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
	
	@LogProcessInfo("Creating/updating iblock structure sections started.", true);
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
		
		$res = SourceCatalogSections2Dest($id, true);
		
		if (empty($res))
		{
			$err = "Unknown error for iblock id=" . $id . ", structure section(s) not created/updated.";
			
			LogOutString($err);
			LogProcessError($err, true);
		}
		else
		{
			$msg = "For iblock id=" . $id . " structure section(s) created/updated.";
			
			LogProcessInfo($msg, true);
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
		
	}
	
}

// создание/обновление структуры iblocks закончено
if ($bFinished)
{
	$arStepParams = Array("STEP" => 8, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Creating/updating iblock structure sections finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>