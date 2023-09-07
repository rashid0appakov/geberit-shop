<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	$arParams['basket_num'] = $component->getNextNumber();
	$cartId = "bx_basket".$arParams['basket_num'];
	$arParams['cartId'] = $cartId;
?>
<div id="<?=$cartId?>" class="bottom_cart bx-basket<?if(!$arResult['NUM_PRODUCTS']):?> cart-is-empty<?endif?>">
	<?
	$frame = $this->createFrame($cartId, false)->begin();
	$frame->setAnimation(true);
		require(realpath(dirname(__FILE__)).'/ajax_template.php');
	$frame->beginStub();?>
	<div class="toolbar-bottom__button">
		<span class="toolbar-bottom__button-title"><?=GetMessage('TSB1_2ORDER')?></span>
	</div>
	<?$frame->end();?>
</div>