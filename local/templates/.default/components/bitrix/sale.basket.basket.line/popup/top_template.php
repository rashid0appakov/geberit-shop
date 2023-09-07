<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<div class="yourCart">
	<div class="yourCartTitle">
		<span><?=GetMessage('TSB1_YOUR_CART')?></span>
	</div>
	<div class="columns is-mobile">
		<? if ($arParams['SHOW_TOTAL_PRICE'] == 'Y' || $arResult['NUM_PRODUCTS'] > 0):?>
		<div class="column is-5-desktop is-6-mobile">
			<div class="totalPriceTitle">
				<? if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'){?>
				<span><?=GetMessage('TSB1_TOTAL_PRICE')?>: </span><?}?>
				<? if ($arResult['NUM_PRODUCTS'] > 0){?>
				<span><?=GetMessage('TSB_COUNT')?>:</span><?}?>
			</div>
		</div>
		<div class="column is-7-desktop is-6-mobile">
			<div class="totalPriceNum">
				<? if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'){?>
				<span><?=customFormatPrice($arResult['TOTAL_PRICE'])?></span><?}?>
				<? if ($arResult['NUM_PRODUCTS'] > 0){?>
				<span><?=$arResult['NUM_PRODUCTS'];?> <?=GetMessage('TSB_QTY')?></span>
				<?}?>
			</div>
		</div>
		<? endif;?>
	</div>
	<div class="columns">
		<div class="column is-6">
			<a href="<?=$arParams['PATH_TO_BASKET']?>" class="btn is-primary btnPlaceOrder">
				<span><?=GetMessage('TSB1_2ORDER')?></span>
			</a>
		</div>
		<div class="column is-6">
			<a href="#" class="btn is-primary is-outlined btnContinueShopping close96" onclick="return SC.closePopup();"><?=GetMessage('TSB_CONTINUE')?></a>
		</div>
	</div>
</div>