<div class='phone_block'>
	<i class="fa fa-phone"></i>
	<?
	if($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 129){
		?>
			<div class="phone_text"><a href="tel:+7 (495) 268-13-03">+7 (495) 268-13-03</a></div>
		<?
	}
	elseif($_COOKIE['BITRIX_SM_GEOLOCATION_LOCATION_ID'] == 817){
		?>
			<div class="phone_text"><a href="tel:+7 (812) 627-16-52">+7 (812) 627-16-52</a></div>
		<?
	}
	else{
		?>
			<div class="phone_text"><a href="tel:+7 (495) 268-13-03">+7 (495) 268-13-03</a></div>
		<?
	}
	?>
</div>