<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$items = array_filter(
	$arResult,
	function($item) use ($arParams) { return $item["PARAMS"]["DEPTH_LEVEL"] <= $arParams["MAX_LEVEL"]; }
);
$itemKeys = array_keys($items);?>
<h4 class="is-size-5">Каталог</h4>
<div class="columns">
	<?$count = count($items);
	$breakCount = ceil($count / 2);?>
	<div class="column">
		<?for ($i = 0; $i < $breakCount; $i++):
			$item = $items[$itemKeys[$i]];?>
			<a class="link" href="<?=$item["LINK"]?>"><?=$item["TEXT"]?></a>
		<?endfor;?>
	</div>
	<div class="column">
		<?for (; $i < $count; $i++):
			$item = $items[$itemKeys[$i]];?>
			<a class="link" href="<?=$item["LINK"]?>"><?=$item["TEXT"]?></a>
		<?endfor;?>
	</div>
</div>