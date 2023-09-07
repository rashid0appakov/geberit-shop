<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
if(defined('MAIN_SITE_BRAND') and !empty($arResult['PROPERTIES']['MANUFACTURER']['VALUE']) and !in_array($arResult['PROPERTIES']['MANUFACTURER']['VALUE'], MAIN_SITE_BRAND))
{

if(SITE_ID == 's8'){
	$templateData['robots'] = 'index, follow';
}
else {
	$templateData['robots'] = 'noindex, nofollow';
}
}

$isAdmin = false; // */$GLOBALS['USER']->IsAdmin();

//pr($arParams["CUSTOM_PROPERTY_CODE"]);
//pr($arResult['PROPERTIES']);

$this->setFrameMode(true);
$this->addExternalCss(SITE_DEFAULT_PATH.'/css/goods-card.css');

$jsParams = [];
?><?if(SITE_ID != 'l1'){?>
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
		<div class="title-bottom__vendor"><?=GetMessage('CT_PRODUCT_CODE')?>: <?=$arResult["ID"]?></div>
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

			$STORE_ID = 4;
			if ($geo_id == 817){ // питер
				$STORE_ID = 1;
			}

			if ($geo_id == 2201){ // питер
				$STORE_ID = 2;
			}


			$set_q = array();

			$ar_res = CCatalogProduct::GetByIDEx($arResult["ID"]);
			$product_id = $ar_res["PRODUCT"]['ID'];

			$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
		        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
		        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
		    ));

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

			if ($arResult["PRODUCT"]['QUANTITY']>0){
				?>
			<div class="title-bottom__in"><?=GetMessage('HDR_IN_STOCK')?></div>
				<?
			}else{
				?>
				<div class="title-bottom__vendor" style="color: rgb(89, 97, 104); width: 130px;">Наличие уточняйте</div>
				<?
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
		</div>*/?>
		<? if (($arResult["PRODUCT"]['QUANTITY']>0) && ($arResult["PROPERTIES"]['PRICE_UPDATE'.CClass::getCurrentAvalCode()]['VALUE'] > 0)):?>
		<?
			$days_str = '1-3 дня';
			if((date('w')==5) && date('H'>16)){
				$days_str = '3-5 дней';
			}elseif(date('w')==6){ // суббота
				$days_str = '2-4 дня';
			}
		?>
		<div class="title-bottom__delivery">
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/truck.png" alt="car" /><?=GetMessage('CT_DELIVERY_LINE')?>: &nbsp;<span><?=$days_str?></span>
			<? /*
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/truck.png" alt="car" /><?=GetMessage('CT_DELIVERY_LINE')?>: &nbsp;<span><?=GetMessage('CT_DELIVERY_DAYS')?></span>
			*/
			?><?/*Завтра, <?=$day;?> <?=$month;*/?>
		</div>
		<? endif;?>
		<? $frame->end();?>
	</div>
	<? if ($arResult["PROPERTIES"]["GUARANTEE"]['VALUE']):?>
	<div class="title-bottom title-bottom--column">
		<div class="title-bottom__guarantee">
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/guarantee.png" alt="guarantee"><?=GetMessage('CT_GARANTEE')?>: &nbsp;<?=CClass::getNormalValueProp($arResult["PROPERTIES"]["GUARANTEE"])?>
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
<div class="goods__description">
	<div class="description__column--left">
		<div class="description__top-slider">
			<?if ($arResult["PROPERTIES"]["SHOWROOM"]["VALUE"] == "Y"):?>
				<a href="/catalog/showroom/" target="_blank" class="description__top-slider-link">Посмотреть в шоуруме</a>
			<?endif;?>
			<div class="badges description__top-slider-badges">
				<?if ($arResult["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == "Y"):?>
					<span class="tag is-warning">Новинка</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["SALELEADER"]["VALUE"] == "Y"):?>
					<span class="tag is-success">Хит</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == "Y"):?>
					<span class="tag is-danger">Скидка</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["RECOMEND"]["VALUE"] == "Y"):?>
					<span class="tag is-warning">Рекомендуем</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["SHOWROOM"]["VALUE"] == "Y"):?>
					<span class="tag is-link">Шоу-рум</span>
				<?endif;?>
				<?if ($arResult["PROPERTIES"]["SALEGOODS"]["VALUE"] == "Y"):?>
					<span class="tag is-danger">Распродажа</span>
				<?endif;?>
			</div>
		<? if (!empty($arResult["PHOTO"])):?>
			<div class="slider-for">
				<?foreach ($arResult["PHOTO"] AS &$arItem):?>
					<div class="slide-item">
						<img src="<?=$arItem['MEDIUM']['src']?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" itemprop="image" />
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
			<? if (count($arResult["PHOTO"]) > 1):?>
			<div class="slider-nav" data-previous-image-url="<?=$templateFolder?>/images/top-sleder__left-arrow.png" data-next-image-url="<?=$templateFolder?>/images/top-sleder__right-arrow.png">
				<? foreach($arResult["PHOTO"] AS &$arItem):?>
					<div class="slide-nav-item">
						<img src="<?=$arItem['SMALL']['src']?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" />
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
			?>
			<script type="text/javascript">
				var products_rasrochka = {}, mode = 'ONE';
			</script>
			<script type="text/javascript">
				products_rasrochka[0] = {name: '<?=htmlspecialchars($arResult['NAME'], ENT_QUOTES)?>', quantity: '1', price: '<?=$minPrice?>', id: '<?=$arResult['ID']?>'};
			</script>
			<div class="buy__item buy__item-buy--mobile">
				<? if ($oldPrice): ?>
					<div class="buy__item-economy"><?=GetMessage('CT_YOU_SAVE_TITLE')?>: <span><?=customFormatPrice(CurrencyFormat($difference, $arResult["MIN_PRICE"]['CURRENCY']))?></span></div>
				<? endif ?>
				<div class="buy__item-price">
					<? if($arItem["PRODUCT"]["QUANTITY"]>0){?>
					<? if ($oldPrice): ?>
					<span class="buy__item-price-old"><?=customFormatPrice(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']))?></span>
					<? endif ?>
					<div class="buy__item-price-new"><?=customFormatPrice(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?></div>
					<?}?>
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
					<button class="btn is-primary buy-button buy__item-button buy__item-button--blue add-to-basket buy--item" data-id="<?=$arResult["ID"]?>" <?if($arItem["PRODUCT"]["QUANTITY"]<=0){?>disabled<?}?>>
						<img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>" /><?=GetMessage('CT_BUY_BUTTON')?>
					</button>
					<?if($arItem["PRODUCT"]["QUANTITY"]>0){?>
					<div class="level-right buy__item-button">
						<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
					</div>
					<?}?>
					<?
                    if(СREDIT_ENABLE && SITE_ID != 's8' && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 105263){?>
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
		<div class="description__list">
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
				<div class="description__item-title">
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-1.png" alt="i" /><?=GetMessage('CT_OPTIONS')?></div>
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
					<div class="description__item-title">
						<img src="<?=SITE_DEFAULT_PATH;?>/images/icons/description-title-img-2.png" alt="i" />
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
					<div class="description__item-title">
						<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i" /><?=GetMessage('CT_KOMPL_TITLE')?>
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
					<div class="description__item-title">
						<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-2.png" alt="i" /><?=GetMessage('CT_ODDS_TITLE')?></div>
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
				<div id="delivery_container" data-id="<?=$arResult['ID']?>">
				</div>
				<a href="/delivery/" class="description__link description__link-2">Подробнее о доставке</a>
			</div>

			<?php
			if ($arResult['INSTALLATION_PRICE']):
			?>
			<div class="description__item">
				<div class="description__item-title"><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-4.png" alt="i">Монтаж</div>
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
					<div class="buy__item buy__item-buy--desktop" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<? if ($oldPrice): ?>
						<div class="buy__item-economy"><?=GetMessage('CT_YOU_SAVE_TITLE')?>: <span><?=customFormatPrice(CurrencyFormat($difference, $arResult["MIN_PRICE"]['CURRENCY']))?></span></div>
						<? endif ?>
						<div class="buy__item-price">
							<? if($arItem["PRODUCT"]["QUANTITY"]>0){?>
							<? if ($oldPrice): ?>
							<span class="buy__item-price-old"><?=customFormatPrice(CurrencyFormat($oldPrice, $arResult["MIN_PRICE"]['CURRENCY']))?></span>
							<? endif ?>
							<div class="buy__item-price-new" itemprop="price"><?=customFormatPrice(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?></div>
							<span itemprop="priceCurrency" style="display:none;">RUB</span>
							<?}?>
						</div>
						<?
                        if (SITE_ID!='s8' && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y'):?>
						<div class="title-bottom__alfa">
							<img src="<?=SITE_DEFAULT_PATH?>/images/icons/alfa.png" alt="alfa" />
							<p><?=GetMessage('CT_INSTALLMENT_PLAN', ['#PRICE#' => customFormatPrice(CurrencyFormat( round($minPrice / 4), $arResult["MIN_PRICE"]['CURRENCY']))])?></p>
						</div>
						<? endif;?>
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
						<?if($arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"):?>
							<div class="sale_section available_section_cat">
								<i class="check-circle circle-disc"></i> Скидка при заказе онлайн <span class="bold"><?=$arResult["DISCOUNT_BASKET"]["VALUE"]?>%</span>
							</div>
						<?endif?>
							<button class="btn is-primary buy-button buy__item-button buy__item-button--blue add-to-basket buy--item drvt-m10" data-id="<?=$arResult["ID"]?>" <?if($arItem["PRODUCT"]["QUANTITY"]<=0){?>disabled<?}?>>
								<img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>" /><?=GetMessage('CT_BUY_BUTTON')?>
							</button>
							<?if($arItem["PRODUCT"]["QUANTITY"]>0){?>
							<div class="level-right buy__item-button drvt-m10">
								<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
							</div>
							<?}?>
							<?
                            if(СREDIT_ENABLE && SITE_ID != 's8' && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 105263){?>
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

				<li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#delivery-and-payment" id="delivery-and-payment-click">Доставка и оплата</li>

                <li class="tabs__header--title js-tabs-title js-tabs-props" data-tab="#_reviews" id="reviews">Отзывы</li>
				<?/*
				<li class="tabs__header--title js-tabs-title" data-tab="#tab-5">Монтаж</li>
				
				*/?>
                                <?if ( $_SERVER["SERVER_NAME"] == "shop-gr.ru" ):?>
                                <li class="tabs__header--title js-tabs-title" data-tab="#tab-otzivy">Отзывы</li>
                                <?endif;?>

			</ul>
			<div class="tabs__underline js-tabs-underline"></div>
		</section>
		<div class="content">
			<div class="content tabs__content js-tabs-content active" id="tab-1">
				<? if ($arResult['DETAIL_TEXT']):?>
					<div class="goods__tabs-description"><?=$arResult['DETAIL_TEXT']?></div>
				<? endif;?>
				<div class="goods__tabs-table tabs-table">
					<?
					//Список нужных свойств
					$arProperties = [];
					foreach($arParams["CUSTOM_PROPERTY_CODE"] as $code)
					{
						// пропускаем свойства с хэшем
						if (strpos($code, "HASH_") === 0)
							continue;
						
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
						<div class="tabs-table--<?=$keySub?>">
							<?
							foreach($arProperties[$valSub] as $code => $arItemProp):
								
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
														<a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$arResult["PROPERTIES"][$code]['FILTER_HINT']?>');"><i class="fa fa-question-circle-o"></i></a>
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
									<div class="tabs-table__row">
										<div class="tabs-table__row-column--left"><?=CClass::getNormalNameProp($arItemProp["NAME"])?></div>
										<?if(!empty($arResult["PROPERTIES"][$code]["FILTER_HINT"])):?>
											<div class="hint-wrap">
												<a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$arResult["PROPERTIES"][$code]['FILTER_HINT']?>');"><i class="fa fa-question-circle-o"></i></a>
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
													<?=$arItemProp['VALUE']?>
												</a>
											<?else:?>
												<?=$arItemProp['VALUE']?>
											<?endif;?>
										</div>
									</div>
									<?
								}
								
							endforeach;
							?>
						</div>
						<?
					endforeach;
					?>
				</div>
				<div class="goods__tabs-link"><a href="#">Всехарактеристкики</a></div>
			</div>

			<? if( count( $arResult['DOCS'] ) > 0 ) { ?>
				<div class="content tabs__content js-tabs-content" id="tab-2">
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

	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_additionals.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_video.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_collection.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_similars.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_spare_parts.php');?>
</div>
<div class="modal" id="open-more">
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
							<? if($arItem["PRODUCT"]["QUANTITY"]>0){?>
							<?/*?>
							<? if($oldPrice != 0): ?>
							<span class="buy__item-price-old"><?= $oldPrice ?> р.</span>
							<? endif ?>
							<?*/?>
							<div class="buy__item-price-new"><?=customFormatPrice(CurrencyFormat($minPrice, $arResult["MIN_PRICE"]['CURRENCY']))?></div>
							<?}?>
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
								<button class="btn is-primary buy-button buy__item-button buy__item-button--blue add-to-basket buy--item" data-id="<?=$arResult["ID"]?>" <?if($arItem["PRODUCT"]["QUANTITY"]<=0){?>disabled<?}?>>
									<img src="<?=SITE_DEFAULT_PATH?>/images/icons/basket.png" alt="<?=GetMessage('CT_BUY_BUTTON')?>"><?=GetMessage('CT_BUY_BUTTON')?>
								</button>
								<?if($arItem["PRODUCT"]["QUANTITY"]>0){?>
								<div class="level-right buy__item-button">
									<a href="#" class="btn is-primary is-outlined one-click buy--one-click--item" data-id="<?=$arResult['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
								</div>
								<?}?>
								<?
                                if(СREDIT_ENABLE && SITE_ID != 's8' && $arParams['DISPLAY_INSTALLMENT_PLAN'] == 'Y' && $minPrice > 3000 && $minPrice < 105263){?>
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

<script type="application/ld+json">
		{
		  "@context": "http://schema.org/",
		  "@type": "Product",
		  "name": "<?=$arResult['NAME']?>",
		  "image": "<?='https://'.SITE_SERVER_NAME.$arResult["PHOTO"][0]['MEDIUM']['src']?>",
		  "brand": {
			"@type": "Thing",
			"name": "<?=$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arResult["PROPERTIES"]["MANUFACTURER"]['VALUE']]['NAME']?>"
		  },
		  "offers": {
			"@type": "Offer",
			"priceCurrency": "RUB",
			"price": "<?=$minPrice?>",
			"itemCondition": "http://schema.org/UsedCondition",
			"availability": "<?=$arResult["PRODUCT"]['QUANTITY'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock' ?>"
		  }
		}
</script>
<script>
    var product_id = <?=(int)$arResult["ID"]?>;
</script>
