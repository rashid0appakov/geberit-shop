<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();

$params = base64_encode(serialize($arParams));
$formId = "form_$uniqueId";
$phoneInputId = "phone_$uniqueId";
$orderInputId = "order_$uniqueId";
$statusInputId = "status_$uniqueId";
$sumOrderInputId = "sum_order_$uniqueId";
$paymentOrderInputId = "payment_order_$uniqueId";
$nameUserInputId = "name_user_$uniqueId";
$phoneUserInputId = "phone_user_$uniqueId";

$jsParams = array(
	"params" => $params,
	"form_id" => $formId,
	"phone_input_id" => $phoneInputId,
	"order_input_id" => $orderInputId,
	"status_input_id" => $statusInputId,
	"sum_order_input_id" => $sumOrderInputId,
	"payment_order_input_id" => $paymentOrderInputId,
	"name_user_input_id" => $nameUserInputId,
	"phone_user_input_id" => $phoneUserInputId,
	"ajax_url" => $templateFolder."/ajax.php",
);?>
<form id="<?=$formId?>">
	<div class="hideOnSubmit">
		<div class="columns">
			<div class="column is-6">
				<div class="inputName">
					<label for="<?=$orderInputId?>">Номер заказа*</label>
					<input id="<?=$orderInputId?>" name="inputName" type="text" placeholder="Введите номер заказа" required>
				</div>
			</div>
			<div class="column is-6">
				<div class="inputTel">
					<label for="<?=$phoneInputId?>">Ваш телефон*</label>
					<input id="<?=$phoneInputId?>" class="mobMarginNull" type="tel" placeholder="7(" required>
				</div>
			</div>
		</div>
	</div>
	<div class="showOnSubmit hide">
		<div class="columns">
			<div class="column is-6">
				<div class="columns">
					<div class="column">
						<span class="boldText">
							Статус:
						</span>
						<span class="boldText">
							Сумма к оплате:
						</span>
						<span class="boldText">
							Способ оплаты:
						</span>
					</div>
					<div class="column">
						<span id="<?=$statusInputId?>">
							
						</span>
						<span id="<?=$sumOrderInputId?>">
							
						</span>
						<span id="<?=$paymentOrderInputId?>">
							
						</span>
					</div>
				</div>
			</div>
			<div class="column is-6">
				<div class="columns">
					<div class="column">
						<span class="boldText">
							Ваш менеджер:
						</span>
						<span class="boldText">
							Телефон:
						</span>
					</div>
					<div class="column">
						<span id="<?=$nameUserInputId?>">
							
						</span>
						<span id="<?=$phoneUserInputId?>">
						   
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="popupFooter">
		<div class="columns">
			<div class="column is-5">
				<div class="hideOnSubmit">
					<div class="submit">
						<input type="submit" value="Узнать статус">
					</div>
				</div>
				<div class="showOnSubmit hide">
					<div class="showOnSubmit hide">
						<button class="close96 callBackLinkClose">Закрыть окно</button>
					</div>
				</div>
			</div>
			<div class="column is-6">
			<!--
				<div class="hideOnSubmit">
					<p class="iAgree">
						Отправляя данные Вы соглашаетесь с <br>
						<a href="/about/agreement/">Пользовательским соглашением</a>
					</p>
				</div>
				<div class="showOnSubmit hide">
					<a href="#" class="close96 closeLinkPopup">Закрыть окно</a>
				</div>
			-->
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
	window.feedbackLeave = new JSFeedbackLeave(<?=json_encode($jsParams)?>);
</script>
