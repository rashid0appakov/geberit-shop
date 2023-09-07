<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    $json   = [
        'status'=> 'error',
        'msg'   => ''
    ];
?>
<?
if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock") || !(int)$_REQUEST["ID"]){
    $json['msg'] = 'Ошибка сервера';
	CClass::Instance()->RenderJSON($json);
    return '';
}
use Bitrix\Sale\Discount\Gift;

$arPriceCode = CClass::getCurrentPriceCode();
// var_dump($arPriceCode);
// die;

$qnt = (float)$_REQUEST["quantity"];
$productID = (int)$_REQUEST["ID"];
$arIDs = [];
$arSet = $arSets = [];
$json['id'] = $productID;

$arItemParams = array();
if (isset($_REQUEST["PROPS"]) && !empty($_REQUEST["PROPS"])):
	$arItemParamsBefore = unserialize(base64_decode(strtr($_REQUEST["PROPS"], "-_,", "+/=")));
	foreach($arItemParamsBefore as $arProp):
		$arItemParams[] = $arProp;
	endforeach;
endif;
if (isset($_REQUEST["SELECT_PROPS"]) && !empty($_REQUEST["SELECT_PROPS"])):
	$select_props = explode("||", $_REQUEST["SELECT_PROPS"]);
	foreach($select_props as $arSelProp):
		$arItemParams[] = unserialize(base64_decode(strtr($arSelProp, "-_,", "+/=")));
	endforeach;
endif;

$arFields = array("QUANTITY" => $qnt, "DELAY" => "N");

// -- Check if the product has a set ------------------------------------ //
$arSets = CCatalogProductSet::getAllSetsByProduct(
	$productID,
	CCatalogProductSet::TYPE_SET
);

$arSet = current($arSets);
if (!empty($arSet['ITEMS']))
	foreach($arSet['ITEMS'] AS &$arItem)
		$arIDs[] = $arItem['ITEM_ID'];

// global $USER;
// if ($USER->IsAdmin()){
// 	var_dump($arIDs);	
// 	die;
// }

// -- Get basket items -------------------------------------------------- //
$resBasket = CSaleBasket::GetList(
	array(),
	array(
		"PRODUCT_ID" => !empty($arIDs) ? $arIDs : $productID,
		"FUSER_ID" => CSaleBasket::GetBasketUserID(),
		"LID" => SITE_ID,
		"ORDER_ID" => "NULL",
		"DELAY" => "Y"
	),
	false,
	false,
	array("ID")
);

while($arItem = $resBasket->Fetch()){
	// -- Update basket sets items -------------------------------------- //
	if (!empty($arIDs) && $key = array_search($arItem["ID"], $arIDs)){
		CSaleBasket::Update($arItem["ID"], $arFields);
		unset($arIDs[$key]);
	}

	// -- Update simple product ----------------------------------------- //
	if ($arItem["ID"] == $productID){
		CSaleBasket::Update($arItem["ID"], $arFields);
	}
}

// -- Add new items to a basket ----------------------------------------- //

	if (!empty($arIDs)){
		foreach($arIDs AS $pID)
			Add2BasketByProductID($pID, $qnt, array('PRODUCT_PROVIDER_CLASS'=>'CCatalogProductProviderCustom'), $arItemParams);
	}else{
		Add2BasketByProductID($productID, $qnt, array('PRODUCT_PROVIDER_CLASS'=>'CCatalogProductProviderCustom'), $arItemParams);
	}


// -- Get cart content -------------------------------------------------- //
ob_end_clean();
ob_start();
?>
<? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "popup",
	array(
		"PATH_TO_BASKET"    => SITE_DIR."personal/cart/",
		"PATH_TO_PERSONAL"  => SITE_DIR."personal/",
		"SHOW_PERSONAL_LINK"=> "N",
		"SHOW_PERSONAL_LINK"=> "N",
		"SHOW_EMPTY_VALUES" => "N",
		"SHOW_PRODUCTS"     => "N",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE"  => "Y"
		),
	false,
	array(
		"0" => ""
	)
);?>
<?
$json['status'] = 'success';
$json['cart'] = ob_get_contents();

if ($_REQUEST['MODE'] == 'CART')
	CClass::Instance()->RenderJSON($json);

// -- Get product data -------------------------------------------------- //
$arSelect= [
	"ID", "IBLOCK_ID", "NAME", "CODE", "DETAIL_PICTURE", "PROPERTY_ARTNUMBER",
	"DETAIL_PAGE_URL", "PROPERTY_SERIES", "IBLOCK_SECTION_ID"
];
$arFilter=[
	"ID"        => $productID,
	"IBLOCK_ID" => CATALOG_IBLOCK_ID
];
$ob = CIBlockElement::GetList(["NAME" => "ASC"], $arFilter, FALSE, Array("nPageSize" => 1), $arSelect);

if ($arItem = $ob->getNext()){
	$productName    = $arItem["NAME"];
	$productArtnum  = $arItem["ID"];    //$arItem["PROPERTY_ARTNUMBER_VALUE"]
	$productImageResize = CFile::ResizeImageGet(
		$arItem["DETAIL_PICTURE"],
		[
			'width' => 100,
			'height' => 100
		],
		BX_RESIZE_IMAGE_PROPORTIONAL
	);
}

$ids = array($productID);
if (count($arIDs)>0){
	$ids = $arIDs;
}
// basketItem fetch
$ob = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL",
                "PRODUCT_ID" => $ids,
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE", 
              "PRODUCT_ID", "QUANTITY", "DELAY", 
              "CAN_BUY", "PRICE", "WEIGHT")
    );

$productPrice = 0;
$productIds = array();
while ($item = $ob->Fetch()){
	$productIds[] = (int) $item["PRODUCT_ID"];

	// global $USER;
	// if ($USER->IsAdmin()){
	// 	var_dump($item);	
	// 	die;
	// }


	//if (count($arIDs)){
		$productPrice += (float) $item["PRICE"];	

		// global $USER;
		// if ($USER->IsAdmin()){
		// 	var_dump($productPrice);	
		// 	//die;
		// }

	// }else{
	// 	$productPrice = (float) $item["PRICE"];
	// }
	
}


// global $USER;
// if ($USER->IsAdmin()){
// 	var_dump($productPrice);	
// 	die;
// }

//$ar_res = CPrice::GetBasePrice($productID);

//$productPrice = (float) $ar_res["PRICE"];
ob_end_clean();
ob_start();
?>

<div class="column is-6">
	<div class="commonStyle">
		<div class="goodsAdd">
			<div class="goodsAddTitle">Товар добавлен в корзину</div>
			<div class="goodsAddItem">
				<div class="columns is-mobile">
					<div class="column is-5-mobile">
						<div class="goodsAddItemImage">
                            <div class="basket-image-wrapper">
                                <img src="<?=$productImageResize["src"]?>" alt="<?=$productName?>">
                            </div>
						</div>
					</div>
					<div class="column is-7-mobile">
						<div class="titleItem">
							<span><?=$productName?></span>
						</div>
						<div class="articleItem">
							<span>(<?=$productArtnum?>)</span>
						</div>
						<div class="price">
							<span>Цена: <?=number_format($productPrice, 0, ".", " ")?> <i class="znakrub">c</i></span>
						</div>
					</div>
				</div>
				<?
				$gifts=CGifts::getGifts($productIds);
				if(count($gifts)>0)
				{
				?>
					<div class="podarok_popup">
						<h4>Выберите подарок на странице<br>оформления заказа</h4>
					</div>
				<?
				}?>
			</div>
		</div>
	</div>
</div>
<script>
dashamail("cart.addProduct", {
    "productId": "<?=$productArtnum?>",
    "quantity": "1",
    "price": "<?=$productPrice?>"
});

</script>
<div class="column is-6"><?=$json['cart']?></div>
<?php
$json['basket'] = ob_get_contents();
$json['status'] = 'success';
ob_clean();
ob_start();

// -- Check if an item has recommended goods ---------------------------- //
$arSetItems = CCatalogProductSet::getAllSetsByProduct(
	$productID,
	CCatalogProductSet::TYPE_GROUP
);
$arSetItems = current($arSetItems);
$arIDs      = [];
$arQTYs     = [];

if (!empty($arSetItems['ITEMS']))
	foreach($arSetItems['ITEMS'] AS &$arItem){
		$arIDs[] = $arItem['ITEM_ID'];
		$arQTYs[$arItem['ITEM_ID']] = $arItem['QUANTITY'];
	}

if (!empty($arIDs)):
	// -- Get products information -------------------------------------- //
	$arProducts = [];
	$resProducts= CCatalogProduct::GetList(["QUANTITY" => "DESC"], [
		"ID" => $arIDs
		],
			false,
			array("nTopCount" => count($arIDs))
	);
	while ($arProduct = $resProducts->Fetch())
		$arProducts[$arProduct['ID']] = $arProduct;

	// -- Get goods list ------------------------------------------------ //
	$arFilter=[
		"ID" => $arIDs,
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
        ">CATALOG_QUANTITY" => "0",
	];
	$ob = CIBlockElement::GetList(["NAME" => "ASC"], $arFilter, FALSE, [], $arSelect);

	$arItems    = [];
	while($arItem = $ob->getNext()){
		$arItem['RESIZED'] = CFile::ResizeImageGet(
			$arItem["DETAIL_PICTURE"],
			[
				'width' => 80,
				'height'=> 80
			],
			BX_RESIZE_IMAGE_PROPORTIONAL
		);
		$arItems[]= $arItem;

	}
	if (!empty($arItems)):
		?>
		<div class="columns hide-mobile is-mobile goodsSupplyTableHead">
			<div class="column is-5-desktop is-8-mobile">Наименование</div>
			<div class="column is-2-desktop is-2-mobile">КОЛ-ВО</div>
			<div class="column is-2-desktop is-2-mobile">СТОИМОСТЬ</div>
			<div class="column is-3-desktop is-12-mobile"></div>
		</div>
		<?foreach($arItems AS $k => &$arItem):
            $arSectionsNames= [];
			$arPrice        = CPrice::GetBasePrice($arItem['ID']);
			

            $arSectionPath  = CClass::getSectionPath($arItem['IBLOCK_SECTION_ID'], CClass::getCatalogSection());

           
            foreach($arSectionPath AS $arSection)
                $arSectionsNames[] = $arSection['NAME'];
			?>
			<div class="columns is-multiline is-mobile goodsSupplyTableString"
                data-id="<?=$arItem['ID']?>"
                data-name="<?=$arItem['NAME']?>"
                data-position="<?=$k?>"
                data-price="<?=(int) $arPrice['PRICE']?>"
                data-artnumber="<?=$arItem["PROPERTY_ARTNUMBER_VALUE"]?>"
                data-section="<?=join(' / ', $arSectionsNames)?>"
                data-collection="<?=$GLOBALS['PAGE_DATA']['INFO_SERIES'][$arItem['PROPERTY_SERIES_VALUE']]['NAME']?>"
                data-discount="<?=((int) $arPrice['DISCOUNT_PRICE'] ? 'yes' : 'no')?>">
				<div class="column is-5-desktop is-12-mobile">
					<div class="columns is-mobile">
						<div class="column is-2-desktop is-4-mobile">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>" target="_blank">
								<img src="<?=$arItem['RESIZED']['src']?>" alt="<?=$arItem['NAME']?>" />
							</a>
						</div>
						<div class="column is-9-desktop is-7-mobile titleColumn">
							<div>
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
							</div>
							<div>
								<span class="article">
									<span>Код товара:</span> <span> <?=$arItem['ID']?></span>
								</span>
								<?/*<span>В наличии</span>*/?>
							</div>
						</div>
					</div>
				</div>
				<div class="column is-2-desktop is-4-mobile">
					<div class="cart__counter" data-entity="basket-item-quantity-block">
						<button class="cart__counter-minus">-</button>
						<div class="cart__counter-counter" data-value="<?=$arQTYs[$arItem['ID']]['QUANTITY']?>" data-max="<?=$arProducts[$arItem['ID']]['QUANTITY']?>" data-id="<?=$arItem['ID']?>"><?=$arQTYs[$arItem['ID']]['QUANTITY']?></div>
						<button class="cart__counter-plus">+</button>
					</div>
				</div>
				<div class="column is-2-desktop is-7-mobile">
					<div class="price">
						<span><?=customFormatPrice(CurrencyFormat($arPrice["PRICE"], $arPrice["CURRENCY"]))?></span>
					</div>
				</div>
				<div class="column is-3-desktop is-12-mobile text-center">
					<a class="btn is-primary btnPlaceOrder" href="#" data-id="<?=$arItem['ID']?>">
						<span class="label-desktop">Добавить</span>
					</a>
					<div class="goodsAddTitle">Добавлен</div>
				</div>
			</div>
			

			<?
        endforeach;
    endif;
endif;
$json['set'] = ob_get_contents();
ob_clean();
?>
<?CClass::Instance()->RenderJSON($json);?>