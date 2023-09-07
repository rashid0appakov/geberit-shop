<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<? if (!empty($arResult['CATEGORIES']['READY'])):
	$strSlider  = '';
	foreach($arResult['CATEGORIES']['READY'] AS &$arItem):
		if ($arItem['CAN_BUY'] != 'Y')
			continue;
		$arFile = $arImage = [];
		if ($arItem["PICTURE_SRC"]){
			$arFile = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
			$arImage= [
				'SMALL' => CFile::ResizeImageGet(
					$arFile,
					[
						"width" => 35,
						"height"=> 35
					], BX_RESIZE_IMAGE_PROPORTIONAL
				),
				'MEDIUM' => CFile::ResizeImageGet(
					$arFile,
					[
						"width" => 70,
						"height"=> 70
					], BX_RESIZE_IMAGE_PROPORTIONAL
				)
			];
		}

		$strSlider .= '<div class="toolbar-bottom__slider-slide" data-id="'.$arItem["ID"].'">';
		$strSlider .= '<a href="'.$arItem["DETAIL_PAGE_URL"].'" title="'.$arItem["NAME"].'">';
		$strSlider .= '<img src="'.$arImage['SMALL']['src'].'" alt="'.$arItem["NAME"].'" />';
		$strSlider .= '</a><span class="tag is-warning">'.$arItem["QUANTITY"].'</span></div>';
		?>
		<div class="_toolbarItem">
			<div class="toolbar-bottom__slider-info slider-info" data-id="<?=$arItem["ID"]?>">
				<div class="slider-info__content">
					<div class="slider-info__content-img">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
							<img src="<?=$arImage['MEDIUM']['src']?>" alt="<?=$arItem["NAME"]?>" />
						</a>
					</div>
					<div class="slider-info__content-info">
						<p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></p>
						<p class="slider-info__content-price"><?=$arItem["PRICE_FORMATED"]?></p>
					</div>
					<div class="slider-info__content-counter">
						<div class="slider-info__counter">
							<button class="slider-info__counter-minus">-</button>
							<div class="slider-info__counter-counter"><?=$arItem["QUANTITY"]?></div>
							<button class="slider-info__counter-plus">+</button>
						</div>
					</div>
					<p class="slider-info__content-price _sum_price" id="bottom-price-<?=$arItem["ID"];?>"><?=$arItem["SUM"]?></p>
					<div class="slider-info__content-button delete-basket" data-id="<?=$arItem["ID"]?>"></div>
				</div>
				<div class="slider-info__corner"></div>
			</div>
		</div>
<?
	endforeach;
endif;
if ($strSlider):?>
<div class="slider-toolbar"><?=$strSlider;?></div>
<?endif;?>