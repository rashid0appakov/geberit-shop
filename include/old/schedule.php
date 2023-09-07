<?
if($arParams['TYPE'] != 'footer'){
	?>
	<p class="time">
		<i class="fa fa-clock-o"></i>Время работы:
	</p>
	<?
}
?>
<div class="schedule_block">
	<?
	if($arParams['TYPE'] == 'footer'){?><i class="fa fa-clock-o"></i><?}
	if($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 129){
		?>
			<div class="schedule_text">Ежедневно 09:00 - 21:00</div>
		<?
	}
	elseif($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 817){
		?>
			<div class="schedule_text">Раб С 9-00 ДО 20-00<br>
			СБ C 11-00 ДО 18-00</div>
		<?
	}
	else{
		?>
			<div class="schedule_text">Ежедневно 09:00 - 21:00</div>
		<?
	}
	?>
</div>
