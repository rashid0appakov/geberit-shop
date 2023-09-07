<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = $arParams["IS_AJAX"] == "Y";

$uniqueId = $this->randString();
$navContainerId = "nav_container_$uniqueId";

$jsParams = array(
	"navElementSelector" => ".card-cell__show-more",
);

?>
<?

$count = count($arResult["ITEMS"]);

if($count==0){
	?>

<div class="container goods__container">
	<div class="goods__wrapper">
    <div class="goods__card">
	<div style="padding-left: 10px">К сожалению товары коллекции <?=$arParams["BREND"]?> <?=$arParams["SEIRES"]?> на данный момент отсутствуют.
<br><br>
	Предлагаем ознакомиться с другими популярными коллекциями <?=$arParams["BREND"]?>
</div>
	<div class="goods__card">
		<style>
		/*.series-section .goods__card h2{
		    font-weight: bold;
		    margin-top: 15px;
		    margin-bottom: 7px;
		    display: block;
		    font-size: 1.5em;
		    margin-block-start: 0.83em;
		    margin-block-end: 0.83em;
		    margin-inline-start: 0px;
		    margin-inline-end: 0px;
		    font-weight: bold;

		}*/
		.series-section .goods__card h2 {
			font-size: 1.1em !important;
		}
		</style>
	<?
					global $arrSFilterP;
					$arrSFilterP['PROPERTY_POPULAR'] = 'Y';
				?>
				<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"slider",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array("DETAIL_PICTURE", ""),
		"FILTER_NAME" => "arrSFilterP",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => SERIES_IBLOCK_ID,
		"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "40",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array("", ""),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "ACTIVE_FROM",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "DESC",
		"STRICT_SECTION_CHECK" => "N"
	)
);?>
</div>
</div>
</div>
</div>
	<?
}else{?>
	<h2>Товары из коллекции <?=$arParams["BREND"]?> <?=$arParams["SEIRES"]?></h2>
<div class="container goods__container">
	<div class="goods__wrapper">
    <div class="goods__card">
<div class="card-cell--all  preview-products"  itemscope itemtype="http://schema.org/ItemList">
	<?

	for ($j = 0, $i = 0; $j < 3 && $i < $count; $j++):?>

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

	<?for ($j = 0; $j < 2 && $i < $count; $j++):?>
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
<?if (!$isAjax):?>
	<script>
		//window.catalogSection = new JSCatalogSection(<?=json_encode($jsParams)?>);
	</script>
<?endif;?>

</div>
    </div>
  </div>
  <style>
  .teg-items .teg-item{    display: inline-block;
    width: 19%;
    padding: 10px 0;}
    @media (max-width: 550px){
		.teg-items .teg-item {
		     width: 49%;
		}
	}
</style>
  <h3>Другие коллекции <?=$arParams["BREND"]?></h3>
  <div class="teg-items">
<?
	if (isset($_GET['brands'])){
		$arIDs = explode('_', $_GET['brands']);
		foreach($arIDs AS $k => &$id)
			if ((int)$id)
				$id = (int)$id;
			else
				unset($arIDs[$k]);
		if (!empty($arIDs)){
			global $arrSFilter;
			$arrSFilter['PROPERTY_BRAND'] = $arIDs;
		}
	}

	$ID =array();
	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","CODE");
	$arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE"=>"Y");
	if (!empty($arIDs)){
		$arFilter['PROPERTY_BRAND'] = $arIDs;
	}
	$res = CIBlockElement::GetList(Array(), $arFilter, false,false, $arSelect);
	while($ob = $res->GetNextElement())
	{
	 $arFields = $ob->GetFields();

	 	$old = false;
			$arSelect_old = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
		$arFilter_old = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE"=>"Y","PROPERTY_OLD_CODE"=>$arFields['CODE']);
		$res_old = CIBlockElement::GetList(Array(), $arFilter_old, false,false, $arSelect_old);
		while($ob_old = $res_old->GetNextElement())
		{
			$arFields_old = $ob_old->GetFields();
			$old = true;
		}
	 	
		$ITEMS_COUNT=0;
		$arSelect_el = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
		$arFilter_el = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y","PROPERTY_SERIES"=>$arFields['ID'],'PROPERTY_DISCONTINUED' =>'N');
		$res_el = CIBlockElement::GetList(Array(), $arFilter_el, false,array('nTopCount'=>1), $arSelect_el);
		while($ob_el = $res_el->GetNextElement())
		{
			$arFields_el = $ob_el->GetFields();
			$ITEMS_COUNT ++;
		}
		
		if($ITEMS_COUNT!=0 && !$old){
			$ID[] =  $arFields['ID'];
		}
	}

global $man_show;

$arFilter = array("IBLOCK_ID" => SERIES_IBLOCK_ID, "ACTIVE" => "Y","ID"=>$ID, 'PROPERTY_BRAND' => $man_show);
if (!empty($arResult["IBLOCK_SECTION_ID"])) {
	$arFilter["IBLOCK_SECTION_ID"] = $arResult["IBLOCK_SECTION_ID"];
}
$resNav = CIBlockElement::GetList(
	array(
		"NAME" => "ASC",
		$arParams['SORT_BY2'] => $arParams['SORT_ORDER2'],
	),
	$arFilter,
	false,
	array("nPageSize" => 5, "nElementID" => $arResult["ID"]),
	array("ID", "DETAIL_PAGE_URL", "NAME")
);
$arNav = array();
while ($item = $resNav->GetNext()) {
	$arNav[] = $item;
}

if (count($arNav) > 2) {
	$arResult["NEXT"][] = $arNav[0];
	$arResult["NEXT"][] = $arNav[1];
	$arResult["NEXT"][] = $arNav[2];
	$arResult["NEXT"][] = $arNav[3];
	$arResult["NEXT"][] = $arNav[4];

	$arResult["PREV"][] = $arNav[5];
	$arResult["PREV"][] = $arNav[6];
	$arResult["PREV"][] = $arNav[7];
	$arResult["PREV"][] = $arNav[8];
	$arResult["PREV"][] = $arNav[9];
} else {
	if ($arNav[0]["ID"] == $arResult["ID"]) {
		$arResult["NEXT"] = false;
		$arResult["PREV"][] = $arNav[1];
		$arResult["PREV"][] = $arNav[2];
		$arResult["PREV"][] = $arNav[3];
		$arResult["PREV"][] = $arNav[4];
		$arResult["PREV"][] = $arNav[5];
	} else {
		$arResult["NEXT"][] = $arNav[0];
		$arResult["NEXT"][] = $arNav[1];
		$arResult["NEXT"][] = $arNav[2];
		$arResult["NEXT"][] = $arNav[3];
		$arResult["NEXT"][] = $arNav[4];
		$arResult["PREV"] = false;
	}
}
foreach ($arResult["NEXT"] as $key => $value) {
		?>
		<a href="<?=$value['DETAIL_PAGE_URL']?>" class="teg-item"><?=$value["NAME"]?></a>
		<?
	}
	foreach ($arResult["PREV"] as $key => $value) {
		?>
		<a href="<?=$value['DETAIL_PAGE_URL']?>" class="teg-item"><?=$value["NAME"]?></a>
		<?
	}
	
?>
</div>
<?}?>