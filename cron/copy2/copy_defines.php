<?php
define("REST_URL", "https://tiptop-shop.ru/local/cron/copy/rest.php");
define("REST_CLIENT", "geberit");
define("REST_SALT", "jtoAm\!C=5{0ygo!");

define("TEST_MODE", "N");

define("LOG_ELEMENTS_NEW", "N");
define("LOG_ELEMENTS_UPDATE", "N");
define("LOG_ELEMENTS_SKIP", "N");
define("LOG_ELEMENTS_DEACTIVATE", "N");
define("LOG_ELEMENTS_ERROR", "Y");
define("LOG_DEBUG_DATA", "N");

define("UPDATE_DEST_HLBLOCK_ITEMS", "N");
define("UPDATE_DEST_IBLOCK_ITEMS", "N");
define("UPDATE_DEST_CATALOG_ITEMS", "Y");

define("UPDATE_DEST_CATALOG_PRICE", "N");
define("UPDATE_DEST_CATALOG_QUANTITY", "N");

// ATTENTION принудительное обновление всех нефайловых свойств!!!
define("OVERRIDE_DEST_CATALOG_ITEMS_HASH", "N");

define("D_TIMEOUT", 50); // максимальное время итерации

define("HLBLOCK_LIMIT", 100);
define("IBLOCK_LIMIT", 100);

define("IBLOCK_ID_SOURCE_CATALOG", 54); // каталог
define("IBLOCK_ID_SOURCE_BRANDS", 52); // производители
define("IBLOCK_ID_SOURCE_COLLECTION", 53); // коллекции

define("IBLOCK_ID_DEST_CATALOG", 84);
define("IBLOCK_ID_DEST_BRANDS", 81);
define("IBLOCK_ID_DEST_COLLECTION", 82);

define("SECTION_ID_DEST_CATALOG_SECTIONS_MAP", 3019);

define("IBLOCK_SOURCE_CATALOG_PROPERTY_CATEGORY", "SANT");

define("HLBLOCK_ID_SOURCE_COUNTRY", 4); // страны
define("HLBLOCK_ID_SOURCE_GUARANTEE", 7); // гарантия
define("HLBLOCK_ID_SOURCE_COLOR", 5); // цвет
define("HLBLOCK_ID_SOURCE_INTERIOR", 6); // интерьеры

define("HLBLOCK_ID_DEST_COUNTRY", 4); // страны
define("HLBLOCK_ID_DEST_GUARANTEE", 7); // гарантия
define("HLBLOCK_ID_DEST_COLOR", 5); // цвет
define("HLBLOCK_ID_DEST_INTERIOR", 6); // интерьеры

define("STORE_ID_SOURCE_MSK", 4); // ИД склад Мск
define("STORE_ID_SOURCE_SPB", 1); // ИД склад Питер
define("STORE_ID_SOURCE_EKB", 2); // ИД склад Екатеринбург

define("PRICE_ID_SOURCE_BASE", 1); // ИД базовой цены

define("PRICE_ID_SOURCE_MSK", 1); // ИД цены Мск
define("PRICE_ID_SOURCE_SPB", 2); // ИД цены Питер
define("PRICE_ID_SOURCE_EKB", 4); // ИД цены Екатеринбург

define("STORE_ID_DEST_MSK", 4); // ИД склад Мск
define("STORE_ID_DEST_SPB", 1); // ИД склад Питер
define("STORE_ID_DEST_EKB", 2); // ИД склад Екатеринбург

define("PRICE_ID_DEST_BASE", 1); // ИД базовой цены

define("PRICE_ID_DEST_MSK", 1); // ИД цены Мск
define("PRICE_ID_DEST_SPB", 2); // ИД цены Питер
define("PRICE_ID_DEST_EKB", 4); // ИД цены Екатеринбург

define("SOURCE_MANUFACTURER_ID", 329217); // монобренд Геберит

define("SOURCE_SECTION_ID", 2386); // раздел Сантехника

define("FILE_PREFIX_SOURCE_HLBLOCK_FIELDS", "copy_source_hlblock_fields_"); // префикс файлов с данными по полям hlblocks
define("FILE_PREFIX_SOURCE_IBLOCK_PROPERTIES", "copy_source_iblock_props_"); // префикс файлов с данными по свойствам iblocks
define("FILE_PREFIX_SOURCE_IBLOCK_STRUCTURE", "copy_source_iblock_structure_"); // префикс файлов с данными по структуре iblocks
define("FILE_PREFIX_SOURCE_HLBLOCK_DATA", "copy_source_hlblock_data_"); // префикс файлов с данными hlblocks
define("FILE_PREFIX_SOURCE_IBLOCK_DATA", "copy_source_iblock_data_"); // префикс файлов с данными iblocks
define("FILE_PREFIX_SOURCE_HLBLOCK_LINKED", "copy_source_hlblock_linked_"); // префикс файлов с данными hlblocks
define("FILE_PREFIX_SOURCE_IBLOCK_LINKED", "copy_source_iblock_linked_"); // префикс файлов с данными iblocks
define("FILE_PREFIX_SOURCE_IBLOCK_SET", "copy_source_iblock_set_"); // префикс файлов с наборами/комплектами iblocks

define("FILE_SOURCE_HLBLOCK_MAP", "copy_source_hlblock_map"); // файл с картой полей hlblocks
define("FILE_SOURCE_IBLOCK_MAP", "copy_source_iblock_map"); // файл с картой свойств iblocks

define("FILE_SOURCE_IBLOCK_SECTION_MAP", "copy_source_iblock_section_map"); // файл с картой разделов iblocks

define("HLBLOCK_FIELD_SOURCE_SUFFIX", "_TT");
define("IBLOCK_PROPERTY_SOURCE_SUFFIX", "_TIPTOP");

define("HLBLOCK_FIELD_SOURCE_ELEMENT_ID", "UF_SRC_EL_ID");
define("HLBLOCK_FIELD_SOURCE_ELEMENT_ID_LINK", "UF_SRC_EL_IDL");
define("IBLOCK_PROPERTY_SOURCE_ELEMENT_ID", "SOURCE_ELEMENT_ID");
define("IBLOCK_PROPERTY_SOURCE_ELEMENT_ID_LINK", "SOURCE_ELEMENT_ID_LINK");
define("HLBLOCK_FIELD_HASH_DATA", "UF_HASH_DATA");
define("IBLOCK_PROPERTY_HASH_DATA", "HASH_DATA");
define("IBLOCK_PROPERTY_HASH_PICTURE", "HASH_PICTURE");
define("IBLOCK_PROPERTY_HASH_PRICE", "HASH_PRICE");
define("IBLOCK_PROPERTY_HASH_QUANTITY", "HASH_QUANTITY");
define("IBLOCK_PROPERTY_HASH_SET", "HASH_SET");
define("IBLOCK_PROPERTY_IMPORT_SOURCE", "IMPORT_SOURCE");

define("IMPORT_SOURCE_KEY", "TIPTOP");

define("CATALOG_PRODUCTS_IDENTITY_PROPERTY", "ARTNUMBER");

// параметры CSV
define("FIELD_DELIMITER", ";");
define("TEXT_SEPARATOR", "\"");

?>