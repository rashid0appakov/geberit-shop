<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();
$buttonId = "button_$uniqueId";
$countId = "count_$uniqueId";

$jsParams = array(
	"buttonSelector" => "#$buttonId",
	"countSelector" => "#$countId",
);?>
<a href="<?=$arParams["PATH_TO_BASKET"]?>" id="<?=$buttonId?>" class="toolbar-bottom__button">
	<span class="toolbar-bottom__button-title">Оформить заказ</span>
	<span id="<?=$countId?>" class="tag is-warning">0</span>
</a>
<script type="text/javascript" charset="UTF-8">
	new JSOrderButtonBottom(<?=json_encode($jsParams)?>);
</script>