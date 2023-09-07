<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

$promoCode = 'installment_0_0_3_5,19';
?>

<div class="popup69 popupCommon popupOneClick" style="position: static;">
	<div class="popupTitle">Купить в рассрочку</div>
	<div class="hideOnSubmit">
		<p class="popupDescription"></p>
	</div><br/>
	<form action='https://forma.tinkoff.ru/api/partners/v1/lightweight/create' method='POST' class='rasrochka_form_tag' target="_blank" onsubmit='validRasrochka(event)'>
		<input name='shopId' value='9b65cf09-dce4-4b1c-a829-09328bdab611' type='hidden'/>
		<input name='showcaseId' value='21c94455-8afc-4fb6-963a-f4a0d49e479f' type='hidden'/>
		<input name='promoCode' value='<?=$promoCode?>' type='hidden'/>
		
		<?
		$sum = 0;
		if($_GET['products']){
			foreach($_GET['products'] as $i => $product){
				?>
				<input name='itemQuantity_<?=$i?>' value='<?=$product['quantity']?>' type='hidden'/>
				<input name='itemProductId_<?=$i?>' value='<?=$product['id']?>' type='hidden'/>
				<input name='itemName_<?=$i?>' value='<?=$product['name']?>' type='hidden'/>
				<input name='itemPrice_<?=$i?>' value='<?=$product['price']?>' type='hidden'/>
				<?
				$sum += $product['price'] * $product['quantity'];
			}
		}
		?>

		<input name="sum" value="<?=$sum?>" type="hidden">
		
		<input name="mode" value="<?=$_GET['mode']?>" type="hidden">
		
		
		<div class="hideOnSubmit">
			<div class="columns">
				<div class="column is-6">
					<div class="inputName">
						<label for="mail_id">Почта*</label>
						<input id="mail_id" name="customerEmail" type="email" placeholder="pochta@example.ru" required>
					</div>
				</div>
				<div class="column is-6">
					<div class="inputTel">
						<label for="phone_id">Телефон*</label>
						<input id="phone_id" name="customerPhone" type="tel" placeholder="7(" required class="mobMarginNull">
					</div>
				</div>
			</div>
		</div>
		
		<div class="showOnSubmit" style="display:none;">
			<p class="formOkText">
				Заполните форму на странице банка
			</p>
		</div>
		
		<div class="popupFooter" style="margin:0px -70px -70px;">
			<div class="columns">
				<div class="column is-5">
					<div class="hideOnSubmit">
						<div class="submit" style="overflow: hidden;">
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
				</div>
				<div class="column is-1">
					<div class="hideOnSubmit">
						<div class="questionImg">
							<img src="/local/templates/.default/components/bxcert/empty/search_error/images/sendMail.png">
						</div>
					</div>
					<div class="showOnSubmit"  style="display:none;">
						<div class="questionImg">
							<img src="/local/templates/.default/components/bxcert/empty/search_error/images/ok_sen_mail.png">
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>