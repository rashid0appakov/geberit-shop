<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams["AUTO_OPEN"] = $arParams["AUTO_OPEN"] == "Y" ? "Y" : "N";

$uniqueId = uniqid();

$popupId = "popup_$uniqueId";
$containerId = "container_$uniqueId";

$jsParams = array(
	"ajaxUrl" => $templateFolder."/ajax.php",
	"autoOpen" => $arParams["AUTO_OPEN"] == "Y",
	"popupSelector" => "#$popupId",
	"containerSelector" => "#$containerId",
);?>
<div id="<?=$popupId?>" class="popup69 popupAddCart" style="display: none">
	<div id="<?=$containerId?>" class="wrapperContentPopup"></div>
</div>
<script>
	window.BasketPopup = new JSBasketPopup(<?=json_encode($jsParams)?>);
</script>