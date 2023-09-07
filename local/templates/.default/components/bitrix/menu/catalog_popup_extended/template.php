<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
	$this->setFrameMode(true);
	if (empty($arResult["ITEMS"]))
		return "";

	$arResult["ITEMS_CHUNK"] = array_chunk($arResult["ITEMS"], 70);
?>
<div class="catalog-menu-popup hero">
	<div class="container is-widescreen">
		<div class="columns">
		<?php
		if (!empty($arResult["ITEMS_CHUNK"])):?>
			<div class="column categories categories__list">
			<? foreach($arResult["ITEMS_CHUNK"] AS &$item)
				foreach($item AS &$arItem):
					$classes = array("btn", "categories__list-item");
					if ($arItem["PARAMS"]["HIGHLIGHT"])
						$classes[] = "categories__list-item--active";
				?>
					<a class="<?=implode(" ", $classes)?>" href="<?=$arItem["LINK"]?>" data-cat="#categories__tab-<?=$arItem["PARAMS"]["ID"]?>" title="<?=$arItem["TEXT"]?>"><?=$arItem["TEXT"]?></a>
				<?endforeach;?>
			</div>
		<?endif;

		if (!empty($arResult["ITEMS"])){
			foreach ($arResult["ITEMS"] AS $k => &$arItem):
				if (empty($arItem["ITEMS"]) && empty($arResult["PROMO"][$arItem["PARAMS"]["ID"]]))
					continue;
			   ?>
				<div class="categories__content<?=(!$k ? " categories__content--active" : "")?>" id="categories__tab-<?=$arItem["PARAMS"]["ID"]?>">
					<? if (!empty($arItem["ITEMS"])){?>
					<div class="column subcategories">
						<?foreach ($arItem["ITEMS"] AS &$arItem2):?>
							<a href="<?=$arItem2["LINK"]?>" class="is-size-4"><?=$arItem2["TEXT"]?></a>
							<?foreach ($arItem2["ITEMS"] AS &$arItem3):?>
								<a href="<?=$arItem3["LINK"]?>">- <?=$arItem3["TEXT"]?></a>
							<?endforeach;?>
						<? endforeach;?>
					</div>
					<?}?>
			<?php
				if (!empty($arResult["PROMO"][$arItem["PARAMS"]["ID"]])){?>
					<div class="popular-column column">
						<p class="is-size-4"><?=GetMessage('PM_POPULAR_TITLE')?> <?=$arItem["TEXT"]?></p>
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
			<? } ?>
				</div>
			<?endforeach;
		}?>
		</div>
	</div>
</div>