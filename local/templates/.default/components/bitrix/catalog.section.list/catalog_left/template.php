<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	$this->setFrameMode(true);

	$uniqueId = $this->randString();

	$params = base64_encode(serialize($arParams));
	$containerId = "container_$uniqueId";

	$jsParams = array(
		"params" => $params,
		"containerSelector" => "#$containerId",
	);

	if( count($arResult["SECTIONS"]) > 0 ) { ?>
	<div class="filter__item" style="margin-bottom: 25px !important;">
		<div class="filter__title--toggle is-expanded" data-id="accordion1">
			<div aria-expanded="true" aria-controls="accordion1" class="filter__title accordion-title accordionTitle js-accordionTrigger is-expanded"><?=GetMessage('CT_SELECT_SECTION')?></div>
		</div>
		<div class="filter__content accordion-content accordionItem is-expanded" id="accordion1" aria-hidden="false">
            <style>.filter__content-item_span a~span{color:rgb(135,135,135);font-size:13px;line-height:13px;}</style>
			<ul class="filter__content-list">
			<?foreach ($arResult["SECTIONS"] as $arSection):?>
				<li class="filter__content-item filter__content-item_span filter__content-item--toggle">
					<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
						<span><?=$arSection["NAME"]?></span><span/>
					</a>
                    <span><?=$arSection["ELEMENT_CNT"]?></span>
				</li>
			<?endforeach;?>
			</ul>
		</div>
	</div>
<? } ?>
<?/*?>
<div id="<?=$containerId?>" class="columns is-gapless is-multiline categoryCardsWrapper">
	<?foreach ($arResult["SECTIONS"] as $arSection):?>
		<?php
		$arShowImages = [];
		$res = CIBlockElement::GetList([], ['SECTION_ID' => $arSection['ID']], false, [], ['PREVIEW_PICTURE']);

		for($i = 0; $i < 3; $i++){
			$ob = $res->GetNextElement();
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
<script>
	window.sectionList = new JSCatalogSectionListCategorySections(<?=json_encode($jsParams)?>);
</script>