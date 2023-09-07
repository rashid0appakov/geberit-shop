<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$cnt = count($arResult["ITEMS"]);
if ($cnt <= 0) return;?>
<div class="promo">
	<?if($cnt < 2):?>
		<?if (!empty($arResult["ITEMS"][0]["PROPERTIES"]["URL"]["VALUE"])):?><a href="<?=$arResult["ITEMS"][0]["PROPERTIES"]["URL"]["VALUE"]?>"><?endif;?><img src="<?=$arResult["ITEMS"][0]["DETAIL_PICTURE"]["SRC"]?>"><?if (!empty($arResult["ITEMS"][0]["PROPERTIES"]["URL"]["VALUE"])):?></a><?endif;?>
	<?else:?>
		<div class="owl-carousel owl-theme goods-list__owl-carousel owl-carousel__progressbar">
			<?foreach ($arResult["ITEMS"] as $arItem):
				$arFile = CFile::ResizeImageGet(
					$arItem["FIELDS"]["DETAIL_PICTURE"]["ID"],
					["width" => 870, "height" => 137],
					BX_RESIZE_IMAGE_EXACT,
					false,
					false,
					false,
					80
				);
				$imgSrc = $arFile["src"];
				?>
				<div class="owl-carousel__item">
					<div class="text">
						<?if (!empty($arItem["PROPERTIES"]["TEXT_BEFORE"]["VALUE"])):?>
							<div class="text-before"><?=$arItem["PROPERTIES"]["TEXT_BEFORE"]["VALUE"]?></div>
						<?endif;?>
						<div class="title"><?=$arItem["NAME"]?></div>
					</div>
					<?if (!empty($arItem["PROPERTIES"]["URL"]["VALUE"])):?><a href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"]?>"><?endif;?><img src="<?=$arFile["src"]?>"><?if (!empty($arItem["PROPERTIES"]["URL"]["VALUE"])):?></a><?endif;?>
				</div>
			<?endforeach;?>
		</div>
	<?endif?>
</div>