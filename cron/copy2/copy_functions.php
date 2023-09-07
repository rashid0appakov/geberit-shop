<?php

// для работы с HL
if (!function_exists("GetEntityDataClass"))
{
	function GetEntityDataClass($HlBlockId = false)
	{
		$result = false;
		
		if (!empty($HlBlockId) && ($HlBlockId = intval($HlBlockId)) > 0)
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HlBlockId)->fetch();
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
			$entity_data_class = $entity->getDataClass();
			
			$result = $entity_data_class;
		}
		
		return $result;
	}
}

// активность тестового режима
function IsTestMode()
{
	$result = false;
	
	if (defined("TEST_MODE") && "Y" == TEST_MODE)
		$result = true;
	
	return $result;
}

// логирование строки в вывод
function LogOutString($string = false)
{
	if (!empty($string))
		echo date(DATE_ATOM) . " " . trim($string) . PHP_EOL;
	else
		echo "\r\n" . PHP_EOL;
}

// проверка достижения максимального времени итерации
function IsTimeout()
{
	global $phpMaxExecutionTime, $start;
	
	$maxTime = D_TIMEOUT;
	
	if (0 < $phpMaxExecutionTime && $phpMaxExecutionTime < D_TIMEOUT)
		$maxTime = $phpMaxExecutionTime - 5;
	
	return (microtime(true) - $start) >= $maxTime;
}

// выставление флага выполнения итерации
function ProcessOn()
{
	$result = false;
	
	if (!file_exists(__DIR__."/copy_process"))
	{
		$result = file_put_contents(__DIR__."/copy_process", strval(microtime(true))) ? true : false;
	}
	
	return $result;
}

// снятие флага выполнения итерации
function ProcessOff()
{
	$result = false;
	
	if (file_exists(__DIR__."/copy_process"))
	{
		$result = unlink(__DIR__."/copy_process");
	}
	
	return $result;
}

// проверка состояния выполнения итерации
function IsProcessOn()
{
	$result = file_exists(__DIR__."/copy_process");
	
	return $result;
}

// чтение параметров шага
function GetStepParams()
{
	$result = Array();
	
	if (file_exists(__DIR__."/copy_step"))
	{
		$params = unserialize(file_get_contents(__DIR__."/copy_step"));
		
		if (!empty($params) && is_array($params))
			$result = $params;
	}
	
	return $result;
}

// сохранение параметров шага
function SaveStepParams()
{
	global $arStepParams;
	
	$result = false;
	
	if (!empty($arStepParams) && is_array($arStepParams))
		$result = file_put_contents(__DIR__."/copy_step", serialize($arStepParams)) ? true : false;
	
	return $result;
}

// установка и сохранение параметров шага
function SetStepParams($arParams = Array())
{
	global $arStepParams;
	
	$result = false;
	
	if (!empty($arParams) && is_array($arParams))
	{
		if (!is_array($arStepParams))
			$arStepParams = Array();
		
		if (empty($arStepParams))
			$arStepParams = $arParams;
		else
			$arStepParams = array_merge($arStepParams, $arParams);
		
		$result = SaveStepParams();
	}
	
	return $result;
}

// строка из букв/цифр
function DistillString($str = false)
{
	$result = $str;
	
	if (!empty($str))
		$result = preg_replace("/[^A-Za-z0-9А-ЯЁа-яё]/u", "", $str);
	
	return $result;
}

// аварийное окончание
function AbortProcess($err = false)
{
	global $arStepParams;
	
	if (empty($err))
		$err = "Process aborted!";
	
	LogOutString($err);
	LogProcessError($err, true);
	
	$arStepParams = Array("STEP" => false, "PROCESS" => false,);
	@SaveStepParams();
	
	
	@DeleteProcessFiles();
	
	@ProcessOff();
}

// штатное окончание
function FinishProcess($msg = false)
{
	global $arStepParams;
	
	if (empty($msg))
		$msg = "Copy finished!";
	
	LogOutString($msg);
	LogProcessInfo($msg, true);
	
	$arStepParams = Array("STEP" => false, "PROCESS" => false,);
	@SaveStepParams();
	
	@DeleteProcessFiles();
	
	@ProcessOff();
}

// перезапуск 
function ResetProcess()
{
	@DeleteProcessFiles();
}

// удаление всех временных файлов и файлов с данными
function DeleteProcessFiles()
{
	global $arSourceHLBlockList, $arSourceIBlockList;
	
	$arSourceHLBlockFilesPrefix = Array(
			FILE_PREFIX_SOURCE_HLBLOCK_FIELDS,
			FILE_PREFIX_SOURCE_HLBLOCK_DATA,
			FILE_PREFIX_SOURCE_HLBLOCK_LINKED,
		);
	
	foreach ($arSourceHLBlockList as $id)
	{
		foreach ($arSourceHLBlockFilesPrefix as $prefix)
		{
			$file = __DIR__."/".$prefix.$id;
			
			if (file_exists($file))
				@unlink($file);
		}
	}
	
	$arSourceIBlockFilesPrefix = Array(
			FILE_PREFIX_SOURCE_IBLOCK_PROPERTIES,
			FILE_PREFIX_SOURCE_IBLOCK_STRUCTURE,
			FILE_PREFIX_SOURCE_IBLOCK_DATA,
			FILE_PREFIX_SOURCE_IBLOCK_LINKED,
			FILE_PREFIX_SOURCE_IBLOCK_SET,
		);
	
	foreach ($arSourceIBlockList as $id)
	{
		foreach ($arSourceIBlockFilesPrefix as $prefix)
		{
			$file = __DIR__."/".$prefix.$id;
			
			if (file_exists($file))
				@unlink($file);
		}
	}
	
	$arProcessFiles = Array(
			FILE_SOURCE_HLBLOCK_MAP,
			FILE_SOURCE_IBLOCK_MAP,
			FILE_SOURCE_IBLOCK_SECTION_MAP,
		);
	
	foreach ($arProcessFiles as $prefix)
	{
		$file = __DIR__."/".$prefix;
		
		if (file_exists($file))
			@unlink($file);
	}
	
	unset($arSourceHLBlockFilesPrefix, $arSourceIBlockFilesPrefix, $arProcessFiles, $id, $prefix, $file);
}

// продложить после сбоя если возможно
function ForceProcess()
{
	global $phpMaxExecutionTime;
	
	$result = false;
	
	$maxTime = D_TIMEOUT;
	
	if (0 < $phpMaxExecutionTime && $phpMaxExecutionTime < D_TIMEOUT)
		$maxTime = $phpMaxExecutionTime + 60;
	else
		$maxTime = D_TIMEOUT + 60;
	
	if (file_exists(__DIR__."/copy_process"))
	{
		$prev = floatval(file_get_contents(__DIR__."/copy_process"));
		$current = microtime(true);
		
		if (0 < $prev && ($current - $prev >= $maxTime))
		{
			$result = unlink(__DIR__."/copy_process");
		}
	}
	
	return $result;
}

// ----------------------------------------------

// логирование данных
function LogProcessData($data = false, $iteration = false)
{
	global $arStepParams;
	
	$logFile = "/data_".date("Ymd");
	
	if (!empty($arStepParams["STEP"]))
		$logFile .= "_" . $arStepParams["STEP"];
	
	if (!empty($iteration))
		$logFile .= "_" . $iteration;
	
	$logFile .= ".log";
	
	file_put_contents(__DIR__.$logFile, json_encode($data, JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
}

// логирование процесса
function LogProcessInfo($msg = false, $time = false)
{
	if (!empty($msg))
		file_put_contents(__DIR__."/info_".date("Ymd").".log", (empty($time)?"":(date(DATE_ATOM).": ")).strval($msg)."\r\n", FILE_APPEND);
}

// логирование ошибок
function LogProcessError($error = false, $time = false)
{
	if (!empty($error))
		file_put_contents(__DIR__."/error_".date("Ymd").".log", (empty($time)?"":(date(DATE_ATOM).": ")).strval($error)."\r\n".PHP_EOL, FILE_APPEND);
}

// логирование отладочных данных
function LogDebugData($data = false, $iteration = false)
{
	global $arStepParams;
	
	$logFile = "/debug_".date("Ymd");
	
	if (!empty($arStepParams["STEP"]))
		$logFile .= "_" . $arStepParams["STEP"];
	
	if (!empty($iteration))
		$logFile .= "_" . $iteration;
	
	$logFile .= ".log";
	
	file_put_contents(__DIR__.$logFile, print_r($data, true)."\r\n\r\n".PHP_EOL, FILE_APPEND);
}

// логирование данных обмена по REST
function LogRestData($data = false, $iteration = false)
{
	global $arStepParams;
	
	$logFile = "/rest_".date("Ymd");
	
	if (!empty($arStepParams["STEP"]))
		$logFile .= "_" . $arStepParams["STEP"];
	
	if (!empty($iteration))
		$logFile .= "_" . $iteration;
	
	$logFile .= ".log";
	
	file_put_contents(__DIR__.$logFile, print_r($data, true)."\r\n".PHP_EOL, FILE_APPEND);
}


// символьный код
function MakeCode($str = false)
{
	global $arTranslitParams;
	
	$result = false;
	
	if (!empty($str))
		$result = \CUtil::translit($str, "ru", $arTranslitParams);
	
	return $result;
}

// строковый ключ
function MakeKey($str = false)
{
	$result = false;
	
	if (!empty($str))
		$result = mb_strtolower(DistillString($str));
	
	return $result;
}

// ----------------------------------------------

function getRESTToken()
{
	$result = date("Ymd");
	
	$result .= md5($result.REST_SALT);
	
	return $result;
}

// получение данных по url
function getSourceDataByURL($url = false)
{
	$result = false;
	
	if (!empty($url))
	{
		$opts = Array(
				"http" => Array(
					"method" => "POST",
				)
			);
		$context = stream_context_create($opts);
		
		$response = file_get_contents($url, false, $context);
		if (!empty($response))
		{
			$result = json_decode($response, true);
		
			if (empty($result) || !is_array($result))
				$result = false;
		}
		
		unset($opts, $context, $response);
	}
	
	return $result;
}

// данные по полям hlblocks
function getSourceHLBlockFields($id = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_hlfields&hlblock_id=" . intval($id);
		
		$result = getSourceDataByURL($url);
		
		unset($url);
	}
	
	return $result;
}

// данные по свойствам iblocks
function getSourceIBlockProps($id = false, $bCategory = false)
{
	global $arIBlocksPropsCategory;
	
	$result = false;
	
	if (intval($id) > 0)
	{
		$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_ibprops&iblock_id=" . intval($id);
		
		if ($bCategory && isset($arIBlocksPropsCategory[$id]) && !empty($arIBlocksPropsCategory[$id]))
			$url .= "&category=" . $arIBlocksPropsCategory[$id];
		
		$result = getSourceDataByURL($url);
		
		unset($url);
	}
	
	return $result;
}

// структура iblock
function getSourceIBlockStructure($id = false, $bSection = false)
{
	global $arSourceIBlockStructureSectionList;
	
	$result = false;
	
	if (intval($id) > 0)
	{
		$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_structure&iblock_id=" . intval($id);
		
		if ($bSection && isset($arSourceIBlockStructureSectionList[$id]) && intval($arSourceIBlockStructureSectionList[$id]) > 0)
			$url .= "&section_id=" . intval($arSourceIBlockStructureSectionList[$id]);
		
		$result = getSourceDataByURL($url);
		
		unset($url);
	}
	
	return $result;
}

// данные hlblocks
function getSourceHLBlockData($id = false, $item_id = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_hldata&hlblock_id=" . intval($id) . 
				"&item_id=" . (intval($item_id) > 0 ? intval($item_id) : 0);
		
		$result = getSourceDataByURL($url);
		
		unset($url);
	}
	
	return $result;
}

// данные iblocks
function getSourceIBlockData($id = false, $item_id = false, $active = false, $filter = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_ibdata&iblock_id=" . intval($id) . 
				"&item_id=" . (intval($item_id) > 0 ? intval($item_id) : 0);
		
		if (!empty($active))
		{
			$active = strtoupper(trim($active));
			
			if ("Y" == $active || "N" == $active)
				$url .= "&active=" . $active;
		}
		
		if (!empty($filter) && is_array($filter))
		{
			$tmp = Array();
			
			foreach ($filter as $p => $v)
			{
				if (!empty($p) && !empty($v))
					$tmp[$p] = $v;
			}
			
			if (!empty($tmp))
				$url .= "&" . http_build_query(Array("filter" => $tmp));
		}
		
		$result = getSourceDataByURL($url);
		
		unset($url);
	}
	
	return $result;
}

// данные sets/groups catalog
function getSourceCatalogProductSetData($id = false, $item_id = false)
{
	global $arIBlocksMap;
	
	$result = false;
	
	if (intval($id) > 0)
	{
		$item_id = intval($item_id) > 0 ? intval($item_id) : 0;
		
		$limit = IBLOCK_LIMIT;
		
		if (defined("UPDATE_DEST_CATALOG_ITEMS") && "Y" == UPDATE_DEST_CATALOG_ITEMS)
			$keyProperty = "PROPERTY_" . IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX;
		else
			$keyProperty = "PROPERTY_" . IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX;
		
		$keyPropertyValue = $keyProperty . "_VALUE";
		
		$bReceived = false;
		
		do {
			$arID = Array();
			
			$res = \CIBlockElement::GetList(
					Array($keyProperty => "ASC,NULLS"),
					Array(
						"IBLOCK_ID" => $arIBlocksMap[$id],
						">".$keyProperty => $item_id,
					),
					false, Array("nTopCount" => $limit),
					Array("ID", "IBLOCK_ID", $keyProperty)
				);
			
			while ($ar_res = $res->Fetch())
			{
				$ar_res[$keyPropertyValue] = intval($ar_res[$keyPropertyValue]);
				
				if (0 < $ar_res[$keyPropertyValue])
					$arID[$ar_res[$keyPropertyValue]] = $ar_res[$keyPropertyValue];
			}
			
			if (!empty($arID))
			{
				@sort($arID);
				
				$url = REST_URL . "?client=" . REST_CLIENT . "&token=" . getRESTToken() . "&action=get_cpset&iblock_id=" . IBLOCK_ID_SOURCE_CATALOG . 
						"&item_id=" . implode(",", $arID);
				
				$result = getSourceDataByURL($url);
				
				if (!empty($result) && !empty($result["success"]))
				{
					if (!empty($result["data"]))
					{
						$bReceived = true;
					}
					else
					{
						$lastId = end($arID);
						
						if (empty($lastId))
							$item_id += $limit;
						else
							$item_id = $lastId;
					}
				}
				else
				{
					$bReceived = true;
				}
			}
			else
			{
				$bReceived = true;
			}
		} while (!$bReceived);
		
		unset($url, $limit, $keyProperty, $keyPropertyValue, $arID, $res, $ar_res);
	}
	
	return $result;
}

// данные hlblock для шага
function getStepDataHLBlock($id = false, $item_id = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$id = intval($id);
		$item_id = intval($item_id) > 0 ? intval($item_id) : 0;
		
		$arSource = false;
		
		// данные были получены и сохранены в файле
		if (file_exists(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_DATA.$id))
		{
			$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_DATA.$id);
			
			if (!empty($arSource))
				$arSource = unserialize($arSource);
		}
		else // полученых и сохранённых данных нет - получаем и сохраняем
		{
			$res = getSourceHLBlockData($id, $item_id);
			
			if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
			{
				$arSource = $res["data"];
				
				@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_DATA.$id, serialize($arSource));
			}
		}
		
		// данные есть - продолжение или следующая порция
		if (!empty($arSource))
		{
			$arLast = end($arSource);
			
			// продолжение
			if ($arLast["ID"] > $item_id)
			{
				$result = $arSource;
			}
			else // получить следующую порцию
			{
				$res = getSourceHLBlockData($id, $item_id);
				
				if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
				{
					$result = $res["data"];
					
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_HLBLOCK_DATA.$id, serialize($result));
				}
			}
		}
	}
	
	unset($arSource, $res, $arLast);
	
	return $result;
}

// данные iblock для шага
function getStepDataIBlock($id = false, $item_id = false, $active = false, $filter = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$id = intval($id);
		$item_id = intval($item_id) > 0 ? intval($item_id) : 0;
		
		$arSource = false;
		
		// данные были получены и сохранены в файле
		if (file_exists(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_DATA.$id))
		{
			$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_DATA.$id);
			
			if (!empty($arSource))
				$arSource = unserialize($arSource);
		}
		else // полученых и сохранённых данных нет - получаем и сохраняем
		{
			$res = getSourceIBlockData($id, $item_id, $active, $filter);
			
			if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
			{
				$arSource = $res["data"];
				
				@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_DATA.$id, serialize($arSource));
			}
		}
		
		// данные есть - продолжение или следующая порция
		if (!empty($arSource))
		{
			$arLast = end($arSource);
			
			// продолжение
			if ($arLast["ID"] > $item_id)
			{
				$result = $arSource;
			}
			else // получить следующую порцию
			{
				$res = getSourceIBlockData($id, $item_id, $active, $filter);
				
				if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
				{
					$result = $res["data"];
					
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_DATA.$id, serialize($result));
				}
			}
		}
	}
	
	unset($arSource, $res, $arLast);
	
	return $result;
}

// данные iblock для шага
function getStepDataCatalogProductSet($id = false, $item_id = false)
{
	$result = false;
	
	if (intval($id) > 0)
	{
		$id = intval($id);
		$item_id = intval($item_id) > 0 ? intval($item_id) : 0;
		
		$arSource = false;
		
		// данные были получены и сохранены в файле
		if (file_exists(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_SET.$id))
		{
			$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_SET.$id);
			
			if (!empty($arSource))
				$arSource = unserialize($arSource);
		}
		else // полученых и сохранённых данных нет - получаем и сохраняем
		{
			$res = getSourceCatalogProductSetData($id, $item_id);
			
			if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
			{
				$arSource = $res["data"];
				
				@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_SET.$id, serialize($arSource));
			}
		}
		
		// данные есть - продолжение или следующая порция
		if (!empty($arSource))
		{
			$arLast = end($arSource);
			
			// продолжение
			if ($arLast["ITEM_ID"] > $item_id)
			{
				$result = $arSource;
			}
			else // получить следующую порцию
			{
				$res = getSourceCatalogProductSetData($id, $item_id);
				
				if (!empty($res) && !empty($res["success"]) && !empty($res["data"]))
				{
					$result = $res["data"];
					
					@file_put_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_SET.$id, serialize($result));
				}
			}
		}
	}
	
	unset($arSource, $res, $arLast);
	
	return $result;
}

// ----------------------------------------------

// разделы источника в получателе
function SourceCatalogSections2Dest($ibid = false, $bFile = false)
{
	global $arIBlocksMap, $arDestIBlockStructureSectionList;
	
	$result = false;
	
	$ibid = intval($ibid);
	
	if (0 < $ibid && isset($arIBlocksMap[$ibid]))
	{
		$arSource = Array();
		$arDest = Array();
		
		// данные из файла или по запросу
		if ($bFile)
		{
			$arSource = file_get_contents(__DIR__."/".FILE_PREFIX_SOURCE_IBLOCK_STRUCTURE.$ibid);
			
			if (!empty($arSource))
			{
				$arSource = unserialize($arSource);
			}
			
			if (empty($arSource))
				$arSource = Array();
		}
		else
		{
			// ATTENTION для каталога структура только нужного раздела
			$bSourceSection = false;
			if (IBLOCK_ID_SOURCE_CATALOG == $ibid)
				$bSourceSection = true;
			
			$arSource = getSourceIBlockStructure($ibid, $bSourceSection);
			
			if (!empty($arSource["success"]) && !empty($arSource["data"]))
				$arSource = $arSource["data"];
			else
				$arSource = Array();
		}
		
		// для сопоставления уровней источника и получателя
		$levelDiff = 0;
		
		// данные по структуре получателя
		if (!empty($arSource))
		{
			// по умолчанию структура переносится в корень
			$destSectionMapId = false;
			
			if (!empty($arDestIBlockStructureSectionList[$arIBlocksMap[$ibid]]))
				$destSectionMapId = $arDestIBlockStructureSectionList[$arIBlocksMap[$ibid]];
			
			$arFilter = Array(
					"IBLOCK_ID" => $arIBlocksMap[$ibid],
				);
			
			if (!empty($destSectionMapId))
			{
				$rsDestSection = \CIBlockSection::GetByID($destSectionMapId);
				
				if ($arDestSection = $rsDestSection->GetNext(true, false))
				{
					$arFilter[">LEFT_MARGIN"] = $arDestSection["LEFT_MARGIN"];
					$arFilter["<RIGHT_MARGIN"] = $arDestSection["RIGHT_MARGIN"];
					$arFilter[">DEPTH_LEVEL"] = $arDestSection["DEPTH_LEVEL"];
					
					$levelDiff = $arDestSection["DEPTH_LEVEL"];
					
					$arDestSection["KEY"] = MakeKey($arDestSection["NAME"]);
					
					$arDest[$arDestSection["ID"]] = $arDestSection;
				}
			}
			
			$res = \CIBlockSection::GetList(
					Array("left_margin" => "asc"), 
					$arFilter,
					false,
					Array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "UF_SOURCE_SECT_ID",)
				);
			while ($ar_res = $res->Fetch())
			{
				$ar_res["KEY"] = MakeKey($ar_res["NAME"]);
				$arDest[$ar_res["ID"]] = $ar_res;
			}
		}
		
		if (!empty($arSource))
		{
			$bs = new \CIBlockSection;
			
			foreach ($arSource as $sid => $arSItem)
			{
				$bFound = false;
				$arCur = Array();
				
				foreach ($arDest as $did => $arDItem)
				{
					// уровень и ключ или ID должны быть идентичны
					if (($arSItem["DEPTH_LEVEL"] + $levelDiff) == $arDItem["DEPTH_LEVEL"] &&
							($arSItem["KEY"] == $arDItem["KEY"] || $arSItem["ID"] == $arDItem["UF_SOURCE_SECT_ID"]))
					{
						$bFound = true;
						$arCur = $arDItem;
						
						break 1;
					}
				}
				
				// раздела ещё нет - найти родительский и создать
				if (!$bFound)
				{
					$bNew = false;
					
					$arNew = Array(
							"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
							"NAME" => $arSItem["NAME"],
							"ACTIVE" => $arSItem["ACTIVE"],
							"SORT" => $arSItem["SORT"],
							"CODE" => $arSItem["CODE"],
							"UF_SOURCE_SECT_ID" => $arSItem["ID"],
							"IBLOCK_SECTION_ID" => false,
						);
					
					if ($arSItem["DEPTH_LEVEL"] != 1 && !empty($arSItem["IBLOCK_SECTION_ID"]) && !empty($arSource[$arSItem["IBLOCK_SECTION_ID"]]))
					{
						// необходимо создать подраздел
						$destParentId = false;
						
						foreach ($arDest as $did => $arDItem)
						{
							if (($arSource[$arSItem["IBLOCK_SECTION_ID"]]["DEPTH_LEVEL"] + $levelDiff) == $arDItem["DEPTH_LEVEL"] && 
									($arSource[$arSItem["IBLOCK_SECTION_ID"]]["KEY"] == $arDItem["KEY"] || $arSource[$arSItem["IBLOCK_SECTION_ID"]]["ID"] == $arDItem["UF_SOURCE_SECT_ID"]))
							{
								$destParentId = $arDItem["ID"];
								
								break 1;
							}
						}
						
						if (!empty($destParentId))
						{
							$bNew = true;
							
							$arNew["IBLOCK_SECTION_ID"] = $destParentId;
						}
					}
					elseif ($arSItem["DEPTH_LEVEL"] == 1)
					{
						// необходимо создать корневой раздел
						$bNew = true;
						
						$arNew["IBLOCK_SECTION_ID"] = $destSectionMapId;
					}
					
					if ($bNew)
					{
						if (!IsTestMode())
							$newID = $bs->Add($arNew);
						else
							$newID = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима !!!
						
						if (!empty($newID))
						{
							$arNew["ID"] = $newID;
							$arNew["KEY"] = MakeKey($arNew["NAME"]);
							$arNew["DEPTH_LEVEL"] = $arSItem["DEPTH_LEVEL"] + $levelDiff;
						
							$arDest[$newID] = $arNew;
						}
					}
				}
				elseif (!empty($arCur))
				{
					// если есть изменения - сохранить
					if ($arCur["NAME"] != $arSItem["NAME"] ||
							$arCur["ACTIVE"] != $arSItem["ACTIVE"] ||
							$arCur["SORT"] != $arSItem["SORT"])
					{
						$arFields = Array(
								"NAME" => $arSItem["NAME"],
								"ACTIVE" => $arSItem["ACTIVE"],
								"SORT" => $arSItem["SORT"],
								"CODE" => $arSItem["CODE"],
							);
						
						if (!IsTestMode())
							@$bs->Update($arCur["ID"], $arFields);
						
						$arDest[$arCur["ID"]]["NAME"] = $arSItem["NAME"];
						$arDest[$arCur["ID"]]["KEY"] = MakeKey($arSItem["NAME"]);
					}
				}
			}
			
			$result = true;
		}
		
	}
	
	return $result;
}

// заполнить данные разделов источник/получатель
function FillSectionsMap()
{
	global $arSourceIBlockStructureList, $arIBlocksMap, $arDestIBlockStructureSectionList, $arSectionsMap;
	$arSectionsMap = Array();
	
	if (file_exists(__DIR__."/".FILE_SOURCE_IBLOCK_SECTION_MAP))
	{
		$map = file_get_contents(__DIR__."/".FILE_SOURCE_IBLOCK_SECTION_MAP);
		
		if (!empty($map))
			$arSectionsMap = unserialize($map);
	}
	
	if (empty($arSectionsMap))
	{
		foreach ($arDestIBlockStructureSectionList as $ibId => $sectId)
		{
			$sourceId = array_search($ibId, $arIBlocksMap);
			
			if (false !== $sourceId)
			{
				$rsTargetSection = \CIBlockSection::GetByID($sectId);
				
				if ($arTargetSection = $rsTargetSection->GetNext(true, false))
				{
					$arMap = Array();
					
					$res = \CIBlockSection::GetList(
							Array("left_margin" => "asc"), 
							Array(
								"IBLOCK_ID" => $arTargetSection["IBLOCK_ID"],
								">LEFT_MARGIN" => $arTargetSection["LEFT_MARGIN"],
								"<RIGHT_MARGIN" => $arTargetSection["RIGHT_MARGIN"],
								">DEPTH_LEVEL" => $arTargetSection["DEPTH_LEVEL"],
							),
							false,
							Array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID", "UF_DEST_SECTION", "UF_SOURCE_SECT_ID")
						);
					while ($ar_res = $res->Fetch())
					{
						if (!empty($ar_res["UF_SOURCE_SECT_ID"]))
						{
							$arMap[$ar_res["UF_SOURCE_SECT_ID"]] = empty($ar_res["UF_DEST_SECTION"]) ? $ar_res["ID"] : $ar_res["UF_DEST_SECTION"];
						}
					}
					
					if (!empty($arMap))
						$arSectionsMap[$sourceId] = $arMap;
				}
				
			}
		}
	}
	
	if (!empty($arSectionsMap))
		@file_put_contents(__DIR__."/".FILE_SOURCE_IBLOCK_SECTION_MAP, serialize($arSectionsMap));
	
	unset($map, $arMap, $res, $ar_res);
}

// заполнить карту справочников/hlblocks
function FillHLReferencesMap()
{
	global $arHLReferencesMap;
	$arHLReferencesMap = Array();
	
}

// заполнить карту справочников/iblocks
function FillIBReferencesMap()
{
	global $arIBReferencesMap;
	$arIBReferencesMap = Array();
	
}

// обновить количество товара на складах и в каталоге
function UpdateStoreProductQuantity($productId = false, $quantity = 0)
{
	global $arStoresMap;
	
	if (empty($quantity))
		$quantity = 0;
	elseif (!is_array($quantity))
	{
		$quantity = intval($quantity);
		
		if (0 > $quantity)
			$quantity = 0;
	}
	
	if (intval($productId) > 0)
	{
		foreach ($arStoresMap as $sourceStoreId => $destStoreId)
		{
			$storeProductId = false;
			
			$res = \CCatalogStoreProduct::GetList(
					Array("ID"), 
					Array("PRODUCT_ID" => $productId, "STORE_ID" => $destStoreId), 
					false, false, 
					Array()
				);
			if ($ar_res = $res->Fetch())
				$storeProductId = $ar_res["ID"];
			
			if(!empty($storeProductId))
			{
				if (is_array($quantity))
				{
					$newQuantity = 0;
					
					foreach ($quantity as $storeId => $storeQuantity)
					{
						if ($storeId == $sourceStoreId)
						{
							$newQuantity = $storeQuantity;
							
							break 1;
						}
					}
				}
				else
					$newQuantity = $quantity;
				
				if (!IsTestMode())
				{
					@\CCatalogStoreProduct::Update(
							$storeProductId, 
							Array(
								"PRODUCT_ID" => $productId,
								"STORE_ID" => $destStoreId,
								"AMOUNT" => $newQuantity,
							)
						);
				}
			}
			else
			{
				if (is_array($quantity))
				{
					$newQuantity = 0;
					
					foreach ($quantity as $storeId => $storeQuantity)
					{
						if ($storeId == $sourceStoreId)
						{
							$newQuantity = $storeQuantity;
							
							break 1;
						}
					}
				}
				else
					$newQuantity = $quantity;
				
				if (!IsTestMode())
				{
					@\CCatalogStoreProduct::Add(
							Array(
								"PRODUCT_ID" => $productId,
								"STORE_ID" => $destStoreId,
								"AMOUNT" => $newQuantity,
							)
						);
				}
			}
		}
		
		// количество в каталоге
		if (is_array($quantity))
		{
			$newQuantity = 0;
			
			foreach ($quantity as $storeId => $storeQuantity)
			{
				if (0 <= $storeQuantity)
				{
					$newQuantity = $storeQuantity;
					
					break 1;
				}
			}
		}
		else
			$newQuantity = $quantity;
		
		@\CCatalogProduct::Update($productId, Array("QUANTITY" => $newQuantity));
		
		unset($sourceStoreId, $destStoreId, $res, $ar_res, $storeProductId, $storeId, $storeQuantity, $newQuantity);
	}
}

// обновить цены товара
function UpdateProductPrices($productId = false, $price = false)
{
	global $arPricesMap;
	
	if (empty($price))
		$price = 0;
	elseif (!is_array($price))
	{
		$price = floatval($price);
		
		if (0 > $price)
			$price = 0;
	}
	
	if (!empty($productId))
	{
		foreach ($arPricesMap as $sourcePriceId => $destPriceId)
		{
			$res = \CPrice::GetList(
					Array(),
					Array(
						"PRODUCT_ID" => $productId,
						"CATALOG_GROUP_ID" => $destPriceId,
					)
				);
			if ($ar_res = $res->Fetch())
			{
				if (is_array($price))
				{
					$newPrice = 0;
					
					foreach ($price as $code => $arPrice)
					{
						if ($sourcePriceId == $arPrice["PRICE_ID"])
						{
							$newPrice = $arPrice["VALUE"];
							
							break 1;
						}
					}
				}
				else
					$newPrice = $price;
				
				if (!IsTestMode())
				{
					@\CPrice::Update(
							$ar_res["ID"], 
							Array(
								"PRODUCT_ID" => $productId,
								"CATALOG_GROUP_ID" => $destPriceId,
								"PRICE" => $newPrice,
								"CURRENCY" => "RUB"
							)
						);
				}
			}
			else
			{
				if (is_array($price))
				{
					$newPrice = 0;
					
					foreach ($price as $code => $arPrice)
					{
						if ($sourcePriceId == $arPrice["PRICE_ID"])
						{
							$newPrice = $arPrice["VALUE"];
							
							break 1;
						}
					}
				}
				else
					$newPrice = $price;
				
				if (!IsTestMode())
				{
					@\CPrice::Add(
							Array(
								"PRODUCT_ID" => $productId,
								"CATALOG_GROUP_ID" => $destPriceId,
								"PRICE" => $newPrice,
								"CURRENCY" => "RUB"
							)
						);
				}
			}
		}
		
		unset($sourcePriceId, $destPriceId, $res, $ar_res, $arPrice, $code, $newPrice);
	}
}

// хэш hlblock element
function GetHashHLBlockElement($data = false)
{
	$result = false;
	
	if (!empty($data) && is_array($data))
	{
		$result = md5(serialize($data));
	}
	
	return $result;
}

// хэш iblock element
function GetHashIBlockElement($data = false)
{
	$result = false;
	
	if (!empty($data) && is_array($data))
	{
		$result = md5(serialize($data));
	}
	
	return $result;
}

// хэш продукта
function GetHashProduct($data = false, $arFileTypeProperties = false)
{
	$result = false;
	
	if (!empty($data) && is_array($data))
	{
		$arData = $data;
		
		// картинка, цена и количество отслеживаются отдельно
		unset($arData["PREVIEW_PICTURE"]);
		unset($arData["DETAIL_PICTURE"]);
		unset($arData["STORES"]);
		unset($arData["PRICES"]);
		
		// ATTENTION раздел игнорируем!!!
		unset($arData["IBLOCK_SECTION_ID"]);
		
		if (!empty($arFileTypeProperties) && is_array($arFileTypeProperties) && isset($arData["PROPERTY_VALUES"]))
		{
			foreach ($arFileTypeProperties as $propCode)
				unset($arData["PROPERTY_VALUES"][$propCode]);
		}
		
		$result = md5(serialize($arData));
		
		unset($arData);
	}
	
	return $result;
}

// хэш картинки
function GetHashPicture($data = false, $arFileTypeProperties = false)
{
	$result = false;
	
	if (!empty($data) && is_array($data) && (isset($data["PREVIEW_PICTURE"]) || isset($data["DETAIL_PICTURE"])))
	{
		$arData = Array();
		
		if (!empty($data["PREVIEW_PICTURE"]))
			$arData["PREVIEW_PICTURE"] = $data["PREVIEW_PICTURE"];
		if (!empty($data["DETAIL_PICTURE"]))
			$arData["DETAIL_PICTURE"] = $data["DETAIL_PICTURE"];
		
		if (!empty($arFileTypeProperties) && is_array($arFileTypeProperties) && isset($data["PROPERTY_VALUES"]))
		{
			foreach ($arFileTypeProperties as $propCode)
			{
				if (!empty($data["PROPERTY_VALUES"][$propCode]))
					$arData["PROPERTY_VALUES"][$propCode] = $data["PROPERTY_VALUES"][$propCode];
			}
		}
		
		$result = md5(serialize($arData));
		
		unset($arData);
	}
	
	return $result;
}

// хэш количества
function GetHashQuantity($data)
{
	$result = false;
	
	if (!empty($data) && is_array($data) && isset($data["STORES"]))
	{
		$result = md5(serialize($data["STORES"]));
	}
	
	return $result;
}

// хэш цены
function GetHashPrice($data)
{
	$result = false;
	
	if (!empty($data) && is_array($data) && isset($data["PRICES"]))
	{
		$result = md5(serialize($data["PRICES"]));
	}
	
	return $result;
}

// хэш комплекта/набора
function GetHashSet($data)
{
	$result = false;
	
	if (!empty($data) && is_array($data))
	{
		$result = md5(serialize($data));
	}
	
	return $result;
}

function dd($data = false)
{
	echo "<div><pre>";
	print_r($data);
	echo "</pre></div>";
}
?>