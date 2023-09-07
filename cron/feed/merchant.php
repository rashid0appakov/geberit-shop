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
            'pictures' => array(),
            'products' => array(),
            'product_prices' => array(),
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
        }
        unset($ob);
		
        // elements fetch
        $pictureIds = array();
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
                'PROPERTY_TIP',
                'PROPERTY_ARTNUMBER',
                'PROPERTY_MANUFACTURER',
                'PROPERTY_COLOR',
                'PROPERTY_NAZNACHENIE',
                'PROPERTY_UPRAVLENIE',
                'PROPERTY_FORMA',
                'PROPERTY_STILISTIKA',
                'PROPERTY_MONTAZH',
                'PROPERTY_GUARANTEE',
                'PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA',
                'PROPERTY_SISTEMA_ANTIVSPLESK',
                'PROPERTY_BEZOBODKOVIY',
                'PROPERTY_SHIRINA',
                'PROPERTY_PERELIV',
                'PROPERTY_TIP_RAKOVINY',
                'PROPERTY_OTVERSTIYA_POD_SMESITEL',
                'PROPERTY_MATERIAL',
				//'PROPERTY_POKRYTIE_KORPUSA',
                //'PROPERTY_DLINA',
                //'PROPERTY_KOLICHESTVO_CHELOVEK',
                //'PROPERTY_VYSOTA',
                //'PROPERTY_TERMOREGULYATOR',
                //'PROPERTY_COUNTRY',
                //'PROPERTY_METOD_KREPLENIYA',
            )
        );
        while ($row = $ob->GetNext())
        {
            $elementId = $row['ID'];
            $elementName = htmlspecialchars($row['NAME']);
            $sectionId = $row['IBLOCK_SECTION_ID'];
            $detailPageUrl = $row['DETAIL_PAGE_URL'];
            $pictureId = $row['DETAIL_PICTURE'];
            $typePrefix = $row['PROPERTY_TIP_VALUE'];
            $artnum = $row['PROPERTY_ARTNUMBER_VALUE'];
            $manufacturerId = $row['PROPERTY_MANUFACTURER_VALUE'];
			
			$arPropDesc = array(
				'Цвет' => 							$row['PROPERTY_COLOR_VALUE'],
				'Назначение' => 					$row['PROPERTY_NAZNACHENIE_VALUE'],
				'Управление' => 					$row['PROPERTY_UPRAVLENIE_VALUE'],
				'Форма' => 							$row['PROPERTY_FORMA_VALUE'],
				'Стилистика дизайна' => 			$row['PROPERTY_STILISTIKA_VALUE'],
				'Монтаж' => 						$row['PROPERTY_MONTAZH_VALUE'],
				'Гарантия' => 						$row['PROPERTY_GUARANTEE_VALUE'],
				'Организация смывающего потока' => 	$row['PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA_VALUE'],
				'Система антивсплеск' => 			$row['PROPERTY_SISTEMA_ANTIVSPLESK_VALUE'],
				'Безободковый' => 					$row['PROPERTY_BEZOBODKOVIY_VALUE'],
				'Ширина' => 						$row['PROPERTY_SHIRINA_VALUE'],
				'Перелив' => 						$row['PROPERTY_PERELIV_VALUE'],
				'Тип раковины' => 					$row['PROPERTY_TIP_RAKOVINY_VALUE'],
				'Отверстия под смеситель' => 		$row['PROPERTY_OTVERSTIYA_POD_SMESITEL_VALUE'],
				'Материал' => 						$row['PROPERTY_MATERIAL_VALUE'],
				'Покрытие корпуса' => 				$row['PROPERTY_POKRYTIE_KORPUSA_VALUE'],
				'Длина' => 							$row['PROPERTY_DLINA_VALUE'],
				'Количество человек' => 			$row['PROPERTY_KOLICHESTVO_CHELOVEK_VALUE'],
				'Высота' => 						$row['PROPERTY_VYSOTA_VALUE'],
				'Терморегулятор' => 				$row['PROPERTY_TERMOREGULYATOR_VALUE'],
				'Страна' => 						$row['PROPERTY_COUNTRY_VALUE'],
				'Тип' => 							$row['PROPERTY_TIP_VALUE'],
				'Метод крепления' => 				$row['PROPERTY_METOD_KREPLENIYA_VALUE'],
			);

			$desc = $this->getDescription($sectionId, $arPropDesc);

            if (!array_key_exists($elementId, $this->data['elements']))
            {
                if (!!$pictureId) $pictureIds[] = $pictureId;

                $this->data['elements'][$elementId] = array(
                    'id' => null,
                    'name' => null,
                    'section_id' => null,
                    'detail_page_url' => null,
                    'picture_id' => null,
                    'tip' => array(),
                    'artnum' => null,
                    'manufacturer_id' => null,
					'description' => null,
                );

                $this->data['elements'][$elementId]['id'] = $elementId;
                $this->data['elements'][$elementId]['name'] = $elementName;
                $this->data['elements'][$elementId]['section_id'] = $sectionId;
                $this->data['elements'][$elementId]['detail_page_url'] = $detailPageUrl;
                $this->data['elements'][$elementId]['picture_id'] = $pictureId;
                $this->data['elements'][$elementId]['tip'] = $typePrefix;
                $this->data['elements'][$elementId]['artnum'] = $artnum;
				$this->data['elements'][$elementId]['manufacturer_id'] = $manufacturerId;
				$this->data['elements'][$elementId]['description'] = $desc;
            }
        }
        unset($ob);
		
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
		unset($ob);

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
        fwrite($h, "<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">" . PHP_EOL);
        fwrite($h, "<channel>" . PHP_EOL);
        fwrite($h, "\t<title>".$this->SITE_NAME."</title>" . PHP_EOL);
        fwrite($h, "\t<link>https://".$this->SITE_SERVER_NAME."</link>" . PHP_EOL);
        $this->WriteFileOffers($h);
        fwrite($h, "</channel>" . PHP_EOL);
        fwrite($h, "</rss>" . PHP_EOL);
        fclose($h);
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

            $typePrefix = $element['tip'];

            $categoryId = $element['section_id'];

            $url = $protocol.$this->SITE_SERVER_NAME.$element['detail_page_url']; 
			
			$desc = $element['description'];

            $price = floatval($this->data['product_prices'][$productId]);

            $picture = null;
            $pictureId = $element['picture_id'];
            if ($pictureId)
            {
                $picturePath = $this->data['pictures'][$pictureId];
                $picture = $siteName . $picturePath;
            }
			
			if($price < 5000){
				$custom_label_0 = "Товары до 5000";
			}
			elseif($price > 5000 && $price < 10000){
				$custom_label_0 = "Товары от 5000 до 10000";
			}
			elseif($price > 10000 && $price < 15000){
				$custom_label_0 = "Товары от 10000 до 15000";
			}
			elseif($price > 15000 && $price < 20000){
				$custom_label_0 = "Товары от 15000 до 20000";
			}
			elseif($price > 20000 && $price < 25000){
				$custom_label_0 = "Товары от 20000 до 35000";
			}

            // output
            fwrite($h, "\t\t\t<item>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<link>" . $url . "</link>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:id>" . $productId . "</g:id>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:price>" . number_format($price, 0, '.', '') . "</g:price>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:condition>new</g:condition>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:availability>in stock</g:availability>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:product_type>" . $typePrefix . "</g:product_type>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:image_link>" . $picture . "</g:image_link>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:brand>" . $vendor . "</g:brand>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<title>" . $model . "</title>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<description>" . $desc . "</description>" . PHP_EOL);
			fwrite($h, "\t\t\t\t<g:custom_label_0>" . $custom_label_0 . "</g:custom_label_0>" . PHP_EOL);
            fwrite($h, "\t\t\t</item>" . PHP_EOL);
        }
    }
	
	public function getDescription($sectionId, $arProp){
		
		$strDesc = "";
		$arSkructure = array(
			469 => array('Цвет', 'Назначение', 'Управление', 'Форма', 'Стилистика дизайна', 'Монтаж', 'Гарантия'),
			468 => array('Цвет', 'Форма', 'Монтаж', 'Организация смывающего потока', 'Система антивсплеск', 'Безободковый', 'Стилистика дизайна', 'Гарантия'),
			456 => array('Ширина', 'Форма', 'Стилистика дизайна', 'Монтаж', 'Цвет', 'Перелив', 'Тип раковины', 'Отверстия под смеситель', 'Гарантия'),
			577 => array('Форма', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Отверстия под смеситель', 'Гарантия'),
			493 => array('Ширина', 'Форма', 'Материал', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Покрытие корпуса', 'Гарантия'),
			463 => array('Длина', 'Ширина', 'Форма', 'Стилистика дизайна', 'Цвет', 'Количество человек', 'Гарантия'),
			513 => array('Ширина', 'Высота', 'Форма', 'Терморегулятор', 'Материал', 'Цвет', 'Гарантия'),
			470 => array('Ширина', 'Высота', 'Страна', 'Тип', 'Метод крепления', 'Гарантия'),
		);
		
		if(empty($this->data['sections'][$sectionId]['parent_id'])){
			$sectionParentId = $sectionId;
		}
		else {
			$sectionParentId = $this->data['sections'][$sectionId]['parent_id'];
		}
		
		$arCurSec = $arSkructure[$sectionParentId];
		
		//Если для текущего товара не предусмотренно описание, пропускаем
		if(count($arCurSec) == 0){
			return $strDesc;
		}
		
		foreach($arCurSec as $nameProp){
			if(!empty($arProp[$nameProp])){
				$strDesc .= $nameProp.": ".$arProp[$nameProp]."; ";
			}
		}
		
		return $strDesc;
		
	}
}

ini_set('memory_limit', '2048M');
$feed = new YandexFeed();
$feed->Generate($_SERVER['DOCUMENT_ROOT'].'/feed/merchant.xml');