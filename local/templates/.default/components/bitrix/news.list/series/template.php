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
<? if (!count($arResult["ITEMS"])){
	print "<p>".GetMessage('CT_EMPTY_LIST')."</p>";
	return;
}?>
	<div class="brand-items">
		<?foreach($arResult["ITEMS"] AS $k => &$arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="brand-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="brand-item-photo">
					<? if (!empty($arItem["RESIZED"])):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
						<img src="<?=$arItem["RESIZED"]["src"]?>" alt="<?=$arItem["NAME"]?>" />
					</a>
					<? endif;?>
				</div>
				<div class="brand-item-title-wrapper">
					<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a></h2>
					<? if ($arItem['SECTIONS']):?>
					<div class="brand-item-blocks">
						<h3><?=GetMessage('CT_SECTIONS')?></h3>
						<ul class="brand-sections-list">
						<?php
							$k = 0;
						foreach($arItem['SECTIONS'] AS &$arSection):
							$use_ser = false;
							foreach(CIBlockSectionPropertyLink::GetArray(CATALOG_IBLOCK_ID, $arSection["ID"]) as $PID => $arLink)  {
							    if($arLink["SMART_FILTER"] !== "Y")
							            continue;
							    if($arLink["PROPERTY_ID"]==5760){
									$use_ser = true;
							    }
							}
							if(!$use_ser) continue;
							if ($k == 3) :?>
						</ul>
						<ul class="brand-sections-list hidden-items">
							<? endif;?>
							<li><a href="<?=$arSection['SECTION_PAGE_URL'].$arItem['CODE']?>/"><?=$arSection['NAME']?></a> <?=$arSection['ITEMS_COUNT']?></li>
						<?php
							$k++;
						endforeach;?>
						</ul>
						<? if (count($arItem["SECTIONS"]) > 3):?>
						<a href="#" class="show-more-items"><?=GetMessage('CT_SHOW_MORE_ITEMS')?></a>
						<? endif;?>
					</div>
					<? endif;?>
				</div>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="brand-item-link" title="<?=$arItem["NAME"]?>"></a>
			</div>
		<? endforeach;?>
	</div>
	<? if ($arParams["DISPLAY_BOTTOM_PAGER"])
			print $arResult["NAV_STRING"];
	?>