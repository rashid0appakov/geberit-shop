<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вы смотрели");
?>
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
		<div class="goods__title">
			<h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
		</div>
		<div class="goods__wrapper page_product_viewed">
			<?$APPLICATION->IncludeComponent("bitrix:catalog.viewed.products", "page", Array(
					"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
					"HIDE_NOT_AVAILABLE" => "N",	// Не отображать товары, которых нет на складах
					"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,	// Тип инфоблока
					"IBLOCK_ID" => CATALOG_IBLOCK_ID,	// Инфоблок
					"PRICE_CODE" => array("BASE"),
					"SHOW_PRICE_COUNT" => "1",
					"PRICE_VAT_INCLUDE" => "N",	// Включать НДС в цену
					"SHOW_OLD_PRICE" => "N",
					"SHOW_IMAGE" => "Y",	// Показывать изображение
					"SHOW_PRODUCTS_".CATALOG_IBLOCK_ID => "Y",	// Показывать товары каталога
					"PAGE_ELEMENT_COUNT" => 20,
				),
				false
			);?>
		</div>
	</div>
</div>
<?
$APPLICATION->AddChainItem("Вы смотрели", "/poduct_viewed/");
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>