<?
$uniqueId = $this->randString();
$carouselId = "carousel_$uniqueId";
$leftArrowId = "left_arrow_$uniqueId";
$rightArrowId = "right_arrow_$uniqueId";


// echo "<pre>";
// // var_dump($arResult['SECTION_ID']);
// // var_dump($arResult);
// $el = GetIBlockElement($arResult['ID']);
// var_dump($el);
// echo "</pre>";

// $ids = array();
// $list = CIBlockSection::GetNavChain($arResult['IBLOCK_ID'], $arResult['IBLOCK_SECTION_ID'], array(), true);
// foreach ($list as $arSectionPath){
// 	$ids[] = $arSectionPath['ID'];
// }





// var_dump($ids);
$arAnalogueProperties = array();
$arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'ID'=>$arResult['IBLOCK_SECTION_ID']);
$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array('UF_*'));
if($ar_result = $db_list->GetNext())
{
	$arAnalogueProperties = $ar_result['UF_ANALOGUE_PROP'];
}


$list = CIBlockSection::GetNavChain(false, $arResult['IBLOCK_SECTION_ID'], array(), true);
foreach ($list as $arSectionPath){
	$arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'ID'=>$arSectionPath['ID']);
	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array('UF_*'));
	if($ar_result = $db_list->GetNext())
	{
		$arAnalogueProperties = $ar_result['UF_ANALOGUE_PROP'];
	}

	if (is_array($arAnalogueProperties) && count($arAnalogueProperties)>0){
		break;
	}

	// if (count($arAnalogueProperties)<0)
 //   		echo '<pre>';print_r($arSectionPath);echo '</pre>';
	// }
}

//var_dump($arAnalogueProperties);

?>
<?if (!empty($arAnalogueProperties) && ($delivery_title != 'В наличии')) : // if () :
	?>
<?$FieldSort = GetSortField();
	$ii = 0;
	$newSort = $arrSortId = $arrSort = array();
	// foreach ($arResult['ADDITIONAL'] as $key => $value) {
	// 	foreach($value as $k => $arItem){
	// 		$newSort[$k] = $arItem;
	// 		$arrSortId[]=$k;
	// 	}
	// }
	
	// if(count($arrSortId)>0){
	// 	$arSelect = Array("ID", "NAME",$FieldSort);
	// 	$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$arrSortId);
	// 	$res = CIBlockElement::GetList(Array($FieldSort=>"ASC", "SORT"=>"ASC"), $arFilter, false, false, $arSelect);
	// 	while($ob = $res->GetNextElement())
	// 	{
	// 	 $arFields = $ob->GetFields();
	// 	 $arrSort[$arFields['ID']] = $arFields['ID'];
	// 	}
	// }


	// @TODO тут надо получить $arAnalogueProperties коды свойств из настроек раздела UF_ANALOGUE_PROP

	// $arAnalogueProperties = array();
	// $arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'ID'=>$arResult['IBLOCK_SECTION_ID']);
	// $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array('UF_*'));
	// if($ar_result = $db_list->GetNext())
	// {
	// 	$arAnalogueProperties = $ar_result['UF_ANALOGUE_PROP'];
	// }

	if (!empty($arAnalogueProperties)) {

	    $arAnalogueFilter = [];

	    global $APPLICATION;
		$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
		$STORE_IS_AVAILABLE_PROP = "PROPERTY_IS_AVAILABLE_4";
		$sPriceCode = 1;
		if ($geo_id == 817){ // питер
		    $STORE_IS_AVAILABLE_PROP = "PROPERTY_IS_AVAILABLE_1";
			$sPriceCode = 2;
		}
		if ($geo_id == 2201){ // Екатеринбург
		    $STORE_IS_AVAILABLE_PROP = "PROPERTY_IS_AVAILABLE_2";
			$sPriceCode = 4;
		}
		if ($geo_id == 1095){ // Краснодар
		    $STORE_IS_AVAILABLE_PROP = "PROPERTY_IS_AVAILABLE_7";
			$sPriceCode = 8;
		}
	    // Свойства
	    foreach ($arAnalogueProperties as $sProperty) {

			$iPercent = 1;

            // Разбор строки, код свойства, процент через запятую
            $arProperty = explode(',', $sProperty);
            if (count($arProperty) == 2) {
                $sVal = trim($arProperty[0]);
                $iVal = intval(trim($arProperty[1]));
                if (!empty($sVal) && !empty($iVal)) {
                    $sProperty = $sVal;
                    $iPercent = $iVal;
                }
            }

	        if (empty($arResult['PROPERTIES'][$sProperty])) {
	            continue;
	        }


	        if (!empty($arResult['PROPERTIES'][$sProperty]['PROPERTY_TYPE'])) {
	            switch ($arResult['PROPERTIES'][$sProperty]['PROPERTY_TYPE']) {
	                case 'S':
	                    if (!empty($arResult['PROPERTIES'][$sProperty]['VALUE'])) {
	                        $arAnalogueFilter['PROPERTY_' . $sProperty] = $arResult['PROPERTIES'][$sProperty]['VALUE'];
	                    }
	                    break;
	                case 'L':
	                    if (!empty($arResult['PROPERTIES'][$sProperty]['VALUE_XML_ID'])) {
	                        $arAnalogueFilter['PROPERTY_' . $sProperty] = $arResult['PROPERTIES'][$sProperty]['VALUE_ENUM_ID'];
	                    }
	                    break;
	                case 'N':
	                    if (!empty($arResult['PROPERTIES'][$sProperty]['VALUE'])) {                            
	                        $fMinValue = $arResult['PROPERTIES'][$sProperty]['VALUE'] * (100 - $iPercent) / 100;
	                        $fMaxValue = $arResult['PROPERTIES'][$sProperty]['VALUE'] * (100 + $iPercent) / 100;
	                        $arAnalogueFilter['>=PROPERTY_' . $sProperty] = $fMinValue;
	                        $arAnalogueFilter['<=PROPERTY_' . $sProperty] = $fMaxValue;
	                    }
	                    break;
	            }
	        }
	    }

	    // Цена
	    //$sPriceCode = (!empty(Tn\location\LocationHelper::$arLocation['PRICE_CODE']) ? Tn\location\LocationHelper::$arLocation['PRICE_CODE'] : 'BASE');
	    //var_dump($arResult["MIN_PRICE"]['DISCOUNT_VALUE']);
	    $arPrice = $arResult["MIN_PRICE"]['DISCOUNT_VALUE'];
	    $fMinValue = $arPrice * 0.85;
	    $fMaxValue = $arPrice * 1.15;
	    $arAnalogueFilter[">CATALOG_PRICE_" . $sPriceCode] = $fMinValue;
	    $arAnalogueFilter["<CATALOG_PRICE_" . $sPriceCode] = $fMaxValue;

	    // Наличие
	    $arAnalogueFilter['=' . $STORE_IS_AVAILABLE_PROP] = array(1, 2);


		$arSelect = Array("ID", "NAME");
		$arFilter = Array("IBLOCK_ID"=>$arResult['IBLOCK_ID'], 'IBLOCK_SECTION_ID'=>$arResult['IBLOCK_SECTION_ID'], 'INCLUDE_SUBSECTIONS'=> 'Y');
		$arFilter = array_merge($arFilter, $arAnalogueFilter);

	    // global $USER;
	    // if ($USER->IsAdmin()){
		   //  echo "<pre>";
		   //  var_dump($arFilter);
		   //  echo "</pre>";
	    // }
	    // die;

		$res = CIBlockElement::GetList(Array($FieldSort=>"ASC", "SORT"=>"ASC"), $arFilter, false, Array("nTopCount"=>8), $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();

			$newSort[$arFields['ID']] = CCatalogProduct::GetByIDEx($arFields['ID']);
			$newSort[$arFields['ID']]['ITEM'] = $newSort[$arFields['ID']];
			$arrSort[$arFields['ID']] = $arFields['ID'];
			// echo "<pre>";
			// var_dump($arItem);
			// echo "</pre>";
			// die;
		}

	}

	//var_dump($arrSort);
	

	/*foreach ($arResult['ADDITIONAL'] as $key => $value) {
		$arrSort = $arrSortId = array();
		foreach($value as $k => $arItem){
			$arrSortId[] = $k;
		}
		
		$arSelect = Array("ID", "NAME",$FieldSort);
		$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$arrSortId);
		$res = CIBlockElement::GetList(Array($FieldSort=>"desc", "SORT"=>"ASC"), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement())
		{
		 $arFields = $ob->GetFields();
		 $arrSort[$arFields['ID']] = $arFields['ID'];
		}
		foreach($arrSort as $k=>$val){
			$arItem = $arResult['ADDITIONAL'][$key][$k];
			$ii ++;
			if($ii > 8)
			{
				break 2;
			}?>
			<div class="swiper-slide">
			  <?$APPLICATION->IncludeComponent(
					"bitrix:catalog.item",
					"product",
					array(
						"RESULT" => array(
							"ITEM" => $arItem
						),
						"PARAMS" => $arParams
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?}

	}*/

	if (count($arrSort)>0){
	?>
	<div class="carousel carousel-tabs goods__carousel-tabs hero" id="list-additionals"  itemscope itemtype="http://schema.org/ItemList">
		<div class="container is-widescreen">
			<div class="level is-mobile carousel__title">
				<div class="level-left">
					<h2 class="is-size-3">Товары-аналоги</h2>
				</div>
			</div>
			<? /*if (count($arResult['ADDITIONAL']) > 1 && CClass::countItems($arResult['ADDITIONAL']) > 4):?>
			<section class="tabs goods__tabs">
				<div class="goods__tabs-button" id="goods__tabs-button-3"></div>
				<ul class="tabs__header" id="tabs__header-3">
					<li class="tabs__header--title js-tabs-title2 active first" data-tab="#tab-goods-add-1"><?=GetMessage('CT_ALL_GOODS')?></li>
				<?php
					$i = 2;
					foreach($arResult['ADDITIONAL'] AS $key => &$sec ):?>
					<li class="tabs__header--title js-tabs-title2" data-tab="#tab-goods_add_<?=$i;?>"><?=$key;?></li>
				<?php
					$i++;
					endforeach; ?>
				</ul>
				<div class="tabs__underline js-tabs-underline2"></div>
			</section>
			<? endif;
			*/?>
			<div class="content">
				<div class="content tabs__content js-tabs-content2 active" id="tab-goods-add-1">
					<div class="swiper-container swiper-container-hit2 preview-products" id="<?=$carouselId?>">
						<div class="swiper-wrapper">
						
						<?
						foreach ($arrSort as $key => $value) {
							$arItem = $newSort[$key];
							$ii ++;
				            if(strpos($_SERVER['HTTP_USER_AGENT'], 'bot') !== false && $ii > 4){ break; }
							if($ii > 8)
							{
								break;
							}
							?>
							<div class="swiper-slide">
							  <?$APPLICATION->IncludeComponent(
									"bitrix:catalog.item",
									"product",
									array(
										"RESULT" => array(
											"ITEM" => $arItem
										),
										"PARAMS" => $arParams
									),
									$component,
									array("HIDE_ICONS" => "Y")
								);?>
							</div><?
						}
						/*$ii = 0;
						foreach($arResult['ADDITIONAL'] AS $key => &$sec):
							foreach($sec AS &$arItem):
								$ii ++;
								if($ii > 8)
								{
									break 2;
								}?>
								<div class="swiper-slide">
								  <?$APPLICATION->IncludeComponent(
										"bitrix:catalog.item",
										"product",
										array(
											"RESULT" => array(
												"ITEM" => $arItem
											),
											"PARAMS" => $arParams
										),
										$component,
										array("HIDE_ICONS" => "Y")
									);?>
								</div>
							<?php
								endforeach;
							endforeach;*/ ?>
						</div>
					</div>
					<button id="<?=$leftArrowId?>" class="arrow left product-tabs-hit2-left"></button>
					<button id="<?=$rightArrowId?>" class="arrow right product-tabs-hit2-right custom-ajax-carousel-preload" data-id="<?=$carouselId?>" data-filter-key="ELEMENT" data-skip="2" data-pp="4" data-element="<?=$arResult['ID']?>" data-type="ADDITIONAL"></button>
					<div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
					<?/*<div class="more-mobile">
					  <a href="#" class="btn is-primary is-outlined">Смотреть все</a>
					</div>*/?>
				</div>
<?
$jsParams = array(
	"carouselSelector" => "#$carouselId",
	"leftArrowSelector" => "#$leftArrowId",
	"rightArrowSelector" => "#$rightArrowId",
	"showCount" => 4
);
?>
<script type="text/javascript">
	new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
	new Swiper('<?=$jsParams['carouselSelector']?>', {
	slidesPerView: 4,
	spaceBetween: 30,
	loop: false,
	//observer: true,
	//observeParents: true,
	navigation: {
		prevEl: '<?=$jsParams['leftArrowSelector']?>',
		nextEl: '<?=$jsParams['rightArrowSelector']?>',
	},
	breakpoints: {
		1200: {
		slidesPerView: 3
		},
		768: {
		spaceBetween: 0,
		slidesPerView: 3
		},
		550: {
		spaceBetween: 0,
		slidesPerView: 2
		}
	}
	});
</script>
	<?
		if (count($arResult['ADDITIONAL']) > 1 && CClass::countItems($arResult['ADDITIONAL']) > 4):
        ?>
                    <script id = "element_additionals_ajax">
                        $('.js-tabs-title2').click(function() {
                            if($($(this).data('tab')).length){ return; }
                            $.ajax({
                                type: "POST",
                                url: '<?=SITE_DEFAULT_PATH."/include/element_additionals_ajax.php"?>',
                                data: {
                                    tab: $(this).data('tab'),
                                    params: '<?=json_encode($arParams)?>', 
                                    carouselId: '<?=$carouselId?>', 
                                    leftArrowId: '<?=$leftArrowId?>', 
                                    rightArrowId: '<?=$rightArrowId?>', 
                                    mess: '<?=json_encode([
                                        'CT_MOVE_FINGER' => GetMessage('CT_MOVE_FINGER'), 
                                        'HDR_BUY_ONE_CLICK' => GetMessage('HDR_BUY_ONE_CLICK'),
                                        'CT_ADD_TO_COMPARE' => GetMessage('CT_ADD_TO_COMPARE'),
                                        'CT_GO_TO_COMPARE' => GetMessage('CT_GO_TO_COMPARE'),
                                        'CT_PRODUCT_CODE' => GetMessage('CT_PRODUCT_CODE'),
                                    ])?>', 
                                    id: '<?=$arResult['ID']?>', 
                                    prop1: '<?=json_encode($arResult['PROPERTIES']['ADDITIONAL']['VALUE'])?>', 
                                    prop2: '<?=$arResult['PROPERTIES']['ADDITIONAL_CACHE']['~VALUE']?>', 
                                }
                            }).done(function( msg ) {
                                //console.log(msg);
                                //return;
                                
                                $('#element_additionals_ajax').remove();
                                $('#list-additionals .content').first().append(msg);
                                
                                $(".podarok_list[data-key]").each(function() {
                                    $.ajax({
                                        type: "POST",
                                        url: '/ajax/podarok_list.php',
                                        data: {id: $(this).data('id'), key: $(this).data('key')}
                                    }).done(function( msg ) {
                                        var data = this.data.split("&");
                                        $(".podarok_list[data-"+data[1]+"]").append(msg);
                                        $(".podarok_list[data-"+data[1]+"]").removeAttr('data-key').removeAttr('data-id');
                                    });
                                });
                                document.querySelectorAll('.swiper-container').forEach((element) => {
                                    element.swiper.on('transitionEnd', function(){
                                        $(".one-click.buy--one-click--catalog:empty").each(function() { $(this).html('<?=GetMessage('HDR_BUY_ONE_CLICK')?>'); });
                                        $(".podarok_list[data-key]").each(function() {
                                            $.ajax({
                                                type: "POST",
                                                url: '/ajax/podarok_list.php',
                                                data: {id: $(this).data('id'), key: $(this).data('key')}
                                            }).done(function( msg ) {
                                                var data = this.data.split("&");
                                                $(".podarok_list[data-"+data[1]+"]").append(msg);
                                                $(".podarok_list[data-"+data[1]+"]").removeAttr('data-key').removeAttr('data-id');
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    </script><?        
		endif;?>
			</div>
		</div>
	</div>
	<?
	}
	?>

<? endif;?>