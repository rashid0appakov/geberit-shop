<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();


$this->setFrameMode(true);

$arItem = $arResult["ITEM"];

$uniqueId = $arResult['uniqueId'];

$basketBtnId = "basketBtn_$uniqueId";

$jsParams = array(
	"basketBtnSelector" => "#$basketBtnId",

	"productInfo" => array(
		"id" => $arItem["ID"],
		"available" => $arItem["PRODUCT"]["AVAILABLE"] == "Y",
	),

	"uniqueId" => $uniqueId,
);?><div class="productInSlaider" id="slaider_<?= $uniqueId ?>">
	<div class="productInSlaiderCompare tooltip is-tooltip-bottom" data-tooltip="Сравнить товары">
		<a href="#">
			<span class="icon-diff icon-diff-productSlaider">
				<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
					<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
					<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
					<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
				</svg>
			</span>
		</a>
	</div>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
		<div class="productInSlaiderImg-1">
			<img src="<?=!!$arItem["DETAIL_PICTURE"]["ID"] ? CFile::ResizeImageGet($arItem["DETAIL_PICTURE"]["ID"], array('width' => 270, 'height' => 250), BX_RESIZE_IMAGE_PROPORTIONAL)["src"] : ""?>">
		</div>
		<div class="productInSlaiderImg-2">
			<img src="<?=!!$arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"][1] ? CFile::ResizeImageGet($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"][1], array('width' => 270, 'height' => 250), BX_RESIZE_IMAGE_PROPORTIONAL)["src"] : ""?>">
		</div>
	</a>
	<div class="badges">
		<?if(!!$arItem["PROPERTIES"]["NEWPRODUCT"]["VALUE"]):?>
			<span class="productTag productTagNew">Новинка</span>
		<?endif;?>
		<?if (!!$arItem["PROPERTIES"]["SALELEADER"]["VALUE"]):?>
			<span class="productTag productTagLeader">Хит</span>
		<?endif;?>
		<?if (!!$arItem["PROPERTIES"]["DISCOUNT"]["VALUE"]):?>
			<span class="productTag productTagDiscount">Скидка</span>
		<?endif;?>
		<?if (!!$arItem["PROPERTIES"]["RECOMEND"]["VALUE"]):?>
			<span class="productTag productTagRecomend">Рекомендуем</span>
		<?endif;?>
		<?if (!!$arItem["PROPERTIES"]["SHOWROOM"]["VALUE"]):?>
			<span class="productTag productTagShowRoom">Шоу-рум</span>
		<?endif;?>
		<?if (!!$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"]):?>
			<span class="productTag productTagSale">Распродажа</span>
		<?endif;?>
	</div>
	<div class="productInSlaiderInfo">
		<div class="productInSlaiderAvailability">
			<div class="productInSlaiderArtnum"><?=GetMessage('CT_PRODUCT_CODE')?>: <?=$arItem["ID"]?></div>
			<?/* if ($arItem["PRODUCT"]["AVAILABLE"] == "Y"):?>
				<div class="productInSlaiderAvailable">В наличии</div>
			<? else:?>
				<div class="productInSlaiderNotAvailable">Под заказ</div>
			<?endif; */?>
		</div>
		<p class="productInSlaiderNameParent">
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="productInSlaiderName"><?=$arItem["NAME"]?></a>
		</p>
		<div class="productInSlaiderBuy">
			<div class="productInSlaiderPrice">
				<div>
					<?$arPrice = $arItem["ITEM_PRICES"][0];
					$price = $arPrice["PRICE"];
					$oldPrice = $arPrice["BASE_PRICE"];
					if ($price != $oldPrice):?>
						<span class="productInSlaiderPriceOld"><?=number_format($oldPrice, 0, ".", " ")?> <i class="znakrub">c</i></span>
					<?endif;?>
					<div class="productInSlaiderPriceNew"><?=number_format($price, 0, ".", " ")?> <i class="znakrub">c</i></div>
				</div>
			</div>
			<div class="productInSlaiderCart">
				<button id="<?=$basketBtnId?>" class="productInSlaiderCartButton">
					<img src="<?=$templateFolder?>/images/basket.png">
				</button>
			</div>
		</div>
	</div>
	<div class="productInSlaiderComplement">
		<div class="productInSlaiderComplementProps">
			<?foreach ($arItem["DISPLAY_PROPERTIES"]["PARAMS"] as $param):?>
				<div class="productInSlaiderComplementPropsItem tooltip is-tooltip-bottom" data-tooltip="<?=$param["NAME"]?>">
					<img src="<?=$param["IMAGE"]?>">
				</div>
			<?endforeach;?>
		</div>
		<div class="productInSlaiderOneclick">
			<a href="#">Купить в один клик</a>
		</div>
	</div>
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => SITE_DEFAULT_PATH."/include/productInSlider_callback.php"
		),
		false
	);?>
</div>
<script>
	window.products = window.products || [];
	window.products.push(new JSCatalogItemProduct(<?=json_encode($jsParams)?>));
</script>