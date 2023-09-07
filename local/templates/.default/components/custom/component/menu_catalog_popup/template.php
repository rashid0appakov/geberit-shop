<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//pr($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS"]);

$this->setFrameMode(true);

$jsParams = [];
if (empty($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS"]))
{
	return "";
}

?>
<?/*<input id="open-catalog-menu-popup" type="checkbox" class="system">*/?>
<div class="catalog-menu-popup hero simple-type">
	<div class="columns">
	<?if(!empty($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS_CHUNK"])):?>
		<div class="column categories categories__list">
			<?
			foreach($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS_CHUNK"] as &$item)
			{
				foreach($item as &$arItem)
				{
					$classes = array("btn", "categories__list-item");
					if($arItem[3]["HIGHLIGHT"])
						$classes[] = "btn--active";
					?>
					<a class="<?=implode(" ", $classes)?>" href="<?=$arItem[1]?>" data-cat="#categories__tab-<?=$arItem[3]["ID"]?>" title="<?=$arItem[0]?>"><?=$arItem[0]?></a>
					<?
			 	}
			}
			?>
		</div>
	<?endif;
	if (!empty($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS"])){
		foreach ($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["ITEMS"] AS $k => &$arItem):?>
			<?php if (!empty($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["PROMO"][$arItem[3]["ID"]])){?>
				<div class="categories__content<?=(!$k ? " categories__content--active" : "")?>" id="categories__tab-<?=$arItem[3]["ID"]?>">
					<div class="popular-column column">
						<p class="is-size-4">Популярные товары в разделе <?=$arItem[0]?></p>
						<div class="popular">
						<? foreach ($GLOBALS['PAGE_DATA']['MENU']['CATALOG_SECTIONS']["PROMO"][$arItem[3]["ID"] ] AS &$arPromo) { ?>
							<a class="product-promo" href="<?=$arPromo["DETAIL_PAGE_URL"]?>" target="_blank">
								<img src="<?=$arPromo["DETAIL_PICTURE"]["src"]?>" alt="<?=$arPromo["PRODUCT"]["NAME"]?>">
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