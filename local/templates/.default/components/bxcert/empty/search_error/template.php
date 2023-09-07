<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = uniqid();

$params = base64_encode(serialize($arParams));
$formId = "form_$uniqueId";
$textInputId = "text_$uniqueId";
$selectInputId = "select_$uniqueId";
$pageInputId = "page_$uniqueId";
$timeSpanId = "time_$uniqueId";

$jsParams = array(
	"params" => $params,
	"form_id" => $formId,
	"text_input_id" => $textInputId,
	"select_input_id" => $selectInputId,
	"page_input_id" => $pageInputId,
	"time_span_id" => $timeSpanId,
	"ajax_url" => "/ajax/form/search_error.php",
);?>
<form id="<?=$formId?>">
	<div class="hideOnSubmit">
		<div class="columns">
			<div class="column">
				<label for="<?=$selectInputId?>">Тип ошибки*</label>
				<div class="sel" style="margin: 40px 0 0px;">
					<select name="selectField" id="<?=$selectInputId?>" required>
						<option value="">Выберите</option>
						<option value="Цена">Цена</option>
						<option value="Наличие">Наличие</option>
						<option value="Фото">Фото</option>
						<option value="Характеристики">Характеристики</option>
						<option value="Комплектующие">Комплектующие</option>
						<option value="Описание">Описание</option>
						<option value="Другое">Другое</option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="columns">
			<div class="column">
				<div class="inputText">
					<label for="<?=$textInputId?>">Ваше сообщение*</label>
					<textarea name="textField" id="<?=$textInputId?>" cols="30" rows="10" required></textarea>
				</div>
			</div>
		</div>
		
		<input type="hidden" name="curent_page" id="<?=$pageInputId?>" value="<?='https://'.SITE_SERVER_NAME.$APPLICATION->GetCurPage()?>">
	</div>
	
	<div class="showOnSubmit hide">
		<p class="formOkText">
			Спасибо! Ваше сообщение отправлено.
			В ближайшее время с вами свяжутся наши менеджеры для уточнения деталей.
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
</form>
<script>
	window.feedbackSearchError = new JSSearchError(<?=json_encode($jsParams)?>);
</script>