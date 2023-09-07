<?	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();
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
?>
<? if (count($arResult["BRANDS"])):?>
	<div class="filter__item">
		<div class="filter__title--toggle is-expanded">
			<div aria-expanded="true" aria-controls="filter_brands" class="filter__title accordion-title accordionTitle js-accordionTrigger1 is-expanded"><?=GetMessage('CT_BRAND_FILTER_TITLE')?></div>
		</div>
		<div class="filter__content filter__content--checkbox accordion-content accordionItem is-expanded" id="filter_brands" aria-hidden="true">
			<form action="" method="post">
		<? foreach($arResult["BRANDS"] AS $k => $arItem):
			if (empty($arResult['S_BRANDS'][$arItem['ID']]))
				continue;
			?>
			<div class="filter__checkbox">
				<input type="checkbox" id="brand_<?=$arItem['ID']?>" name="BRAND[<?=$arItem['ID']?>]" value="Y" data-id="<?=$arItem['ID']?>" <?//=($arItem['SELECTED'] == 'Y' ? 'checked="checked"': '')?>/>
				<label for="brand_<?=$arItem['ID']?>"><?=$arItem['NAME']?></label>
				<span><?=$arResult['S_BRANDS'][$arItem['ID']]?></span>
			</div>
		<? endforeach;?>
			</form>
		</div>
	</div>
<? endif;?>