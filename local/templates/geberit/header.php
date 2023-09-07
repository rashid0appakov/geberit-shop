<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if(!CSite::InDir('/about/')&&!CSite::InDir('/optovye-prodazhi/')&&!CSite::InDir('/service/')&&!CSite::InDir('/payments/')) {
	
	CClass::setGeoSubDomain();
	/*if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'){
		global $USER;
		if ($USER->IsAdmin()){
			
			//Редирект на региональные поддомены

			CClass::setGeoSubDomain();
		}else{
			?>
			<div>Техническое обслуживание сайта</div>
			<?die();
		}
	}else{
		global $USER;
		if ($USER->IsAdmin()){
			//Редирект на региональные поддомены
			CClass::setGeoSubDomain();
		}
	}*/
}
if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru' || $_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru' || $_SERVER["SERVER_NAME"]=='novosibirsk.geberit-shop.ru' || $_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru'){
	if(CSite::InDir('/about/')||CSite::InDir('/optovye-prodazhi/')||CSite::InDir('/service/')||CSite::InDir('/payments/')) {
		global $APPLICATION;
		$dir = $APPLICATION->GetCurDir();
		$dir = 'https://geberit-shop.ru'.$dir;
		header("Location: ".$dir, true, 301); 
		exit(); 
		//LocalRedirect('https://geberit-shop.ru'.$dir,"301 Moved permanently");
	}
}
/*if($_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru'){
	if(CSite::InDir('/about/')||CSite::InDir('/optovye-prodazhi/')||CSite::InDir('/service/')||CSite::InDir('/payments/')) {
		global $APPLICATION;
		$dir = $APPLICATION->GetCurDir();
		$dir = 'https://geberit-shop.ru'.$dir;
		header("Location: ".$dir, true, 301); 
		exit(); 
		//LocalRedirect('https://geberit-shop.ru'.$dir,"301 Moved permanently");
	}
}*/
//if($GLOBALS['USER']->IsAdmin())
{
    $dir = strtolower($APPLICATION->GetCurDir());
    if($dir!=$APPLICATION->GetCurDir()){
        LocalRedirect($_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].$dir.($_SERVER["QUERY_STRING"]?'?'.$_SERVER["QUERY_STRING"]:''), false, "301 Moved permanently");
    }
}
//Вынести все редиректы в отдельный файл
$page = $APPLICATION->GetCurPageParam("", array());
if(strpos($page, '/catalog/?q=')!==false){
	LocalRedirect(str_replace('/catalog/?q=', '/search/?q=', $page), false, "301 Moved permanently");
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;

global $APPLICATION, $USER, $arContact;
use \Bitrix\Conversion\Internals\MobileDetect;

$MobileDetect = new MobileDetect;

Loc::loadMessages(__FILE__);?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#">
    <link rel="preconnect" href="https://mc.yandex.ru">
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="preconnect" href="https://www.google-analytics.com">
	<title><?$APPLICATION->ShowTitle()?></title>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/template_header.php');?>
	<?if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru' || $_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru'  || $_SERVER["SERVER_NAME"]=='novosibirsk.geberit-shop.ru' || $_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru'){?>
	<meta name="googlebot" content="noindex">
	<meta name="yandex-verification" content="ad008265e2675e99" />
	<?}?>
	<meta name='yandex-verification' content='3d2a388ee63df78f' />
	<meta name="google-site-verification" content="google-site-verification=tQF5RC77SzTMuvTizHbyCBZTBUpbsF_ceV1pEnNAISI" />
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MHDZZNX');</script>
<!-- End Google Tag Manager -->
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(23796220, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/23796220" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<script type="text/javascript">
dashamail = window.dashamail || function() { dashamail.queue.push(arguments); };
dashamail.queue = dashamail.queue || [];
dashamail('create');
</script>
<script src="https://directcrm.dashamail.com/scripts/v2/tracker.js" async></script>
	<? if($MobileDetect->isMobile()){ ?>
		<style>
			.product .product__inner{position:relative!important}
			.product .info .list_prop{display:block!important}
		</style>
	<? } ?>
	<style>.card-cell--row:last-child .product:first-child:last-child:hover .product__inner{position:relative!important}</style>
	<style>.catalog-menu-popup a.btn.categories__list-item{line-height:30px!important}.catalog-menu-popup .has-child.btn:before{top:5px!important}.catalog-menu-popup .subcategories a{margin-top:15px!important}</style>
</head>
<body data-section="<?$APPLICATION->ShowProperty('currentSectionId', '0');?>" data-element="<?$APPLICATION->ShowProperty('currentElementId', '0');?>">
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MHDZZNX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
	<?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => SITE_DIR."include/top_banner2.php",
        ),
        false
    );?>
    <?$APPLICATION->IncludeFile(SITE_DIR."include/header_message.php",[],["MODE"=>"php"]);?>
	<?
$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
if ($geo_id == 817 || $geo_id == 129 || $geo_id ==0){ // питер
    $show_baner = true;
}else{
    $show_baner = false;
}
    if (IS_SALE && $show_baner) {?>
	<noindex>
					<div class="topHeaderBnr">
            <div class="topHeaderBnr__inner">
                <div class="left">
                    <img src="/local/templates/.default/images/img_headDelivery.jpg" alt="" class="headDelivery">
                    <h3>Мы заботимся о вашем здоровье!</h3>
                     <?
                    $kad='МКАД';
                    if($geo_id == 817){
                    	$kad='КАД';
                    }
                    ?>
                    <p>Бесплатная доставка в пределах <?=$kad?> теперь   <span>от 5000 рублей</span>   на весь период карантина!</p>
                    <span class="details">Подробнее...</span>
                    <img src="/local/templates/.default/images/img_stayAtHome.png" alt="" class="stayAtHome_img">
                </div>
                <div class="right">
                    <div class="sun">
                        Все водители носят защитные маски, перчатки и обрабатывают поверхность лица и рук дезинфицирующими средствами перед контактом с клиентом
                    </div>
                    <div class="termom">
                        Сотрудники службы доставки проходят ежедневный контроль измерения температуры и состояния здоровья
                    </div>
                </div>
                <a href="javascript:void(0);" class="mobileBnrToggle"><img src="/local/templates/.default/images/ico_arrowVertical.png" alt=""></a>
            </div>
        </div>
	</noindex>
	<?}?>
<?php if(!defined("T731")):?>
	<div id="top-header" class="top-header hero">
		<div class="container is-widescreen">
				<div class="top_header-new">
					<div class="logo" >
						<img src="/local/templates/.default/images/top_header-new-logo.png" alt="" id="semsLogo">
						<div id="semsLogoTxt">Cеть экспертных <br>магазинов сантехники</div>
					</div>
					
					<?$APPLICATION->IncludeComponent(
						"custom:menu",
						"header_top_new",
						array(
							"TYPE" => "top",
						)
					);?>
					<div class="region">
						<? 
						/*$DEFAULT_GEOLOCATION_ID = DEFAULT_GEOLOCATION_ID;
						global $USER;
						if ($USER->IsAdmin()){
						*/
						$DEFAULT_GEOLOCATION_ID = DEFAULT_GEOLOCATION_ID;
							$isBot = !!preg_match("~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", $_SERVER['HTTP_USER_AGENT']);
							if( strpos($_SERVER["SERVER_NAME"], 'spb.')!==false ){
				                $HTTP_IS_SUB_HEADER = 'spb';
				            }
				            if( strpos($_SERVER["SERVER_NAME"], 'ekb.')!==false ){
				                $HTTP_IS_SUB_HEADER = 'ekb';
				            }
				            if( strpos($_SERVER["SERVER_NAME"], 'novosibirsk.')!==false ){
				                $HTTP_IS_SUB_HEADER = 'novosibirsk';
				            }
				            if( strpos($_SERVER["SERVER_NAME"], 'krasnodar.')!==false ){
				                $HTTP_IS_SUB_HEADER = 'krasnodar';
				            }
				           /* if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'){
				                $HTTP_IS_SUB_HEADER = 'spb';
				            }
				            if($_SERVER["SERVER_NAME"]=='ekb.geberit-shop.ru'){
				                $HTTP_IS_SUB_HEADER = 'ekb';
				            }
				            if($_SERVER["SERVER_NAME"]=='novosibirsk.geberit-shop.ru'){
				                $HTTP_IS_SUB_HEADER = 'novosibirsk';
				            }*/
				            
							if($isBot){
								$DEFAULT_GEOLOCATION_ID = DEFAULT_GEOLOCATION_ID;
							}else{
								if(!empty( $HTTP_IS_SUB_HEADER)){
									if( $HTTP_IS_SUB_HEADER=='spb'){
										$DEFAULT_GEOLOCATION_ID = 817;
									}
									if( $HTTP_IS_SUB_HEADER=='ekb'){
										$DEFAULT_GEOLOCATION_ID = 2201;
									}
									if( $HTTP_IS_SUB_HEADER=='novosibirsk'){
										$DEFAULT_GEOLOCATION_ID = 2622;
									}
									if( $HTTP_IS_SUB_HEADER=='krasnodar'){
										$DEFAULT_GEOLOCATION_ID = 1095;
									}
								}
							}
							/* $request = Application::getInstance()->getContext()->getRequest();
            				$geolocationId = $request->getCookie("GEOLOCATION_ID");*/
						//}?>
						<?$APPLICATION->IncludeComponent(
							"custom:geolocation.preview",
							"top_new",
							array(
								"DEFAULT_GEOLOCATION_ID" => $DEFAULT_GEOLOCATION_ID,
							)
						);?>
					</div>
					<div class="account">
						<img src="/local/templates/.default/images/ico_top_header-new--account.png" alt="">
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

					<div id="dropDown-menu-load"></div>
					<script>
						$( document ).ready(function() {
							$('#has_subMenu-link').click(function(){
								if ($("#dropDown-menu").length){
								  console.log('no this load!');
								}else{
									 console.log('no this load!');
									$.ajax({
									  type: "POST",
									  url: '<?=SITE_DEFAULT_PATH?>/include/drop_down_menu.php',
									  data: {}
									}).done(function( msg ) {
										$('#dropDown-menu-load').replaceWith(msg);
										$('.dropDown-menu').slideToggle();
									});
								}
								
							});
								$('body').on('click', '.dropDown-menu-inner .close', function() {
									$('.dropDown-menu').slideUp();
									$('.top_header-new .menu ul li.has_subMenu').removeClass('active');
								});
								$('body').on('click', '.top_header-new .menu ul li.has_subMenu, #semsLogo', function() {
									$(this).toggleClass('active');
									$('.dropDown-menu').slideToggle();
								});
								$(document).on('click', function(e) {
									$('body').on('click', '#dropDown-menu', function(e) {
							            e.stopPropagation();
							        });
							        if (e.target.id === 'dropDown-menu') {
							        } else if(e.target.id === 'has_subMenu-link'){
							        } else if(e.target.id === 'semsLogo'){
							        } else {
							           $('.dropDown-menu').slideUp();
							           $('.top_header-new .menu ul li.has_subMenu').removeClass('active');
							        }
					    	})
							});
						</script>
		</div>
<?php else:?>
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
					<span class="call_phone_1"><a class="phone mskgeberit" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
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
						<style type="text/css">
							.icon-help{
								width: 18px;
			  	        		height: 18px;
								background-image: url(<?=SITE_DEFAULT_PATH?>/images/icons/help.png);
    							display: inline-block;}
						</style>
						<div class="icon-help"></div>
						<?/*?><img class="icon-help" src="<?=SITE_DEFAULT_PATH?>/images/icons/help.png" alt="<?=GetMessage('HDR_ORDER_STATUS')?>" /><?*/?>
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
<?php endif;?>
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
						<?php if(!defined("T731")&&false):?>
							<a class="logo-img-new" href="/">
								GEBERIT
								<span>Фирменный магазин СЕТИ</span>
							</a>
						<?php else:?>
							<style type="text/css">.logo-img{width:210px;height:70px;background:url(<?=SITE_TEMPLATE_PATH?>/images/logo.png) no-repeat}</style>
							<div class="logo-img"></div>
						<? endif;?>
						<?/*$APPLICATION->IncludeComponent("custom:include", "", array(
								"PATH" => "/include/header_middle_logo.php"
							),
							false,
							array(
							)
						);*/?>
						<? if (!CClass::Instance()->IsRoot()):?>
							</a>
						<? endif;?>
						<?/* Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("roistat_visit_mobile");?>
						<div class="roistat-promo roistat_mobile"></div>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("roistat_visit_mobile", ".");*/?>
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
				<div class="phone column is-2 custom-live-ajax-update" id="custom-live-ajax-update-header-phone">
						<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_header_1");?>
						<span class="call_phone_1"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="call mskgeberit"><?=$arContact['PHONE']['VALUE']?></a></span>
						<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_header_1", "");?>
<a href="#" class="actionCallRequest"><?=GetMessage('HDR_CALL_BACK_27')?></a>
						<? // Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("roistat_visit");?>
<?/*
						<div class="roistat-promo roistat_desktop"></div>
*/?>
						<? // Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("roistat_visit", ".");?>
				</div>
				<div class="help column is-3 custom-live-ajax-update" id="custom-live-ajax-update-header-time">
					<span class="speaker">
						<style type="text/css">
							.icon-speaker{    width: 50px;
    							height: 52px;
								background: url(<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png) no-repeat;
								    background-position: center;
							}
						</style>
						<div class="icon-speaker"></div>
						<?/*?><img class="icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />*/?>
						<div class="dot"></div>
					</span>
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_header_1");?>
					<span class="info"><?=GetMessage('HDR_SCHEDULE_HELP_TEXT')?> <strong id="get-schedule-here"><?=$arContact['SCHEDULE']?></strong></span>
					<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_header_1", "");?>
					<span>
						<a href="#" class="actionChatConsultant"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
						<a href="#popupFeedback" class="callBackShow modal-link"><?=GetMessage('HDR_FEEDBAK_LINK')?></a>
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
				<span class="m_call_phone_1 donor_href" style="display:none !important;"><a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="mobile-phone-button search bottom-header__search _popup-catalog-menu-start-mobile-button--bottom-header mskgeberit"
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
				<div class="column is-7 search">
					<?$APPLICATION->IncludeComponent(
						"custom:search.header",
						"",
						array(),
						false
					);?>
				</div>
				<?/*?>
				<div class="pack column other_link_header_pack"></div>
				<div class="install column other_link_header_install"></div>
				<?*/?>
				<div class="phone column">
					<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts_header_2");?>
					<span class="call_phone_1 phone"><a class="mskgeberit" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></span>
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
	<? 

	if(!$MobileDetect->isMobile()){?>
	<?$APPLICATION->IncludeComponent(
		"custom:menu",
		"catalog_popup".(POPUP_MENU_TYPE == 'EXTENDED' ? '_extended' : ''),
		array(
			"TYPE" => "catalog"
		)
	);?>
	<?
	}
	?>
<? if($MobileDetect->isMobile()){?>
	<? $APPLICATION->IncludeComponent(
			"custom:menu",
			"catalog_popup_mobile",
			array(
				"TYPE" => "catalog",
			)
		);?>
	<?}?>