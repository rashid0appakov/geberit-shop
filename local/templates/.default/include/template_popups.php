<div id="popupStatusZakazaForm" class="popup69 popupOrder popupCommon popupStatusZakaza" style="display: none">
	<div class="close69">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png">
		</div>
	</div>
	<div class="popupTitle"><?=GetMessage('HDR_ORDER_STATUS')?>
		<div class="myClassShow showOnSubmit hide thisInline-block">
			<span id="<?=$orderIdNum?>"></span>
		</div>
	</div>
	<div class="hideOnSubmit">
		<p class="popupDescription"><?=GetMessage('HDR_ORDER_STATUS_TEXT')?></p>
	</div>
	<div class="tabs__content js-tabs-content active" id="tab-callback-1">
		<?$APPLICATION->IncludeComponent(
			"bxcert:empty",
			"feedback_leave",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"IBLOCK_ID" => 34,
			)
		);?>
	</div>
</div>
<?$enableName = true;//$GLOBALS['USER']->IsAdmin();?>
<div class="popup69 popupCommon popupOneClick" style="display: none">
	<div class="close69">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" alt="" />
		</div>
	</div>
	<div class="popupTitle"><?=GetMessage('HDR_BUY_ONE_CLICK')?></div>
	<div class="hideOnSubmit">
		<p class="popupDescription">Чтобы оформить заказ введите <?=$enableName?'ваше имя и ':''?>номер телефона</p>
	</div><br/>

	<form action="" id="formOneClick">
		<span class="alert"></span>
		<input type="hidden" name="item_id" value="" />
		<input type="hidden" name="iblock_id" value="<?=CATALOG_IBLOCK_ID?>" />
        <?if(!$enableName){?>
            <input type="hidden" name="name" value="<?=GetMessage('HDR_BUY_ONE_CLICK')?>" />
        <?}?>
		<input type="hidden" name="params" value="<?=CClass::getParamsString(["REQUIRED" => $enableName?["EMAIL","NAME"]:"EMAIL"])?>" />
        <input type="hidden" name="email" value="<?=($GLOBALS['USER']->isAuthorized() ? $USER->GetEmail() : CClass::getOneClickEmail())?>" />

		<div class="hideOnSubmit">
			<div class="columns">
                <?if($enableName){?>
                    <div class="column is-6">     
                        <div class="inputName">
                            <label for="name_id">Ваше имя *</label>
                            <input id="name_id" type="text" name="name" placeholder="Введите имя" required style="display:block!important;"/>
                        </div>
                    </div>
                <?}?>
				<div class="column is-6">     
					<div class="inputTel">
						<label for="inputTel1">Ваш телефон *</label>
						<input id="inputTel1" type="tel" name="inputTel" placeholder="+7 (" required style="display:block!important;"/>
						<label class="hide" for="inputTel1"></label>
					</div>
				</div>
			</div>
		</div>
		<div class="showOnSubmit hide OrderInfo"></div>
		<div class="popupFooter">
			<div class="columns">
				<div class="column is-5">
					<div class="hideOnSubmit">
					<div class="submit">
						<button class="callBackLink" type="submit">Оформить</button>
					</div>
					</div>
					<div class="showOnSubmit hide">
						<button class="callBackLink" type="submit">Оформить</button>
					</div>
				</div>
				<div class="column is-6">
					<div class="hideOnSubmit">
					<p class="iAgree">Отправляя данные Вы соглашаетесь с <br>
						<a href="#">Пользовательским соглашением</a>
					</p>
					</div>
					<div class="showOnSubmit hide">
						<a href="#" class="close96 closeLinkPopup">Закрыть окно</a>
					</div>
				</div>
				<div class="column is-1">
					<div class="questionImg">
						<img src="<?=SITE_DEFAULT_PATH?>/images/icon_question.png" alt="" />
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- POPUPS -->
<?/*
<input type="radio" id="open-modal-region" name="popup-change-region" class="system">
<div class="modal">
	<div class="modal-background"></div>
	<div class="modal-content">
		<h4 class="is-size-3">Выбор региона</h4>
		<div class="is-size-4">Выберите город из списка или введите для поиска</div>
		<p class="small">Доставка заказа до транспортной компании бесплатная от 25 000 рублей. Стоимость доставки заказа
		транспортной компании и время доставки в ваш город указаны на странице товара после выбора города. Вы можете
		вернуть товар в течение 7 дней без объяснения причины</p>
		<div class="field search">
			<p class="control has-icons-right">
				<input class="input" type="text" placeholder="Начинайте вводить название города для поиска" />
			</p>
		</div>
		<div class="cities">
			<span class="city mark">
				<a href="#">Москва</a>
				<img src="<?=SITE_DEFAULT_PATH?>/images/icons/box-mini.png" alt="" />
				<img src="<?=SITE_DEFAULT_PATH?>/images/icons/auto-mini.png" alt="" />
			</span>
			<span class="city"><a href="#">Воронеж</a></span>
			<span class="city"><a href="#">Екатеринбург</a></span>
			<span class="city mark">
				<a href="#">Санкт-петербург</a>
				<img src="<?=SITE_DEFAULT_PATH?>/images/icons/box-mini.png">
				<img src="<?=SITE_DEFAULT_PATH?>/images/icons/auto-mini.png">
			</span>
			<span class="city"><a href="#">Казань</a></span>
			<span class="city"><a href="#">Краснодар</a></span>
			<span class="city"><a href="#">Красноярск</a></span>
			<span class="city"><a href="#">Нижний Новгород</a></span>
			<span class="city"><a href="#">Пермь</a></span>
			<span class="city"><a href="#">Самара</a></span>
			<span class="city"><a href="#">Челябинск</a></span>
			<span class="city"><a href="#">Череповец</a></span>
		</div>
		<p><a href="#">Прочитайте нашу инструкцию</a> по оформление заказов с доставкой транспортной компанией</p>
		<div class="info">
			<span class="small">В этом городе есть склад продукции</span>
			<span class="small">В этих городах мы доставляем своими машинами</span>
		</div>
		<label class="close" for="close-modal-region"></label>
		<input type="radio" id="close-modal-region" name="popup-change-region" class="system">
	</div>
	<div class="modal-content is-mobile">
		<div class="header container">
			<div class="search field">
				<div class="back">Выбор региона</div>
			</div>
			<label for="close-modal-region">
				<div class="close-icon">
					<svg viewBox="0 0 20 18" width="20" height="18" xmlns="http://www.w3.org/2000/svg">
						<line x1="1" y1="0" x2="19" y2="18" stroke="black" stroke-width="2" />
						<line x1="1" y1="18" x2="19" y2="0" stroke="black" stroke-width="2" />
					</svg>
				</div>
			</label>
		</div>
		<div class="info container section ">
			<h4 class="is-size-4">Выберите город из списка или введите для поиска</h4>
			<p class="small">Доставка заказа до транспортной компании бесплатная от 25 000 рублей. Стоимость доставки заказа транспортной компании и время доставки в ваш город указаны на странице товара после выбора города. Вы можете вернуть товар в течение 7 дней без объяснения причины</p>
			<div class="search field">
				<input class="input" type="text" placeholder="Найти город">
			</div>
		</div>

		<div class="subcategories">
			<div class="section">
				<a href="#" class="is-size-4 red">Москва
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/box-mini.png" alt="" />
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/auto-mini.png" alt="" />
				</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4 red">Санкт-петербург
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/box-mini.png" alt="" />
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/auto-mini.png" alt="" />
				</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Екатеринбург</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Воронеж</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Казань</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Краснодар</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Красноярск</a>
			</div>
			<div class="section">
				<a href="#" class="is-size-4">Нижний Новгород</a>
			</div>
			<div class="section">
				<p><a href="#">Прочитайте нашу инструкцию</a> по оформление заказов с доставкой транспортной компанией</p>
				<div class="legend">
					<span class="small">В этом городе есть склад продукции</span>
					<span class="small">В этих городах мы доставляем своими машинами</span>
				</div>
			</div>
		</div>
	</div>
</div>
*/?>
<div id="popupAddCart" class="popup69 popupAddCart" style="display: none;">
	<div class="close69">
		<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" />
	</div>
	<div class="columns info"></div>
	<div class="goodsSupply">
		<div class="goodsSupplyTitle">
			<span><?=GetMessage('HDR_BASKET_KOMPLECT')?></span>
		</div>
		<div id="itemSet"></div>
	</div>
</div>

<div class="popup69 popupCommon popupCallback" style="display: none" id="popupFeedback">
	<div class="close69">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" />
		</div>
	</div>
	<div class="popupTitle">Обратная связь</div>
    <?/*/?>
	<div class="hideOnSubmit">
		<section class="tabs">
			<ul class="tabs__header" id="tabs__header-1">
				<li class="tabs__header--title js-tabs-title active" data-tab="#tab-callback-5">Отправить заявку</li>
				<li class="tabs__header--title js-tabs-title" data-tab="#tab-callback-2">Поблагодарить</li>
				<li class="tabs__header--title js-tabs-title" data-tab="#tab-callback-3">Пожаловаться</li>
				<li class="tabs__header--title js-tabs-title" data-tab="#tab-callback-4">Помогите подобрать</li>
			</ul>
			<div class="tabs__underline js-tabs-underline"></div>
		</section>
	</div><?/**/?>
	<div class="tabs__content js-tabs-content active" id="tab-callback-5">
		<?$APPLICATION->IncludeComponent(
			"bxcert:empty",
			"feedback_other",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"IBLOCK_ID" => 38,
			)
		);?>
	</div>
    <?/*/?>
	<div class="tabs__content js-tabs-content" id="tab-callback-2">
		<?$APPLICATION->IncludeComponent(
			"bxcert:empty",
			"feedback_thanks",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"IBLOCK_ID" => 35,
			)
		);?>
	</div>
	<div class="tabs__content js-tabs-content" id="tab-callback-3">
		<?$APPLICATION->IncludeComponent(
			"bxcert:empty",
			"feedback_complaint",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"IBLOCK_ID" => 36,
			)
		);?>
	</div>
	<div class="tabs__content js-tabs-content" id="tab-callback-4">
		<?$APPLICATION->IncludeComponent(
			"bxcert:empty",
			"feedback_help_pickup",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"IBLOCK_ID" => 37,
			)
		);?>
	</div><?/**/?>
</div>
<div class="okSendLine">
	<p>Ваше сообщение получено. Менеджер магазина свяжется с вами <?=$arContact['SCHEDULE']?></p>
	<div class="closeOK">
		<div>
			<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" />
		</div>
	</div>
</div>

<div id="popupSearchError" class="popup69 popupCommon popupCallback" style="display: none;">
	<div class="close69">
		<img src="<?=SITE_DEFAULT_PATH?>/images/close.png" />
	</div>
	<div class="popupTitle">Сообщение об ошибку</div>
	<div class="hideOnSubmit">
		<p class="popupDescription">Пожалуйста, опишите ошибку на текущей странице и мы ее обязательно исправим!</p>
	</div><br/>
	<?$APPLICATION->IncludeComponent(
		"bxcert:empty",
		"search_error",
		array(
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"IBLOCK_ID" => 98,
		)
	);?>
</div>