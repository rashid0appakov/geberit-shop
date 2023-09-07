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

$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();

$this->setFrameMode(true);
?>
<?//pr($arResult);?>
<div class="contacts-detail" itemscope itemtype="http://schema.org/Organization">
	<?if(isset($arResult['PROPERTIES']['MAPS']) && !empty($arResult['PROPERTIES']['MAPS']['VALUE'])) {?>
		<div class="contacts-detail__map">
			<div class="contacts-detail__map-view">
				<?$arPos = explode(",", $arResult['PROPERTIES']['MAPS']['VALUE']);?>
				<?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", Array(
						"INIT_MAP_TYPE" => "MAP",
						"MAP_DATA" => serialize(array(
							'yandex_lat' => $arPos[0],
							'yandex_lon' => $arPos[1],
							'yandex_scale' => 13,
							'PLACEMARKS' => array(
								array(
									'TEXT' => $arResult['PROPERTIES']['MAPS']['VALUE'].", ".$arResult['PROPERTIES']['MAPS']['VALUE'],
									'LON' => $arPos[1],
									'LAT' => $arPos[0],
								),
							),
						)),
						"MAP_WIDTH" => "auto",
						"MAP_HEIGHT" => "300",
						"CONTROLS" => array(
							"SMALLZOOM",
						),
						"OPTIONS" => array(
							"ENABLE_SCROLL_ZOOM",
							"ENABLE_DBLCLICK_ZOOM",
							"ENABLE_DRAGGING"
						),
						"MAP_ID" => "yam_1"
					)
				);?>
			</div>
			<?if(isset($arResult['PROPERTIES']['MAPS_DESCRIPTION']) && !empty($arResult['PROPERTIES']['MAPS_DESCRIPTION']['~VALUE'])) {?>
				<div class="contacts-detail__map-popup">
					<div class="contacts-detail__map-popup-content">
						<?=$arResult['PROPERTIES']['MAPS_DESCRIPTION']['~VALUE']['TEXT']?>
					</div>
				</div>
			<?}?>
		</div>
	<?}?>
    <?if(isset($arResult['PROPERTIES']['MAPS_ADDITIONAL']) && !empty($arResult['PROPERTIES']['MAPS_ADDITIONAL']['~VALUE'])) {?>
		<?=$arResult['PROPERTIES']['MAPS_ADDITIONAL']['~VALUE']['TEXT']?>
	<?}?>
	<div class="contacts-detail__properties">
		<div class="contacts-detail__property-group">
			<div class="contacts-detail__property-group-title">
				<?=$arResult['PROPERTIES']['FILIAL_NAME']['VALUE']?>
			</div>
			<div class="contacts-detail__property-group-row">
				<?if(isset($arResult['PROPERTIES']['ADRESS']) && !empty($arResult['PROPERTIES']['ADRESS']['~VALUE'])) {?>
					<div class="contacts-detail__property">
						<h5 class="contacts-detail__property-title-wrap">
							<div class="contacts-detail__property-icon contacts-detail__property-icon--address"></div>
							<div class="contacts-detail__property-title"><?=$arResult['PROPERTIES']['ADRESS']['NAME']?></div>
						</h5>
						<div class="contacts-detail__property-content" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
							<?=$arResult['PROPERTIES']['ADRESS']['~VALUE']['TEXT']?>
						</div>
					</div>
				<?}?>
			</div>
			<div class="contacts-detail__property-group-row contacts-detail__property-group-row--double">
				<?if(isset($arResult['PROPERTIES']['TIME_OFFICE']) && !empty($arResult['PROPERTIES']['TIME_OFFICE']['~VALUE'])) {?>
					<div class="contacts-detail__property">
						<h5 class="contacts-detail__property-title-wrap">
							<div class="contacts-detail__property-icon contacts-detail__property-icon--office-time"></div>
							<div class="contacts-detail__property-title"><?=$arResult['PROPERTIES']['TIME_OFFICE']['NAME']?></div>
						</h5>
						<div class="contacts-detail__property-content" itemprop="openingHours" datetime="Mo-Su 09:00−21:00">
							<?=$arResult['PROPERTIES']['TIME_OFFICE']['~VALUE']['TEXT']?>
						</div>
					</div>
				<?}?>
				<?if(isset($arResult['PROPERTIES']['TIME_SKLAD']) && !empty($arResult['PROPERTIES']['TIME_SKLAD']['~VALUE'])) {?>
					<div class="contacts-detail__property">
						<h5 class="contacts-detail__property-title-wrap">
							<div class="contacts-detail__property-icon contacts-detail__property-icon--warehouse"></div>
							<div class="contacts-detail__property-title"><?=$arResult['PROPERTIES']['TIME_SKLAD']['NAME']?></div>
						</h5>
						<div class="contacts-detail__property-content">
							<?=$arResult['PROPERTIES']['TIME_SKLAD']['~VALUE']['TEXT']?>
						</div>
					</div>
				<?}?>
			</div>
			
			<?=$arResult["PROPERTIES"]['SKLAD']['~VALUE']['TEXT'];?>
			
		</div>
		
		
		
		<div class="contacts-detail__property-group">
			<div class="contacts-detail__property-group-title">
				Реквизиты
			</div>
			<div class="contacts-detail__property-group-row contacts-detail__property-group-row--double">
				<?if(isset($arResult['PROPERTIES']['FIZ_LICA']) && !empty($arResult['PROPERTIES']['FIZ_LICA']['~VALUE'])) {?>
					<div class="contacts-detail__property">
						<h5 class="contacts-detail__property-title-wrap">
							<div class="contacts-detail__property-icon contacts-detail__property-icon--fiz-lica"></div>
							<div class="contacts-detail__property-title"><?=$arResult['PROPERTIES']['FIZ_LICA']['NAME']?></div>
						</h5>
						<div class="contacts-detail__property-content">
							<?=$arResult['PROPERTIES']['FIZ_LICA']['~VALUE']['TEXT']?>
						</div>
					</div>
				<?}?>
				<?if(isset($arResult['PROPERTIES']['UR_LICA']) && !empty($arResult['PROPERTIES']['UR_LICA']['~VALUE'])) {?>
					<div class="contacts-detail__property">
						<?/* <h5 class="contacts-detail__property-title-wrap">
							<div class="contacts-detail__property-icon contacts-detail__property-icon--ur-lica"></div>
							<div class="contacts-detail__property-title"><?=$arResult['PROPERTIES']['UR_LICA']['NAME']?></div>
						</h5> */?>
						<div class="contacts-detail__property-content">
							<?=$arResult['PROPERTIES']['UR_LICA']['~VALUE']['TEXT']?>
						</div>
					</div>
				<?}?>
			</div>
		</div>
		<?if(isset($arResult['PROPERTIES']['MANAGERS']) && !empty($arResult['PROPERTIES']['MANAGERS']['~VALUE'])) {?>
			<div class="contacts-detail__property-group">
				<div class="contacts-detail__property-group-title">
					Отдел продаж
				</div>
				<div class="contacts-detail__property-group-row">
					<div class="contacts-detail__property">
						<div class="contacts-detail__property-content">
							<?=$arResult['PROPERTIES']['MANAGERS']['~VALUE']['TEXT']?>
						</div>
					</div>
				</div>
			</div>
		<?}?>
	</div>
	<?if(!empty($arResult["PHOTOS"]) && is_array($arResult["PHOTOS"])) {?>
		<div class="contacts-detail__photos">
			<div class="contacts-detail__photos-title"><?=$arResult['PROPERTIES']['FOTO']['NAME']?></div>
			<div class="contacts-detail__photos-slider">
				<div id="constant_slider" class="owl-carousel owl-theme">
					<?foreach($arResult["PHOTOS"] as $arItem) { ?>
						<?if(!empty($arItem['SRC'])) {?>
							<div class="owl-carousel__item">
								<img src="<?=$arItem['SRC']?>">
							</div>
						<? } ?>
					<? } ?>
				</div>
			</div>
		</div>
	<?}?>
</div>

<?
$jsParams = array(
	"carouselId" => "constant_slider",
);
?>
<script>
	var mainSlider = new JSMainSlider(<?=json_encode($jsParams)?>);
</script>
<?/*
<script>
	$(function() {
		if ($('.owl-carousel__contacts-photo').length) {
			var owl = $('.owl-carousel__contacts-photo').owlCarousel({
				loop: true,
				smartSpeed: 700,
				nav: true,
				autoplay: false,
				dotsContainer: '#carousel-image-dots',
				navText: ["<img src='<?=SITE_DEFAULT_PATH?>/images/arrow_left.png'>","<img src='<?=SITE_DEFAULT_PATH?>/images/arrow_right.png'>"],
				responsive: {
					0: {
						items: 1
					},
					600: {
						items: 1
					},
					1000: {
						items: 1
					}
				}
			});
			$('.play').on('click', function () {
				owl.trigger('play.owl.autoplay', [1000]);
			});
			$('.stop').on('click', function () {
				owl.trigger('stop.owl.autoplay');
			});
			$('.owl-dot').click(function () {
				owl.trigger('to.owl.carousel', [$(this).index(), 300]);
			});
		}
	});
</script>
*/?>
<?
$arLogo = array(
			'l1' => 'https://swet-online.ru/upload/swet/logo.png',
			's0' => 'https://tiptop-shop.ru/upload/tiptop/logo.png',
			's1' => 'https://drvt.shop/upload/duravit/logo.png',
			's6' => 'https://shop-gr/upload/gr/Grohe-logo.png',
);
?>
<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "Organization",
		"name": "<?=$arSite['NAME']?>",
		"url": "https://<?=SITE_SERVER_NAME?>",
		"logo": "<?=$arLogo[SITE_ID]?>",
		"address": "<?=preg_replace("/\r|\n/", " ", strip_tags($arResult['PROPERTIES']['ADRESS']['~VALUE']['TEXT']))?>",
		"contactPoint": {
			"@type": "ContactPoint",
			"contactType": "customer service",
			"telephone": "<?=$arResult['PROPERTIES']['PHONE']['VALUE']?>",
			"email": "<?=$arSite['EMAIL']?>"
		}
	}
</script>
