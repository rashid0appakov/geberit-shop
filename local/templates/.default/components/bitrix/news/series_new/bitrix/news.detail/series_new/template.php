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
	$this->SetViewTarget("SERIES_BG");
	/*if ($arResult['DETAIL_PICTURE']['SRC'])
		$style  = ' style="background-image: url(\''.$arResult['DETAIL_PICTURE']['SRC'].'\')"';*/

	$hide_text=true;
		foreach ($_GET as $key => $value) {
			if(strpos($key, 'PAGEN_')!==false){
				$hide_text=false;
			}
		}
?>

<div class="series-background<?=(!$style ? ' no-bg' : '')?>"<?=$style?>></div>
<?$this->EndViewTarget();?>
<div class="goods__wrapper<?=(!$style ? ' no-bg' : '')?>">
	<div class="content">
		<? if (!empty($arResult['BRAND']['RESIZED'])):?>
		<div class="series-title-right">
			<a href="<?=$arResult['BRAND']['DETAIL_PAGE_URL']?>">
				<img src="<?=$arResult['BRAND']['RESIZED']['src']?>" alt="<?=$arResult['NAME']?>" />
			</a>
		</div>
		<? endif;?>
		<div class="series-title-left">
			<div class="series-title"><?=GetMessage('CT_COLLECTION_TITLE')?></div>
			<h1><?$APPLICATION->ShowTitle(false);?></h1>
			<div class="series-description" <?if(!$hide_text){?>style="min-height: 111px;"<?}?>><?if($hide_text){?><?=(is_array($arResult['DETAIL_TEXT']) ? strip_tags($arResult['~DETAIL_TEXT']['TEXT'], '<p><br><ul><li><ol>') : strip_tags($arResult['DETAIL_TEXT'], '<p><br><ul><li><ol>'))?><?}?></div>
		</div>
	</div>

	<? if (!empty($arResult['SECTIONS'])):
		$uniqueId = $this->randString();

		$params = base64_encode(serialize($arParams));
		$containerId = "container_$uniqueId";

		$jsParams = array(
			"params" => $params,
			"containerSelector" => "#$containerId",
		);
	?>
	<h2>Каталог <?=$arResult['BRAND']['NAME']?> <?=$arResult['NAME']?></h2>
	<div class="categoryWrapper">
		<div id="<?=$containerId?>" class="columns is-gapless is-multiline categoryCardsWrapper">
			<? 
			foreach($arResult['SECTIONS'] as &$arSection):
				$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","CODE", 'CATALOG_QUANTITY');
	            $arFilter = Array("IBLOCK_ID"=>IntVal(CATALOG_IBLOCK_ID), "SECTION_ID" => $arSection["ID"], "!PROPERTY_DISCONTINUED" => "Y", "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_SERIES' => $arResult['ID']/*, ">CATALOG_QUANTITY" => 0*/);

	            //var_dump($arFilter);

	            $res = CIBlockElement::GetList(Array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
	            if($ob = $res->GetNextElement())
	            {
//	             $arFields = $ob->GetFields();
	            }else{
	            	continue;
	            }

			// foreach($arResult['SECTIONS'] as &$arSection):
			// 		$use_ser = false;
			// 	foreach(CIBlockSectionPropertyLink::GetArray(CATALOG_IBLOCK_ID, $arSection["ID"]) as $PID => $arLink)  {
			// 	    if($arLink["SMART_FILTER"] !== "Y")
			// 	            continue;
			// 	    if($arLink["PROPERTY_ID"]==5760){
			// 			$use_ser = true;
			// 	    }
			// 	}
			// 	if(!$use_ser) continue;
				?>
				<div class="column is-12-mobile is-4-tablet is-3-desktop">
					<div class="categoryCardWrapper">
						<div class="categoryCard">
							<? if (!empty($arSection['RESIZED'])):?>
							<div class="categoryImages">
								<div class="categoryImage">
									<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
									<?foreach ($arSection['RESIZED'] as &$arImg):?>
										<div class="categoryImageWrapper">
											<img src="<?=$arImg['src']?>" alt="" />
										</div>
									<?endforeach;?>
									</a>
								</div>
							</div>
							<? endif;?>
							<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="categoryTitle">
								<span class="title"><?=$arSection["NAME"]?> <?=$arResult['BRAND']['NAME']?> <?=$arResult['NAME']?></span>
								
							</a><br><span class="categoryNum"><?=$arSection["ITEMS_COUNT"]?> <?=GetMessage('HDR_ITEM_TITLE').CClass::getFilesEnds($arSection["ITEMS_COUNT"])?></span>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
	<script type="text/javascript">
		window.sectionList = new JSCatalogSectionListCategorySections(<?=json_encode($jsParams)?>);
	</script>
	<? endif;?>
	<?/*<div class="go-back">
		<a href="<?=$arParams['SECTION_URL']?>" class="news-page-back__link"><?=GetMessage('T_NEWS_DETAIL_BACK')?></a>
	</div>*/?>
</div>
<? if (!empty($arResult['PHOTOS'])):
	$this->SetViewTarget("SERIES_GALLERY");?>
	<div id="series-gallery">
	<? foreach($arResult['PHOTOS'] as $arPhoto):?>
		<div class="slick-slide">
			<img src="<?=$arPhoto['src']?>" alt="" />
		</div>
	<? endforeach;?>
	</div>
	<div class="container goods__container series-slick-arrows">
		<a class="series-slick-prev-arrow slick-arrows">&larr;</a>
		<a class="series-slick-next-arrow slick-arrows">&rarr;</a>
	</div>
	<?$this->EndViewTarget();?>
<?endif; ?>
<style>
.product {
    width: 25% !important;
    min-width: 25%;
}
</style>

  <?
	global $serFilter;
	$serFilter = array(
		"PROPERTY_SERIES" => $arResult['ID'],
		"SECTION_GLOBAL_ACTIVE"=>"Y"
	);

	
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		'series',
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => CATALOG_IBLOCK_ID,//$arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => GetSortField(),
	        "ELEMENT_SORT_FIELD2" => "SORT",//"PROPERTY_PRICE_UPDATE".CClass::getCurrentAvalCode(),
	        "ELEMENT_SORT_ORDER" => "ASC",
	        "ELEMENT_SORT_ORDER2" => "ASC",
			"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PROPERTY_CODE" => array("ARTNUMBER","MANUFACTURER","ODDS","MORE_PHOTO"), //$arParams["PROPERTY_CODE"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
			"SECTION_URL" => $arParams["SECTION_URL"],
			"DETAIL_URL" => "/product/#ELEMENT_CODE#/",
			"BASKET_URL" => "/personal/cart/", //$arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"DISPLAY_COMPARE" => true, //$arParams["DISPLAY_COMPARE"],
			"PRICE_CODE" => array("BASE"), //$arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => "Товары",//$arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => "load_more",//$arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"FILTER_NAME" => "serFilter",
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"META_KEYWORDS" => "",
			"META_DESCRIPTION" => "",
			"BROWSER_TITLE" => "",
			"ADD_SECTIONS_CHAIN" => "N",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "N",
			"SEIRES" => $arResult['NAME'],
			"BREND" => "Geberit",
		),
		$arResult["THEME_COMPONENT"],
		array('HIDE_ICONS' => 'Y')
	);?>
	 
