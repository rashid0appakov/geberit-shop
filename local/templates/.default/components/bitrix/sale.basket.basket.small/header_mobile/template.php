<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<a href="/personal/cart/" class="btn is-primary">
  <!-- "is-disabled" class for empty basket -->
  <span class="label-desktop">Оформить заказ</span>
  <span class="tag is-warning is-warning-order">
	<?=count($arResult['ITEMS']);?>
  </span>
</a>

