<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = $arParams["IS_AJAX"] == "Y";

$uniqueId = $this->randString();
$navContainerId = "nav_container_$uniqueId";

$jsParams = array(
	"navElementSelector" => ".card-cell__show-more",
);

$tFound=$arResult['NAV_RESULT']->NavRecordCount;
?>

<?if($tFound==0){?>
	<div class="gray-block empty-search">
		<div class="sub-block">
			<div class="sub-title">К сожалению по вашему запросу ничего не найдено, попробуйте новый поиск</div>
			<form method="get" action="/search/">
				<div class="b-empty__search-form columns">
					<div class="column col1">
						<input type="text" class="input" name="q" value="" autocomplete="off" placeholder="Поиск товаров">
					</div>
					<div class="column submitExpressForm">
						<button class="btn is-primary buy__item-button buy__item-button--blue" type="submit">Найти</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<style>
		.goods__card {
			width: 100%;
		}
	</style>
<?}else{?>
<div class="info_search">Найдено <?=$tFound;?> товаров</div>
<div class="card-cell--all preview-products"  itemscope itemtype="http://schema.org/ItemList">
	<?

	$count = count($arResult["ITEMS"]);
	for ($j = 0, $i = 0; $j < 4 && $i < $count; $j++):?>

		<div class="card-cell--row">
			<?
			for ($k = 0; $k < 4 && $i < $count; $k++, $i++):
				$arItem = $arResult["ITEMS"][$i];
				$APPLICATION->IncludeComponent(
					"bitrix:catalog.item",
					"product",
					array(
						"RESULT" => array(
							"ITEM" => $arItem,						
						),
						"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
			endfor;?>
		</div>
	<?endfor;?>

	<?for ($j = 0; $j < 4 && $i < $count; $j++):?>
		<div class="card-cell--row">
			<?for ($k = 0; $k < 4 && $i < $count; $k++, $i++):
				$arItem = $arResult["ITEMS"][$i];
				$APPLICATION->IncludeComponent(
					"bitrix:catalog.item",
					"product",
					array(
						"RESULT" => array(
							"ITEM" => $arItem,						
						),
						"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
			endfor;?>
		</div>
	<?endfor;?>
</div>
<?=$arResult['NAV_STRING']?>
<?}?>
<?if (!$isAjax):?>
	<script>
		//window.catalogSection = new JSCatalogSection(<?=json_encode($jsParams)?>);
	</script>
<?endif;?>