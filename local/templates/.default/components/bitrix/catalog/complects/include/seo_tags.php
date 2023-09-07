<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
	die();
if (empty($arParams['ITEMS']))
	return "";

//pr($arParams['ITEMS']);

foreach($arParams['ITEMS'] as $arItem)
{
	if($arItem["SELECTED"]=='Y')
	{
		return '';
	}
}
?>
	<div class="seo-catalog-section-list">
		<div class="seo-catalog-section">
			<div class="seo-catalog-section-childs">
				<?foreach($arParams['ITEMS'] as $arItem):?>
					<div class="seo-catalog-section-child <?=$arItem["SELECTED"]=='Y' ? 'fast_link_selected' : ''?>">
						 <div class="seo-image">
							  <a href="<?=$arItem["LINK"]?>">
								   <img src="<?=$arItem["SRC"]?>" width="50" height="50" />
							  </a>
						 </div>
						 <div class="text-cont">
							  <a href="<?=$arItem["LINK"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a>
						 </div>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<?if (!empty($arParams["BACK"])){?><a href="<?=$arParams["BACK"]?>" class="back_tag">Назад</a><?}?>
   </div>