<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1) return;
?>

<div class="owl-carousel owl-theme  owl-carousel__showroom">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?if(!empty($arItem['PREVIEW_PICTURE']['SRC'])) {?>
			<div class="owl-carousel__item">
				<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>">
			</div>
		<? } ?>
	<? } ?>
</div>

<div id="carousel-image-dots" class="owl-dots">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?if(!empty($arItem['PICTURE_PREVIEW_SMALL']['SRC'])) {?>
			<div class="image-dot owl-dot"><img src="<?=$arItem['PICTURE_PREVIEW_SMALL']['SRC']?>" alt=""></div>
		<? } ?>
	<? } ?>
</div>

<script>
	$(function() {
		var owl = $('.owl-carousel__showroom').owlCarousel({
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
	})
</script>
