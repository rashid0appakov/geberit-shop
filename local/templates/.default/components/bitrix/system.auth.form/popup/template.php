<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$formId = "system_auth_form_popup_".$arResult["RND"];
$errorContinerId = "system_auth_form_popup_error_".$arResult["RND"];

$jsParams = array(
	"form_id" => $formId,
	"error_container_id" => $errorContinerId,
	"ajax_url" => $templateFolder."/ajax.php",
);?>
<div id="popupLoginForm" class="popup69 popupCommon" style="display: none">
	<div class="close69">
		<div>
			<img src="<?=$templateFolder?>/images/close.png" alt="" />
		</div>
	</div>
	<div class="popupTitle">Вход на сайт</div>
	<div id="<?=$errorContinerId?>" class="error-container"></div>
	<form action="<?=$arResult["AUTH_URL"]?>" method="POST" name="system_auth_form<?=$arResult["RND"]?>" id="<?=$formId?>" class="validateFormLogin">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<input type="hidden" name="USER_REMEMBER" value="Y" />
		<div class="columns">
			<div class="column is-6">
				<div class="inputLogin">
					<label for="inputLogin">Телефон или E-mail*</label>
					<input id="inputLogin" name="USER_LOGIN" type="text" placeholder="example@site.ru" required>
				</div>
			</div>
			<div class="column is-6">
				<div class="inputPass">
					<label for="inputPass">Пароль*</label>
					<input id="inputPass" name="USER_PASSWORD" type="password" placeholder="*******" required>
				</div>
			</div>
		</div>

		<div class="showOnSubmit user-error">Неверный логин или e-mail</div>

		<div class="popupFooter">
			<div class="columns">
				<div class="column is-5">
					<div class="submit">
						<input type="submit" name="Login" value="Войти" />
					</div>
				</div>
				<div class="column is-6">
					<p class="regLink">
						<a href="#">Зарегистрироваться на сайте</a>
					</p>
				</div>
				<div class="column is-1">
					<div class="questionImg">
						<img src="<?=$templateFolder?>/images/zamok.png" alt="" />
					</div>
				</div>
			</div>
		</div>
	</form>
    <script type="text/javascript">
		window.authPopupForm = new JSAuthFormPopup(<?=json_encode($jsParams)?>);
	</script>
</div>