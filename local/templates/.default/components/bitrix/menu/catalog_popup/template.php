<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
	$this->setFrameMode(true);
	$jsParams = [];
	if (empty($arResult["ITEMS"]))
		return "";

	$arResult["ITEMS_CHUNK"] = array_chunk($arResult["ITEMS"], 70);
?>
<?/*<input id="open-catalog-menu-popup" type="checkbox" class="system">*/?>
<div class="catalog-menu-popup hero simple-type">
	<div class="columns">
	<?php
	if (!empty($arResult["ITEMS_CHUNK"])):?>
		<div class="column categories categories__list">
		<? foreach($arResult["ITEMS_CHUNK"] AS &$item)
			foreach($item AS &$arItem):
				$classes = array("btn", "categories__list-item");
				if ($arItem["PARAMS"]["HIGHLIGHT"])
					$classes[] = "btn--active";
				?>
					<a class="<?=implode(" ", $classes)?>" href="<?=$arItem["LINK"]?>" data-cat="#categories__tab-<?=$arItem["PARAMS"]["ID"]?>" title="<?=$arItem["TEXT"]?>"><?=$arItem["TEXT"]?></a>
			 <?endforeach;?>
		</div>
	<?endif;
	if (!empty($arResult["ITEMS"])){
		foreach ($arResult["ITEMS"] AS $k => &$arItem):?>
			<?php if (!empty($arResult["PROMO"][$arItem["PARAMS"]["ID"]])){?>
				<div class="categories__content<?=(!$k ? " categories__content--active" : "")?>" id="categories__tab-<?=$arItem["PARAMS"]["ID"]?>">
					<div class="popular-column column">
						<p class="is-size-4">Популярные товары в разделе <?=$arItem["TEXT"]?></p>
						<div class="popular">
						<? foreach ($arResult["PROMO"][$arItem["PARAMS"]["ID"] ] AS &$arPromo) { ?>
							<a class="product-promo" href="<?=$arPromo["DETAIL_PAGE_URL"]?>" target="_blank">
								<img src="<?=CFile::ResizeImageGet($arPromo["DETAIL_PICTURE"], array("width" => 70, "height" => 70))["src"]?>" alt="<?=$arPromo["PRODUCT"]["NAME"]?>">
								<div class="info">
									<div class="name"><?=$arPromo["NAME"]?></div>
									<div class="price-block">
										<?/*if ( $arPromo['PRICE']['PRICE'] != $arPromo['PRICE']["DISCOUNT_PRICE"] ):?>
											<div class="old-price"><?=number_format($arPromo["PRODUCT"]["BASE_PRICE"], 0, ".", " ")?> р.</div>
										<?endif;*/?>
										<div class="price"><?=$arPromo['PRICE']['PRICE']?></div>
									</div>
								</div>
							</a>
						<? } ?>
						</div>
					</div>
				</div>
			<? } ?>
			<?$i++;
		endforeach;
	}?>
	</div>
</div>