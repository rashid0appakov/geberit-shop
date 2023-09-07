<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale\Delivery;
use Bitrix\Sale\Location\LocationTable;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;
use Bitrix\Sale\Delivery\Services\Manager as DeliveryManager;
use Bitrix\Sale\PaySystem\Manager as PaySystemManager;

use Bitrix\Main\Application;
use Bitrix\Main\Service\GeoIp;
use Bitrix\Sale\Location;


Loader::includeModule('catalog');
Loader::includeModule('sale');
Loader::includeModule('iblock');

$arParams = array();

$arParams['PRICE'] = intval($_REQUEST['price']);
$arParams['LOC'] = intval($_REQUEST['loc']);

$request = Application::getInstance()->getContext()->getRequest();

$regionId = $arParams['LOC'];

if($regionId < 1)
{
	$regionId = DEFAULT_GEOLOCATION_ID;
}

$locationRuntime = array(
    "COUNTRY" => array(
        'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
        'reference' => array(
            '=this.COUNTRY_ID' => 'ref.LOCATION_ID',
        ),
    ),
    "REGION" => array(
        'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
        'reference' => array(
            '=this.REGION_ID' => 'ref.LOCATION_ID',
        ),
    ),
    "CITY" => array(
        'data_type' => Location\Name\LocationTable::getEntity()->getDataClass(),
        'reference' => array(
            '=this.CITY_ID' => 'ref.LOCATION_ID',
        ),
    ),
);
$locationSelect = array(
    "ID",
    "COUNTRY_NAME" => "COUNTRY.NAME",
    "REGION_NAME" => "REGION.NAME",
    "CITY_NAME" => "CITY.NAME",
);

$locationData = Location\LocationTable::getRow(array(
    "runtime" => $locationRuntime,
    "select" => $locationSelect,
    "filter" => array(
        "ID" => $regionId,
    ),
));

$arDelivery = [];

if($locationData)
{
	$ob = CSaleDelivery::GetList(
		array(
			"SORT" => "ASC",
		),
		array(
			"LID" => SITE_ID,
			"ACTIVE" => "Y",
			"LOCATION" => $regionId,
		),
		false
	);
	while($arItem = $ob->Fetch()){
		$srok = [];
		if($arItem['PERIOD_FROM'])
		{
			$srok[] = 'от '.$arItem['PERIOD_FROM'];
		}
		if($arItem['PERIOD_TO'])
		{
			$srok[] = 'до '.$arItem['PERIOD_TO'];
		}
		switch($arItem['PERIOD_TYPE'])
		{
			case 'D':
				$srok[] = 'дней';
				break;
			case 'M':
				$srok[] = 'месяцев';
				break;
		}
		$arItem['SROK'] = implode(' ', $srok);
		$arDelivery[$arItem["ID"]] = $arItem;
	}
}

$sam = ($regionId and in_array($regionId, [129, 2201, 817]));


/*
$arDeliverys = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
/*foreach($arDeliverys as $arDelivery)
{
	\Bitrix\Sale\Delivery\Restrictions\ByLocation::check(
	integer $locationCode, 
	array(), 
	$arDelivery['ID']
	);
}*/


?>
<h4 class="payment-block__items-title">Доставка</h4>
<div class="payment-block__item-text">
	<?
	$arFilter = CClass::getDeliveryData(true);

	if(isset($arFilter[$regionId])){
		$id = $arFilter[$regionId]['ID'];
		$code = $arFilter[$regionId]['CODE'];
	}
	elseif(isset($arFilter['OTHER'])){
		$id = $arFilter['OTHER']['ID'];
		$code = $arFilter['OTHER']['CODE'];
	}
	else{
		$id = $arFilter[DEFAULT_GEOLOCATION_ID]['ID'];
		$code = $arFilter[DEFAULT_GEOLOCATION_ID]['CODE'];
	}

	$APPLICATION->IncludeComponent("bitrix:news.detail", "delivery",
		Array(
			"DISPLAY_NAME" => "N",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => CClass::RU_DELIVERY_IBLOCK_ID,
			"FIELD_CODE" => array(),
			"PROPERTY_CODE" => array(
                            0=>"DESCRIPTION"
                        ),
			"DETAIL_URL" => "/delivery/#ELEMENT_CODE#/",
			"SECTION_URL" => "/delivery/",
			"META_KEYWORDS" => "-",
			"META_DESCRIPTION" => "-",
			"BROWSER_TITLE" => "-",
			"SET_CANONICAL_URL" => "N",
			"SET_LAST_MODIFIED" => "N",
			"SET_TITLE" => "N",
			"MESSAGE_404" => "",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_GROUPS" => "N",
			"USE_PERMISSIONS" => "N",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "",
			"PAGER_SHOW_ALL" => "",
			"CHECK_DATES" => "Y",
			"ELEMENT_ID" => $id,
			"ELEMENT_CODE" => $code,
			"IBLOCK_URL" => "/delivery/",
		),
		$component
	);

	?>
	
	<?/*
	<p>В таблице показана стоимость доставки в <?=$locationData['CITY_NAME']?></p>
	<table class="custom-table table table-md table-hover table-bordered table-striped table-responsive-sm">
		<thead>
			<tr>
				<th scope="col" style="vertical-align: middle;">
					Описание доставки
				</th>
				<th scope="col" class="text-center" style="vertical-align: middle; ">
					Сумма заказа минимальная
				</th>
				<th scope="col" class="text-center" style="vertical-align: middle; ">
					Сумма заказа максимальная
				</th>
				<th scope="col" class="text-center" style="vertical-align: middle; ">
					Стомость доставки
				</th>
				<th scope="col" class="text-center" style="vertical-align: middle; ">
					Срок доставки
				</th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arDelivery as $arItem):?>
				<tr scope="row">
					<td scope="col" style="vertical-align: middle;">
						<?=$arItem['DESCRIPTION']?>
					</td>
					<td scope="col" class="text-center" style="vertical-align: middle; ">
						<?=($arItem['ORDER_PRICE_FROM'] ? SaleFormatCurrency($arItem['ORDER_PRICE_FROM'], $arItem['CURRENCY']) : 'от 1 рубля');?>
					</td>
					<td scope="col" class="text-center" style="vertical-align: middle; ">
						<?=($arItem['ORDER_PRICE_TO'] ? SaleFormatCurrency($arItem['ORDER_PRICE_TO'], $arItem['CURRENCY']) : 'и выше');?>
					</td>
					<td scope="col" class="text-center" style="vertical-align: middle; ">
						<?=($arItem['PRICE'] ? SaleFormatCurrency($arItem['PRICE'], $arItem['CURRENCY']) :'Бесплатно');?>
					</td>
					<td scope="col" class="text-center" style="vertical-align: middle; ">
						<?=$arItem['SROK']?>
					</td>
				</tr>
			<?endforeach?>
		<tbody>
	</table>
	*/?>
</div>

<h5 class="payment-block__item-title">Условия доставки</h5>
<div class="payment-block__item-text">
	<?$APPLICATION->IncludeComponent("custom:include", "", array(
			"PATH" => "include/text_element_delivery.php"
		),
		false
	);?>
</div>

<h4 class="payment-block__items-title">Оплата</h4>
<div class="payment-block">
	<?$APPLICATION->IncludeComponent("custom:include", "", array(
			"PATH" => "include/text_element_payment.php"
		),
		false
	);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>