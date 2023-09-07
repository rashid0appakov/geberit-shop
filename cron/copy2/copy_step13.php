<?php

// ШАГ 13 - hlblocks перепривязка связанных полей

@ProcessOn();

$bProcess = false;
$bFinished = true;

clearstatcache();

@LogProcessInfo("Hlblocks linked fields started.", true);

// TODO

$ITERATION = date("His");

// hlblocks перепривязка связанных полей закончена
if ($bFinished)
{
	$arStepParams = Array("STEP" => 14, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Hlblocks linked fields finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>