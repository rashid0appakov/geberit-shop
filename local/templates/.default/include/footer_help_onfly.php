<?php
	use Bitrix\Main\Config\Option;

	$worktime_start = Option::get("tiptop", "worktime_start", "");
	$worktime_end = Option::get("tiptop", "worktime_end", "");

	$current_time = time();
	$timeToHide = "";
	if (strtotime($worktime_start) > $current_time || strtotime($worktime_end) < $current_time)
		$timeToHide = ' style="display:none;"';

	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$LINK='/delivery/';
	$SERV_NAME = 'geberit-shop.ru';
			if($_SERVER["HTTP_HOST"]=='ekb.geberit-shop.ru'||$geo_id ==2201){
				$LINK .='ekaterinburg/';
			}elseif($_SERVER["HTTP_HOST"]=='krasnodar.geberit-shop.ru'||$geo_id ==1095){
				$LINK .='krasnodar/';
			}elseif($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'||$geo_id ==817){
				$LINK .='sankt-peterburg/';
			}elseif($geo_id==129){
				$LINK .='moskva/';
			}else{
				$LINK .='moskva/';
			}

			if($_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru' || $geo_id ==1095){
				$SERV_NAME = 'spb.geberit-shop.ru';
			}elseif($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru' || $geo_id ==817){
				$SERV_NAME = 'spb.geberit-shop.ru';
			}elseif($_SERVER["HTTP_HOST"]=='ekb.geberit-shop.ru' || $geo_id ==2201){
				$SERV_NAME = 'ekb.geberit-shop.ru';
			}else{
				$SERV_NAME = 'geberit-shop.ru';
			}
?>
<div class="goods__need-help">
	<div class="need-help">
		<div class="need-help__title">
			<p class="need-help__title-question"></p>
			<p class="need-help__title-title">Нужна помощь?</p>
		</div>
		<div class="need-help__dropdown">
			<ul class="need-help__dropdown-list">
				<?/*<li class="need-help__dropdown-item"><a href="#">Онлайн подборщик</a></li> */?>
				<li class="need-help__dropdown-item"<?= $timeToHide ?>><a href="#" class="actionChatConsultant"><?=GetMessage('HDR_CONSULT_BUTTON')?></a></li>
				<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("phone_helponfly");?>
				<li class="need-help__dropdown-item"<?= $timeToHide ?>><a href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></li>
				<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("phone_helponfly", "");?>
				<li class="need-help__dropdown-item">
					<a href="#popupFeedback" class="callBackShow modal-link"><?=GetMessage('HDR_FEEDBAK_LINK')?></a>
				</li>
				<li class="need-help__dropdown-item"<?= $timeToHide ?>><a href="#" class="actionCallRequest"><?=GetMessage('HDR_ORDER_CALLBACK')?></a></li>
			</ul>
			<ul class="need-help__dropdown-list">
				<li class="need-help__dropdown-item"><a href="https://<?=$SERV_NAME?><?=$LINK?>">Доставка</a></li>
				<li class="need-help__dropdown-item"><a href="#" class="call-ec-widget">Доставка в регионы</a></li>
				<?/*<li class="need-help__dropdown-item"><a href="#">Гарантия</a></li>
				<li class="need-help__dropdown-item"><a href="#">Доставка</a></li>*/?>
				<li class="need-help__dropdown-item"><a href="/payments/">Оплата</a></li>
				<?/*<li class="need-help__dropdown-item"><a href="#">Возврат и обмен</a></li>
				<li class="need-help__dropdown-item"><a href="#">Контакты</a></li>*/?>
			</ul>
			<ul class="need-help__dropdown-list">
				<li class="need-help__dropdown-item"><a href="#popupSearchError" class="callBackShow modal-link">Сообщить об ошибке</a></li>
			</ul>
		</div>
		<div class="need-help__triangle"></div>
	</div>
</div>