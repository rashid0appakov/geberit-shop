<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();

$params = base64_encode(serialize($arParams));
$formId = "form_$uniqueId";
$nameInputId = "name_$uniqueId";
$phoneInputId = "phone_$uniqueId";
$textInputId = "text_$uniqueId";
$submitNameSpanId = "submit_name_$uniqueId";
$timeSpanId = "time_$uniqueId";
$privateInputId = "time_$uniqueId";

$jsParams = array(
	"params" => $params,
	"form_id" => $formId,
	"name_input_id" => $nameInputId,
	"phone_input_id" => $phoneInputId,
	"text_input_id" => $textInputId,
	"submit_name_span_id" => $submitNameSpanId,
	"time_span_id" => $timeSpanId,
	"private_input_id" => $privateInputId,
	"ajax_url" => "/ajax/form/help_pickup.php",
);?>
<form id="<?=$formId?>">
	<div class="hideOnSubmit">
		<div class="columns">
			<div class="column is-6 private-message">
				<div class="inputPrivate">
					<label for="<?=$privateInputId?>">Ваше приватное сообщение*</label>
					<input id="<?=$privateInputId?>" name="inputPrivate" type="text" placeholder="Сообщение">
				</div>
			</div>
			<div class="column is-6">
				<div class="inputName">
					<label for="<?=$nameInputId?>">Ваше имя*</label>
					<input id="<?=$nameInputId?>" name="inputName" type="text" placeholder="Имя" required>
				</div>
			</div>
			<div class="column is-6">
				<div class="inputTel">
					<label for="<?=$phoneInputId?>">Номер телефона*</label>
					<input id="<?=$phoneInputId?>" class="mobMarginNull" type="tel" placeholder="7(" required>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="inputText">
					<label for="<?=$textInputId?>">Описание запроса*</label>
					<textarea name="text" id="<?=$textInputId?>" cols="30" rows="10" required></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="showOnSubmit hide">
		<p class="formOkText">
			<strong id="<?=$submitNameSpanId?>">Алексей</strong>, спасибо! Ваше сообщение отправлено.
			В ближайшее время с вами свяжутся наши менеджеры для уточнения деталей.
		</p>
	</div>
	<div class="popupFooter">
		<div class="columns">
			<div class="column is-5">
				<div class="hideOnSubmit">
					<div class="submit">
						<input type="submit" value="Отправить заявку">
					</div>
				</div>
				<div class="showOnSubmit hide">
					<div class="showOnSubmit hide">
						<button class="close96 callBackLinkClose">Обратный звонок</button>
					</div>
				</div>
			</div>
			<div class="column is-6">
				<div class="hideOnSubmit">
					<p class="iAgree">
						Отправляя данные Вы соглашаетесь с <br>
						<a href="/about/agreement/">Пользовательским соглашением</a>
					</p>
				</div>
				<div class="showOnSubmit hide">
					<p class="iAgree">
						Сообщение закроется через: <span><span id="<?=$timeSpanId?>">3</span> секунды</span>
					</p>
				</div>
			</div>
			<div class="column is-1">
				<div class="hideOnSubmit">
					<div class="questionImg">
						<img src="<?=$templateFolder?>/images/sendMail.png">
					</div>
				</div>
				<div class="showOnSubmit hide">
					<div class="questionImg">
						<img src="<?=$templateFolder?>/images/ok_sen_mail.png">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	window.feedbackHelpPickup = new JSFeedbackHelpPickup(<?=json_encode($jsParams)?>);
</script>