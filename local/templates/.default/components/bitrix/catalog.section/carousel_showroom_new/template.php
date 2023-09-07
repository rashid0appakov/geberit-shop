<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
	$this->setFrameMode(true);

	if (empty($arResult["ITEMS"]))
		return "";

	$uniqueId = $this->randString();

	$carouselId	 = "carousel_$uniqueId";
	$leftArrowId	= "left_arrow_$uniqueId";
	$rightArrowId   = "right_arrow_$uniqueId";

	$jsParams = array(
		"carouselSelector" => "#$carouselId",
		"leftArrowSelector" => "#$leftArrowId",
		"rightArrowSelector" => "#$rightArrowId",
		"showCount" => $arParams["SHOW_COUNT"] ?: 4,
);?>
<div class="carousel carousel-showroom goods__carousel-tabs hero">
	<div class="container is-widescreen">
		<div id="<?=$carouselId?>" class="swiper-container preview-products">
			<div class="swiper-wrapper">
				<?foreach ($arResult["ITEMS"] AS &$arItem):?>
					<div class="swiper-slide" style="width: 270px; margin-right: 30px;">
						<?$APPLICATION->IncludeComponent(
							"bitrix:catalog.item",
							"product",
							array(
								"RESULT" => array(
									"ITEM" => $arItem,
									"AREA_ID" => $areaId,
								),
								"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<button id="<?=$leftArrowId?>" class="arrow left"></button>
		<button id="<?=$rightArrowId?>" class="arrow right custom-ajax-carousel-preload" data-id="<?=$carouselId?>" data-filter-key="<?=$arParams['FILTER_KEY']?>" data-skip="2" data-skip="2" data-pp="<?=$arParams['PAGE_ELEMENT_COUNT']?>" data-section="<?echo intval($arFilter['SECTION_ID']);?>"></button>
		<div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
		<script type="text/javascript">
			new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
		</script>
        <?/*?>
		<div class="more-mobile">
			<a href="/catalog/showroom/" class="btn is-primary is-outlined"><?=GetMessage('CT_ALL')?></a>
		</div><?/**/?>
	</div>
</div>