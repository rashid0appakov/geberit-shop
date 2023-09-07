<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arFilter = $GLOBALS[$arParams['FILTER_NAME']];

	$this->setFrameMode(true);

	$uniqueId = $this->randString();

	$carouselId = "carousel_$uniqueId";
	$leftArrowId = "left_arrow_$uniqueId";
	$rightArrowId = "right_arrow_$uniqueId";

	$jsParams = array(
		"carouselSelector" => "#$carouselId",
		"leftArrowSelector" => "#$leftArrowId",
		"rightArrowSelector" => "#$rightArrowId",
		"showCount" => $arParams["SHOW_COUNT"] ? $arParams["SHOW_COUNT"] : 4
	);
	if (empty($arResult["ITEMS"]))
		return "";
?>
<?if(count($arResult["ITEMS"])):
	
	$arColorsTypes = array("NEWS" => "orange", "HITS" => "green", "SALES" => "pink", "SALEGOODS" => "pink");
	
	?>
	<div class="carousel carousel-news carousel-news--<?=$arColorsTypes[$arParams["TYPE"]]?> hero">
		<div class="is-widescreen">
			<?if(!empty($arParams["PAGER_TITLE"])){
				?>
				<div class="level is-mobile carousel__title">
					<div class="level-left">
						<h2 class="is-size-7"><?=$arParams["~PAGER_TITLE"]?></h2>
					</div>
				</div>
				<?
			}?>

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
			<button id="<?=$rightArrowId?>" class="arrow right custom-ajax-carousel-preload" data-id="<?=$carouselId?>" data-filter-key="<?=$arParams['FILTER_KEY']?>" data-skip="2" data-pp="<?=$arParams['PAGE_ELEMENT_COUNT']?>" data-section="<?echo intval($arFilter['SECTION_ID']);?>"></button>
			
			<div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
			
			<script type="text/javascript">
				new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
			</script>
			
			<div class="more-mobile">
				<a href="#" class="btn is-primary is-outlined">Смотреть все</a>
			</div>
			
		</div>
	</div>
<?endif?>