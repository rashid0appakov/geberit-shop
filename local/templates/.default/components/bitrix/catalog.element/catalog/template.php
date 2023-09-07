<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/*if ($USER->IsAdmin())
	die;/**/

$templateData['ITEM_INFO'] = [
	'ID' => $arResult['ID'],
	'NAME' => $arResult['NAME'],
	'PICTURE' => [
		'SMALL' => CFile::ResizeImageGet(($arResult['DETAIL_PICTURE']['ID']? : $arResult['PREVIEW_PICTURE']['ID']), ['width' => 35, 'height' => 35], BX_RESIZE_IMAGE_PROPORTIONAL, true),
		'MEDIUM' => CFile::ResizeImageGet(($arResult['DETAIL_PICTURE']['ID']? : $arResult['PREVIEW_PICTURE']['ID']), ['width' => 70, 'height' => 70], BX_RESIZE_IMAGE_PROPORTIONAL, true),
	],
	'URL' => $arResult['DETAIL_PAGE_URL'],
	'PRICE' => 0,
	'PRICE_OLD' => 0,
];
$templateData['robots'] = 'index, follow';


$isAdmin = false; // */$GLOBALS['USER']->IsAdmin();

//pr($arParams["CUSTOM_PROPERTY_CODE"]);
//pr($arResult['PROPERTIES']);

$this->setFrameMode(true);
$this->addExternalCss(SITE_DEFAULT_PATH.'/css/goods-card.css');

$jsParams = [];

// подсказки
$arSelect = Array("PREVIEW_TEXT", "NAME", "CODE");
$arFilter = Array("IBLOCK_ID"=>121, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$hints = array();
while($ob = $res->GetNextElement())
{
 $arFields = $ob->GetFields();
 $hints[$arFields['NAME']] = $arFields['PREVIEW_TEXT'];
 //print_r($arFields);
}

//var_dump($hints);


?>
<style>
.description__item-title.info__icon::before{margin-right:12px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-1.png")}
.description__item-title.check__icon::before{margin-right:12px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png")}
.description__item-title.car__icon::before{margin-right:12px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-3.png")}
.description__item-title.key__icon::before{margin-right:12px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-4.png")}
a.buy__icon::before{position:relative;top:3px;margin-right:15px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/basket.png")}
.title-bottom__guarantee::before{margin-right:15px;content:url("<?=SITE_DEFAULT_PATH?>/images/icons/guarantee.png")}
.tabs-table__row-column--left .info::before{content:"?"}
.finger-mobile::before{content:"<?=GetMessage('CT_MOVE_FINGER')?>";min-height:25px}
</style>
<?if($GLOBALS['USER']->IsAdmin() && SITE_ID != 'l1'){?>
	<div class="provider_info">
		<script type="text/javascript">
			var number = [], brand = '<?=$arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"]?>', a = 0;
		</script>
		<?
		if(count($arResult["SET_ITEMS"]) > 0){
			foreach($arResult["SET_ITEMS"] as $arItem){
				if($arItem['PRODUCT']['ARTNUMBER'] != ''){
					?>
					<script type="text/javascript">
						number[a] = '<?=$arItem['PRODUCT']['ARTNUMBER']?>';
						a++;
					</script>
					<?
				}
			}
		}
		elseif($arResult['PROPERTIES']['ARTNUMBER']['VALUE'] != ''){
			?>
			<script type="text/javascript">
				number[a] = '<?=$arResult['PROPERTIES']['ARTNUMBER']['VALUE']?>';
				a++;
			</script>
			<?
		}
		?>
		<script type="text/javascript">
			SC.provider_info(number, brand);
		</script>
		
		<span class="min_price"></span>
						
		<span class="all_prov btn" onclick="openRecallPopup()">Показать всех поставщиков</span>

		<div id="bx_recall_popup_form" style="display:none; padding:10px;min-height: 300px" class="bx_login_popup_form">
			<table class="provider_table">
				<tr>
					<th>
						Поставщик
					</th>
					<th>
						Артикул
					</th>
					<th>
						Цена
					</th>
					<th>
						Наличие
					</th>
					<th>
						Наличие (из прайс листа)
					</th>
					<th>
						Актуальность на
					</th>
				</tr>
			</table>
		</div>
	</div>
<?}?>
<?/*<div class="fast-links">
	<? if( count($arResult['COLLECTION']) > 0 ) { ?>
		<a href="#list-collections" class="description__link-2">Товары из коллекции</a>
	<? } ?>
	<? if( count($arResult['ADDITIONAL']) > 0 ) { ?>
		<a href="#list-additionals" class="description__link-2">Дополнительные товары</a>
	<? } ?>
	<? if( count($arResult['SPARE']) > 0 ) { ?>
		<a href="#list-spare" class="description__link-2">Запасные детали</a>
	<? } ?>
	<? if( count($arResult['PROPERTIES']['VIDEO']['VALUE']) > 0 && is_array($arResult['PROPERTIES']['VIDEO']['VALUE']) ) { ?>
		<a href="#list-video" class="description__link-2">Видеообзор</a>
	<? } ?>
</div>*/?>
<div class="goods__title-bottom">
	<div class="title-bottom title-bottom--column custom-live-ajax-update" id="custom-live-ajax-update-title-bottom-column">
		<?
		global $USER;

		if ($USER->IsAdmin()){
			if (strlen($arResult["PROPERTIES"]['session_key']['VALUE'])>0){
				?>
				<div class="title-bottom__vendor">ID обновления: <?=$arResult["PROPERTIES"]['session_key']['VALUE']?></div>
				<?
			}

			if (strlen($arResult["PROPERTIES"]['time_update']['VALUE'])>0){
				?>
				<div class="title-bottom__vendor">Дата обновления: <?=$arResult["PROPERTIES"]['time_update']['VALUE']?></div>
				<?
			}
			?>
			<?
		}
		?>
		<!-- <div class="title-bottom__vendor"><?=GetMessage('CT_PRODUCT_CODE')?>: <?=$arResult["ID"]?></div> -->
		<?php
			$frame = $this->createFrame()->begin('...');
			$frame->setAnimation(true);

/*
$stores_dictionary = array(
                "8f038b0f-b44b-11e9-929d-7824af46b558" => 4, //Склад виртуальный по Москве
                "989e6e57-b2ae-11e9-929d-7824af46b558" => 1, //Склад виртуальный по СПБ
                "aa7f5011-b52d-11e9-929d-7824af46b558" => 2, //Склад виртуальный по Екатеринбургу
);
*/

			$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");

			$STORE_ID = GetStoreId();
			$sale_price_id = GetSaleStoreId();

			$delivery_days_title =  '';
			$delivery_title = GetMessage('HDR_IN_STOCK');
			



			$set_q = array();

			$ar_res = CCatalogProduct::GetByIDEx($arResult["ID"]);
			$product_id = $ar_res["PRODUCT"]['ID'];

			$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
		        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
		        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
		    ));

		    $arResult['PRODUCT']['QUANTITY'] = 0;
		    while($arStoreProduct=$rsStoreProduct->fetch())
		    {
		        //var_dump($arStoreProduct);
		        $arResult['PRODUCT']['QUANTITY'] = $arStoreProduct['AMOUNT'];
		    }

			foreach($arResult["SET_ITEMS"] AS $arItem){
				//if ($_REQUEST['test'] == 'Y'){
					$product_id = $arItem["PRODUCT"]['ID'];

					$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
				        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
				        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
				    ));

				    while($arStoreProduct=$rsStoreProduct->fetch())
				    {
				        //var_dump($arStoreProduct);
				        $arItem['PRODUCT']['QUANTITY'] = $arStoreProduct['AMOUNT'];
				    }
		    	//}

		    	$set_q[] = intval($arItem['PRODUCT']['QUANTITY']);
			}

			if (is_array($set_q) && count($set_q)>0){
				$arResult["PRODUCT"]['QUANTITY'] = min($set_q);
			}

			// логика доставки 1-2-30-45-360 дней - начало

			$data = GetDeliveryDataForElementByArr($arResult);
			$delivery_title = $data['delivery_title'];
			$delivery_days_title = $data['delivery_days_title'];
			$peremeshen = $data['peremeshen'];
			$q = $data['q'];

			if ($q == 0){
				$arResult["PRODUCT"]['QUANTITY'] = 0;
			}
			// логика доставки 1-2-30-45-360 дней - конец

			$arPrice = $arResult["ITEM_PRICES"][0];
			$price = $arResult["PRICE"];
			$oldPrice = $arResult["BASE_PRICE"];

			// var_dump($delivery_title);
			// var_dump($delivery_days_title);
			if (strlen($delivery_title)>0){
				?>
				<style>
					.title-bottom__in.orange { color: orange; border-color: orange; }
					.title-bottom__in.orange::before { content: none !important; margin-right: 0px; }
				</style>
				<?
				if ($arResult["PRODUCT"]['QUANTITY']>0){
					?>
				<div class="title-bottom__in<?=$delivery_title=="Наличие уточняйте"?" orange":""?>"><?=$delivery_title?></div>
					<?
				}else{
					//var_dump($minPrice);
					if($minPrice > 0){
					?>
					<style>
						.title-bottom__in::before{
							content: none !important;
							margin-right: 0px;
						}
					</style>
					<div class="title-bottom__in" style="color:red; border: 1px solid red; ">&#10006; <?=$delivery_title?></div>
					<?
					}else{
					?>
						<div class="title-bottom__in<?=$delivery_title=="Наличие уточняйте"?" orange":""?>"><?=$delivery_title?></div>

					<?
//						<div class="title-bottom__vendor" style="color: rgb(89, 97, 104); width: 130px;">Наличие уточняйте</div>

					}
					?>
					
					<?
					/*
					?>
					<div class="title-bottom__vendor" style="color: rgb(89, 97, 104); width: 130px;">На центральном складе</div>
					<?
					*/
				}
				
			}
		?>
		<?/*if((SITE_ID != 'l1' && $arResult["PROPERTIES"]['PRICE_UPDATE'.CClass::getCurrentAvalCode()]['VALUE'] > 0) || (SITE_ID == 'l1' && $arResult["PRODUCT"]['QUANTITY'])){
			?>
			<div class="title-bottom__in"><?=GetMessage('HDR_IN_STOCK')?></div>
			<?
		}
		*/
		?>
		<?/*<div class="title-bottom__in mobile-show<?=$arResult["PRODUCT"]['AVAILABLE'] != 'Y' ? ' is-desabled' : ''?>" style="width: 150px;">
			<? if ($arResult["PRODUCT"]['AVAILABLE'] == 'Y'):?><?=GetMessage('HDR_IN_STOCK')?><?else:?><?=GetMessage('HDR_NOT_IN_STOCK')?><?endif;?>
		</div>*/
		
		?>
		<?
		if (strlen($delivery_days_title)>0){
			if ($delivery_title == 'В наличии'){
				if ($arResult["PRODUCT"]['QUANTITY']>0){ $delivery_days_title = '1-2 дня'; }	
			}
		?>
		<div class="title-bottom__delivery">
			<?
			//var_dump(days_str);
			if (strlen($delivery_title)>0){
				if ($arResult["PRODUCT"]['QUANTITY']>0){
				?>
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/truck.png" alt="car" /><?=GetMessage('CT_DELIVERY_LINE')?>: &nbsp;<span><?=$delivery_days_title?></span>
				<?
					//}
				} 
			}
			/*
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/truck.png" alt="car" /><?=GetMessage('CT_DELIVERY_LINE')?>: &nbsp;<span><?=GetMessage('CT_DELIVERY_DAYS')?></span>
			*/
			?><?/*Завтра, <?=$day;?> <?=$month;*/?>
		</div>
		<?
		}
		?>
		<? $frame->end();?>
	</div>
	<? if ($arResult["PROPERTIES"]["GUARANTEE"]['VALUE']):?>
	<div class="title-bottom title-bottom--column">
		<div class="title-bottom__guarantee">
			<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/guarantee.png" alt="guarantee"><?/**/?><?=GetMessage('CT_GARANTEE')?>: &nbsp;<?=CClass::getNormalValueProp($arResult["PROPERTIES"]["GUARANTEE"])?>
		</div>
	</div>
	<? endif;?>
<?/*
	<?
		$oldPrice = intval($arResult["MIN_PRICE"]['VALUE']);
	?>

	<div class="title-bottom title-bottom--column" style="visibility: hidden;">
		<div class="title-bottom__alfa">
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/alfa.png" alt="alfa">
			<a href="#">В рассрочку от <?=round(intval($oldPrice)/6);?> р./мес.</a>
		</div>
	</div>
*/?>
</div>
<div class="goods__description" itemscope itemtype="http://schema.org/Product">
	<meta itemprop="name" content="<?=$arResult["NAME"]?>">
	<meta itemprop="description" content="<?=$arResult["NAME"]?>">
	<div class="description__column--left">
		<div class="description__top-slider">
			<?/*if ($arResult["PROPERTIES"]["SHOWROOM"]["VALUE"] == "Y"):?>
				<a href="/catalog/showroom/" target="_blank" class="description__top-slider-link">Посмотреть в шоуруме</a>
			<?endif;/**/?>
			<div class="badges description__top-slider-badges">
				<?if ($arResult["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == "Y"):?>
					<span class="tag is-warning">Новинка</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["SALELEADER"]["VALUE"] == "Y"):?>
					<span class="tag is-success">Хит</span>
				<?endif;?>
				<?/*if ($arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == "Y"):?>
					<span class="tag is-danger">Скидка</span>
				<?endif;*/?>
				<?if ($arResult["PROPERTIES"]["RECOMEND"]["VALUE"] == "Y"):?>
					<span class="tag is-warning">Рекомендуем</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["SHOWROOM"]["VALUE"] == "Y"):?>
					<span class="tag is-link">Магазин</span>
				<?endif;?>
<?
			$arResult['SEO_MIN_PRICE'] = $minPrice = intval($arResult["MIN_PRICE"]['DISCOUNT_VALUE']);

			$arResult["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'N';
		    if (($ar_res['PRICES'][$sale_price_id]['PRICE'] > 0) && ($ar_res['PRICES'][$sale_price_id]['PRICE'] > $minPrice)){
				$arResult["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'Y';
		    	$oldPrice = $ar_res['PRICES'][$sale_price_id]['PRICE'];
		    }

?>

				<?if ($arResult["PROPERTIES"]["SALEGOODS"]["VALUE"] == "Y"):?>
					<span class="tag is-danger">Распродажа</span>
				<?endif;?>
				<?
				$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
				if(!$geo_id>0){$geo_id=129;}
				if (($geo_id == 129 && $arResult["PROPERTIES"]["EXPRESS_DELIVERY"]["VALUE"]=='Y')){
                    ?><span class="tag expressBtn">Экспресс<div class="descr" data-loaded="false"></div></span><?
                    //include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/express_delivery.php');
				}
				if (($geo_id == 817 && $arResult["PROPERTIES"]["EXPRESS_DELIVERY_P"]["VALUE"]=='Y')){
                    ?><span class="tag expressBtn">Экспресс<div class="descr" data-loaded="false"></div></span><?
                    //include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/express_delivery.php');
				}
				?>
				
			</div>
		<? if (!empty($arResult["PHOTO"])):?>
			<div class="slider-for">
				<?
				$i=0;
				foreach ($arResult["PHOTO"] AS $key => &$arItem):
					$i++;
					?>
					<div class="slide-item">
						<?
							$filename =$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($key);
							$info = CFile::GetFileArray($key);
							
							/*global $USER;
							if ($USER->IsAdmin()){*/
								$fil = \Bitrix\Main\IO\Path::getExtension($filename);
								$temp_name = $info["FILE_NAME"].'$$';
								$path =  $_SERVER["DOCUMENT_ROOT"].'/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;
								$img_nm = '/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;
							/*}else{
								$tmp = explode('.', $info["FILE_NAME"]);
								$path = $_SERVER["DOCUMENT_ROOT"].'/upload/'.$info["SUBDIR"].'/'.$tmp[0].'_1920.'.$tmp[1];
								$img_nm = '/upload/'.$info["SUBDIR"].'/'.$tmp[0].'_1920.'.$tmp[1];
							}*/
							if (!file_exists($path)) {
								$image = new SimpleImage();
							    $image->load($filename);
							    $image->resizeToWidth(1920);
							    $image->save($path);
							}
							?>
						<img itemprop="image" src="<?=$img_nm?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>"  class="lzy_img" data-src="<?=$arItem['MEDIUM']['src']?>" srcset="<?=$arItem['MEDIUM']['src']?> 300w" decode="async" sizes="100vw"/>
						<?/*<img src="<?=$arItem['MEDIUM']['src']?>" class="lzy_img" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" />*/?>
					</div>
				<?endforeach;?>
				<?if(!empty($arResult['PROPERTIES']['YOUTUBE']['VALUE']) and is_array($arResult['PROPERTIES']['YOUTUBE']['VALUE'])):?>
					<?foreach($arResult['PROPERTIES']['YOUTUBE']['VALUE'] as $key=>$val):?>
						<div class="slide-item main-img__youtube" data-youtube="<?=$val?>">
							<a class="fancybox-media" href="https://www.youtube.com/watch?v=<?=$val?>"><img src="//img.youtube.com/vi/<?=$val?>/hqdefault.jpg" /></a>
						</div>
					<?endforeach?>
				<?endif?>
			</div>
			<style type="text/css">.small_icon{width: 67px;
    height: 67px;
    object-fit: contain;
    background-position: center;
    background-size: 100% !important;}
    .slider-for .slick-slide img, .slider-for .slide-item img{
    	    height: auto;
    width: auto;
    object-fit: contain;
    margin: 0 auto;
    max-width: 100%;
    max-height: 100%;
    }
</style>
			<? if (count($arResult["PHOTO"]) > 1):?>
			<div class="slider-nav" data-previous-image-url="<?=$templateFolder?>/images/top-sleder__left-arrow.png" data-next-image-url="<?=$templateFolder?>/images/top-sleder__right-arrow.png">
				<? foreach($arResult["PHOTO"] AS &$arItem):?>
					<div class="slide-nav-item">
						<div class="small_icon" style="background: url(<?=$arItem['SMALL']['src']?>)no-repeat;"></div>
					</div>
				<? endforeach;?>
				<?if(!empty($arResult['PROPERTIES']['YOUTUBE']['VALUE']) and is_array($arResult['PROPERTIES']['YOUTUBE']['VALUE'])):?>
					<?foreach($arResult['PROPERTIES']['YOUTUBE']['VALUE'] as $key=>$val):?>
						<div class="slide-nav-item click-youtube" data-youtube="<?=$val?>">
							<a class="fancybox-media" href="https://www.youtube.com/watch?v=<?=$val?>"><img src="//img.youtube.com/vi/<?=$val?>/default.jpg" /></a>
						</div>
					<?endforeach?>
				<?endif?>
			</div>
			<? endif;?>
		<? endif;?>
		</div>
	</div>
	<div class="description__column--right">
		<?
		//Если снят с производства
		if($arResult["PROPERTIES"]["DISCONTINUED"]["VALUE"] == "Y"){
			?>
			<div class="buy__item buy__item-buy--mobile discontinued discontinued--mobile">
				Снят с производства
			</div>
			<?
		}
		else
		{
			?>
			<?php
				$frame=$this->createFrame()->begin('...');
				$frame->setAnimation(true);
			?>
			<?php
			if(!$arResult["MIN_PRICE"]['CURRENCY']){
				$arResult["MIN_PRICE"]['CURRENCY']='RUB';
			}
			$arResult['SEO_MIN_PRICE'] = $minPrice = intval($arResult["MIN_PRICE"]['DISCOUNT_VALUE']);
			$oldPrice = intval($arResult["MIN_PRICE"]['VALUE']);
			if ($minPrice == $oldPrice){
				$difference = 0;
				$oldPrice = 0;
				
				if(!empty($arResult["PROPERTIES"]["OLD_PRICE"]["VALUE"])){
					$oldPrice = $arResult["PROPERTIES"]["OLD_PRICE"]["VALUE"];
					$difference = $oldPrice - $minPrice;
				}
			}else{
				$difference = $oldPrice - $minPrice;
			}
			$templateData['ITEM_INFO']['PRICE'] = customFormatPrice(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']));
			if($oldPrice)
			{
				$templateData['ITEM_INFO']['PRICE_OLD'] = customFormatPrice(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']));
			}
			$oldPrice = false;
			    $ar_res = CCatalogProduct::GetByIDEx($arResult["ID"]);
				$product_id = $ar_res["PRODUCT"]['ID'];

				$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'N';

			    if (($ar_res['PRICES'][$sale_price_id]['PRICE'] > 0) && ($ar_res['PRICES'][$sale_price_id]['PRICE'] > $price)){
					$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'Y';
			    	$oldPrice = $ar_res['PRICES'][$sale_price_id]['PRICE'];
					$arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);
					$difference = $oldPrice -$minPrice;
					if($difference<=0){$oldPrice = false;}
			    }
			?>
			<script type="text/javascript">
				var products_rasrochka = {}, mode = 'ONE';
			</script>
			<script type="text/javascript">
				products_rasrochka[0] = {name: '<?=htmlspecialchars($arResult['NAME'], ENT_QUOTES)?>', quantity: '1', price: '<?=$minPrice?>', id: '<?=$arResult['ID']?>'};
			</script>
			<div class="buy__item buy__item-buy--mobile">
				<?
				if ($arResult["DISCOUNT_BASKET"]["VALUE"]>0){
					$oldPrice = $minPrice;
					$minPrice = $minPrice*(1-$arResult["DISCOUNT_BASKET"]["VALUE"]/100);
					$difPrice = $arResult["DISCOUNT_BASKET"]["VALUE"];
				} elseif($arResult["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]>0){
					$oldPrice = $arResult["MIN_PRICE"]["VALUE"];
					$difPrice = $arResult["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"];
				} elseif($oldPrice){
					$difPrice = round(($minPrice-$oldPrice)/$oldPrice*100);
				}
				?>
				<? if ($oldPrice): ?>
					<div class="buy__item-economy"><?=GetMessage('CT_YOU_SAVE_TITLE')?>: <span><?=customFormatPrice(CurrencyFormat(abs($difference), $arResult["MIN_PRICE"]['CURRENCY']))?></span></div>
				<? endif ?>
				<div class="buy__item-price">
					<? if ($oldPrice): ?>
					<span class="buy__item-price-old"><?=customFormatPriceClear(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></span>
					<? endif ?>
					<div class="buy__item-price-new"><?=customFormatPriceClear(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></div>
				</div>
				
			</div>
			<? if ($minPrice):?>
			<div class="buy__item buy__item-buy--mobile">
				<?
				if($arResult["PROPERTIES"]["SYS_UNDER_THE_ORDER"]["VALUE"] == "Y")
				{
				?>
					<button class="btn is-primary buy-button buy--catalog btn-under-the-order" data-id="<?=$arResult["ID"]?>" >
						Под заказ
					</button>
				<?
				}
				else
				{
				?>
					<a href="<?=$arResult["DETAIL_PAGE_URL"]?>#buy" class="buy__icon btn is-primary buy-button buy__item-button buy__item-button--blue<?if($minPrice>0){?> add-to-basket<?}else{?> btn-grey<?}?> buy--item drvt-m10" data-id="<?=$arResult["ID"]?>" <?if($minPrice<=0){?>onclick="return false;"<?}?>>
								<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>" /><?/**/?><?=GetMessage('CT_BUY_BUTTON')?>
							</a>
					
					<div class="level-right buy__item-button">
						<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
					</div>
					
					<?
                    if(СREDIT_ENABLE && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 200000){?>
						<div class="level-right buy__item-button button_credit">
							<a href="#" onclick="buyRasrochka(products_rasrochka, event, mode)" class="btn is-primary is-outlined" data-id="<?=$arResult['ID'];?>">Купить в рассрочку</a>
						</div>
						<?
					}?>
				<?
				}
				?>
			</div>
			<? endif;?>
			<? $frame->end();?>
			<?
		}
		?>
		<div class="description__list" data-nosnippet>
			<?
			$gifts=CGifts::getGifts([$arResult["ID"]]);
			if(count($gifts[$arResult["ID"]])>0)
			{
			?>
				<div class="podarok_detail">
					<h4>Добавьте товар в корзину и получите один из подарков <span class="goto-star">*</span>:</h4>
					<?foreach ($gifts[$arResult["ID"]] as $id => $gift) {
						?>
						<div class="item">
							<?if(intval($gift["DETAIL_PICTURE"])>0){
								$pic=CGifts::getPreviewPhoto($gift["DETAIL_PICTURE"]);
								?>
								<a target="_blank" href="<?=$gift["DETAIL_PAGE_URL"]?>" class="img"><img src="<?=$pic?>" alt="<?=$gift["NAME"]?>"></a>
							<?}?>
				            <a target="_blank" href="<?=$gift["DETAIL_PAGE_URL"]?>" class="name"><?=$gift["NAME"]?></a>
						</div>
						<?
					}?>
				</div>
			<?
			}?>
			<style>
				.gift_lampochki .save-img{width: 10%;}
				.gift_lampochki .save-txt{width: 85%;font-size: 13px !important;line-height: 1.5;}
				.description__list .save-item{padding: 25px 0 25px !important;}				
			</style>
			<div class="title-bottom__vendor" style="margin: 15px 0;"><?=GetMessage('CT_PRODUCT_CODE')?>: <?=$arResult["ID"]?></div>
<?/*
			<div class="description__item save-item">
				<div class="gift_lampochki">
					<span class="gift_lampochki-img save-img"><img src="<?=SITE_DEFAULT_PATH?>/images/i_save_a.jpg" ></span>
					<span class="gift_lampochki-text save-txt">Бесплатное хранение вашего заказа на весь период карантина. <a href="/safekeeping/">Подробнее</a></span>
				</div>
			</div>
*/?>
			<?
			if(!empty(GIFT_BRANDS) && in_array($arResult['PROPERTIES']['MANUFACTURER']['VALUE'], GIFT_BRANDS) && intval($arResult["MIN_PRICE"]['DISCOUNT_VALUE']) > 2000){
				?>
				<div class="description__item">
					<div class="gift_lampochki">
						<span class="gift_lampochki-img"><img src="<?=SITE_DEFAULT_PATH?>/images/gift_lampochki.png" height='50' width='50'></span>
						<span class="gift_lampochki-text">Комплект светодиодных ламп в подарок</span>
					</div>
				</div>
				<?
			}
			?>
			<?php if ($arResult['SHOW_IN_DESCRIPTION']): ?>
			<div class="description__item">
				<div class="description__item-title info__icon">
					<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-1.png" alt="i" /><?/**/?><?=GetMessage('CT_OPTIONS')?></div>
				<ul class="description__item-title-list">
					<?foreach($arResult['SHOW_IN_DESCRIPTION'] as $name=>$value):
						if (!$value)
						{
							continue;
						}
						?>
						<li>• <strong><?=CClass::getNormalNameProp($name)?></strong>: <?=$value?></li>
					<? endforeach; ?>
				</ul>
				<div class="all-props-detail">
					<a href="#goods_tabs" class="description__link description__link-1" id="description__link-1"><?=GetMessage('CT_ALL_OPTIONS')?></a>
				</div>
			</div>
			<?php endif; ?>
			<? if( $arResult['ODDS'] ) { ?>
				<div class="description__item">
					<div class="description__item-title check__icon">
						<?/*/?><img src="<?=SITE_DEFAULT_PATH;?>/images/icons/description-title-img-2.png" alt="i" /><?/**/?>
						Особенности
					</div>
					<div class="description__item-img--container">
						<? foreach( $arResult['ODDS'] as $item ) { ?>
							<div class="description__item-img product__comparison-tooltip tooltip is-tooltip-bottom" data-tooltip="<?=$item['NAME'];?>">
								<img src="<?=$item['PREVIEW_PICTURE'];?>" alt="bascket" />
							</div>
						<? } ?>
					</div>
				</div>
			<? } ?>


			<? if ($arResult["SET_ITEMS"]):?>
				<div class="description__item">
					<div class="description__item-title check__icon">
						<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i" /><?/**/?><?=GetMessage('CT_KOMPL_TITLE')?>
					</div>
					<div class="description__item-img--container set-item">
						<ul class="description__item-title-list">
						<? foreach($arResult["SET_ITEMS"] AS &$arItem):?>
							<li>
								<? if (!empty($arItem['PRODUCT']['IMAGE'])):?>
								<div class="complekt-img">
									<a href="<?=$arItem['PRODUCT']['URL'];?>" target="_blank" title="<?=$arItem['PRODUCT']['NAME'];?>">
										<img src="<?=$arItem['PRODUCT']['IMAGE']['src']?>" alt="<?=$arItem['PRODUCT']['NAME'];?>" />
									</a>
								</div><? endif;?>
								<a href="<?=$arItem['PRODUCT']['URL'];?>" target="_blank" title="<?=$arItem['PRODUCT']['NAME'];?>"><?=$arItem['PRODUCT']['NAME'];?></a>
							</li>
						<? endforeach; ?>
						</ul>
					</div>
				</div>
			<? endif;?>
			<?if ($arResult["PARAMS_PROPERTIES"]):?>
				<div class="description__item">
					<div class="description__item-title check__icon">
						<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i" /><?/**/?><?=GetMessage('CT_ODDS_TITLE')?></div>
					<div class="description__item-img--container">
						<?foreach ($arResult["PARAMS_PROPERTIES"] as $param):?>
							<div class="description__item-img product__comparison-tooltip tooltip is-tooltip-bottom" data-tooltip="<?=$param["NAME"]?>">
								<img src="<?=$param["IMAGE"]?>" alt="bascket">
							</div>
						<?endforeach;?>
					</div>
				</div>
			<?endif;?>


			<div class="description__item">
				<?/*<div id="delivery_container" data-id="<?=$arResult['ID']?>">
				</div>*/?>
				<?
				$deliveryContent = '';
				$price = $arResult["MIN_PRICE"]["DISCOUNT_VALUE"];
				include($_SERVER['DOCUMENT_ROOT'].'/local/templates/.default/include/catalog_delivery.php');?>
				<?$deliveryContent = str_replace(['class="description__item-title"','<img src="/local/templates/.default/images/icons/description-title-img-3.png" alt="i">'],['class="description__item-title car__icon"',''],$deliveryContent)?>
				<?echo $deliveryContent;?>
				<a href="/delivery/" class="description__link description__link-2" target="_blank">Подробнее о доставке</a>
			</div>

			<?php
			if ($arResult['INSTALLATION_PRICE']):
			?>
			<div class="description__item">
				<div class="description__item-title key__icon"><?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-4.png" alt="i"><?/**/?>Монтаж</div>
				<ul class="description__item-title-list">
					<li>Стоимость установки: <span><?= $arResult['INSTALLATION_PRICE'] ?> <i class="znakrub">c</i></span></li>
				</ul>
				<a href="#goods_tabs" class="description__link description__link-3">Подробнее об услуге</a>
			</div>
			<?php endif; ?>

			<?/*?>
			<? if( count($arResult['COLLECTION']) > 0 ) { ?>

				<div class="description__item etc">
					<div class="description__item-title">
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i">
						<a href="#list-collections" class="description__link-2">Товары из коллекции</a>
					</div>

				</div>

			<? } ?>


			<? if( count($arResult['ADDITIONAL']) > 0 ) { ?>

				<div class="description__item etc">
					<div class="description__item-title">
						<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i">
						<a href="#list-additionals" class="description__link-2">Дополнительные товары</a>
					</div>

				</div>

			<? } ?>


			<? if( count($arResult['SPARE']) > 0 ) { ?>

				<div class="description__item etc">
					<div class="description__item-title">
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i">
						<a href="#list-spare" class="description__link-2">Запасные детали</a>
					</div>

				</div>

			<? } ?>


			<? if( count($arResult['PROPERTIES']['VIDEO']['VALUE']) > 0 && is_array($arResult['PROPERTIES']['VIDEO']['VALUE']) ) { ?>

				<div class="description__item etc">
					<div class="description__item-title">
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i">
						<a href="#list-video" class="description__link-2">Видеообзор</a>
					</div>

				</div>

			<? } ?>
			<?*/?>
		</div>

		<div class="goods__top-slider-buy">

			<div class="buy">

				<?
				if($arResult["PROPERTIES"]["DISCONTINUED"]["VALUE"] == "Y")
				{
									$arItem["PRODUCT"]["QUANTITY"] = null;

					?>
					<div class="buy__item buy__item-buy--desktop discontinued">
						Снят с производства
					</div>
					<?
				}
				else{
					?>
					<?php
						$frame=$this->createFrame()->begin('...');
						$frame->setAnimation(true);
					?>
					<div class="buy__item buy__item-buy--desktop">
						<?
						if ($arResult["DISCOUNT_BASKET"]["VALUE"]>0){
							$oldPrice = $minPrice;
							$minPrice = $minPrice*(1-$arResult["DISCOUNT_BASKET"]["VALUE"]/100);
							$difPrice = $arResult["DISCOUNT_BASKET"]["VALUE"];
						} elseif($arResult["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]>0){
							$oldPrice = $arResult["MIN_PRICE"]["VALUE"];
							$difPrice = $arResult["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"];
						} elseif($oldPrice){
							$difPrice = round(($minPrice-$oldPrice)/$oldPrice*100);
						}
						?>
						<? if ($oldPrice): ?>
						<div class="buy__item-economy"><?=GetMessage('CT_YOU_SAVE_TITLE')?>: <span><?=customFormatPrice(CurrencyFormat(abs($difference), $arResult["MIN_PRICE"]['CURRENCY']))?></span></div>
						<? endif ?>
						<div class="buy__item-price">
							<?/* if ($oldPrice): ?>
							<span class="buy__item-price-old"><?=customFormatPriceClear(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></span>
							<? endif /**/?>
							<div class="buy__item-price-new"><?=customFormatPriceClear(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></div>
							<? if ($oldPrice): ?>
							<span class="buy__item-price-old"><?=customFormatPriceClear(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></span>
							&nbsp;&nbsp;<font style="background-color: yellow;"><?=$difPrice?> %</font>
							<? endif ?>
							<span style="display:none;">RUB</span>
						</div>
						<?

				$days_str = '1-3 дня';
				if((date('w')==5) && date('H'>=16)){
					$days_str = '3-5 дней';
				}elseif(date('w')==6){ // суббота
					$days_str = '2-4 дня';
				}/*elseif (date('w')==0){ // воскресенье
					$days_str = '1-3';
				}
				*/
				if ($_GET['test'] == 'Y'){
					$brand_data = array();
					if ($arResult["PROPERTIES"]["MANUFACTURER"]['VALUE']>0){
						$brand_data = GetIBlockElement($arResult["PROPERTIES"]["MANUFACTURER"]['VALUE']);
					}

					$from_days = $brand_data["PROPERTIES"]['DAYS_DELIVERY_FROM']['VALUE'];
					if ($from_days <= 0){
						$from_days = 1;
					}
					$to_days = $brand_data["PROPERTIES"]['DAYS_DELIVERY_TO']['VALUE'];
					if ($to_days<0){
						$to_days = 3;
					}					
					//var_dump($brand_data["PROPERTIES"]['DAYS_DELIVERY_TO']['VALUE']);

					$days_str = $from_days.'-'.$to_days.' дня';
					if((date('w')==5) && date('H'>=16)){
						$from_days2 = $from_days+2;
						$to_days2 = $to_days+2;
						$days_str = $from_days2.'-'.$to_days2.' дней';

					}elseif(date('w')==6){ // суббота
						$from_days2 = $from_days+1;
						$to_days2 = $to_days+1;
						$days_str = $from_days2.'-'.$to_days2.' дней';

					}
					//die;
				}
				if (($geo_id == 129 && $arResult["PROPERTIES"]["EXPRESS_DELIVERY"]["VALUE"]=='Y')){
					$datetime = new DateTime('tomorrow');
					$tomorrow = $datetime->format('d.m');
					$datetime = new DateTime();
					$now =  $datetime->format('d.m');
					$h = date('H');
					
					if($h>=11){
						$days_str = 'завтра ('.$tomorrow.')';
					}else{
						$days_str = 'сегодня ('.$now.')';
					}
					$dn = date("w",mktime (0, 0, 0, date('m'), date('d'), date('Y')));
					$dop_text = '<br> Самовывоз: ';
					if($dn>=1 && $dn<=5 && $h <18 )//ПН-ПЦ до 16:00 выводим "Сегодня (дата)"
					{ $dop_text .= $now; }
					if($dn>=1 && $dn<=4 && $h >=18 )//ПН-ЧТ после 16:00 выводим "Завтра (дата)"
					{ $dop_text .= $tomorrow;}
					if($dn==5 && $h >=18 )//ПЦ после 16:00 выводим "В понедельник (дата)"
					{ $dp = date("d.m", mktime(0, 0, 0, date('m'), date('d') + 3, date('Y')));
						$dop_text .= $dp;}
					if($dn==6 && $h >=18 )//Сб весь выводим "В понедельник (дата)""
					{ $dp = date("d.m", mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')));
						$dop_text .= $dp;}
					if($dn==0 )//Вс выводим "Завтра (дата)"
					{ $dop_text .= $tomorrow;}
					$dop_text .= ' до 18-00';
					?>
					<div class="buy__item-economy" style="margin-top: 5px;width: 100%; height:40px">Cрок доставки <?=$days_str?> <?=$dop_text?></div>
					<?
				}
				elseif (($geo_id == 817 && $arResult["PROPERTIES"]["EXPRESS_DELIVERY_P"]["VALUE"]=='Y')){
					$datetime = new DateTime('tomorrow');
					$tomorrow = $datetime->format('d.m');
					$datetime = new DateTime();
					$now =  $datetime->format('d.m');
					$h = date('H');
					
					if($h>=11){
						$days_str = 'завтра ('.$tomorrow.')';
					}else{
						$days_str = 'сегодня ('.$now.')';
					}
					$dn = date("w",mktime (0, 0, 0, date('m'), date('d'), date('Y')));
					$dop_text = '<br> Самовывоз: ';
					if($dn>=1 && $dn<=5 && $h <18 )//ПН-ПЦ до 16:00 выводим "Сегодня (дата)"
					{ $dop_text .= $now; }
					if($dn>=1 && $dn<=4 && $h >=18 )//ПН-ЧТ после 16:00 выводим "Завтра (дата)"
					{ $dop_text .= $tomorrow;}
					if($dn==5 && $h >=18 )//ПЦ после 16:00 выводим "В понедельник (дата)"
					{ $dp = date("d.m", mktime(0, 0, 0, date('m'), date('d') + 3, date('Y')));
						$dop_text .= $dp;}
					if($dn==6 && $h >=18 )//Сб весь выводим "В понедельник (дата)""
					{ $dp = date("d.m", mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')));
						$dop_text .= $dp;}
					if($dn==0 )//Вс выводим "Завтра (дата)"
					{ $dop_text .= $tomorrow;}
					$dop_text .= ' до 18-00';
					?>
					<div class="buy__item-economy" style="margin-top: 5px;width: 100%; height:40px">Cрок доставки <?=$days_str?> <?=$dop_text?></div>
					<?
				}/*else{
				?>
				<div class="buy__item-economy" style="margin-top: 5px;">Cрок доставки <?=$days_str?></div>
				<?}*/?>
						<?
                        /*if ($arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y'):?>
						<div class="title-bottom__alfa">
							<img src="<?=SITE_DEFAULT_PATH?>/images/icons/alfa.png" alt="alfa" />
							<p><?=GetMessage('CT_INSTALLMENT_PLAN', ['#PRICE#' => customFormatPrice(CurrencyFormat( round($minPrice / 4), $arResult["MIN_PRICE"]['CURRENCY']))])?></p>
						</div>
						<? endif;*/?>

                        <?
                        if ($arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y'):?>
                            <div class="title-bottom__alfa">
                                <img src="<?=SITE_DEFAULT_PATH?>/images/icons/alfa.png" alt="alfa" />
                                <p><?=GetMessage('CT_INSTALLMENT_PLAN', ['#PRICE#' => customFormatPrice(CurrencyFormat( round($minPrice / 4), $arResult["MIN_PRICE"]['CURRENCY']))])?></p>
                            </div>
                        <? endif;?>

					</div>

					<? if ($minPrice):?>
					<meta itemprop="brand" content="<?=$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arResult["PROPERTIES"]["MANUFACTURER"]['VALUE']]['NAME']?>">
					<div class="buy__item buy__item-buy--desktop" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?$date1 = date('Y-m-d');
						$dir = $APPLICATION->GetCurDir();
						?>
						<meta itemprop="price" content="<?=intval(str_replace(' ', '', $minPrice))?>">
						<meta itemprop="priceCurrency" content="RUB">
						<?if($delivery_title=='В наличии'){?>
						<meta itemprop="availability" content="http://schema.org/InStock" />
						<?}else{?>
							<meta itemprop="availability" content="http://schema.org/InStock" />
						<?}?>
						<meta itemprop="url" content="<?=$dir?>">
						<meta itemprop="priceValidUntil" content="<?=$date1?>T23:59">
						<?
						if($arResult["PROPERTIES"]["SYS_UNDER_THE_ORDER"]["VALUE"] == "Y")
						{
						?>
							<button class="btn is-primary buy-button buy--catalog btn-under-the-order" data-id="<?=$arResult["ID"]?>" >
								Под заказ
							</button>
						<?
						}
						else
						{
						?>
						<?if($arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"):?>
							<div class="sale_section available_section_cat">
								<i class="check-circle circle-disc"></i> Скидка при заказе онлайн <span class="bold"><?=$arResult["DISCOUNT_BASKET"]["VALUE"]?>% <span class="goto-star">*</span></span>
							</div>
						<?endif?>
							<a href="<?=$arResult["DETAIL_PAGE_URL"]?>#buy" class="buy__icon btn is-primary buy-button buy__item-button buy__item-button--blue<?if($minPrice>0){?> add-to-basket<?}else{?> btn-grey<?}?> buy--item drvt-m10" data-id="<?=$arResult["ID"]?>" <?if($minPrice<=0){?>onclick="return false;"<?}?>>
								<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>" /><?/**/?><?=GetMessage('CT_BUY_BUTTON')?>
							</a>
							<div class="level-right buy__item-button drvt-m10">
								<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
							</div>
							<?
                            if(СREDIT_ENABLE && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 200000){?>
								<div class="level-right buy__item-button button_credit">
									<a href="#" onclick="buyRasrochka(products_rasrochka, event, mode)" class="btn is-primary is-outlined" data-id="<?=$arResult['ID'];?>">Купить в рассрочку</a>
								</div>
								<?
							}?>
						<?
						}
						?>
					</div>
					<? endif;?>
					<? $frame->end();?>
					<?
				}
				?>
	
					<?/*
				<div class="buy__item middle-header callback">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DEFAULT_PATH."/include/catalogElement_callback.php"
						),
						false
					);?>
				</div>*/?>
				<div class="buy__item middle-header buy__item--diff">
					<div class="diff">
						<a href="#" class="<?=(CClass::isAddedToCompare($arResult['ID']) ? "compare-added" : '')?>
							icon-diff-big button tooltip is-tooltip-bottom buy__item-diff" data-id="<?=$arResult["ID"]?>"
							data-tooltip="<?=GetMessage(CClass::isAddedToCompare($arResult['ID']) ? 'CT_GO_TO_COMPARE' : 'CT_ADD_TO_COMPARE')?>">
						<span class="icon-diff">
							<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
							<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
							<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
							<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
							</svg>
						</span>
						<span class="buy__item-diff-link"><?=GetMessage(CClass::isAddedToCompare($arResult['ID']) ? 'CT_GO_TO_COMPARE' : 'CT_ADD_TO_COMPARE')?></span>
						<!-- <a href="#" class="buy__item-diff-link">Добавить к сравнению</a> -->
						<!-- <span class="tag is-warning">99</span>"is-disabled" class for empty diff list -->
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_analogs.php');?>

<?
//var_dump($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_complect.php');
?>
<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_complect.php');?>
<?
?>
	<?
	//Определяемся с объемом поставки
	$arScope = array();
	if(!empty($arResult['SCOPE_DEL']))
	{
		$arScopeCnt = count($arResult['SCOPE_DEL']);
		foreach($arResult['SCOPE_DEL'] as $key => $arItemScope){
			$arScope[$key]['NAME'] = $arItemScope['NAME'];
			$arScope[$key]['URL'] = $arItemScope['URL'];
			$arScope[$key]['PRICE'] = $arItemScope['PRICE'];
			$arScope[$key]['STR_SCOPE'] = $arItemScope['STR_SCOPE'];
			$arScope[$key]['PREVIEW_PICTURE'] = $arItemScope['PREVIEW_PICTURE'];
		}
	}
	else
	{
		$arScopeCnt = 0;
		$arScope[$arResult['ID']]['NAME'] = $arResult['NAME'];
		$arScope[$arResult['ID']]['URL'] = $arResult['DETAIL_PAGE_URL'];
		$arScope[$arResult['ID']]['PRICE'] = $arResult["ITEM_PRICES"][0]["PRICE"];
		$arScope[$arResult['ID']]['STR_SCOPE'] = $arResult['PROPERTIES']['SOSTAV_TOVARA']['VALUE'];
		$arScope[$arResult['ID']]['PREVIEW_PICTURE'] = $arResult['PREVIEW_PICTURE']['ID'];
	}
	//pr($arScope);
	?>

	<div class="container is-widescreen goods__tabs--wrap" id="goods_tabs">
		<div class="level is-mobile carousel__title">
			<div class="level-left">
				<h2 class="is-size-3">Описание и характеристики</h2>
			</div>
		</div>
		<section class="tabs goods__tabs goods__tabs-table-title">
			<div class="goods__tabs-button" id="goods__tabs-button-1"></div>
			<ul class="tabs__header" id="tabs__header-1">
				<li class="tabs__header--title js-tabs-title js-tabs-props active" data-tab="#tab-1">Характеристики</li>

				<? if( count( $arResult['DOCS'] ) > 0 ) { ?>
					<li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#tab-2">Инструкции и документация</li>
				<? } ?>

				<li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#tab-set">Объем поставки<?if ($arScopeCnt):?> (<?=count($arScope)?>)<?endif;?></li>

				<?/*
				<li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#delivery-and-payment" id="delivery-and-payment-click">Доставка и оплата</li>

                <li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#_reviews" id="reviews">Отзывы</li>
				<li class="tabs__header--title js-tabs-title" data-tab="#tab-5">Монтаж</li>
				
                                <?if ( $_SERVER["SERVER_NAME"] == "shop-gr.ru" ):?>
                                <li class="tabs__header--title js-tabs-title" data-tab="#tab-otzivy">Отзывы</li>
                                <?endif;?>
				*/?>

			</ul>
			<div class="tabs__underline js-tabs-underline"></div>
		</section>
		<div class="content">
			<div class="content tabs__content js-tabs-content active" id="tab-1">
				<? if ($arResult['DETAIL_TEXT']):?>
					<div class="goods__tabs-description"><?$subst = '$2';
						$result = preg_replace('/(<a.*href=".*tiptop-shop.ru.*".*>)(.*)(<\/a>)/uU', $subst, $arResult['DETAIL_TEXT']);
						$result = preg_replace('~<a\b[^>]*+>|</a\b[^>]*+>~', '', $result);
						echo $result;
						?></div>
				<? endif;?>
				<?
				// echo "<pre>";
				// var_dump($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['VALUE']);
				// echo "</pre>";

					
				?>
				<div class="goods__tabs-table tabs-table" data-nosnippet>
					<?
					//Список нужных свойств
					$arProperties = [];
					foreach($arParams["CUSTOM_PROPERTY_CODE"] as $code)
					{
						// пропускаем свойства
						
						$arr_ignore = array('TIP','HASH_', 'session_key', 'time_update', 'xml_id', 'MORE_PHOTO', 'MANUFACTURER', 'MANUFACTURER', 'EXPRESS_DELIVERY', 'EXPRESS_DELIVERY_P','IS_AVAILABLE_1','IS_AVAILABLE_2','IS_AVAILABLE_4', 'IS_AVAILABLE_7', "MERGE_PROPS_FOR_CONTENT", "MERGE_PROPS", 'DeliveryDays', 'delivery_from_ptr', 'delivery_from_msk');

						if (in_array($code, $arr_ignore)){
							continue;
						}


						
						if ($arResult["PROPERTIES"][$code]['NAME'] && $arResult["PROPERTIES"][$code]['VALUE_CUSTOMIZED'])
						{
							$arProperties[$code]['NAME'] = $arResult["PROPERTIES"][$code]['NAME'];
							$arProperties[$code]['VALUE'] = $arResult["PROPERTIES"][$code]['VALUE_CUSTOMIZED'];
						}
						elseif ($arResult["PROPERTIES"][$code]['NAME'] && $arResult["PROPERTIES"][$code]['VALUE'] && $v = CClass::getNormalValueProp($arResult["PROPERTIES"][$code]))
						{
							
							$arProperties[$code]['NAME'] = $arResult["PROPERTIES"][$code]['NAME'];
							$arProperties[$code]['VALUE'] = $v;
							
						}
					}
					
					//Выделяем из списка группы
					foreach($arProperties as $code => $arItemProp){
						$returnResult = preg_match('/\{(.*)\}/', $arItemProp['NAME'], $matches);
						
						if (count($matches) > 1) {
							$arItemProp['NAME'] = CClass::getNormalNameProp($arItemProp['NAME']);
							$arPropertyGroups[$matches[1]][$code] = $arItemProp;
							$arPropertyGroups[$matches[1]]['IS_GROUP'] = "Y";
							unset($arProperties[$code]);
						}
						
						unset($matches);
						unset($returnResult);
					}
					
					//Ко всем свойствам добавляем группы
					if(isset($arPropertyGroups)){
						$arProperties = $arPropertyGroups + $arProperties;
					}

					if (is_array($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['VALUE']) && (count($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['VALUE'])>0)){
						foreach ($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['VALUE'] as $k=>$val){
							if (trim($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['DESCRIPTION'][$k]) == 'N') continue;
							if (strlen(trim($arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['DESCRIPTION'][$k]))<=0) continue;

							$arProperties[$k] = array();
							$arProperties[$k]['CODE'] = $k;
							$arProperties[$k]['NAME'] = $val;
							$arProperties[$k]['VALUE'] = $arResult['PROPERTIES']["MERGE_PROPS_FOR_CONTENT"]['DESCRIPTION'][$k];
						}
					}

					//Разделяем свойства на 2 части
					$part = ceil(count($arProperties) / 2);
					$arProperties = array_chunk($arProperties, $part, true);
					
					$sub = [
						'left' => 0,
						'right' => 1,
					];
					
					//Выводим свойства по частям
					foreach($sub as $keySub => $valSub):
						?>
						<table class="tabs-table--<?=$keySub?>">
							<?
							foreach($arProperties[$valSub] as $code => $arItemProp):
								if($code == 'SERIES'){
									$el = GetIBlockElement($arResult["PROPERTIES"][$code]['VALUE']);
									global $man_show;

									if (in_array($el['PROPERTIES']['BRAND']['VALUE'], $man_show)){
										continue;
									}

									if (strlen($el['CODE'])>0){
										$url = '/series/'.$el['CODE'].'/';
										$name = $el['NAME'];
										$arItemProp['VALUE'] = '<a href='.$url.'>'.$name.'</a>';										
									}
									// var_dump($el['CODE']);
									// die;
								}
								//Если это группа свойств
								if(isset($arItemProp['IS_GROUP'])){
									?>
									<div class="group_props active">
										<span><?=$code?></span>
										<?
										foreach($arItemProp as $code => $arItemGroup){
											if($code == 'IS_GROUP'){
												continue;
											}
											?>
											<div class="tabs-table__row">
												<div class="tabs-table__row-column--left"><?=$arItemGroup["NAME"]?></div>
												<?if(!empty($arResult["PROPERTIES"][$code]["FILTER_HINT"])):?>
													<div class="hint-wrap">
														<a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$arResult['PROPERTIES'][$code]['FILTER_HINT']; ?>');">
															<i class="fa fa-question-circle-o"></i>
														</a>
													</div>
												<?endif;?>
												<?/*<div class="dots"></div>*/?>
												<div class="tabs-table__row-column--right">
													<?if($arResult["PROPERTIES"][$code]['CODE'] == 'FILES_DOCS' && is_array($arResult["PROPERTIES"][$code]['FILE_VALUE'])):?>
														<?foreach($arResult["PROPERTIES"][$code]['FILE_VALUE'] as $arFile):?>
															<a href="<?=$arFile['SRC'] ?>" target="_blank">
																<?=$arFile['DESCRIPTION'];?>
															</a>
														<?endforeach;?>
													<?elseif(array_key_exists('FILTER_LINK', $arResult["PROPERTIES"][$code])): ?>
														<a href="<?=$arResult["PROPERTIES"][$code]['FILTER_LINK'] ?>">
															<?=$arItemGroup['VALUE']?>
														</a>
													<?else:?>
														<?=$arItemGroup['VALUE']?>
													<?endif;?>
												</div>
											</div>
											<?
										}
										?>
									</div>
									<?
								}
								//Если обычное свойство
								else{
									?>
									<tr class="tabs-table__row">
										<td class="tabs-table__row-column--left">
											<?=CClass::getNormalNameProp($arItemProp["NAME"])?>
											<? /*if($_GET['dev']=='1')*/ 
												//var_dump($hints);

												if (strlen($hints[$code])>0){
													$arResult["PROPERTIES"][$code]['HINT'] = $hints[$code];
												}
												if (strlen($arResult["PROPERTIES"][$code]['HINT'])>0): ?>
												<div class="info">
													<div class="info__inner">
														<?
														//var_dump($arResult["PROPERTIES"][$code]['HINT']);

														?>
														<?=$arResult["PROPERTIES"][$code]['HINT']?>
														<?/*
														Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur laudantium minima sit dolorum consequuntur veniam a soluta recusandae vero repellendus, officia beatae illum magnam id. Porro distinctio obcaecati ipsam temporibus?
														*/?>
													</div>
												</div>
											<?  endif;?>
										</td>
										<?if(!empty($arResult["PROPERTIES"][$code]["FILTER_HINT"])):?>
											<div class="hint-wrap">
												<a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$arResult['PROPERTIES'][$code]['FILTER_HINT']; ?>');"><i class="fa fa-question-circle-o"></i></a>
											</div>
										<?endif;?>
										<?/*<div class="dots"></div>*/?>
										<td class="tabs-table__row-column--right">
											<?if($arResult["PROPERTIES"][$code]['CODE'] == 'FILES_DOCS' && is_array($arResult["PROPERTIES"][$code]['FILE_VALUE'])):?>
												<?foreach($arResult["PROPERTIES"][$code]['FILE_VALUE'] as $arFile):?>
													<a href="<?=$arFile['SRC'] ?>" target="_blank">
														<?=$arFile['DESCRIPTION'];?>
													</a>
												<?endforeach;?>
											<?elseif(array_key_exists('FILTER_LINK', $arResult["PROPERTIES"][$code])): ?>
												<a href="<?=$arResult["PROPERTIES"][$code]['FILTER_LINK'] ?>">
													<?=$arItemProp['VALUE']?>
												</a>
											<?else:?>
												<?=$arItemProp['VALUE']?>
											<?endif;?> 
										</td>
									</tr>
									<?
								}
								
							endforeach;
							?>
						</table>
						<?
					endforeach;
					?>
				</div>
				<div class="goods__tabs-link"><a href="#">Всехарактеристкики</a></div>
			</div>

			<? if( count( $arResult['DOCS'] ) > 0 ) { ?>
				<div class="content tabs__content js-tabs-content" id="tab-2" data-nosnippet>
					<?//pr($arResult['DOCS']);?>
					<ul class="docs new-file-list">
						<? foreach( $arResult['DOCS'] as $item ) { ?>
							<li>
								<a href="<?echo $item['PATH']?>" target="_blank">
									<img src="<?=SITE_DEFAULT_PATH?>/images/icons/file.png" alt="">
									<div><span><?echo $item['DESCRIPTION']?></span></div>
									<div><?echo $item['SIZE']?></div>
									<div class="clear"></div>
								</a>
							</li>
						<? } ?>
					</ul>
					<div class="clear"></div>
				</div>
			<? } ?>
			<?if (!empty($arScope)):?>
				<div class="content tabs__content js-tabs-content" id="tab-set">
					<?
						foreach($arScope as $arItemScope){
							?>
							<div class="product-package__item">
								<div class="product-package__title">
									<div class="product-package__name"><a href="<?=$arItemScope['URL']?>"><?=$arItemScope['NAME']?></a></div>
									<div class="product-package__price"><?=number_format($arItemScope['PRICE'], 0, '', ' ')?> руб.</div>
								</div>
								<div class="product-package__info"><?=str_replace(' шт.', ' шт.<br>', $arItemScope['STR_SCOPE'])?></div>
							</div>
							<?
						}
					?>
				</div>
			<?endif;?>
			<div class="content tabs__content js-tabs-content payment" id="delivery-and-payment" data-price="<?=$minPrice?>">
			</div>

<!--            REVIEWS-->
            <div class="content tabs__content js-tabs-content tab-reviews" id="_reviews">
                <? GLOBAL $USER; if ($USER->IsAuthorized()){ ?>
                    <?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form", 
	"reviews", 
	array(
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "Сообщение",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_NAME" => "Тема",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_TAGS" => "",
		"DEFAULT_INPUT_SIZE" => "30",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"GROUPS" => array(
			0 => "1",
			1 => "2",
			2 => "6",
		),
		"IBLOCK_ID" => "111",
		"IBLOCK_TYPE" => "catalog",
		"LEVEL_LAST" => "Y",
		"LIST_URL" => "",
		"MAX_FILE_SIZE" => "0",
		"MAX_LEVELS" => "100000",
		"MAX_USER_ENTRIES" => "100000",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"PROPERTY_CODES" => array(
			0 => "7215",
			1 => "NAME",
			2 => "DETAIL_TEXT",
		),
		"PROPERTY_CODES_REQUIRED" => array(
			0 => "7215",
			1 => "NAME",
			2 => "DETAIL_TEXT",
		),
		"RESIZE_IMAGES" => "N",
		"SEF_MODE" => "N",
		"STATUS" => "ANY",
		"STATUS_NEW" => "NEW",
		"USER_MESSAGE_ADD" => "",
		"USER_MESSAGE_EDIT" => "",
		"USE_CAPTCHA" => "N",
		"COMPONENT_TEMPLATE" => "reviews"
	),
	false
);?>
                <? } else { ?>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:system.auth.form",
                        "custom_popup",
                        array(
                            "REGISTER_URL" => "",
                            "FORGOT_PASSWORD_URL" => "",
                            "PROFILE_URL" => "/personal/",
                            "SHOW_ERRORS" => "Y",
                        ),
                        false
                    );?>
                <? } ?>

                <hr>
                <? if (empty($arResult["reviews"])) { ?>
                <p>Отзывов нет.<p>
                    <? } else { ?>
                    <? foreach ($arResult["reviews"] as $feedback) { ?>
                    <div class="tab-reviews-feedback tab-reviews-feedback-style">
                        <img src="https://image.flaticon.com/icons/svg/667/667327.svg" alt="" class="tab-reviews-feedback__user-picture">
                <p class="tab-reviews-feedback-style__message"><?=$feedback["message"]?></p>
                <? preg_match("/(.+)..:..:..$/", $feedback["created_at"], $dateTime); ?>
                <p class="tab-reviews-feedback-style__datetime"><?=$dateTime[1]?></p>
            </div>
        <? } ?>
        <? } ?>
        </div>
            <!--            REVIEWS-->

			<?/*<div class="content tabs__content js-tabs-content" id="tab-5">
				контент 5
			</div>
			<?*/
            ?>
                    <?if ( $_SERVER["SERVER_NAME"] == "shop-gr.ru" ):?>
                        <div class="content tabs__content js-tabs-content" id="tab-otzivy">
                            <img src="<?=SITE_DEFAULT_PATH?>/images/preloader.gif" />
			</div>
                    <?endif;?>
		</div>
	</div>

<?ob_start();?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_additionals.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_video.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_collection.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_similars.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_spare_parts.php');?>
<?$str=ob_get_clean();?>
<?= str_replace(GetMessage('CT_MOVE_FINGER'),'',$str);?>

	<?
	// seo tags - begin
//	if($GLOBALS["USER"]->IsAdmin()){		
		$arSectionsNames    = $arSectionPath = [];
		$arSectionPath  = CClass::getSectionPath($arResult['IBLOCK_SECTION_ID'], CClass::getCatalogSection());
		$sec_ids = array();
		foreach($arSectionPath AS $arSection){
		    $arSectionsNames[] = $arSection['NAME'];
		    $sec_ids[] = $arSection['ID'];
		}

		$arSeo = $GLOBALS['PAGE_DATA']['SEO_FILTER']['PAGE_SEO'];
		$arSeoTags = $GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'];

		// echo "<pre>";
		// 	var_dump($GLOBALS['PAGE_DATA']['SEO_FILTER']);
		// echo "</pre>";

		$tags_all = array();
		foreach ($sec_ids as $id){
			$tags_all = array_merge($tags_all, $arSeoTags[$id]);
		}


		$arSeoItems = array();
		foreach($arSeo as $url => $arSeo_item){
			$arr2 = explode('/', $arSeo_item['FILTER_URL_PAGE']);

			$arr = explode('-is-', $arr2[0]); // $arSeo_item['FILTER_URL_PAGE']
			$xml_ids = explode('-or-', $arr[1]);

			$code2 = $arr[0];
			$from = $to = 0;
			if (strpos($code2, '-from-') !== false){
				$val = substr($code2, strpos($code2, '-from-'));
				$code2 = substr($code2, 0, strpos($code2, '-from-'));

				$val_arr = explode('-to-', $val);
				$val_arr[0] = str_replace('-from-', '', $val_arr[0]);
				$from = $val_arr[0];
				$to = $val_arr[1];

				$xml_ids = array($from);


				// echo "<pre>";
			 // 	var_dump($arr[0]);
			 // 	var_dump($val_arr);
				// echo "</pre>";
			}


			// echo "<pre>";
			// 	var_dump($xml_ids);
			// echo "</pre>";

			foreach ($arResult['PROPERTIES'] as $code => $data_temp){

				if ($code2 == strtolower($code)){


					$val = '';
					// if (($from>0) && ($to > 0)){
					// 	$val = $from;
					// }else

					if (is_array($data_temp['VALUE_XML_ID']) && count($data_temp['VALUE_XML_ID'])>0){
						$val = $data_temp['VALUE_XML_ID'][0];
					}elseif (strlen($data_temp['VALUE_XML_ID'])>0){
						$val = $data_temp['VALUE_XML_ID'];
					}elseif (is_array($data_temp['VALUE'])){
						$val = $data_temp['VALUE'][0];
					}else{
						$val = $data_temp['VALUE'];
					}


					// echo "<pre>";
				 // 	var_dump($code);
				 // 	var_dump($xml_ids);
				 // 	var_dump($data_temp['VALUE']);
				 // 	var_dump($val);
					// echo "</pre>";


					if (in_array($val, $xml_ids)){
						if (!in_array($url, array_keys($arSeoItems))){
							if (in_array($url, array_keys($tags_all))){
								$arSeo_item['IMG'] = $tags_all[$url]['IMG'];
								$arSeo_item['LINK'] = $url;
								$arSeoItems[$url] = $arSeo_item;
							}
						}
					}
				}
			}

		}

		?>
		<? $APPLICATION->IncludeFile ( SITE_DEFAULT_PATH.'/include/seo_tags_element.php',
                                                   [
                                                       'BACK'  => $arBack,
                                                       'ITEMS' => $arSeoItems,
                                                       'ALL'   => $arSeo
                                                   ],
                                                   array (
                                                       "MODE"     => "file",
                                                       "TEMPLATE" => "include"
                                                   )
                ); ?>
		<?
//	}

	// seo tags - end
	?>
	<?if($GLOBALS["USER"]->IsAdmin()/**/ or true/**/) { include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_popular_series.php'); }?>
</div>
<div class="modal" id="open-more" data-nosnippet>
	<div class="modal-background"></div>
	<div class="modal-content">
			<div class="media-container">
		<? if (!empty($arResult["PHOTO"])):?>
				<?foreach ($arResult["PHOTO"] AS &$arItem):?>
					<div class="media-container__item">
						<img src="<?=$arItem['MEDIUM']['src']?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" />
					</div>					
				<?endforeach;?>
				
				<?if(!empty($arResult['PROPERTIES']['YOUTUBE']['VALUE']) and is_array($arResult['PROPERTIES']['YOUTUBE']['VALUE'])):?>
					<?foreach($arResult['PROPERTIES']['YOUTUBE']['VALUE'] as $key=>$val):?>
						<div class="media-container__item media-container__youtube">
							<a class="fancybox-media" href="https://www.youtube.com/watch?v=<?=$val?>"><img src="//img.youtube.com/vi/<?=$val?>/hqdefault.jpg" /></a>
						</div>
					<?endforeach?>
				<?endif?>
			</div>
		<? endif?>
	
		<div class="aside">
			<div class="aside__title"><?=$arResult['NAME']?></div>
			<div class="buy">
				<?php
				$frame=$this->createFrame()->begin('...');
				$frame->setAnimation(true);
				?>
					<div class="buy__item buy__item-buy--desktop">
						<?/*?>
						<? if($oldPrice != 0): ?>
						<div class="buy__item-economy">Вы экономите:   <span><?= $difference ?> руб.</span></div>
						<? endif ?>
						<?*/?>
						<div class="buy__item-price">
							<?/*?>
							<? if($oldPrice != 0): ?>
							<span class="buy__item-price-old"><?= $oldPrice ?> р.</span>
							<? endif ?>$APPLICATION->AddHeadString('<meta property="product:price:amount" content="'.$minPrice.'">',true);
							<?*/?>
							<div class="buy__item-price-new"><?=customFormatPriceClear(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?><span class="rubl">руб.</span></div>
						</div>
					</div>
					<? if ($minPrice):?>
						<div class="buy__item buy__item-buy--desktop">
							<?
							if($arResult["PROPERTIES"]["SYS_UNDER_THE_ORDER"]["VALUE"] == "Y")
							{
							?>
								<button class="btn is-primary buy-button buy--catalog btn-under-the-order" data-id="<?=$arResult["ID"]?>" >
									Под заказ
								</button>
							<?
							}
							else
							{
							?>
								<a href="<?=$arResult["DETAIL_PAGE_URL"]?>#buy" class="buy__icon btn is-primary buy-button buy__item-button buy__item-button--blue<?if($minPrice>0){?> add-to-basket<?}else{?> btn-grey<?}?> buy--item drvt-m10" data-id="<?=$arResult["ID"]?>" <?if($minPrice<=0){?>onclick="return false;"<?}?>>
								<?/*/?><img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>" /><?/**/?><?=GetMessage('CT_BUY_BUTTON')?>
							</a>
								<div class="level-right buy__item-button">
									<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
								</div>
								<?
                                if(СREDIT_ENABLE && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 200000){?>
									<div class="level-right buy__item-button button_credit">
										<a href="#" onclick="buyRasrochka(products_rasrochka, event, mode)" class="btn is-primary is-outlined" data-id="<?=$arResult['ID'];?>">Купить в рассрочку</a>
									</div>
									<?
								}?>
							<?
							}
							?>
						</div>
					<? endif;?>
				<? $frame->end();?>
			</div>
			<button class="close" aria-label="close"></button>
		</div>
	</div>
</div>
<?
$APPLICATION->AddHeadString('<meta property="product:price:amount" content="'.$minPrice.'">',true);
if($delivery_title=='В наличии'){
$APPLICATION->AddHeadString('<meta property="product:availability" content="in stock">',true);
}
/*
<script type="text/javascript">
	window.CatalogElement = new JSCatalogElement(<?= json_encode([
		'uniqueId' => '111',
		'product' => [
			'id' => $arResult['ID'],
			'available' => $arResult["PRODUCT"]['AVAILABLE'] == 'Y'
		]
	]) ?>);
</script>*/?>
<?if ( $_SERVER["SERVER_NAME"] == "shop-gr.ru" ):?>
<script>
    $( document ).ready(function() {
        var request = $.ajax({
          url: "/ajax/getReviewsBySKUID.php",
          type: "get",
          data: {sku_id : <?=$arResult['ID']?>},
          dataType: "html"
        });

        request.done(function(msg) { 
            $('#tab-otzivy').html(msg);
        });

        request.fail(function(jqXHR, textStatus) {
            $('#tab-otzivy').html( "Request failed: " + textStatus );
        }); 
    });
</script>
<?endif;?>
<!--Ecommerce and Pixels-->
<script>
window.dataLayer = window.dataLayer || [];
dataLayer.push({
 'ecommerce': {
   'currencyCode': 'RUB',	// Обязательно
   'detail': {
	   'products': [{
	   'name': '<?=$arResult['NAME']?>',	// Обязательно
	   'id': '<?=$arResult['ID']?>',	// Обязательно
	   'price': '<?=$minPrice?>',
	 }]
   },
  },
 'goods_id': '<?=$arResult['ID']?>',	// Обязательно
 'goods_price': '<?=$minPrice?>',	// Обязательно
 'page_type': 'product',
 'event': 'pixel-mg-event',	// Обязательно
 'pixel-mg-event-category': 'Enhanced Ecommerce',	// Обязательно
 'pixel-mg-event-action': 'Product Details',	// Обязательно
 'pixel-mg-event-non-interaction': 'True'	// Обязательно
});
</script>
<!-- /Ecommerce and Pixels-->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const imageObserver = new IntersectionObserver((entries, imgObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
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
		  "@context": "http://schema.org/",
		  "@type": "Product",
		  "name": "<?=$arResult['NAME']?>",
		  
		  "image":[
		  			<?$c=0;
		  			foreach ($arResult["PHOTO"] AS $key => &$arItem):
		  				$c++;
		  				$filename =$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($key);
							
							
							$fil = \Bitrix\Main\IO\Path::getExtension($filename);
							$temp_name = $info["FILE_NAME"].'$$';
							$img_nm = '/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;

							//$img_nm = '/upload/'.$info["SUBDIR"].'/'.$tmp[0].'_1920.'.$tmp[1];
							$size = getimagesize($_SERVER["DOCUMENT_ROOT"].$img_nm);
		  				?>
		  				{
						  "@type" : "ImageObject" ,
						  "url": "https://<?=SITE_SERVER_NAME.$img_nm?>", 
						  "height" : "<?=$size[1]?>", 
						  "width" : "<?=$size[0]?>",
						  "name" : "<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>"
						 }
						 <?=count($arResult["PHOTO"])!=$c?',':''?>
		  				<?
		  			endforeach;
					?>
		  ],
		  "brand": {
			"@type": "Brand",
			"name": "<?=$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arResult["PROPERTIES"]["MANUFACTURER"]['VALUE']]['NAME']?>"
		  },
		  "offers": {
			"@type": "Offer",
			"priceCurrency": "RUB",
			"price": "<?=$minPrice?>",
			"availability": "<?=$arResult["PRODUCT"]['QUANTITY'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock' ?>"
		  }
		}
</script>*/?>
<script>
    var product_id = <?=(int)$arResult["ID"]?>;
dashamail("async", {
    "operation": "ViewProduct",
    "data": {
        "product": {
            "productId": "<?=$arResult["ID"]?>",
            "price": "<?=$minPrice?>"
        }
    }
});

</script>
