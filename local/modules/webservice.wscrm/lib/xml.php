<?php

namespace Webservice\Wscrm;

use Bitrix\Iblock;
use Bitrix\Main;

class Xml
{
    public $iblock;
    public $iblockOffer;

    protected $shop;
    protected $file;
    protected $params;
    protected $dd;
    protected $serverName;
    protected $eCategories;
    protected $eOffers;
    protected $productProperties;
    protected $possibleProperties = [
        "article",
        "brend",
        "color",
        "size",
        "weight",
        "length",
        "width",
        "height",
    ];


    protected $properties = [
        "url",
        "price",
        "currencyId",
        "categoryId",
        "picture",
        "additionalPhoto",
        "new",
        "hit",
        "hot",
        "action",
        "discount",
        "originalPrice",
        "name",
        "productName",
        "vendor",
        "vendorCode",
        "description",
        "cpa",
        "quantity",
    ];

    public function __construct($shop, $file)
    {
        $this->shop = $shop;
        $this->file = $file;

        $this->initServer();
    }

    public function generate()
    {
        $string = '<?xml version="1.0" encoding="UTF-8"?>'.
            '<yml_catalog date="' . date('Y-m-d H:i:s') . '">'.
                '<shop>'.
                    '<name>' . $this->shop . '</name>'.
                    '<categories/>'.
                    '<offers/>'.
                '</shop>'.
            '</yml_catalog>'.
        '';

        $xml = new \SimpleXMLElement(
            $string, LIBXML_NOENT | LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE
        );

        $this->dd = new \DOMDocument();
        $this->dd->preserveWhiteSpace = false;
        $this->dd->formatOutput = true;
        $this->dd->loadXML($xml->asXML());

        $this->eCategories = $this->dd
            ->getElementsByTagName('categories')->item(0);
        $this->eOffers = $this->dd
            ->getElementsByTagName('offers')->item(0);

        $this->prepareProperties();

        $this->addCategories($this->buildCategories());
        $this->addOffers($this->buildOffers());

        $this->dd->saveXML();
        $this->dd->save($this->file);
    }

    private function prepareProperties()
    {
        $productPropertiesDb = \CIBlockProperty::getList([
            ['SORT' => 'ASC', 'NAME' => 'ASC'],
            ['IBLOCK_ID' => $this->iblock],
        ]);
        while ($property = $productPropertiesDb->fetch()) {
            if (in_array(strtolower($property['CODE']), $this->possibleProperties)) {
                $productProperties[$property['ID']] = strtoupper($property["CODE"]);
            }
        }

        $this->productProperties = $productProperties;
    }

    private function buildCategories()
    {
        $return = [];
        $categories = Iblock\SectionTable::getList([
            'filter' => ['IBLOCK_ID' => $this->iblock],
            'order' => ['LEFT_MARGIN' => 'ASC']
        ]);

        $hasCategories = false;
        while ($category = $categories->fetch()) {
            $return[$category['ID']] = $category;
            $hasCategories = true;
        }

        if (! $hasCategories) {
            $iblock =  Iblock\IblockTable::getById($this->iblock);
            $return[$iblock['ID']] = [
                'ID' => $iblock['ID'],
                'NAME' => $iblock['NAME'],
                'IBLOCK_SECTION_ID' => 0,
            ];
        }

        return $return;
    }

    private function addCategories($categories)
    {
        foreach ($categories as $category) {
            $e = $this->eCategories->appendChild(
                $this->dd->createElement(
                    'category', $category['NAME']
                )
            );
            $e->setAttribute('id', $category['ID']);

            if ($category['IBLOCK_SECTION_ID'] > 0) {
                $e->setAttribute('parentId', $category['IBLOCK_SECTION_ID']);
            }
        }
    }

    private function buildOffers()
    {
        $result = [];

        // get base price
        // TODO: get real price id
        $basePriceId = 1;

        // get element from iblock
        $productsSettings = [
            'select' => ['ID', 'LID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'ACTIVE', 'NAME', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', 'CATALOG_GROUP_' . $basePriceId]
        ];

        // add product properties
        foreach ($this->productProperties as $property) {
            $productsSettings['select'][] = 'PROPERTY_' . strtoupper($property);
            $productsSettings['select'][] = 'PROPERTY_' . strtoupper($property) . '_NAME';
        }

        $bProducts = \CIBlockElement::getList([
            'select' => $productsSettings['select'],
            'order'  => ['ID' => 'DESC'],
            'filter' => ['IBLOCK_ID' => $this->iblock]
        ]);

        // main build
        $products = [];
        while ($product = $bProducts->getNext()) {
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
                $fileDB = \CFile::getList([], ['ID' => $picture]);
                if ($file = $fileDB->fetch()) {
                    $result[$product['ID']]['picture'] = $this->serverName . '/upload/' . $file['SUBDIR'] . '/' . $file['FILE_NAME'];
                }
            }
        }

        // get offers
        $iblockOffer = \CCatalogSKU::GetInfoByProductIBlock($this->iblock);
        $offersSettings = [
            'select' => ['ID', 'NAME', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID'], 'CATALOG_GROUP_' . $basePriceId]
        ];

        // check if offers exists
        if (! empty($iblockOffer['IBLOCK_ID'])) {
            $offersDb = \CIBlockElement::getList([
                [],
                [
                    'IBLOCK_ID' => $iblockOffer['IBLOCK_ID'],
                    'PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID'] => array_keys($products),
                ],
                false,
                [],
                $offersSettings['select']
            ]);

            while ($offer = $offersDb->getNext()) {
                $products[$offer['PROPERTY_' . $iblockOffer['SKU_PROPERTY_ID']]]['offers'][$offer['ID']] = $offer;
            }
        }

        foreach ($products as $product) {
            // init properties
            $productProperties = [];
            foreach ($this->productProperties as $key => $property) {
               $productProperties[$key] = (isset($product['PROPERTY_' . $property]))
                   ? $product['PROPERTY_' . $property]
                   : $product['PROPERTY_' . $property . '_VALUE'];
            }

            $existOffer = false;
            if (!empty($iblockOffer['IBLOCK_ID'])) {
                if (is_array($product['offers']) && count($product['offers']) > 0) {
                    foreach ($product['offers'] as $offer) {
                        $offerData = [
                            'id'              => $offer['ID'],
                            'productId'       => $product['ID'],
                            'price'           => $offer['CATALOG_PRICE_' . $basePriceId],
                            'name'            => $product['NAME'],
                            'url'             => $product['DETAIL_PAGE_URL'],
                            'quantity'        => $offer['CATALOG_QUANTITY'],
                        ];

                        $productSkuProperties = [];
                        foreach ($this->productProperties as $key => $property) {
                           $productSkuProperties[$key] = (isset($offer['PROPERTY_' . $property]))
                               ? $offer['PROPERTY_' . $property]
                               : $offer['PROPERTY_' . $property . '_VALUE'];
                        }
                        $offerData['param'] = $productSkuProperties;
                        $result[] = $offerData;
                    }
                    $existsOffer = true;
                }
            }

            if (! $existsOffer) {
                $productData[] = [
                    'id'              => $product['ID'],
                    'productId'       => $product['ID'],
                    'price'           => $product['CATALOG_GROUP_' . $basePriceId],
                    'name'            => $product['NAME'],
                    'url'             => $product['DETAIL_PAGE_URL'],
                    'quantity'        => $product['CATALOG_QUANTITY'],
                    'param'           => $productProperties
                ];

                $result[] = $productData;
            }
        }

        return $result;
    }

    private function addOffers(array $offers)
    {
        foreach ($offers as $offerKey => $offer) {
            $e = $this->eOffers->appendChild(
                $this->dd->createElement('offer')
            );

            $e->setAttribute('id', $offerKey);
            $e->setAttribute('productId', $offer['productId']);

            if (is_array($offer['additionalProducts']) && count($offer['additionalProducts']) > 0) {
                $e->setAttribute('additionalProducts', trim(implode(',', $offer['additionalProducts'])));
            }

            if (!empty($offer['quantity'])) {
                $e->setAttribute('quantity', (int) $offer['quantity']);
            } else {
                $e->setAttribute('quantity', 0);
            }

            foreach ($offer['categoryId'] as $categoryId) {
                $e->appendChild(
                    $this->dd->createElement('categoryId', $categoryId)
                );
            }

            $offerKeys = array_keys($offer);

            foreach ($offerKeys as $key) {

                if ($offer[$key] == null) {
                    continue;
                }

                if (in_array($key, $this->properties)) {
                    if (is_array($offer[$key])) {
                        foreach($offer[$key] as $property) {
                            $e
                                ->appendChild($this->dd->createElement($key))
                                ->appendChild($this->dd->createTextNode(trim($property)));
                        }
                    }
                    else {
                        $e
                            ->appendChild($this->dd->createElement($key))
                            ->appendChild($this->dd->createTextNode(trim($offer[$key])));
                    }
                }
            }

            if (is_array($offer["param"])) {
                foreach ($offer["param"] as $paramKey => $param) {

                    $paramElement = $this->dd->createElement('param');
                    $paramElement->setAttribute('code', $paramKey);
                    $paramElement->setAttribute('name', $param["name"]);
                    $paramElement->appendChild(
                        $this->dd->createTextNode($param["value"])
                    );

                    $e->appendChild($paramElement);
                }
            }

        }
    }

    private function initServer()
    {
        $server = Main\Context::getCurrent()->getServer();
        $protocol = 'http://';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $server->getServerName());
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode === 200) {
            $protocol = 'https://';
        }

        $this->serverName = $protocol . $server->getServerName();
    }
}
