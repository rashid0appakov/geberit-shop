<?php
if (!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

// global $USER;
// if ($USER->IsAdmin()){
// 	echo "<pre>";
// 		var_dump($_SERVER['REAL_FILE_PATH']);	
// 		var_dump($_SERVER['QUERY_STRING']);	
// 	echo "</pre>";

// 	if (strpos($_SERVER['REQUEST_URI'], '/product/') !== false){}
// }

use Bitrix\Main\Config\Option;

$this->setFrameMode(true);

$arItem = $arResult["ITEM"];
$jsParams = array(
	"uid" => $arItem['uniqueId'],
	"productInfo" => array(
		"id" => $arItem["ID"],
		"available" => $arItem["PRODUCT"]["AVAILABLE"] == "Y",
	),
);

if(empty($arParams['CUSTOM_DISPLAY_PARAMS']) and $arItem['IBLOCK_SECTION_ID'])
{
	$arParams['CUSTOM_DISPLAY_PARAMS'] = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]['DISPLAY_PARAMS'];
	
	//Если пусто, ищем в родительском
	if(count($arParams['CUSTOM_DISPLAY_PARAMS']) == 0 && isset($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]['IBLOCK_SECTION_ID'])){
		$sectionParentId = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arItem['IBLOCK_SECTION_ID']]['IBLOCK_SECTION_ID'];
		
		$arParams['CUSTOM_DISPLAY_PARAMS'] = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$sectionParentId]['DISPLAY_PARAMS'];
	}
}
if(empty($arParams['CUSTOM_DISPLAY_PARAMS']) and $arParams['SECTION_ID'])
{
	$arParams['CUSTOM_DISPLAY_PARAMS'] = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arParams['SECTION_ID']]['DISPLAY_PARAMS'];
	
	//Если пусто, ищем в родительском
	if(count($arParams['CUSTOM_DISPLAY_PARAMS']) == 0 && isset($GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arParams['SECTION_ID']]['IBLOCK_SECTION_ID'])){
		$sectionParentId = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arParams['SECTION_ID']]['IBLOCK_SECTION_ID'];
		
		$arParams['CUSTOM_DISPLAY_PARAMS'] = $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$sectionParentId]['DISPLAY_PARAMS'];
	}
}

$name = "";
if(!empty($arItem["PROPERTIES"]["SYS_NAME"]["VALUE"])){
	$name = $arItem["PROPERTIES"]["SYS_NAME"]["VALUE"];
}
elseif(SITE_ID == 'l1'){
	
	if(!empty($arItem["PROPERTIES"]["VID_SEO"]["VALUE"])){
		$name .= $arItem["PROPERTIES"]["VID_SEO"]["VALUE"];
	}
	
	if(!empty($arItem["PROPERTIES"]["MANUFACTURER"]["VALUE"])){
		$name .= " ".strip_tags(CClass::getNormalValueProp($arItem["PROPERTIES"]["MANUFACTURER"]));
	}
	
	if(!empty($arItem["PROPERTIES"]["SERIES"]["VALUE"])){
		$name .= " ".strip_tags(CClass::getNormalValueProp($arItem["PROPERTIES"]["SERIES"]));
	}
	
	if(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])){
		$name .= " ".$arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"];
	}
	
}
else{
	$name = $arItem["NAME"];
}
?>
<div class="product product--card-cell" id="product_<?=$arItem['uniqueId']?>" itemscope itemprop="itemListElement" itemtype="http://schema.org/Product">
	<div class="product__inner">
	<div class="middle-header">
		<a href="#" class="<?=(CClass::isAddedToCompare($arItem['ID']) ? "compare-added" : '')?>
			icon-diff-big button tooltip is-tooltip-left goods__carousel-tabs-diff" data-id="<?=$arItem["ID"]?>"
			data-tooltip="<?=GetMessage(CClass::isAddedToCompare($arItem['ID']) ? 'CT_GO_TO_COMPARE' : 'CT_ADD_TO_COMPARE')?>">
			<span class="icon-diff">
				<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
					<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
					<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
					<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
				</svg>
			</span>
		</a>
	</div>
	<? if (!empty($arItem['PHOTOS'])):?>
		<?  $filename =$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arItem["DETAIL_PICTURE"]['ID']);
		$info = CFile::GetFileArray($arItem["DETAIL_PICTURE"]['ID']);
		/*$tmp = explode('.', $info["FILE_NAME"]);
		$path = $_SERVER["DOCUMENT_ROOT"].'/upload/'.$info["SUBDIR"].'/'.$tmp[0].'_1920.'.$tmp[1];
		$img = '/upload/'.$info["SUBDIR"].'/'.$tmp[0].'_1920.'.$tmp[1];*/

		$fil = \Bitrix\Main\IO\Path::getExtension($filename);
		$temp_name = $info["FILE_NAME"].'$$';
		$path =  $_SERVER["DOCUMENT_ROOT"].'/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;
		$img = '/upload/'.$info["SUBDIR"].'/'.str_replace('.'.$fil.'$$', '', $temp_name).'_1920.'.$fil;
		if (!file_exists($path)) {
			$image = new SimpleImage();
		    $image->load($filename);
		    $image->resizeToWidth(1920);
		    $image->save($path);
		}
	    	//$arItem['PHOTOS'][0]['src'] = $img;
		//}
        $arSrcSet998 = Pict::getResizeWebpSrc($arItem["DETAIL_PICTURE"]['ID'], '250','270');
        $arSrcSet480 = Pict::getResizeWebpSrc($arItem["DETAIL_PICTURE"]['ID'], '175','270');
		?>
	<div class="product-img-cover" data-origin="<?=$arSrcSet998?>" data-hover="<?=$arItem['PHOTOS'][1]['src']?>" data-id="<?=$arItem['ID']?>">
		<a itemprop="url" href="<?=$arItem["DETAIL_PAGE_URL"]?>" class=" product__link-img product__link-img-1?><?=(count($arItem['PHOTOS']) == 1 ? " show-allways" : '')?>" >
            <img itemprop="image" alt="<?=$name?>" srcset="<?=$arSrcSet998?> 980w, <?=$arSrcSet480?> 750w" id="img<?=$arItem['ID']?>" class="lzy_img" decode="async"/>
        </a>
	</div>
	<?endif;?>
	<div class="badges">
		<?
		$STORE_ID = GetStoreId();
		$sale_price_id = GetSaleStoreId();
		?>
		<?
		$arPrice = $arItem["ITEM_PRICES"][0];
		$price = $arPrice["PRICE"];
		$oldPrice = $arPrice["BASE_PRICE"];

		//$percentChange = round((($oldPrice - $price) / ($oldPrice)) * 100);
		if($arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"){
			$percentChange=$arResult["DISCOUNT_BASKET"]["VALUE"];
		}
		if(empty($arPrice['CURRENCY'])){
            $arPrice['CURRENCY'] = 'RUB';
        }
		$arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);
		$arPrice["PRINT_PRICE"] = CurrencyFormat($price, $arPrice['CURRENCY']);

		$ar_res = CCatalogProduct::GetByIDEx($arItem["ID"]);
		$product_id = $ar_res["PRODUCT"]['ID'];

		$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'N';

		global $USER;

	    if (($ar_res['PRICES'][$sale_price_id]['PRICE'] > 0) && ($ar_res['PRICES'][$sale_price_id]['PRICE'] > $price)){
			$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'Y';
	    	$oldPrice = $ar_res['PRICES'][$sale_price_id]['PRICE'];
			$arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);

			// if ($USER->IsAdmin()){
			// 	var_dump($ar_res['PRICES'][$sale_price_id]['PRICE']);
			// 	var_dump($price);
			// }
	    }

		?>
		<? if ($arItem["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == "Y"):?>
			<span class="tag is-warning">Новинка</span>
		<?endif;?>
		<?if ($arItem["PROPERTIES"]["SALELEADER"]["VALUE"] == "Y"):?>
			<span class="tag is-success">Хит</span>
		<?endif;?>
		<?/*if ($arItem["PROPERTIES"]["DISCOUNT"]["VALUE"] == "Y"):?>
			<span class="tag is-danger">Скидка</span>
		<?endif;*/?>
		<?if ($arItem["PROPERTIES"]["RECOMEND"]["VALUE"] == "Y"):?>
			<span class="tag is-warning">Рекомендуем</span>
		<?endif;?>
		<?if ($arItem["PROPERTIES"]["SHOWROOM"]["VALUE"] == "Y"):?>
			<span class="tag is-link">Магазин</span>
		<?endif;?>
		<?if ($arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] == "Y"):?>
			<span class="tag is-danger">Распродажа</span>
		<?endif;?>
		<?if (SITE_ID == 'l1' && in_array($arItem["PROPERTIES"]["MANUFACTURER"]["VALUE"], GIFT_BRANDS) && $arItem["ITEM_PRICES"][0]['PRICE'] > 2000):?>
			<span class="lp-label lamp"><span>Лампы в подарок</span></span>
		<?endif;?>
		<?if (SITE_ID == 'l1' && $arItem["ITEM_PRICES"][0]['PRICE'] > 5000):?>
			<span class="lp-label delivery"><span>Бесплатная доставка</span></span>
		<?endif;?>
		<?
		$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
		if(!$geo_id>0){$geo_id=129;}
		if (($geo_id == 129 && $arItem["PROPERTIES"]["EXPRESS_DELIVERY"]["VALUE"]=='Y')){
			?><span class="tag expressBtn">Экспресс<div class="descr" data-loaded="false"></div></span><?
            //include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/express_delivery.php');
		}
		if (($geo_id == 817 && $arItem["PROPERTIES"]["EXPRESS_DELIVERY_P"]["VALUE"]=='Y')){
			?><span class="tag expressBtn">Экспресс<div class="descr" data-loaded="false"></div></span><?
			//include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/express_delivery.php');
		}
		?>

		<? 
		global $USER;
		//if($USER->IsAdmin()):
		if(isset($arItem["GIFT"]) && count($arItem["GIFT"])>0){
		?>
			<div class="tag is-gift" data-pid="<?=$arItem["ID"]?>">
				Подарок
				<div class="descr">
				</div>
			</div>
		<? 
		}
		// endif; ?>
	</div>

	<div class="info">
		<p>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span itemprop="name"><?=$name?></span></a>
		</p>
		<meta itemprop="brand" content="<?=$GLOBALS['PAGE_DATA']['INFO_BRAND'][$arItem["PROPERTIES"]["MANUFACTURER"]['VALUE']]['NAME']?>">
		
		<?
		$show_props = false;
		if(SITE_ID == 'l1' and !empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])){
			$show_props = true;
		}

		if(SITE_ID != 'l1' and !empty($arParams['CUSTOM_DISPLAY_PARAMS'])){
			?>
			<?
				$country_present = false;
					foreach($arParams['CUSTOM_DISPLAY_PARAMS'] as $code):?>
					<?
					
					$val = CClass::getNormalValueProp($arItem["PROPERTIES"][$code]);
					
					if ($country_present) break;

					if(!empty($val)){
						if ($arItem["PROPERTIES"][$code]["NAME"] == 'Страна'){
							$country_present = true;
						}

						if ($arItem["PROPERTIES"][$code]["NAME"] == 'Производитель'){
							$val = strip_tags($val);
						}

						$show_props = true;
					}?>
				<?endforeach?>
			<?
		}else{
			?>
			<?if(!empty($arItem["PROPERTIES"]["STIL"]["VALUE"])){
				$show_props = true;
			}?>
				<?if(!empty($arResult['INTERIORS'][$arItem["PROPERTIES"]["APPLICATION"]["VALUE"]])){
				$show_props = true;
			}?>
				<?if(!empty($arResult['INTERIORS'][$arItem["PROPERTIES"]["PLOSHCHAD_OSVESHCHENIYA"]["VALUE"]])){
				$show_props = true;
			}?>
				<?if(!empty($arResult['INTERIORS'][$arItem["PROPERTIES"]["GABARITY_DIAMETR_MM"]["VALUE"]])){
				$show_props = true;
			}?>
				<?if(!empty($arResult['INTERIORS'][$arItem["PROPERTIES"]["GABARITY_VYSOTA_MM"]["VALUE"]])){
				$show_props = true;
			}?>
			<?
		}
		?>

		<?
		if($_SERVER['REAL_FILE_PATH'] != '/catalog/index.php'){
			$show_props = false;
		}

		if (strpos($_SERVER['REQUEST_URI'], '/product/') !== false){
			$show_props = false;
		}

		if ($show_props){
			?>
		<ul class="level availability list_prop">
			<?/*<li><div class="level-left artnum">
				<span><?=GetMessage('CT_PRODUCT_CODE')?>: <?=$arItem["ID"]?></span>
			</div></li>*/?>
			<?if(SITE_ID == 'l1' and !empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])){?>
				<li><div class="level-left artnum">
					<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["ARTNUMBER"]["NAME"])?>: <?=$arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]?></span>
				</div></li>
			<?}?>
			<?if(SITE_ID != 'l1' and !empty($arParams['CUSTOM_DISPLAY_PARAMS'])):?>
				<?
					$country_present = false;
					$counter = 0;

					foreach($arParams['CUSTOM_DISPLAY_PARAMS'] as $code):?>
					<?

					if($_SERVER['REAL_FILE_PATH'] != '/catalog/index.php'){
						if ($counter>=0) break;	
					}

					if (strpos($_SERVER['REQUEST_URI'], '/product/') !== false){
						if ($counter>=0) break;	
					}
					
					
					$val = CClass::getNormalValueProp($arItem["PROPERTIES"][$code]);
					
					if ($country_present) break;


					if(!empty($val)){
						if ($arItem["PROPERTIES"][$code]["NAME"] == 'Страна'){
							$country_present = true;
						}
						$counter++;
					?>
						<li><div class="level-left artnum">
							<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"][$code]["NAME"]);?>: <?=$val?></span>
						</div></li>
					<?}?>
				<?endforeach?>
			<?else:?>
				<?if(!empty($arItem["PROPERTIES"]["STIL"]["VALUE"])){?>
					<li><div class="level-left artnum">
						<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["STIL"]["NAME"])?>: <?=implode(', ', $arItem["PROPERTIES"]["STIL"]["VALUE"])?></span>
					</div></li>
				<?}?>
				<?if(!empty($arResult['INTERIORS'][$arItem["PROPERTIES"]["APPLICATION"]["VALUE"]])){?>
					<li><div class="level-left artnum">
						<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["APPLICATION"]["NAME"])?>: <?=customGetIntVal($arResult['INTERIORS'][$arItem["PROPERTIES"]["APPLICATION"]["VALUE"]]);?></span>
					</div></li>
				<?}?>
				<?if(!empty($arItem["PROPERTIES"]["PLOSHCHAD_OSVESHCHENIYA"]["VALUE"])){?>
					<li><div class="level-left artnum">
						<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["PLOSHCHAD_OSVESHCHENIYA"]["NAME"])?>: <?=customGetIntVal($arItem["PROPERTIES"]["PLOSHCHAD_OSVESHCHENIYA"]["VALUE"]);?></span>
					</div></li>
				<?}?>
				<?if(!empty($arItem["PROPERTIES"]["GABARITY_DIAMETR_MM"]["VALUE"])){?>
					<li><div class="level-left artnum">
						<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["GABARITY_DIAMETR_MM"]["NAME"])?>: <?=customGetIntVal($arItem["PROPERTIES"]["GABARITY_DIAMETR_MM"]["VALUE"]);?></span>
					</div></li>
				<?}?>
				<?if(!empty($arItem["PROPERTIES"]["GABARITY_VYSOTA_MM"]["VALUE"])){?>
					<li><div class="level-left artnum">
						<span><?=CClass::getNormalNameProp($arItem["PROPERTIES"]["GABARITY_VYSOTA_MM"]["NAME"])?>: <?=customGetIntVal($arItem["PROPERTIES"]["GABARITY_VYSOTA_MM"]["VALUE"]);?></span>
					</div></li>
				<?}?>
			<?endif?>
			<?/*<div class="level-right<?=(!$arItem["PRODUCT"]["QUANTITY"] ? " is-disabled" : '')?>">
			<?=GetMessage('HDR_'.($arItem["PRODUCT"]["QUANTITY"] ? '' : 'NOT_').'IN_STOCK')?></div>*/?>
			<?/* $frame=$this->createFrame()->begin('...');?>
			<? if ($arItem["PRODUCT"]["QUANTITY"]):?><div class="level-right"><?=GetMessage('HDR_IN_STOCK')?></div><?endif;?>
			<? $frame->end();*/?>


		</ul>
			<?
		}
		?>
		<?
		// $arPrice = $arItem["ITEM_PRICES"][0];
		// $price = $arPrice["PRICE"];
		// $oldPrice = $arPrice["BASE_PRICE"];
		// //$percentChange = round((($oldPrice - $price) / ($oldPrice)) * 100);
		// if($arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"){
		// 	$percentChange=$arResult["DISCOUNT_BASKET"]["VALUE"];
		// }
		// if(empty($arPrice['CURRENCY'])){
  //           $arPrice['CURRENCY'] = 'RUB';
  //       }
		// $arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);
		// $arPrice["PRINT_PRICE"] = CurrencyFormat($price, $arPrice['CURRENCY']);

		//var_dump($arItem["PRODUCT"]["QUANTITY"]);
		?>
<?
	/*
	$stores_dictionary = array(
	                "8f038b0f-b44b-11e9-929d-7824af46b558" => 4, //Склад виртуальный по Москве
	                "989e6e57-b2ae-11e9-929d-7824af46b558" => 1, //Склад виртуальный по СПБ
	                "aa7f5011-b52d-11e9-929d-7824af46b558" => 2, //Склад виртуальный по Екатеринбургу
	);
	*/
	$arItem["PRODUCT"]['QUANTITY'] = 0;
	$ar_res = CCatalogProduct::GetByIDEx($arItem["ID"]);
	$product_id = $ar_res["PRODUCT"]['ID'];

	$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
    ));

    while($arStoreProduct=$rsStoreProduct->fetch())
    {
        //var_dump($arStoreProduct);
        $arItem['PRODUCT']['QUANTITY'] = $arStoreProduct['AMOUNT'];
    }

	//var_dump($arItem['ID']);
	$result = CCatalogProductSet::getAllSetsByProduct($arItem['ID'], CCatalogProductSet::TYPE_SET);
	if (!empty($result))
	{

	    $set = reset($result);
	    //$setId = key($result);

	    $set_q = array();
	    foreach ($set['ITEMS'] as $k => $arr){
	    	$arItem['SET_ITEMS'][] = $arr;
	    	$ar_res = CCatalogProduct::GetByIDEx($arr["ITEM_ID"]);
	    	$product_id = $ar_res["PRODUCT"]['ID'];

			$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
		        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
		        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
		    ));

		    while($arStoreProduct=$rsStoreProduct->fetch())
		    {
		        //var_dump($arStoreProduct);
		        $ar_res['PRODUCT']['QUANTITY'] = $arStoreProduct['AMOUNT'];
		    }

			$set_q[] = intval($ar_res['PRODUCT']['QUANTITY']);
	    }

		if (is_array($set_q) && count($set_q)>0){
			$arItem["PRODUCT"]['QUANTITY'] = min($set_q);
		}


//	      echo 'ID комплекта: '.$setId;
//	      print_r($set); // описание и состав комплекта
	}

			// логика доставки 1-2-30-45-360 дней - начало

			//var_dump($ar_res['SET_ITEMS']);


			$data = GetDeliveryDataForElementByArr($arItem);
			$delivery_title = $data['delivery_title'];
			$delivery_days_title = $data['delivery_days_title'];
			$peremeshen = $data['peremeshen'];
			$q = $data['q'];

			if ($q == 0){
				$arItem["PRODUCT"]['QUANTITY'] = 0;
			}

			global $USER;
			if ($USER->IsAdmin()){
				//var_dump($data);
				//echo'<pre>';var_dump($delivery_title);echo'</pre>';
			}
			// логика доставки 1-2-30-45-360 дней - конец

?>

			<?if($arResult["PROPERTIES"]["DISCONTINUED"]["VALUE"] !== "Y" && isset($arItem["PRODUCT"]["QUANTITY"])) {?>


				<div class="available_section available_section_cat">

					<?if($arItem["PRODUCT"]["QUANTITY"] > 0){?>
						<span class="circle-<?=$delivery_title=="Наличие уточняйте"?'red" style="color:orange"':"gr"?>"><?=$delivery_title?></span>
					<?}else{?>
						<?if($arPrice["PRICE"] > 0){
							?>
						<span class="circle-<?=$delivery_title=="Наличие уточняйте"?'red" style="color:orange"':"gr"?>"><?=$delivery_title?></span>
							<?
						}else{
							?>
						<span class="circle-red">Наличие уточняйте</span>
							<?
						}?>
					<?}?>

				</div>

				<?}?>
		<div class="level price is-mobile">
			<?
			if($arItem["PROPERTIES"]["DISCONTINUED"]["VALUE"] == "Y"){
				$arItem["PRODUCT"]["QUANTITY"] = null;
				?>
				<div class="discontinued discontinued_list">
					Снят с производства
				</div>
				<?
			}
			else
			{

				if (($ar_res['PRICES'][$sale_price_id]['PRICE'] > 0) && ($ar_res['PRICES'][$sale_price_id]['PRICE'] > $price)){
					$arItem["PROPERTIES"]["SALEGOODS"]["VALUE"] = 'Y';
			    	$oldPrice = $ar_res['PRICES'][$sale_price_id]['PRICE'];
					$arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);
			    }
				?>
				<? //$frame=$this->createFrame()->begin('...');?>
				<meta itemprop="description" content="<?=$arItem["NAME"]?>"/>
				<div class="level-left" itemscope itemprop="offers" itemtype="http://schema.org/Offer">
					<div>
						<div class="rubl" >Цена:</div>
						<?if ($price != $oldPrice):?>
							<span class="old"><?=customFormatPriceClear($arPrice["PRINT_BASE_PRICE"])?><span class="rubl">руб.</span></span>
						<?endif;?>
						<div class="new" ><?=customFormatPriceClear($arPrice["PRINT_PRICE"])?><span class="rubl">руб.</span></div>
						<?$date1 = date('Y-m-d');?>
						<meta itemprop="price" content="<?=intval(str_replace(' ', '', $arPrice["PRINT_PRICE"]))?>">
						<meta itemprop="priceCurrency" content="RUB">
						<?if($delivery_title=='В наличии'){?>
						<meta itemprop="availability" content="http://schema.org/InStock" />
						<?}else{?>
							<meta itemprop="availability" content="http://schema.org/InStock" />
						<?}?>
						<meta itemprop="url" content="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<meta itemprop="priceValidUntil" content="<?=$date1?>T23:59">

					</div>
				</div>
				<div class="level-right">
					<?
					if($arItem["PROPERTIES"]["SYS_UNDER_THE_ORDER"]["VALUE"] == "Y")
					{
					?>
						<button class="btn is-primary buy-button buy--catalog btn-under-the-order" data-id="<?=$arItem["ID"]?>" >
							Под заказ
						</button>
					<?
					}
					else
					{
					?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>#buy" class="btn is-primary buy-button<?if($price > 0){?> add-to-basket<?}else{?> btn-grey<?}?> buy--catalog" data-id="<?=$arItem["ID"]?>" style="color: #fff;" id="buy_<?=$uid?>" <?if($price <= 0){?>onclick="return false;"<?}?> >
							Купить
						</a>
					<?
					}
					?>
				</div>
				<? //$frame->end();?>
				<?
			}
			?>
						
		</div>

		<?
		if(isset($arItem["GIFT"]) && count($arItem["GIFT"])>0)
		{
		?> 

		<? 
		global $USER;
		/*
		if(!$USER->IsAdmin()):?>
			<div class="podarok_list" data-key="<?=randString(15)?>" data-id="<?=$arItem['ID']?>">
                
				<div class="h4">Подарок при покупке</div>
				<div class="descr">
					<?
					$temp=[];
					foreach ($arItem["GIFT"] as $gift) {
						$temp[]=$gift["NAME"];
					}
					echo '<b>'.implode('</b> или <b>', $temp).'</b>';
					?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее на странице товара</a>
				</div>
                
			</div>
		<? endif;
		*/
		 ?>
		<?
		}?>
		
		<?if ($arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"){
			//($arItem["PROPERTIES"]["DISCOUNT"]["VALUE"] == "Y" || $arResult["DISCOUNT_BASKET"]["IS_DISCOUNT"]=="Y"){
		?>
<?/*
		<div class="sale_section available_section_cat">
			<i class="check-circle circle-disc"></i> Скидка при заказе онлайн <span class="bold"><?=$percentChange?>% <span class="goto-star">*</span></span>
		</div>
*/?>
		<?}?>

	</div>
	<div class="level extends is-mobile product__comparison product__comparison">
		<?/*
		<div class="props">
		<? if ($arItem["DISPLAY_PROPERTIES"]["ODDS"]["VALUE"]):
			foreach($arItem["DISPLAY_PROPERTIES"]["ODDS"]["VALUE"] AS $param):?>
				<div class="product__comparison-tooltip tooltip is-tooltip-bottom" data-tooltip="<?=$param["NAME"]?>">
					<img src="<?=$param["PREVIEW_PICTURE"]?>">
				</div>
			<?endforeach;
		endif;?>
		<? if ($arItem["DISPLAY_PROPERTIES"]["PARAMS"]):
			foreach($arItem["DISPLAY_PROPERTIES"]["PARAMS"] AS &$arOddParam):?>
				<div class="product__comparison-tooltip tooltip is-tooltip-bottom" data-tooltip="<?=$arOddParam["NAME"]?>">
					<img src="<?=$arOddParam["IMAGE"]?>" alt="<?=$arOddParam["NAME"]?>" />
				</div>
			<?endforeach;
		endifd		</div>
		*/?>
		<div class="level-right">
			<span class="icon is-small has-text-info">
				<i class="fas fa-info-circle"></i>
			</span>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="one-click buy--one-click--catalog" data-id="<?=$arItem['ID'];?>"><?=GetMessage('HDR_BUY_ONE_CLICK')?></a>
		</div>
	</div>
    <?/*?>
	<div class="product__consultant middle-header">
		<span class="speaker">
			<img class="icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png">
			<div class="dot"></div>
		</span>
		<p class="product__consultant-link"><a href="#"><?=GetMessage('CT_CONSULT_DISCOUS')?></a></p>
	</div><?/*
	<script>
		//new JSCatalogItemProduct(<?=json_encode($jsParams)?>);
	</script>*/?>
	</div>
</div>