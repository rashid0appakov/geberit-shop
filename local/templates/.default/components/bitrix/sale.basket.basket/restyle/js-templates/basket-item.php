<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

$restoreColSpan = 2 + $usePriceInAdditionalColumn + $useSumColumn + $useActionColumn;

$positionClassMap = array(
	'left' => 'basket-item-label-left',
	'center' => 'basket-item-label-center',
	'right' => 'basket-item-label-right',
	'bottom' => 'basket-item-label-bottom',
	'middle' => 'basket-item-label-middle',
	'top' => 'basket-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}
?>
<script id="basket-item-template" type="text/html">
	<div class="columns trCart" id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#SHOW_RESTORE}}
			<td class="basket-items-list-item-notification" colspan="<?=$restoreColSpan?>">
				<div class="basket-items-list-item-notification-inner basket-items-list-item-notification-removed" id="basket-item-height-aligner-{{ID}}">
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
					<div class="basket-items-list-item-removed-container">
						<div>
							<?=Loc::getMessage('SBB_GOOD_CAP')?> <strong>{{NAME}}</strong> <?=Loc::getMessage('SBB_BASKET_ITEM_DELETED')?>.
						</div>
						<div class="basket-items-list-item-removed-block">
							<a href="javascript:void(0)" data-entity="basket-item-restore-button">
								<?=Loc::getMessage('SBB_BASKET_ITEM_RESTORE')?>
							</a>
							<span class="basket-items-list-item-clear-btn" data-entity="basket-item-close-restore-button"></span>
						</div>
					</div>
				</div>
			</td>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
		<div class="column is-2 is-3-mobile tdCart">
			<?php if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST'])): ?>
			<div>
				{{#DETAIL_PAGE_URL}}
					<a href="{{DETAIL_PAGE_URL}}" class="basket-item-image-link">
				{{/DETAIL_PAGE_URL}}
				<img class="basket-item-image" alt="{{NAME}}"
					 src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}">
					{{#DETAIL_PAGE_URL}}
					</a>
					{{/DETAIL_PAGE_URL}}
			</div>
			<?php endif; ?>
			
		</div>
		<div class="column is-4 is-8-mobile tdCart">
			<div class="titleItem">
				{{#DETAIL_PAGE_URL}}
				<a href="{{DETAIL_PAGE_URL}}">
				{{/DETAIL_PAGE_URL}}
					{{NAME}}
				{{#DETAIL_PAGE_URL}}
				</a>
				{{/DETAIL_PAGE_URL}}
				<div>
					<div class="filter__checkbox">
						<input id="install-item-1" type="checkbox">
						<label for="install-item-1">Интресует установка этого товара (от 2 500
							руб.)</label>
					</div>
				</div>
			</div>
		</div>
		<div class="column is-2 is-7-mobile tdCart">
			<div class="tdTitleMobile margin-left-45">
				ЦЕНА
			</div>
			
			{{#SHOW_DISCOUNT_PRICE}}
				<div class="basket-item-price-old">
					<span class="basket-item-price-old-text">
						{{{FULL_PRICE_FORMATED}}}
					</span>
				</div>
			{{/SHOW_DISCOUNT_PRICE}}
			<div class="priceItem">
				<span id="basket-item-price-{{ID}}">
					{{{PRICE_FORMATED}}}
				</span>
			</div>
		</div>
		<div class="column is-2 is-4-mobile tdCart">
			<div class="tdTitleMobile">
				КОЛИЧЕСТВО
			</div>
			<div class="cart__content-counter">
				<div class="cart__counter{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}"
					data-entity="basket-item-quantity-block">
					<button class="cart__counter-minus">-</button>
					<div class="cart__counter-counter"
							data-value="{{QUANTITY}}">{{QUANTITY}}</div>
					<button class="cart__counter-plus">+</button>
					<input type="hidden" value="{{QUANTITY}}" data-value="{{QUANTITY}}"
							id="basket-item-quantity-{{ID}}">
				</div>
			</div>
		</div>
		<div class="column is-1 is-7-mobile tdCart">
			<div class="tdTitleMobile margin-left-45">
				СУММА
			</div>
			{{#SHOW_DISCOUNT_PRICE}}
				<div class="basket-item-price-old">
					<span class="basket-item-price-old-text" id="basket-item-sum-price-old-{{ID}}">
						{{{SUM_FULL_PRICE_FORMATED}}}
					</span>
				</div>
			{{/SHOW_DISCOUNT_PRICE}}
			<div class="totalPriceItem">
				<span id="basket-item-sum-price-{{ID}}">
					{{{SUM_PRICE_FORMATED}}}
				</span>
			</div>
			{{#SHOW_DISCOUNT_PRICE}}
				<div class="basket-item-price-difference">
					<?=Loc::getMessage('SBB_BASKET_ITEM_ECONOMY')?>
					<span id="basket-item-sum-price-difference-{{ID}}" style="white-space: nowrap;">
						{{{SUM_DISCOUNT_PRICE_FORMATED}}}
					</span>
				</div>
			{{/SHOW_DISCOUNT_PRICE}}
		</div>
		<div class="column is- is-4-mobile tdCart basket-items-list-item-remove">
			<div class="deleteItemCart basket-item-block-action">
				<a href="javascript:void(0);" class="basket-item-action-remove">
					<img src="<?= $templateFolder ?>/images/closeIconBig.png" alt="">
					<span class="basket-item-actions-remove">Удалить</span>
				</a>
			</div>
		</div>
	{{/SHOW_RESTORE}}   
	</div>
</script>