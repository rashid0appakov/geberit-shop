<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;

$worktime_start = Option::get("tiptop", "worktime_start", "");
$worktime_end = Option::get("tiptop", "worktime_end", "");

$current_time = time();
$dotDisabled = "";
if (strtotime($worktime_start) > $current_time || strtotime($worktime_end) < $current_time) {
	$dotDisabled = " disabled";
}

?>
<div style="display:none" id="dotResult_<?= $arResult['uniqueId'] ?>"><?= $dotDisabled ?></div>