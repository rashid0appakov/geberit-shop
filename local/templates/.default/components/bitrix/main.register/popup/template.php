<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$uniqueId = uniqid();

$formId = "system_reg_form_popup_".$uniqueId;
$errorContinerId = "system_reg_form_popup_error_".$uniqueId;

$jsParams = array(
	"form_id" => $formId,
	"error_container_id" => $errorContinerId,
	"ajax_url" => $templateFolder."/ajax.php",
);?>
<div id="popupRegForm" class="popup69 popupCommon" style="display: none">
	<div class="close69">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" alt="" />
		</div>
	</div>
	<div class="popupTitle">Регистрация на сайте</div>
	<form action="<?=POST_FORM_ACTION_URI?>" method="POST" name="regform" id="<?=$formId?>" class="validateFormReg">
		<?=bitrix_sessid_post()?>
		<div class="columns">
			<div class="column is-6">
				<div class="form-field">
					<label for="register_input_name">Ваше имя*</label>
					<input id="register_input_name" name="REGISTER[NAME]" type="text" required>
				</div>
				<div class="form-field">
					<label for="register_input_lastname">Фамилия*</label>
					<input id="register_input_lastname" name="REGISTER[LAST_NAME]" type="text" required>
				</div>
				<div class="form-field">
					<label for="register_input_email">E-mail*</label>
					<input id="register_input_email" name="REGISTER[EMAIL]" type="email" required>
				</div>
			</div>
			<div class="column is-6">
				<div class="form-field">
					<label for="register_input_phone">Телефон*</label>
					<input id="register_input_phone" name="REGISTER[PERSONAL_PHONE]" type="tel" required>
				</div>
				<div class="form-field">
					<label for="register_input_password">Пароль*</label>
					<input id="register_input_password" name="REGISTER[PASSWORD]" type="password" placeholder="*******" required>
				</div>
				<div class="form-field">
					<label for="register_input_confirm">Подтверждение*</label>
					<input id="register_input_confirm" name="REGISTER[CONFIRM_PASSWORD]" type="password" placeholder="*******" required>
				</div>
			</div>
		</div>
        <div id="<?=$errorContinerId?>" class="error-container"></div>
		<div class="popupFooter">
			<div class="columns">
				<div class="column is-5">
					<div class="submit">
						<input type="submit" name="register_submit_button" value="Регистрация">
					</div>
				</div>
				<div class="column is-6">
					<p class="iAgree">
						Отправляя данные Вы соглашаетесь с <br>
						<a href="#">Пользовательским соглашением</a>
					</p>
				</div>
				<div class="column is-1">
					<div class="questionImg">
						<img src="<?=$templateFolder?>/images/iconReg.png">
					</div>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		window.regPopupForm = new JSRegFormPopup(<?=json_encode($jsParams)?>);
	</script>
</div>