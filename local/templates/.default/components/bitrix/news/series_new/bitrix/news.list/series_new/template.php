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
<?php if (!empty($arResult['LETTERS'])) { ?>
    <div class="brands__head">
        <? foreach ($arResult['LETTERS'] as $sLetter): ?>
            <a <?= ($_GET['letter'] == $sLetter) ? 'class="active"' : ''; ?>
                    href="?letter=<?= $sLetter ?>"><?= $sLetter ?></a>
        <? endforeach; ?>
        <?if(isset($_GET['letter'])&&strlen($_GET['letter'])>0){?>
        <button onclick="location.href = '/series/';return false;" style="width: 125px;">Все коллекции</button>
        <?}?>
    </div>
<?php } ?>

	<div class="brand-items">
		<div class="card-cell--all">
		<?
		$isAjax = $arParams["IS_AJAX"] == "Y";

		$uniqueId = $this->randString();
		$navContainerId = "nav_container_$uniqueId";

		$jsParams = array(
			"navElementSelector" => ".card-cell__show-more",
		);
?>
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
					
				</div>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="brand-item-link" title="<?=$arItem["NAME"]?>"></a>
			</div>
		<? endforeach;?>
	</div>
	</div>
	<? if ($arParams["DISPLAY_BOTTOM_PAGER"])
			print $arResult["NAV_STRING"];
	?>