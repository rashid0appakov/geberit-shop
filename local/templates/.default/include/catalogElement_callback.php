<?php
use Bitrix\Main\Config\Option;

$worktime_start = Option::get("tiptop", "worktime_start", "");
$worktime_end = Option::get("tiptop", "worktime_end", "");

$current_time = time();
$dotDisabled = "";
if (strtotime($worktime_start) > $current_time || strtotime($worktime_end) < $current_time)
	$dotDisabled = " disabled";

$arContact = empty($arContact) ? CClass::Instance()->getLocationContacts() : $arContact;
?>

<div class="help">
	 <span class="speaker">
		  <img class="icon-speaker buy__item-icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />
		  <div class="dot<?= $dotDisabled ?>"></div>
	 </span>
	 <span class="buy__item-info">Подходит ли мне этот товар? Как установить?</span>
	 <div class="buy__item-phone">
		  <img src="<?=SITE_DEFAULT_PATH?>/images/icons/phone.png" alt="phone" />
		  <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_callback");?>
		  <a class="phone" href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a>
		  <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_callback", "");?>
	 </div>
	 <span class="buy__item-call">
		  <a href="#" class="actionCallRequest"><?=GetMessage('HDR_ORDER_CALLBACK')?></a>
		  <a href="#" class="actionChatConsultant"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
	 </span>
</div>