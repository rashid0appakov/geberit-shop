<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$uniqueId = uniqid();

$jsParams = array(
	"ajaxUrl" => $templateFolder."/ajax.php",
	"uniqueId" => $uniqueId,
);?>
<div id="popup_<?=$uniqueId?>" class="popup69 popupTellMe" style="display: none">
	<div class="close69">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png">
		</div>
	</div>
	<div class="popupTellMeTitle">Сообщить о появлении <span id="product_name_<?=$uniqueId?>"></span></div>
	<div class="columns popupTellMeColumns">
		<div class="column is-6">
			<div class="popupTellMeImg">
				<img id="product_image_<?=$uniqueId?>">
			</div>
		</div>
		<div class="column is-6">
			<form id="form_<?=$uniqueId?>">
				<input id="product_id_<?=$uniqueId?>" type="hidden">
				<div class="name">
					<label for="name_<?=$uniqueId?>">Ваше имя*</label>
					<input id="name_<?=$uniqueId?>" type="text" placeholder="Имя" required>
				</div>
				<div class="email">
					<label for="email_<?=$uniqueId?>">Ваш e-mail*</label>
					<input id="email_<?=$uniqueId?>" type="email" placeholder="eMail" required>
				</div>
				<div class="tel">
					<label for="phone_<?=$uniqueId?>">Ваш телефон*</label>
					<input id="phone_<?=$uniqueId?>" type="tel" placeholder="7(" required>
				</div>
				<div class="submit">
					<input type="submit" value="Отправить">
					<p>Вам будет отпралено смс, на указаный номер телефона</p>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	window.ReportAppearancePopup = new JSReportAppearancePopup(<?=json_encode($jsParams)?>);
</script>