<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arShowMenuLinks = CClass::getTopMenuLinks();
?>
<? if (in_array('GAZ', $arShowMenuLinks)):?>
<a href="https://gazkomfort.ru/" target="_blank" class="navbar-link" title="<?=GetMessage('HDR_MENU_GAZ_LABEL')?>">
	<img src="<?=SITE_DEFAULT_PATH?>/images/icons/box.png" alt="<?=GetMessage('HDR_MENU_GAZ_LABEL')?>" />
	<span>ОТОПЛЕНИЕ</span>
</a><?endif;?>