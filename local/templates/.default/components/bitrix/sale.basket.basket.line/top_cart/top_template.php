<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
	<?if ($arParams['SHOW_AUTHOR'] == 'Y'):?>
		<div class="bx-basket-block">
			<i class="fa fa-user"></i>
			<?if ($USER->IsAuthorized()):
				$name = trim($USER->GetFullName());
				if (! $name)
					$name = trim($USER->GetLogin());
				if (strlen($name) > 15)
					$name = substr($name, 0, 12).'...';
				?>
				<a href="<?=$arParams['PATH_TO_PROFILE']?>"><?=htmlspecialcharsbx($name)?></a>
				&nbsp;
				<a href="?logout=yes"><?=GetMessage('TSB1_LOGOUT')?></a>
			<?else:?>
				<a href="<?=$arParams['PATH_TO_REGISTER']?>?login=yes"><?=GetMessage('TSB1_LOGIN')?></a>
				&nbsp;
				<a href="<?=$arParams['PATH_TO_REGISTER']?>?register=yes"><?=GetMessage('TSB1_REGISTER')?></a>
			<?endif?>
		</div>
	<?endif?>

<?// if ($arResult['NUM_PRODUCTS'] > 0):?>
	<a href="<?=$arParams['PATH_TO_BASKET']?>" class="btn is-primary">
<?/* else:?>
	<div class="btn is-primary is-desabled">
<? endif;*/?>
		<span class="label-desktop"><?=GetMessage('TSB1_2ORDER1')?></span>
	<?if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'):?>
		<?if ($arResult['NUM_PRODUCTS'] > 0):?>
			<?=GetMessage("TSB_TEXT");?><?//=$arResult['TOTAL_PRICE']?>
		<?endif?>
	<?endif?>
	<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
		<span class="tag is-warning" id="count_<?=$arParams['basket_num']?>"><?=$arResult['NUM_PRODUCTS']?></span>
	<?endif?>
<?/* if ($arResult['NUM_PRODUCTS'] > 0):*/?></a><?/* else:?></div><? endif;*/?>
	<?if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
		<br>
		<span class="icon_info"></span>
		<a href="<?=$arParams['PATH_TO_PERSONAL']?>"><?=GetMessage('TSB1_PERSONAL')?></a>
	<?endif?>
	<? if (!empty($arResult["CATEGORIES"])):
		foreach ($arResult["CATEGORIES"] AS $category => $items):
			if (empty($items) || $category != "READY")
				continue;
	?>
		<div id="basket_order" class="hide">
			<? foreach($items AS $arItem):?>
			<div class="order_item" data-pid="<?=$arItem["PRODUCT_ID"]?>" data-price="<?=$arItem["PRICE"]?>" data-qty="<?=$arItem["QUANTITY"]?>"></div>
			<? endforeach;?>
		<? endforeach;?>
		</div>
	<?endif;?>