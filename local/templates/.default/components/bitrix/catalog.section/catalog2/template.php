	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= $templateFolder;?>/styles/normalize.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $templateFolder;?>/slick/slick.css"/>    
    <link rel="stylesheet" type="text/css" href="<?= $templateFolder;?>/styles/filter-styles.css">
    <link rel="stylesheet" type="text/css" href="<?= $templateFolder;?>/styles/style.css"/>	
	
	<script type="text/javascript" src="<?= $templateFolder;?>/slick/slick.js?t=<?=time();?>"></script>
	<script type="text/javascript" src="<?= $templateFolder;?>/scripts/index.js"></script> 

	<input type="hidden" name="prevArrow" value="<?= $templateFolder;?>/images/prev.png" />	
	<input type="hidden" name="nextArrow" value="<?= $templateFolder;?>/images/next.png" />	

		
	<? foreach($arResult["ITEMS"] as $arItem ) { ?>	


		<? if( count( $arItem['SET'] ) <= 2 ) { ?>

			<!-- Вертикальные карточки - 2 шт -->
			  <div class="complect complect-v">        
			  
				<? $price=0; ?>
			  
				<? foreach( $arItem['SET'] as $key=>$el ) { ?>
				
					<?  
					if( $key != 0 ) { continue; }
					
					$price += round( $el['PRICES']['PRICE'] );
					?>
			  
				  <div class="complect-card" id='xxx'>
					<div class="complect-card__img">              
						<img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img active-card-img" alt="Название продукта">           
						<img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img" alt="Название продукта">         
					</div>
					<div class="complect-card__info">                 
						  <div class="complect-card__name"><?=$el['NAME'];?></div>
					  <div class="complect-card__price new"><?=round( $el['PRICES']['PRICE'] );?> <i class="znakrub">c</i></div>
					</div>
					<a href="<?=$el['DETAIL_PAGE_URL'];?>" target="_blanc" class="stretched-link"></a>
				  </div>
				  
				<? } ?>  
				  
				  <div class="complect-card__symb">
					  <div class="complect-card__symb-wrapper"><img src="<?= $templateFolder;?>/images/plu.png" alt="+"></div>
				  </div>
				  <!-- Слайдер внутри карточки -->
				  <div class="complect-card__slider-container">
						
						<? foreach( $arItem['SET'] as $key=>$el ) { ?>
						
							<?  
							if( $key == 0 ) { continue; }
							
							$price += round( $el['PRICES']['PRICE'] );
							?>
						
							<div class="complect-card__slide"> 
					
							  <div class="complect-card">
								  <div class="complect-card__img">              
									  <img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img active-card-img" alt="Название продукта">           
									  <img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img" alt="Название продукта">          
									</div>
								<div class="complect-card__info">
								  <div class="complect-card__name"><?=$el['NAME'];?></div>
								  <div class="complect-card__price new"><?=round( $el['PRICES']['PRICE'] );?> <i class="znakrub">c</i></div>
								</div>
								<a href="<?=$el['DETAIL_PAGE_URL'];?>" target="_blanc" class="stretched-link"></a>
							  </div>              
							</div>   
						
						<? } ?>

	 
				  </div>    
				  <div class="complect-card__symb">
					<div class="complect-card__symb-wrapper">
					<img src="<?= $templateFolder;?>/images/eq.png" alt="+"></div>
				  </div>                  
				  <div class="complect-card--buy">
				  
					<!--
					<div class="price-part">
					  <div class="price__savings">Вы экономите: <span class="savings">2000</span><span class="currency"> руб.</span></div>
					  <div class="price__text">Стоимость комплекта</div>
					  <div class="price__wrapper">
						<div class="price__old">39800 <i class="znakrub">c</i></div>
						<div class="price__new">18300 <i class="znakrub">c</i></div>
					  </div>
					</div>  
					-->
					
					<div class="price-part">
					
						<?
						$diff = 0;
						
						if( $price != $arItem['MIN_PRICE']['VALUE'] ) {
							$diff = $arItem['MIN_PRICE']['VALUE'] - $price;
						}
						?>
					
					
					
						<div class="price__savings">Вы экономите: <span class="savings"><?=$diff;?></span><span class="currency"> руб.</span></div>
					  <div class="price__text">Стоимость комплекта</div>
					  <div class="price__wrapper">
						<div class="price__new"><?=$arItem['MIN_PRICE']['VALUE']?> <i class="znakrub">c</i></div>
					  </div>
					</div>  
					
					<? 
					//echo $arItem['DETAIL_PAGE_URL'];
					?>
					
					<div class="buy-part">
					  <div class="btn__wrapper">
						<!--<button class="btn buy__btn buy__btn--colored " id="buy_" data-id="<? echo $arItem['ID']; ?>">-->
						<button class="btn buy__btn buy__btn--colored  add-to-basket buy--item" id="buy_" data-id="<? echo $arItem['ID']; ?>">
						  <img src="<?= $templateFolder;?>/images/shopping-cart.png" alt="shopping-cart">
						  <span>Купить</span>                
						</button>              
						<button class="btn buy__btn buy__btn--oneclick " data-id="<? echo $arItem['ID']; ?>">
						  <span>Купить в один клик</span>
						</button>
					  </div>  
					  <div class="profit__text">+ бесплатная доставка по Москве и до транспортной компании</div>              
					</div>        
				  </div>    
			  </div>
		  
		  <? } ?>
		  
		  
			<? if( count( $arItem['SET'] ) > 2 ) { ?>
		  
			
				<!-- Горизонтальные карточки - 3 шт -->
			  <div class="complect complect-h">      
				  <div class="complect-cards__container--horizontal">
				  
					<!-- Слайдер внутри карточки -->
					<!--
					<div class="complect-card__slider-container--horizontal card-order-h-0">
						<div class="complect-card__slide--horizontal">
						  <div class="complect-card--horizontal">              
							  <div class="complect-card__img--horizontal">              
								  <img src="<?= $templateFolder;?>/images/item1.jpg" class="card-img--horizontal active-card-img" alt="Название продукта">           
								  <img src="<?= $templateFolder;?>/images/item2.jpg" class="card-img--horizontal" alt="Название продукта">          
							  </div>
							<div class="complect-card__info--horizontal">
							  <div class="complect-card__name--horizontal">Название товара в несколько строк на сайте</div>
							  <div class="complect-card__price--horizontal new">1400 <i class="znakrub">c</i></div>
							</div>
							<a href="https://tiptop-shop.ru/" target="_blanc" class="stretched-link"></a>
						  </div>
						</div>  
						<div class="complect-card__slide">
						  <div class="complect-card--horizontal">              
							  <div class="complect-card__img--horizontal">              
								  <img src="<?= $templateFolder;?>/images/item1.jpg" class="card-img--horizontal active-card-img" alt="Название продукта">           
								  <img src="<?= $templateFolder;?>/images/item2.jpg" class="card-img--horizontal" alt="Название продукта">          
							  </div>
							<div class="complect-card__info--horizontal">
							  <div class="complect-card__name--horizontal">Название товара в несколько строк на сайте</div>
							  <div class="complect-card__price--horizontal new">1400 <i class="znakrub">c</i></div>
							</div>
							<a href="https://tiptop-shop.ru/" target="_blanc" class="stretched-link"></a>
						  </div>
						</div>  
						<div class="complect-card__slide">
						  <div class="complect-card--horizontal">              
							  <div class="complect-card__img--horizontal">              
								  <img src="<?= $templateFolder;?>/images/item1.jpg" class="card-img--horizontal active-card-img" alt="Название продукта">           
								  <img src="<?= $templateFolder;?>/images/item2.jpg" class="card-img--horizontal" alt="Название продукта">          
							  </div>
							<div class="complect-card__info--horizontal">
							  <div class="complect-card__name--horizontal">Название товара в несколько строк на сайте</div>
							  <div class="complect-card__price--horizontal new">1400 <i class="znakrub">c</i></div>
							</div>
							<a href="https://tiptop-shop.ru/" target="_blanc" class="stretched-link"></a>
						  </div>
						</div>               
					</div>    
					-->
					
					<? $price=0; ?>
			  
					<? foreach( $arItem['SET'] as $key=>$el ) { ?>
				
						<?  
						$price += round( $el['PRICES']['PRICE'] );
						?>
	   
						<div class="complect-card--horizontal card-order-h-2">
							<div class="complect-card__img--horizontal">              
								<img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img active-card-img" alt="Название продукта">           
								<img src="<?=CFile::GetPath($el['PREVIEW_PICTURE']);?>" class="card-img" alt="Название продукта">         
							</div>
						  <div class="complect-card__info--horizontal">
							<div class="complect-card__name--horizontal"><?=$el['NAME'];?></div>
							<div class="complect-card__price--horizontal new"><?=round( $el['PRICES']['PRICE'] );?> <i class="znakrub">c</i></div>
						  </div>
						  <a href="<?=$el['DETAIL_PAGE_URL'];?>" target="_blanc" class="stretched-link"></a>
						</div>
						
						<? if( $key < ( count( $arItem['SET'] ) + 1 ) ) { ?>
						
							<div class="complect-card__symb--h card-order-h-3">
								<div class="complect-card__symb-wrapper--h">
								<img src="<?= $templateFolder;?>/images/plu.png" alt="+"></div>
							</div>  
						
						<? } ?>

					<? } ?>
								
        
				  </div>
				  
				  
					<?
					$diff = 0;
					
					if( $price != $arItem['MIN_PRICE']['VALUE'] ) {
						$diff = $arItem['MIN_PRICE']['VALUE'] - $price;
					}
					?>
      
				  <div class="complect-card--buy card-order-h-6">
					<div class="price-part">
					  <div class="price__savings">Вы экономите: <span class="savings"><?=$diff;?></span><span class="currency"> руб.</span></div>
					  <div class="price__text">Стоимость комплекта</div>
					  <div class="price__wrapper">
						<div class="price__new"><?=$arItem['MIN_PRICE']['VALUE']?> <i class="znakrub">c</i></div>
					  </div>  
					</div>  
					<div class="buy-part">
					  <div class="btn__wrapper">
						<!--<button class="btn buy__btn buy__btn--colored " id="buy_" data-id="<? echo $arItem['ID']; ?>">-->
						<button class="btn buy__btn buy__btn--colored  add-to-basket buy--item" id="buy_" data-id="<? echo $arItem['ID']; ?>">
						  <img src="<?= $templateFolder;?>/images/shopping-cart.png" alt="shopping-cart">
						  <span>Купить</span>                
						</button>              
						<button class="btn buy__btn buy__btn--oneclick " data-id="<? echo $arItem['ID']; ?>">
						  <span>Купить в один клик</span>
						</button>
					  </div>  
					  <div class="profit__text">+ бесплатная доставка по Москве и до транспортной компании</div>              
					</div>        
				  </div>    
				</div>
				  
		  
		  
			<? } ?>
		  
	  
	<? } ?>
	
	
	<?=$arResult['NAV_STRING']?>













<?/*?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;

$templateData['robots'] = 'index, follow';
$templateData['all_cnt'] = $arResult['NAV_RESULT']->NavRecordCount;
if(defined('MAIN_SITE_BRAND') and (
		!empty($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY])
		and is_array($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY])
		and !in_array($arResult["ORIGINAL_PARAMETERS"]['GLOBAL_FILTER']['=PROPERTY_'.MAIN_BRAND_PROPERTY], MAIN_SITE_BRAND)
	)
)
{
	$templateData['robots'] = 'noindex, nofollow';
}
?>
<?$this->SetViewTarget('all_catalog_items');?>
<?echo number_format($arResult['NAV_RESULT']->NavRecordCount, 0, ',', ' ');?>
<?$this->EndViewTarget();?>
<div class="goods__card-cell card-cell preview-products">
	<div class='goods-list section_list'>
		<?
		$isAjax = $arParams["IS_AJAX"] == "Y";

		$uniqueId = $this->randString();
		$navContainerId = "nav_container_$uniqueId";

		$jsParams = array(
			"navElementSelector" => ".card-cell__show-more",
		);
?>
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
		<?if($r == $arParams['LINE_ELEMENT_COUNT'] and !$isAjax):?>
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
		<?endif?>
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
<?=$arResult['NAV_STRING']?>
<?/*if (!$isAjax):?>
	<script>
		//window.catalogSection = new JSCatalogSection(<?=json_encode($jsParams)?>);
	</script>
<?endif;*/?>
</div>