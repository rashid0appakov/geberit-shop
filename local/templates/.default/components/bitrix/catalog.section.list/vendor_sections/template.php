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

	if (empty($arResult["SECTIONS"]))
		return;?>
	<div class="filter__item">
		<div class="filter__title--toggle">
			<div aria-expanded="true" aria-controls="accordion1" class="filter__title accordion-title accordionTitle js-accordionTrigger is-expanded"><?=GetMessage('CT_SELECT_SECTION')?></div>
		</div>

		<div class="filter__content accordion-content accordionItem is-expanded" id="accordion1" aria-hidden="false">
            <style>.filter__content-item_span a~span{color:rgb(135,135,135);font-size:13px;line-height:13px;}</style>
			<ul class="filter__content-list">
			<?foreach($arResult["SECTIONS"] AS $k => &$arItem):
				if ($k == 3) :?>
			</ul>
			<ul class="filter__content-list hidden-items">
				<? endif;?>
				<li class="filter__content-item filter__content-item_span filter__content-item--toggle">
					<a href="<?=$arItem['SECTION_PAGE_URL']?>">
						<span><?=$arItem['NAME']?></span><span/>
					</a>
                    <span><?=$arItem['ELEMENT_CNT']?></span>
				</li>
			<? endforeach; ?>
			</ul>
			<? if (count($arResult["SECTIONS"]) > 3):?>
			<a href="#" class="show-more-items"><?=GetMessage('CT_SHOW_MORE_ITEMS')?></a>
			<? endif;?>
		</div>
	</div>