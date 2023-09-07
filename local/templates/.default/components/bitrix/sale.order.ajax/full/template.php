<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var SaleOrderAjax $component
 * @var string $templateFolder
 */
    CJSCore::Init(array('currency'));
	$currencyFormat = CCurrencyLang::GetFormatDescription('RUB');

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$regionId= $request->getCookie("GEOLOCATION_ID");
             	
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
	if (!is_file(Main\Application::getDocumentRoot().'/bitrix/css/main/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
	{
		$arParams['TEMPLATE_THEME'] = 'blue';
	}
}

$arParams['ALLOW_USER_PROFILES'] = $arParams['ALLOW_USER_PROFILES'] === 'Y' ? 'Y' : 'N';
$arParams['SKIP_USELESS_BLOCK'] = $arParams['SKIP_USELESS_BLOCK'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['SHOW_ORDER_BUTTON']))
{
	$arParams['SHOW_ORDER_BUTTON'] = 'final_step';
}

$arParams['SHOW_TOTAL_ORDER_BUTTON'] = $arParams['SHOW_TOTAL_ORDER_BUTTON'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] = $arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_PAY_SYSTEM_INFO_NAME'] = $arParams['SHOW_PAY_SYSTEM_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_LIST_NAMES'] = $arParams['SHOW_DELIVERY_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_INFO_NAME'] = $arParams['SHOW_DELIVERY_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_PARENT_NAMES'] = $arParams['SHOW_DELIVERY_PARENT_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_STORES_IMAGES'] = $arParams['SHOW_STORES_IMAGES'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['BASKET_POSITION']) || !in_array($arParams['BASKET_POSITION'], array('before', 'after')))
{
	$arParams['BASKET_POSITION'] = 'after';
}

$arParams['SHOW_BASKET_HEADERS'] = $arParams['SHOW_BASKET_HEADERS'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERY_FADE_EXTRA_SERVICES'] = $arParams['DELIVERY_FADE_EXTRA_SERVICES'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_COUPONS_BASKET'] = $arParams['SHOW_COUPONS_BASKET'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_COUPONS_DELIVERY'] = $arParams['SHOW_COUPONS_DELIVERY'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_COUPONS_PAY_SYSTEM'] = $arParams['SHOW_COUPONS_PAY_SYSTEM'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_NEAREST_PICKUP'] = $arParams['SHOW_NEAREST_PICKUP'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERIES_PER_PAGE'] = isset($arParams['DELIVERIES_PER_PAGE']) ? intval($arParams['DELIVERIES_PER_PAGE']) : 9;
$arParams['PAY_SYSTEMS_PER_PAGE'] = isset($arParams['PAY_SYSTEMS_PER_PAGE']) ? intval($arParams['PAY_SYSTEMS_PER_PAGE']) : 9;
$arParams['PICKUPS_PER_PAGE'] = isset($arParams['PICKUPS_PER_PAGE']) ? intval($arParams['PICKUPS_PER_PAGE']) : 5;
$arParams['SHOW_PICKUP_MAP'] = $arParams['SHOW_PICKUP_MAP'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_MAP_IN_PROPS'] = $arParams['SHOW_MAP_IN_PROPS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_YM_GOALS'] = $arParams['USE_YM_GOALS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

$useDefaultMessages = !isset($arParams['USE_CUSTOM_MAIN_MESSAGES']) || $arParams['USE_CUSTOM_MAIN_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_BLOCK_NAME']))
{
	$arParams['MESS_AUTH_BLOCK_NAME'] = Loc::getMessage('AUTH_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REG_BLOCK_NAME']))
{
	$arParams['MESS_REG_BLOCK_NAME'] = Loc::getMessage('REG_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BASKET_BLOCK_NAME']))
{
	$arParams['MESS_BASKET_BLOCK_NAME'] = Loc::getMessage('BASKET_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_BLOCK_NAME']))
{
	$arParams['MESS_REGION_BLOCK_NAME'] = Loc::getMessage('REGION_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAYMENT_BLOCK_NAME']))
{
	$arParams['MESS_PAYMENT_BLOCK_NAME'] = Loc::getMessage('PAYMENT_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_BLOCK_NAME']))
{
	$arParams['MESS_DELIVERY_BLOCK_NAME'] = Loc::getMessage('DELIVERY_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BUYER_BLOCK_NAME']))
{
	$arParams['MESS_BUYER_BLOCK_NAME'] = Loc::getMessage('BUYER_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BACK']))
{
	$arParams['MESS_BACK'] = Loc::getMessage('BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FURTHER']))
{
	$arParams['MESS_FURTHER'] = Loc::getMessage('FURTHER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_EDIT']))
{
	$arParams['MESS_EDIT'] = Loc::getMessage('EDIT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER']))
{
	$arParams['MESS_ORDER'] = $arParams['~MESS_ORDER'] = Loc::getMessage('ORDER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PRICE']))
{
	$arParams['MESS_PRICE'] = Loc::getMessage('PRICE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERIOD']))
{
	$arParams['MESS_PERIOD'] = Loc::getMessage('PERIOD_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_BACK']))
{
	$arParams['MESS_NAV_BACK'] = Loc::getMessage('NAV_BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_FORWARD']))
{
	$arParams['MESS_NAV_FORWARD'] = Loc::getMessage('NAV_FORWARD_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ADDITIONAL_MESSAGES']) || $arParams['USE_CUSTOM_ADDITIONAL_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRICE_FREE']))
{
	$arParams['MESS_PRICE_FREE'] = Loc::getMessage('PRICE_FREE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ECONOMY']))
{
	$arParams['MESS_ECONOMY'] = Loc::getMessage('ECONOMY_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGISTRATION_REFERENCE']))
{
	$arParams['MESS_REGISTRATION_REFERENCE'] = Loc::getMessage('REGISTRATION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_1']))
{
	$arParams['MESS_AUTH_REFERENCE_1'] = Loc::getMessage('AUTH_REFERENCE_1_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_2']))
{
	$arParams['MESS_AUTH_REFERENCE_2'] = Loc::getMessage('AUTH_REFERENCE_2_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_3']))
{
	$arParams['MESS_AUTH_REFERENCE_3'] = Loc::getMessage('AUTH_REFERENCE_3_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ADDITIONAL_PROPS']))
{
	$arParams['MESS_ADDITIONAL_PROPS'] = Loc::getMessage('ADDITIONAL_PROPS_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_USE_COUPON']))
{
	$arParams['MESS_USE_COUPON'] = Loc::getMessage('USE_COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_COUPON']))
{
	$arParams['MESS_COUPON'] = Loc::getMessage('COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERSON_TYPE']))
{
	$arParams['MESS_PERSON_TYPE'] = Loc::getMessage('PERSON_TYPE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PROFILE']))
{
	$arParams['MESS_SELECT_PROFILE'] = Loc::getMessage('SELECT_PROFILE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_REFERENCE']))
{
	$arParams['MESS_REGION_REFERENCE'] = Loc::getMessage('REGION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PICKUP_LIST']))
{
	$arParams['MESS_PICKUP_LIST'] = Loc::getMessage('PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NEAREST_PICKUP_LIST']))
{
	$arParams['MESS_NEAREST_PICKUP_LIST'] = Loc::getMessage('NEAREST_PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PICKUP']))
{
	$arParams['MESS_SELECT_PICKUP'] = Loc::getMessage('SELECT_PICKUP_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_INNER_PS_BALANCE']))
{
	$arParams['MESS_INNER_PS_BALANCE'] = Loc::getMessage('INNER_PS_BALANCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER_DESC']))
{
	$arParams['MESS_ORDER_DESC'] = Loc::getMessage('ORDER_DESC_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ERROR_MESSAGES']) || $arParams['USE_CUSTOM_ERROR_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRELOAD_ORDER_TITLE']))
{
	$arParams['MESS_PRELOAD_ORDER_TITLE'] = Loc::getMessage('PRELOAD_ORDER_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SUCCESS_PRELOAD_TEXT']))
{
	$arParams['MESS_SUCCESS_PRELOAD_TEXT'] = Loc::getMessage('SUCCESS_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FAIL_PRELOAD_TEXT']))
{
	$arParams['MESS_FAIL_PRELOAD_TEXT'] = Loc::getMessage('FAIL_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TITLE']))
{
	$arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] = Loc::getMessage('DELIVERY_CALC_ERROR_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TEXT']))
{
	$arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] = Loc::getMessage('DELIVERY_CALC_ERROR_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR']))
{
	$arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] = Loc::getMessage('PAY_SYSTEM_PAYABLE_ERROR_DEFAULT');
}

$scheme = $request->isHttps() ? 'https' : 'http';

switch (LANGUAGE_ID)
{
	case 'ru':
		$locale = 'ru-RU'; break;
	case 'ua':
		$locale = 'ru-UA'; break;
	case 'tk':
		$locale = 'tr-TR'; break;
	default:
		$locale = 'en-US'; break;
}

$APPLICATION->SetAdditionalCSS($templateFolder.'/style.css', true);
$this->addExternalJs($templateFolder.'/order_ajax.js');
\Bitrix\Sale\PropertyValueCollection::initJs();
$this->addExternalJs($templateFolder.'/script.js');
$this->addExternalJs(SITE_DEFAULT_PATH.'/js/select-box.js');
$this->addExternalJs($templateFolder.'/scripts4actions.js');
if (!empty($arResult['ORDER_DATA']['QUANTITY_LIST']))
    foreach($arResult['ORDER_DATA']['QUANTITY_LIST'] AS $qty)
        $arResult['ORDER_DATA']['QUANTITY'] += $qty;

$regionId   = $request->getCookie("GEOLOCATION_ID");
$cart_logo  = (file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/images/likeCart.png') ?  SITE_TEMPLATE_PATH.'/images/likeCart.png' : $templateFolder.'/images/likeCart.png');
?>
	<NOSCRIPT>
		<div style="color:red"><?=Loc::getMessage('SOA_NO_JS')?></div>
	</NOSCRIPT>
<div class="tabs__content js-tabs-content active" id="express">
	<div class="express">
		<div>
			<p>
				Мы ценим ваше время!
			</p>
			<p>
				Введите только номер телефона. Адрес, удобную дату доставки и способ оплаты вы
				сможете
				согласовать с менеджером по телефону.
			</p>
		</div>
		<form action="<?=POST_FORM_ACTION_URI?>" method="POST" name="ORDER_FORM" enctype="multipart/form-data" id="oneclickOrderForm" onsubmit="return false;">
			<?= bitrix_sessid_post(); ?>
			<input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="saveOrderAjax" />
			<input type="hidden" name="location_type" value="id" />
			<input type="hidden" name="BUYER_STORE" id="BUYER_STORE_ONCLICK" value="<?=$arResult['BUYER_STORE']?>" />
			<input type="hidden" name="PERSON_TYPE" value="1" />
			<input type="hidden" name="PERSON_TYPE_OLD" value="1" />
			<input type="hidden" name="ORDER_PROP_1" value="<?=($GLOBALS['USER']->isAuthorized() ? $USER->GetFullName() : 'Купить&nbsp;в&nbsp;один&nbsp;клик')?>" />
			<input type="hidden" name="profile_change" value="N" />
	        <input type="hidden" name="ORDER_PROP_6" value="<?=($regionId ? $regionId : DEFAULT_GEOLOCATION_ID)?>" />
			<input type="hidden" name="RECENT_DELIVERY_VALUE" value="<?=($regionId ? $regionId : DEFAULT_GEOLOCATION_ID)?>" />
			<input type="hidden" name="ORDER_PROP_5" value="Москва" />
			<input type="hidden" name="ORDER_PROP_4" value="101000" >
			<input type="hidden" name="ZIP_PROPERTY_CHANGED" value="Y" />
			<input type="hidden" name="DELIVERY_ID" value="3" />
			<input type="hidden" name="ORDER_DESCRIPTION" value="Экспресс оформление" />
			<input type="hidden" name="PAY_SYSTEM_ID" value="1" />
	        <input type="hidden" name="ORDER_PROP_2" value="<?=($GLOBALS['USER']->isAuthorized() ? $USER->GetEmail() : CClass::getOneClickEmail())?>" />
			<input type="hidden" name="ORDER_PROP_3" value="" />
			<input type="hidden" name="ALLOW_APPEND_ORDER" value="Y" />
			<input type="hidden" name="save" value="Y" />
			<div class="columns">
				<div class="column">
					<span class="sevenNum">+7</span>
				</div>
				<div class="column shortNum">
					<div>
						<input class="phone-input" name="phone-input" type="text" size="3" value="<?=$arResult['USER_PHONE'][0];?>"
								onkeyup="this.value=this.value.replace(/[^\d]|^7|^8/,'')" maxlength="3" >
					</div>
				</div>
				<div class="column longNum">
					<div><input class="phone-input" name="phone-input" type="text" size="7" value="<?=$arResult['USER_PHONE'][1];?>"
								onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="7"></div>
				</div>
				<div class="column submitExpressForm">
					<div><input type="submit"></div>
				</div>
			</div>
		</form>
		<p class="weCallYou"><?=GetMessage('CT_MANAGER_CALLBACK_TEXT')?></p>
	</div>
</div>
<div class="tabs__content js-tabs-content" id="himself">
	<div class="himself">
		<form action="<?=POST_FORM_ACTION_URI?>" method="POST" name="ORDER_FORM" id="full-order-form">
			<?//= bitrix_sessid_post(); ?>
			<input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="saveOrderAjax" />
			<input type="hidden" name="location_type" value="id" />
			<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>" />
			<input type="hidden" name="PERSON_TYPE" value="1" />
			<input type="hidden" name="PERSON_TYPE_OLD" value="1" />
			<input type="hidden" name="profile_change" value="N" />
			<input type="hidden" name="ORDER_PROP_6" value="<?=($regionId ? $regionId : DEFAULT_GEOLOCATION_ID)?>" />
			<input type="hidden" name="RECENT_DELIVERY_VALUE" value="<?=($regionId ? $regionId : DEFAULT_GEOLOCATION_ID)?>" />
			<input type="hidden" name="ORDER_PROP_5" value="" />
			<input type="hidden" name="ORDER_PROP_4" value="" />
			<input type="hidden" name="ZIP_PROPERTY_CHANGED" value="Y" />
			<input type="hidden" name="save" value="Y" />
	        <input type="hidden" name="DELIVERY_ID" value="" />
			<input type="hidden" name="PARENT_DELIVERY_ID" value="" />
	        <input type="hidden" name="PAY_SYSTEM_ID" value="" />
	<!-- 		<?if($USER->GetID() == 2422){?>
			<div class="columns">
	            <div class="delivery himselfBlock popupCommon column is-10">
	                <div class="titleBlockHimself">
	                    <p>Воспользуйся промокодом и получи скидку</p>
	                </div>
	                <div class="columns">
		                <div class="column">
		                	<div class="inputPromoCode">
		                		<label>Ваш промокод</label>
		                		<input id="promoCode" type="text" placeholder ="Промокод">
		                	</div>
		                </div>
		                <div class="column">
							<div class="himselfButtonBlock">
		                        <button type="button" class="btn is-primary">
		                            <span class="label-desktop" onclick="set_coupon()">Применить промокод</span>
		                        </button>
		                    </div>
		                </div>
		            </div>
	            </div>
	        </div>
				
			<?}?> -->
			<div class="columns">
	            <div class="delivery himselfBlock column is-10">
	                <div class="titleBlockHimself">
	                    <p>Выберите способ получения заказа</p>
	                </div>
	                <div id="delivery-list">
	                    <?/*<div class="listDelivery columns">
	                    <?php
	                        $i = 0;
	                        foreach($arResult['DELIVERY'] AS $id => $arDelivery):
	                            if ($i && !($i % 3)):?>
	                            </div>
	                            <div class="listDelivery columns">
	                            <?endif;?>
	                            <div class="itemDelivery column" data-id="<?= $id ?>" data-is_mkad="<?=(int)$arDelivery['IS_MKAD']?>">
	                                <div class="title"><?=$arDelivery['NAME']?></div>
	                                <div class="price" data-price="<?=$arDelivery['PRICE']?>"><?=(!$arDelivery['PRICE'] ? CClass::mb_ucfirst(GetMessage('PRICE_FREE_DEFAULT')) : $arDelivery['PRICE_FORMATED'])?></div>
	                                <?php if (!empty($arDelivery['LOGOTIP'])): ?>
	                                    <img src="<?= $arDelivery['LOGOTIP']['SRC'] ?>" alt="<?= $arDelivery['NAME'] ?>" />
	                                <?php else: ?>
	                                    <img src="<?= $templateFolder ?>/images/delivery-moscow-icon.png" alt="" />
	                                <?php endif; ?>
	                            </div>
	                        <?php
	                            $i++;
	                            endforeach;
	                        ?>
	                        </div>*/?>
	                </div>
	                    <?/*if ($regionId == DEFAULT_GEOLOCATION_ID):*/?>
	                    <div id="delivery_form_MKAD" style="display: none;">
	                        <div class="delivery-for-MKAD-title">Доставка осуществляется в пределах МКАД</div>
	                        <div class="delivery-for-MKAD">
	                            <div class="filter__checkbox">
	                                <input id="delivery-for-MKAD" type="checkbox" autocomplete="off" data-price="<?=(int)$arParams['OUTSIDE_MKAD_PRICE']?>" />
	                                <label for="delivery-for-MKAD">Нужна доставка за МКАД</label>
	                            </div>
	                        </div>
	                        <div class="if-MKAD">
	                            <div class="sel sel--black-panther">
	                                <select name="ORDER_PROP_25" id="select-mkad-km">
	                                    <option value="0">не нужна</option>
	                                    <option value="15">15 км за МКАД</option>
	                                    <option value="20">20 км за МКАД</option>
	                                    <option value="25">25 км за МКАД</option>
	                                    <option value="30">30 км за МКАД</option>
	                                    <option value="35">35 км за МКАД</option>
	                                </select>
	                            </div>
	                            <div>Стоимость доставки: <?=(int)$arParams['OUTSIDE_MKAD_PRICE']?> ₽/км.</div>
	                        </div>
	                    </div>
	                    <?/*endif;*/?>
	                <div class="delivery-item-note" data-id="3">
	                    <?=GetMessage('SOA_SELF_DELIVERY_NOTE', ['#LINK#' => '/contacts/'])?>
	                </div>
	                <div class="delivery-item-note" data-id="29">
	                    <?=GetMessage('SOA_TC_DELIVERY_NOTE')?>
	                </div>
	            </div>
	        </div>
			
			<div class="columns">
	            <div class="youContact himselfBlock popupCommon column is-10" id="contact-data">
	                <div class="titleBlockHimself">
	                    <p>Контактные данные</p>
	                </div>
	                <div class="formYouContact">
	                    <div class="columns">
	                        <div class="column">
	                            <div class="inputTel">
	                                <label for="ORDER_PROP_3">Номер телефона <span class="red-star">*</span></label>
	                                <input id="ORDER_PROP_3" name="ORDER_PROP_3" class="mobMarginNull" type="tel" placeholder="7(" value="<?=$arResult['USER_VALS']['ORDER_PROP'][3] ?>" required="" />
	                            </div>
	                        </div>
	                        <div class="column">
	                            <div class="inputName">
	                                <label for="ORDER_PROP_1">Ваше имя <span class="red-star">*</span></label>
	                                <input id="ORDER_PROP_1" name="ORDER_PROP_1" type="text" placeholder="Имя" value="<?=$arResult['USER_VALS']['ORDER_PROP'][1] ?>" required="" />
	                            </div>
	                        </div>
	                        <div class="column">
	                            <div class="inputName">
	                                <label for="ORDER_PROP_2">Ваш email <span class="red-star">*</span></label>
	                                <input id="ORDER_PROP_2" name="ORDER_PROP_2" type="text" placeholder="e-mail" value="<?= $arResult['USER_VALS']['ORDER_PROP'][2] ?>" required="" />
	                            </div>
	                        </div>
	                    </div>
						<div class="input_adres" style="display:none;">
							<div class="columns">
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_26">Улица <span class="red-star">*</span></label>
										<input id="ORDER_PROP_26" name="ORDER_PROP_26" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][26] ?>" />
									</div>
								</div>
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_27">Дом <span class="red-star">*</span></label>
										<input id="ORDER_PROP_27" name="ORDER_PROP_27" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][27] ?>" />
									</div>
								</div>
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_28">Корпус</label>
										<input id="ORDER_PROP_28" name="ORDER_PROP_28" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][28] ?>" />
									</div>
								</div>
							</div>
							<div class="columns">
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_29">Строение</label>
										<input id="ORDER_PROP_29" name="ORDER_PROP_29" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][29] ?>" />
									</div>
								</div>
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_30">Владение</label>
										<input id="ORDER_PROP_30" name="ORDER_PROP_30" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][30] ?>" />
									</div>
								</div>
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_31">Квартира</label>
										<input id="ORDER_PROP_31" name="ORDER_PROP_31" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][31] ?>" />
									</div>
								</div>
							</div>
							<div class="columns">
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_32">Этаж</label>
										<input id="ORDER_PROP_32" name="ORDER_PROP_32" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][32] ?>" />
									</div>
								</div>
								<div class="column">
									<div class="inputName">
										<label for="ORDER_PROP_33">Подъезд</label>
										<input id="ORDER_PROP_33" name="ORDER_PROP_33" type="text" placeholder="" value="<?= $arResult['USER_VALS']['ORDER_PROP'][33] ?>" />
									</div>
								</div>
							</div>
						</div>
	                    <div class="columns">
	                        <div class="column">
	                            <div class="inputText">
	                                <label for="ORDER_DESCRIPTION">Комментарий к заказу</label>
	                                <textarea id="ORDER_DESCRIPTION" name="ORDER_DESCRIPTION" cols="30" rows="10" maxlength="255"></textarea>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <div class="columns">
				<div class="methodPay himselfBlock column is-10" >
	                <div class="titleBlockHimself">
	                    <p>Выберите способ оплаты</p>
	                </div>
	                <div id="pay-system-list">
	                    <div class="listDelivery columns">
	                    <?
	                    if (!empty($arResult['PAY_SYSTEM'])){
	                        $i = 0;
	                        foreach($arResult['PAY_SYSTEM'] AS $pay):
	                            if ($i && !($i % 4)):?>
	                                </div>
	                                <div class="listDelivery columns">
	                            <?endif;?>
	                        <div class="itemDelivery column" data-id="<?=$pay['ID']?>">
	                            <?php if(!empty($pay['PSA_LOGOTIP'])): ?>
	                                <img src="<?= $pay['PSA_LOGOTIP']['SRC'] ?>" alt="<?=$pay['NAME'] ?>" />
	                            <?php else: ?>
	                                <img src="<?= $templateFolder ?>/images/cash-icon.png" alt="" />
	                            <?php endif; ?>
	                            <div class="title"><?=$pay['NAME']?></div>
	                        </div>
	                    <?php
	                        $i++;
	                        endforeach;
	                    }?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="columns">
	            <div class="service himselfBlock column is-10" style="display:none;!important">
	                <div class="titleBlockHimself">
	                    <p>Сервис и услуги</p>
	                </div>
	                <div class="listDelivery columns">
	                    <div class="itemService column service-eleven" data-service="1">
	                        <div class="title">Нужен подъем на этаж</div>
	                        <div class="checkboxService">
	                            <div class="filter__checkbox">
	                                <input id="service-eleven" name="ORDER_PROP_22" type="checkbox" autocomplete="off" value="N" />
	                                <label for="service-eleven">Да</label>
	                            </div>
	                        </div>
	                        <img src="<?= $templateFolder ?>/images/level-icon.png" alt="" />
	                    </div>
	                    <div class="itemService column" data-service="2">
	                        <div class="title">Нужен монтаж</div>
	                        <div class="checkboxService">
	                            <div class="filter__checkbox">
	                                <input id="service-installation" name="ORDER_PROP_23" type="checkbox" autocomplete="off" value="N" />
	                                <label for="service-installation">Да</label>
	                            </div>
	                        </div>
	                        <img src="<?= $templateFolder ?>/images/installation-icon.png" alt="" />
	                    </div>
	                    <?/*<div class="itemService column" data-service="3">
	                        <div class="title">Нужна рассрочка</div>
	                        <div class="checkboxService">
	                            <div class="filter__checkbox">
	                                <input id="service-credit" name="ORDER_PROP_24" type="checkbox" autocomplete="off" value="N" />
	                                <label for="service-credit" id="credit-data">
	                                    <?
	                                    $sum = $arResult['ORDER_PRICE'] / 4;
	                                    $sum = number_format($sum, 0, '', ' ');
	                                    ?>
	                                    от <?=$sum;?> р./мес.
	                                </label>
	                            </div>
	                        </div>
	                        <img src="<?= $templateFolder ?>/images/credit-icon.png" alt="" />
	                    </div>*/?>
	                </div>
	            </div>
	        </div>
	        <div class="columns">
	            <div class="totalSubmit himselfBlock column is-10">
	                <div class="himselfSubmitBlock">
	                    <div class="himselfTotalBlock">
	                        <span>Итого:</span>
	                        <span id="orderFullPrice" data-price="<?= $arResult['ORDER_DATA']['ORDER_PRICE'] ?>">
	                            <?=customFormatPrice(CCurrencyLang::CurrencyFormat($arResult['ORDER_DATA']['ORDER_PRICE'], 'RUB')); ?>
	                        </span>
	                    </div>
	                    <div class="himselfButtonBlock">
	                        <button type="submit" class="btn is-primary">
	                            <span class="label-desktop" id="order-form-submit">Оформить заказ</span>
	                        </button>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <div class="asideCart column is-2" id="basket-total-aside-block">
	            <div>
	                <div>
	                    <div class="title">
	                        <img src="<?= $templateFolder ?>/images/asidecart-icon.png" alt="" /> Корзина
	                    </div>
	                </div>
	                <div>
	                    <div class="listPrice">
	                        <div>
	                            <span>Товаров:</span>
	                            <span>На сумму:</span>
	                            <span>Доставка:</span>
	                            <span>Подъем:</span>
	                            <span>Монтаж:</span>
	                            <?/*<span>Рассрочка:</span>*/?>
	                        </div>
	                        <div>
	                            <span class="aside-products-count"><i class="value"><?=$arResult['ORDER_DATA']['QUANTITY']?></i> шт.</span>
	                            <span class="aside-price">
	                                <?= customFormatPrice(CCurrencyLang::CurrencyFormat($arResult['ORDER_DATA']['ORDER_PRICE'], 'RUB')); ?>
	                            </span>
	                            <span class="aside-delivery">не определено</span>
	                            <span data-service="1">не требуется</span>
	                            <span data-service="2">не требуется</span>
	                            <?/*<span data-service="3">не требуется</span>*/?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div>
	                <div class="totalPrice">
	                    <span>Итого:</span>
	                    <span class="aside-price aside-price-full" data-price="<?= $arResult['ORDER_DATA']['ORDER_PRICE'] ?>" data-symbol="₽">
	                        <?= customFormatPrice(CCurrencyLang::CurrencyFormat($arResult['ORDER_DATA']['ORDER_PRICE'], 'RUB')); ?>
	                    </span>
	                    <img src="<?=$cart_logo?>" alt="" />
	                </div>
	                <div class="buyOneClick">
	                    <a href="#tabs__header-1" class="btn is-primary is-outlined">Купить в один клик</a>
	                </div>
	            </div>
	        </div>
		</form>
	</div>
</div>
<script id="deliveryTemplate" type="text/x-jsrender">
    <div class="itemDelivery column" data-id="{{>ID}}" data-is_mkad="{{>IS_MKAD}}" data-parent_id="{{>PARENT_ID}}">
        <div class="title">{{>NAME}}</div>
        <div class="price" data-price="{{>PRICE}}">{{>PRICE_FORMATED}} <i class="znakrub">c</i></div>
        {{if (LOGOTIP_SRC != '')}}
            <img src="{{>LOGOTIP_SRC}}" alt="{{>NAME}}" />
        {{else}}
            <img src="<?=$templateFolder ?>/images/delivery-moscow-icon.png" alt="{{>NAME}}" />
        {{/if}}
    </div>
</script>
<script type="text/javascript">
	set_coupon = function(){
		let coupon = $('#promoCode').val(); 
		$.ajax({
			url:location.protocol+'//'+location.host+'/ajax/set_coupon.php',
			method:'POST',
			data:{'coupon':coupon},
			success:function(res){
				SBB.updateCartProducts();
			}
		})
	}
</script>