<?php

global $arTranslitParams;
$arTranslitParams = Array(
	"max_len" => "150", // обрезает символьный код до 100 символов
	"change_case" => "L", // буквы преобразуются к нижнему регистру
	"replace_space" => "_", // меняем пробелы на нижнее подчеркивание
	"replace_other" => "_", // меняем левые символы на нижнее подчеркивание
	"delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
	"use_google" => "false", // отключаем использование google
);

// ----------------------------------------------

// параметры
global $arStepParams;
$arStepParams = Array();

// список hlblocks источника
global $arSourceHLBlockList;
$arSourceHLBlockList = Array(
		HLBLOCK_ID_SOURCE_COUNTRY,
		HLBLOCK_ID_SOURCE_COLOR,
		HLBLOCK_ID_SOURCE_INTERIOR,
		HLBLOCK_ID_SOURCE_GUARANTEE,
	);

// карта hlblocks
global $arHLBlocksMap;
$arHLBlocksMap = Array(
		HLBLOCK_ID_SOURCE_COUNTRY => HLBLOCK_ID_DEST_COUNTRY,
		HLBLOCK_ID_SOURCE_COLOR => HLBLOCK_ID_DEST_COLOR,
		HLBLOCK_ID_SOURCE_INTERIOR => HLBLOCK_ID_DEST_INTERIOR,
		HLBLOCK_ID_SOURCE_GUARANTEE => HLBLOCK_ID_DEST_GUARANTEE,
	);

// карта полей hlblocks
global $arHLBlocksFieldMap;
$arHLBlocksFieldMap = Array(
		HLBLOCK_ID_SOURCE_COUNTRY => Array(),
		HLBLOCK_ID_SOURCE_COLOR => Array(),
		HLBLOCK_ID_SOURCE_INTERIOR => Array(),
		HLBLOCK_ID_SOURCE_GUARANTEE => Array(),
	);

// список iblocks источника
global $arSourceIBlockList;
$arSourceIBlockList = Array(
		IBLOCK_ID_SOURCE_BRANDS,
		IBLOCK_ID_SOURCE_COLLECTION,
		IBLOCK_ID_SOURCE_CATALOG, // ATTENTION для корректной обработки должен идти последним!!!
	);

// карта iblocks
global $arIBlocksMap;
$arIBlocksMap = Array(
		IBLOCK_ID_SOURCE_BRANDS => IBLOCK_ID_DEST_BRANDS,
		IBLOCK_ID_SOURCE_COLLECTION => IBLOCK_ID_DEST_COLLECTION,
		IBLOCK_ID_SOURCE_CATALOG => IBLOCK_ID_DEST_CATALOG, // ATTENTION для корректной обработки должен идти последним!!!
	);

// карта категорий свойств iblocks
global $arIBlocksPropsCategory;
$arIBlocksPropsCategory = Array(
		IBLOCK_ID_SOURCE_CATALOG => IBLOCK_SOURCE_CATALOG_PROPERTY_CATEGORY,
	);

// карта свойств iblocks
global $arIBlocksPropsMap;
$arIBlocksPropsMap = Array(
		IBLOCK_ID_SOURCE_BRANDS => Array(),
		IBLOCK_ID_SOURCE_COLLECTION => Array(),
		IBLOCK_ID_SOURCE_CATALOG => Array(),
	);

// список iblocks источника структуры
global $arSourceIBlockStructureList;
$arSourceIBlockStructureList = Array(
		IBLOCK_ID_SOURCE_CATALOG,
	);

// список iblocks источника структуры разделов
global $arSourceIBlockStructureSectionList;
$arSourceIBlockStructureList = Array(
		IBLOCK_ID_SOURCE_CATALOG => SOURCE_SECTION_ID,
	);

// список iblocks получателя разделов назначения
global $arDestIBlockStructureSectionList;
$arDestIBlockStructureSectionList = Array(
		IBLOCK_ID_DEST_CATALOG => SECTION_ID_DEST_CATALOG_SECTIONS_MAP,
	);

global $arPricesMap;
$arPricesMap = Array(
		PRICE_ID_SOURCE_MSK => PRICE_ID_DEST_MSK,
		PRICE_ID_SOURCE_SPB => PRICE_ID_DEST_SPB,
		PRICE_ID_SOURCE_EKB => PRICE_ID_DEST_EKB,
	);

global $arStoresMap;
$arStoresMap = Array(
		STORE_ID_SOURCE_MSK => STORE_ID_DEST_MSK,
		STORE_ID_SOURCE_SPB => STORE_ID_DEST_SPB,
		STORE_ID_SOURCE_EKB => STORE_ID_DEST_EKB,
	);

// Карта разделов
global $arSectionsMap;
$arSectionsMap = Array();

// Карта справочников/hlblocks
global $arHLReferencesMap;
$arHLReferencesMap = Array();

// Карта справочников/iblocks
global $arIBReferencesMap;
$arIBReferencesMap = Array();

global $arTestData;
$arTestData = Array();

?>