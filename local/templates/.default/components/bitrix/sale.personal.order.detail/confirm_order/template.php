<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

if (!$GLOBALS['USER']->IsAuthorized () && empty($_REQUEST['access'])) {
    LocalRedirect ( "/personal/cart/" );
}

//pr($arResult);
?>
<div class="goods">
	<div class="container goods__container">
		<div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
				"bitrix:breadcrumb",
				"main",
				Array(
					"PATH"	  => "",
					"SITE_ID"   => SITE_ID,
					"START_FROM"=> "0"
				)
			);?>
		</div>
<?php
if (!empty($arResult['ERRORS']['FATAL']))
{
	foreach ($arResult['ERRORS']['FATAL'] as $error)
	{
		ShowError($error);
	}

	$component = $this->__component;

	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED]))
	{
		$APPLICATION->AuthForm('', false, false, 'N', false);
	}
}
else
{
	if (!empty($arResult['ERRORS']['NONFATAL']))
	{
		foreach ($arResult['ERRORS']['NONFATAL'] as $error)
		{
			ShowError($error);
		}
	}
	?>
		<div class="goods__title">
			<h1 class="goods__title-title"><?=$arResult['DATA']['NAME']?><?=(!$arResult['DATA']['NAME'] ? CClass::mb_ucfirst(Loc::getMessage('SPOD_THANX')) : Loc::getMessage('SPOD_THANX'))?></h1>
		</div>
    
      <div class="order-confirm-block columns">
        <div class="column is-12-mobile is-<?=($arResult['DELIVERY_ID'] == CClass::DELIVERY_SELF_PICKUP_ID ? 5 : 7)?>-desktop">
          <h2 class="is-size-3"><?=Loc::getMessage('SPOD_NUMBER')?>: <?=htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])?></h2>
          <h2 class="is-size-3 light"><?=Loc::getMessage('SPOD_SUMM')?>: <span class="no-wrap"><?=customFormatPrice($arResult["PRICE_FORMATED"])?></span></h2>
          <br />
                  <p><?=Loc::getMessage('SPOD_TEXT_1', ['#PHONE#' => CClass::normolizePhone($userPhone)])?></p>
          <div class="confirm-block order-info">
            <div class="c-title"><?=Loc::getMessage('SPOD_ORDER_INFO')?>:</div>
            <div class="c-label"><?= Loc::getMessage('SPOD_ORDER_DELIVERY')?>:</div>
                      <div class="c-value"><?=$arResult['DELIVERY']['NAME']?></div>
            <div class="c-label"><?= Loc::getMessage('SPOD_ORDER_PAYMENT')?>:</div><div class="c-value"><?=$arResult['DATA']['PAYS_YSTEM']['PAY_SYSTEM_NAME']?></div>
            <? if (!empty($arResult['DATA']['SERVICES'])):?>
            <div class="c-label"><?= Loc::getMessage('SPOD_ORDER_SERVICES')?>:</div><div class="c-value"><?=join(', ', $arResult['DATA']['SERVICES'])?></div>
            <? endif;?>
          </div>
          <? if ($arResult['DELIVERY_ID'] == CClass::DELIVERY_SELF_PICKUP_ID):?>
          <div class="confirm-block stock-time">
            <div class="c-title"><?=Loc::getMessage('SPOD_STOCK_INFO')?>:</div>
            <?if(empty($arResult['DATA']['CONTACTS']['SKLAD'])):?>
              <div class="c-label"><?=Loc::getMessage('SPOD_STOCK_ADRESS')?>:</div>
              <div class="c-value c-width"><?=$arResult['DATA']['CONTACTS']['ADRESS']?></div>
            <? endif;?>
            <? if ($arResult['DATA']['CONTACTS']['STOCK']):?><div class="clear-btm"></div>
            <div class="c-label"><?=Loc::getMessage('SPOD_STOCK_TIME')?>:</div>
            <div class="c-value c-width"><?=$arResult['DATA']['CONTACTS']['STOCK']?></div>
            <? endif;?>
          </div>
          <? endif;?>
        </div>
        <div class="column is-12-mobile is-<?=($arResult['DELIVERY_ID'] == CClass::DELIVERY_SELF_PICKUP_ID ? 5 : 3)?>-desktop">
          <?if(!empty($arResult['DATA']['CONTACTS']['SKLAD'])):?>
            <br>
            <?=$arResult['DATA']['CONTACTS']['SKLAD'];?>
          <?else:?>
            <? if ($arResult['DELIVERY_ID'] == CClass::DELIVERY_SELF_PICKUP_ID):
              if ($arResult['DATA']['CONTACTS']['MAPS']):
                $arCoords   = explode(',', $arResult['DATA']['CONTACTS']['MAPS']);
            ?>
            <?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", Array(
                "INIT_MAP_TYPE" => "MAP",
                "MAP_DATA" => serialize(array(
                  'yandex_lat' => $arCoords[0],
                  'yandex_lon' => $arCoords[1],
                  'yandex_scale' => 13,
                  'PLACEMARKS' => array(
                    array(
                      'TEXT' => "",
                      'LON' => $arCoords[1],
                      'LAT' => $arCoords[0],
                    ),
                  ),
                )),
                "MAP_WIDTH" => "auto",
                "MAP_HEIGHT" => "480",
                "CONTROLS" => array(
                  "SMALLZOOM",
                ),
                "OPTIONS" => array(
                  "ENABLE_SCROLL_ZOOM",
                  "ENABLE_DBLCLICK_ZOOM",
                  "ENABLE_DRAGGING"
                ),
                "MAP_ID" => "yam_1"
              )
            );?>
            <? endif;
                    else:?>
                    <img src="<?=$arResult['DATA']['ORDER_IMG']?>" alt="" class="ok-img"/>
            <?endif;?>
          <?endif;?>
        </div>
      </div>
	</div>
</div>
<?$APPLICATION->IncludeComponent("custom:include", "", array(
		"PATH" => "include/template_counters_order.php",
		"RESULT" => $arResult
	),
	false
);?>
<?}?>