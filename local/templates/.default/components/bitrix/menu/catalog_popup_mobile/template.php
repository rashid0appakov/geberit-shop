<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$jsParams = [];
?>
	<div id="popup-catalog-menu-start-mobile" class="popup-catalog-menu-start-mobile" style="left: -1600px;">
		<div class="catalog-menu-popup">
			<?/*<div class="container"> -->*/?>
			<div class="header container">
				<div class="search field">
					<div class="back">Каталог</div>
					<form action="/catalog/">
						<div class="header-submit"></div>
						<input class="input" type="text" name="q" placeholder="Поиск товаров" />
					</form>

				</div>
				<div id="popup-catalog-menu-start-mobile-close" class="close">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2"></line>
						<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2"></line>
					</svg>
				</div>
			</div>

			<div class="subcategories">
			<?$i = 1;
			foreach ($arResult["ITEMS"] AS $key => $arItem):
				if( count($arItem["ITEMS"]) > 0 ) {
					$link = "#popup-catalog-menu-mobile-".$key;
					$sub = 'true';
				} else {
					$link = $arItem['LINK'];
					$sub = 'false';
				}
				?>
				<div class="section">
					<a href="<?=$link;?>" data-sub="<?=$sub?>" <? if( count($arItem["ITEMS"]) > 0 ) { ?>class="is-size-4"<? } ?>>
						<?=$arItem['TEXT'];?>
					</a>
				</div>
			<?endforeach;?>
			</div>
	  <?/*</div>*/?>
		</div>
	</div>
<?$i = 1;
foreach ($arResult["ITEMS"] as $key=>$arItem):?>
	<div id="popup-catalog-menu-mobile-<?=$key;?>" class="popup-catalog-menu-mobile" style="left: 400px;">
		<div class="catalog-menu-popup">
			<?/*<div class="container">*/?>
			<div class="header">
				<div id="popup-catalog-menu-mobile-back-<?=$key;?>" class="back"><?=$arItem['TEXT'];?></div>
				<div id="popup-catalog-menu-mobile-close-<?=$key;?>" class="close">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2"></line>
						<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2"></line>
					</svg>
				</div>
			</div>
		  <div class="subcategories">
			<div class="section">
				<a href="<?=$arItem["LINK"]?>" class="is-size-4"><?=$arItem["TEXT"]?></a>
			</div>
			<?foreach ($arItem["ITEMS"] AS &$arItem2):?>
				<div class="section">
					<a href="<?=$arItem2["LINK"]?>" class="is-size-4"><?=$arItem2["TEXT"]?></a>
				</div>
			<?endforeach;?>
			<?/*<div class="border">
			  <div class="mini-product">
				<div class="line">
				  <a href="#">
					<img src="img/product-mini-1.png">
					<span class="article">Артикул: 123456</span>
					<div>Зеркало-шкаф с подсветкой 90см Keramag Option</div>
					<span class="old">9 999р.</span>
					<span class="new">9 000р.</span>
				  </a>
				</div>
			  </div>
			</div>*/?>
		  </div>
		  <?/*</div> -->*/?>
		</div>
	</div>
<?endforeach;?>
<script type="text/javascript">
	$(document).ready(function() {
		if ($('.popup-catalog-menu-mobile .back').length)
			$('.popup-catalog-menu-mobile .back').on('click', function () {
				TweenMax.fromTo('popup-catalog-menu-start-mobile', 1, {
					ease: Power4.easeOut,
					left: -globalWidth
				}, {
					ease: Power4.easeOut,
					left: 0
				});
				TweenMax.fromTo('.popup-catalog-menu-mobile', 1, {
					ease: Power4.easeOut,
					left: 0
				}, {
					ease: Power4.easeOut,
					left: globalWidth
				});
			});
	})
</script>