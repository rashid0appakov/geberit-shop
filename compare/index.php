<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сравнение");
?>


<div class="goods">

  <div class="container goods__container">
    <div class="goods__breadcrumbs">
      <ul class="breadcrumbs">
        <li class="breadcrumbs__item">
          <a href="#">Главная</a>
        </li>
        <li class="breadcrumbs__item">
          <span> Сравнение товаров</span>

        </li>
      </ul>
      <div class="breadcrumbs__need-help">
        <a href="#">Нужна помощь в выборе душевой кабины?</a>
      </div>
    </div>

<?
include_once($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/components/bitrix/catalog/.default/functions.php');

$comparePropertyCode = getShowPropertyValues(CATALOG_IBLOCK_ID, 8600);

?>

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.result",
	".default",
	array(
		"AJAX_MODE" => "Y",
		"NAME" => "CATALOG_COMPARE_LIST",
		"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
		"FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "PREVIEW_TEXT",
			3 => "PREVIEW_PICTURE",
			4 => "DETAIL_TEXT",
			5 => "DETAIL_PICTURE",
			6 => "DATE_ACTIVE_FROM",
			7 => "DATE_ACTIVE_TO",
		),
		"PROPERTY_CODE" => $comparePropertyCode,
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"DETAIL_URL" => "",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"PRICE_CODE" => CClass::getCurrentPriceCode(),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"DISPLAY_ELEMENT_SELECT_BOX" => "Y",
		"ELEMENT_SORT_FIELD_BOX" => "name",
		"ELEMENT_SORT_ORDER_BOX" => "asc",
		"ELEMENT_SORT_FIELD_BOX2" => "id",
		"ELEMENT_SORT_ORDER_BOX2" => "desc",
		"HIDE_NOT_AVAILABLE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "Y",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"TEMPLATE_THEME" => "blue",
		"COMPONENT_TEMPLATE" => ".default",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>

	<br/>
	<br/>


  </div>

</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>