<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;

// delivery
$request = Application::getInstance()->GetContext()->getRequest();

$regionId = $request->getCookie("GEOLOCATION_ID");
$productPrice = $price;
if (!$regionId)
	$regionId = DEFAULT_GEOLOCATION_ID;

$regionInfo	 = [];
$deliveryInfo   = [];
$deliveryTK	 = [];
if (!!$regionId){
	// region fetch
	$ob = Bitrix\Sale\Location\LocationTable::getList(array(
		"filter" => array(
			"ID" => $regionId,
		),
		"runtime" => array(
			"CITY" => array(
				"data_type" => Bitrix\Sale\Location\Name\LocationTable::getEntity()->getDataClass(),
				"reference" => array(
					"=this.CITY_ID" => "ref.LOCATION_ID",
				),
			)
		),
		"select" => array(
			"ID",
			"CITY_NAME" => "CITY.NAME"
		),
	));
	if ($item = $ob->fetch()){
		$regionInfo = array(
			"ID" => $item["ID"],
			"CITY_NAME" => $item["CITY_NAME"],
		);
	}

	// delivery fetch
	$ob = CSaleDelivery::GetList(
		array(
			"SORT" => "ASC",
		),
		array(
			"LID" => SITE_ID,
			"ACTIVE" => "Y",
			"<=ORDER_PRICE_FROM" => $productPrice,
			">=ORDER_PRICE_TO" => $productPrice,
			"LOCATION" => $regionId,
			"PARENT_ID" => 21,
		),
		false,
		array(
			"nTopCount" => 1,
		)
	);
	if ($arItem = $ob->Fetch()){
		$deliveryInfo = array(
			"ID"		=> $arItem["ID"],
			"PRICE"	 => $arItem["PRICE"]?:\Bitrix\Sale\Delivery\Services\Manager::getById($arItem["ID"])['CONFIG']['MAIN']['PRICE'],
			"SELFPICKUP"=> !empty($arItem["STORE"]),
			"CURRENCY"  => $arItem["CURRENCY"]
		);
	}


	if (empty($deliveryInfo)){
		$ob = CSaleDelivery::GetList(
			array(
				"SORT" => "ASC",
			),
			array(
				"LID" => SITE_ID,
				"ACTIVE" => "Y",
				"<=ORDER_PRICE_FROM" => $productPrice,
				">=ORDER_PRICE_TO" => $productPrice,
				"LOCATION" => $regionId,
				"PARENT_ID" => 29,
			),
			false,
			array(
				"nTopCount" => 1,
			)
		);
		if ($arItem = $ob->Fetch()){
			$deliveryTK = array(
				"ID"		=> $arItem["ID"],
				"PRICE"	 => $arItem["PRICE"]?:\Bitrix\Sale\Delivery\Services\Manager::getById($arItem["ID"])['CONFIG']['MAIN']['PRICE'],
				"SELFPICKUP"=> !empty($arItem["STORE"]),
				"CURRENCY"  => $arItem["CURRENCY"]
			);
		}
	}
}

$regionName = $regionInfo["CITY_NAME"] ?: "Не определено";
ob_start();

//var_dump($deliveryInfo);
$sam = ($regionId and in_array($regionId, [129, /*2201,*/ 817]));
if (!empty($deliveryTK)):?>
	<div class="description__item-title">
		<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-3.png" alt="i">Доставка через ТК</div>
	<ul class="description__item-title-list">
		<li>Доставка в терминал: <span><?=!$deliveryTK['PRICE'] ? '<span class="green-text">Бесплатно</span>' : CurrencyFormat($deliveryTK['PRICE'], $deliveryTK['CURRENCY'])?></span></li>
		<li>Доставка до <?=$regionName?> по тарифам ТК</li>
	</ul>
<?elseif (empty($deliveryInfo)):
	$sam = false;?>
	<div class="description__item-title"><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-3.png" alt="i">Самовывоз</div>
	<ul class="description__item-title-list">
		<li>Самовывоз: <span class="green-text">Бесплатно</span></li>
	</ul>
<?else:?>
	<div class="description__item-title">
		<img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-3.png" alt="i"><?=$regionName?>
	</div>
	<ul class="description__item-title-list">
		<li>Стоимость доставки: <span><?=!$deliveryInfo['PRICE'] ? '<span class="green-text">Бесплатно</span>' : CurrencyFormat($deliveryInfo['PRICE'], $deliveryInfo['CURRENCY'])?></span></li>
		<?if ($deliveryInfo["SELFPICKUP"]):
			$sam = false;?>
			<li>Самовывоз: <span class="green-text">Бесплатно</span></li>
		<?endif;?>
	</ul>
<?endif;
if($sam):?>
<div class="description__item-title"><img src="<?=SITE_DEFAULT_PATH?>/images/icons/description-title-img-3.png" alt="i">Самовывоз</div>
<ul class="description__item-title-list">
	<li>Самовывоз: <span class="green-text">Бесплатно</span></li>
</ul>
<?endif;
$deliveryContent = ob_get_clean();
?>