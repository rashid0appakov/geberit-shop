<?php
		function getSeriesSectionItemsCount(){
			$arResult = [];

			$arSections = CClass::getCatalogSection();

			/*$cache = new CPHPCache();
			$cache_id = 'SERIES_SECTIONS_ITEMS1';
			if ($cache->InitCache(CClass::CACHE_TIME, $cache_id, "/")){
				$res = $cache->GetVars();
				if (is_array($res["arSections"]) && (count($res["arSections"]) > 0))
				   $arResult = $res["arSections"];
			}*/

			//if (empty($arResult)){
				CModule::IncludeModule("iblock");
				$arSelect   = [
					"ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_SERIES"
				];
				$arFilter   = [
					'IBLOCK_ID' => CATALOG_IBLOCK_ID,
					'ACTIVE'	=> "Y"
				];

				$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 50000), $arSelect);
				while ($arItem = $dbItems->GetNext()){
					if (!isset($arResult[$arItem['PROPERTY_SERIES_VALUE']][$arItem['IBLOCK_SECTION_ID']])){
						$arResult[$arItem['PROPERTY_SERIES_VALUE']][$arItem['IBLOCK_SECTION_ID']] = [
							'NAME'			  => $arSections[$arItem['IBLOCK_SECTION_ID']]['NAME'],
							'SECTION_PAGE_URL'  => $arSections[$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL'],
							'ID'  => $arItem['IBLOCK_SECTION_ID']
						] ;
					}

					$arResult[$arItem['PROPERTY_SERIES_VALUE']][$arItem['IBLOCK_SECTION_ID']]['ITEMS_COUNT'] += 1;
				}

				/*$cache->StartDataCache(CClass::CACHE_TIME, $cache_id, "/");
				$cache->EndDataCache(array("arSections" => $arResult));
			}*/

			return $arResult;
		}