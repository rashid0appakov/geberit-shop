<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin();

//pr($_SESSION['CUSTOM_VIEWED']);

if (!empty($_SESSION['CUSTOM_VIEWED']))
{
	$firstItem = current($_SESSION['CUSTOM_VIEWED']);
	?>
	<div class="viewed_list_contanier">
		<a href="/product_viewed/" class="viewed_list_title">
			<span><? echo GetMessage('CVP_TPL_MESS_YOU_LOOKED') ?></span>
			<img src="<?=$firstItem['PICTURE']['SMALL']['src']?>" alt="<?=$firstItem['NAME']?>" />
			<span class="tag viewed_count" id="">
				<?=count($_SESSION['CUSTOM_VIEWED'])?>
			</span>
		</a>
		<div class="toolbar-bottom__slider bx-basket viewed_list" >
			<?
			//Элементы которые выводятся при наведение на элемент слайдера
			$i = 0;
			foreach ($_SESSION['CUSTOM_VIEWED'] as $key => $arItem)
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
								<a href="<?=$arItem["URL"]?>" title="<?=$arItem["NAME"]?>">
									<img src="<?=$arItem["PICTURE"]['MEDIUM']['src']?>" alt="<?=$arItem["NAME"]?>" />
								</a>
							</div>
							<div class="slider-info__content-info">
								<p><a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a></p>
								<p class="slider-info__content-price"><?=$arItem["PRICE"]?></p>
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
				foreach ($_SESSION['CUSTOM_VIEWED'] as $key => $arItem)
				{
					if($i > 9){
						continue;
					}
					$i++;
					?>
					<div class="toolbar-bottom__slider-slide" data-id="<?=$arItem["ID"]?>">
						<a href="<?=$arItem["URL"]?>" title="<?=$arItem["NAME"]?>">
							<img src="<?=$arItem["PICTURE"]['SMALL']['src']?>" alt="<?=$arItem["NAME"]?>" />
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
		foreach ($_SESSION['CUSTOM_VIEWED'] as $key => $arItem)
		{
			if($i > 9){
				continue;
			}
			$i++;
			?>
			<div class="viewed_mobile_item" data-id="<?=$arItem["ID"]?>">
				<div class="viewed_mobile_item_content">
					<div class="viewed_mobile_item_content_img">
						<a href="<?=$arItem["URL"]?>" title="<?=$arItem["NAME"]?>">
							<img src="<?=$arItem["PICTURE"]['MEDIUM']['src']?>" alt="<?=$arItem["NAME"]?>" />
						</a>
					</div>
					<div class="viewed_mobile_item_content_info">
						<p><a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a></p>
						<p class="viewed_mobile_item_content_price"><?=$arItem["PRICE"]?></p>
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
