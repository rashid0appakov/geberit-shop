<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin();

if (!empty($arResult['ITEMS']))
{
	?>
	<div class="viewed_list_contanier">
		<a href="/product_viewed/" class="viewed_list_title">
			<span><? echo GetMessage('CVP_TPL_MESS_YOU_LOOKED') ?></span>
			<img src="<?=$arResult['FIRST_ITEM']['DETAIL_PICTURE']['SMALL']['src']?>" alt="<?=$arResult['FIRST_ITEM']['NAME']?>" />
			<span class="tag viewed_count" id="">
				<?=count($arResult['ITEMS'])?>
			</span>
		</a>
		<div class="toolbar-bottom__slider bx-basket viewed_list" >
			<?
			//Элементы которые выводятся при наведение на элемент слайдера
			$i = 0;
			foreach ($arResult['ITEMS'] as $key => $arItem)
			{
				if($i > 9){
					continue;
				}
				$i++;
				?>
				<div class="_toolbarItem">
					<div class="toolbar-bottom__slider-info slider-info" data-id="<?=$arItem["ID"]?>">
						<div class="slider-info__content">
							<div class="slider-info__content-img">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
									<img src="<?=$arItem["DETAIL_PICTURE"]['MEDIUM']['src']?>" alt="<?=$arItem["NAME"]?>" />
								</a>
							</div>
							<div class="slider-info__content-info">
								<p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></p>
								<p class="slider-info__content-price"><?=$arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?></p>
							</div>
						</div>
						<div class="slider-info__corner"></div>
					</div>
				</div>
				<?
			}
			//Элементы в слайдере
			?>
			<div class="slider-toolbar">
				<?
				$i = 0;
				foreach ($arResult['ITEMS'] as $key => $arItem)
				{
					if($i > 9){
						continue;
					}
					$i++;
					?>
					<div class="toolbar-bottom__slider-slide" data-id="<?=$arItem["ID"]?>">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
							<img src="<?=$arItem["DETAIL_PICTURE"]['SMALL']['src']?>" alt="<?=$arItem["NAME"]?>" />
						</a>
					</div>
					<?
				}
				?>
			</div>
		</div>
	</div>
	<?
	//Элемены которые показываются при клике на глаз в мобильной версии
	?>
	<div class="viewed_mobile">
		<?
		$i = 0;
		foreach ($arResult['ITEMS'] as $key => $arItem)
		{
			if($i > 9){
				continue;
			}
			$i++;
			?>
			<div class="viewed_mobile_item" data-id="<?=$arItem["ID"]?>">
				<div class="viewed_mobile_item_content">
					<div class="viewed_mobile_item_content_img">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
							<img src="<?=$arItem["DETAIL_PICTURE"]['MEDIUM']['src']?>" alt="<?=$arItem["NAME"]?>" />
						</a>
					</div>
					<div class="viewed_mobile_item_content_info">
						<p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></p>
						<p class="viewed_mobile_item_content_price"><?=$arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?></p>
					</div>
				</div>
			</div>
			<?
		}
		?>
	</div>
	<?
}
$frame->beginStub();
$frame->end();
?>
