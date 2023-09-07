<?
$uniqueId = $this->randString();
$carouselId = "carousel_$uniqueId";
$leftArrowId = "left_arrow_$uniqueId";
$rightArrowId = "right_arrow_$uniqueId";
?>
<? if( count($arResult['SPARE']) > 0):?>
	<div class="carousel carousel-tabs goods__carousel-tabs hero" id="list-spare" data-nosnippet>
		<div class="container is-widescreen">
			<div class="level is-mobile carousel__title">
				<div class="level-left">
					<h2 class="is-size-3">Запасные детали</h2>
				</div>
			</div>
			<? if (count($arResult['SPARE']) > 1 && CClass::countItems($arResult['SPARE']) > 4):?>
			<section class="tabs goods__tabs">
				<div class="goods__tabs-button" id="goods__tabs-button-5"></div>
				<ul class="tabs__header" id="tabs__header-5">
					<li class="tabs__header--title js-tabs-title-spare active" data-tab="#tab-goods-spare-1"><?=GetMessage('CT_ALL_GOODS')?></li>
				<?php
					$i = 2;
					foreach($arResult['SPARE'] AS $key => &$sec ):?>
					<li class="tabs__header--title js-tabs-title-spare" data-tab="#tab-goods-spare-<?=$i;?>"><?=$key;?></li>
				<?php
					$i++;
					endforeach; ?>
				</ul>
				<div class="tabs__underline js-tabs-underline-spare"></div>
			</section>
			<? endif;?>
			<div class="content">
				<div class="content tabs__content js-tabs-content-spare active" id="tab-goods-spare-1">
					<div class="swiper-container swiper-container-hit5 preview-products" id="<?=$carouselId?>">
						<div class="swiper-wrapper">
						 <?
						$ii = 0;
						foreach($arResult['SPARE'] AS $key => &$sec):
							foreach($sec AS &$arItem):
								$ii ++;
								if($ii > 8)
								{
									break 2;
								}?>
								<div class="swiper-slide">
								  <?$APPLICATION->IncludeComponent(
										"bitrix:catalog.item",
										"product",
										array(
											"RESULT" => array(
												"ITEM" => $arItem
											),
											"PARAMS" => $arParams
										),
										$component,
										array("HIDE_ICONS" => "Y")
									);?>
								</div>
							<?php
								endforeach;
							endforeach; ?>
						</div>
					</div>
					<button id="<?=$leftArrowId?>" class="arrow left product-tabs-hit5-left"></button>
					<button id="<?=$rightArrowId?>" class="arrow right product-tabs-hit5-right custom-ajax-carousel-preload" data-id="<?=$carouselId?>" data-filter-key="ELEMENT" data-skip="2" data-pp="4" data-element="<?=$arResult['ID']?>" data-type="SPARE"></button>
					<div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
					<?/*<div class="more-mobile">
					  <a href="#" class="btn is-primary is-outlined">Смотреть все</a>
					</div>*/?>
				</div>
<?
$jsParams = array(
	"carouselSelector" => "#$carouselId",
	"leftArrowSelector" => "#$leftArrowId",
	"rightArrowSelector" => "#$rightArrowId",
	"showCount" => 4
);
?>
<script type="text/javascript">
	new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
	new Swiper('<?=$jsParams['carouselSelector']?>', {
	slidesPerView: 4,
	spaceBetween: 30,
	loop: false,
	//observer: true,
	//observeParents: true,
	navigation: {
		prevEl: '<?=$jsParams['leftArrowSelector']?>',
		nextEl: '<?=$jsParams['rightArrowSelector']?>',
	},
	breakpoints: {
		1200: {
		slidesPerView: 3
		},
		768: {
		spaceBetween: 0,
		slidesPerView: 3
		},
		550: {
		spaceBetween: 0,
		slidesPerView: 2
		}
	}
	});
</script>
<?php
		if (count($arResult['SPARE']) > 1 && CClass::countItems($arResult['SPARE']) > 4):
			$i = 2;
			foreach( $arResult['SPARE'] AS $key => &$sec):
				$carouselId .= '_'.$i;
				$leftArrowId .= '_'.$i;
				$rightArrowId .= '_'.$i;
				?>
				<div class="content tabs__content js-tabs-content-spare" id="tab-goods-spare-<?=$i;?>" data-nosnippet>
					<div class="swiper-container swiper-container-hit preview-products" id="<?=$carouselId?>">
						<div class="swiper-wrapper">
						<?
							$i = 0;
							foreach($sec AS &$arItem):
								$i ++;
								if($i > 8)
								{
									break;
								}?>
							<div class="swiper-slide">
								<?$APPLICATION->IncludeComponent(
									"bitrix:catalog.item",
									"product",
									array(
										"RESULT" => array(
											"ITEM" => $arItem,
										),
										"PARAMS" => $arParams
									),
									$component,
									array("HIDE_ICONS" => "Y")
								);?>
							</div>
						<? endforeach;?>
						</div>
					</div>
					<button id="<?=$leftArrowId?>" class="arrow left product-tabs-hit5-left"></button>
					<button id="<?=$rightArrowId?>" class="arrow right product-tabs-hit5-right custom-ajax-carousel-preload" data-tab="<?=$key;?>" data-id="<?=$carouselId?>" data-filter-key="ELEMENT" data-skip="2" data-pp="4" data-element="<?=$arResult['ID']?>" data-type="SPARE"></button>
					<div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
					<?/*<div class="more-mobile">
					  <a href="#" class="btn is-primary is-outlined">Смотреть все</a>
					</div>*/?>
				</div>
<?
$jsParams = array(
	"carouselSelector" => "#$carouselId",
	"leftArrowSelector" => "#$leftArrowId",
	"rightArrowSelector" => "#$rightArrowId",
	"showCount" => 4
);
?>
<script type="text/javascript">
	new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
	new Swiper('<?=$jsParams['carouselSelector']?>', {
	slidesPerView: 4,
	spaceBetween: 30,
	loop: false,
	//observer: true,
	//observeParents: true,
	navigation: {
		prevEl: '<?=$jsParams['leftArrowSelector']?>',
		nextEl: '<?=$jsParams['rightArrowSelector']?>',
	},
	breakpoints: {
		1200: {
		slidesPerView: 3
		},
		768: {
		spaceBetween: 0,
		slidesPerView: 3
		},
		550: {
		spaceBetween: 0,
		slidesPerView: 2
		}
	}
	});
</script>
		<?php
				$i++;
			endforeach;
		endif;?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			tabWidthSpare();

			/*Tabs */
			function tabWidthSpare() {
				var tabWidth = $('.js-tabs-title-spare.active').width();
				$('.js-tabs-underline-spare').css('width', tabWidth + 'px');
			};

			$('.js-tabs-title-spare').on('click', function () {
				var openTab = $(this).data('tab'),
				linePosition = $(this).position().left;

				$('.js-tabs-underline-spare').css('transform', 'translateX(' + linePosition + 'px)');
				$('.js-tabs-title-spare').removeClass('active');
				$(this).addClass('active');
				$('.js-tabs-content-spare').removeClass('active');
				$(openTab).addClass('active');
				tabWidthSpare();
			});
		});
	</script>
<? endif; ?>