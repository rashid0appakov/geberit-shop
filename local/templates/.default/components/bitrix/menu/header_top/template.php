<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<div class="main-navigate column is-narrow">
	<?foreach ($arResult as $itemIdex => $arItem):?>
		<a href="<?=$arItem["LINK"]?>" <? if( $arItem['SELECTED'] == 1 ) { ?>class="active"<? } ?> ><?=$arItem["TEXT"]?></a>
	<?endforeach;?>
</div>