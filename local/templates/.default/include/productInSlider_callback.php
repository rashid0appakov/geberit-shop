<?php
use Bitrix\Main\Config\Option;

$worktime_start = Option::get("tiptop", "worktime_start", "");
$worktime_end = Option::get("tiptop", "worktime_end", "");

$current_time = time();
$dotDisabled = "";
if (strtotime($worktime_start) > $current_time || strtotime($worktime_end) < $current_time) {
	$dotDisabled = " disabled";
}

?>

<div class="productInSlaiderCallConsultant">
	<span class="productInSlaiderCallConsultantSpeaker">
		<img class="productInSlaiderCallConsultantSpeakerIcon" src="<?=SITE_DEFAULT_PATH?>/images/icons/speaker.png" alt="" />
		<div class="productInSlaiderCallConsultantSpeakerDot<?= $dotDisabled ?>"></div>
	</span>
	<p class="productInSlaiderCallConsultantSpeakerLink"><a href="#">Обсудить этот товар с чат консультантом</a></p>
</div>