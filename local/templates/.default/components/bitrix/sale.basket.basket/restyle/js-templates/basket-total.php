<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="columns tFooterCart">
		<div class="column is-8 is-12-mobile tdCart">
			<div class="enterCoupon">
				<label>
					<span>Ввести купон на скидку</span>
					<input type="text" class="couponInput" style="display: none;" data-entity="basket-coupon-input">
				</label>
			</div>
			<div class="himselfButtonBlock forCoupon" style="display: none;">
				<a class="btn is-primary">
					<!-- "is-disabled" class for empty basket -->
					<span class="label-desktop">OK</span>
				</a>
			</div>
		</div>

		<div class="column is-1 is-6-mobile tdCart">
			<span class="subtotal">
				Подытог:
			</span>
		</div>
		<div class="column is-2 is-6-mobile tdCart">
			<span class="subtotal" id="basket-full-total">
				{{{PRICE_FORMATED}}}
			</span>
		</div>
	</div>

	<?/*<div class="basket-checkout-container" data-entity="basket-checkout-aligner">
		<?
		if ($arParams['HIDE_COUPON'] !== 'Y')
		{
			?>
			<div class="basket-coupon-section">
				<div class="basket-coupon-block-field">
					<div class="basket-coupon-block-field-description">
						<?=Loc::getMessage('SBB_COUPON_ENTER')?>:
					</div>
					<div class="form">
						<div class="form-group" style="position: relative;">
							<input type="text" class="form-control" id="" placeholder="">
							<span class="basket-coupon-block-coupon-btn"></span>
						</div>
					</div>
				</div>
			</div>
			<?
		}
		?>
		<div class="basket-checkout-section">
			<div class="basket-checkout-section-inner">
				<div class="basket-checkout-block basket-checkout-block-total">
					<div class="basket-checkout-block-total-inner">
						<div class="basket-checkout-block-total-title"><?=Loc::getMessage('SBB_TOTAL')?>:</div>
						<div class="basket-checkout-block-total-description">
							{{#WEIGHT_FORMATED}}
								<?=Loc::getMessage('SBB_WEIGHT')?>: {{{WEIGHT_FORMATED}}}
								{{#SHOW_VAT}}<br>{{/SHOW_VAT}}
							{{/WEIGHT_FORMATED}}
							{{#SHOW_VAT}}
								<?=Loc::getMessage('SBB_VAT')?>: {{{VAT_SUM_FORMATED}}}
							{{/SHOW_VAT}}
						</div>
					</div>
				</div>

				<div class="basket-checkout-block basket-checkout-block-total-price">
					<div class="basket-checkout-block-total-price-inner">
						{{#DISCOUNT_PRICE_FORMATED}}
							<div class="basket-coupon-block-total-price-old">
								{{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
							</div>
						{{/DISCOUNT_PRICE_FORMATED}}

						<div class="basket-coupon-block-total-price-current" data-entity="basket-total-price">
							{{{PRICE_FORMATED}}}
						</div>

						{{#DISCOUNT_PRICE_FORMATED}}
							<div class="basket-coupon-block-total-price-difference">
								<?=Loc::getMessage('SBB_BASKET_ITEM_ECONOMY')?>
								<span style="white-space: nowrap;">{{{DISCOUNT_PRICE_FORMATED}}}</span>
							</div>
						{{/DISCOUNT_PRICE_FORMATED}}
					</div>
				</div>

				<div class="basket-checkout-block basket-checkout-block-btn">
					<button class="btn btn-lg btn-default basket-btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
						data-entity="basket-checkout-button">
						<?=Loc::getMessage('SBB_ORDER')?>
					</button>
				</div>
			</div>
		</div>

		<?
		if ($arParams['HIDE_COUPON'] !== 'Y')
		{
		?>
			<div class="basket-coupon-alert-section">
				<div class="basket-coupon-alert-inner">
					{{#COUPON_LIST}}
					<div class="basket-coupon-alert text-{{CLASS}}">
						<span class="basket-coupon-text">
							<strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
							{{#DISCOUNT_NAME}}({{{DISCOUNT_NAME}}}){{/DISCOUNT_NAME}}
						</span>
						<span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
							<?=Loc::getMessage('SBB_DELETE')?>
						</span>
					</div>
					{{/COUPON_LIST}}
				</div>
			</div>
			<?
		}
		?>
	</div>*/?>
</script>