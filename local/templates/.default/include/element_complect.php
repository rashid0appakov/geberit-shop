<?
$uniqueId = $this->randString();
$carouselId = "carousel_$uniqueId";
$leftArrowId = "left_arrow_$uniqueId";
$rightArrowId = "right_arrow_$uniqueId";


?>
<? if (count($arResult['KOMLP']) > 0):
	/*foreach ($arResult['KOMLP'] as $key => $value) {
		foreach($value as $k => $arItem){
			$arrSortId[] = $k;
		}
		
		
	}
	
	$FieldSort = GetSortField();
	$arSelect = Array("ID", "NAME",$FieldSort);
	$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$arrSortId);
	$res = CIBlockElement::GetList(Array($FieldSort=>"desc", "SORT"=>"ASC"), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement())
	{
	 $arFields = $ob->GetFields();
	 $arrSort[$arFields['ID']] = $arFields['ID'];
	}
	*/
	?>
	<div class="carousel carousel-tabs goods__carousel-tabs hero" id="list-complects" itemscope itemtype="http://schema.org/ItemList">
		<div class="container is-widescreen">
			<div class="level is-mobile carousel__title">
				<div class="level-left">
					<h2 class="is-size-3">Добавить в комплект</h2>
				</div>
			</div>
			<? if (count($arResult['KOMLP']) > 1 && CClass::countItems($arResult['KOMLP']) > 4):?>
			<section class="tabs goods__tabs">
				<div class="goods__tabs-button" id="goods__tabs-button-2"></div>
				<ul class="tabs__header" id="tabs__header-2">
					<li class="tabs__header--title js-tabs-title-compl active first" data-tab="#tab-goods-1"><?=GetMessage('CT_ALL_GOODS')?></li>
				<?php
					$i = 2;
					foreach($arResult['KOMLP'] AS $key => &$sec ):?>
					<li class="tabs__header--title js-tabs-title-compl" data-tab="#tab-goods-<?=$i;?>"><?=$key;?></li>
				<?php
					$i++;
					endforeach; ?>
				</ul>
				<div class="tabs__underline js-tabs-underline-compl"></div>
			</section>
			<? endif;?>
			<div class="content">
				<div class="content tabs__content js-tabs-content-compl active" id="tab-goods-1">
					<div class="swiper-container swiper-container-hit-c preview-products" id="<?=$carouselId?>">
						<div class="swiper-wrapper">
						<?
                        $FieldSort = GetSortField();
						$ii = 0;
						$newSort = $arrSortId = $arrSort = array();
						foreach ($arResult['KOMLP'] as $key => $value) {
							foreach($value as $k => $arItem){
								$newSort[$k] = $arItem;
								$arrSortId[]=$k;
							}
						}
						if(count($arrSortId)>0){
							$arSelect = Array("ID", "NAME",$FieldSort);
							$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$arrSortId);
							$res = CIBlockElement::GetList(Array($FieldSort=>"ASC", "SORT"=>"ASC"), $arFilter, false, false, $arSelect);
							while($ob = $res->GetNextElement())
							{
							 $arFields = $ob->GetFields();
							 $arrSort[$arFields['ID']] = $arFields['ID'];
							}
						}
                        
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
						/*}else{
						foreach ($arResult['KOMLP'] as $key => $value) {
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
								$arItem = $arResult['KOMLP'][$key][$k];
																
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

						}

					}*/?>
						<?
						/*$ii = 0;
						foreach($arResult['KOMLP'] AS $key => &$sec):
							foreach($sec AS &$arItem):
								$ii ++;
								if($ii > 8)
								{ 
									break 2;
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
								</div>
							<?php
								endforeach;
							endforeach; */?>
						</div>
					</div>
					<button id="<?=$leftArrowId?>" class="arrow left product-tabs-hit-left"></button>
					<button id="<?=$rightArrowId?>" class="arrow right product-tabs-hit-right custom-ajax-carousel-preload" data-id="<?=$carouselId?>" data-filter-key="ELEMENT" data-skip="2" data-pp="4" data-element="<?=$arResult['ID']?>" data-type="KOMLP" aria-disabled="false"></button>
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
	<?php
		if (count($arResult['KOMLP']) > 1 && CClass::countItems($arResult['KOMLP']) > 4):
        ?>
                    <script id = "element_complect_ajax">
                        $('.js-tabs-title-compl').click(function() {
                            if($($(this).data('tab')).length){ return; }
                            $.ajax({
                                type: "POST",
                                url: '<?=SITE_DEFAULT_PATH."/include/element_complect_ajax.php"?>',
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
                                    prop1: '<?=json_encode($arResult['PROPERTIES']['KOMLP']['VALUE'])?>', 
                                    prop2: '<?=$arResult['PROPERTIES']['KOMLP_CACHE']['~VALUE']?>', 
                                }
                            }).done(function( msg ) {
                                $('#element_complect_ajax').remove();
                                $('#list-complects .content').first().append(msg);
                                
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
                    </script>
                    <?
		endif;?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('.js-tabs-title-compl.first').click();
			tabWidthCompl();

			/*Tabs */
			function tabWidthCompl() {
				var tabWidth = $('.js-tabs-title-compl.active').width();
				$('.js-tabs-underline-compl').css('width', tabWidth + 'px');
			};

			$('.js-tabs-title-compl').on('click', function () {
				var openTab = $(this).data('tab'),
				linePosition = $(this).position().left;

				$('.js-tabs-underline-compl').css('transform', 'translateX(' + linePosition + 'px)');
				$('.js-tabs-title-compl').removeClass('active');
				$(this).addClass('active');
				$('.js-tabs-content-compl').removeClass('active');
				$(openTab).addClass('active');
				tabWidthCompl();
			});

		});
	</script>
<? endif;?>