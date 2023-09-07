<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<?foreach ($arResult as $itemIdex => $arItem):?>
	<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
<?endforeach;?>