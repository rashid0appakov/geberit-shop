<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();
$buttonId = "button_$uniqueId";
$countId = "count_$uniqueId";

$jsParams = array(
	"buttonSelector" => "#$buttonId",
	"countSelector" => "#$countId",
);?>
<a href="<?=$arParams["PATH_TO_BASKET"]?>" id="<?=$buttonId?>" class="btn is-primary is-disabled">
	<span class="label-desktop">Оформить заказ</span>
	<span id="<?=$countId?>" class="tag is-warning is-warning-order">0</span>
</a>
<script>
	new JSOrderButtonMobile(<?=json_encode($jsParams)?>);
</script>