<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$DIR = $APPLICATION->GetCurDir();
$arContact = CClass::Instance()->getLocationContacts();
$arShowMenuLinks= CClass::getTopMenuLinks();
$arSite = $GLOBALS['PAGE_DATA']['SITE'];

use Bitrix\Main\Page\Asset;
?>
<!-- <?=$_SERVER['REQUEST_URI']?> -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0" />
<meta name="format-detection" content="telephone=no" />

<? if (file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/images/favicon.ico')):?>
<link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/images/favicon.ico" type="image/x-icon" />
<? endif;?>
<? if (file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/images/favicon-114.png')):?>
<link rel="apple-touch-icon" sizes="57x57" href="<?=SITE_TEMPLATE_PATH?>/images/favicon-114.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?=SITE_TEMPLATE_PATH?>/images/favicon-144.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?=SITE_TEMPLATE_PATH?>/images/favicon-114.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?=SITE_TEMPLATE_PATH?>/images/favicon-144.png" />
<? endif;?>
<?
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/bulma-0.7.1/css/bulma.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/shake-animation.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/debug.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/overrides.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/styles.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/swiper/swiper.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/swiper/promo.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/fonts.css");

if( $DIR != '/' ) {
	Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/goods-list.css");
}

//Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/goods-card.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/bulma-ext/bulma-tooltip.min.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/owlcarousel/owl.carousel.min.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/owlcarousel/owl.theme.default.min.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/slick/slick.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/components/filter-price.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/comparison.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/css/jquery.fancybox.css");
Asset::getInstance()->addCss(SITE_DEFAULT_PATH.'/template_styles.css');
Asset::getInstance()->addCss(SITE_DEFAULT_PATH."/styles.css");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/template_styles.css');

Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/fixes.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery/jquery-3.3.1.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery.cookie.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery.maskedinput.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery.validate.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery.fancybox.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jquery.fitvids.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/passfield.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/messages_ru.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/owlcarousel/owl.carousel.min.js");
//Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/swiper-4.3.5/swiper.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/swiper-4.5.0/swiper.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/animater.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/progressbar/progressbar.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/slick/slick.min.js");

Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/simsalabim-sisyphus-1878a0f/sisyphus.js");

Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/main.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/animate.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/common.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/select-box.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/callbackManager.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/goods-card.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/goods-list.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/compare.min.js");
Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/jsrender.min.js");


if ($DIR == '/personal/cart/')
	Asset::getInstance()->addJs(SITE_DEFAULT_PATH."/js/script.js");

//$APPLICATION->ShowHead();?>
<?$APPLICATION->ShowMeta("robots")?>
<?$APPLICATION->ShowLink("canonical", null, true);?>
<?$APPLICATION->ShowCSS()?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<?$APPLICATION->ShowMeta("description");?>
<? if ($DIR == '/personal/cart/'):?>
<script type="text/javascript">
	window.BasketManager = new JSBasketManager(<?=json_encode(array(
		"add2BasketUrl" => SITE_DEFAULT_PATH."/ajax/basket/add2basket.php",
		"changeQuantityUrl" => SITE_DEFAULT_PATH."/ajax/basket/changeQuantity.php",
		"deleteFromBasketUrl" => SITE_DEFAULT_PATH."/ajax/basket/deleteFromBasket.php",
	))?>);
	window.CallbackManager = new JSCallbackManager(<?= json_encode([]) ?>);
</script>
<? endif;?>
