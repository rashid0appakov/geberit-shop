<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="goods">
	<div class="container goods__container">
		<div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
				"bitrix:breadcrumb",
				"main",
				Array(
					"PATH"	  => "",
					"SITE_ID"   => SITE_ID,
					"START_FROM"=> "0"
				)
			);?>
		</div>
		<div class="goods__title content">
			<h1><? $APPLICATION->ShowTitle(FALSE)?></h1>
		</div>
		<div class="goods__wrapper">
			<div class="goods__filter">
				<div class="goods__filter-container">
					<div class="goods__filter-content goods__filter-content--mobile">
						<div class="goods__filter-close-button"></div>
						<div class="filter">
							<?$APPLICATION->IncludeComponent(
								"bitrix:catalog.section.list",
								"vendor_sections",
								Array(
									"ADD_SECTIONS_CHAIN" => "N",
									"CACHE_GROUPS" => "Y",
									"CACHE_TIME" => "36000000",
									"CACHE_TYPE" => "A",
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO",
									"COUNT_ELEMENTS" => "Y",
									"IBLOCK_ID"	 => CATALOG_IBLOCK_ID,
									"SECTION_CODE"  => "",
									"SECTION_FIELDS" => array("CODE", "NAME", ""),
									"SECTION_ID" => "",
									"SECTION_URL" => "",
									"SECTION_USER_FIELDS" => array("",""),
									"SHOW_PARENT_NAME" => "N",
									"TOP_DEPTH" => "1",
									"VIEW_MODE" => "LIST"
								)
							);?>
						</div>
					</div><br /><br />
					<div class="goods__filter-content goods__filter-content--mobile">
						<div class="goods__filter-close-button"></div>
						<div class="filter">
							<?$APPLICATION->IncludeComponent(
								"bitrix:news.list",
								"filter_series_by_brands",
								Array(
									"IBLOCK_TYPE"   => $arParams["IBLOCK_TYPE"],
									"IBLOCK_ID"	 => $arParams["IBLOCK_ID"],
									"NEWS_COUNT"	=> 5000,
									"SORT_BY1"	  => "SORT",
									"SORT_ORDER1"   => "ASC",
									"SORT_BY2"	  => "ID",
									"SORT_ORDER2"   => "ASC",
									"FILTER_NAME"   => "arrSBFilter",
									"FIELD_CODE"	=> [],
									"PROPERTY_CODE" => ['BRAND'],
									"CHECK_DATES" => "Y",
									"DETAIL_URL" => "",
									"AJAX_MODE" => "N",
									"AJAX_OPTION_JUMP" => "N",
									"AJAX_OPTION_STYLE" => "N",
									"AJAX_OPTION_HISTORY" => "N",
									"CACHE_TYPE" => "A",
									"CACHE_TIME" => "36000000",
									"CACHE_FILTER" => "N",
									"CACHE_GROUPS" => "Y",
									"PREVIEW_TRUNCATE_LEN" => "",
									"ACTIVE_DATE_FORMAT" => "j F Y",
									"SET_TITLE" => "N",
									"SET_BROWSER_TITLE" => "N",
									"SET_META_KEYWORDS" => "N",
									"SET_META_DESCRIPTION" => "N",
									"SET_STATUS_404" => "N",
									"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
									"ADD_SECTIONS_CHAIN" => "N",
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",
									"PARENT_SECTION" => "",
									"PARENT_SECTION_CODE" => "",
									"INCLUDE_SUBSECTIONS" => "Y",
									"PAGER_TEMPLATE" => "arrows",
									"DISPLAY_TOP_PAGER" => "N",
									"DISPLAY_BOTTOM_PAGER" => "N",
									"PAGER_TITLE" => "",
									"PAGER_SHOW_ALWAYS" => "N",
									"PAGER_DESC_NUMBERING" => "N",
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
									"PAGER_SHOW_ALL" => "N"
								)
							);?>
						</div>
					</div>
				</div>
			</div>
			<div class="goods__card">
				<?php
					if (isset($_GET['brands'])){
						$arIDs = explode('_', $_GET['brands']);
						foreach($arIDs AS $k => &$id)
							if ((int)$id)
								$id = (int)$id;
							else
								unset($arIDs[$k]);
						if (!empty($arIDs)){
							global $arrSFilter;
							$arrSFilter['PROPERTY_BRAND'] = $arIDs;
						}
					}
					$ID =array();
					$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","CODE");
					$arFilter = Array("IBLOCK_ID"=>IntVal($arParams["IBLOCK_ID"]), "ACTIVE"=>"Y");
					if (!empty($arIDs)){
						$arFilter['PROPERTY_BRAND'] = $arIDs;
					}
					$res = CIBlockElement::GetList(Array(), $arFilter, false,false, $arSelect);
					while($ob = $res->GetNextElement())
					{
					 $arFields = $ob->GetFields();
					
					 	$old = false;
				 		$arSelect_old = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
						$arFilter_old = Array("IBLOCK_ID"=>IntVal($arParams["IBLOCK_ID"]), "ACTIVE"=>"Y","PROPERTY_OLD_CODE"=>$arFields['CODE']);
						$res_old = CIBlockElement::GetList(Array(), $arFilter_old, false,false, $arSelect_old);
						while($ob_old = $res_old->GetNextElement())
						{
							$arFields_old = $ob_old->GetFields();
							$old = true;
						}
					 	
						$ITEMS_COUNT=0;
						$arSelect_el = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
						$arFilter_el = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y","PROPERTY_SERIES"=>$arFields['ID'],'PROPERTY_DISCONTINUED' =>'N');
						$res_el = CIBlockElement::GetList(Array(), $arFilter_el, false,array('nTopCount'=>1), $arSelect_el);
						while($ob_el = $res_el->GetNextElement())
						{
							$arFields_el = $ob_el->GetFields();
							$ITEMS_COUNT ++;
						}
						
						if($ITEMS_COUNT!=0 && !$old){
							$ID[] =  $arFields['ID'];
						}
					}
					global $arrSFilter;
					$arrSFilter['ID'] = $ID;
				?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"series",
					Array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"NEWS_COUNT" => $arParams["NEWS_COUNT"],
						"SORT_BY1" => $arParams["SORT_BY1"],
						"SORT_ORDER1" => $arParams["SORT_ORDER1"],
						"SORT_BY2" => $arParams["SORT_BY2"],
						"SORT_ORDER2" => $arParams["SORT_ORDER2"],
						"FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
						"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
						"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
						"SET_TITLE" => $arParams["SET_TITLE"],
						"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
						"MESSAGE_404" => $arParams["MESSAGE_404"],
						"SET_STATUS_404" => $arParams["SET_STATUS_404"],
						"SHOW_404" => $arParams["SHOW_404"],
						"FILE_404" => $arParams["FILE_404"],
						"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_FILTER" => $arParams["CACHE_FILTER"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
						"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
						"PAGER_TITLE" => $arParams["PAGER_TITLE"],
						"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
						"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
						"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
						"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
						"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
						"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
						"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
						"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
						"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
						"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
						"PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
						"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
						"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
						"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
						"FILTER_NAME" => 'arrSFilter',
						"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
						"CHECK_DATES" => $arParams["CHECK_DATES"],
					),
					$component
				);?>
				<? global $NavNum;
				$NavNum = $NavNum-1;
				if(isset($_REQUEST["PAGEN_".$NavNum])){
					$brand = ' Geberit';
				    $title = $APPLICATION->GetTitle(false).$brand.' — Страница '.$_REQUEST["PAGEN_".$NavNum];
				    $APPLICATION->SetPageProperty('title', $title);
				    $APPLICATION->SetPageProperty('description','');
				}
				?>
			</div>
		</div>
	</div>
</div>