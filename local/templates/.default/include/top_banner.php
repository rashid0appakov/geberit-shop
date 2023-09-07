<?
use \Bitrix\Conversion\Internals\MobileDetect;

global $arSlideTop;


$MobileDetect = new MobileDetect;
$dir = $APPLICATION->GetCurDir();

$arSlideTop = Array(array(
        "LOGIC" => "OR",
        array("PROPERTY_LINK" => false),
        array("=PROPERTY_LINK" => $dir),
    ));
if($MobileDetect->isMobile()){
	$templ = "top_slider_mob";
}else{
	$templ = "top_slider";
}
$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    $templ,
    array(
        "ACTIVE_DATE_FORMAT" => "",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "N",
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "N",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(
            0 => "NAME",
            1 => "DETAIL_PICTURE",
            2 => "",
        ),
        "FILTER_NAME" => "arSlideTop",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "122",
        "IBLOCK_TYPE" => "content",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "100",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(
            0 => "PIC",
            1 => "LINK",
            2 => "PIC_MOB",
        ),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "ID",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "COMPONENT_TEMPLATE" => "main_slider"
    ),
    false
);	


/*<!--Top banner-->
<style>
.may1 {
	height: 30px;
	background:#edeef8; 
	text-align: center;
	background-position: center;
}

.may_warning {
	background-color: #d24b44;
	text-transform: uppercase;
	border-radius: 2px;
	height: 25px;
	font-size: 11px;
	line-height: 17px;
	letter-spacing: 0.02em;
	display: inline-block;
	color: #fff;
	padding: 5px;
	margin-right: 10px;
}

.may_text {
	color: #575b71;
	font-size: 14px;
	line-height: 30px;
	display: inline-block;
}

@media (max-width:992px){
	icons_before_catalog_wrapper{
			display:none;
}
	
}
@media (max-width:767px){
	.may1 {
		height: 90px;
		margin-top:95px;
	}
	.may_warning {
		display:none;
	}
	.header_mobile_fixed {
		margin-top: -30px;
	}
	header{
		margin-top:0px;
}
	icons_before_catalog_wrapper{
			display:none;
}
@media (max-width:440px){
	.may1 {
		height: 90px;
	}
	.header_mobile_fixed {
		margin-top: -60px;
	}
	icons_before_catalog_wrapper{
			display:none;
}
}
</style>
<div class="may1" style="">
	<div class="may_text">
		<b>Уважаемые клиенты! Телефон временно не работает, ведутся технические работы. Просьба оформлять заказ через корзину или почту. <?=$arContacts['EMAIL']?></b>
	</div>
	<!-- a href="#">
		<img class="may_desk" src="/upload/nds.jpg" />
		<img class="may_mob" src="/upload/nds_mob.jpg" />
	</a-->
</div>
<!--Top banner-->*/?>