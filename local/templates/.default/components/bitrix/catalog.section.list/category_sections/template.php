<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = $this->randString();

$params = base64_encode(serialize($arParams));
$containerId = "container_$uniqueId";

$jsParams = array(
	"params" => $params,
	"containerSelector" => "#$containerId",
);
?>

<div id="<?=$containerId?>" class="columns is-gapless is-multiline categoryCardsWrapper">
	<?foreach ($arResult["SECTIONS"] as $arSection):?>
		<?php
		$arShowImages = [];
		$res = CIBlockElement::GetList([], ['SECTION_ID' => $arSection['ID']], false, [], ['PREVIEW_PICTURE']);

		for($i = 0; $i < 3; $i++)
			if ($ob = $res->GetNextElement()){
				$arFields = $ob->GetFields();
				$arShowImages[] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
			}
		?>
		<div class="column is-12-mobile is-4-tablet is-3-desktop">
			<div class="categoryCardWrapper">
				<div class="categoryCard">
					<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
						<div class="categoryImages">
							<div class="categoryImage"><!--
								<?foreach ($arShowImages as $img):?>
									--><img src="<?=$img?>"><!--
								<?endforeach;?>
							--></div>
						</div>
						<div class="categoryTitle">
							<span class="title"><?=$arSection["NAME"]?></span>
							<span class="categoryNum"><?=$arSection["ELEMENT_CNT"]?> товаров</span>
						</div>
					</a>
				</div>
			</div>
		</div>
	<?endforeach;?>
</div>
<script type="text/javascript">
	window.sectionList = new JSCatalogSectionListCategorySections(<?=json_encode($jsParams)?>);
</script>