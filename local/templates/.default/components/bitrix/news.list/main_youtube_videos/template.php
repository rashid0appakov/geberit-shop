<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<? if( count($arResult["ITEMS"]) > 0 ){?>
	<div class="main-videos carousel hero">
		<div class="container is-widescreen">
			<div class="level">
				<div class="level-left">
					<h2 class="is-size-3">Видео на нашем канале YouTube</h2>
				</div>
				<div class="level-right">
					<a class="btn is-primary is-outlined" href="#">Все видео на канале</a>
				</div>
			</div>
			<div class="swiper-container preview-videos">
				<div class="swiper-wrapper">
					<?foreach ($arResult["ITEMS"] as $arItem):?>
						<div class="swiper-slide">
							<div class="video">
								<?=$arItem["PROPERTIES"]["BACKGROUND_YOUTUBE"]["~VALUE"]?>
							</div>
						</div>
					<?endforeach;?>
				</div>
			</div>
			<div class="arrow left main-videos-left"></div>
			<div class="arrow right main-videos-right"></div>
			<div class="finger-mobile">Перемещайте видео пальцем</div>
			<div class="more-mobile">
				<a href="#" class="btn is-primary is-outlined">Все видео на канале</a>
			</div>
		</div>
	</div>
<? } ?>