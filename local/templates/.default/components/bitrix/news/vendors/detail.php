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
<div class="goods series-section brand-detail">
	<?=$APPLICATION->ShowViewContent("BRAND_BG")?>
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
		<?$ElementID = $APPLICATION->IncludeComponent(
			"bitrix:news.detail",
			"vendors",
			Array(
				"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
				"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
				"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
				"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
				"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"META_KEYWORDS" => $arParams["META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
				"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
				"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
				"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"MESSAGE_404" => $arParams["MESSAGE_404"],
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"SHOW_404" => $arParams["SHOW_404"],
				"FILE_404" => $arParams["FILE_404"],
				"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
				"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
				"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
				"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
				"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
				"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
				"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
				"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
				"CHECK_DATES" => $arParams["CHECK_DATES"],
				"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
				"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
				"USE_SHARE" => $arParams["USE_SHARE"],
				"SHARE_HIDE" => $arParams["SHARE_HIDE"],
				"SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
				"SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
				"SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
				"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
				"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : 'N'),
				'STRICT_SECTION_CHECK' => (isset($arParams['STRICT_SECTION_CHECK']) ? $arParams['STRICT_SECTION_CHECK'] : ''),
				"BACK_URL"  => $arResult["URL_TEMPLATES"]["news"]
			),
			$component
		);?>
		<?php
			if ($APPLICATION->GetDirProperty('s_title')){
				$APPLICATION->SetTitle($APPLICATION->GetDirProperty('s_title'));
				$APPLICATION->SetPageProperty('title', $APPLICATION->GetDirProperty('s_title'));
			}
			if ($APPLICATION->GetDirProperty('s_keywords'))
				$APPLICATION->SetPageProperty('keywords', $APPLICATION->GetDirProperty('s_keywords'));
			if ($APPLICATION->GetDirProperty('s_description'))
				$APPLICATION->SetPageProperty('description', $APPLICATION->GetDirProperty('s_description'));
		?>
	</div>
	<?=$APPLICATION->ShowViewContent("BRAND_SERIES")?>
	<?php
	$arFilter = array(
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
		"!PROPERTY_NEWPRODUCT" => false,
		"CATALOG_AVAILABLE" => 'Y',
		"INCLUDE_SUBSECTIONS" => "Y",
		"PROPERTY_MANUFACTURER" => $ElementID
	);
	$cacheId = md5(serialize($arFilter));
	$cacheDir = "/brands/brand".$ElementID;
	$obCache = new CPHPCache();
	if ($obCache->InitCache(false, $cacheId, $cacheDir))
		$newproductsCount = $obCache->GetVars();
	elseif ($obCache->StartDataCache()){
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cacheDir);
		$CACHE_MANAGER->RegisterTag("iblock_id_".CATALOG_IBLOCK_ID.'_'.$ElementID);

		$newproductsCount = CIBLockElement::GetList(array(), $arFilter, array());

		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($salegoodsCount);
	}

	if ($newproductsCount > 0):?>
		<div class="container goods__container">
			<div class="carousel carousel-news carousel-news--orange hero">
				<div class="is-widescreen">
					<div class="level is-mobile carousel__title">
						<div class="level-left">
							<h2 class="is-size-7"><span><?=GetMessage('CT_NEW_PRODUCT_TITLE')?></span> <?=$APPLICATION->ShowViewContent("BRAND_TITLE")?> </h2>
						</div>
					</div>
					<?global $arrFilter;
					$arrFilter = $arFilter;
					$APPLICATION->IncludeComponent(
						"bitrix:catalog.section",
						"carousel",
						array(
							"IBLOCK_TYPE" => CATALOG_IBLOCK_ID,
							"IBLOCK_ID" => CATALOG_IBLOCK_ID,
							"ELEMENT_SORT_FIELD" => "RAND",
							"ELEMENT_SORT_ORDER" => "ASC",
							"ELEMENT_SORT_FIELD2" => "",
							"ELEMENT_SORT_ORDER2" => "",
							"PROPERTY_CODE" => array(
								0 => "NEWPRODUCT",
								1 => "SALELEADER",
								2 => "DISCOUNT",
								3 => "SALEGOODS",
								4 => "",
							),
							"SET_META_KEYWORDS" => "N",
							"SET_META_DESCRIPTION" => "N",
							"SET_BROWSER_TITLE" => "N",
							"SET_LAST_MODIFIED" => "N",
							"INCLUDE_SUBSECTIONS" => "Y",
							"SHOW_ALL_WO_SECTION" => "Y",
							"BASKET_URL" => "/personal/cart/",
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"PRODUCT_QUANTITY_VARIABLE" => "quantity",
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"FILTER_NAME" => "arrFilter",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "36000000",
							"CACHE_FILTER" => "Y",
							"CACHE_GROUPS" => "Y",
							"SET_TITLE" => "N",
							"MESSAGE_404" => "",
							"SET_STATUS_404" => "N",
							"SHOW_404" => "N",
							"FILE_404" => "",
							"DISPLAY_COMPARE" => "Y",
							"PAGE_ELEMENT_COUNT" => "10",
							"LINE_ELEMENT_COUNT" => "",
							"PRICE_CODE" => CClass::getCurrentPriceCode(),
							"USE_PRICE_COUNT" => "Y",
							"SHOW_PRICE_COUNT" => "1",
							"PRICE_VAT_INCLUDE" => "Y",
							"USE_PRODUCT_QUANTITY" => "Y",
							"ADD_PROPERTIES_TO_BASKET" => "N",
							"PARTIAL_PRODUCT_PROPERTIES" => "N",
							"PRODUCT_PROPERTIES" => array(
							),
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_TITLE" => "",
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_TEMPLATE" => "",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "N",
							"PAGER_BASE_LINK_ENABLE" => "N",
							"PAGER_BASE_LINK" => "",
							"PAGER_PARAMS_NAME" => "",
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"USE_MAIN_ELEMENT_SECTION" => "Y",
							"CONVERT_CURRENCY" => "N",
							"CURRENCY_ID" => "",
							"HIDE_NOT_AVAILABLE" => "N",
							"ADD_SECTIONS_CHAIN" => "N",
							"COMPARE_PATH" => "",
							"BACKGROUND_IMAGE" => "",
							"DISABLE_INIT_JS_IN_COMPONENT" => "N",
							"PROPERTY_CODE_MOD" => array(
								0 => "GUARANTEE",
								1 => "",
							),
							"COMPONENT_TEMPLATE" => "filtered",
							"SECTION_USER_FIELDS" => array(
								0 => "",
								1 => "",
							),
							"CUSTOM_FILTER" => "",
							"HIDE_NOT_AVAILABLE_OFFERS" => "N",
							"SEF_MODE" => "N",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"BROWSER_TITLE" => "-",
							"META_KEYWORDS" => "-",
							"META_DESCRIPTION" => "-",
							"COMPATIBLE_MODE" => "Y",

							"SHOW_COUNT" => 4
						),
						false
					);?>
				</div>
			</div>
		</div>
	<?endif;?>
	<?
	$APPLICATION->SetTitle(str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetTitle()));
	$APPLICATION->SetPageProperty('title', str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetPageProperty('title')));
	?>
</div>