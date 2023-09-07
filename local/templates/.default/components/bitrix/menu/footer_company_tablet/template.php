<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<h4 class="is-size-5">Компания</h4>
<?foreach ($arResult as $itemIndex => $arItem):
	if ($arItem["PARAMS"]["SHOW_TABLET"] == "Y"):?>
		<a class="column is-size-5" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
	<?endif;
endforeach;?>