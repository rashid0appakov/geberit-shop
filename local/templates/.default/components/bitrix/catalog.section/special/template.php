<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = $arParams["IS_AJAX"] == "Y";

$uniqueId = $this->randString();
$navContainerId = "nav_container_$uniqueId";

$jsParams = array(
	"navElementSelector" => ".card-cell__show-more",
);

$count = count($arResult["ITEMS"]);
for ($j = 0, $i = 0; $j < 2 && $i < $count; $j++):?>
	<div class="card-cell--row">
		<?for ($k = 0; $k < 3 && $i < $count; $k++, $i++):
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

<?for ($j = 0; $j < 2 && $i < $count; $j++):?>
	<div class="card-cell--row">
		<?for ($k = 0; $k < 3 && $i < $count; $k++, $i++):
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
<?=$arResult['NAV_STRING']?>
<?/*if (!$isAjax):?>
	<script>
		window.catalogSection = new JSCatalogSection(<?=json_encode($jsParams)?>);
	</script>
<?endif;*/?>