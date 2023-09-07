<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();
$buttonId = "button_$uniqueId";
$countId = "count_$uniqueId";

$jsParams = array(
	"buttonSelector" => "#$buttonId",
	"countSelector" => "#$countId",
);?>
<a href="<?=$arParams["PATH_TO_BASKET"]?>" id="<?=$buttonId?>" class="btn is-primary">
	<span class="label-desktop">Оформить заказ</span>
	<span id="<?=$countId?>" class="tag is-warning">
		<?=getCountBasket();?>
	</span>
</a>
<script>
	new JSOrderButton(<?=json_encode($jsParams)?>);
</script>