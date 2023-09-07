<?php
//<title>wscrm</title>

use Bitrix\Iblock;
use Bitrix\Catalog;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Webservice\Wscrm;

if (! Loader::includeModule('iblock') || ! Loader::includeModule('catalog') || ! Loader::includeModule('webservice.wscrm')) {
    return;
}

// CIblock => Bitrix\Iblock\IblockTable
// CIBlockElement::GetList => \Bitrix\Iblock\ElementTable
// CIBlockProperty => Bitrix\Iblock\PropertyTable
// CIBlockSection => Bitrix\Iblock\SectionTable
// CCatalogGroup => Bitrix\Catalog\GroupTable

/*
    BookTable::getList(array(
        'select'  => ... // имена полей, которые необходимо получить в результате
        'filter'  => ... // описание фильтра для WHERE и HAVING
        'group'   => ... // явное указание полей, по которым нужно группировать результат
        'order'   => ... // параметры сортировки
        'limit'   => ... // количество записей
        'offset'  => ... // смещение для limit
        'runtime' => ... // динамически определенные поля
    ));
 */

ignore_user_abort(true);
set_time_limit(0);

// Переменная $IBLOCK_ID должна быть установлена
// мастером экспорта или из профиля
// Переменная $SETUP_FILE_NAME должна быть установлена
// мастером экспорта или из профиля
$IBLOCK_ID = (int) $IBLOCK_ID;
$iblock = Iblock\IblockTable::getById($IBLOCK_ID)->fetch();
if (! $iblock) {
    $strExportErrorMessage = Loc::getMessage('WEBSERVICE_WSCRM_NO_IBLOCK');
    return;
}

$productPropertiesArray = $PRODUCT_PROPERTIES;
$productSKUPropertiesArray = $PRODUCT_SKU_PROPERTIES;

$articleProperty = $PROPERTY_ARTICLE != '' ? $PROPERTY_ARTICLE : 'ARTNUMBER';
$brandProperty = $PROPERTY_BRAND != '' ? $PROPERTY_BRAND : 'MANUFACTURER';

$mainProperties = [
    'article'  => $articleProperty,
    'brend'    => $brandProperty,
    'color'    => 'color',
    'width'    => 'width',
    'height'   => 'height',
];
$availableCodeProperties = array_map('strtoupper', array_values($mainProperties));

$productPropertiesDb = Iblock\PropertyTable::getList([
    'order'  => ['SORT' => 'ASC', 'NAME' => 'ASC'],
    'filter' => ['=IBLOCK_ID' => $iblock['ID'], 'CODE' => $availableCodeProperties],
]);

$productProperties = [];
while ($property = $productPropertiesDb->fetch()) {
    $productProperties[$property['ID']] = strtoupper($property["CODE"]);
}

$iblockOffer = Catalog\CatalogIblockTable::getList(array(
    'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID', 'VERSION' => 'IBLOCK.VERSION'),
    'filter' => array('=PRODUCT_IBLOCK_ID' => $iblock['ID'])
))->fetch();
$productSKUProperties = [];
if (! empty($iblockOffer)) {
    // prepare sku properties
    $productSKUPropertiesDb = Iblock\PropertyTable::getList([
        'filter' => ['=IBLOCK_ID' => $iblockOffer['IBLOCK_ID'], 'CODE' => $availableCodeProperties],
    ]);
    $productSKUProperties = [];
    while ($property = $productSKUPropertiesDb->fetch()) {
        $productSKUProperties[$property['ID']] = strtoupper($property['CODE']);
    }
}

$site = Bitrix\Main\SiteTable::getList([
    'filter' => ['DEF' => 'Y'],
    'order'  => ['DEF']
])->fetch();

$logger = new Wscrm\Logger(
    Bitrix\Main\Application::getInstance()->getContext()->getServer()->getDocumentRoot() . '/bitrix/catalog_export/webservicexml_run_log.txt'
);
$logger->log('START: Run catalog export at: ' . date('d.m.Y H:i:s'));

try {
    $export = new Wscrm\Export();
    $export->setIblock($iblock['ID']);
    $export->setDimainName($site['SERVER_NAME'] ?: '');
    $export->setProductProperties($productProperties);
    $export->setProductSKUProperties($productSKUProperties);
    $export->setArticleProperty($articleProperty);
    $export->setBrandProperty($brandProperty);
    $export->run();

    $xml = new WebServiceCrm\Xml(
        ($site && $site['NAME']) ? $site['NAME']: 'Default',
        $_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME
    );

    $xml->generate(
        $export->getOffers(),
        $export->getCategories()
    );

    $logger->log('END: catalog export end at: ' . date('d.m.Y H:i:s'));
} catch (\Exception $e) {
    $strExportErrorMessage = Loc::getMessage('WEBSERVICE_WSCRM_ERROR') . ': ' . $e->getMessage();
    $logger->log('ERROR: export ended with error: ' . $e->getMessage());
}
?>