<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<div class="menu">
	<ul>
		<li class="has_subMenu" ><a href="javascript:void(0);" id="has_subMenu-link">Наши магазины <div></div></a></li>
		<?foreach ($arResult as $itemIdex => $arItem):?>
			<li><a href="<?=$arItem["LINK"]?>" <? if( $arItem['SELECTED'] == 1 ) { ?>class="active"<? } ?> ><?=$arItem["TEXT"]?></a></li>
		<?endforeach;?>
	</ul>
</div>
