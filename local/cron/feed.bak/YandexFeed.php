<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

$_SERVER["DOCUMENT_ROOT"] = "/var/www/clients/client0/web1/private/drvt.shop/";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/FeedParams.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/FeedSlice.php");

use Bitrix\Main\Application;

class YandexFeed
{
    public $PRODUCT_IBLOCK_ID;
    public $SECTION_IDS = array();
    public $MANUFACTURER_IBLOCK_ID;

    public $MANUFACTURER_IDS;
    public $arSkructure;
    public $CATALOG_PRICE_ID;
    public $arElementParam;
    public $arVariable;
    public $SITE_SERVER_NAME = '';
    public $SITE_NAME = '';
    public $SITE_EMAIL = '';
    public $SITE_ID = '';
    public $arPropPriceUpdate;
    public $productCounter = 0;
    public $storageId;
    protected $filePath;

    protected $data = array();

    public function __construct($SITE_ID,$CATALOG_PRICE_ID)
    {
        $feedParams = new FeedParams;
        $this->SITE_ID = $SITE_ID;
        $this->PRODUCT_IBLOCK_ID = $feedParams->getIblockId()[$this->SITE_ID]['PRODUCT_IBLOCK_ID'];
        $this->MANUFACTURER_IBLOCK_ID = $feedParams->getIblockId()[$this->SITE_ID]['MANUFACTURER_IBLOCK_ID'];
        $this->MANUFACTURER_IDS = $feedParams->getIblockId()[$this->SITE_ID]['MANUFACTURER_IDS'];
        $this->arSkructure = $feedParams->getArSkructure()[$this->SITE_ID];
        $this->CATALOG_PRICE_ID = $CATALOG_PRICE_ID;
        $this->arElementParam = $feedParams->getArElementParam()[$this->SITE_ID];
        $this->arVariable = $feedParams->getArVariable()[$this->SITE_ID];
        $this->arPropPriceUpdate = $feedParams->getArPropPriceUpdate()[$this->CATALOG_PRICE_ID];
        $this->filePath = $feedParams->getArPathSite()[$this->SITE_ID].$feedParams->getArPathCatalogGroup()[$this->CATALOG_PRICE_ID];
        $this->storageId = $feedParams->getStorageIdByCatalogGroup()[$this->SITE_ID][$this->CATALOG_PRICE_ID];
        $rsSites = CSite::GetByID($this->SITE_ID);
        $arSite = $rsSites->Fetch();
        $this->SITE_SERVER_NAME = $arSite["SERVER_NAME"];
        $this->SITE_NAME = $arSite["NAME"];
        $this->SITE_EMAIL = $arSite["EMAIL"];


    }

    public function Generate()
    {

        if( $this->Load() ){
            $this->WriteFile($this->filePath);
            return $this->productCounter;
        }
        else{
            return false;
        }
    }

    protected function Load()
    {
        \CModule::IncludeModule('iblock');
        \CModule::IncludeModule('catalog');

        $this->data = array(
            'manufacturers' => array(),
            'sections' => array(),
            'elements' => array(),
            'pictures' => array(),
            'products' => array(),
            'product_prices' => array(),
            'product_optimal_prices' => array(),
        );
        // manufacturer fetch
        $manufacturerIds = array();
        $ob = \CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => $this->MANUFACTURER_IBLOCK_ID,
                'ACTIVE' => 'Y',

                #'ID' => $this->MANUFACTURER_IDS
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
            )
        );
        while ($row = $ob->Fetch())
        {
            $manufacturerId = $row['ID'];
            $manufacturerName = htmlspecialchars($row['NAME']);
            $manufacturerCode = $row['CODE'];

            $manufacturerIds[] = $manufacturerId;
            $this->data['manufacturers'][$manufacturerId] = array(
                'id' => $manufacturerId,
                'name' => $manufacturerName,
                'code' => $manufacturerCode,
            );
        }
        unset($ob);

        // section fetch
        $sectionIds = array();
        $ob = \CIBlockSection::GetList(
            array(
                'DEPTH_LEVEL' => 'ASC',
                'ID' => 'ASC',
            ),
            array(
                'ACTIVE' => 'Y',
                'GLOBAL_ACTIVE' => 'Y',
                'IBLOCK_ID' => $this->PRODUCT_IBLOCK_ID,
                #'ID' => $this->SECTION_IDS
            ),
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
                'IBLOCK_SECTION_ID',
            )
        );
        while ($row = $ob->Fetch())
        {
            $sectionId = $row['ID'];
            $sectionName = $row['NAME'];
            $sectionCode = $row['CODE'];
            $sectionParentId = $row['IBLOCK_SECTION_ID'];

            $sectionIds[] = $sectionId;
            $this->data['sections'][$sectionId] = array(
                'id' => $sectionId,
                'name' => $sectionName,
                'code' => $sectionCode,
                'parent_id' => $sectionParentId,
            );
        }
        unset($ob);

        // elements fetch
        $pictureIds = array();
        $ob = \CIBlockElement::GetList(
            array(
                'ID' => 'ASC',
            ),
            array(
                'IBLOCK_ID' => $this->PRODUCT_IBLOCK_ID,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'PROPERTY_MANUFACTURER' => $manufacturerIds,
                'IBLOCK_SECTION_ID' => $sectionIds,
                $this->arPropPriceUpdate => 0,
                #'!PROPERTY_DISCONTINUED' => 'Y'
            ),
            false,
            false,
            $this->arElementParam
        );
        while ($row = $ob->GetNext())
        {
            $elementId = $row['ID'];
            $elementName = htmlspecialchars($row['NAME']);
            $elementCode = htmlspecialchars($row['CODE']);
            $sectionId = $row['IBLOCK_SECTION_ID'];
            $detailPageUrl = $row['DETAIL_PAGE_URL'];
            $pictureId = $row['DETAIL_PICTURE'];
            $typePrefix = isset($this->arVariable['typePrefix']) ? $this->arVariable['typePrefix']($row) : null;
            $artnum = $row['PROPERTY_ARTNUMBER_VALUE'];
            $manufacturerId = $row['PROPERTY_MANUFACTURER_VALUE'];
            $garant = $row['PROPERTY_GUARANTEE_VALUE'];
            $seriesId = isset($this->arVariable['seriesId']) ? $this->arVariable['seriesId']($row) : null;
            $vid_seo = isset($this->arVariable['vid_seo']) ? $this->arVariable['vid_seo']($row) : null;
            $arPropDesc = isset($this->arVariable['arPropDesc']) ? $this->arVariable['arPropDesc']($row) : null;
            $desc = $this->getDescription($sectionId, $arPropDesc);

            if (!array_key_exists($elementId, $this->data['elements']))
            {
                if (!!$pictureId) $pictureIds[] = $pictureId;

                $this->data['elements'][$elementId] = array(
                    'id' => null,
                    'name' => null,
                    'code' => null,
                    'section_id' => null,
                    'detail_page_url' => null,
                    'picture_id' => null,
                    'tip' => null,
                    'artnum' => null,
                    'manufacturer_id' => null,
                    'description' => null,
                    'seriesId' => null,
                    'vid_seo' => null,
                );

                $this->data['elements'][$elementId]['id'] = $elementId;
                $this->data['elements'][$elementId]['name'] = $elementName;
                $this->data['elements'][$elementId]['code'] = $elementCode;
                $this->data['elements'][$elementId]['section_id'] = $sectionId;
                $this->data['elements'][$elementId]['detail_page_url'] = $detailPageUrl;
                $this->data['elements'][$elementId]['picture_id'] = $pictureId;
                $this->data['elements'][$elementId]['tip'] = $typePrefix;
                $this->data['elements'][$elementId]['artnum'] = $artnum;
                $this->data['elements'][$elementId]['manufacturer_id'] = $manufacturerId;
                $this->data['elements'][$elementId]['description'] = $desc;
                $this->data['elements'][$elementId]['garant'] = $garant;
                $this->data['elements'][$elementId]['seriesId'] = $seriesId;
                $this->data['elements'][$elementId]['vid_seo'] = $vid_seo;
            }
        }
        unset($ob);

        //unset products if no have in Storage
        $this->checkStorage();

        // picture fetch
        foreach ($pictureIds as $pictureId)
        {
            $filePath = \CFile::GetPath($pictureId);

            $this->data['pictures'][$pictureId] = $filePath;
        }

        // products fetch
        $productIds = array();
        $ob = \Bitrix\Catalog\ProductTable::getList(array(
            'filter' => array(
                'ID' => array_keys($this->data['elements']),
            ),
            'select' => array(
                'ID',
                'AVAILABLE',
                'QUANTITY',
            ),
        ));
        
        while ($row = $ob->fetch())
        {
            $productId = $row['ID'];
            $productAvailable = "Y";
            $productQuantity = 100;

            $productIds[] = $productId;
            $this->data['products'][$productId] = array(
                'id' => $productId,
                'available' => $productAvailable == 'Y',
                'quantity' => floatval($productQuantity),
            );
        }
        unset($ob);

        $ob = \CIBlockElement::GetList(
            array(
                'ID' => 'ASC',
            ),
            array(
                'IBLOCK_ID' => $this->PRODUCT_IBLOCK_ID,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'ID' => GIFT_PRODUCTS
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'DETAIL_PICTURE'
            )
        );
        while ($row = $ob->GetNext())
        {
            $this->data['gift_lampochka'][$row['ID']]['id'] = $row['ID'];
            $this->data['gift_lampochka'][$row['ID']]['name'] = htmlspecialchars($row['NAME']);
            $this->data['gift_lampochka'][$row['ID']]['picture_id'] = $row['DETAIL_PICTURE'];
            
            $pictureIds[] = $row['DETAIL_PICTURE'];
            
        }
        unset($ob);

        // price fetch
        $ob = \Bitrix\Catalog\PriceTable::getList(array(
            'filter' => array(
                'PRODUCT_ID' => $productIds,
                'CATALOG_GROUP_ID' => $this->CATALOG_PRICE_ID,
            ),
            'select' => array(
                'ID',
                'PRICE',
                'PRODUCT_ID',
                'CURRENCY',
                'CATALOG_GROUP_ID',
            ),
        ));

        $arProductPrices = [];
        while ($row = $ob->fetch())
        {
            $this->productCounter++;
            $productId = $row['PRODUCT_ID'];
            $price = $row['PRICE'];

            $arProductPrices[$productId] = 
            [
                'ID' => $row['ID'],
                'PRICE' => $row['PRICE'],
                'CURRENCY' => $row['CURRENCY'],
                'CATALOG_GROUP_ID' => $row['CATALOG_GROUP_ID'],
                'optimal_price' => null
            ];

            $this->data['product_prices'][$productId] = $price;

        }

        // if ($this->productCounter > 1000){
        $feedSlice = new FeedSlice;

        $res = $feedSlice->run($this->SITE_ID, $arProductPrices);

        if($res === false || $res === NULL){
            return false;
        }
        foreach ($res as $id => $value) {
            $this->data['product_optimal_prices'][$id] = $value['optimal_price'];
        }

        return true;
    }

    protected function checkStorage(){
        $amountProductsObj = \Bitrix\Catalog\StoreProductTable::getList(array(
            'filter' => array(
                'PRODUCT_ID' => array_keys($this->data['elements']),
                'STORE_ID' => $this->storageId,
            ),
            'select' => array(
                'PRODUCT_ID',
                'AMOUNT'
            )
        ));

        while ($amountProducts = $amountProductsObj->Fetch()) {
            if($amountProducts['AMOUNT'] < 1){
                unset($this->data['elements'][$amountProducts['PRODUCT_ID']]);
            }
        }
    }

    protected function WriteFile($filePath)
    {
        $h = fopen($filePath, 'w');
        if (!$h) return false;

        fwrite($h, "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">" . PHP_EOL);
        fwrite($h, "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">" . PHP_EOL);
        fwrite($h, "<yml_catalog date=\"" . date('Y-m-d H:i') . "\">" . PHP_EOL);
        fwrite($h, "\t<shop>" . PHP_EOL);
        fwrite($h, "\t\t<name>".$this->SITE_NAME."</name>" . PHP_EOL);
        fwrite($h, "\t\t<company>".$this->SITE_NAME."</company>" . PHP_EOL);
        fwrite($h, "\t\t<url>https://".$this->SITE_SERVER_NAME."</url>" . PHP_EOL);
        fwrite($h, "\t\t<platform>1C-Bitrix</platform>" . PHP_EOL);
        fwrite($h, "\t\t<version>18.0.2</version>" . PHP_EOL);
        fwrite($h, "\t\t<email>".$this->SITE_EMAIL."</email>" . PHP_EOL);
        fwrite($h, "\t\t<currencies>" . PHP_EOL);
        fwrite($h, "\t\t\t<currency id=\"RUB\" rate=\"1\" />" . PHP_EOL);
        fwrite($h, "\t\t</currencies>" . PHP_EOL);
        fwrite($h, "\t\t<categories>" . PHP_EOL);
        $this->WriteFileCategoties($h);
        fwrite($h, "\t\t</categories>" . PHP_EOL);
        fwrite($h, "\t\t<offers>" . PHP_EOL);
        $this->WriteFileOffers($h);
        fwrite($h, "\t\t</offers>" . PHP_EOL);
        $this->WriteGifts($h);
        fwrite($h, "\t</shop>" . PHP_EOL);
        fwrite($h, "</yml_catalog>" . PHP_EOL);
        fclose($h);
    }

    private function WriteGifts($h)
    {
        if(!empty($this->data['gift_lampochka'])){
            
            $request = Application::getInstance()->getContext()->getRequest();
            
            $protocol = $request->isHttps() ? 'https://' : 'http://';
            $siteName = $protocol . $this->SITE_SERVER_NAME;
            
            fwrite($h, "\t\t<gifts>" . PHP_EOL);
            foreach ($this->data['gift_lampochka'] as $arItem)
            {
                $picture = null;
                $pictureId = $arItem['picture_id'];
                if ($pictureId)
                {
                    $picturePath = $this->data['pictures'][$pictureId];
                    $picture = $siteName . $picturePath;
                }
        
                fwrite($h, "\t\t\t<gift id=\"".$arItem['id']."\">" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<name>".$arItem['name']."</name>" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<picture>".$picture."</picture>" . PHP_EOL);
                fwrite($h, "\t\t\t</gift>" . PHP_EOL);
            }   
            fwrite($h, "\t\t</gifts>" . PHP_EOL);
            
            fwrite($h, "\t\t<promos>" . PHP_EOL);
                fwrite($h, "\t\t\t<promo id=\"Promo401\" type=\"gift with purchase\">" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<start-date>2019-05-01</start-date>" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<end-date>2019-09-30</end-date>" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<description>Подарок при покупке</description>" . PHP_EOL);
                    fwrite($h, "\t\t\t\t<purchase>" . PHP_EOL);
                        foreach ($this->data['elements'] as $elementId => $element)
                        {
                            $productId = $elementId;
                            $manufacturerId = $element['manufacturer_id'];
                            $price = floatval($this->data['product_prices'][$productId]);
                            
                            if(in_array($manufacturerId, GIFT_BRANDS) && $price > 2000){
                                fwrite($h, "\t\t\t\t\t<product offer-id=\"".$productId."\"></product>" . PHP_EOL);
                            }
                        }
                    fwrite($h, "\t\t\t\t</purchase>" . PHP_EOL);
                    
                    fwrite($h, "\t\t\t\t<promo-gifts>" . PHP_EOL);
                        fwrite($h, "\t\t\t\t\t<promo-gift gift-id=\"".GIFT_PRODUCTS."\"></promo-gift>" . PHP_EOL);
                    fwrite($h, "\t\t\t\t</promo-gifts>" . PHP_EOL);
                    
                fwrite($h, "\t\t\t</promo>" . PHP_EOL);
            fwrite($h, "\t\t</promos>" . PHP_EOL);
        }
        
    }

    private function WriteFileCategoties($h)
    {
        foreach ($this->data['sections'] as $section)
        {
            if (!!$section['parent_id'])
            {
                fwrite($h, "\t\t\t<category id=\"" . $section['id'] ."\" parentId=\"" . $section['parent_id'] . "\">" . htmlspecialchars($section['name']) . "</category>" . PHP_EOL);
            }
            else
            {
                fwrite($h, "\t\t\t<category id=\"" . $section['id'] ."\">" . htmlspecialchars($section['name']) . "</category>" . PHP_EOL);
            }
        }
    }

    private function WriteFileOffers($h)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        $protocol = $request->isHttps() ? 'https://' : 'http://';
        $siteName = $protocol . $this->SITE_SERVER_NAME;

        foreach ($this->data['elements'] as $elementId => $element)
        {
            $productId = $elementId;
            $product = $this->data['products'][$productId];

            // data
            $available = $product['available'] ? 'true' : 'false';

            $manufacturerId = $element['manufacturer_id'];
            $manufacturer = $this->data['manufacturers'][$manufacturerId];

            $vendor = $manufacturer['name'];
            $vendorCode = $element['artnum'];

            $model = $element['name'];

            $typePrefix = is_array($element['tip']) > 0
                ? implode('; ', $element['tip'])
                : $element['tip'];

            $categoryId = $element['section_id'];

            $url = $protocol.$this->SITE_SERVER_NAME.$element['detail_page_url'].'?utm_source=yandex_market&amp;utm_medium=cpc&amp;utm_campaign='.$this->data['sections'][$categoryId]['code'].'&amp;utm_content='.$element['code'].'&amp;utm_term='.$productId;

            $desc = $element['description'];

            $garant = $element['garant'];
            if(!empty($garant)){
                $garantBool = 'true';
            }
            else {
                $garantBool = 'false';
            }

            $price = floatval($this->data['product_prices'][$productId]);
            $optimalPrice = $this->data['product_optimal_prices'][$productId];
            $discountPrice = floatval($optimalPrice);

            //пропускаем товары с ценой менее 3000
            if ($price < 3000) continue;

            //пропускаем бренды
            if ($this->SITE_SERVER_NAME == 'tiptop-shop.ru' && in_array($vendor, array("Roca", "Geberit", "Jacob Delafon", "Duravit","Hansgrohe","Grohe"))) continue; 
            if ($this->SITE_SERVER_NAME == 'shop-roca.ru' && $vendor !== "Roca" ) continue;
            if ($this->SITE_SERVER_NAME == 'shop-jd.ru' && $vendor !== "Jacob Delafon" ) continue;
            if ($this->SITE_SERVER_NAME == 'shop-gr.ru' && $vendor !== "Grohe" ) continue;
            if ($this->SITE_SERVER_NAME == 'hg-online.ru' && $vendor !== "Hansgrohe" ) continue;
            if ($this->SITE_SERVER_NAME == 'geberit-shop.ru' && !in_array($vendor, array("Geberit", "Ifo", "Ido", "Keramag"))) continue; 

            $picture = null;
            $pictureId = $element['picture_id'];
            if ($pictureId)
            {
                $picturePath = $this->data['pictures'][$pictureId];
                $picture = $siteName . $picturePath;
            }
           
           // fwrite($h, "<!-------". $vendor . "\" available=\"" . $this->SITE_SERVER_NAME . "----->" . PHP_EOL);

            // output
            fwrite($h, "\t\t\t<offer id=\"". $productId . "\" available=\"" . $available . "\">" . PHP_EOL);
            fwrite($h, "\t\t\t\t<url>" . $url . "</url>" . PHP_EOL);
            if ($discountPrice < $price) {
                fwrite($h, "\t\t\t\t<price>" . number_format($discountPrice, 0, '.', '') . "</price>" . PHP_EOL);
                fwrite($h, "\t\t\t\t<oldprice>" . number_format($price, 0, '.', '') . "</oldprice>" . PHP_EOL);
            } else {
                fwrite($h, "\t\t\t\t<price>" . number_format($price, 0, '.', '') . "</price>" . PHP_EOL);
            }
            fwrite($h, "\t\t\t\t<sales_notes>Консультации от эксперта VISA/MASTER Наличные</sales_notes>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<currencyId>RUB</currencyId>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<categoryId>" . $categoryId . "</categoryId>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<name>" . $model . "</name>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<vendor>" . $vendor . "</vendor>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<vendorCode>" . $vendorCode . "</vendorCode>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<description>". $desc . "</description>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<manufacturer_warranty>". $garantBool . "</manufacturer_warranty>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<cpa>" . '1' . "</cpa>" . PHP_EOL);

            if ($picture)
            {
                fwrite($h, "\t\t\t\t<picture>" . $picture . "</picture>" . PHP_EOL);
            }

            fwrite($h, "\t\t\t\t<param name=\"Тип продукта\">" . $typePrefix . "</param>" . PHP_EOL);

            if (!!$vendor)
            {
                fwrite($h, "\t\t\t\t<param name=\"Бренд\">" . $vendor . "</param>" . PHP_EOL);
            }
            fwrite($h, "\t\t\t</offer>" . PHP_EOL);

        }
    }

    public function getDescription($sectionId, $arProp){

        $strDesc = "";

        if(empty($this->data['sections'][$sectionId]['parent_id'])){
            $sectionParentId = $sectionId;
        }
        else {
            $sectionParentId = $this->data['sections'][$sectionId]['parent_id'];
        }

        $arCurSec = $this->arSkructure[$sectionParentId];

        //Если для текущего товара не предусмотренно описание, пропускаем
        if(count($arCurSec) == 0){
            return $strDesc;
        }

        foreach($arCurSec as $nameProp){
            if(!empty($arProp[$nameProp])){
                $v = $arProp[$nameProp];
                if(is_array($arProp[$nameProp])){
                    
                    $i = 0;
                    $values = '';
                    foreach($v as $item){
                        $i++;
                        $values .= $item;
                        
                        if($i < count($v)){
                            $values .= ', ';
                        }
                    }
                    
                    $v = $values;
                }

                $strDesc .= $nameProp.": ".$v."; ";

            }
        }

        return $strDesc;

    }
}