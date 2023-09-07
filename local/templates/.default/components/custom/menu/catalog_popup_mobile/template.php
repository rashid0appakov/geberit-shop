<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$jsParams = [];
$arRes = [];
?>
	<div id="popup-catalog-menu-start-mobile" class="popup-catalog-menu-start-mobile" style="left: -1600px;">
		<div class="catalog-menu-popup">
			<?/*<div class="container"> -->*/?>
			<div class="header container">
				<div class="search field">
					<div class="back">Каталог</div>
					<?$APPLICATION->IncludeComponent(
						"custom:search.header",
						"",
						array(),
						false
					);?>

				</div>
				<div id="popup-catalog-menu-start-mobile-close" class="close">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2"></line>
						<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2"></line>
					</svg>
				</div>
			</div>

			<div class="subcategories">
			<?$i = 1;
			foreach ($arResult["ITEMS"] AS $key => $arItem):
				if( count($arItem["ITEMS"]) > 0 || count($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]]) > 0) {
					$link = "#popup-catalog-menu-mobile-".$key;
					$sub = 'true';
				} else {
					$link = $arItem['LINK'];
					$sub = 'false';
				}
				?>
				<div class="section">
					<?if($sub=='true'){?>
						<a href="javascript:;" data-href="<?=$link;?>" data-sub="<?=$sub?>" <? if( count($arItem["ITEMS"]) > 0  || count($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]]) > 0 ) { ?>class="is-size-4"<? } ?>>
						<?=$arItem['TEXT'];?>
					</a>
					<?}else{?>
						<a href="<?=$link;?>" data-sub="<?=$sub?>" <? if( count($arItem["ITEMS"]) > 0  || count($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]]) > 0 ) { ?>class="is-size-4"<? } ?>>
						<?=$arItem['TEXT'];?>
					</a>
					<?}?>
				</div>
			<?endforeach;?>
			</div>
	  <?/*</div>*/?>
		</div>
	</div>
<?$i = 1;
foreach ($arResult["ITEMS"] as $key=>$arItem):?>
	<div id="popup-catalog-menu-mobile-<?=$key;?>" class="popup-catalog-menu-mobile" style="left: 400px;">
		<div class="catalog-menu-popup">
			<?/*<div class="container">*/?>
			<div class="header">
				<div id="popup-catalog-menu-mobile-back-<?=$key;?>" class="back"><?=$arItem['TEXT'];?></div>
				<div id="popup-catalog-menu-mobile-close-<?=$key;?>" class="close">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2"></line>
						<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2"></line>
					</svg>
				</div>
			</div>
		  <div class="subcategories">
			<div class="section">
				<a href="<?=$arItem["LINK"]?>" class="is-size-4"><?=$arItem["TEXT"]?></a>
			</div>
            <?
                $res = CIBlockElement::GetList(["NAME"=>"ASC", "SORT"=>"ASC"], ["IBLOCK_ID" => SERIES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_POPULAR" => "Y"], false, false, ["ID", "NAME", "CODE", "DATE_ACTIVE_FROM"]);
                while ($item = $res->GetNext()) {
                    if(!CIBlockElement::GetList(Array(), ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!PROPERTY_DISCONTINUED" => "Y", "PROPERTY_SERIES" => $item['ID'], "SECTION_ID" => $arItem["PARAMS"]["ID"]], false, false, ["ID"],["nTopCount"=>1])->GetNext()['ID']) { continue; }
                    $arRes[$arItem["PARAMS"]["ID"]][] = $item;
                }
                if(count($arRes[$arItem["PARAMS"]["ID"]])>0){?>
                    <div class="section">
                        <a href="javascript:;" data-href="#popup-catalog-menu-mobile-popular-series-<?=$key;?>" data-sub="true" class="is-size-4">Популярные серии</a>
                    </div><?
                }
            ?>
			<?foreach ($arItem["ITEMS"] AS &$arItem2):?>
				<div class="section">
					<a href="<?=$arItem2["LINK"]?>" class=""><?=$arItem2["TEXT"]?></a>
				</div>
			<?endforeach;?>
			
			<?foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]] AS $arTag){
				if (isset($arTag['THIS_PARENT'])){
					continue;
				}
				?>
				<div class="section">
					<a href="<?=$arItem["LINK"].$arTag["CODE"].'/'?>" class=""><?=$arTag["NAME"]?></a>
				</div>
			<?}?>
			<?/*<div class="border">
			  <div class="mini-product">
				<div class="line">
				  <a href="#">
					<img src="img/product-mini-1.png">
					<span class="article">Артикул: 123456</span>
					<div>Зеркало-шкаф с подсветкой 90см Keramag Option</div>
					<span class="old">9 999р.</span>
					<span class="new">9 000р.</span>
				  </a>
				</div>
			  </div>
			</div>*/?>
		  </div>
		  <?/*</div> -->*/?>
		</div>
	</div>
<?endforeach;?>
<?
    foreach ($arResult["ITEMS"] as $key=>$arItem){
        if(count($arRes[$arItem["PARAMS"]["ID"]])>0){}else{continue;}?>

        <div id="popup-catalog-menu-mobile-popular-series-<?=$key;?>" class="popup-catalog-menu-mobile" style="left: 400px;">
            <div class="catalog-menu-popup">
                <div class="header">
                    <div id="popup-catalog-menu-mobile-back-<?=$key;?>" class="back" data-href="#popup-catalog-menu-mobile-<?=$key;?>">Популярные серии</div>
                    <div id="popup-catalog-menu-mobile-close-<?=$key;?>" class="close">
                        <svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
                            <line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2"></line>
                            <line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2"></line>
                        </svg>
                    </div>
                </div>
                <div class="subcategories">
                    <div class="section">
                        <a href="<?=$arItem["LINK"]?>" class="is-size-4"><?=$arItem["TEXT"]?></a>
                    </div>
                    <?foreach ($arRes[$arItem["PARAMS"]["ID"]] AS &$arItem2):?>
                        <div class="section">
                            <a href="<?=$arItem["LINK"].$arItem2["CODE"].'/'?>" class=""><?=$arItem2["NAME"]?></a>
                        </div>
                    <?endforeach;?>
                </div>
            </div>
        </div><?
    }
?>
<script type="text/javascript">
	$(document).ready(function() {
        $('.popup-catalog-menu-mobile').on('click', '.section', function (e) {
            var href = $(this).find('a').attr('data-href');
            //console.log(href);
            if ($(this).find('a').attr('data-sub') == 'true') {
            	e.preventDefault();
            	
                $(href).show();

                TweenMax.fromTo(href, 1, {
                    ease: Power4.easeOut,
                    left: 0
                }, {
                    ease: Power4.easeOut,
                    left: -globalWidth
                });
                TweenMax.fromTo(href, 1, {
                    ease: Power4.easeOut,
                    left: globalWidth
                }, {
                    ease: Power4.easeOut,
                    left: 0
                });
                
                return false;
            }
        });/**/
        
		if ($('.popup-catalog-menu-mobile .back').length)
			$('.popup-catalog-menu-mobile .back').on('click', function () {
                var hrefTo = $(this).attr('data-href');
                if(!hrefTo){ hrefTo='popup-catalog-menu-start-mobile'; }
                var hrefFrom = $(this).parents('.popup-catalog-menu-mobile').attr('id');
                if(!hrefFrom){ hrefFrom='.popup-catalog-menu-mobile'; }
				TweenMax.fromTo(hrefTo, 1, {
					ease: Power4.easeOut,
					left: -globalWidth
				}, {
					ease: Power4.easeOut,
					left: 0
				});
				TweenMax.fromTo(hrefFrom, 1, {
					ease: Power4.easeOut,
					left: 0
				}, {
					ease: Power4.easeOut,
					left: globalWidth
				});
			});
	})
</script>