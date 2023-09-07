<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	/** @var array $arParams */
	/** @var array $arResult */
	/** @global CMain $APPLICATION */
	/** @global CUser $USER */
	/** @global CDatabase $DB */
	/** @var CBitrixComponentTemplate $this */
	/** @var string $templateName */
	/** @var string $templateFile */
	/** @var string $templateFolder */
	/** @var string $componentPath */
	/** @var CBitrixComponent $component */
	$this->setFrameMode(true);

	if (empty($arResult['PROPERTIES']['DESCRIPTION']['VALUE']))
		return "";
?>
<div class="content">
	<div class="payment-block">
		<div class="payment-block__items">
		<? foreach($arResult['PROPERTIES']['DESCRIPTION']['~VALUE'] AS $k => $arItem):?>
		<div class="payment-block__item">
			<div class="payment-block__item-content">
				<? if ($arResult['PROPERTIES']['DESCRIPTION']['DESCRIPTION'][$k]):?>
				<h5 class="payment-block__item-title"><?=$arResult['PROPERTIES']['DESCRIPTION']['DESCRIPTION'][$k]?></h5>
				<? endif;?>
				<div class="payment-block__item-text"><?=$arItem['TEXT']?></div>
			</div>
		</div>
		<? endforeach;?>
		</div>
	</div>
</div>