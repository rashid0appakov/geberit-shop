<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;

global $APPLICATION, $USER, $arContact;

Loc::loadMessages(__FILE__);?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/template_header.php');?>
	<meta name="yandex-verification" content="c4d3dd5513e60ed4" />
</head>
<body data-section="<?$APPLICATION->ShowProperty('currentSectionId', '0');?>" data-element="<?$APPLICATION->ShowProperty('currentElementId', '0');?>">
	<script data-skip-moving="true">
		localStorage.setItem('svt.debug', JSON.stringify({
			'overrides': {
				'selector': '-'
			}
		}));
		let h = document.querySelector("html");
		let m = new MutationObserver(function(b) {
			b.forEach(function(a) {
				if ('g_init' === a.attributeName) {
					setTimeout(function() {
						localStorage.removeItem('svt.debug')
					}, 1000)
				}
			})
		});
		m.observe(h, {
			attributes: true,
			attributeOldValue: true,
			characterData: true,
			childList: true,
			subtree: true
		});
	</script>
	<?if ($GLOBALS['USER']->IsAuthorized()):?><?$APPLICATION->ShowPanel();?><?endif;?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/template_filters.php');?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/top_banner.php');?>
	<?if (IS_NEW_YEAR) {?>
	<!-- New Year !-->
	<link rel="stylesheet" type="text/css" href="/local/templates/.default/new_year.css">
	<div class="notify-wrapper notify-wrapper--top"><div id="campaign-christmas-landing" class="notification notify notify--banner notification--static notification--no-shadow notification--no-border notification--hide-disable mg-none-i notification--show">
			<div class="notification__content container">
				<a href="/new-year.php" сlass="nostyle medium">
					<p class="mg-none">С Новым годом и рождеством!  </p>
					<span class="button button--xs button--flat button--inverted mg-left-lv2">График работы в праздники</span>
				</a>
				<span class="santa"></span>
			</div>
		</div>
	</div>
	<!-- New Year !-->
	<?}?>
	<div id="top-header" class="top-header hero">
		<div class="container is-widescreen">
			<div class="columns is-mobile">
				<div class="region column is-3">
					<?$APPLICATION->IncludeComponent(
						"custom:geolocation.preview",
						"top",
						array(
							"DEFAULT_GEOLOCATION_ID" => DEFAULT_GEOLOCATION_ID,
						)
					);?>
				</div>
				<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("compare_header_1");?>
				<a href="/compare/" class="<?=count($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS']) ? "compare-added " : ''?>
					compare-item-button icon-diff-big button tooltip is-tooltip-bottom is-disabled mobile" data-tooltip="<?=GetMessage('HDR_COMPARE_GOODS')?>"
					data-added="<?=GetMessage('HDR_GO_TO_COMPARE')?>"
					data-add="<?=GetMessage('HDR_ADD_TO_COMPARE')?>"
				>
					<span class="icon-diff">
						<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
							<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
							<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
							<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
						</svg>
					</span>
					<span class="tag is-warning"><?=count($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS']);?></span>
				</a>
				<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("compare_header_1", "");?>

				<div class="phone column custom-live-ajax-update" id="custom-live-ajax-update-header-phone2">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts_header_1");?>
					<span class="call_phone_1"><a class="phone <?=$arContact['CLASS_ROISTAT']?>" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
					<span class="work"><?=$arContact['SCHEDULE']?></span>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts_header_1", "");?>
				</div>
				<?$APPLICATION->IncludeComponent(
					"custom:menu",
					"header_top",
					array(
						"TYPE" => "top",
					)
				);?>
				<div class="status column has-text-centered">
					<a href="#popupStatusZakazaForm" class="_orderStatusBtn modal-link">
						<img class="icon-help" src="<?=SITE_DEFAULT_PATH?>/images/icons/help.png" alt="<?=GetMessage('HDR_ORDER_STATUS')?>" />
						<span><?=GetMessage('HDR_ORDER_STATUS')?></span>
					</a>
				</div>
				<div class="search _popup-catalog-menu-start-mobile-button"
					id="popup-catalog-menu-start-mobile-button">
					<button></button>
				</div>

				<div class="account column has-text-right">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("auth_block_header");?>
					<?if ($USER->IsAuthorized()):?>
						<a href="?logout=yes" rel="nofollow"><?=GetMessage('HDR_EXIT')?></a>
					<?else:?>
						<a href="#popupLoginForm" rel="nofollow" class="logLink modal-link"><?=GetMessage('HDR_ENTER')?></a>
						<span>|</span>
						<a href="#popupRegForm" rel="nofollow" class="regLink modal-link"><?=GetMessage('HDR_REGISTRATION')?></a>
					<?endif;?>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("auth_block_header", "");?>
				</div>
			</div>
		</div>
	</div>
	<div class="middle-header hero">
		<div class="container is-widescreen">
			<div class="columns is-mobile">
				<div class="burger" id="popup-main-menu-start-mobile">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="0" y1="5" x2="20" y2="5" stroke="black" stroke-width="2" />
						<line x1="0" y1="10" x2="20" y2="10" stroke="black" stroke-width="2" />
						<line x1="0" y1="15" x2="20" y2="15" stroke="black" stroke-width="2" />
					</svg>
				</div>
				<div class="quality column is-3">
					<?/*<a href="/" class="hide-mobile">
						<div class="icon-good is-pulled-left">
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.include",
								"",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DEFAULT_PATH."/include/header_middle_logo_icon.php"
								),
								false
							);?>
						</div>
					</a>*/?>
					<div class="logo-cover">
						<? if (!CClass::Instance()->IsRoot()):?>
							<a href="<?=SITE_DIR?>" >
						<? endif;?>
						<?$APPLICATION->IncludeComponent("custom:include", "", array(
								"PATH" => "/include/header_middle_logo.php"
							),
							false,
							array(
							)
						);?>
						<? if (!CClass::Instance()->IsRoot()):?>
							</a>
						<? endif;?>
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("roistat_visit_mobile");?>
						<div class="roistat-promo roistat_mobile"></div>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("roistat_visit_mobile", ".");?>
					</div>
					<?/*<span class="tag is-danger">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DEFAULT_PATH."/include/header_middle_logo_year.php"
							),
							false,
							array(
								"HIDE_ICONS" => "Y",
								"ACTIVE_COMPONENT" => "N"
							)
						);?>
					</span>*/?>
				</div>
				<?/*$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DEFAULT_PATH."/include/header_callback.php"
					),
					false
				);*/?>
				<div class="phone column is-2 custom-live-ajax-update" id="custom-live-ajax-update-header-phone" style="margin-top: -35px;">
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_header_1");?>
						<span class="call_phone_1"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="call <?=$arContact['CLASS_ROISTAT']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_header_1", "");?>
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("roistat_visit");?>
						<div class="roistat-promo roistat_desktop"></div>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("roistat_visit", ".");?>
				</div>
				<div class="help column is-3 custom-live-ajax-update" id="custom-live-ajax-update-header-time">
					<span class="speaker">
						<img class="icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />
						<div class="dot"></div>
					</span>
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_header_1");?>
					<span class="info"><?=GetMessage('HDR_SCHEDULE_HELP_TEXT')?> <strong id="get-schedule-here"><?=$arContact['SCHEDULE']?></strong></span>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_header_1", "");?>
					<span>
						<a href="#" class="actionCallRequest">Заказать звонок</a>
						<a href="#" class="actionChatConsultant"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
					</span>
				</div>

				<div class="diff column is-narrow">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("compare_header_2");?>
					<a href="/compare/" class="<?=count($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS']) ? "compare-added " : ''?>
					   compare-item-button icon-diff-big button tooltip is-tooltip-bottom" data-tooltip="<?=GetMessage('HDR_COMPARE_GOODS')?>"
					   data-added="<?=GetMessage('HDR_GO_TO_COMPARE')?>"
						data-add="<?=GetMessage('HDR_ADD_TO_COMPARE')?>"
					>
						<span class="icon-diff">
							<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
								<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
								<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
								<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
							</svg>
						</span>
						<span class="tag is-warning is-disabled"><?=count($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS']);?></span>
					</a>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("compare_header_2", "");?>
				</div>
				<div class="order column is-narrow">
					<?
					/*$APPLICATION->IncludeComponent(
						"bxcert:empty",
						"order_button",
						array(
							"PATH_TO_BASKET" => "/personal/cart/",
							"PATH_TO_ORDER" => "/personal/order/",
						),
						false
					);*/
					?>
					<? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "top_cart",
						array(
							"PATH_TO_BASKET"	=> SITE_DIR."personal/cart/",
							"PATH_TO_PERSONAL"  => SITE_DIR."personal/",
							"SHOW_PERSONAL_LINK"=> "N",
							"SHOW_EMPTY_VALUES" => "N",
							"SHOW_PRODUCTS"	 => "N",
							"SHOW_NUM_PRODUCTS" => "Y",
							"SHOW_TOTAL_PRICE"  => "N"
						),
						false,
						array(
							"0" => ""
						)
					);?>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom-header" class="bottom-header hero">
		<div class="container is-widescreen">
			<div class="columns is-mobile">
				<div class="catalog-button column">
					<button class="btn is-primary button__open-menu">
						<span class="icon-burger"></span>
						<span class="label-button"><?=GetMessage('HDR_CATALOG_TITLE')?></span>
					</button>
				</div>
				<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_header_2");?>
				<span class="m_call_phone_1 donor_href" style="display:none !important;"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="mobile-phone-button search bottom-header__search _popup-catalog-menu-start-mobile-button--bottom-header <?=$arContact['CLASS_ROISTAT']?>"
					<?/*id="popup-catalog-menu-start-mobile-button--bottom-header2"*/?>
				>
					<button></button>
				</a></span>
				
				<a href="#" class="provider_href mobile-phone-button search bottom-header__search _popup-catalog-menu-start-mobile-button--bottom-header"
					<?/*id="popup-catalog-menu-start-mobile-button--bottom-header2"*/?>
				>
					<button></button>
				</a>
				<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_header_2", "");?>
				<div class="column is-4 search">
					<?$APPLICATION->IncludeComponent(
						"custom:search.header",
						"",
						array(),
						false
					);?>
				</div>

				<div class="pack column other_link_header_pack"></div>
				<div class="install column other_link_header_install"></div>
				<div class="phone column">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts_header_2");?>
					<span class="call_phone_1 phone"><a class="<?=$arContact['CLASS_ROISTAT']?>" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
					<span class="work"><?=$arContact['SCHEDULE']?></span>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts_header_2", "");?>
				</div>
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DEFAULT_PATH."/include/header_discount_dropdown.php"
					),
					false
				);?>
			</div>
		</div>
	</div>
	<?$APPLICATION->IncludeComponent(
		"custom:menu",
		"catalog_popup".(POPUP_MENU_TYPE == 'EXTENDED' ? '_extended' : ''),
		array(
			"TYPE" => "catalog"
		)
	);?>
