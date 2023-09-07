<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (
	!check_bitrix_sessid()
	||
	!Loader::includeModule("sale")
	||
	!Loader::includeModule("catalog")
	||
	!Loader::includeModule("iblock")
)
{
	__ReturnError("Ошибка");
}


$request = Application::getInstance()->getContext()->getRequest();

$basketItemId = (int) $request["basketItemId"];

if (!$basketItemId)
	__ReturnError("Не указан ID корзины");


// basketItem fetch
$ob = CSaleBasket::GetList(
	array(),
	array(
		"ID" => $basketItemId,
	)
);
if ($item = $ob->Fetch())
{
	$productId = (int) $item["PRODUCT_ID"];
	$productPrice = (float) $item["PRICE"];
}
else
{
	__ReturnError("Корзина не найдена");
}

// product fetch
$ob = CIBlockElement::GetList(
	array(),
	array(
		"ID" => $productId,
	),
	false,
	false,
	array(
		"ID",
		"IBLOCK_ID",
		"NAME",
		"DETAIL_PICTURE",
		"PROPERTY_ARTNUMBER",
	)
);
if ($item = $ob->Fetch())
{
	$productName = $item["NAME"];
	$productArtnum = $item["PROPERTY_ARTNUMBER_VALUE"];
	$productImageResize = CFile::ResizeImageGet($item["DETAIL_PICTURE"], array('width' => 120, 'height' => 120), BX_RESIZE_IMAGE_PROPORTIONAL);
}
else
{
	__ReturnError("Товар не найден");
}

// basketInfo fetch
$basketProductCount = 0;
$basketPrice = 0.0;
$ob = CSaleBasket::GetList(
	array(),
	array(
		"FUSER_ID" => CSaleBasket::GetBasketUserID(),
		"LID" => SITE_ID,
		"ORDER_ID" => "NULL",							
		"CAN_BUY" => "Y",							
		"DELAY" => "N",							
	)
);
while ($arItem = $ob->Fetch())
{
	$itemId = $arItem["ID"];
	$productId = $arItem["PRODUCT_ID"];
	$price = (float) $arItem["PRICE"];
	$quantity = $arItem["QUANTITY"];

	$basketProductCount++;
	$basketPrice += $price * $quantity;
	
	/*
	echo "<pre>";
	print_r($arItem);
	echo "</pre>";
	*/
	
}


ob_start();?>
	<div class="close69">
		<img src="<?=SITE_DEFAULT_PATH?>/images/close.png">
	</div>
	<div class="columns">
		<div class="column is-6">
			<div class="commonStyle">
				<div class="goodsAdd">
					<div class="goodsAddTitle">Товар добавлен в корзину</div>
					<div class="goodsAddItem">
						<div class="columns is-mobile">
							<div class="column is-5-mobile">
								<div class="goodsAddItemImage">
									<img src="<?=$productImageResize["src"]?>" alt="<?=$productName?>">
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
					</div>
				</div>
			</div>
		</div>
		<div class="column is-6">
		
		
			<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket","new-popup",Array(
					"HIDE_ON_BASKET_PAGES" => "Y",
					"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
					"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
					"PATH_TO_PERSONAL" => SITE_DIR."personal/",
					"PATH_TO_PROFILE" => SITE_DIR."personal/",
					"PATH_TO_REGISTER" => SITE_DIR."login/",
					"POSITION_FIXED" => "Y",
					"POSITION_HORIZONTAL" => "right",
					"POSITION_VERTICAL" => "top",
					"SHOW_AUTHOR" => "Y",
					"SHOW_DELAY" => "N",
					"SHOW_EMPTY_VALUES" => "Y",
					"SHOW_IMAGE" => "Y",
					"SHOW_NOTAVAIL" => "N",
					"SHOW_NUM_PRODUCTS" => "Y",
					"SHOW_PERSONAL_LINK" => "N",
					"SHOW_PRICE" => "Y",
					"SHOW_PRODUCTS" => "Y",
					"SHOW_SUMMARY" => "Y",
					"SHOW_TOTAL_PRICE" => "Y"
				)
			);?>

			
		</div>
	</div>
<?$html = ob_get_clean();


__ReturnAnswer(array(
	"basketItemId" => $basketItemId,
	"html" => base64_encode(json_encode($html)),
));





function __ReturnAnswer($data)
{
	echo json_encode($data);
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
	die();
}

function __ReturnError($message)
{
	__ReturnAnswer(array(
		"error" => $message,
	));
}

