<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();

$params = base64_encode(serialize($arParams));
$formId = "form_$uniqueId";
$mailInputId = "mail_$uniqueId";
$phoneInputId = "phone_$uniqueId";
$timeSpanId = "time_$uniqueId";

$jsParams = array(
	"params" => $params,
	"form_id" => $formId,
	"mail_input_id" => $mailInputId,
	"phone_input_id" => $phoneInputId,
	"time_span_id" => $timeSpanId,
	"ajax_url" => "/ajax/form/credit.php",
);?>
	<div class="hideOnSubmit">
		<div class="columns">
			<div class="column is-6">
				<div class="inputName">
					<label for="<?=$mailInputId?>">Почта*</label>
					<input id="<?=$mailInputId?>" name="customerEmail" type="email" placeholder="test@test.ru" required>
				</div>
			</div>
			<div class="column is-6">
				<div class="inputTel">
					<label for="<?=$phoneInputId?>">Телефон*</label>
					<input id="<?=$phoneInputId?>" name="customerPhone" type="tel" placeholder="7(" required class="mobMarginNull">
				</div>
			</div>
		</div>
	</div>
	
	<div class="showOnSubmit hide">
		<p class="formOkText">
			Заполните форму на странице банка
		</p>
	</div>

	<div class="popupFooter" style="margin:0px -70px -70px;">
		<div class="columns">
			<div class="column is-5">
				<div class="hideOnSubmit">
					<div class="submit">
						<input type="submit" value="Отправить" >
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
<script>
	window.feedbackCredit = new JSCredit(<?=json_encode($jsParams)?>);
</script>