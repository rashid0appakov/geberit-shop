<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<div class="sidebar-menu">
	<ul class="sidebar-menu__list">
		<?
		$previousLevel = 0;
		foreach($arResult as $arItem):
		?>
			<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
				<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
			<?endif?>

			<?if ($arItem["IS_PARENT"]):?>
				<li class="sidebar-menu__item is-parent <?if($arItem["SELECTED"] === true || $arItem["CHILD_SELECTED"]):?>sidebar-menu__item--active<?endif?><?if($arItem["CHILD_SELECTED"] !== true):?> sidebar-menu__close<?endif?>">
					<div class="sidebar-menu__item-title">
						<a href="<?=$arItem["LINK"]?>" class="sidebar-menu__item-link"><?=$arItem["TEXT"]?></a>
					</div>
					<ul class="sidebar-menu__childs">
			<?else:?>
				<?if ($arItem["PERMISSION"] > "D"):?>
					<li class="sidebar-menu__item <?if($arItem["SELECTED"] === true):?>sidebar-menu__item--active<?endif?>">
						<div class="sidebar-menu__item-title"><a href="<?=$arItem["LINK"]?>" class="sidebar-menu__item-link"><?=$arItem["TEXT"]?></a></div>
					</li>
				<?endif?>
			<?endif?>
			<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
		<?endforeach?>

		<?if ($previousLevel > 1)://close last item tags?>
			<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
		<?endif?>
	</ul>
</div>
<?endif?>