<?php
namespace WebServiceCrm;

class Xml
{
    protected $shop;
    protected $file;
    protected $properties;
    protected $params;
    protected $dd;
    protected $eCategories;
    protected $eOffers;

    public function __construct($shop, $file)
    {
        $this->shop = $shop;
        $this->file = $file;

        $this->properties = array(
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
        );
    }

    public function generate($offers, $categories = null)
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

        $this->addCategories($categories);
        $this->addOffers($offers);

        $this->dd->saveXML();
        $this->dd->save($this->file);
    }

    private function addCategories($categories)
    {
        foreach ($categories as $category) {
            $e = $this->eCategories->appendChild(
                $this->dd->createElement(
                    'category', $category['name']
                )
            );

            $e->setAttribute('id', $category['id']);

            if ($category['parentId'] > 0) {
                $e->setAttribute('parentId', $category['parentId']);
            }
        }
    }

    private function addOffers($offers)
    {

        foreach ($offers as $offerKey => $offer) {

            $e = $this->eOffers->appendChild(
                $this->dd->createElement('offer')
            );

            $e->setAttribute('id', $offerKey);
            $e->setAttribute('productId', $offer['productId']);

            if (isset($offer['additionalProducts']) && is_array($offer['additionalProducts']) && count($offer['additionalProducts']) > 0) {
                $e->setAttribute('additionalProducts', trim(implode(',', $offer['additionalProducts'])));
            }

            if (isset($offer['quantity']) && ! empty($offer['quantity'])) {
                $e->setAttribute('quantity', (int) $offer['quantity']);
            }
            else {
                $e->setAttribute('quantity', 0);
            }

            if (isset($offer['categoryId']) && is_array($offer['categoryId'])) {
                foreach ($offer['categoryId'] as $categoryId) {
                    $e->appendChild(
                        $this->dd->createElement('categoryId', $categoryId)
                    );
                }
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
}