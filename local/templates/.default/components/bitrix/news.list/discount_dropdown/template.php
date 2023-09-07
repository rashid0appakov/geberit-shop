<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<?if(count($arResult["ITEMS"])):?>
<div class="sales column is-narrow">
	<div class="all-sales-popup">
		<?foreach ($arResult["ITEMS"] as $arItem):?>
			<div class="line"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
		<?endforeach;?>
	</div>
	<label for="open-all-sales" class="navbar-link navbar-link-sales">
		<span>%</span>
		<span>Все акции</span>
	</label>
</div>
<?endif?>