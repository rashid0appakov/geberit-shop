<?php

// ШАГ 6 - поля/свойства для хэша и свойство SOURCE_ELEMENT_ID в каталоге

@ProcessOn();

// ATTENTION если не удалось создать нужное поле/свойство - отмена копирования!!!
$bFatal = false;

clearstatcache();

@LogProcessInfo("Creating/check hash fields/properties and catalog iblock property SOURCE_ELEMENT_ID started.", true);

$ITERATION = date("His");

// поля для хайлоадов

$hlf = new \CUserTypeEntity();

$arHLRequired = Array(
		(HLBLOCK_FIELD_SOURCE_ELEMENT_ID . HLBLOCK_FIELD_SOURCE_SUFFIX) => Array(
			"ENTITY_ID" => false,
			"FIELD_NAME" => (HLBLOCK_FIELD_SOURCE_ELEMENT_ID . HLBLOCK_FIELD_SOURCE_SUFFIX),
			"USER_TYPE_ID" => "integer",
			"XML_ID" => "",
			"SORT" => "100",
			"MULTIPLE" => "N",
		),
		(HLBLOCK_FIELD_SOURCE_ELEMENT_ID_LINK . HLBLOCK_FIELD_SOURCE_SUFFIX) => Array(
			"ENTITY_ID" => false,
			"FIELD_NAME" => (HLBLOCK_FIELD_SOURCE_ELEMENT_ID_LINK . HLBLOCK_FIELD_SOURCE_SUFFIX),
			"USER_TYPE_ID" => "integer",
			"XML_ID" => "",
			"SORT" => "100",
			"MULTIPLE" => "N",
		),
		(HLBLOCK_FIELD_HASH_DATA . HLBLOCK_FIELD_SOURCE_SUFFIX) => Array(
			"ENTITY_ID" => false,
			"FIELD_NAME" => (HLBLOCK_FIELD_HASH_DATA . HLBLOCK_FIELD_SOURCE_SUFFIX),
			"USER_TYPE_ID" => "string",
			"XML_ID" => "",
			"SORT" => "100",
			"MULTIPLE" => "N",
		),
	);

foreach ($arSourceHLBlockList as $id)
{
	if (!empty($arHLBlocksMap[$id]))
	{
		foreach ($arHLRequired as $newCode => $arParams)
		{
			$res = \CUserTypeEntity::GetList(
					Array(($by = "ID") => ($order = "ASC")), 
					Array("ENTITY_ID" => "HLBLOCK_".$arHLBlocksMap[$id], "FIELD_NAME" => $newCode)
				);
			if (intval($res->SelectedRowsCount()) <= 0)
			{
				$arParams["ENTITY_ID"] = "HLBLOCK_" . $arHLBlocksMap[$id];
				
				if (!IsTestMode())
					$newId = $hlf->Add($arParams);
				else
					$newId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
				
				if (!empty($newId))
				{
					if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
						LogProcessData(
							Array(
								"time" => date("H:i:s"),
								"action" => "new hlblock hash field",
								"hlblock" => $arHLBlocksMap[$id],
								"data" => $arParams,
								"fieldId" => $newId,
						), $ITERATION);
					if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
						LogDebugData(
							Array(
								"time" => date("H:i:s"),
								"action" => "new hlblock hash field",
								"hlblock" => $arHLBlocksMap[$id],
								"data" => $arParams,
								"fieldId" => $newId,
						), $ITERATION);
					
					$msg = "Destination HLBlock #" . $arHLBlocksMap[$id] . " new field CODE: " . $newCode . ".";
					@LogProcessInfo($msg);
				}
				else
				{
					$err = "Destination HLBlock #" . $arHLBlocksMap[$id] . " can't create field CODE: " . $newCode . ", error: " . $hlf->LAST_ERROR;
					
					LogProcessError($err);
					
					$bFatal = true;
				}
			}
		}
	}
}

$ibp = new \CIBlockProperty;

// свойства для каталога

if (defined("IBLOCK_ID_DEST_CATALOG") && intval(IBLOCK_ID_DEST_CATALOG) > 0)
{
	$arCatalogRequired = Array(
			(IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "ID элемента источника",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "N",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "ID элемента источника (связь)",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "N",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_IMPORT_SOURCE . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Источник импорта ТипТоп",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_IMPORT_SOURCE . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Хэш данных",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_HASH_PICTURE . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Хэш картинок",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_HASH_PICTURE . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_HASH_PRICE . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Хэш цен",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_HASH_PRICE . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_HASH_QUANTITY . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Хэш количества",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_HASH_QUANTITY . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
			(IBLOCK_PROPERTY_HASH_SET . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
				"IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG,
				"NAME" => "Хэш набора/комплекта",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => (IBLOCK_PROPERTY_HASH_SET . IBLOCK_PROPERTY_SOURCE_SUFFIX),
				"PROPERTY_TYPE" => "S",
				"ROW_COUNT" => "1",
				"COL_COUNT" => "30",
				"LIST_TYPE" => "L",
				"MULTIPLE" => "N",
				"MULTIPLE_CNT" => "5",
			),
		);
	
	foreach ($arCatalogRequired as $code => $arParams)
	{
		$res = \CIBlockProperty::GetList(Array("ID" => "ASC",), Array("IBLOCK_ID" => IBLOCK_ID_DEST_CATALOG, "CODE" => $code));
		if (intval($res->SelectedRowsCount()) <= 0)
		{
			if (!IsTestMode())
				$newId = $ibp->Add($arParams);
			else
				$newId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
			
			if (!empty($newId))
			{
				if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
					LogProcessData(
						Array(
							"time" => date("H:i:s"),
							"action" => "new catalog iblock required property",
							"iblock" => IBLOCK_ID_DEST_CATALOG,
							"data" => $arParams,
							"propId" => $newId,
					), $ITERATION);
				if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
					LogDebugData(
						Array(
							"time" => date("H:i:s"),
							"action" => "new catalog iblock required property",
							"iblock" => IBLOCK_ID_DEST_CATALOG,
							"data" => $arParams,
							"propId" => $newId,
					), $ITERATION);
					
				$msg = "Destination IBlock #" . IBLOCK_ID_DEST_CATALOG . " new property CODE: " . $code . ".";
				@LogProcessInfo($msg);
			}
			else
			{
				$err = "Destination IBlock #" . IBLOCK_ID_DEST_CATALOG . " can't create property CODE: " . $code . ", error: " . $ibp->LAST_ERROR;
				
				LogProcessError($err);
				
				$bFatal = true;
			}
		}
	}
}

// свойства для ИБ

$arIBRequired = Array(
		(IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
			"IBLOCK_ID" => false,
			"NAME" => "ИД элемента в источнике",
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => (IBLOCK_PROPERTY_SOURCE_ELEMENT_ID . IBLOCK_PROPERTY_SOURCE_SUFFIX),
			"PROPERTY_TYPE" => "N",
			"MULTIPLE" => "N",
		),
		(IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
			"IBLOCK_ID" => false,
			"NAME" => "ИД элемента в источнике (связь)",
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => (IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK . IBLOCK_PROPERTY_SOURCE_SUFFIX),
			"PROPERTY_TYPE" => "N",
			"MULTIPLE" => "N",
		),
		(IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX) => Array(
			"IBLOCK_ID" => false,
			"NAME" => "Хэш данных",
			"ACTIVE" => "Y",
			"SORT" => "500",
			"CODE" => (IBLOCK_PROPERTY_HASH_DATA . IBLOCK_PROPERTY_SOURCE_SUFFIX),
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
		),
	);

foreach ($arSourceIBlockList as $id)
{
	if (IBLOCK_ID_DEST_CATALOG == $arIBlocksMap[$id])
		continue;
	
	foreach ($arIBRequired as $newCode => $arParams)
	{
		$res = \CIBlockProperty::GetList(Array("ID" => "ASC",), Array("IBLOCK_ID" => $arIBlocksMap[$id], "CODE" => $newCode));
		if (intval($res->SelectedRowsCount()) <= 0)
		{
			$arParams["IBLOCK_ID"] = $arIBlocksMap[$id];
			
			if (!IsTestMode())
				$newId = $ibp->Add($arParams);
			else
				$newId = "test_" . mt_rand(1000, 9999); // ATTENTION для тестового режима
			
			if (!empty($newId))
			{
				if (defined("LOG_ELEMENTS_NEW") && "Y" == LOG_ELEMENTS_NEW)
					LogProcessData(
						Array(
							"time" => date("H:i:s"),
							"action" => "new iblock hash property",
							"iblock" => $arIBlocksMap[$id],
							"data" => $arParams,
							"propId" => $newId,
					), $ITERATION);
				if (defined("LOG_DEBUG_DATA") && "Y" == LOG_DEBUG_DATA)
					LogDebugData(
						Array(
							"time" => date("H:i:s"),
							"action" => "new iblock hash property",
							"iblock" => $arIBlocksMap[$id],
							"data" => $arParams,
							"propId" => $newId,
					), $ITERATION);
				
				$msg = "Destination IBlock #" . $arIBlocksMap[$id] . " new property CODE: " . $newCode . ".";
				@LogProcessInfo($msg);
			}
			else
			{
				$err = "Destination IBlock #" . $arIBlocksMap[$id] . " can't create property CODE: " . $newCode . ", error: " . $ibp->LAST_ERROR;
				
				LogProcessError($err);
				
				$bFatal = true;
			}
		}
	}
}

// фатальная ошибка
if ($bFatal)
{
	AbortProcess("Fatal error: can't create required field(s) and/or property(ies)");
}
else
{
	// создание/проверка наличия полей/свойств для хэша и свойства SOURCE_ELEMENT_ID в каталоге закончено
	$arStepParams = Array("STEP" => 7, "PROCESS" => true,);
	
	@SaveStepParams();
	
	@LogProcessInfo("Creating/check hash fields/properties and catalog iblock property SOURCE_ELEMENT_ID finished.\r\n".PHP_EOL, true);
}

@ProcessOff();
?>