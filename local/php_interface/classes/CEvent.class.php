<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

	/**
	 * Class CEventHandlers
	 *
	 * Bitrix events handlers 
	 *
	 */
	 
	use Bitrix\Highloadblock as HL;
	 
	class CEventHandler {
		/**
		 * Dynamic 404 handling
		 */
		public static function Redirect404() {
            global $APPLICATION;

            if (!defined('ADMIN_SECTION') && defined("ERROR_404")) {

				/** @var $APPLICATION \CMain */
                //$APPLICATION->RestartBuffer();
				require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

				if (file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR.'404/index.php') && $APPLICATION->GetCurDir() !== SITE_DIR.'404/')
                    Localredirect(SITE_DIR.'404/', "404 Not Found");
                else
					include_once $_SERVER['DOCUMENT_ROOT'] . SITE_DIR.'404/index.php';

				require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
			}
		}

        public static function OnBeforeIBlockElementAdd(&$arFields){
            global $APPLICATION;
            return TRUE;

            $hasException   = FALSE;

            if (!filter_var($arFields["CODE"], FILTER_VALIDATE_EMAIL) && in_array($arFields['IBLOCK_ID'], [CClass::FEEDBACK_FORM_IBLOCK_ID])){
                $APPLICATION->ThrowException("'E-mail'");
                $hasException= TRUE;
            }
            /*if (!isset($_POST['agreement']) && in_array($arFields['IBLOCK_ID'], [
                    CClass::FEEDBACK_FORM_IBLOCK_ID,
                    CClass::ORDER_FORM_IBLOCK_ID
                ])){
                $APPLICATION->ThrowException("FALSE_AGREEMENT");
                $hasException= TRUE;
            }*/
            if ( (!isset($_POST['PROPERTY']['BX'][0]) || $_POST['PROPERTY']['BX'][0]) && in_array($arFields['IBLOCK_ID'], [
                    CClass::FEEDBACK_FORM_IBLOCK_ID
                ])){
                $APPLICATION->ThrowException("BOT!");
                $hasException= TRUE;
            }

            if ($arFields["IBLOCK_ID"] == CClass::FEEDBACK_FORM_IBLOCK_ID)
                $arFields["PROPERTY_VALUES"][3514] = $_SERVER['REMOTE_ADDR'];

            if ($hasException)
                return FALSE;

            return TRUE;
        }

        public static function OnAfterIBlockElementAdd(&$arFields){
            $arMail     = [];
            $eventName  = "";

            switch($arFields['IBLOCK_ID']){
            case CClass::FEEDBACK_FORM_IBLOCK_ID :
                $arMail = [
                    'NAME'      => $arFields['NAME'],
                    'PHONE'     => $arFields['PROPERTY_VALUES'][3513],
                    'EMAIL'     => $arFields['CODE'],
                    'TEXT'      => $arFields['PREVIEW_TEXT']
                ];
                $eventName  = "FEEDBACK_FORM";
                break;

            /*case CClass::RU_CATALOG_IBLOCK_ID:
                self::UpdateSetsProductPrice($arFields['ID']);
                break;*/
            }

            if ($eventName && !empty($arMail))
                CEvent::Send($eventName, SITE_ID, $arMail, "Y");
        }
		
		//ИЗМЕНЕНИЕ ТОВАРА Что было до обновления
		public static function OnBeforeIBlockElementUpdate(&$arFields){
			
			$acces = true;
			$arCatalogIds = array(50 => 'l1', 54 => 's0', 15 => 's1', 60 => 's6');
			if(empty($arCatalogIds[$arFields['IBLOCK_ID']])){
				$acces = false;
			}
			
			if($acces){
				$res = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => $arFields['IBLOCK_ID'], "ID" => $arFields['ID']), false, Array(), Array('ID', 'IBLOCK_ID', 'TIMESTAMP_X_UNIX', 'MODIFIED_BY', 'NAME', 'ACTIVE', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PROPERTY_*'));
				if($arItem = $res->GetNext()) { 
					
					$arBefore = array();
					$arBefore = $arItem;
					
					define("arBefore", $arBefore);
				}		
			}	
        }
		
		//ИЗМЕНЕНИЕ ТОВАРА Что стало после обновления
        public static function OnAfterIBlockElementUpdate(&$arFields){
			
			$acces = true;
			$arCatalogIds = array(50 => 'l1', 54 => 's0', 15 => 's1', 60 => 's6');
			if(empty($arCatalogIds[$arFields['IBLOCK_ID']])){
				$acces = false;
			}

			$arPageData = array();
			
			$SITE_ID = $arCatalogIds[$arFields['IBLOCK_ID']];
			
			$file = __DIR__.'/../../cache/'.$SITE_ID.'-cache_page.php';
			if(file_exists($file)){
				$data = file_get_contents($file);
				list($php, $json) = explode("\n", $data, 2);
				$arPageData = json_decode($json, true);
			}
			else{
				$acces = false;
			}
			
			if($acces){
				$res = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => $arFields['IBLOCK_ID'], "ID" => $arFields['ID']), false, Array(), Array('ID', 'IBLOCK_ID', 'TIMESTAMP_X_UNIX', 'MODIFIED_BY', 'NAME', 'ACTIVE', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PROPERTY_*'));
				if($arItem = $res->GetNext()) { 

					$arAfter = array();
					$arAfter = $arItem;
					
					define("arAfter", $arAfter);
				}
				
				//Узнаем что изменили
				$arResult = array();
				
				//В елементе
				if(arBefore['NAME'] != arAfter['NAME']){
					$arResult['ELEMENT']['NAME'] = arAfter['NAME'];
				}
				if(arBefore['ACTIVE'] != arAfter['ACTIVE']){
					$arResult['ELEMENT']['ACTIVE'] = arAfter['ACTIVE'];
				}
				if(arBefore['PREVIEW_TEXT'] != arAfter['PREVIEW_TEXT']){
					$arResult['ELEMENT']['PREVIEW_TEXT'] = arAfter['PREVIEW_TEXT'];
				}
				if(arBefore['DETAIL_TEXT'] != arAfter['DETAIL_TEXT']){
					$arResult['ELEMENT']['DETAIL_TEXT'] = arAfter['DETAIL_TEXT'];
				}

				//В свойствах
				$arChangeProp = array();
				foreach(arBefore as $key => $arItem){
					if(preg_match("/^PROPERTY_(\d+)/i", $key, $matches)){
						if(arBefore[$key] != arAfter[$key]){
							$arChangeProp[$matches[1]] = arAfter[$key];
						}
					}
				}

				//Информация об измененых свойствах
				$arProp = array();			
				foreach($arChangeProp as $propId => $value){
					
					if(count($arPageData['LIST_PROP'][$propId]) > 0){
						
						$nameProp = $arPageData['LIST_PROP'][$propId]['NAME'];
						$codeProp = $arPageData['LIST_PROP'][$propId]['CODE'];
						$typeProp = $arPageData['LIST_PROP'][$propId]['PROPERTY_TYPE'];
						
						if(count($arPageData['LIST_PROP'][$propId]['USER_TYPE_SETTINGS']) > 0){
							$tableNameHL = $arPageData['LIST_PROP'][$propId]['USER_TYPE_SETTINGS']['TABLE_NAME'];
						}
						
						//Бренд
						if($typeProp == 'E' && $codeProp == 'MANUFACTURER'){
							$arProp[$nameProp] = $arPageData['INFO_BRAND'][$value]['NAME'];
						}
						//Коллекция
						elseif($typeProp == 'E' && $codeProp == 'SERIES'){
							$arProp[$nameProp] = $arPageData['INFO_SERIES'][$value]['NAME'];
						}
						//Список
						elseif($typeProp == 'L'){
							
							if(is_array($value)){
								foreach($value as $valueId => $valueItem){
									$arProp[$nameProp][] = $arPageData['LIST_PROP'][$propId]['LIST_VALUES_CUSTOM'][$valueId]['VALUE'];
								}
							}
							else{
								$arProp[$nameProp] = $arPageData['LIST_PROP'][$propId]['LIST_VALUES_CUSTOM'][$value]['VALUE'];
							}
							
						}
						//HL
						elseif($typeProp == 'S' && !empty($tableNameHL)){
							if(is_array($value)){
								foreach($value as $valueId => $valueItem){
									$arProp[$nameProp][] = $arPageData['HL'][$arPageData['HL_TABLE'][$tableNameHL]][$valueItem]['UF_NAME'];
								}
							}
							else{
								$arProp[$nameProp] = $arPageData['HL'][$arPageData['HL_TABLE'][$tableNameHL]][$value]['UF_NAME'];
							}
						}
						//Все остальное
						else{
							$arProp[$nameProp] = $value;
						}
					}
					
				}

				$arResult['PROPERTIES'] = $arProp;

				$rsUser = CUser::GetByID(arAfter['MODIFIED_BY']);
				$arUser = $rsUser->Fetch();
				
				//Сохраняем в базу данных
				if(count($arResult['ELEMENT']) > 0 || count($arResult['PROPERTIES']) > 0){
					
					global $DB;
					
					$query = "INSERT INTO `a_logs_update_product` 
								(`id`, `iblock_id`, `product_id`, `user_id`, `user_mail`, `time`, `json`) 
							VALUES 
								(NULL, '".arAfter['IBLOCK_ID']."', '".arAfter['ID']."', '".arAfter['MODIFIED_BY']."', '".$arUser['EMAIL']."', '".arAfter['TIMESTAMP_X_UNIX']."', '".addslashes(json_encode($arResult))."')";
					
					$DB->Query($query);
					
				}	
			}
        }
		
		//ИЗМЕНЕНИЕ СВОЙСТВА Что было до обновления
		public static function OnBeforeIBlockPropertyUpdate(&$arFields){
            if(isset($_REQUEST['only_seo'])) {
                return $arFields;
            }
			$acces = true;
			$arCatalogIds = array(50 => 'l1', 54 => 's0', 15 => 's1', 60 => 's6');
			if(empty($arCatalogIds[$arFields['IBLOCK_ID']])){
				$acces = false;
			}
			
			if($acces){
				$arPageData = array();
				
				$SITE_ID = $arCatalogIds[$arFields['IBLOCK_ID']];
				
				$file = __DIR__.'/../../cache/'.$SITE_ID.'-cache_page.php';
				if(file_exists($file)){
					$data = file_get_contents($file);
					list($php, $json) = explode("\n", $data, 2);
					$arPageData = json_decode($json, true);
				}
				else{
					return false;
				}
				
				//Получаем информацию о свойстве
				$propId = $arFields['ID'];
				$nameProp = $arPageData['LIST_PROP'][$propId]['NAME'];
				$codeProp = $arPageData['LIST_PROP'][$propId]['CODE'];
				$typeProp = $arPageData['LIST_PROP'][$propId]['PROPERTY_TYPE'];
				
				if(count($arPageData['LIST_PROP'][$propId]['USER_TYPE_SETTINGS']) > 0){
					$tableNameHL = $arPageData['LIST_PROP'][$propId]['USER_TYPE_SETTINGS']['TABLE_NAME'];
				}
				
				//Если свойство HL подтягиваем значения
				if($typeProp == 'S' && !empty($tableNameHL)){
					foreach($arPageData['HL'][$arPageData['HL_TABLE'][$tableNameHL]] as $arItem){
						$arPageData['LIST_PROP'][$propId]['LIST_VALUES_CUSTOM'][$arItem['UF_XML_ID']] = $arItem;
					}
				}
				
				define("arBeforeProp", $arPageData['LIST_PROP'][$propId]);		
			}
		}
		
		//ИЗМЕНЕНИЕ СВОЙСТВА Что стало после обновления
		public static function OnAfterIBlockPropertyUpdate(&$arFields){

		    if(isset($_REQUEST['only_seo'])) {
		        return $arFields;
            }

			$acces = true;
			$arCatalogIds = array(50 => 'l1', 54 => 's0', 15 => 's1', 60 => 's6');
			if(empty($arCatalogIds[$arFields['IBLOCK_ID']])){
				$acces = false;
			}
			
			$SITE_ID = $arCatalogIds[$arFields['IBLOCK_ID']];
			
			$file = __DIR__.'/../../cache/'.$SITE_ID.'-cache_page.php';
			if(file_exists($file)){
				$data = file_get_contents($file);
				list($php, $json) = explode("\n", $data, 2);
				$arPageData = json_decode($json, true);
			}
			else{
				$acces = false;
			}
			
			if($acces){
				//Получаем информацию о свойстве
				$arProp = array();
				$res = CIBlockProperty::GetByID($arFields['ID'], $arFields['IBLOCK_ID']);
				if($ar_res = $res->GetNext()){
					$arProp = $ar_res;
					
					$typeProp = $ar_res['PROPERTY_TYPE'];
					
					if(count($ar_res['USER_TYPE_SETTINGS']) > 0){
						$tableNameHL = $ar_res['USER_TYPE_SETTINGS']['TABLE_NAME'];
					}
					
					//Значения
					$arValues = array();
					
					//Если свойство список
					if($typeProp == "L"){
						$db_enum_list = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$ar_res['IBLOCK_ID'], "CODE"=>$ar_res['CODE']));
						while($ar_enum_list = $db_enum_list->GetNext())
						{
							$arValues[$ar_enum_list['ID']] = $ar_enum_list;
						}
						
						$arProp['LIST_VALUES_CUSTOM'] = $arValues;
					}
					
					//Если HL
					if($typeProp == 'S' && !empty($tableNameHL)){
						
						$HLBlockId = $arPageData['HL_TABLE'][$tableNameHL];
						
						$hlblock = HL\HighloadBlockTable::getById($HLBlockId)->fetch(); 

						$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
						$entity_data_class = $entity->getDataClass(); 

						$rsData = $entity_data_class::getList([
							"select" => [
								"*"
							],
							"order" => [
								"UF_SORT" => "ASC",
								"UF_NAME" => "ASC",
								"ID" => "ASC",
							]
						]);
						while($arData = $rsData->Fetch()){
							$arValues[$arData['UF_XML_ID']] = $arData;
						}
						
						$arProp['LIST_VALUES_CUSTOM'] = $arValues;
					}
				}	
				
				define("arAfterProp", $arProp);
				
				//pr(arBeforeProp);
				//pr(arAfterProp);
				
				//Узнаем что изменили
				$arResult = array();
				
				//В свойстве
				if(arBeforeProp['NAME'] != arAfterProp['NAME']){
					$arResult['PROPERTY']['NAME'] = arAfterProp['NAME'];
				}
				if(arBeforeProp['CODE'] != arAfterProp['CODE']){
					$arResult['PROPERTY']['CODE'] = arAfterProp['CODE'];
				}
				if(arBeforeProp['ACTIVE'] != arAfterProp['ACTIVE']){
					$arResult['PROPERTY']['ACTIVE'] = arAfterProp['ACTIVE'];
				}
				if(arBeforeProp['PROPERTY_TYPE'] != arAfterProp['PROPERTY_TYPE']){
					$arResult['PROPERTY']['PROPERTY_TYPE'] = arAfterProp['PROPERTY_TYPE'];
				}
				
				//В значениях			
				if(count(arBeforeProp['LIST_VALUES_CUSTOM']) > 0 && count(arAfterProp['LIST_VALUES_CUSTOM']) > 0){
					
					foreach(arBeforeProp['LIST_VALUES_CUSTOM'] as $valId => $arItem){
						
						//Список
						if(isset($arItem['XML_ID']) && $arItem['XML_ID'] != arAfterProp['LIST_VALUES_CUSTOM'][$valId]['XML_ID']){
							$arResult['VALUES'][$valId]['XML_ID'] = arAfterProp['LIST_VALUES_CUSTOM'][$valId]['XML_ID'];
						}
						if(isset($arItem['VALUE']) && $arItem['VALUE'] != arAfterProp['LIST_VALUES_CUSTOM'][$valId]['VALUE']){
							$arResult['VALUES'][$valId]['VALUE'] = arAfterProp['LIST_VALUES_CUSTOM'][$valId]['VALUE'];
						}
						
						//HL 
						if(isset($arItem['UF_XML_ID']) && $arItem['UF_XML_ID'] != arAfterProp['LIST_VALUES_CUSTOM'][$valId]['UF_XML_ID']){
							$arResult['VALUES'][$valId]['XML_ID'] = arAfterProp['LIST_VALUES_CUSTOM'][$valId]['UF_XML_ID'];
						}
						if(isset($arItem['UF_NAME']) && $arItem['UF_NAME'] != arAfterProp['LIST_VALUES_CUSTOM'][$valId]['UF_NAME']){
							$arResult['VALUES'][$valId]['VALUE'] = arAfterProp['LIST_VALUES_CUSTOM'][$valId]['UF_NAME'];
						}
				
					}
					
					//Проверяем добавилсось ли что нибудь
					foreach(arAfterProp['LIST_VALUES_CUSTOM'] as $valId => $arItem){
						if(!isset(arBeforeProp['LIST_VALUES_CUSTOM'][$valId])){
							$arResult['VALUES'][$valId]['XML_ID'] = $arItem['UF_XML_ID'] ? $arItem['UF_XML_ID'].' - ДОБАВЛЕНО' : $arItem['XML_ID'].' - ДОБАВЛЕНО';
							$arResult['VALUES'][$valId]['VALUE'] = $arItem['UF_NAME'] ? $arItem['UF_NAME'].' - ДОБАВЛЕНО' : $arItem['VALUE'].' - ДОБАВЛЕНО';
						}
					}
					
				}
				
				//Сохраняем в базу данных
				if(count($arResult['PROPERTY']) > 0 || count($arResult['VALUES']) > 0){
					
					global $DB;
					
					$query = "INSERT INTO `a_logs_update_prop` 
								(`id`, `iblock_id`, `prop_id`, `time`, `json`) 
							VALUES 
								(NULL, '".arAfterProp['IBLOCK_ID']."', '".arAfterProp['ID']."', '".strtotime(arAfterProp['TIMESTAMP_X'])."', '".addslashes(json_encode($arResult))."')";
					
					$DB->Query($query);
					
				}
			}
		}
		
        /**
         * Update Product-set price like summ off all set products prices
         * @param integer $productID    - product ID
         * @return boolean
         */
        public static function UpdateSetsProductPrice($productID){
            if (!$productID || !CModule::IncludeModule("catalog"))
                return FALSE;

            $arSets = CCatalogProductSet::getAllSetsByProduct(
                $productID,
                CCatalogProductSet::TYPE_SET
            );

            $arSet = current($arSets);
            if (!empty($arSet['ITEMS'])){
                $arIDs          = [$productID];
                $productPrice   = 0;
                $productPriceID = 0;
                $currencyCode   = '';
                foreach($arSet['ITEMS'] AS &$arItem)
                    $arIDs[] = $arItem['ITEM_ID'];

                $rsPrice    = CPrice::GetList([], [
                    'PRODUCT_ID'        => $arIDs,
                    'CATALOG_GROUP_ID'  => '1'
                ]);
                while($arPrice = $rsPrice->Fetch())
                    if ($productID != $arPrice['PRODUCT_ID'])
                        $productPrice += (int)$arPrice['PRICE'];
                    else{
                        $productPriceID = $arPrice['ID'];
                        $currencyCode   = $arPrice['CURRENCY'];
                    }

                if ($productPriceID && $productPrice && $currencyCode){
                    $res = CPrice::Update($productPriceID, [
                        'PRODUCT_ID'=> $productID,
                        'PRICE'     => $productPrice,
                        'CURRENCY'  => $currencyCode,
                        'CATALOG_GROUP_ID'  => 1
                    ]);
                    return TRUE;
                }
            }
			
			$arElement = CIBlockElement::GetByID($productID)->Fetch();
			if($arElement['IBLOCK_ID'])
			{
				$arSelect = array(
					'ID',
					'IBLOCK_ID',
					'PROPERTY_SERIES',
					'PROPERTY_MANUFACTURER',
					'PROPERTY_MATERIAL_ARMATURY_TSVET_ARMATURY',
					'PROPERTY_PARAMETRY_PLAFONA_TSVET_PLAFONOV',
					'PROPERTY_PLAFONA_FORMA_PLAFONA',
					'PROPERTY_PARAMETRY_PLAFONA_KOLICHESTVO_PLAFONOV',
					'PROPERTY_STIL',
					'PROPERTY_VID_SVETILNIKA',
				);
				foreach($GLOBALS['customCacheProps'] as $prop1=>$prop2)
				{
					$arSelect[] = 'PROPERTY_'.$prop1;
					$arSelect[] = 'PROPERTY_'.$prop2;
				}
				$arMainFilter = [
					'IBLOCK_ID' => $arElement['IBLOCK_ID'],
					'ID' => $productID
				];
				$ob = CIBlockElement::GetList(
					array(),
					$arMainFilter,
					false,
					false,
					$arSelect
				);
				if($obItem = $ob->GetNextElement())
				{
					$item = $obItem->GetFields();
					$props = $obItem->GetProperties();
					foreach($GLOBALS['customCacheProps'] as $prop1=>$prop2)
					{
						$item['PROPERTY_'.$prop1.'_VALUE'] = $props[$prop1]['VALUE'];
					}
					$arProps = CustomUpdateProductCache($item, $arElement['IBLOCK_ID']);
					/*
					if($GLOBALS['USER']->GetID() == 733)
					{
						pr($item);
						die();
					}
					*/
				}
			}
			
            return FALSE;
        }

        function deleteKernelCss(&$content) {
            global $USER, $APPLICATION;

            if ((is_object($USER) && $USER->IsAuthorized()) ||
                strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false)
                return;

            if ($APPLICATION->GetProperty("save_kernel") == "Y")
                return;

            $arPatternsToRemove = Array(
               '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
               '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
               '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
               '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
            );

            $content = preg_replace($arPatternsToRemove, "", $content);
            $content = preg_replace("/\n{2,}/", "\n\n", $content);
            $content = preg_replace("/<\!--\s(.*?)\s-->/is", "", $content);
            $content = preg_replace("/\s{2,}/mis", " ", $content);
            $content = preg_replace('/[\r\n]+/s',"\n",
                preg_replace('/[\r\n][ \t]+/s',"\n", $content)
            );
        }

        public static function OnBeforePriceUpdate(&$arFields){

        }

        public static function OnBeforeOrderAdd(&$arFields){
        }

        static function OnSaleComponentOrderProperties(&$arFields){
            if (!$arFields['ORDER_PROP'][6]){
                $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                $regionId = $request->getCookie("GEOLOCATION_ID");

                $arFields['ORDER_PROP'][6] = CSaleLocation::getLocationCODEbyID($regionId ? $regionId : DEFAULT_GEOLOCATION_ID);
            }
        }
	}
?>