<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Информация о официальном интернет-магазине сантехники Geberit Shop");
$APPLICATION->SetPageProperty("keywords", "Geberit Shop");
$APPLICATION->SetPageProperty("title", "О компании Geberit Shop");
$APPLICATION->SetTitle("О компании");?>
<div class="goods">
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
				<!-- page title -->
				<div class="goods__title">
					<h1 class="goods__title-title"><?$APPLICATION->ShowTitle(FALSE)?></h1>
				</div>
				<div class="about-block">
					<h3 class="about-block__title">О бренде GEBERIT</h3>
					<div class="about-block__text">
	<p><a href="/" title="Официальный сайт Геберит">Geberit</a> — транснациональная компания, представительства которой действуют в 41 стране. Несколько направлений производства: инженерные сантехнические системы (инсталляции для подвесной сантехники, решения для душевых зон без поддона, смесители и системы смыва, унитазы и крышки с функцией биде), трубопроводные системы (системы внутренней и наружной канализации, системы водоснабжения, отопления и т.д.) и керамика и мебель для ванных комнат.</p> <?/*
<p>Основана в 1874 году Каспаром Мельхиором Гебертом в Рапперсвилле, Швейцария. С самого открытия компания производила санитарно-технические системы и оборудование.</p>
					</div>
					<div class="about-block__history">
						<ul>
							<li><span>1936 -</span><span> Фридрих Гроэ приобретает фабрику изделий из металла Berkenhoff&Paschedag в немецком городе Хемер и перенаправляет все производственные мощности предприятия на выпуск сантехники
</span></li>
							<li><span>1948 -</span><span>С расцветом послевоенного строительства компания получает мощный скачок в развитии. Название фабрики меняется на Friedrich GROHE Armaturenfabrik
</span></li>
							<li><span>1956 -</span><span>GROHE приобретает компанию-производителя термостатов Carl Nestler, которая в дальнейшем будет ее дочерним предприятием GROHE Thermostat GmbH
</span></li>
							<li><span>1961 -</span><span>GROHE выходит на международный уровень — открывается первый завод за границей — во Франции. Через год французская фабрика получает эксклюзивные права на выпуск однорычажных смесителей
</span></li>
							<li><span>1965 -</span><span>Открывается второе дочернее предприятие в Австрии</span></li>
							<li><span>1967 -</span><span> Начинает работать третий завод GROHE за границей — в Италии
</span></li>
							<li><span>1968 -</span><span>Чтобы получить средства для расширения производства, Фридрих Гроэ продает 51% акций американской компании International Telephone & Telegraph (ITT)
</span></li>
							<li><span>1970-е -</span><span>Открываются дочерние предприятия в США, Великобритании, Голландии и Бельгии
</span></li>
							<li><span>1982 -</span><span>Запускается производство на новом заводе в г. Хемер-Эдельбург</span></li>
							<li><span>1983 -</span><span>Умирает основатель GROHE Фридрих Гроэ, через год его наследники выкупают у ITT мажоритарный пакет
</span></li>
							<li><span>1989 -</span><span> Открывается специализированный завод по производству красок и новое дочернее предприятие в Канаде
</span></li>
							<li><span>1990-е -</span><span>GROHE становится открытой акционерной компанией Friedrich Grohe AG. Это событие стало новым толчком в развитии компании
</span></li>
							<li><span>1993 -</span><span>GROHE приобрела 50% акций компании GROME Marketing Cyprus Ltd
</span></li>
							<li><span>1994 -</span><span>Слияние GROHE с группой Dal/Rost</span></li>
							<li><span>1995  -</span><span>Открытие филиала в Польше, а через год еще двух новых фабрик — в Таиланде и Португалии
</span></li>
							<li><span>1997 -</span><span>Запуск нового Конструкторского центра GROHE</span></li>
						</ul>
					</div>
					<div class="about-block__text">
						В 2004 году компанию GROHE купили TPG Partners IV, L.P и DLJ Merchant Banking и до сих пор остаются ее акционерами.
					</div>
					<div class="about-block__text">
						Основные производственные мощности Geberit находятся в Швейцарии, Германии и Австрии.
					</div>
					<div class="about-block__text">
Компания финансирует исследования в разных областях технологий: гидравлика, гигиена питьевой воды, материаловедение (прежде всего пластика), моделирование, электроника, противопожарная защита и других. Geberit открывает свои учебно-образовательные центры для монтажников, сантехников, инженеров-проектировщиков и архитекторов.

					</div>
					*/ ?>
				</div> 
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"showroom_slider",
					array(
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"ADD_SECTIONS_CHAIN" => "N",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"CACHE_FILTER" => "N",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"COMPOSITE_FRAME_MODE" => "A",
						"COMPOSITE_FRAME_TYPE" => "AUTO",
						"DETAIL_URL" => "",
						"DISPLAY_BOTTOM_PAGER" => "N",
						"DISPLAY_DATE" => "N",
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"FIELD_CODE" => array(
							0 => "NAME",
							1 => "PREVIEW_TEXT",
							2 => "PREVIEW_PICTURE",
							3 => "DETAIL_TEXT",
							4 => "DETAIL_PICTURE",
							5 => "",
						),
						"FILTER_NAME" => "",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"IBLOCK_ID" => "45",
						"IBLOCK_TYPE" => "-",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"INCLUDE_SUBSECTIONS" => "N",
						"MESSAGE_404" => "",
						"NEWS_COUNT" => "999",
						"PAGER_BASE_LINK_ENABLE" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_TEMPLATE" => ".default",
						"PAGER_TITLE" => "Новости",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"PREVIEW_TRUNCATE_LEN" => "",
						"PROPERTY_CODE" => array(
							0 => "",
							1 => "",
						),
						"SET_BROWSER_TITLE" => "N",
						"SET_LAST_MODIFIED" => "N",
						"SET_META_DESCRIPTION" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_STATUS_404" => "N",
						"SET_TITLE" => "N",
						"SHOW_404" => "N",
						"SORT_BY1" => "ACTIVE_FROM",
						"SORT_BY2" => "SORT",
						"SORT_ORDER1" => "DESC",
						"SORT_ORDER2" => "ASC",
						"STRICT_SECTION_CHECK" => "N",
						"COMPONENT_TEMPLATE" => "showroom_slider"
					),
					false
				);?>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>