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
	//pr($arResult);
?>
<? if (!count($arResult["ITEMS"])){
	print "<p>".GetMessage('CT_EMPTY_LIST')."</p>";
	return;
}?>
	<?if(count($arResult['LETTERS'])):?>
		<div id="brand-letters">
			<ul>
				<?foreach($arResult['LETTERS'] as $letter=>$cnt):?>
					<li><a href="#" data-letter="<?=$letter?>" title="<?=$cnt?> брендов на эту букву"><?=ToUpper($letter);?></a></li>
				<?endforeach?>
				<li><a href="#" data-letter="-" title="Будут выведены все бренды" class="active">Все бренды</a></li>
			</ul>
			<div class="clearfix"></div>
		</div>
	<?endif?>
	<div class="brand-items">
		<? 
		$a = 0;
		foreach($arResult["ITEMS"] AS $k => &$arItem):
			if ($a == 10){?>
				</div>
				<div class="brand-items hidden-brands">
				<? 
			}
		
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="brand-item brand-letters brand-letter-<?=$arItem['LETTER']?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="brand-item-photo">
					<? if (!empty($arItem["RESIZED"])):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
						<img src="<?=$arItem["RESIZED"]["src"]?>" alt="<?=$arItem["NAME"]?>" />
					</a>
					<? endif;?>
				</div>
				<div class="brand-item-title-wrapper">
					<h2><?=$arItem["NAME"]?><? if ($arItem['PROPERTIES']['COUNTRY']['VALUE']):?> <span>/ <?=$arItem['DISPLAY_PROPERTIES']['COUNTRY']['DISPLAY_VALUE']?></span><?endif;?></h2>
					<? if ($arItem['SECTIONS']):?>
						<div class="brand-item-blocks">
							<h3><?=GetMessage('CT_SECTIONS')?></h3>
							<ul class="brand-sections-list">
								<?php
								$k = 0;
								foreach($arItem['SECTIONS'] AS &$arSection):
								if ($k == 3) :?>
									</ul>
									<ul class="brand-sections-list hidden-items">
								<? endif;?>
								<li><a href="<?=$arSection['SECTION_PAGE_URL'].'manufacturer-is-'.$arItem['CODE']?>/"><?=$arSection['NAME']?></a> <?=$arSection['ITEMS_COUNT']?></li>
								<?php
								$k++;
							endforeach;?>
							</ul>
							<? if (count($arItem["SECTIONS"]) > 3):?>
								<a href="#" class="show-more-items"><?=GetMessage('CT_SHOW_MORE_ITEMS')?></a>
							<? endif;?>
						</div>
					<? endif;?>

					<? if ($arItem['SERIES']):?>
					<div class="brand-item-blocks">
						<h3><?=GetMessage('CT_SERIES')?></h3>
						<ul class="brand-sections-list">
							<?php
							$k = 0;
							foreach($arItem['SERIES'] AS &$arSeria):
								if ($k == 3) :?>
									</ul>
									<ul class="brand-sections-list hidden-items">
								<? endif;?>
								<li><a href="<?=$arSeria['DETAIL_PAGE_URL']?>"><?=$arSeria['NAME']?></a></li>
								<?php
								$k++;
							endforeach;?>
						</ul>
						<? if (count($arItem["SERIES"]) > 3):?>
						<a href="#" class="show-more-items"><?=GetMessage('CT_SHOW_MORE_ITEMS')?></a>
						<? endif;?>
					</div>
					<? endif;?>
				</div>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="brand-item-link" title="<?=$arItem["NAME"]?>"></a>
			</div>
			<? 
			$a++;
		endforeach;?>
	</div>
	<? if (count($arResult["ITEMS"]) > 3){?>
		<a href="#" class="show-more-brands"><?=GetMessage('CT_SHOW_MORE_ITEMS')?></a>
	<? }?>
	<? if (false and $arParams["DISPLAY_BOTTOM_PAGER"])
			print $arResult["NAV_STRING"];
	?>