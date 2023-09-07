<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$cartStyle = ' bx-basket';
$arParams['basket_num'] = $component->getNextNumber();
$cartId = "bx_basket".$arParams['basket_num'];
$arParams['cartId'] = $cartId;

if ($arParams['POSITION_FIXED'] == 'Y')
{
	$cartStyle .= "-fixed {$arParams['POSITION_HORIZONTAL']} {$arParams['POSITION_VERTICAL']}";
	if ($arParams['SHOW_PRODUCTS'] == 'Y')
		$cartStyle .= ' bx-closed';
}
else
{
	$cartStyle .= ' bx-opener';
}
?>
<div id="<?=$cartId?>" class="top_cart<?=$cartStyle?>">
	<?
	$frame = $this->createFrame($cartId, false)->begin();
	$frame->setAnimation(true);
		require(realpath(dirname(__FILE__)).'/ajax_template.php');
	$frame->beginStub();
		//require(realpath(dirname(__FILE__)).'/top_template.php');?>
	<div class="btn is-primary"><span class="label-desktop"><?=GetMessage('TSB1_2ORDER1')?></span></div>
	<?$frame->end();?>
</div><?/*
<script type="text/javascript">
window.onload   = function(){
	var <?=$cartId?> = new BitrixSmallCart;
	<?=$cartId?>.siteId	   = '<?=SITE_ID?>';
	<?=$cartId?>.cartId	   = '<?=$cartId?>';
	<?=$cartId?>.ajaxPath	 = '<?=$componentPath?>/ajax.php';
	<?=$cartId?>.templateName = '<?=$templateName?>';
	<?=$cartId?>.arParams	 =  <?=CUtil::PhpToJSObject ($arParams)?>; // TODO \Bitrix\Main\Web\Json::encode
	<?=$cartId?>.closeMessage = '<?=GetMessage('TSB1_COLLAPSE')?>';
	<?=$cartId?>.openMessage  = '<?=GetMessage('TSB1_EXPAND')?>';
	<?=$cartId?>.activate();
}
</script>*/?>