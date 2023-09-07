<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (count($arResult["ITEMS"]) <= 0)
    return;

$uniqueId = uniqid("slider");
$jsParams = array(
	"carouselId" => $uniqueId,
);
?>
<div id="promo" class="promo hero">
	<div class="container">
		<div id="<?=$uniqueId?>" class="owl-carousel owl-theme">
			<?foreach ($arResult["ITEMS"] AS &$arItem):
                $arFile = Pict::getResizeWebpSrc($arItem["FIELDS"]["DETAIL_PICTURE"]["ID"], '1170','420');
                $arSrcSet998 = Pict::getResizeWebpSrc($arItem["FIELDS"]["DETAIL_PICTURE"]["ID"], '998','360');
                $arSrcSet480 = Pict::getResizeWebpSrc($arItem["FIELDS"]["DETAIL_PICTURE"]["ID"], '480','174');
				$imgSrc = $arFile;
				$title = $arItem["FIELDS"]["NAME"];
				$textBefore = $arItem["PROPERTIES"]["TEXT_BEFORE"]["VALUE"];
				$textAfter = $arItem["PROPERTIES"]["TEXT_AFTER"]["VALUE"];
				$link = $arItem["PROPERTIES"]["LINK"]["VALUE"];
				$newTab = !!$arItem["PROPERTIES"]["NEW_TAB"]["VALUE"];
				?>
				<div class="owl-carousel__item">
					<?if (!!$link):?><a href="<?=$link?>"<?=$newTab ? ' target="_blank"' : ''?>><?endif;?>
					<div class="text">
						<?if (!!$textBefore):?>
							<div class="text-before"><?=$textBefore?></div>
						<?endif;?>
						<?if (!!$textAfter):?>
							<div class="text-after"><?=$textAfter?></div>
						<?endif;?>
					</div>
					<img srcset="<?=$imgSrc?> 1770w, <?=$arSrcSet998?> 998w, <?=$arSrcSet480?> 750w" alt="<?=$arItem['NAME']?>" />
					<?if (!!$link):?></a><?endif;?>
				</div>
			<?endforeach;?>
		</div>
	</div>
	<br>
	<div class="container level">
		<h1 class="is-size-3" style="width:100%;text-align:center">Официальный дилер Geberit в России</h1>
	</div>	
</div>
<script>
	var mainSlider = new JSMainSlider(<?=json_encode($jsParams)?>);
</script>