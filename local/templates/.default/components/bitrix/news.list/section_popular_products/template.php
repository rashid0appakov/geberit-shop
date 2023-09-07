
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<div class="popular-column column">
	<p class="is-size-4">Популярные товары в разделе <?=$arParams["SECTION_NAME"]?></p>
	<div class="popular">
		<?foreach ($arResult["ITEMS"] as $arItem):?>
			<a href="#"><img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>"></a>
		<?endforeach;?>
	</div>
</div>