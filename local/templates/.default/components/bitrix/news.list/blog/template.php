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
//pr($arResult);
$this->setFrameMode(true);
?>
<div class="blog-list card-cell--all">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$arImg = CFile::ResizeImageGet(
		$arItem['~PREVIEW_PICTURE'],
		array('width' => 582, 'height' => 300),
		BX_RESIZE_IMAGE_EXACT,
		true
	);
	?>
	<div class="blog-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="blog-item-image"><img
				src="<?=$arImg["src"]?>"
				width="<?=$arImg["width"]?>"
				height="<?=$arImg["height"]?>"
				alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
				title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
				/></a>
		<div class="blog-item-info">
			<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="blog-item-title"><?echo $arItem["NAME"]?></a>
			<p class="blog-item-text"><?echo $arItem["PREVIEW_TEXT"];?></p>
			<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="blog-item-more">Читать полностью></a>
		</div>
	</div>
<?endforeach;?>
</div>
<div class="clearfix"></div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
