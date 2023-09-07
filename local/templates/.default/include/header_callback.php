<?php
	use Bitrix\Main\Config\Option;

	$worktime_start = Option::get("tiptop", "worktime_start", "");
	$worktime_end   = Option::get("tiptop", "worktime_end", "");

	$current_time   = time();
	$dotDisabled	= "";
	if (strtotime($worktime_start) > $current_time || strtotime($worktime_end) < $current_time)
		$dotDisabled = " disabled";
?>
<div class="phone column is-2">
	<span class="duravitmsk">
		<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_headercallback");?>
		<a href="tel:<?=$arContact['PHONE']['NUMBER']?>" class="call"><?=$arContact['PHONE']['VALUE']?></a>
		<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_headercallback", "");?>
	</span>
	<a href="#" class="actionCallRequest"><?=GetMessage('HDR_CALL_BACK_27')?></a>
</div>
<div class="help column is-3">
	<span class="speaker">
		<img class="icon-speaker" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />
		<div class="dot<?= $dotDisabled ?>"></div>
	</span>
	<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("schedule_headercallback");?>
	<span class="info"><?=GetMessage('HDR_SCHEDULE_HELP_TEXT')?> <strong><?=$arContact['SHEDULE']?></strong></span>
	<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("schedule_headercallback", "");?>
	<span>
		<a href="#" class="actionChatConsultant"><?=GetMessage('HDR_CONSULT_BUTTON')?></a>
		<a href="#popupFeedback" class="callBackShow modal-link"><?=GetMessage('HDR_FEEDBAK_LINK')?></a>
	</span>
</div>