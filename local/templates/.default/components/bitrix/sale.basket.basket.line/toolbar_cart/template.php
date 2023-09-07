<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	$arParams['basket_num'] = $component->getNextNumber();
	$cartId = "bx_basket".$arParams['basket_num'];
	$arParams['cartId'] = $cartId;
?>
<div id="<?=$cartId?>" class="toolbar-bottom__slider bx-basket">
	<?
	$frame = $this->createFrame($cartId, false)->begin();
	$frame->setAnimation(true);
		require(realpath(dirname(__FILE__)).'/ajax_template.php');
	$frame->beginStub();?>
	<?$frame->end();?>
</div>