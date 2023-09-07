<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
if (empty($arResult["ITEMS"]))
	return FALSE;
?>
<div class="container is-widescreen">
	<h4 class="is-size-5"><?=GetMessage('CT_BLOCK_TITLE')?>:</h4>
	<div class="columns">
		<?foreach ($arResult["ITEMS"] as $arItem):
            if($arItem['NAME']=='FBS'){continue;}
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
			<div class="shop-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<a href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"]?>" target="_blank"><?=htmlspecialchars($arItem["NAME"])?></a>
			</div>
		<?endforeach;?>
	</div>
</div>