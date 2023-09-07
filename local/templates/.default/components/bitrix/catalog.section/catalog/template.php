<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// global $USER;
// if ($USER->IsAdmin()){
//     var_dump(123);
//     var_dump($arResult["ITEMS"]);
//     die;
// }

if(!$arResult["ITEMS"]){
	\Bitrix\Iblock\Component\Tools::process404(
        ""
        ,($arParams["SET_STATUS_404"] === "Y")
        ,($arParams["SET_STATUS_404"] === "Y")
        ,($arParams["SHOW_404"] === "Y")
        ,$arParams["FILE_404"]
    );
}


if (empty($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;

$bNoindexTag = false;
if (3 > $arResult['NAV_RESULT']->NavRecordCount)
	$bNoindexTag = true;

$templateData['robots'] = 'index, follow';
$templateData['all_cnt'] = $arResult['NAV_RESULT']->NavRecordCount;
/*if(defined('MAIN_SITE_BRAND') and (
		!empty($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY])
		and is_array($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY])
		and !in_array($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY], MAIN_SITE_BRAND)
	)
)
{
	$templateData['robots'] = 'noindex, nofollow';
}*/


global $arrSliderFIlter;
$arrSliderFIlter = array(
	"PROPERTY_SECTION" => $arResult["ID"],
	"PROPERTY_SITE_ID" => SITE_ID,
);
$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"section_slider",
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
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array("NAME", "DETAIL_PICTURE", ""),
		"FILTER_NAME" => "arrSliderFIlter",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "39",
		"IBLOCK_TYPE" => "content",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "20",
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
		"PROPERTY_CODE" => array("TEXT_BEFORE", ""),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "ID",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N"
	),
	$component
);
?>
<?$this->SetViewTarget('all_catalog_items');?>
<?echo number_format($arResult['NAV_RESULT']->NavRecordCount, 0, ',', ' ');?>
<?$this->EndViewTarget();?>
<div class="goods__card-cell card-cell preview-products" itemscope itemtype="http://schema.org/ItemList">
	<div class='goods-list section_list'>
		<?
		$isAjax = $arParams["IS_AJAX"] == "Y";

		$uniqueId = $this->randString();
		$navContainerId = "nav_container_$uniqueId";

		$jsParams = array(
			"navElementSelector" => ".card-cell__show-more",
		);
?>
<? if ($bNoindexTag) : ?>
<noindex>
<? endif; ?>
<div class="card-cell--all">
<div class="card-cell--row">
<?
$r = 1;
$i = 0;

foreach($arResult["ITEMS"] as $arItem ):
	$i ++;
	if($i > $arParams['LINE_ELEMENT_COUNT']):
		$r ++;
		$i = 1;
		?>
		</div>
<!-- 		<?if($r == $arParams['LINE_ELEMENT_COUNT'] and !$isAjax):?>
			<?php
			global $arrSliderFIlter;
			$arrSliderFIlter = array(
				"PROPERTY_SECTION" => $arResult["ID"],
				"PROPERTY_SITE_ID" => SITE_ID,
			);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"section_slider",
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
					"CACHE_TYPE" => "A",
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"DISPLAY_DATE" => "N",
					"DISPLAY_NAME" => "N",
					"DISPLAY_PICTURE" => "N",
					"DISPLAY_PREVIEW_TEXT" => "N",
					"DISPLAY_TOP_PAGER" => "N",
					"FIELD_CODE" => array("NAME", "DETAIL_PICTURE", ""),
					"FILTER_NAME" => "arrSliderFIlter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => "39",
					"IBLOCK_TYPE" => "content",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "N",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "20",
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
					"PROPERTY_CODE" => array("TEXT_BEFORE", ""),
					"SET_BROWSER_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_STATUS_404" => "N",
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SORT_BY1" => "SORT",
					"SORT_BY2" => "ID",
					"SORT_ORDER1" => "ASC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N"
				),
				$component
			);?>
		<?endif?> -->
		<div class="card-cell--row">
		<?
	endif;
	?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.item",
		"product",
		array(
			"RESULT" => array(
				"ITEM" => $arItem,
			),
			"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"]) + array("CUSTOM_DISPLAY_PARAMS" => $arParams["CUSTOM_DISPLAY_PARAMS"])
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
	?>
<?endforeach?>
</div>
</div>
<? if ($bNoindexTag) : ?>
</noindex>
<? endif; ?>
<?=$arResult['NAV_STRING']?>
<?/*if (!$isAjax):?>
	<script>
		//window.catalogSection = new JSCatalogSection(<?=json_encode($jsParams)?>);
	</script>
<?endif;*/?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const imageObserver = new IntersectionObserver((entries, imgObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target
                    console.log("lazy loading ", lazyImage)
                    lazyImage.src = lazyImage.dataset.src
                    lazyImage.classList.remove("lzy_img");
                    imgObserver.unobserve(lazyImage);
                }
            })
        });
        const arr = document.querySelectorAll('img.lzy_img')
        arr.forEach((v) => {
            imageObserver.observe(v);
        })
    })
</script>
<?/*<script type="application/ld+json">
	{
	    "@context": "http://schema.org",
	    "@type": "ItemList",
	    "url": "https://<?=SITE_SERVER_NAME.$APPLICATION->GetCurUri()?>",
	    "numberOfItems": "<?=$arResult['NAV_RESULT']->NavRecordCount?>",
	    "itemListElement": [
	    	<?
	    	$c=0;
	    	foreach ($arResult["ITEMS"] as $itemProd) {
	    		$c++;
	    		
	    		$prices = CCatalogProduct::GetOptimalPrice($itemProd['ID']);
	    		$prices['RESULT_PRICE']['DISCOUNT_PRICE'];
	    		$photos=array_shift(CClass::getPreviewPhotos($itemProd["DETAIL_PICTURE"], $itemProd["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0]));
	    		$filename =$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($itemProd["DETAIL_PICTURE"]);
				$info = CFile::GetFileArray($itemProd["DETAIL_PICTURE"]['ID']);
				
				$fil = \Bitrix\Main\IO\Path::getExtension($filename);
				$temp_name = $info["FILE_NAME"].'$$';
				$img = '/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;
				$size = getimagesize($_SERVER["DOCUMENT_ROOT"].$img);
	    	?>

		        {
		            "@type": "Product",
		           
		            "image": {
					  "@type" : "ImageObject" ,
					  "url": "https://<?=SITE_SERVER_NAME.$img?>", 
					  "height" : "<?=$size[1]?>", 
					  "width" : "<?=$size[0]?>",
					  "name" : "<?=$itemProd['NAME']?>"
					 },
		            "url": "https://<?=SITE_SERVER_NAME.$itemProd["DETAIL_PAGE_URL"]?>",
		            "name": "<?=$itemProd['NAME']?>",
		            "brand": {
						"@type": "Brand",
						"name": "<?=$GLOBALS['PAGE_DATA']['INFO_BRAND'][$itemProd["PROPERTIES"]["MANUFACTURER"]['VALUE']]['NAME']?>"
					},
		            "offers": {
		                "@type": "Offer",
		                "price": "<?=$prices['RESULT_PRICE']['DISCOUNT_PRICE']?>",
		                "priceCurrency":"RUB",
		                "availability": "<?=$itemProd["PRODUCT"]['QUANTITY'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock' ?>"
		            }
		        }<?=count($arResult["ITEMS"])!=$c?',':''?>

	        <?
	    	}?>
	        
	    ]
	}
</script>*/?>