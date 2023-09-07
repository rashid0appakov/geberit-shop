<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оптовые продажи");
?><div class="optovieProdagi goods">
	<div class="container goods__container">
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
		<div class="goods__wrapper">
			<div class="goods__sidebar">
      			<?$APPLICATION->IncludeComponent(
					"bitrix:menu",
					"sidebar",
					Array(
						"ALLOW_MULTI_SELECT" => "N",
						"CHILD_MENU_TYPE" => "sidebar",
						"DELAY" => "N",
						"MAX_LEVEL" => "2",
						"MENU_CACHE_GET_VARS" => array(""),
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"ROOT_MENU_TYPE" => "sidebar",
						"USE_EXT" => "Y"
					)
				);?>
			</div>
			<div class="goods__card">
				<div class="goods__title">
					<h1 class="goods__title-title">Эксперты в проектных поставках</h1>
				</div>
				<p>На сегодняшний день компания ООО "СЕТЬ ЭКСПЕРТНЫХ МАГАЗИНОВ САНТЕХНИКИ" имеет репутацию надежного и компетентного поставщика оборудования по сантехнике, отоплению и водоотведению.</p>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_1.jpg" alt="" style="float: right;margin-top: -15px;">
				<p>Нам доверяют многие наши клиенты, мы осуществляем подбор продукции по Вашему проекту и можем предложить лучшие цены от ведущих производителей Испании, Италии, Финляндии, Германии, Франции, Дании, Швеции, США, Китая, России.</p>
<p>По вопросам приобретения продукции оптом обращайтесь к нашему менеджеру: +7 (904) 035-68-78, ribalchenko@expert-santehniki.ru, Рыбальченко Владимир</p>
				<br>
				<p style="margin-bottom: 22px;"><b>Мы работаем с такими фабриками как:</b></p>
				<p>Hansgrohe, Roca, Geberit, Jacob Delafon, Kaldewei, Ravak, Laufen, Viega, Jika фаянс, Oras, Aquatek, Santek, Ideal Standart, Duravit, Aquaton, Gustavberg, Hueppe, Keramag, Sanita, Nobili, Keuco, Dyson, Gala, Kermi, Kolo, Sanit.</p>
				<div class="content__1">
					<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_2.jpg" alt="">
					Производители, специализирующиеся на специальной продукции для проектов: DELABIE, Sanela, Moeff, Oceanus, Тругор.
				</div>
				<div class="content__2">
					<div>Что делает нас экспертами в проектных поставках:</div>
					<ul>
						<li><img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_3.jpg" alt="">Мы имеем опыт более 10 лет поставок на проекты разной сложности</li>
						<li><img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_4.jpg" alt="">Имеем прямые договора поставок с самыми известными производителями сантехнических изделий – гарантия низкой цены и минимальных сроков</li>
						<li><img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_5.jpg" alt="">Глубокое знание пользовательского опыта по использованию того или иного бренда сантехники</li>
						<li><img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_6.jpg" alt="">Умение предложить максимально качественное изделие, когда заказчик просит минимальную цену</li>
					</ul>
				</div>
				<div class="content__3">
					<div>Имеем в портфеле более<br />	 175 брендов сантехники со всего мира</div>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_7.jpg" alt="">
				</div>
				<div class="content__4">
					<div class="heading_c4">Ниже представлены некоторые объекты, на которые мы поставляли оборудование:</div>
					<div class="grid">
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_8.jpg" alt="">
							<p>Газпром Арена  г. Санкт-Петербург</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_9.jpg" alt="">
							<p>Ледовый дворец «Кристалл» в Лужниках г. Москва</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_10.jpg" alt="">
							<p>Бизнес-центр «Два капитана» г. Красногорск</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_11.jpg" alt="">
							<p>Китайский деловой центр "Парк Хуамин" г.Москва</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_12.jpg" alt="">
							<p>Клиническая больница МЕДСИ в Отрадном </p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_13.jpg" alt="">
							<p>Европейский медицинский центр ЕМС, г. Москва</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_14.jpg" alt="">
							<p>Горно-туристический центр «Газпром» г. Сочи</p>
						</div>
						<div class="item">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_15.jpg" alt="">
							<p>Реконструкция «Северный речной вокзал» г. Москва</p>
						</div>
					</div>
				</div>
				<div class="content__5">
					<div class="heading_c5">Товарные категории специальной сантехники:</div>
					<div class="grid">
						<div class="item">
							<ul>
								<li>—   Медицинские смесители</li>
								<li>—   Инсталляция для людей с ограниченными возможностями</li>
								<li>—   Раковина для людей с ограниченными возможностями</li>
								<li>—   Полки и сиденья для людей с ограниченными возможностями</li>
								<li>—   Унитазы для людей с ограниченными возможностями</li>
								<li>—   Поручни для людей с с ограниченными возможностями</li>
								<li>—   Антивандальные унитазы</li>
								<li>—   Антивандальные раковины</li>
								<li>—   Антивандальные писсуары</li>
								<li>—   Антивандальные клавиши для инсталляций</li>
								<li>—   Антивандальные клавиши для писсуаров</li>
								<li>—   Антивандальная раковина</li>
							</ul>
						</div>
						<div class="item">
							<ul>
								<li>—   Антивандальные смесители</li>
								<li>—   Антивандальные поддоны</li>
								<li>—   Антивандальные аксессуары</li>
								<li>—   Питьевые фонтанчики</li>
								<li>—   Антивандальная мебель</li>
								<li>—   Антивандальные перегородки</li>
								<li>—   Сушилки для рук</li>
								<li>—   Антивандальные столешницы</li>
								<li>—   Антивандальные трапы</li>
								<li>—   Антивандальные душевые системы</li>
								<li>—   Антивандальные зеркала</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="content__6">
					<div class="heading_c6">Категории объектов с которыми мы работаем: </div>
					<div class="grid">
						<div class="item">
							<div>Спортивные объекты</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_16.jpg" alt="">
						</div>
						<div class="item">
							<div>Гостиницы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_17.jpg" alt="">
						</div>
						<div class="item">
							<div>Гражданские объекты, жилые комплексы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_18.jpg" alt="">
						</div>
						<div class="item">
							<div>Торгово - развлекательные комплексы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_19.jpg" alt="">
						</div>
						<div class="item">
							<div>Промышленные объекты, заводы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_20.jpg" alt="">
						</div>
						<div class="item">
							<div>Таможенные терминалы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_21.jpg" alt="">
						</div>
						<div class="item">
							<div>Складские комплексы, логопарки</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_22.jpg" alt="">
						</div>
						<div class="item">
							<div>Передвижные составы, передвижные комплексы</div>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/content_img_23.jpg" alt="">
						</div>
					</div>
				</div>
				<div class="content__7">
					<h2>Контактная информация</h2>
					<div class="grid">
						<div class="item">
							<div class="img">
								<img src="<?=SITE_TEMPLATE_PATH?>/upload/medialibrary/cba/1255.png" alt="">
							</div>
							<div>
						  	<span>Рыбальченко Владимир</span><br>
						  	<b>Ведущий специалист</b>
							</div>
						</div>
						<div class="item mailto">
							E-mail:<br>
							<a href="mailto:ribalchenko@expert-santehniki.ru">ribalchenko@expert-santehniki.ru</a>
						</div>
						<div class="item schedule">
							Телефон: <br>
							<b>+7(904)035-68-78</b>
						</div>
					</div>				
				</div>
			</div>
		</div>		
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>