<script type="text/javascript">
var products_tm = [], products_ecomers = [];	
</script>
<?
$i = 0;
foreach($arParams['RESULT']['BASKET'] as $arItem){
	?>
	<script type="text/javascript">
	products_tm[<?=$i?>] = <?=$arItem['PRODUCT_ID']?>;
	
	products_ecomers[<?=$i?>] = {
		'name': '<?=htmlspecialchars($arItem['NAME'], ENT_QUOTES)?>',
		'id': '<?=$arItem['PRODUCT_ID']?>',
		'price': '<?=$arItem['PRICE']?>',
		'quantity': '<?=$arItem['QUANTITY']?>'
	};
	</script>
	<?
	$i++;
}
?>
<script type="text/javascript">
window.dataLayer = window.dataLayer || [];
dataLayer.push({
 'ecommerce': {
   'currencyCode': 'RUB',	// Обязательно
   'purchase': {
	 'actionField': {
	   'id': '<?=$arParams['RESULT']['ID']?>',	// уникальный идентификатор транзакции (обязательно)
	   'revenue': '<?=$arParams['RESULT']['PRICE']?>',	// полная сумма транзакции, включая стоимость доставки и налог (обязательно)
	 },
	 'products': products_ecomers
   }
 },
 'goods_id': products_tm,	//массив id купленных товаров (обязательно)
 'goods_price': '<?=$arParams['RESULT']['PRICE']?>',	// полная сумма транзакции (обязательно)
 'event': 'pixel-mg-event',	// Обязательно
 'pixel-mg-event-category': 'Enhanced Ecommerce',	// Обязательно
 'pixel-mg-event-action': 'Purchase',	// Обязательно
 'pixel-mg-event-non-interaction': 'False',	// Обязательно

});
</script>	