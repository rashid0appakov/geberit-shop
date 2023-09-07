<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $templateFolder
 * @var string $templateName
 * @var CMain $APPLICATION
 * @var CBitrixBasketComponent $component
 * @var CBitrixComponentTemplate $this
 * @var array $giftParameters
 */

$this->setFrameMode(true);

$APPLICATION->SetAdditionalCSS($templateFolder . '/css/cart.css');
$APPLICATION->SetAdditionalCSS($templateFolder . '/css/goods-list.css');
$APPLICATION->AddHeadScript($templateFolder . '/scripts4actions.js');

$documentRoot = Main\Application::getDocumentRoot();

if (empty($arParams['TEMPLATE_THEME']))
{
	$arParams['TEMPLATE_THEME'] = Main\ModuleManager::isModuleInstalled('bitrix.eshop') ? 'site' : 'blue';
}

if ($arParams['TEMPLATE_THEME'] === 'site')
{
	$templateId = Main\Config\Option::get('main', 'wizard_template_id', 'eshop_bootstrap', $component->getSiteId());
	$templateId = preg_match('/^eshop_adapt/', $templateId) ? 'eshop_adapt' : $templateId;
	$arParams['TEMPLATE_THEME'] = Main\Config\Option::get('main', 'wizard_'.$templateId.'_theme_id', 'blue', $component->getSiteId());
}

if (!empty($arParams['TEMPLATE_THEME']))
{
	if (!is_file($documentRoot.'/bitrix/css/main/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
	{
		$arParams['TEMPLATE_THEME'] = 'blue';
	}
}

if (!isset($arParams['DISPLAY_MODE']) || !in_array($arParams['DISPLAY_MODE'], array('extended', 'compact')))
{
	$arParams['DISPLAY_MODE'] = 'extended';
}

$arParams['USE_DYNAMIC_SCROLL'] = isset($arParams['USE_DYNAMIC_SCROLL']) && $arParams['USE_DYNAMIC_SCROLL'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_FILTER'] = isset($arParams['SHOW_FILTER']) && $arParams['SHOW_FILTER'] === 'N' ? 'N' : 'Y';

$arParams['PRICE_DISPLAY_MODE'] = isset($arParams['PRICE_DISPLAY_MODE']) && $arParams['PRICE_DISPLAY_MODE'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['TOTAL_BLOCK_DISPLAY']) || !is_array($arParams['TOTAL_BLOCK_DISPLAY']))
{
	$arParams['TOTAL_BLOCK_DISPLAY'] = array('top');
}

if (empty($arParams['PRODUCT_BLOCKS_ORDER']))
{
	$arParams['PRODUCT_BLOCKS_ORDER'] = 'props,sku,columns';
}

if (is_string($arParams['PRODUCT_BLOCKS_ORDER']))
{
	$arParams['PRODUCT_BLOCKS_ORDER'] = explode(',', $arParams['PRODUCT_BLOCKS_ORDER']);
}

$arParams['USE_PRICE_ANIMATION'] = isset($arParams['USE_PRICE_ANIMATION']) && $arParams['USE_PRICE_ANIMATION'] === 'N' ? 'N' : 'Y';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

if ($arParams['USE_GIFTS'] === 'Y')
{
	$giftParameters = array(
		'SHOW_PRICE_COUNT' => 1,
		'PRODUCT_SUBSCRIPTION' => 'N',
		'PRODUCT_ID_VARIABLE' => 'id',
		'PARTIAL_PRODUCT_PROPERTIES' => 'N',
		'USE_PRODUCT_QUANTITY' => 'N',
		'ACTION_VARIABLE' => 'actionGift',
		'ADD_PROPERTIES_TO_BASKET' => 'Y',

		'BASKET_URL' => $APPLICATION->GetCurPage(),
		'APPLIED_DISCOUNT_LIST' => $arResult['APPLIED_DISCOUNT_LIST'],
		'FULL_DISCOUNT_LIST' => $arResult['FULL_DISCOUNT_LIST'],

		'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
		'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_SHOW_VALUE'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],

		'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
		'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
		'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
		'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
		'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
		'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
		'SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
		'SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
		'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
		'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
		'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
		'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
		'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],

		'LINE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],

		'DETAIL_URL' => isset($arParams['GIFTS_DETAIL_URL']) ? $arParams['GIFTS_DETAIL_URL'] : null
	);
}

\CJSCore::Init(array('fx', 'popup', 'ajax'));

$this->addExternalJs($templateFolder.'/js/mustache.js');
$this->addExternalJs($templateFolder.'/js/action-pool.js');
$this->addExternalJs($templateFolder.'/js/filter.js');
$this->addExternalJs($templateFolder.'/js/component.js');
$this->addExternalJs($templateFolder.'/js/cart.js');

$mobileColumns = isset($arParams['COLUMNS_LIST_MOBILE'])
	? $arParams['COLUMNS_LIST_MOBILE']
	: $arParams['COLUMNS_LIST'];
$mobileColumns = array_fill_keys($mobileColumns, true);

$jsTemplates = new Main\IO\Directory($documentRoot.$templateFolder.'/js-templates');
/** @var Main\IO\File $jsTemplate */
foreach ($jsTemplates->getChildren() as $jsTemplate)
{
    include($jsTemplate->getPath());
}

$displayModeClass = $arParams['DISPLAY_MODE'] === 'compact' ? ' basket-items-list-wrapper-compact' : '';

$showCoupon = !false;

$arMapCredit = array('s0', 'l1', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 'sa', 'sr', 'sb');

$sumPrice = 0;
foreach ($arResult['ITEMS']['AnDelCanBuy'] as $item){
	$sumPrice += $item['PRICE'] * $item['QUANTITY'];
}

if (empty($arResult['ERROR_MESSAGE']))
{
    if ($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP') {
        $APPLICATION->IncludeComponent(
            'bitrix:sale.gift.basket',
            '.default',
            $giftParameters,
            $component
        );
    }

    if ($arResult['BASKET_ITEM_MAX_COUNT_EXCEEDED']) {
        ?>
        <div id="basket-item-message">
            <?= Loc::getMessage('SBB_BASKET_ITEM_MAX_COUNT_EXCEEDED', array('#PATH#' => $arParams['PATH_TO_BASKET'])) ?>
        </div>
        <?
    }
    $i = 0;
    ?>
    <script type="text/javascript">
        var products_ecomers = [], good_ids = [], products_rasrochka = {}, mode = 'ONE';
    </script>
    <div class="cart" id="basket-root">
        <div class="categoryWrapper">
            <div class="container goods__container">
                <div class="goods__breadcrumbs"><?/*
				<ul class="breadcrumbs">
					<li class="breadcrumbs__item">
						<a href="#">Главная</a>
					</li>
					<li class="breadcrumbs__item">
						<span> Шоурум</span>
					</li>
				</ul>*/
                    ?>
                </div>
                <div class="wrapper">
                    <div class="cart" id="basket-items-list-wrapper">
                        <div class="columns">
                            <div class="column is-2">
                                <div class="title">
                                    <h2 class="goods__title-title">Корзина</h2>
                                </div>
                            </div>
                            <div class="column is-2">
                                <div class="clearCart">
                                    <a href="javascript:void(0);"
                                       class="empty-cart-btn" onclick='dashamail("cart.clear");'><span><?= GetMessage('SBB_CLEAR') ?></span></a>
                                </div>
                            </div>
                            <div class="column is-2 is-12-mobile">
                                <div class="sendOrder">
                                    <div class="buy__item buy__item-buy--desktop">
                                        <div class="level-right buy__item-button">
                                            <a href="#make-order" class="btn is-primary is-outlined">Оформить заказ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            if (CREDIT_ENABLE && in_array(SITE_ID, $arMapCredit) && $sumPrice > 3000 && $sumPrice < 200000) {
                                ?>
                                <div class="column is-2 is-12-mobile">
                                    <div class="buy__item buy__item-buy--desktop">
                                        <div class="level-right buy__item-button button_credit button_credit_cart">
                                            <a href="#" onclick="buyRasrochka(products_rasrochka, event, mode)"
                                               class="btn is-primary is-outlined">Купить в рассрочку</a>
                                        </div>
                                    </div>
                                </div>
                            <?
                            } ?>
                        </div>
                        <?/*<div id="basket-root" class="bx-basket bx-<?=$arParams['TEMPLATE_THEME']?> bx-step-opacity" style="opacity: 0;"> -->*/
                        ?>

                        <form class="basket">
                            <div class="tableCart" id="basket-items-list-container">
                                <div class="tbodyCart" id="basket-item-list">
                                    <div class="columns tHeadCart">
                                        <div class="column is-6 tdCart">НАИМЕНОВАНИЕ</div>
                                        <div class="column is-2 tdCart">ЦЕНА</div>
                                        <div class="column is-2 tdCart">КОЛИЧЕСТВО</div>
                                        <div class="column is-2 tdCart">СУММА</div>
                                    </div>
                                    <div id="basket-item-table">
                                        <?
                                        foreach ($arResult['ITEMS']['AnDelCanBuy'] as $item):?>
                                            <?
                                            if (!$showCoupon) {
                                                //pr($item);
                                            }

                                            $elementRes = CIBlockElement::GetByID($item['PRODUCT_ID']);
                                            $arElement = $elementRes->GetNext();
                                            $sectionID = $arElement['IBLOCK_SECTION_ID'];
                                            $iblockID = $arElement['IBLOCK_ID'];
                                            $sectionRes = CIBlockSection::GetList(
                                                [],
                                                ['IBLOCK_ID' => $iblockID, 'ID' => $sectionID],
                                                true,
                                                ['UF_INSTALLATION']
                                            );
                                            $section = $sectionRes->GetNext();
                                            $installationPrice = $section['UF_INSTALLATION'];
                                            while (!$installationPrice && $section['IBLOCK_SECTION_ID']) {
                                                $sectionRes = CIBlockSection::GetList(
                                                    [],
                                                    ['IBLOCK_ID' => $iblockID, 'ID' => $section['IBLOCK_SECTION_ID']],
                                                    true,
                                                    ['UF_INSTALLATION', 'IBLOCK_SECTION_ID']
                                                );
                                                $section = $sectionRes->GetNext();
                                                $installationPrice = $section['UF_INSTALLATION'];
                                            }
                                            $installationPrice = CurrencyFormat($installationPrice, 'RUB');
                                            if ($installationPrice) {
                                                $propsRes = CSaleBasket::GetPropsList(
                                                    array("SORT" => "ASC", "NAME" => "ASC"),
                                                    array("BASKET_ID" => $item['ID'])
                                                );
                                                $needInstall = $propsRes->Fetch()['VALUE'];
                                            }
                                            /*global $USER;
                                            if ($USER->IsAdmin()){
                                            echo "<pre>";
                                            	var_dump($item);
                                            	echo "</pre>";	
                                            }*/
                                            
                                            ?>

                                            <div class="columns trCart" id="basket-item-<?= $item['ID'] ?>"
                                                 data-entity="basket-item" data-id="<?= $item['ID'] ?>">

                                                <div class="column is-2 is-3-mobile tdCart">
                                                    <?php if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST'])): ?>
                                                        <div>
                                                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>"
                                                               class="basket-item-image-link">
                                                                <img class="basket-item-image"
                                                                     alt="<?= $item['NAME'] ?>"
                                                                     src="<?php if ($item['PREVIEW_PICTURE_SRC']) echo $item['PREVIEW_PICTURE_SRC']; else echo $templateFolder . "/images/no_photo.png" ?>">
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                                <?

                                                // логика доставки 1-2-30-45-360 дней - начало
                                                $geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");

                                                $STORE_ID = GetStoreId();
                                                $sale_price_id = GetSaleStoreId();

                                                $element = GetIBlockElement($item["PRODUCT_ID"]);
                                                $product = CCatalogProduct::GetByIDEx($item["PRODUCT_ID"]);
                                                $element['PRODUCT'] = $product['PRODUCT'];

                                                $data = GetDeliveryDataForElementByArr($element);
                                                $delivery_title = $data['delivery_title'];
                                                $delivery_days_title = $data['delivery_days_title'];
                                                $peremeshen = $data['peremeshen'];
                                                $q = $data['q'];

                                                if ($q == 0){
                                                    $element["PRODUCT"]['QUANTITY'] = 0;
                                                }

                                                //var_dump($data);
                                                  if (($product['PRICES'][$sale_price_id]['PRICE'] > 0) && ($product['PRICES'][$sale_price_id]['PRICE'] > $item["PRICE"])){
												    	$item['SUM_DISCOUNT_PRICE'] = $product['PRICES'][$sale_price_id]['PRICE'];
												    	$item['FULL_PRICE_FORMATED'] = CurrencyFormat($item['SUM_DISCOUNT_PRICE'], $item['CURRENCY']);
												    	$item['SUM_DISCOUNT_PRICE_FORMATED'] = CurrencyFormat(($item['SUM_DISCOUNT_PRICE'] - $item["PRICE"])*$item['QUANTITY'], $item['CURRENCY']) ;

												    	$item['SUM_FULL_PRICE_FORMATED'] = CurrencyFormat($item['SUM_DISCOUNT_PRICE']*$item['QUANTITY'], $item['CURRENCY']);
														//$arPrice["PRINT_BASE_PRICE"] = CurrencyFormat($oldPrice, $arPrice['CURRENCY']);
												    }	
                                                ?>
                                                <div class="column is-4 is-8-mobile tdCart">
                                                    <div class="titleItem">
                                                        <a href="<?= $item['DETAIL_PAGE_URL'] ?>">
                                                            <?= $item['NAME'] ?>
                                                        </a>
                                                            <?
                                                        echo '<br><br><font style="color: rgb(88, 176, 96)">'.$delivery_title.'</font>';
                                                        if (strlen($delivery_days_title)>0){
                                                            echo '<font style="color: rgb(88, 176, 96)">, cрок доставки: '.$delivery_days_title.'</font>';
                                                            //var_dump($delivery_days_title);
                                                        }
                                                        ?>
                                                        <div>
                                                            <div class="filter__checkbox">
                                                                <?php if ($installationPrice): ?>
                                                                    <input class="installation"
                                                                           id="install-item-<?= $item['ID'] ?>"
                                                                           data-id="<?= $item['ID'] ?>" type="checkbox"
                                                                        <?php if ($needInstall > 0) echo "checked" ?>
                                                                    >
                                                                    <label for="install-item-<?= $item['ID'] ?>">Интресует
                                                                        установка этого товара
                                                                        (от <?= $installationPrice ?>)</label>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?
                                                    $gifts=CGifts::getComplect($item['PRODUCT_ID']);

                                                   
   
                                                    $gifts=count($gifts)>0?$gifts:CGifts::getGifts([$item['PRODUCT_ID']]);
                                                    //$gifts=CGifts::getGifts([$item['PRODUCT_ID']]);

                                                    
                                                    
                                                    if(count($gifts[$item['PRODUCT_ID']])>0 && !in_array($item['PRODUCT_ID'], $skipItem))
                                                    {
                                                        $skipItem[]=$item['PRODUCT_ID'];
                                                        ?>

                                                            <div class="podarok_busket">
                                                                <h4>Выберите подарок</h4>
                                                                <div class="listPodarok" data-productid="<?=$item['PRODUCT_ID']?>">
                                                                    <?foreach ($gifts[$item['PRODUCT_ID']] as $id => $gift){
                                                                        $selected=CGifts::getGiftBasketItem($item['PRODUCT_ID'])===$id;
                                                                        ?>
                                                                        <div class="item" data-id="<?=$id?>">
                                                                            <label class="radio__outer">
                                                                                <input type="radio" <?=$selected!==false?'checked="checked"':''?> name="choosePodarok_<?=$item['PRODUCT_ID']?>" class="choosePodarok" value="<?=$id?>">
                                                                                <span class="checkmark"></span>
                                                                            </label>
                                                                            <?if(intval($gift["DETAIL_PICTURE"])>0){
                                                                                $pic=CGifts::getPreviewPhoto($gift["DETAIL_PICTURE"]);
                                                                                ?>
                                                                                <a target="_blank" href="<?=$gift["DETAIL_PAGE_URL"]?>" class="img"><img src="<?=$pic?>" alt="<?=$gift["NAME"]?>"></a>
                                                                            <?}?>
                                                                            <a target="_blank" href="<?=$gift["DETAIL_PAGE_URL"]?>" class="name"><?=$gift["NAME"]?></a>
                                                                        </div>
                                                                    <?}?>
                                                                </div>
                                                            </div>
                                                        <?
                                                    }?>
                                                </div>
                                                <div class="column is-2 is-7-mobile tdCart">
                                                    <div class="tdTitleMobile margin-left-45">
                                                        ЦЕНА
                                                    </div>

                                                    <div class="basket-item-price-old">
                                                    <span class="basket-item-price-old-text"
                                                          id="basket-item-price-old-text-<?= $item['ID'] ?>">
                                            		<? if ($item['SUM_DISCOUNT_PRICE'] > 0)
                                                        echo $item['FULL_PRICE_FORMATED']
                                                    ?>
                                                    </span>
                                                    </div>
                                                    <div class="priceItem">
                                                <span id="basket-item-price-<?= $item['ID'] ?>">
                                                    <?= $item['PRICE_FORMATED'] ?>
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="column is-2 is-4-mobile tdCart">
                                                    <div class="tdTitleMobile">
                                                        КОЛИЧЕСТВО
                                                    </div>
                                                    <div class="cart__content-counter">
                                                        <div class="cart__counter"
                                                             data-entity="basket-item-quantity-block">
                                                            <button class="cart__counter-minus">-</button>
                                                            <div class="cart__counter-counter"
                                                                 data-value="<?= $item['QUANTITY'] ?>"
                                                                 data-id="<?= $item['ID'] ?>"><?= $item['QUANTITY'] ?></div>
                                                            <button class="cart__counter-plus">+</button>
                                                            <input
                                                                    type="hidden"
                                                                    name="BASKET[<?= $item['ID'] ?>]"
                                                                    value="<?= $item['QUANTITY'] ?>"
                                                                    data-value="<?= $item['QUANTITY'] ?>"
                                                                    id="basket-item-quantity-<?= $item['ID'] ?>"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column is-1 is-7-mobile tdCart">
                                                    <div class="tdTitleMobile margin-left-45">
                                                        СУММА
                                                    </div>
                                                    <div class="basket-item-price-old">
                                                    <span class="basket-item-price-old-text"
                                                          id="basket-item-sum-price-old-<?= $item['ID'] ?>">
                                            		<? if ($item['SUM_DISCOUNT_PRICE'] > 0)
                                                        echo $item['SUM_FULL_PRICE_FORMATED'];
                                                    ?>
                                                    </span>
                                                    </div>
                                                    <div class="totalPriceItem">
                                                <span id="basket-item-sum-price-<?= $item['ID'] ?>">
                                                    <?= $item['SUM'] ?>
                                                </span>
                                                    </div>
                                                    <div id="basket-item-sum-price-difference-<?= $item['ID'] ?>">
                                                        <?php if ($item['SUM_DISCOUNT_PRICE'] > 0): ?>
                                                            <div class="basket-item-price-difference">
                                                                <?= Loc::getMessage('SBB_BASKET_ITEM_ECONOMY') ?>
                                                                <span style="white-space: nowrap;">
                                                    <?= $item['SUM_DISCOUNT_PRICE_FORMATED'] ?>
                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="column is- is-4-mobile tdCart basket-items-list-item-remove">
                                                    <div class="deleteItemCart basket-item-block-action">
                                                        <a href="javascript:void(0);" class="basket-item-action-remove" onclick='dashamail("cart.removeProduct", { "productId": "<?= $item['ID'] ?>", "quantity": "<?= $item['QUANTITY'] ?>"});'>
                                                            <img src="<?= $templateFolder ?>/images/closeIconBig.png"
                                                                 alt="">
                                                            <span class="basket-item-actions-remove1">Удалить</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                                products_ecomers[<?=$i?>] = {
                                                    'name': '<?= $item['NAME'] ?>',
                                                    'id': '<?= $item['PRODUCT_ID'] ?>',
                                                    'price': '<?= $item['PRICE'] ?>',
                                                    'quantity': <?= $item['QUANTITY'] ?>
                                                };

                                                good_ids[<?=$i?>] = '<?= $item['PRODUCT_ID'] ?>';

                                                products_rasrochka[<?=$i?>] = {
                                                    name: '<?=htmlspecialchars($item['NAME'], ENT_QUOTES)?>',
                                                    quantity: '<?=$item['QUANTITY']?>',
                                                    price: '<?=$item['PRICE']?>',
                                                    id: '<?=$item['PRODUCT_ID']?>'
                                                };
                                            </script>
                                            <?php
                                            $i++;
                                        endforeach; ?>
                                    </div>
                                    <?


                                    ?>
                                    <div data-entity="basket-total-block">
                                        <div class="columns tFooterCart">
                                            <div class="column is-8 is-12-mobile tdCart">
                                                <?
                                                if ($showCoupon):?>
                                                    <?
                                                    if(!empty($arResult['COUPON_LIST']))
                                                    {
                                                        foreach ($arResult['COUPON_LIST'] as $coupon)
                                                        {
                                                           
                                                            if ($coupon['JS_STATUS'] !== 'APPLYED')
                                                            {
                                                                \Bitrix\Main\Loader::includeModule('sale');
                                                                \Bitrix\Sale\DiscountCouponsManager::init();
                                                                \Bitrix\Sale\DiscountCouponsManager::clear(true);
                                                                \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
                                                                $arResult["COUPON"]="";
                                                            }
                                                            
                                                        }
                                                    }
                                                    ?>
                                                    
                                                    

                                                    <div class="enterCoupon">
                                                        <label>
                                                            <span>Применить промокод: </span>
                                                            <div>
                                                            <input type="text" class="couponInput"
                                                                   style="" placeholder=""
                                                                   data-entity="basket-coupon-input" <?
                                                            if ($arResult['COUPON']) echo "value='$arResult[COUPON]' disabled"; ?>>
                                                            <?if($arResult["COUPON"]){?>
                                                            <button id="delCupon"></button>
                                                            <?}?>
                                                            </div>
                                                        </label>
                                                        
                                                    </div>
                                                    <div class="himselfButtonBlock forCoupon" style="">
                                                        <a class="btn is-primary" id="applyCouponBtn">
                                                            <!-- "is-disabled" class for empty basket -->
                                                            <span class="label-desktop">OK</span>
                                                        </a>
                                                    </div>
                                                    <style>
                                                        .enterCoupon div{
                                                          position: relative;
                                                          
                                                          display: inline-block;
                                                        }

                                                        .enterCoupon input[type='text'] {
                                                          position: relative;
                                                          
                                                          padding-right: 25px;
                                                        }

                                                        .enterCoupon button {
                                                          position: absolute;
                                                          right: 0;
                                                          top: 0;
                                                          width: 45px;
                                                          height: 50px;
                                                          border: none;
                                                          cursor: pointer;
                                                          background-image: url('/local/templates/.default/components/bitrix/sale.basket.basket/restyle/images/closeIconBig.png');
                                                          background-repeat: no-repeat;
                                                          background-position: center;
                                                          border-radius:6px;
                                                          background-color: #eef2f7;
                                                        }
                                                    </style>
                                                <?endif ?>
                                            </div>

                                            <div class="column is-2 is-6-mobile tdCart">
                                            <span class="subtotal">
                                                Сумма заказа:
                                            </span>
                                            </div>
                                            <div class="column is-2 is-6-mobile tdCart">
                                            <span class="subtotal" id="basket-full-total"
                                                  data-price="<?= $arResult['allSum'] ?>">
                                                <?= $arResult['allSum_FORMATED'] ?>
                                            </span>
                                            </div>
<div class="sales-no-summ"><?=GetMessage('SBB_SALES_NO_SUMM')?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
				</div>
			</div>
		</div>
	</div>
</div>
	<?
	if (!empty($arResult['CURRENCIES']) && Main\Loader::includeModule('currency'))
	{
		CJSCore::Init('currency');
		?>
		<script>
			BX.Currency.setCurrencies(<?=CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true)?>);
		</script>
		<?
	}

	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedTemplate = $signer->sign($templateName, 'sale.basket.basket');
	$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.basket.basket');
	$messages = Loc::loadLanguageFile(__FILE__);
	?>
	<script type="text/javascript">
        <?/*BX.message(<?=CUtil::PhpToJSObject($messages)?>);
		 BX.Sale.BasketComponent.init({
		 	result: <?=CUtil::PhpToJSObject($arResult, false, false, true)?>,
		 	params: <?=CUtil::PhpToJSObject($arParams)?>,
		 	template: '<?=CUtil::JSEscape($signedTemplate)?>',
		 	signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
		 	siteId: '<?=$component->getSiteId()?>',
		 	ajaxUrl: '<?=CUtil::JSEscape($component->getPath().'/ajax.php')?>',
		 	templateFolder: '<?=CUtil::JSEscape($templateFolder)?>'
		});*/?>
		$(document).ready(function() {
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
			 'ecommerce': {
			   'currencyCode': 'RUB',
			   'checkout': {
				'actionField': {'step': 1},
				'products': products_ecomers
				}
			 },
			 'goods_id': good_ids,
			 'goods_price': <?= $arResult['allSum'] ?>,
			 'page_type': 'cart',
			 'event': 'pixel-mg-event',
			 'pixel-mg-event-category': 'Enhanced Ecommerce',
			 'pixel-mg-event-action': 'Checkout Step 1',
			 'pixel-mg-event-non-interaction': 'False'
			});

		});
	</script>
	<?
	if ($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
	{
		$APPLICATION->IncludeComponent(
			'bitrix:sale.gift.basket',
			'.default',
			$giftParameters,
			$component
		);
	}
?>
<input type="hidden" id="event-basket-changed" />
<?
}else{
	//ShowError($arResult['ERROR_MESSAGE']);
?>
    <div class="goods">
        <div class="container goods__container">
            <div class="goods__breadcrumbs">
                <ul id="breadcrumbs_5c66abe488c8e" class="breadcrumbs">
                    <li class="item">
                        <a href="/">Главная</a>
                    </li>
                </ul>
                <div class="breadcrumbs__need-help">
                    <a href="#">Нужна помощь в выборе душевой кабины?</a>
                </div>
            </div>

            <div class="goods__wrapper">
                <div class="goods__card">

                    <div class="search-page">
                        <form action="" method="get">
                            <input type="text" name="q" value="gfngh" size="40">
                            &nbsp;<input type="submit" value="Искать">
                            <input type="hidden" name="how" value="r">
                        </form><br>
                    </div>

                    <div class="b-empty">
                        <h2 class="b-empty-title b-title--h1">Ваша корзина пуста.</h2>
                        <div class="b-empty-result__icon"></div>
                        <div class="b-empty__text"> Индивидуальный подбор товара по телефону (бесплатно по России):</div>
                        <div class="b-empty__phone call_phone_6">8 (800) 777-08-96</div>
                    </div>

                    <style>
                        .goods__card {
                            width: 100%;
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
<? } ?>

<script>
    $(document).ready(function(){
        $("input[class='choosePodarok']").click(function(){
            var giftid = $(this).val();
            var productid = $(this).closest('.listPodarok').data('productid');
            if(giftid){
                BX.ajax.loadJSON(
                    "/ajax/setGift.php",
                    {giftid:giftid,productid:productid},
                    function (response){
                        if(response>0)
                        {
                        }
                    },
                    function (){
                    
                    }
                );
            }
        });
    });
    BX.ready(function(){
        BX.addCustomEvent('itco:OnBasketChange', function(data){
            if (data['status'] == 'success'){
                let newBasket={};
                for (var key in data.BASKET.ITEMS) {
                    item=data.BASKET.ITEMS[key];
                    newBasket[key]={name:item.NAME,quantity:item.QUANTITY.toString(),price:item.PRICE_NOT_FORMATED.toString(),id:item.PID.toString(),};
                }
                products_rasrochka=newBasket;
            }
        });
        BX.addCustomEvent('itco:onDelFromBasket', function(data){
            let temp={},i=0;
            for (var key in products_rasrochka) {
                if(products_rasrochka[key].id!==data.PRODUCT_ID)
                {
                    temp[i++]=products_rasrochka[key];
                }
            }
            products_rasrochka=temp;
        });
    });
</script>