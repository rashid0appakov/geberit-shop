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
	const MANUFACTURER_IDS = array();
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
		CModule::IncludeModule('highloadblock');
		$arHLBlockTSVET = Bitrix\Highloadblock\HighloadBlockTable::getById(5)->fetch();
		$obEntityTSVET = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockTSVET);
		$strEntityDataClassTSVET = $obEntityTSVET->getDataClass();
		
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
                'IBLOCK_ID' => self::PRODUCT_IBLOCK_ID,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'PROPERTY_MANUFACTURER' => $manufacturerIds,
                'IBLOCK_SECTION_ID' => $sectionIds
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
				'CODE',
                'IBLOCK_SECTION_ID',
                'DETAIL_PAGE_URL',
                'DETAIL_PICTURE',
                'PROPERTY_TIP',
                'PROPERTY_ARTNUMBER',
                'PROPERTY_MANUFACTURER',
                'PROPERTY_TSVET',
                'PROPERTY_NAZNACHENIE',
                'PROPERTY_UPRAVLENIE',
                'PROPERTY_FORMA',
                'PROPERTY_STILISTIKA',
                'PROPERTY_MONTAZH',
                'PROPERTY_SISTEMA_ANTIVSPLESK',
                'PROPERTY_BEZOBODKOVYY',
                'PROPERTY_SHIRINA_SM',
                'PROPERTY_PERELIV',
                'PROPERTY_MATERIAL',
                'PROPERTY_DLINA_SM',
                'PROPERTY_VYSOTA_SM',
                'PROPERTY_TERMOREGULYATOR',
                'PROPERTY_TIP',
                'PROPERTY_METOD_KREPLENIYA',
            )
        );
        while ($row = $ob->GetNext())
        {
            $elementId = $row['ID'];
            $elementName = htmlspecialchars($row['NAME']);
			$elementCode = htmlspecialchars($row['CODE']);
            $sectionId = $row['IBLOCK_SECTION_ID'];
            $detailPageUrl = $row['DETAIL_PAGE_URL'];
            $pictureId = $row['DETAIL_PICTURE'];
            $typePrefix = $row['PROPERTY_TIP_VALUE'];
            $artnum = $row['PROPERTY_ARTNUMBER_VALUE'];
            $manufacturerId = $row['PROPERTY_MANUFACTURER_VALUE'];
			$garant = $row['PROPERTY_GUARANTEE_VALUE'];
			
			$tsvets = array();
			foreach($row['PROPERTY_TSVET_VALUE'] as $arTsvet){
				$rsDataTSVET = $strEntityDataClassTSVET::getList(array(
					'select' => array('UF_NAME'),
					'order' => array('ID' => 'ASC'),
					'filter' => array('UF_XML_ID' => $arTsvet)
				));
				if($arItemTSVET = $rsDataTSVET->Fetch()) {
					$tsvets[] = $arItemTSVET['UF_NAME'];
				}
			}

			$arPropDesc = array(
				'Цвет' => 							$tsvets,
				'Назначение' => 					$row['PROPERTY_NAZNACHENIE_VALUE'],
				'Управление' => 					$row['PROPERTY_UPRAVLENIE_VALUE'],
				'Форма' => 							$row['PROPERTY_FORMA_VALUE'],
				'Стилистика дизайна' => 			$row['PROPERTY_STILISTIKA_VALUE'],
				'Монтаж' => 						$row['PROPERTY_MONTAZH_VALUE'],
				'Система антивсплеск' => 			$row['PROPERTY_SISTEMA_ANTIVSPLESK_VALUE'],
				'Безободковый' => 					$row['PROPERTY_BEZOBODKOVYY_VALUE'],
				'Ширина' => 						$row['PROPERTY_SHIRINA_SM_VALUE'],
				'Перелив' => 						$row['PROPERTY_PERELIV_VALUE'],
				'Материал' => 						$row['PROPERTY_MATERIAL_VALUE'],
				'Длина' => 							$row['PROPERTY_DLINA_SM_VALUE'],
				'Высота' => 						$row['PROPERTY_VYSOTA_SM_VALUE'],
				'Терморегулятор' => 				$row['PROPERTY_TERMOREGULYATOR_VALUE'],
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
					'code' => null,
                    'section_id' => null,
                    'detail_page_url' => null,
                    'picture_id' => null,
                    'tip' => null,
                    'artnum' => null,
                    'manufacturer_id' => null,
					'description' => null,
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
                fwrite($h, "\t\t\t<category id=\"" . $section['id'] ."\" parentId=\"" . $section['parent_id'] . "\">" . htmlentities($section['name']) . "</category>" . PHP_EOL);
            }
            else
            {
                fwrite($h, "\t\t\t<category id=\"" . $section['id'] ."\">" . htmlentities($section['name']) . "</category>" . PHP_EOL);
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
			fwrite($h, "\t\t\t\t<sales_notes>Консультации от эксперта VISA/MASTER Рассрочка Нал</sales_notes>" . PHP_EOL);
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
		$arSkructure = array(
			2158 => array('Цвет', 'Назначение', 'Управление', 'Форма', 'Стилистика дизайна', 'Монтаж'),
			2144 => array('Цвет', 'Форма', 'Монтаж', 'Организация смывающего потока', 'Система антивсплеск', 'Безободковый', 'Стилистика дизайна'),
			2151 => array('Ширина', 'Форма', 'Стилистика дизайна', 'Монтаж', 'Цвет', 'Перелив', 'Тип раковины', 'Отверстия под смеситель'),
			2152 => array('Форма', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Отверстия под смеситель'),
			2176 => array('Ширина', 'Форма', 'Материал', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Покрытие корпуса'),
			2132 => array('Длина', 'Ширина', 'Форма', 'Стилистика дизайна', 'Цвет', 'Количество человек'),
			2195 => array('Ширина', 'Высота', 'Форма', 'Терморегулятор', 'Материал', 'Цвет'),
			2187 => array('Ширина', 'Высота', 'Страна', 'Тип', 'Метод крепления'),
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

ini_set('memory_limit', '2048M');
$feed = new YandexFeed();
$feed->Generate($_SERVER['DOCUMENT_ROOT'].'/feed/turbo.xml');