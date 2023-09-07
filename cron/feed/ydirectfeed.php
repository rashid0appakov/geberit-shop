<?php
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

class YandexFeed
{
    const PRODUCT_IBLOCK_ID = CATALOG_IBLOCK_ID;
	const SECTION_IDS = array();
    const MANUFACTURER_IBLOCK_ID = BRANDS_IBLOCK_ID;
	const MANUFACTURER_IDS = FEEDS_BRAND;
	const CATALOG_PRICE_ID = 1;

    public $SITE_SERVER_NAME = '';
	public $SITE_NAME = '';
	public $SITE_EMAIL = '';

    protected $data = array();

    public function __construct()
    {
		$rsSites = CSite::GetByID('s8');
		$arSite = $rsSites->Fetch();
		$this->SITE_SERVER_NAME = $arSite["SERVER_NAME"];
		$this->SITE_NAME = $arSite["NAME"];
		$this->SITE_EMAIL = $arSite["EMAIL"];
    }

    public function Generate($filePath)
    {
        $this->Load();
        $this->WriteFile($filePath);
    }

    protected function Load()
    {
        \CModule::IncludeModule('iblock');
        \CModule::IncludeModule('catalog');

        $this->data = array(
            'manufacturers' => array(),
            'sections' => array(),
            'elements' => array(),
            'sectionsLampa' => array(),
            'elementsLampa' => array(),
            'pictures' => array(),
            'products' => array(),
            'collections' => array(),
            'interiors' => array(),

            'product_prices' => array(),
            'product_discounts' => array(),
        );

        // manufacturer fetch
        $manufacturerIds = array();
        $ob = \CIBlockElement::GetList(
            array(), 
            array(
                'IBLOCK_ID' => self::MANUFACTURER_IBLOCK_ID,
                'ACTIVE' => 'Y',
				'ID' => self::MANUFACTURER_IDS
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
                'IBLOCK_ID' => self::PRODUCT_IBLOCK_ID,
				'ID' => self::SECTION_IDS
            ),
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'IBLOCK_SECTION_ID',
            )
        );
        while ($row = $ob->Fetch())
        {
			
			$sectionId = $row['ID'];
            $sectionName = $row['NAME'];
            $sectionParentId = $row['IBLOCK_SECTION_ID'];

            $sectionIds[] = $sectionId;
            $this->data['sections'][$sectionId] = array(
                'id' => $sectionId,
                'name' => $sectionName,
                'parent_id' => $sectionParentId,
            );
			
			$rsParentSection = CIBlockSection::GetByID($row['ID']);
			
			if ($arParentSection = $rsParentSection->GetNext())
			{
			   $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], 'ACTIVE' => 'Y', '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']);
			   
			   $rsSect = \CIBlockSection::GetList(
					array('left_margin' => 'asc'),
					$arFilter,
					false,
					array(
						'ID',
						'IBLOCK_ID',
						'NAME',
						'IBLOCK_SECTION_ID',
					)
				);
					
				while ($arSect = $rsSect->GetNext())
				{
					$sectionId = $arSect['ID'];
					$sectionName = $arSect['NAME'];
					$sectionParentId = $arSect['IBLOCK_SECTION_ID'];

					$sectionIds[] = $sectionId;
					$this->data['sections'][$sectionId] = array(
						'id' => $sectionId,
						'name' => $sectionName,
						'parent_id' => $sectionParentId,
					);
				}
			}
        }
        unset($ob);
		
        // elements fetch
        $elementIds = array();
        $pictureIds = array();
        $interiorIds = array();
        $ob = \CIBlockElement::GetList(
            array(
                'ID' => 'ASC',
            ),
            array(
                'IBLOCK_ID' => self::PRODUCT_IBLOCK_ID,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'PROPERTY_MANUFACTURER' => $manufacturerIds,
                'IBLOCK_SECTION_ID' => $sectionIds,
				'>CATALOG_QUANTITY' => 0,
				'!PROPERTY_DISCONTINUED' => 'Y' 
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'IBLOCK_SECTION_ID',
                'DETAIL_PAGE_URL',
                'DETAIL_PICTURE',
                'PREVIEW_TEXT',
                'PROPERTY_TIP',
                'PROPERTY_ARTNUMBER',
                'PROPERTY_MANUFACTURER',
            )
        );
        while ($row = $ob->GetNext())
        {
            // static $i = 0;
            // if ($i++ >= 50) break;

            $elementId = $row['ID'];
            $elementName = htmlspecialchars($row['NAME']);
            $sectionId = $row['IBLOCK_SECTION_ID'];
            $detailPageUrl = $row['DETAIL_PAGE_URL'];
            $pictureId = $row['DETAIL_PICTURE'];
            $prevewText = $row['PREVIEW_TEXT'];
            $previewTextRaw = $row['~PREVIEW_TEXT'];
            $previewTextType = $row['PREVIEW_TEXT_TYPE'];
            $typePrefix = $row['PROPERTY_TIP_VALUE'];
            $artnum = $row['PROPERTY_ARTNUMBER_VALUE'];
            $manufacturerId = $row['PROPERTY_MANUFACTURER_VALUE'];

            if (!array_key_exists($elementId, $this->data['elements']))
            {
                $elementIds[] = $elementId;
                if (!!$pictureId) $pictureIds[] = $pictureId;

                $this->data['elements'][$elementId] = array(
                    'id' => null,
                    'name' => null,
                    'section_id' => null,
                    'detail_page_url' => null,
                    'picture_id' => null,
                    'preview_text' => array(
                        'formated' => null,
                        'raw' => null,
                        'type' => null,
                    ),
                    'tip' => null,
                    'artnum' => null,
                    'manufacturer_id' => null,
                );

                $this->data['elements'][$elementId]['id'] = $elementId;
                $this->data['elements'][$elementId]['name'] = $elementName;
                $this->data['elements'][$elementId]['section_id'] = $sectionId;
                $this->data['elements'][$elementId]['detail_page_url'] = $detailPageUrl;
                $this->data['elements'][$elementId]['picture_id'] = $pictureId;
                $this->data['elements'][$elementId]['preview_text']['formated'] = $prevewText;
                $this->data['elements'][$elementId]['preview_text']['raw'] = $previewTextRaw;
                $this->data['elements'][$elementId]['preview_text']['type'] = $previewTextType;
                $this->data['elements'][$elementId]['tip'] = $typePrefix;
                $this->data['elements'][$elementId]['artnum'] = $artnum;
				$this->data['elements'][$elementId]['manufacturer_id'] = $manufacturerId;
            }
        }
        
        // picture fetch
        foreach ($pictureIds as $pictureId)
        {
            $filePath = \CFile::GetPath($pictureId);

            $this->data['pictures'][$pictureId] = $filePath;
        }
		
        // products fetch
        $productids = array();
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

        // price fetch
        $ob = \Bitrix\Catalog\PriceTable::getList(array(
            'filter' => array(
                'PRODUCT_ID' => $productIds,
                'CATALOG_GROUP_ID' => self::CATALOG_PRICE_ID,
            ),
            'select' => array(
                'PRICE',
                'PRODUCT_ID',
            ),
        ));
        while ($row = $ob->fetch())
        {
            $productId = $row['PRODUCT_ID'];
            $price = $row['PRICE'];

            $this->data['product_prices'][$productId] = $price;
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
        fwrite($h, "\t</shop>" . PHP_EOL);
        fwrite($h, "</yml_catalog>" . PHP_EOL);
        fclose($h);
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
    private function WriteFileCategoties2($h)
    {
        foreach ($this->data['sectionsLampa'] as $section)
        {
            if (!!$section['parent_id'])
            {
                fwrite($h, "\t\t\t\t\t<product category-id=\"" . $section['id'] ."\"/>" . PHP_EOL);
            }
            else
            {
                fwrite($h, "\t\t\t\t\t<product category-id=\"" . $section['id'] ."\"/>" . PHP_EOL);
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

            $url = $protocol.$this->SITE_SERVER_NAME.$element['detail_page_url']; 

            $price = floatval($this->data['product_prices'][$productId]);

            if ($price < 4000) continue;

            $picture = null;
            $pictureId = $element['picture_id'];
            if ($pictureId)
            {
                $picturePath = $this->data['pictures'][$pictureId];
                $picture = $siteName . $picturePath;
            }

            // output
            fwrite($h, "\t\t\t<offer id=\"". $productId . "\" available=\"" . $available . "\">" . PHP_EOL);
            fwrite($h, "\t\t\t\t<url>" . $url . "</url>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<price>" . number_format($price, 0, '.', '') . "</price>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<currencyId>RUB</currencyId>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<categoryId>" . $categoryId . "</categoryId>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<name>" . $model . "</name>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<vendor>" . $vendor . "</vendor>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<vendorCode>" . $vendorCode . "</vendorCode>" . PHP_EOL);
            fwrite($h, "\t\t\t\t<description>");
            fwrite($h, "</description>" . PHP_EOL);
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
}

ini_set('memory_limit', '2048M');
$feed = new YandexFeed();
$feed->Generate($_SERVER['DOCUMENT_ROOT'].'/feed/ydirectfeed.xml');