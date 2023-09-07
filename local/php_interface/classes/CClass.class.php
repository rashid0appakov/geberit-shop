<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

    use Bitrix\Main\Config\Option;
    use Bitrix\Sale;
    use Bitrix\Sale\Location AS CLocation;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Entity;
    use Bitrix\Highloadblock as HL;
    use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Application;

	/**
	 * Class CClass
	 *
	 * Contains most commonly used site-wide methods
	 *
	 */
	class CClass {
        const CACHE_TIME            = 360000;

        const RU_PAYMENTS_IBLOCK_ID     = 10;
        const RU_BRANDS_IBLOCK_ID       = 13;
        const RU_CATALOG_IBLOCK_ID      = 15;
        const RU_SERIES_IBLOCK_ID       = 22;
        const RU_DELIVERY_IBLOCK_ID     = 23;
        const RU_SEO_FILTER_IBLOCK_ID   = 24;
        const FEEDBACK_FORM_IBLOCK_ID   = 38;
        const RU_SERVICES_IBLOCK_ID     = 46;
        const RU_CONTACTS_IBLOCK_ID     = 47;

        const RU_ODDS_HBLOCK_ID         = 3;

        const DELIVERY_SELF_PICKUP_ID   = 3;
        const DELIVERY_SELF_SECTION_ID  = 21;
        const DELIVERY_TK_SECTION_ID    = 29;

        const ONE_CLICK_EMAIL           = 'onclick@drwt.shop'; //'test@test.ru'

        const PATH_TO_DELIVERY_LANG_FILE= '/local/templates/.default/delivery_services.php';

        public $hideH1              = FALSE;
        public $curPage             = NULL;     // Current page URL
        public $curDir              = NULL;     // Current section URL
        public $url_langs           = '';       // Url for language switcher (ru / en)
        public $arContacts          = [];

        /** @var $app \CMain */
		private $app                = NULL;

        /** @var $instance CClass */
		private static $instance = NULL;

        public static $arSortFields = [
            'rating'=> 'По популярности',
            'price' => 'По цене',
            'name'  => 'По названию',
        ];
        public static $arMobileSortFields = [
            'rating'    => ['desc'],
            'price'     => ['asc', 'desc'],
            'name'      => ['asc'],
        ];
        public static $arOrderServicesProps = [
            22, 23, 24
        ];

        private function __construct(\CMain $app) {
            global $redirects;

            $this->app      = $app;
            $this->curPage  = $app->GetCurPage();
            $this->curDir   = $app->GetCurDir();
		}

		/**
		 * Singletone implementation
		 *
		 * @return CClass
		 */
		public static function Instance() {
			if (!is_object(CClass::$instance))
				CClass::$instance = new CClass($GLOBALS['APPLICATION']);

			return CClass::$instance;
		}

        /**
		 * Check for site-root
		 *
		 * @return bool
		 */
		public function IsRoot() {
			return ($this->app->GetCurPage() === SITE_DIR && !defined("ERROR_404") );
		}

        /**
         * Get catalog sections
         */
        public static function getCatalogSection(){
            $arResult = [];

			if(!empty($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL']) and is_array($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'CATALOG_SECTIONS_'.CATALOG_IBLOCK_ID;
				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arSections"]) && (count($res["arSections"]) > 0))
					   $arResult = $res["arSections"];
				}

				if (empty($arResult)){
					$arDPUF = [];
					CModule::IncludeModule("iblock");
					// -- Get user firld ID by XML_ID --------------------------- //
					$arDPFilter = [
						'XML_ID'    => 'UF_DISPLAY_PARAMS',
						'ENTITY_ID' => 'IBLOCK_'.CATALOG_IBLOCK_ID.'_SECTION'
					];
					$rsData = CUserTypeEntity::GetList([], $arDPFilter);
					if ($arRes = $rsData->Fetch()){
						$DPID   = $arRes['ID'];

						// -- Get diplay params list ---------------------------- //
						$obEnum = new \CUserFieldEnum;
						$ufFields = $obEnum->GetList(['SORT' => 'ASC'], ['USER_FIELD_ID' => $DPID]);
						while($arProp = $ufFields->Fetch())
							$arDPUF[$arProp['ID']] = $arProp;
					}

					$arSelect   = [
						"ID", "IBLOCK_ID", "NAME", "SECTION_PAGE_URL", "CODE",
						"IBLOCK_SECTION_ID", "UF_*", "DEPTH_LEVEL"
					];
					$arFilter   = Array(
						"IBLOCK_ID"     => CATALOG_IBLOCK_ID,
						"ACTIVE"        => "Y",
						"GLOBAL_ACTIVE" => "Y"
					);

					$resItems   = CIBlockSection::GetList(Array('SORT' => 'ASC'), $arFilter, false, $arSelect);
					while($arItem = $resItems->GetNext()){
						if ($arItem['UF_DISPLAY_PARAMS'])
							foreach($arItem['UF_DISPLAY_PARAMS'] AS &$propID)
								$arItem['DISPLAY_PARAMS'][] = $arDPUF[$propID]['XML_ID'];

						$arResult[$arItem['ID']]  = $arItem;
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arSections" => $arResult));
				}
				*/
			}
            return $arResult;
        }

        /**
         * Get section items count by brands
         * @return array
         */
        public static function getBrandsSectionItemsCount(){
            $arResult = [];

			if(!empty($GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT']) and is_array($GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['BRAND_ITEMS_COUNT'];
			}
			else
			{
				/*
				$arSections = self::getCatalogSection();

				$cache = new CPHPCache();
				$cache_id = 'BRANDS_SECTIONS_ITEMS_'.BRANDS_IBLOCK_ID;
				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arSections"]) && (count($res["arSections"]) > 0))
					   $arResult = $res["arSections"];
				}

				if (empty($arResult)){
					CModule::IncludeModule("iblock");
					$arSelect   = [
						"ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_MANUFACTURER"
					];
					$arFilter   = [
						'IBLOCK_ID' => CATALOG_IBLOCK_ID,
						'ACTIVE'    => "Y"
					];

					$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 50000), $arSelect);
					while ($arItem = $dbItems->GetNext()){
						if (!empty($arSections[$arItem['IBLOCK_SECTION_ID']]))
						{
							if (!isset($arResult[$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']])){
								$arResult[$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']] = [
									'NAME'              => $arSections[$arItem['IBLOCK_SECTION_ID']]['NAME'],
									'SECTION_PAGE_URL'  => $arSections[$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']
								] ;
							}

							$arResult[$arItem['PROPERTY_MANUFACTURER_VALUE']][$arItem['IBLOCK_SECTION_ID']]['ITEMS_COUNT'] += 1;
						}
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arSections" => $arResult));
				}
				*/
			}
            return $arResult;
        }

        /**
         * Get product odds list
         * @return boolean|array
         */
        public static function getProductOds(){
            $arResult = [];

			if(!empty($GLOBALS['PAGE_DATA']['PRODUCT_ODS']) and is_array($GLOBALS['PAGE_DATA']['PRODUCT_ODS']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['PRODUCT_ODS'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'PRODUCT_ODS';
				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arResult"]) && (count($res["arResult"]) > 0))
					   $arResult = $res["arResult"];
				}

				if (empty($arResult)){
					if (!Loader::includeModule("highloadblock"))
						return FALSE;

					$arHLBlock = HL\HighloadBlockTable::getById(3)->fetch();
					$obEntity = HL\HighloadBlockTable::compileEntity(self::RU_ODDS_HBLOCK_ID);
					$paramsEntity = $obEntity->getDataClass();

					$ob = $paramsEntity::getList(array(
						"select" => array(
							"UF_NAME",
							"UF_FILE",
							"UF_XML_ID"
						),
						"order" => array(
							"UF_SORT" => "ASC",
						),
					));
					while ($arItem = $ob->fetch()){
						$fileUrl = CFile::GetPath($arItem["UF_FILE"]);

						$arResult[$arItem["UF_XML_ID"]] = array(
							"NAME"      => $arItem["UF_NAME"],
							"XML_ID"    => $arItem["UF_XML_ID"],
							"IMAGE"     => $fileUrl
						);
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arResult" => $arResult));
				}
				*/
			}
            return $arResult;
        }

        /**
         * Get string ends
         * @param integer $count    - item counts
         * @return boolean|string
         */
        public static function getFilesEnds($count){
            if (!$count)
                return false;

            $text   = '';

            if ($count > 1 && $count < 5)
                $text .= "а";
            else if ($count > 4 && $count < 21)
                $text .= "ов";
            else if ($count > 20){
                $c = substr($count, -1, 1);
                if ($c > 1 && $count < 5)
                    $text .= "a";
                else if ($c > 4 || !$c)
                    $text .= "ов";
            }

            return $text;
        }

        /**
         * Get params string for one click order form
         * @global type $USER
         * @param array $arParams   - parameters array
         * @return string
         */
        public static function getParamsString($arParams = []){
            if (!isset($arParams["CACHE_TIME"]))
                $arParams["CACHE_TIME"] = 36000000;

            if (empty($arParams["REQUIRED"]))
                $arParams["REQUIRED"] = ["NAME", "PHONE"];

            if (empty($arParams["BUY_MODE"]))
                $arParams["BUY_MODE"] = "ONE";

            global $USER;
            $arParams["IS_AUTHORIZED"] = $USER->IsAuthorized() ? "Y" : "N";
            $arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);
            $arParams["USE_CAPTCHA"] = $arParams["IS_AUTHORIZED"] != "Y" && $arSetting["FORMS_USE_CAPTCHA"] == "Y" ? "Y" : "N";

            $arParams["PHONE_MASK"] = $arSetting["FORMS_PHONE_MASK"];
            $arParams["VALIDATE_PHONE_MASK"] = $arSetting["FORMS_VALIDATE_PHONE_MASK"];
            $arParams["SHOW_PERSONAL_DATA"] = $arSetting["SHOW_PERSONAL_DATA"];
            $arParams["TEXT_PERSONAL_DATA"] = $arSetting["TEXT_PERSONAL_DATA"];

            $arParams["PROPERTIES"] = array("NAME", "PHONE", "EMAIL", "MESSAGE");

            $arParams["PARAMS_STRING"] = array(
                "REQUIRED" => $arParams["REQUIRED"],
                "VALIDATE_PHONE_MASK" => $arParams["VALIDATE_PHONE_MASK"],
                "IS_AUTHORIZED" => $arParams["IS_AUTHORIZED"]
            );

            return strtr(base64_encode(serialize($arParams["PARAMS_STRING"])), "+/=", "-_,");
        }

        /**
         * Check if product is added to a compare list
         * @param integer $ID   - product id
         * @return boolean
         */
        public static function isAddedToCompare($ID = 0){
            if (!(int)$ID || !isset($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS']))
                return FALSE;

            return isset($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS'][(int)$ID]);
        }

        /**
         * Get cart information
         * @return array
         */
        public static function getCartData(){
            $arResult= [
                "ITEMS" => [],
                "SUMM"  => 0
            ];
            $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), SITE_ID);
            $basketItems = $basket->getBasketItems();
			$order  = Sale\Order::create( SITE_ID , Sale\Fuser::getId());
			$order ->setBasket( $basket );
            foreach ($order->getBasket() AS &$basketItem){
                if ($basketItem->canBuy()){
                    $arResult['ITEMS'][]    = [
                        'ID'    => $basketItem->getId(),
                        'NAME'  => $basketItem->getField('NAME'),
                        'PRICE' => CurrencyFormat($basketItem->getPrice(), "RUB"),
                        'FULL_PRICE' => CurrencyFormat($basketItem->getBasePrice(), "RUB"),
                        'PRICE_TOTAL'   => CurrencyFormat($basketItem->getFinalPrice(), "RUB"),
                        'QUANTITY'  => $basketItem->getQuantity(),
                        'DISCOUNT_INT' => ($basketItem->getBasePrice() - $basketItem->getPrice()),
                        'DISCOUNT' => CurrencyFormat($basketItem->getBasePrice() - $basketItem->getPrice(), "RUB"),
                        'DISCOUNT_TOTAL' => CurrencyFormat(($basketItem->getBasePrice() - $basketItem->getPrice()) * $basketItem->getQuantity(), "RUB"),
                        'FULL_PRICE_TOTAL' => CurrencyFormat($basketItem->getBasePrice() * $basketItem->getQuantity(), "RUB"),
                        'PID'    => $basketItem->getProductId(),
                        'PRICE_NOT_FORMATED' => $basketItem->getPrice(),
                    ];
                    $arResult['SUMM']       += $basketItem->getPrice() * $basketItem->getQuantity();
                    $arResult['QUANTITY']   += $basketItem->getQuantity();
                }
            }
            $arResult['SUMM_FORMATED']  = CurrencyFormat($arResult['SUMM'], "RUB");

            return $arResult;
        }

        /**
         * Clear phone number for link callto value
         * @param string $phone - phone number
         * @return string
         */
        public static function clearPhone($phone){
            if (!$phone)
                return FALSE;

            return str_replace(['(', ')', '-', ' '], '', $phone);
        }

        /**
         * Normolize phone number
         * @param string $phone - phone number
         * @return string
         */
        public static function normolizePhone($phone){
            $phone = self::clearPhone($phone);
            if (!$phone || strlen($phone) != 11)
                return $phone;

            $pref = substr($phone, 0, 1) != 7 ? substr($phone, 0, 1) : '+7';

            return $pref.' ('.substr($phone, 1, 3).') '.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9, 2);
        }

        /**
         * Count total array items
         * @param array $array  - array to count items
         * @return boolean|integer
         */
        public function countItems($array){
            if (empty($array))
                return false;

            $count  = 0;
            foreach($array AS &$arSubArray)
                $count += count($arSubArray);

            return $count;
        }

        /**
         * Get product preview pictures
         * @param array $arImages   - image array
         * @param integer $fileID   - image id
         * @return array
         */
        public static function getPreviewPhotos($arImages = [], $fileID = 0){
            $arPhotos   = [];
            if (!empty($arImages))
                $arPhotos[]   = CFile::ResizeImageGet(
                    $arImages,
                    array('width' => 270, 'height' => 250),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );

            if ($fileID)
                $arPhotos[]   = CFile::ResizeImageGet(
                    CFile::GetFileArray($fileID),
                    array('width' => 270, 'height' => 250),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );

            return $arPhotos;
        }

        /**
         * Get location name by ID
         * @param integer $locationID   - location ID
         * @return boolean|array
         */
        public static function getLocationName($locationID){
            if (!$locationID)
                return FALSE;

            return self::getRegionByLocation($locationID, TRUE);
        }

        /**
         * Get location region by location ID
         * @param integer $locationId   - location ID
         * @return boolean|integer
         */
        private static function getRegionByLocation($locationId, $all = FALSE){
            if (!$locationId || !CModule::IncludeModule("sale"))
                return FALSE;

            $res = CLocation\LocationTable::getList(array(
                'filter' => array(
                    '=ID' => $locationId,
                    '=PARENT.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                    '=PARENT.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                ),
                'select' => array(
                    'P_ID'  => 'PARENT.ID',
                    'NAME_RU'  => 'NAME.NAME'
                )
            ));
            if ($item = $res->fetch())
                return $all ? $item : $item['P_ID'];

            return FALSE;
        }

        /**
         * Check user location data
         * @return array
         */
        public function getContactsData($getAll = FALSE){
            $arResult   = [];

			if(!empty($GLOBALS['PAGE_DATA']['LOCATION']) and is_array($GLOBALS['PAGE_DATA']['LOCATION']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['LOCATION'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'LOCATION_CONTACTS_DATA_'.SITE_ID;
				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arResult"]) && (count($res["arResult"]) > 0))
					   $arResult = $res["arResult"];
				}

				if (empty($arResult)){
					CModule::IncludeModule("iblock");

					$arSelect   = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CODE"];
					$arFilter = [
						"IBLOCK_ID" => CClass::RU_CONTACTS_IBLOCK_ID,
						"ACTIVE"    => "Y",
						"PROPERTY_SITE_ID"  => SITE_ID
					];

					$rsElement = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, ['nPageSize' => 20], $arSelect);
					while($obItem   = $rsElement->GetNextElement()){
						$arItam     = $obItem->GetFields();
						$arProps    = $obItem->GetProperties();
						$arSchedule = [];
						if (!empty($arProps['SCHEDULE']['VALUE']))
							foreach($arProps['SCHEDULE']['VALUE'] AS $key => &$item)
								$arSchedule[($key + 1)]   = [
									'NAME'  => $item,
									'VALUE' => $arProps['SCHEDULE']['DESCRIPTION'][$key]
								];

						//$regionID   = (is_array($arProps['LOCATION']['VALUE']) ? join(',',$arProps['LOCATION']['VALUE']) : $arProps['LOCATION']['VALUE']);

						foreach($arProps['LOCATION']['VALUE'] AS $rID)
							$arResult[$rID] = [
								'ID'        => $arItam['ID'],
								'NAME'      => $arItam['NAME'],
								'LINK'      => $arItam['DETAIL_PAGE_URL'],
								'SCHEDULE'  => $arSchedule,
								'LOCATION_ID'   => $arProps['LOCATION']['VALUE'],
								'TIME_ZONE' => $arProps['TIME_ZONE']['VALUE'],
								'PHONE'     => $arProps['PHONE']['VALUE'],
								'CLASS_ROISTAT' => $arProps['CLASS_ROISTAT']['VALUE'],
								'ADD_PHONE' => $arProps['ADD_PHONE']['VALUE'],
								'CODE'      => $arItam['CODE'],
								'ADRESS'    => is_array($arProps['ADRESS']['VALUE']) ? $arProps['ADRESS']['~VALUE']['TEXT'] : $arProps['ADRESS']['VALUE'],
								'STOCK'     => is_array($arProps['TIME_SKLAD']['VALUE']) ? $arProps['TIME_SKLAD']['~VALUE']['TEXT'] : $arProps['TIME_SKLAD']['VALUE'],
								'MAPS'      => $arProps['MAPS']['VALUE']
							];
					}
					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arResult" => $arResult));
				}
				*/
			}
            
            if ($getAll)
                return $arResult;
            else
            {
                global $locId;
                $loc=129;
                if(intval($GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID"))>0)
                {
                    $loc=intval($GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID"));
                }
                elseif(isset($locId)&&intval($locId)>0)
                {
                    $loc=intval($locId);   
                }
                
                return self::getDataByLocation($arResult, (int)$loc, DEFAULT_GEOLOCATION_ID);
            }
        }

        /**
         * Check user delivery location data
         * @return array
         */
        public function getDeliveryData($getAll = FALSE){
            $arResult   = [];

			if(!empty($GLOBALS['PAGE_DATA']['DELIVERY_DATA']) and is_array($GLOBALS['PAGE_DATA']['DELIVERY_DATA']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['DELIVERY_DATA'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'LOCATION_DELIVERY_DATA_'.SITE_ID;
				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arResult"]) && (count($res["arResult"]) > 0))
					   $arResult = $res["arResult"];
				}

				if (empty($arResult)){
					CModule::IncludeModule("iblock");

					$arSelect   = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CODE"];
					$arFilter = [
						"IBLOCK_ID" => CClass::RU_DELIVERY_IBLOCK_ID,
						"ACTIVE"    => "Y",
						"PROPERTY_SITE_ID"  => SITE_ID
					];

					$rsElement = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, ['nPageSize' => 20], $arSelect);
					while($obItem   = $rsElement->GetNextElement()){
						$arItam     = $obItem->GetFields();
						$arProps    = $obItem->GetProperties();
						$regionID   = (is_array($arProps['LOCATION']['VALUE']) ? join(',',$arProps['LOCATION']['VALUE']) : $arProps['LOCATION']['VALUE']);

						$arResult[$regionID ? $regionID : 'OTHER'] = [
							'ID'        => $arItam['ID'],
							'NAME'      => $arItam['NAME'],
							'LINK'      => $arItam['DETAIL_PAGE_URL'],
							'LOCATION_ID'   => $arProps['LOCATION']['VALUE'],
							'CODE'      => $arItam['CODE']
						];
					}
					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arResult" => $arResult));
				}
				*/
			}

            if ($getAll)
                return $arResult;
            else
                return self::getDataByLocation($arResult, (int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID"), DEFAULT_GEOLOCATION_ID);
        }

        /**
         * Get location data by location ID
         * @param array $arLocations    - array of loactions data
         * @param integer $locationID   - current location ID
         * @param integer $defaultLocationID    - default location ID
         * @return boolean|array
         */
        private static function getDataByLocation($arLocations, $locationID, $defaultLocationID){
            if (empty($arLocations) || !$defaultLocationID)
                return FALSE;

            $arDefaultLocation  = [];

            // -- Select location by location ID ---------------------------- //
            if ($arLocations[$locationID])
                return $arLocations[$locationID];

            // -- Get location by region ID --------------------------------- //
            $regionID   = self::getRegionByLocation($locationID);

            if ($arLocations[$regionID])
                return $arLocations[$regionID];

            // -- Select from a list of location IDs ------------------------ //
            foreach($arLocations AS $key => &$arLocation){
                $arTmp  = [];
                if (preg_match('/\,/msi', $key)){
                    $arTmp = explode(',', $key);
                    if (in_array($locationID, $arTmp) || in_array($regionID, $arTmp))
                        return $arLocation;
                }
                if ( ($key == $defaultLocationID || in_array($defaultLocationID, $arTmp)) ||
                     ($defaultLocationID == 'OTHER' && ($key == DEFAULT_GEOLOCATION_ID || in_array(DEFAULT_GEOLOCATION_ID, $arTmp)))
                )
                    $arDefaultLocation = $arLocation;
            }

            // -- Select default location ----------------------------------- //
            if (!empty($arDefaultLocation))
                return $arDefaultLocation;

            return FALSE;
        }
        /**
         * Redirect to location contacts page
         * @return boolean
         */
        public function redirectToUserLocation($type){
            $arLocation = [];
            switch($type){
            case 'DELIVERY':
                $arLocation    = self::getDeliveryData();
                break;
            case 'CONTACTS':
                $arLocation    = self::getContactsData();
                break;
            }

            if (isset($arLocation['LINK']))
                LocalRedirect($arLocation['LINK'], '301');

            return FALSE;
        }

        /**
         * Get element ID by CODE
         * @param string $type  - data type to serach in
         * @param string $code  - element code
         * @return boolean|integer
         */
        public function getElementIdByCode($type, $code){
            if (!$type || !$code)
                return FALSE;

            $arResult   = [];

            switch($type){
            case 'DELIVERY':
                $arResult = self::getDeliveryData(TRUE);
                break;
            case 'CONTACTS':
                $arResult = self::getContactsData(TRUE);
                break;
            }

            if (!empty($arResult))
                foreach($arResult AS &$arItem)
                    if ($arItem['CODE'] == $code)
                        return $arItem['ID'];

            return FALSE;
        }

        /**
         * Get current location contacts data
         * @return array
         */
        public function getLocationContacts(){
            $arResult   = [];

            $locationID = (int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID")
                    ? (int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID") : DEFAULT_GEOLOCATION_ID;

            if ($this->arContacts[$locationID])
                return $this->arContacts[$locationID];

            $arLocations= self::getContactsData(TRUE);
            $GEOLOCATION_ID = $GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID");
            if ($GEOLOCATION_ID == 817  || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){ // питер
                $GEOLOCATION_ID = 817;
            }
            if ($GEOLOCATION_ID == 2201  || strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){ // питер
                $GEOLOCATION_ID = 2201;
            }
            if ($GEOLOCATION_ID == 2622  || strpos($_SERVER["SERVER_NAME"], 'novosibirsk.')!==false ){ // питер
                $GEOLOCATION_ID = 2622;
            }
            if ($GEOLOCATION_ID == 1095  || strpos($_SERVER["SERVER_NAME"], 'krasnodar.')!==false ){ // питер
                $GEOLOCATION_ID = 1095;
            }
           
            
            $arCurLocation  = self::getDataByLocation($arLocations, (int)$GEOLOCATION_ID, DEFAULT_GEOLOCATION_ID);
            
            $DEFAULT_GEOLOCATION_ID = DEFAULT_GEOLOCATION_ID;
            if(DEFAULT_GEOLOCATION_ID==817){
                $DEFAULT_GEOLOCATION_ID = 768;
            }
 
             $isMSK  = (int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID") != DEFAULT_GEOLOCATION_ID &&
                        empty($arLocations[(int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID")]['SCHEDULE'])
                        && !$arLocations[(int)$GLOBALS['APPLICATION']->get_cookie("GEOLOCATION_ID")]['TIME_ZONE'];

            $arResult['SCHEDULE']   = self::getSchedule($arCurLocation, $arLocations, $isMSK);
            $arResult['PHONE']      = self::getPhone($arCurLocation, $arLocations[$DEFAULT_GEOLOCATION_ID], 'PHONE');
            $arResult['ADD_PHONE']  = self::getPhone($arCurLocation, $arLocations[$DEFAULT_GEOLOCATION_ID], 'ADD_PHONE');

            if ($arCurLocation['ADRESS']){
                $arResult['ADRESS'] = $arCurLocation['ADRESS'];
                $arResult['MAPS']   = $arCurLocation['MAPS'];
                $arResult['STOCK']  = $arCurLocation['STOCK'];
                $arResult['SKLAD']  = $arCurLocation['SKLAD'];
            }else{
                $arResult['ADRESS'] = $arLocations[$DEFAULT_GEOLOCATION_ID]['ADRESS'];
                $arResult['MAPS']   = $arLocations[$DEFAULT_GEOLOCATION_ID]['MAPS'];
                $arResult['STOCK']  = $arLocations[$DEFAULT_GEOLOCATION_ID]['STOCK'];
                $arResult['SKLAD']  = $arLocations[$DEFAULT_GEOLOCATION_ID]['SKLAD'];
            }

			$arResult['CLASS_ROISTAT'] = $arCurLocation['CLASS_ROISTAT'];

            $this->arContacts[$locationID] = $arResult;

            return $arResult;
        }

        /**
         * Get formated schedule for current location
         * @param array $arSchedule - schedule list
         * @param array $arLocation - location data
         * @param boolean $isMSK    - is moscow time zone
         * @return boolean|string
         */
        private function getSchedule($arCurLocation, $arLocations, $isMSK){
            $pref   =   'сегодня ';

            $arSchedule = !empty($arCurLocation['SCHEDULE'][date('N')])
                ? $arCurLocation['SCHEDULE'] : $arLocations[DEFAULT_GEOLOCATION_ID]['SCHEDULE'];

            $arTmp = explode('-', $arSchedule[date('N')]['VALUE']);
            foreach($arTmp AS $k => &$time)
                $arTmp[$k] = date('Hi', (
                    strtotime(date('d.m.Y '.$time.':00'))
                    + (empty($arCurLocation['SCHEDULE'][date('N')]) ? (int)$arCurLocation['TIME_ZONE'] * 3600 : '')
                    )
                );

            return $pref.self::getScheduleFormated($arSchedule, $arCurLocation, $isMSK);
        }

        /**
         * Get formated schedule for current location
         * @param array $arSchedule - schedule list
         * @param array $arLocation - location data
         * @param boolean $isMSK    - is moscow time zone
         * @return boolean|string
         */
        private function getScheduleFormated($arSchedule, $arLocation, $isMSK){
            if (empty($arSchedule) || empty($arLocation))
                return FALSE;

            $arTmp = explode('-', $arSchedule[date('N')]['VALUE']);

            foreach($arTmp AS $k => &$time)
                $arTmp[$k] = date('H:i', (
                    strtotime(date('d.m.Y '.$time.':00'))
                    + (empty($arLocation['SCHEDULE'][date('N')]) ? (int)$arLocation['TIME_ZONE'] * 3600 : '')
                    )
                );

            return 'c '.join(' до ', $arTmp).($isMSK ? ' по МСК' : '');
        }

        /**
         * Get current location phone number
         * @param array $arCurLocation  - current location data
         * @param array $arLocation     - default location data
         * @return boolean|array
         */
        private function getPhone($arCurLocation, $arLocation, $code = 'PHONE'){
            if (empty($arCurLocation) || empty($arLocation))
                return [];

            $phone  = $arCurLocation[$code] ? $arCurLocation[$code] : $arLocation[$code];

            return [
                'VALUE' => $phone,
                'NUMBER'=> self::clearPhone($phone)
            ];
        }

        /**
         * Get top menu liks list
         * @return boolean|array
         */
        public static function getTopMenuLinks(){
            if (!defined('MENU_SHOW_LINKS'))
                return FALSE;

            return explode(',', MENU_SHOW_LINKS);
        }

        /**
         * Get delivery service name
         * @param integer $id   - delivery service ID
         * @param integer $name - native delivery service name
         * @param integer $locationID   - location ID
         * @param integer $parent_id    - delivery service section ID
         * @return boolean|string
         */
        public static function getDeliveryServiceName($id, $name, $locationID, $parent_id){
            if (!$id)
                return FALSE;

            Loc::loadLanguageFile($_SERVER['DOCUMENT_ROOT'].self::PATH_TO_DELIVERY_LANG_FILE);

            $d_name = $name;
            // -- Title for self pickup delivery service ---------------- //
            if (Loc::getMessage("SOA_SELF_PICKUP_CITY_".$locationID) && $id == self::DELIVERY_SELF_PICKUP_ID)
                $d_name .= Loc::getMessage("SOA_SELF_PICKUP_CITY_".$locationID);

            // -- Title for self delivery service ----------------------- //
            if ($parent_id == self::DELIVERY_SELF_SECTION_ID){
                if (Loc::getMessage("SOA_SELF_CITY_".$locationID))
                    $d_name = Loc::getMessage('SOA_DELIVERY_PREF').Loc::getMessage("SOA_SELF_CITY_".$locationID);
                elseif (\Bitrix\Sale\Delivery\DeliveryLocationTable::checkConnectionExists($id, $locationID, ['LOCATION_LINK_TYPE' => 'ID'])){
                    $arCity = self::getLocationName($locationID);
                    $d_name = Loc::getMessage('SOA_DELIVERY_PREF_IN').$arCity['NAME_RU'];
                }
            }

            return $d_name;
        }

        /**
         * Get one click email for current site
         * @return string
         */
        public static function getOneClickEmail(){
            return Option::get('webservice.wscrm', 'oneclick_email_'.SITE_ID, self::ONE_CLICK_EMAIL);
        }

        public static function curl($url, $port = 80){
            $error  = FALSE;
            $info   = "";

            if ($tuCurl = curl_init()){
                $userAgent  = "Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots mtmon01g.yandex.ru)";

                $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;";
                $header[0] .= "q=0.9,image/webp,*/*;q=0.8";
                $header[] = "Cache-Control: max-age=0";
                $header[] = "Connection: keep-alive";
                $header[] = "Keep-Alive: 300";
                $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4";

                curl_setopt($tuCurl, CURLOPT_PORT, $port);
                if ($port == 443)
                    curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($tuCurl, CURLOPT_ENCODING, 'gzip, deflate, sdch');
                curl_setopt($tuCurl, CURLOPT_URL, $url);
                curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
                curl_setopt($tuCurl, CURLOPT_HEADER, 0);
                curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($tuCurl, CURLOPT_USERAGENT, $userAgent);
                /*$strCookie = 'SessionID=' . $_COOKIE['PHPSESSID'] . '; path=/';
                curl_setopt($tuCurl, CURLOPT_COOKIE, $strCookie);*/

                $tuData = curl_exec($tuCurl);

                if (!curl_errno($tuCurl))
                    $info = curl_getinfo($tuCurl);
                else
                    $error = 'Curl error: ' . curl_error($tuCurl);
                curl_close($tuCurl);

                return [
                    "error" => ($error ? TRUE : FALSE),
                    "data"  => json_decode($tuData),
                    "info"  => $info
                ];
            }

            return [
                "error" => TRUE,
                "msg"   => "CURL not installed"
            ];
        }

        public static function uniqueMultidimArray($array, $key) {
            $temp_array = array();
            $key_array = array();

            foreach($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[]    = $val[$key];
                    $temp_array[]   = $val;
                }
            }
            return $temp_array;
        }

        /**
         * Multibite ucfirst function
         * @param string $text  - input text
         * @return boolean|string
         */
        public function mb_ucfirst($text) {
            if (!$text)
                return FALSE;

            return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
        }

        /**
         * Correct string cutting
         *
         * @param string $string Target string
         * @param int    $maxlen Maximal length
         *
         * @return string
         */
        public static function cutString($string, $maxlen) {
            $len    = (mb_strlen($string) > $maxlen) ? mb_strripos(mb_substr($string, 0, $maxlen), ' ') : $maxlen;
            $cutStr = mb_substr($string, 0, $len);

            return (mb_strlen($string) > $maxlen) ? $cutStr . '...' : $cutStr;
        }

        /**
         * Handle json object
         * @param array/string $json
         * @param integer $encode
         */
        public static function json($json, $encode = 0){
            if ($encode)
                if (!is_array($json))
                    echo json_encode(array('data' => iconv('windows-1251','UTF-8', $json)));
                else
                    echo json_encode(array(iconv('windows-1251','UTF-8', $json)));
            else
                if(!is_array($json))
                    echo json_encode(array('data' => $json));
                else
                    echo json_encode($json);
        }

        /**
         * Generate JSON object responce
         * @param string $text - text to cnvert
         * @return json - json object
         */
        public static function MakeJSON($text, $debug = ''){
            $data = array(
                'status'    => '2',
                'msg'       => 'Внутреняя ошибка сервера!'
            );
            $data['debug']  = $debug;
            if (strlen($text) > 5){
                $data['status'] = 0;
                $data['msg']    = $text;
            }
            echo self::json($data);
        }

        /**
		 * Sends object as JSON-response
		 */
		public function RenderJSON($data) {
			header('Cache-Control: no-cache, must-revalidate');
			header('Content-type: text/plain; charset=utf-8');

			$this->app->RestartBuffer();
			die(json_encode($data));
		}

        public static function clearHTML($string = ""){
            if (!$string)
                return FALSE;

            $string = preg_replace('/\s{2,3}/msi', "", $string);
            $string = preg_replace('/\n{2,3}/msi', "", $string);

            return trim($string);
        }

        public function IsMobile() {
            $user_agent=strtolower(getenv('HTTP_USER_AGENT'));
            $accept=strtolower(getenv('HTTP_ACCEPT'));

            if ((strpos($accept,'text/vnd.wap.wml')!==false) ||
                (strpos($accept,'application/vnd.wap.xhtml+xml')!==false)) {
                return 1; // Мобильный браузер обнаружен по HTTP-заголовкам
            }

            if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
                    return 2; // Мобильный браузер обнаружен по установкам сервера
            }

            if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|'.
                'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|'.
                'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|'.
                'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|'.
                'm881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|'.
                'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|'.
                'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|'.
                'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|'.
                'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|'.
                'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|'.
                '_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|'.
                's800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|'.
                'd736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |'.
                'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|'.
                'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|'.
                'pocket|kindle|mobile|psp|treo)/', $user_agent)) {
                return 3; // Мобильный браузер обнаружен по сигнатуре User Agent
            }

            if (in_array(substr($user_agent,0,4),
        	    Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
                    "6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
                    "aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
                    "aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
                    "aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
                    "brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
                    "cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
                    "dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
                    "el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
                    "ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
                    "g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
                    "hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
                    "hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
                    "htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
                    "iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
                    "iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
                    "kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
                    "lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
                    "lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
                    "lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
                    "m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
                    "meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
                    "mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
                    "mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
                    "n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
                    "neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
                    "o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
                    "pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
                    "phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
                    "psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
                    "qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
                    "rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
                    "sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
                    "sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
                    "sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
                    "sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
                    "t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
                    "tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
                    "up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
                    "vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
                    "vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
                    "w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
                    "waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
                    "winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
                    "zeto", "zte-"))) {
                return 4; // Мобильный браузер обнаружен по сигнатуре User Agent
            }

            return false; // Мобильный браузер не обнаружен
        }

        public static function Dump($array, $type = 0){
            if (!$type)
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/dump.txt', print_r($array, true));
            else{
                $f = fopen($_SERVER['DOCUMENT_ROOT'].'/dump_file.txt', 'a+');
                fwrite($f, date('d.m.Y H:i:s').print_r($array, true));
                fclose($f);
            }
        }

		public function getNameCountry(){

			$arResult = array();

			if(!empty($GLOBALS['PAGE_DATA']['NAME_COUNTRY']) and is_array($GLOBALS['PAGE_DATA']['NAME_COUNTRY']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['NAME_COUNTRY'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'NAME_COUNTRY';

				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arCountry"]) && (count($res["arCountry"]) > 0)){
						$arResult = $res["arCountry"];
					}
				}

				if (empty($arResult)){
					Loader::includeModule("highloadblock");

					$arHLBlock = HL\HighloadBlockTable::getById(4)->fetch();
					$obEntity = HL\HighloadBlockTable::compileEntity($arHLBlock);
					$paramsEntity = $obEntity->getDataClass();

					$ob = $paramsEntity::getList(array(
						"select" => array(
							"UF_NAME",
							"UF_XML_ID"
						),
						"filter" => array(

						),
						"order" => array(
							"UF_SORT" => "ASC",
						),
					));
					while ($item = $ob->fetch())
					{
						$name = $item["UF_NAME"];
						$xml_id = $item["UF_XML_ID"];

						$arResult[$xml_id] = $name;
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arCountry" => $arResult));
				}
				*/
			}

			return $arResult;
		}

		public function getNameInterior(){

			$arResult = array();

			if(!empty($GLOBALS['PAGE_DATA']['NAME_INTERIOR']) and is_array($GLOBALS['PAGE_DATA']['NAME_INTERIOR']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['NAME_INTERIOR'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'NAME_INTERIOR';

				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arInterior"]) && (count($res["arInterior"]) > 0)){
						$arResult = $res["arInterior"];
					}
				}

				if (empty($arResult)){
					Loader::includeModule("highloadblock");

					$arHLBlock = HL\HighloadBlockTable::getById(6)->fetch();
					$obEntity = HL\HighloadBlockTable::compileEntity($arHLBlock);
					$paramsEntity = $obEntity->getDataClass();

					$ob = $paramsEntity::getList(array(
						"select" => array(
							"UF_NAME",
							"UF_XML_ID"
						),
						"filter" => array(

						),
						"order" => array(
							"UF_SORT" => "ASC",
						),
					));
					while ($item = $ob->fetch())
					{
						$name = $item["UF_NAME"];
						$xml_id = $item["UF_XML_ID"];

						$arResult[$xml_id] = $name;
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arInterior" => $arResult));
				}
				*/
			}

			return $arResult;
		}

		public function getInfoBrands(){

			$arResult = array();

			if(!empty($GLOBALS['PAGE_DATA']['INFO_BRAND']) and is_array($GLOBALS['PAGE_DATA']['INFO_BRAND']))
			{
				$arResult = $GLOBALS['PAGE_DATA']['INFO_BRAND'];
			}
			else
			{
				/*
				$cache = new CPHPCache();
				$cache_id = 'INFO_BRAND';

				if ($cache->InitCache(self::CACHE_TIME, $cache_id, "/")){
					$res = $cache->GetVars();
					if (is_array($res["arBrands"]) && (count($res["arBrands"]) > 0)){
						$arResult = $res["arBrands"];
					}
				}

				if (empty($arResult)){

					Loader::includeModule("catalog");

					$ob = \CIBlockElement::GetList(
						array(),
						array(
							'IBLOCK_ID' => BRANDS_IBLOCK_ID,
							'ACTIVE' => 'Y'
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
						$arResult[$row['ID']]['NAME'] = $row['NAME'];
						$arResult[$row['ID']]['CODE'] = $row['CODE'];
					}

					$cache->StartDataCache(self::CACHE_TIME, $cache_id, "/");
					$cache->EndDataCache(array("arBrands" => $arResult));
				}
				*/
			}

			return $arResult;
		}

		public function getNormalNameProp($name){

			$matches = null;
			$returnResult = preg_match('/\[(.*)\]/', $name, $matches);
			if (!is_null($matches)) {
				$name = str_replace($matches[0], '', $name);
			}

			$matches = null;
			$returnResult = preg_match('/\{(.*)\}/', $name, $matches);
			if (!is_null($matches)) {
				$name = str_replace($matches[0], '', $name);
			}

			return $name;
		}

		public function getNormalValueProp($arValue)
		{
			$value = $arValue['VALUE'];
			//pr($arValue);

			//Не выводим значение если у свойства заполненно 2 и более значений
			if(($arValue['ID'] == 3934 && count($arValue['VALUE']) > 1) || ($arValue['ID'] == 3943 && count($arValue['VALUE']) > 1)){
				return false;
			}

			if($arValue['USER_TYPE'] == 'SASDCheckbox')
			{
				if($arValue['VALUE'] == $arValue['DEFAULT_VALUE'])
				{
					return false;
				}
				$value = $arValue['USER_TYPE_SETTINGS']['VIEW'][$value];
			}
			elseif(!empty($arValue['USER_TYPE_SETTINGS']['TABLE_NAME']) and !empty($GLOBALS['PAGE_DATA']['HL_TABLE'][$arValue['USER_TYPE_SETTINGS']['TABLE_NAME']]))
			{
				if(is_array($arValue['VALUE']))
				{
					$value = [];
					foreach($arValue['VALUE'] as $val)
					{
						if(!empty($GLOBALS['PAGE_DATA']['HL'][$GLOBALS['PAGE_DATA']['HL_TABLE'][$arValue['USER_TYPE_SETTINGS']['TABLE_NAME']]][$val]['UF_NAME']))
						{
							$value[] = $GLOBALS['PAGE_DATA']['HL'][$GLOBALS['PAGE_DATA']['HL_TABLE'][$arValue['USER_TYPE_SETTINGS']['TABLE_NAME']]][$val]['UF_NAME'];
						}
					}
				}
				else
				{
					if(!empty($GLOBALS['PAGE_DATA']['HL'][$GLOBALS['PAGE_DATA']['HL_TABLE'][$arValue['USER_TYPE_SETTINGS']['TABLE_NAME']]][$arValue['VALUE']]['UF_NAME']))
					{
						$value = $GLOBALS['PAGE_DATA']['HL'][$GLOBALS['PAGE_DATA']['HL_TABLE'][$arValue['USER_TYPE_SETTINGS']['TABLE_NAME']]][$arValue['VALUE']]['UF_NAME'];
					}
				}
			}
			elseif(!empty($arValue['LINK_IBLOCK_ID']) and $arValue['LINK_IBLOCK_ID'] == BRANDS_IBLOCK_ID)
			{
				if(is_array($arValue['VALUE']))
				{
					$value = [];
					foreach($arValue['VALUE'] as $val)
					{
						if(!empty($GLOBALS['PAGE_DATA']['INFO_BRAND'][$val]))
						{
							$value[] = '<a href="/vendors/'.$GLOBALS['PAGE_DATA']['INFO_BRAND'][$val]['CODE'].'/">'.$GLOBALS['PAGE_DATA']['INFO_BRAND'][$val]['NAME'].'</a>';
						}
					}
				}
				else
				{
					if(!empty($GLOBALS['PAGE_DATA']['INFO_BRAND'][$arValue['VALUE']]))
					{
						$value = '<a href="/vendors/'.$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arValue['VALUE']]['CODE'].'/">'.$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arValue['VALUE']]['NAME'].'</a>';
					}
				}
			}
			elseif(!empty($arValue['LINK_IBLOCK_ID']) and $arValue['LINK_IBLOCK_ID'] == SERIES_IBLOCK_ID)
			{
				if(is_array($arValue['VALUE']))
				{
					$value = [];
					foreach($arValue['VALUE'] as $val)
					{
						if(!empty($GLOBALS['PAGE_DATA']['INFO_SERIES'][$val]))
						{
							$value[] = '<a href="/'.(SITE_ID == 's0' ? 'ser' : 'series').'/'.$GLOBALS['PAGE_DATA']['INFO_SERIES'][$val]['CODE'].'/">'.$GLOBALS['PAGE_DATA']['INFO_SERIES'][$val]['NAME'].'</a>';
						}
					}
				}
				else
				{
					if(!empty($GLOBALS['PAGE_DATA']['INFO_SERIES'][$arValue['VALUE']]))
					{
						$value = '<a href="/'.(SITE_ID == 's0' ? 'ser' : 'series').'/'.$GLOBALS['PAGE_DATA']['INFO_SERIES'][$arValue['VALUE']]['CODE'].'/">'.$GLOBALS['PAGE_DATA']['INFO_SERIES'][$arValue['VALUE']]['NAME'].'</a>';
					}
				}
			}

			if(is_array($value))
			{
				if($arValue['PROPERTY_TYPE'] == 'N')
				{
					$value = array_map('customGetIntVal', $value);
				}
				$value = implode(', ', $value);
			}
			else
			{
				if($arValue['PROPERTY_TYPE'] == 'N')
				{
					$value = customGetIntVal($value);
				}
			}
			return html_entity_decode($value);
		}

		public function getCurrentPriceCode(&$geolocationId = 0)
		{
			static $value, $cookie;

			if(!$value)
			{
				$value = 'BASE';

				$request = Application::getInstance()->getContext()->getRequest();
				$cookie = $geolocationId = $request->getCookie("GEOLOCATION_ID");

				switch($geolocationId)
				{
					case 817:
						\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
						$value = 'SPB';
						break;
					case 2201:
						\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
						$value = 'EKB';
						break;
                    case 1095:
                        \Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
                        $value = 'KRAS';
                        break;                        
				}
			}
			else
			{
				$geolocationId = $cookie;
			}

			return [$value];
		}

		public function getCurrentAvalCode(&$geolocationId = 0){
			static $value, $cookie;

			if(!$value)
			{
				$value = '';

				$request = Application::getInstance()->getContext()->getRequest();
				$cookie = $geolocationId = $request->getCookie("GEOLOCATION_ID");

				switch($geolocationId)
				{
					case 817:
						\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
						$value = '_SPB';
						break;
					case 2201:
						\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
						$value = '_EKB';
						break;
                    case 1095:
                        \Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
                        $value = '_KRAS';
                        break;
				}
			}
			else
			{
				$geolocationId = $cookie;
			}

			return $value;
		}

		public function setGeoSubDomain() {
            $request = Application::getInstance()->getContext()->getRequest();
            $geolocationId = $request->getCookie("GEOLOCATION_ID") ? $request->getCookie("GEOLOCATION_ID") : DEFAULT_GEOLOCATION_ID;
            $isBot = !!preg_match("~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", $_SERVER['HTTP_USER_AGENT']);
            
           
            /*if($_SERVER["HTTP_HOST"]=='ekb.geberit-shop.ru'){
                $HTTP_IS_SUB_HEADER = 'ekb';
            }else*/
            if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'){
                $HTTP_IS_SUB_HEADER = 'spb';
            }
            if($_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru'){
                $HTTP_IS_SUB_HEADER = 'ekb';
            }
            if($_SERVER["SERVER_NAME"]=='novosibirsk.geberit-shop.ru'){
                $HTTP_IS_SUB_HEADER = 'novosibirsk';
            }
            if($_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru'){
                $HTTP_IS_SUB_HEADER = 'krasnodar';
            }
            
            //Если бот ничего не делаем
            if($isBot && !empty($HTTP_IS_SUB_HEADER)){
                return false;
            }
            
            if($isBot && empty($HTTP_IS_SUB_HEADER)){
                $geolocationId = $request->getCookie("GEOLOCATION_ID") ? $request->getCookie("GEOLOCATION_ID") : DEFAULT_GEOLOCATION_ID;
            } 
			
            
            //Определение города
            if(!empty($geolocationId)){

                $subDomain = 'non';
                switch($geolocationId)
                {
                    case 129:
                        $subDomain = 'non';
                        break;
                    case 817:
                        $subDomain = 'spb';
                        break;
                    case 2201:
                        $subDomain = 'ekb';
                        break;
                    case 2622:
                        $subDomain = 'novosibirsk';
                        break;
                     case 1095:
                        $subDomain = 'krasnodar';
                        break;
                }
            }
            else{
                return false;
            }

 
            $request = Application::getInstance()->getContext()->getRequest();
            $protocol = $request->isHttps() ? 'https://' : 'http://';
            $requestUri = $request->getRequestUri();
           
            //Соотвествие города поддомену
            $redirect = false;

            //Если на поддомене
            if(!empty($HTTP_IS_SUB_HEADER)){

                //Если на не правильном
                if($_GET['test'] == 'test'){
//                    var_dump($_SERVER["HTTP_IS_SUB_HEADER"]);
//                    var_dump($subDomain);
                }
                if($subDomain != 'non' && $HTTP_IS_SUB_HEADER != $subDomain){
                    $redirect = true;
                }

                //Если не нужен поддмене
                if($subDomain == 'non'){
                    $redirect = true;
                }

            }
            //Если не на поддомене
            else{
                //Если должны быть быть на поддомене
                if($subDomain != 'non'){
                    $redirect = true;
                }
            }
			
			if($_GET['test'] == 't'){
				var_dump([$geolocationId,$_SERVER["SERVER_NAME"]]);
				die;
			}
            //Редирект
            /*global $USER;
            if ($USER->IsAdmin()){*/
            if($geolocationId==817&&strpos($_SERVER["SERVER_NAME"], 'spb.')===false){
                    LocalRedirect($protocol.'spb.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
            }elseif($geolocationId==2201&&strpos($_SERVER["SERVER_NAME"], 'ekb.')===false){
                    LocalRedirect($protocol.'ekb.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
            }elseif($geolocationId==2622&&strpos($_SERVER["SERVER_NAME"], 'novosibirsk.')===false){
                    LocalRedirect($protocol.'novosibirsk.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
            }elseif($geolocationId==1095&&strpos($_SERVER["SERVER_NAME"], 'krasnodar.')===false){
                    LocalRedirect($protocol.'krasnodar.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
            
            } elseif ($_SERVER["SERVER_NAME"] != 'geberit-shop.ru' && $geolocationId == 129) {
                if ($_SERVER["SERVER_NAME"] !== 'test.geberit-shop.ru') {
                    LocalRedirect($protocol . SITE_SERVER_NAME . $requestUri, true, "301 Moved permanently");
                }
            }

                
            /*if(empty($HTTP_IS_SUB_HEADER)){
                if($geolocationId==817&&empty($HTTP_IS_SUB_HEADER)&&$subDomain='spb'){
                    LocalRedirect($protocol.'spb.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
                if($geolocationId==2201&&empty($HTTP_IS_SUB_HEADER)&&$subDomain='ekb'){
                    LocalRedirect($protocol.'ekb.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
                if($geolocationId==2622&&empty($HTTP_IS_SUB_HEADER)&&$subDomain='novosibirsk'){
                    LocalRedirect($protocol.'novosibirsk.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
                if($geolocationId==1095&&empty($HTTP_IS_SUB_HEADER)&&$subDomain='krasnodar'){
                    LocalRedirect($protocol.'krasnodar.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
            }elseif($subDomain=='non'&&$geolocationId!=817&&$geolocationId!=2201&&$geolocationId!=2622&&$geolocationId!=1095){
                LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
            }*/
                /*{
                if($_SERVER["SERVER_NAME"]='spb.geberit-shop.ru'&&$geolocationId!=817){
                 // LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
            }*/
            // }
            /*if($_SERVER["SERVER_NAME"]=='geberit-shop.ru'&&$geolocationId==817){
                echo "<pre>";
                var_dump($protocol.'spb.'.SITE_SERVER_NAME.$requestUri);
                echo "</pre>";
            }*/
            /*if(!empty($HTTP_IS_SUB_HEADER)){
                if($_SERVER["SERVER_NAME"]=='geberit-shop.ru'&&$geolocationId==817){
                    //LocalRedirect($protocol.$subDomain.'.'.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
                if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'&&$geolocationId!=817){
                    //LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
            }else{
                if($_SERVER["SERVER_NAME"]!='spb.geberit-shop.ru'&&$geolocationId!=817){
                  //  LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
            }*/
       
/*if($redirect){


            
                //Если на поддомен
                if($subDomain != 'spb'){
                   // LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }
                //Если на основной домен
                else{
                  //  LocalRedirect($protocol.SITE_SERVER_NAME.$requestUri, true, "301 Moved permanently");
                }

            }*/
                

        }

		public function getNameSubdomain(){

			$arSubDomains = array('msk' => 'в Москве', 'spb' => 'в Санкт-Петербурге', 'ekb' => 'в Екатеринбурге', 'novosibirsk' => 'в Новосибирске', 'krasnodar' => 'в Краснодаре');

			$arResult = "";
			if(!empty($_SERVER["HTTP_IS_SUB_HEADER"]) && !empty($arSubDomains[$_SERVER["HTTP_IS_SUB_HEADER"]])){
				$arResult = $arSubDomains[$_SERVER["HTTP_IS_SUB_HEADER"]];
			}

			return $arResult;
		}
        /**
         * Get sections path
         * @param integer $sectionID    - child section id
         * @param array $arSections     - sections array
         * @return boolean|array
         */
        public function getSectionPath($sectionID, $arSections){
            if (empty($arSections) || !$sectionID)
                return FALSE;
            if (!$arSections[$sectionID]['IBLOCK_SECTION_ID'])
                return [$arSections[$sectionID]];

            return array_merge(array($arSections[$sectionID]), self::getSectionPath($arSections[$sectionID]['IBLOCK_SECTION_ID'], $arSections));
        }

    }

	function customGetIntVal($str)
	{
		if(($number = $str + 0) !== 0)
		{
			$str = $number;
		}

		return $str;
	}


	class BitrixNotFoundException extends \RuntimeException {
		public function __construct() {
			$this->message = 'Can not find Bitrix core';
		}
	}
?>
