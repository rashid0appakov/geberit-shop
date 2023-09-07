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
	
	$bIsBrandSite = false;
	
	if (defined("MAIN_SITE_BRAND") && is_array(MAIN_SITE_BRAND) && !empty(MAIN_SITE_BRAND))
		$bIsBrandSite = true;

	$set404=true;

	$arSelect = Array("ID", "CODE");
	//var_dump($arResult["VARIABLES"]["ELEMENT_CODE"]);
	$arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_OLD_CODE"=>$arResult["VARIABLES"]["ELEMENT_CODE"]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();
		LocalRedirect('/series/'.$arFields['CODE'].'/', false, '301 Moved permanently');
	}


	$arSelect = Array("ID", "NAME", "IBLOCK_ID","PROPERTY_BRAND","PROPERTY_NAME_RU");

	//var_dump($arResult["VARIABLES"]["ELEMENT_CODE"]);
	$arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE"=>"Y", "CODE"=>$arResult["VARIABLES"]["ELEMENT_CODE"]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();
		$nameSerie_ru = $nameSerie = $arFields['NAME'];
		if($arFields['PROPERTY_NAME_RU_VALUE']){
			$nameSerie_ru =$arFields['PROPERTY_NAME_RU_VALUE'];
		}
		//if($arFields['PROPERTY_BRAND_VALUE'] == '420764'){//geberit
			$set404=false;
		//}
	}

	// var_dump($set404);
	// die;
	if($set404){
		Bitrix\Iblock\Component\Tools::process404(
		       '', //Сообщение
		       true, // Нужно ли определять 404-ю константу
		       true, // Устанавливать ли статус
		       true, // Показывать ли 404-ю страницу
		       false // Ссылка на отличную от стандартной 404-ю
		);
	}

//	var_dump($set404);

?>
<?$APPLICATION->AddChainItem("Серии", "/series/");?>
<div class="goods series-section">
	<?=$APPLICATION->ShowViewContent("SERIES_BG")?>
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
			"series_new",
			Array(
				"IS_BRAND_SITE" => ($bIsBrandSite ? "Y" : "N"),
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
<? 
	/*$ITEMS_COUNT=0;
	$arSelect_el = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
	$arFilter_el = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y","PROPERTY_SERIES"=>$ElementID );
	$res_el = CIBlockElement::GetList(Array(), $arFilter_el, false,array('nTopCount'=>1), $arSelect_el);
	while($ob_el = $res_el->GetNextElement())
	{
		$arFields_el = $ob_el->GetFields();
		$ITEMS_COUNT ++;
	}
	if($ITEMS_COUNT==0){
		Bitrix\Iblock\Component\Tools::process404(
		       '', //Сообщение
		       true, // Нужно ли определять 404-ю константу
		       true, // Устанавливать ли статус
		       true, // Показывать ли 404-ю страницу
		       false // Ссылка на отличную от стандартной 404-ю
		);
	}*/
	?>		
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
	<?=$APPLICATION->ShowViewContent("SERIES_GALLERY")?>
	<?
	$APPLICATION->SetTitle(str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetTitle()));
	$APPLICATION->SetPageProperty('title', str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetPageProperty('title')));
	$APPLICATION->SetPageProperty('description', str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetPageProperty('description')));
	?>
</div>
<?
	//$geo = "в Москве";
	$market = 'Geberit';
	$market_ru = 'Геберит';
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	/*if ($geo_id == 817){$geo = "в Санкт-Петербурге";}
	if ($geo_id == 2201){$geo = "в Екатеринбурге";}*/
	$geo = GetGeoText();
	$title = $market.' '.$nameSerie.' — купить '.$geo.', каталог товаров '.$market_ru.' '.$nameSerie_ru.' в geberit-shop.ru';
	$h1 = $market.' '.$nameSerie;
	$pagen = 0;
	foreach ($_GET as $key => $value) {
		if(strpos($key, 'PAGEN_')!==false){
			$pagen = $value;
		}		
	}
	if($pagen>0){
		$title = $h1.'- Страница №'.$pagen;
		if ($geo_id == 817 || strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){
			$title = $h1.' '.GetGeoText().'- Страница №'.$pagen;
	    }
		$h1 =trim($h1).'. Страница №'.$pagen; 
	}
	
  	$APPLICATION->SetTitle ($h1);
  	$APPLICATION->SetPageProperty('title', $title);
	?>