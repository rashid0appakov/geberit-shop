<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arShowMenuLinks = CClass::getTopMenuLinks();
?>
<? if (in_array('LIGHT', $arShowMenuLinks)):?>
<a href="https://swet-online.ru/" target="_blank" class="navbar-link" title="<?=GetMessage('HDR_MENU_LIGHT_LABEL')?>">
	<img src="<?=SITE_DEFAULT_PATH?>/images/icons/tasks.png" alt="<?=GetMessage('HDR_MENU_LIGHT_LABEL')?>" />
	<span>ОСВЕЩЕНИЕ</span>
</a><?endif;?>
<? if (in_array('TIPTOP', $arShowMenuLinks)):?>
<a href="https://tiptop-shop.ru/" target="_blank" class="navbar-link" title="<?=GetMessage('HDR_MENU_TIPTOP_LABEL')?>">
	<img src="<?=SITE_DEFAULT_PATH?>/images/icons/water.png" alt="<?=GetMessage('HDR_MENU_TIPTOP_LABEL')?>" />
	<span>САНТЕХНИКА</span>
</a><?endif;?>