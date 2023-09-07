<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	/** @var array $arParams */
	/** @var array $arResult */
	/** @global CMain $APPLICATION */
	/** @global CUser $USER */
	/** @global CDatabase $DB */
	/** @var CBitrixComponentTemplate $this */
	/** @var string $templateName */
	/** @var string $templateFile */
	/** @var string $templateFolder */
	/** @var string $componentPath */
	/** @var CBitrixComponent $component */
	$is_success = FALSE;
	if (empty($arResult['ERRORS']) && $arResult['MESSAGE']){
		$is_success = TRUE;?>
	<script type="text/javascript">
		$(document).ready(function(){
			setTimeout(function(){
				SC.closePopup();
			}, 2500);
		});
	</script>
	<?}?>
	<div class="form-success<?=(!$is_success ? ' hide' : '')?>"><?=GetMessage('FORM_SUCCESS_MESSAGE')?></div>
	<div class="form-wrapper<?=($is_success ? ' hide' : '')?>">
		<div class="popupTitle"><?=GetMessage('FORM_TITLE')?></div>
		<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" id="feedback-form">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="PROPERTY[BX][0]" class="form__field" value="" />
			<input type="hidden" name="method" value="get-form" />
			<input type="hidden" name="name" value="<?=$arParams['FORM_NAME']?>" />
			<div class="columns">
				<div class="column is-12">
					<label for="feedback_name"><?=GetMessage("FIELD_NAME")?></label>
					<input type="text" name="PROPERTY[NAME][0]" id="feedback_name" class="form__field<?=($arResult['ERROR'][$arParams["CUSTOM_TITLE_NAME"]] ? " error" : "")?>" placeholder="<?=GetMessage("FIELD_NAME_HOLDER")?>" value="<?=$arResult['ELEMENT']['NAME']?>" />
					<? if ($arResult['ERROR'][$arParams["CUSTOM_TITLE_NAME"]]):?>
					<label class="error" for="feedback_name"><?=GetMessage('FORM_ERROR_TEXT')?></label>
					<? endif;?>
				</div>
			</div>
			<div class="columns">
				<div class="column is-6">
					<label for="feedback_phone"><?=GetMessage("FIELD_PHONE")?></label>
					<input type="text" name="PROPERTY[3513][0]" id="feedback_phone" class="form__field phone__field<?=($arResult['ERROR'][$arResult['PROPERTY_LIST_FULL'][3513]['NAME']] ? " error" : "")?>" placeholder="<?=GetMessage("FIELD_PHONE_HOLDER")?>" value="<?=$arResult['ELEMENT_PROPERTIES'][3513][0]['VALUE']?>" />
					<? if ($arResult['ERROR'][$arResult['PROPERTY_LIST_FULL'][3513]['NAME']]):?>
					<label class="error" for="feedback_phone"><?=GetMessage('FORM_ERROR_TEXT')?></label>
					<? endif;?>
				</div>
				<div class="column is-6">
					<label for="feedback_email"><?=GetMessage("FIELD_PHONE")?></label>
					<input type="text" name="PROPERTY[CODE][0]" id="feedback_email" class="form__field<?=($arResult['ERROR'][$arParams["CUSTOM_TITLE_CODE"]] ? " error" : "")?>" placeholder="<?=GetMessage("FIELD_EMAIL_HOLDER")?>" value="<?=$arResult['ELEMENT']['CODE']?>" />
					<? if ($arResult['ERROR'][$arParams["CUSTOM_TITLE_CODE"]]):?>
					<label class="error" for="feedback_email"><?=GetMessage('FORM_ERROR_TEXT')?></label>
					<? endif;?>
				</div>
			</div>
			<div class="columns">
				<div class="column is-12">
					<label for="feedback_text"><?=GetMessage("FIELD_TEXT")?></label>
					<textarea name="PROPERTY[PREVIEW_TEXT][0]" id="feedback_text" class="form__textarea<?=($arResult['ERROR'][$arParams["CUSTOM_TITLE_PREVIEW_TEXT"]] ? " error" : "")?>" placeholder="<?=GetMessage("FIELD_TEXT_HOLDER")?>"><?=$arResult['ELEMENT']['PREVIEW_TEXT']?></textarea>
					<? if ($arResult['ERROR'][$arParams["CUSTOM_TITLE_PREVIEW_TEXT"]]):?>
					<label class="error" for="feedback_text"><?=GetMessage('FORM_ERROR_TEXT')?></label>
					<? endif;?>
				</div>
			</div>
			<?php
			/*	$isCheked   = TRUE;
				if (!isset($_POST['agreement']) && isset($_POST['iblock_submit']))
					$isCheked   = FALSE;
			?>
			<div class="form-check<?=($arResult['ERROR']['agreement'] ? " invalid" : "")?>">
				<input type="checkbox" class="form-check-input" value="Y" name="agreement" id="agreement3" <?=($isCheked ? ' checked="checked"' : "")?> />
				<label for="agreement3" class="form-check__checkbox"></label>
				<label for="agreement3" class="form-check-label">
					<?=GetMessage('USER_AGREEMENT',["#LINK#" => SITE_DIR.'user-agreement/'])?>
				</label>
			</div>
			<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
				<label><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></label>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
				<input type="text" name="captcha_word" maxlength="50" value="" />
			<?endif */?>
			<div class="popupFooter">
				<div class="columns">
					<div class="column is-5">
						<div class="submit">
							<input type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
						</div>
					</div>
					<div class="column is-6">
						<p class="iAgree"><?=GetMessage('FORM_AGREEMENT_TEXT')?></p>
					</div>
					<div class="column is-1">
						<div class="questionImg">
							<img src="<?=SITE_DEFAULT_PATH?>/components/bxcert/empty/feedback_leave/images/sendMail.png" alt="" />
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>