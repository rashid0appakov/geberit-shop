<?php

namespace Webservice\Wscrm;

use Bitrix\Main;
use Bitrix\Iblock;
use Bitrix\Catalog;
use CIBlockElement;

class Export
{
    protected $iblock;
    protected $categories = [];
    protected $offers = [];
    protected $serverName;
    protected $domainName;
    protected $productProperties = [];
    protected $productSKUProperties = [];
    protected $logger;
    protected $logfile = '/bitrix/catalog_export/webservicexml_export_log.txt';

    protected $brandProperty = 'brend';
    protected $articleProperty = 'article';

    public function __construct()
    {
        $this->logger = new Logger(
            Main\Context::getCurrent()->getServer()->getDocumentRoot() . $this->logfile
        );
    }

    public function setIblock($iblock = null)
    {
        $this->iblock = $iblock;
    }

    public function setArticleProperty($property)
    {
        $this->articleProperty = $property;
    }

    public function setBrandProperty($property)
    {
        $this->brandProperty = $property;
    }

    public function setDimainName($value = null)
    {
        if (! is_null($value) && ! empty($value)) {
            $this->domainName = $value;
        }

        $this->initServer();
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    public function setProductProperties($properties)
    {
        if (is_array($properties) && count($properties) > 0) {
            $this->productProperties = $properties;
        }
    }

    public function setProductSKUProperties($properties)
    {
        if (is_array($properties) && count($properties) > 0) {
            $this->productSKUProperties = $properties;
        }
    }

    public function run()
    {
        $this->logger->log('Run export');
        $this->logger->log('Start load categories');
        $this->buildCategories();
        $this->logger->log('End load ategories');
        $this->logger->log('Start load offers');
        $this->buildOffers();
        $this->logger->log('End load offers');
        $this->logger->log("End export (peek memory usage: " . memory_get_peak_usage() . ")");
    }

    private function buildCategories()
    {
        $categories = [];
        $categoriesDB = Iblock\SectionTable::getList([
            'filter' => ['IBLOCK_ID' => $this->iblock],
            'order'  => ['LEFT_MARGIN' => 'ASC'],
            'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID']
        ]);

        $hasCategories = false;
        while ($category = $categoriesDB->fetch()) {
            $categories[$category['ID']] = [
                'id'        => $category['ID'],
                'parentId'  => $category['IBLOCK_SECTION_ID'],
                'name'      => $category['NAME']
            ];
            $hasCategories = true;
        }
        unset($categoriesDB);

        if (! $hasCategories) {
            $iblock =  Iblock\IblockTable::getList([
                'filter' => ['=ID' => $this->iblock],
                'select' => ['ID', 'NAME']
            ])->fetch();
            $categories[$iblock['ID']] = [
                'id'       => $iblock['ID'],
                'name'     => $iblock['NAME'],
                'parentId' => 0,
            ];
        }
        unset($hasCategories, $iblock);
        $this->categories = $categories;
    }

    private function buildOffers()
    {
        $this->logger->log('Offers: get base price');
        // get base price
        $basePriceId = 1;
        $basePriceDB = Catalog\GroupTable::getList([
            'filter' => ['BASE' => 'Y'],
            'select' => ['ID']
        ]);
        while ($basePrice = $basePriceDB->fetch()) {
            $basePriceId = $basePrice['ID'];
        }

        // get elements from iblock
        $productsSettings = [
            'order'  => ['ID' => 'ASC'],
            'filter' => ['IBLOCK_ID' => intval($this->iblock)],
            'select' => [
                'ID',
                'IBLOCK_ID',
                'IBLOCK_SECTION_ID',
                'ACTIVE',
                'NAME',
                'PREVIEW_TEXT',
                'PREVIEW_TEXT_TYPE',
                'DETAIL_PICTURE',
                'PREVIEW_PICTURE',
                'DETAIL_PAGE_URL',
                'CATALOG_GROUP_' . $basePriceId,
            ],
            'offset' => [
                'iNumPage' => 1, // номер страницы при постраничной навигации
                'nPageSize' => 500, // количество элементов на странице при постраничной навигации
            ],
        ];

        // add product properties
        foreach ($this->productProperties as $id => $propertyCode) {
            $productsSettings['select'][] = 'PROPERTY_' . strtoupper($propertyCode);
            $productsSettings['select'][] = 'PROPERTY_' . strtoupper($propertyCode) . '.NAME';
        }

        $this->logger->log('Offers: check iblock offer');
        // get offers
        $iblockOffer = Catalog\CatalogIblockTable::getList(array(
            'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID', 'VERSION' => 'IBLOCK.VERSION'),
            'filter' => array('=PRODUCT_IBLOCK_ID' => $this->iblock)
        ))->fetch();

        if (! empty($iblockOffer)) {
            $offersSettings = [
                'select' => [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'DETAIL_PAGE_URL',
                    'DETAIL_PICTURE',
                    'PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID'],
                    'CATALOG_GROUP_' . $basePriceId
                ]
            ];
        }

        if (is_array($this->productSKUProperties) && count($this->productSKUProperties) > 0) {
            foreach ($this->productSKUProperties as $id => $propertyCode) {
                $offersSettings['select'][] = 'PROPERTY_' . strtoupper($propertyCode);
                $offersSettings['select'][] = 'PROPERTY_' . strtoupper($propertyCode) . '.NAME';
            }
        }

        // with big count products make cblockelement::getlist throught nav params
        do {
            $result = [];
            $this->logger->log('Offers: prepare product query to get ids');
            // get only ids with pagination params
            $productsIdDB = CIBlockElement::GetList([], $productsSettings['filter'], false, $productsSettings['offset'], ['ID']);
            $productsIds = [];
            while ($productID = $productsIdDB->GetNext()) {
                $productsIds[] = $productID['ID'];
            }
            $productsIds = array_unique($productsIds);

            $this->logger->log('Offers prepare products db query');
            $productDB = CIBlockElement::GetList(
                $productsSettings['order'],
                ['ID' => array_values($productsIds)],
                false,
                false,
                $productsSettings['select']
            );

            $this->logger->log('Offers: loop products from db');
            // main build
            $products = [];
            $pictures = [];
            while ($product = $productDB->GetNext()) {
                $products[$product['ID']] = $product;
                $products[$product['ID']]['offers'] = [];

                // get picture
                $picture = null;
                if (intval($product['DETAIL_PICTURE']) > 0) {
                    $picture = intval($product['DETAIL_PICTURE']);
                } elseif (intval($product['PREVIEW_PICTURE']) > 0) {
                    $picture = intval($product['PREVIEW_PICTURE']);
                }

                if ($picture != null && (int) $picture > 0) {
                    $pictures[$picture] = $product['ID']; // this need later
                }
            }
            unset($product, $picture);

            $this->logger->log('Offers: get files from db');
            // get pictures for products
            $filesDB = Main\FileTable::getList(['filter' => ['ID' => array_keys($pictures)]]);
            while ($file = $filesDB->fetch()) {
                $products[$pictures[$file['ID']]]['PICTURE'] = $this->serverName . '/upload/' . $file['SUBDIR'] . '/' . $file['FILE_NAME'];
            }
            unset($filesDB, $file);

            $this->logger->log('Offers: prepare offer db query');
            // check if offers exists
            if (! empty($iblockOffer['IBLOCK_ID'])) {
                $offersDb = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $iblockOffer['IBLOCK_ID'],
                        'PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID'] => array_keys($products),
                    ],
                    false,
                    false,
                    $offersSettings['select']
                );

                $this->logger->log('Offers: loop offers from db');
                while ($offer = $offersDb->GetNext()) {
                    $productID = $offer['PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID'] . '_VALUE'];
                    if ($productID && is_array($products[$productID])) {
                        $products[$productID]['offers'][$offer['ID']] = $offer;
                    }
                }
                unset($offersDb);
            }

            $this->logger->log('Offers: loop products and offers from array');
            foreach ($products as $product) {
                // get product category
                $productCategories = [];
                $productCategoriesDB = \CIBlockElement::GetElementGroups($product['ID'], true);
                while ($productCategory = $productCategoriesDB->fetch()) {
                    $productCategories[] = $productCategory['ID'];
                }
                unset($productCategory);

                $productProperties = $this->getOfferPropertiesValue($product, $this->productProperties);

                $existsOffer = false;
                if (! empty($iblockOffer['IBLOCK_ID'])) {
                    if (is_array($product['offers']) && count($product['offers']) > 0) {
                        foreach ($product['offers'] as $offer) {
                            $offer['PRICE'] = $offer['CATALOG_PRICE_' . $basePriceId];

                            $productSKUProperties = $this->getOfferPropertiesValue($offer, $this->productSKUProperties);

                            foreach ($productProperties as $code => $property) {
                                $productSKUPropertyValue = $this->getOfferPropertyValue($offer, $code);

                                if (is_null($productSKUPropertyValue)) {
                                    $productSKUProperties[$code] = $property;
                                }
                            }

                            $result[] = $this->buildOfferData($offer, $productCategories, $productSKUProperties, $product);
                            $existsOffer = true;
                            unset($productSKUProperties);
                        }
                    }
                }

                if ($existsOffer === false) {
                    $product['PRICE'] = $product['CATALOG_PRICE_' . $basePriceId];
                    $result[] = $this->buildOfferData($product, $productCategories, $productProperties);
                }
                unset($product);
            }
            unset($existsOffer, $productCategories);

            // end
            if (count($result) > 0) {
                $this->offers = array_merge($this->offers, $result);
                unset($result);
            }
            // continue get elements from iblock
            $productsSettings['offset']['iNumPage'] = $productsSettings['offset']['iNumPage'] + 1;
        } while ($productsIdDB->NavPageNomer < $productsIdDB->NavPageCount);

        $this->logger->log('Offers: end');
    }

    private function buildOfferData($offer, $categories, $properties, $product = null)
    {
        $result = [
            'id'              => $offer['ID'],
            'productId'       => $product['ID'] ?: $offer['ID'],
            'price'           => $offer['PRICE'],
            'name'            => $offer['NAME'] ,
            'url'             => $this->serverName . $offer['DETAIL_PAGE_URL'],
            'quantity'        => $offer['CATALOG_QUANTITY'],
            'currencyId'      => null,
            'categoryId'      => is_array($categories) ? $categories : [],
            'picture'         => $offer['PICTURE'] ?: $product['PICTURE'],
            'additionalPhoto' => null,
            'new'             => null,
            'hit'             => null,
            'hot'             => null,
            'action'          => null,
            'discount'        => null,
            'originalPrice'   => null,
            'productName'     => null,
            'vendor'          => null,
            'vendorCode'      => $this->getOfferPropertyValue($offer, $this->articleProperty),
            'description'     => $offer['PREVIEW_TEXT'],
            'cpa'             => null,
            'param'           => $this->buildOfferPropertyValues($offer, $properties),
        ];

        return array_filter($result);
    }

    private function getOfferPropertiesValue($offer, $properties)
    {
        $result = [];
        foreach ($properties as $key => $property) {
            if (($value = $this->getOfferPropertyValue($offer, $property)) != '') {
                $result[$property] = $value;
            }
        }
        return $result;
    }

    private function buildOfferPropertyValues($offer, $properties)
    {
        $result = [];
        foreach ($properties as $code => $value) {
            $propertyCode = $code === $this->brandProperty ? 'brend' : $code;
            $result[strtolower($propertyCode)] = [
                'value' => $value,
                'name' => strtolower($code)
            ];
        }
        return $result;
    }

    private function getOfferPropertyValue($offer, $property)
    {
        $result = null;
        if (isset($offer["PROPERTY_" . strtoupper($property) . "_NAME"])) {
            $result =  $offer["PROPERTY_" . strtoupper($property) . "_NAME"];
        } elseif (isset($offer["PROPERTY_" . strtoupper($property) . "_VALUE"])) {
            $result =  $offer["PROPERTY_" . strtoupper($property) . "_VALUE"];
        } elseif (isset($offer[strtoupper($property)])) {
            $result =  $offer[strtoupper($property)];
        }
        return $result;
    }

    private function getOfferValue($offer, $value)
    {
        return isset($offer[$value]) ?: null;
    }

    private function initServer()
    {
        $protocol = 'http://';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->domainName);
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode === 200) {
            $protocol = 'https://';
        }

        $this->serverName = $protocol . $this->domainName;
    }
}
