<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бесплатное хранение вашего заказа на весь период карантина");?>
<div class="goods">
	<div class="container goods__container">
		 <!-- page breadcrumbs -->
		 <div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "main",
                Array(
                    "PATH"      => "",
                    "SITE_ID"   => SITE_ID,
                    "START_FROM"=> "0"
                )
            );?>
		</div>
		 <div  class="promo hero">
			<div class="container">
				<div  class="owl-theme">
					
						<div class="owl-carousel__item">
							
							
							<img src="<?=SITE_DEFAULT_PATH.'/images/1ban.jpg'?>" alt="" />
							
						</div>
					
				</div>
			</div>
		</div>
		
		<div class="goods__card">
		<h1 class="goods__title-title">Купите сейчас - забирайте потом!</h1>
		<h2 class="payment-block__item-title">Бесплатное хранение вашего заказа на весь период карантина</h2>

<style>
.about-block__text{line-height: 1.5;
    margin-left: 20px;
    margin-top: 20px;}
</style>
		<div class="about-block__text">
		1. Выберите товары на сайте и добавьте в заказ<br>
		2. Оплатите покупку любым удобным способом<br>
		3. Мы переместим ваш заказ на склад хранения для клиентов (адрес склада смотрите на странице <a href="/contacts/">контактов</a>)<br>
		4. Заберите заказ самостоятельно в любой удобный день и время до 31 мая 2020 года или закажите доставку<br>
		5. Вы можете забрать весь заказ или вывезти в несколько этапов (например, вы можете оформить заказ на весь комплект сантехники для квартиры или дома, а забирать его по мере выполнения ремонтных работ)<br>
		</div>

		<h3 class="payment-block__item-title">Ваши преимущества:</h3>

		<div class="about-block__text">
		1. Экономия денежных средств на курсе евро<br>
		2. Сохранность товаров на время ремонта<br>
		3. Безопасность для вашего здоровья и здоровья ваших близких на период карантина<br>
		</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>