<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//pr($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL']);
$uniqueId = $this->randString();
$params = base64_encode(serialize($arParams));
$containerId = "container_$uniqueId";

$jsParams = array(
	"params" => $params,
	"containerSelector" => "#$containerId",
);
?>
<div class="container goods__container">
	<div class="goods__wrapper no-bg">
		<div class="categoryWrapper">
			<h1 class="goods__title-title margin-inv"><?$APPLICATION->ShowTitle(false);?></h1>
			<div id="<?=$containerId?>" class="columns is-gapless is-multiline categoryCardsWrapper">
				<? foreach($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'] AS &$arSection):
					if ($arSection["ELEMENT_CNT"]<=0) continue;

					global $man_show;

					$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	                $arFilter = Array("IBLOCK_ID"=>$arSection['IBLOCK_ID'], "SECTION_ID"=>$arSection['ID'], "INCLUDE_SUBSECTIONS"=>"Y", "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_DISCONTINUED" => "Y", "PROPERTY_MANUFACTURER" => $man_show);

	                //var_dump($arFilter);
	                $res = CIBlockElement::GetList(Array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
	                if($ob = $res->GetNextElement())
	                {
	                  //  $arFields = $ob->GetFields();
	                }else{
	                	continue;
	                }
					?>
					<div class="column is-12-mobile is-4-tablet is-3-desktop">
						<div class="categoryCardWrapper">
							<div class="categoryCard">
								<? if (!empty($arSection['RESIZED'])):?>
								<div class="categoryImages">
									<div class="categoryImage" title="<?=$arSection["NAME"]?> в каталоге">
										<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
										<?foreach ($arSection['RESIZED'] AS &$arImg):?>
											<div class="categoryImageWrapper">
												<img src="<?=$arImg['src']?>" alt="<?=$arSection["NAME"]?>" />
											</div>
										<?endforeach;?>
										</a>
									</div>
								</div>
								<? endif;?>
								<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="categoryTitle" title="<?=$arSection["NAME"]?> в каталоге">
									<span class="title"><?=$arSection["NAME"]?></span>
									<span class="categoryNum"><?=$arSection["ELEMENT_CNT"]?> <?=GetMessage('HDR_ITEM_TITLE').CClass::getFilesEnds($arSection["ITEMS_COUNT"])?></span>
								</a>
							</div>
						</div>
					</div>
				<?endforeach;?>
				<div class="clearfix footer-navnav"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
 window.sectionList = new JSCatalogSectionListCategorySections(<?=json_encode($jsParams)?>);
</script>
