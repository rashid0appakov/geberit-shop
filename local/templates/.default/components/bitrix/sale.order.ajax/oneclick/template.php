<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$regionId= $request->getCookie("GEOLOCATION_ID");

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
\Bitrix\Sale\PropertyValueCollection::initJs();
$this->addExternalJs($templateFolder.'/script.js');
?>
	<NOSCRIPT>
		<div style="color:red"><?=Loc::getMessage('SOA_NO_JS')?></div>
	</NOSCRIPT>
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