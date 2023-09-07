<?php
	use Bitrix\Main\Config\Option;
	if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
    $dir = $APPLICATION->GetCurDir();
?>
	<div class="footer-top hero desktop">
		<div class="container is-widescreen">
			<div class="columns is-mobile">
				<div class="column custom-live-ajax-update" id="custom-live-ajax-update-footer-address2">
					<h4 class="is-size-5">Центральный офис</h4>

					<p class="address"><?=Option::get("tiptop", "template_address", "")?></p>
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_footer_1");?>
					<span class="call_phone_2"><a class="phone jdmsk" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span><br />
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_footer_1", "");?>
					<?if(!empty($arContact['ADD_PHONE']['NUMBER'])){?><span class="call_phone_5"><a class="phone" href="tel:<?=$arContact['ADD_PHONE']['NUMBER']?>"><?=$arContact['ADD_PHONE']['VALUE']?></a></span><?}?>

					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_footer_1");?>
					<p class="timework"><?=($arContact['SCHEDULE'] ? : '&nbsp;');?></p>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_footer_1", "");?>
					<?if(!empty($arSite["EMAIL"])){?><a class="email" href="mailto:<?=$arSite["EMAIL"]?>"><?=$arSite["EMAIL"]?></a><?}?>
				</div>
				<div class="column section-navigate">
					<?$APPLICATION->IncludeComponent(
						"custom:menu",
						"footer_company_desktop",
						array(
							"TYPE" => "footer_company",
						)
					);?>
				</div>
				<div class="column is-5 catalog-menu">
					<?$APPLICATION->IncludeComponent(
						"custom:menu",
						"footer_catalog",
						array(
							"TYPE" => "catalog",
						)
					);?>
				</div>
				<div class="column">
					<div class="other_link_footer"></div>
					<p><?=GetMessage('HDR_PAYMENT_METHODS')?>:</p>
					<div class="payments">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/include/footer/payment_methods.php"
							),
							false
						);?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-top hero tablet">
		<div class="container is-widescreen">
			<div class="columns is-mobile custom-live-ajax-update" id="custom-live-ajax-update-footer-address3">
				<div class="column first">
					<h4 class="is-size-5">Центральный офис</h4>
					<p class="address"><?=Option::get("tiptop", "template_address", "")?></p>
				</div>
				<div class="column phone-column">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts_footer_1");?>
					<span class="call_phone_2"><a class="phone jdmsk" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
					<p class="timework"><?=($arContact['SCHEDULE'] ? : '&nbsp;');?></p>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts_footer_1", "");?>
				</div>
				<div class="column phone-column">
					<?if(!empty($arContact['ADD_PHONE']['NUMBER'])){?><span class="call_phone_5"><a class="phone" href="tel:<?=$arContact['ADD_PHONE']['NUMBER']?>"><?=$arContact['ADD_PHONE']['VALUE']?></a></span><?}?>
					<?if(!empty($arSite["EMAIL"])){?><a class="email" href="mailto:<?=$arSite["EMAIL"]?>"><?=$arSite["EMAIL"]?></a><?}?>
				</div>
			</div>
			<hr>
			<div class="columns footer-menu is-mobile">
				<?$APPLICATION->IncludeComponent(
					"custom:menu",
					"footer_company_tablet",
					array(
						"TYPE" => "footer_company",
					)
				);?>
			</div>
			<hr>
			<div class="columns footer-payment is-mobile">
				<p><?=GetMessage('HDR_PAYMENT_METHODS')?>:</p>
				<div class="payments">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/include/footer/payment_methods.php"
						),
						false
					);?>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-middle hero">
		<div class="container is-widescreen">
			<div class="columns is-mobile">
				<div class="more column">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/include/footer/middle_text_more.php"
						),
						false
					);?>
				</div>
				<div class="column">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/include/footer/middle_text.php"
						),
						false
					);?>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom <?=($DIR != '/personal/cart/' ? 'hero' : '')?> our_shops">
	</div>
	<div class="footer-top hero mobile">
		<div class="container is-widescreen custom-live-ajax-update" id="custom-live-ajax-update-footer-address">
			<p class="address"><?=Option::get("tiptop", "template_address", "")?></p>
			<div class="columns is-mobile">
				<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_footer_2");?>
				<span class="call_phone_2"><a class="phone jdmsk" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
				<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_footer_2", "");?>
				<?if(!empty($arContact['ADD_PHONE']['NUMBER'])){?><span class="call_phone_5"><a class="phone" href="tel:<?=$arContact['ADD_PHONE']['NUMBER']?>"><?=$arContact['ADD_PHONE']['VALUE']?></a></span><?}?>
			</div>
			<?if(!empty($arSite["EMAIL"])){?><a class="email" href="mailto:<?=$arSite["EMAIL"]?>"><?=$arSite["EMAIL"]?></a><?}?>
			<hr>
			<div class="columns footer-payment is-mobile">
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => "/include/footer/payment_methods.php"
					),
					false
				);?>
			</div>
			<hr>
			<div class="columns bottom is-mobile">
				<div class="text">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/include/footer/top_mobile_text.php"
						),
						false
					);?>
				</div>
				<a class="top" id="to-top" href="#"></a>
			</div>
		</div>
	</div>
	<? if( $dir != '/personal/cart/' ) { ?>
		<!-- ПЛАВАЮЩАЯ ПОЛОСА -->
		<div class="toolbar-bottom">
			<div class="toolbar-bottom__container">
				<div class="container">
					<div class="toolbar-bottom__content">
						<?
						/*$APPLICATION->IncludeComponent("custom:component", "footer_viewed_products", Array(),
							false
						);*/
						$APPLICATION->IncludeComponent("bitrix:catalog.viewed.products", "footer", Array(
								"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
								"HIDE_NOT_AVAILABLE" => "N",	// Не отображать товары, которых нет на складах
								"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,	// Тип инфоблока
								"IBLOCK_ID" => CATALOG_IBLOCK_ID,	// Инфоблок
								"PRICE_CODE" => array("BASE"),
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "N",	// Включать НДС в цену
								"SHOW_OLD_PRICE" => "N",
								"SHOW_IMAGE" => "Y",	// Показывать изображение
								"SHOW_PRODUCTS_".CATALOG_IBLOCK_ID => "Y",	// Показывать товары каталога
								"PAGE_ELEMENT_COUNT" => 10,
							),
							false
						);
						?>
						<div class="toolbar-bottom__phone custom-live-ajax-update" id="custom-live-ajax-update-footer-phone">
                            <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_footer_3");?>
							<span class="call_phone_3"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="toolbar-bottom__phone-number jdmsk"><?=$arContact['PHONE']['VALUE']?></a></span>
                            <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_footer_3", "");?>
							<span class="toolbar-bottom__phone-link">
								<a href="#"><?=GetMessage('HDR_ORDER_CALLBACK')?></a>
								<a href="#"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
							</span>
						</div>
						<div class="toolbar-bottom__callback">
							<span class="toolbar-bottom__callback-toggle">
								<span><?=GetMessage('HDR_CONNECT_WITH')?></span>
								<div class="toolbar-bottom__callback-dropdown toolbar-bottom__callback-dropdown--close">
									<a class="actionCallRequest" href="#"><?=GetMessage('HDR_ORDER_CALLBACK')?></a>
									<a class="actionChatConsultant" href="#"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
                                    <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_footer_2");?>
									<p class="info toolbar-bottom__callback-dropdown-info"><?=GetMessage('HDR_SCHEDULE_HELP_TEXT')?> <strong><?=$arContact['SCHEDULE']?></strong></p>
                                    <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_footer_2", "");?>
								</div>
							</span>
						</div>
                        <?/* $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "toolbar_cart",
                            array(
                                "PATH_TO_BASKET"    => SITE_DIR."personal/cart/",
                                "PATH_TO_PERSONAL"  => SITE_DIR."personal/",
                                "SHOW_PERSONAL_LINK"=> "N",
                                "SHOW_PERSONAL_LINK"=> "N",
                                "SHOW_EMPTY_VALUES" => "N",
                                "SHOW_PRODUCTS"     => "N",
                                "SHOW_NUM_PRODUCTS" => "Y",
                                "SHOW_TOTAL_PRICE"  => "Y",
                                "SHOW_PRODUCTS"     => "Y"
                                ),
                            false,
                            array(
                                "0" => ""
                            )
                        );*/?>
						<?
						/*$APPLICATION->IncludeComponent(
							"bxcert:empty",
							"footer_order_info",
							array(),
							false
						);*/
						?>
						<? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "bottom_cart",
                            array(
                                "PATH_TO_BASKET"    => SITE_DIR."personal/cart/",
                                "PATH_TO_PERSONAL"  => SITE_DIR."personal/",
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
					</div>
				</div>
			</div>
		</div>
	<? } ?>
	<!-- POPUPS -->
	<?$APPLICATION->IncludeComponent(
		"custom:geolocation.select",
		"popup",
		array(
			"STORE_CITY" => array(
				"129", //Москва
				"817", //Санкт-петербург
				"2201", // Екб
			),
			"DELIVERY_CITY" => array(
				"129", //Москва
				"817", //Санкт-петербург
				"2201", // Екб
			),
			"DEFAULT_CITY" => array(
				"129", //Москва
				"817", //Санкт-петербург
				"2201", // Екб
				"2473", //Красноярск
				"1816", //Самара
				"679", //Воронеж
				"1537", //Казань
				"1680", //Нижний Новгород
				"2356", //Челябинск
				"2201", //Екатеринбург
				"1095", //Краснодар
				"1855", //Пермь
				"880", //Череповец
			),
		),
		false
	);?>

	<!-- MAIN MENU -->
	<div class="popup-menu" id="popup-main-main-menu">
		<div class="middle-header hero">
			<div class="container is-widescreen">
				<div class="columns is-mobile">
					<div class="burger close" id="popup-main-menu-mobile-back">
						<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
							<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2" />
							<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2" />
						</svg>
					</div>
					<div class="phone column is-2 custom-live-ajax-update" id="custom-live-ajax-update-footer-phone2">
							<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_footer_4");?>
							<span class="call_phone_1"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="call jdmsk"><?=$arContact['PHONE']['VALUE']?></a></span>
							<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_footer_4", "");?>
						<a href="#" class="actionCallRequest"><?=GetMessage('HDR_CALL_BACK_27')?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="top-header hero">
			<div class="container is-widescreen">
				<div class="columns is-mobile">
					<div class="status column has-text-centered">
						<a href="#popupStatusZakazaForm" class="_orderStatusBtn modal-link">
							<img class="icon-help" src="<?=SITE_DEFAULT_PATH?>/images/icons/help.png" />
							<span><?=GetMessage('HDR_ORDER_STATUS')?></span>
						</a>
					</div>
					<div class="account column has-text-right">
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("auth_block_footer");?>
						<?if ($USER->IsAuthorized()):?>
							<a href="?logout=yes" rel="nofollow"><?=GetMessage('HDR_EXIT')?></a>
						<?else:?>
							<a href="#popupLoginForm" rel="nofollow" class="logLink modal-link"><?=GetMessage('HDR_ENTER')?></a>
							<span>|</span>
							<a href="#popupRegForm" rel="nofollow" class="regLink modal-link"><?=GetMessage('HDR_REGISTRATION')?></a>
						<?endif;?>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("auth_block_footer", "");?>
					</div>
				</div>
			</div>
		</div>
		<?/*<div class="top-header__order">
			<a href="/compare/" class="icon-diff-big button tooltip is-tooltip-bottom is-disabled" data-tooltip="Сравнить товары">
				<span class="icon-diff">
					<svg viewBox="0 0 14 18" width="13" height="18" xmlns="http://www.w3.org/2000/svg">
						<rect class="column" fill="#010101" stroke="none" x="0" y="10" rx="1" ry="1" width="3" height="8" />
						<rect class="column" fill="#010101" stroke="none" x="5" y="0" rx="1" ry="1" width="3" height="18" />
						<rect class="column" fill="#010101" stroke="none" x="10" y="4" rx="1" ry="1" width="3" height="14" />
					</svg>
				</span>
				<span class="tag is-warning"><?=count( $_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS'] );?></span>
			</a>
			<div class="order is-narrow">
				<?$APPLICATION->IncludeComponent(
					"bitrix:sale.basket.basket.small",
					"header_mobile",
					array(
						"PATH_TO_BASKET" => "/personal/cart/",
						"PATH_TO_ORDER" => "/personal/order/",
						"SHOW_DELAY" => "N",
						"SHOW_NOTAVAIL" => "N",
						"SHOW_SUBSCRIBE" => "N",
					),
					false
				);?>
			</div>
		</div>*/?>
		<div class="main-menu-mobile hero">
			<div class="main-section button__open-menu">
				<a id="mobile_catalog_btn" class="is-active" href="/catalog/"><?=GetMessage('HDR_CATALOG_TITLE')?></a>
				<?/*<a href="https://swet-online.ru/">Освещение</a>
				<a href="https://gazkomfort.ru/">Отопление</a>
				<a class="red" href="/promotions/">Все акции</a>*/?>
			</div>
			<div class="second-section">
				<?$APPLICATION->IncludeComponent(
					"custom:menu",
					"mobule_menu",
					array(
						"TYPE" => "top",
					)
				);?>
			</div>
		</div>
		<div class="middle-header hero speaker-block">
			<div class="container is-widescreen">
				<div class="columns is-mobile">
					<div class="help column is-3 custom-live-ajax-update" id="custom-live-ajax-update-footer-phone3">
						<span class="speaker">
							<img class="icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />
							<div class="dot"></div>
						</span>
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_footer_3");?>
						<span class="info"><?=GetMessage('HDR_SCHEDULE_HELP_TEXT')?><strong> <?=$arContact['SCHEDULE']?></strong></span>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_footer_3", "");?>
						<span>
							<a href="#"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
							<a href="#"><?=GetMessage('HDR_FEEDBAK_LINK')?></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-top hero mobile">
			<div class="container is-widescreen custom-live-ajax-update" id="custom-live-ajax-update-footer-phone4">
				<p class="address"><?=Option::get("tiptop", "template_address", "")?></p>
				<div class="columns is-mobile">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_footer_5");?>
					<span class="call_phone_2"><a class="phone jdmsk" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_footer_5", "");?>
					<?if(!empty($arContact['ADD_PHONE']['NUMBER'])){?><span class="call_phone_5"><a class="phone" href="tel:<?=$arContact['ADD_PHONE']['NUMBER']?>"><?=$arContact['ADD_PHONE']['VALUE']?></a></span><?}?>
				</div>
				<?if(!empty($arSite["EMAIL"])){?><a class="email" href="mailto:<?=$arSite["EMAIL"]?>"><?=$arSite["EMAIL"]?></a><?}?>
				<hr />
				<div class="columns footer-payment is-mobile">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/include/footer/payment_methods.php"
						),
						false
					);?>
				</div>
				<hr>
				<div class="columns bottom is-mobile">
					<p class="text">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/include/footer/top_mobile_text.php"
							),
							false
						);?>
					</p>
					<?/*<a class="top" id="to-top" href="#"></a>*/?>
				</div>
			</div>
		</div>
	</div>

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => SITE_DEFAULT_PATH."/include/footer_help_onfly.php"
		),
		false
	);?>


	<?if (!$USER->IsAuthorized()):?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:system.auth.form",
			"popup",
			array(
				"REGISTER_URL" => "",
				"FORGOT_PASSWORD_URL" => "",
				"PROFILE_URL" => "/personal/",
				"SHOW_ERRORS" => "Y",
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.register",
			"popup",
			Array(
				"USER_PROPERTY_NAME" => "",
				"SHOW_FIELDS" => array(),
				"REQUIRED_FIELDS" => array(),
				"AUTH" => "Y",
				"USE_BACKURL" => "Y",
				"SUCCESS_PAGE" => "/index.php",
				"SET_TITLE" => "N",
				"USER_PROPERTY" => array()
			)
		);?>
	<?endif;?>
	<!-- ADD2BASKET_POPUP -->
	<?$APPLICATION->IncludeComponent(
		"bxcert:empty",
		"basket_popup",
		array(
			"AUTO_OPEN" => "Y",
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
	<?$APPLICATION->IncludeComponent(
		"bxcert:empty",
		"reportapperance_popup",
		array(),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
	<? $APPLICATION->IncludeComponent(
			"custom:menu",
			"catalog_popup_mobile",
			array(
				"TYPE" => "catalog",
			)
		);?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/template_popups.php');?>

	<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => "/include/footer/scripts.php"
		),
		false,
		array(
		)
	);?>
	<?$APPLICATION->IncludeComponent("custom:include", "", array(
			"PATH" => "include/template_counters.php"
		),
		false,
		array(
		)
	);?>
</body>
</html>