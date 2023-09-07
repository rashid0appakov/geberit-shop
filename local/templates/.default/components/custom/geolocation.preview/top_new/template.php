<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$city   = $arResult["GEOLOCATION_INFO"]["CITY_NAME"] ?: "Не определено";
$region = $arResult["GEOLOCATION_INFO"]["REGION_NAME"] ?: "Не определено";


$uniqueId = $this->randString();

$params = base64_encode(serialize($arParams));
$confirmElementId = "confirm_$uniqueId";
$openRegionCBId = "open_change_region_$uniqueId";
$closeRegionCBId = "close_change_region_$uniqueId";
$ajaxUrl = $this->__component->GetPath()."/ajax.php";


$jsParams = array(
	"params" => $params,
	"ajaxUrl" => $ajaxUrl,
	"locationId" => $arResult["GEOLOCATION_INFO"]["ID"],
	"needOpen" => !$arResult["GEOLOCATION_ID"],
	"confirmElementSelector" => "#$confirmElementId",
	"openRegionCBSelector" => "#$openRegionCBId",
	"closeRegionCBSelector" => "#$closeRegionCBId",
	"selectedLocationID" => "#location_id",
);?>



<div class="current-region navbar-item">
	<label for="<?=$openRegionCBId?>" class="navbar-link">
		<style>
			  .icon-pointer{  max-height: 1.75rem;
			  	    width: 12px;
			  	        height: 14px;
    				margin-right: 8px;
    				background-image: url(<?=$templateFolder?>/images/pointer.svg);
    				display: inline-block;
			  }
			</style>
			<div class="icon-pointer"></div>
		<span class="value" id="ajax-input-city" data-id="<?=$arResult["GEOLOCATION_INFO"]["ID"]?>">
	<?php
		$frame = $this->createFrame()->begin('...');
		$frame->setAnimation(true);
	?><?=$city?>
	<? $frame->end();?>
		</span>
	</label>
	<input id="<?=$openRegionCBId?>" name="popup-change-region" type="radio" class="system" />
	<div class="popup">
		<div class="arrow"></div>
		<div class="bg"></div>
		<div class="popup-region-name">Ваш регион <span id="ajax-input-region">
	<?php
		$frame = $this->createFrame()->begin('...');
		$frame->setAnimation(true);
	?><?=$region?>
	<? $frame->end();?></span>?</div>
		<label for="open-modal-region">
			<span class="btn is-primary">Изменить</span>
		</label>
		<label for="<?=$closeRegionCBId?>">
			<span id="<?=$confirmElementId?>" class="btn is-primary is-outlined is-pulled-right">Да</span>
		</label>
		<input id="<?=$closeRegionCBId?>" name="popup-change-region" type="radio" class="system">
	</div>
</div>
<?$frame = $this->createFrame()->begin();?>
<input type="hidden" name="location_id" id="location_id" value="<?=$arResult["GEOLOCATION_ID"]?>" />
<script type="text/javascript">
	new JSGeolocationSelectPopup(<?=json_encode($jsParams)?>);
</script>
<? $frame->end();?>