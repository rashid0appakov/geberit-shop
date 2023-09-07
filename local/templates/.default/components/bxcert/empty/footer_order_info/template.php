<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();
$countId = "count_$uniqueId";
$priceId = "price_$uniqueId";

$jsParams = array(
	"count_selector" => "#$countId",
	"price_selector" => "#$priceId",
);
?>
<div class="toolbar-bottom__basket">
	<p><span id="<?=$countId?>">0</span> шт. на сумму: <span id="<?=$priceId?>" class="toolbar-bottom__basket-price">0 <i class="znakrub">c</i></span></p>
</div>
<script>
	new JSFooterOrderInfo(<?=json_encode($jsParams)?>);
</script>