<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$ORIGINAL_PARAMETERS = Array
(
	"IBLOCK_TYPE" => CATALOG_IBLOCK_TYPE,
	"IBLOCK_ID" => CATALOG_IBLOCK_ID,
	"ELEMENT_SORT_FIELD2" => "rating",
	"ELEMENT_SORT_ORDER2" => "desc",
	"ELEMENT_SORT_FIELD" => "CATALOG_QUANTITY",
	"ELEMENT_SORT_ORDER" => "desc",
	"PROPERTY_CODE" => Array
		(
			0 => "",
			1 => "ARTNUMBER",
			2 => "MANUFACTURER",
			3 => "ODDS",
			4 => "MORE_PHOTO",
			5 => "",
		),
	"BASKET_URL" => "/personal/cart/",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "Y",
	"CACHE_GROUPS" => "N",
	"DISPLAY_COMPARE" => "Y",
	"PAGE_ELEMENT_COUNT" => "12",
	"LINE_ELEMENT_COUNT" => "",
	"PRICE_CODE" => CClass::getCurrentPriceCode(),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"SHOW_OLD_PRICE" => "Y",
	"USE_PRODUCT_QUANTITY" => "N",
	"ADD_PROPERTIES_TO_BASKET" => "N",
	"PARTIAL_PRODUCT_PROPERTIES" => "N",
	"SECTION_URL" => "/catalog/#SECTION_CODE#/",
	"DETAIL_URL" => "/product/#ELEMENT_CODE#/",
	"USE_MAIN_ELEMENT_SECTION" => "Y",
	"CONVERT_CURRENCY" => "N",
	"CURRENCY_ID" => "",
	"HIDE_NOT_AVAILABLE" => "N",
	"HIDE_NOT_AVAILABLE_OFFERS" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"ADD_TO_BASKET_ACTION" => "",
	"SHOW_CLOSE_POPUP" => "",
	"COMPARE_PATH" => "/compare/",
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"USE_COMPARE_LIST" => "Y",
	"BACKGROUND_IMAGE" => "-",
	"COMPATIBLE_MODE" => "Y",
	"DISABLE_INIT_JS_IN_COMPONENT" => "N",
	"IS_AJAX" => "N",
);

$frame = $this->createFrame()->begin();

if (!empty($arResult['ITEMS']))
{
	?>
	<div class="goods__card-sort--container hide mobile-show">
		<div class="sort-cover">
			<div class="goods__card-sort-filter">
				<span>Фильтры <div class="tag is-warning hide">!</div></span>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="goods__filter">
		<div class="goods__filter-container">
			<div class="goods__filter-title--mobile">
				<?/*<p class="goods__filter-title">Фильтры</p>*/?>
				<div class="goods__filter-close-button"></div>
			</div>

			<div class="goods__filter-content goods__filter-content--mobile">
				<p class="goods__filter-title">Фильтры</p>
				<div class="goods__filter-close-button"></div>
				<div class="filter">
					<div class="filter__item" style="margin-bottom: 25px !important;">
						<div class="filter__title--toggle is-expanded">
							<div aria-expanded="true" aria-controls="accordion1" class="filter__title accordion-title accordionTitle js-accordionTrigger is-expanded">Выберите категорию</div>
						</div>
						<div class="filter__content accordion-content accordionItem is-expanded" id="accordion1" aria-hidden="false">
							<ul class="filter__content-list">
								<li class="filter__content-item filter__content-item--toggle <?=empty($_GET['SECTION_ID']) ? 'active' : ''?>">
									<a href="/product_viewed/">
										<span>Все</span>
										<span><?=count($arResult['ITEMS'])?></span>
									</a>
								</li>
								<?
								foreach ($arResult['SECTIONS'] as $key => $arItem)
								{
									?>
									<li class="filter__content-item filter__content-item--toggle <?=$_GET['SECTION_ID'] == $arItem['ID'] ? 'active' : ''?>">
										<a href="?SECTION_ID=<?=$arItem['ID']?>">
											<span><?=$arItem['NAME']?></span>
											<span><?=$arItem['COUNT']?></span>
										</a>
									</li>
									<?
								}
								?>
							</ul>
						</div>
					</div>
					<div class="page_product_viewed_button_compare btn is-primary">Добавить к сравнению</div>
				</div>
			</div>
		</div>
	</div>
	<div class="goods__card">
		<div class="goods__card-cell card-cell preview-products">
			<div class="goods-list">
				<div class="card-cell--row">
					<?
					$r = 1;
					$i = 0;
					foreach ($arResult['ITEMS'] as $key => $arItem)
					{
						if(!empty($_GET['SECTION_ID']) && $_GET['SECTION_ID'] != $arItem['IBLOCK_SECTION_ID']){
							continue;
						}
						$i ++;
						if($i > 3):
							$r ++;
							$i = 1;
							?>
							</div>
							<div class="card-cell--row">
							<?
						endif;

						$APPLICATION->IncludeComponent(
							"bitrix:catalog.item",
							"product",
							array(
								"RESULT" => array(
									"ITEM" => $arItem,
								),
								"PARAMS" => $ORIGINAL_PARAMETERS + array("SETTING" => "")
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?
}
$frame->beginStub();
$frame->end();
?>
