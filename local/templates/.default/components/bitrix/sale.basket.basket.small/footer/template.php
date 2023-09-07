<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
?>
<? ob_start();?>
<?php
	if (!empty($arResult["ITEMS"]))
		foreach ($arResult["ITEMS"] AS &$arItem):
			if ($arItem["CAN_BUY"] == 'N')
				continue;
	?>
			<div class="_toolbarItem">
				<div class="toolbar-bottom__slider-info slider-info" data-id="<?=$arItem["ID"]?>">
					<div class="slider-info__content">
						<div class="slider-info__content-img">
							<img src="<?=CFile::ResizeImageGet($arItem["ELEMENT"]["DETAIL_PICTURE"], array("width" => 70, "height" => 70), BX_RESIZE_IMAGE_PROPORTIONAL)['src']?>">
						</div>
						<div class="slider-info__content-info">
							<p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></p>
							<p class="slider-info__content-price"><?=customFormatPrice(CurrencyFormat($arItem["PRICE"], "RUB"))?></p>
						</div>
						<div class="slider-info__content-counter">
							<div class="slider-info__counter">
								<button class="slider-info__counter-minus">-</button>
								<div class="slider-info__counter-counter"><?=$arItem["QUANTITY"]?></div>
								<button class="slider-info__counter-plus">+</button>
							</div>
						</div>
						<p class="slider-info__content-price _sum_price" id="bottom-price-<?=$arItem["PRICE"];?>"><?=customFormatPrice(CurrencyFormat($arItem["PRICE"] * $arItem["QUANTITY"], "RUB"))?></p>
						<div class="slider-info__content-button delete-basket" data-id="<?=$arItem["ID"]?>"></div>
					</div>
					<div class="slider-info__corner"></div>
				</div>
			</div>
		<?endforeach;?>
		<?php
			$tooltipContent = ob_get_clean();
			ob_start();
		?>
<?php
	if (!empty($arResult["ITEMS"]))
		foreach ($arResult["ITEMS"] AS &$arItem):
			if ($arItem["CAN_BUY"] == 'N')
				continue;
?>
		<div class="toolbar-bottom__slider-slide" data-id="<?=$arItem["ID"]?>">
			<img src="<?=CFile::ResizeImageGet($arItem["ELEMENT"]["DETAIL_PICTURE"], array("width" => 35, "height" => 35), BX_RESIZE_IMAGE_PROPORTIONAL)['src']?>">
			<span class="tag is-warning"><?=$arItem["QUANTITY"]?></span>
		</div>
	<?endforeach;?>
<?php
	$toolbarContent = ob_get_clean();
	if ($arParams["IS_AJAX"] == "Y"){
		$tooltipContent = base64_encode(json_encode($tooltipContent));
		$toolbarContent = base64_encode(json_encode($toolbarContent));

		echo json_encode(array(
			"tooltip_content" => $tooltipContent,
			"toolbar_content" => $toolbarContent,
		));
	}else{
		$basketInfo = array(
			"PRODUCT_COUNT" => 0,
			"PRICE" => 0.0,
			"ITEMS" => array(),
		);
		$productCount = 0;
		$basketPrice = 0.0;
		foreach ($arResult["ITEMS"] as $arItem){
			if ($arItem["CAN_BUY"] == 'N')
				continue;

			$basketInfo["ITEMS"][] = array(
				"ID" => $arItem["ID"],
				"PRODUCT_ID" => $arItem["PRODUCT_ID"],
				"PRICE" => $arItem["PRICE"],
				"QUANTITY" => $arItem["QUANTITY"],
			);

			$productCount++;
			$basketPrice += $arItem["PRICE"] * $arItem["QUANTITY"];
		}
		$basketInfo["PRODUCT_COUNT"] = $productCount;
		$basketInfo["PRICE"] = $basketPrice;

		$signer = new \Bitrix\Main\Security\Sign\Signer;
		$signedParams = $signer->sign(base64_encode(serialize($arParams)), "sale.basket.basket.small");

		$uniqueId = uniqid();
		$tooltipContainerId = "tooltip_container_$uniqueId";
		$toolbarId = "toolbar_$uniqueId";

		$jsParams = array(
			"tooltipContainerSelector" => "#$tooltipContainerId",
			"toolbarSelector" => "#$toolbarId",

			"params" => $signedParams,
			"ajaxUrl" => $templateFolder."/ajax.php",

			"basketInfo" => $basketInfo,
		);?>
			<div id="<?=$tooltipContainerId?>" class="toolbar-bottom__slider">
				<?=$tooltipContent?>
				<div id="<?=$toolbarId?>" class="slider-toolbar">
					<?=$toolbarContent?>
				</div>
			</div>
			<?/*
			<script type="text/javascript">
				window.BasketFooter = new JSSaleBasketBasketSmallFooter(<?=json_encode($jsParams)?>);
			</script>
			*/?>
		<?
	}
?>