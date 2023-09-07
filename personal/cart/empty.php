<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Моя корзина");?>


<div class="goods">
	<div class="container goods__container">
		
		
		<div class="goods__breadcrumbs">
			<ul id="breadcrumbs_5c66abe488c8e" class="breadcrumbs">
				<li class="item">
					<a href="/">Главная</a>
				</li>
			</ul>

			<div class="breadcrumbs__need-help">
				<a href="#">Нужна помощь в выборе душевой кабины?</a>
			</div>
		</div>
		
		
		<div class="goods__wrapper">
			<div class="goods__card">

				<div class="search-page">
					<form action="" method="get">
						<input type="text" name="q" value="gfngh" size="40">
						&nbsp;<input type="submit" value="Искать">
						<input type="hidden" name="how" value="r">
					</form><br>
				</div>
				
				<div class="b-empty">
					<h2 class="b-empty-title b-title--h1">Ваша корзина пуста.</h2>
					<div class="b-empty-result__icon"></div>
					<div class="b-empty__text"> Индивидуальный подбор товара по телефону (бесплатно по России):</div>
					<div class="b-empty__phone">8 (800) 777-08-96</div>
				</div>
				
				<style>
				
					.goods__card {
						width: 100%;
					}
					
				</style>


			</div>

		</div>

	</div>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>