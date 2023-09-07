<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<?if (count($arResult["ITEMS"])){?>
<h2 class="is-size-3">Популярные коллекции Geberit</h2>
<div class="swiper-container1" style="overflow: hidden;">
    <div class="swiper-wrapper">
    	<?foreach($arResult["ITEMS"] AS $k => &$arItem){?>
	      <div class="swiper-slide" style="text-align: center;">
	      	<? if (!empty($arItem["PREVIEW_PICTURE"]['ID'])):?>
            <?
            $arImg = Pict::getResizeWebpSrc($arItem["PREVIEW_PICTURE"]['ID'], '170','170');
            ?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
					<img src="<?=$arImg?>" alt="<?=$arItem["NAME"]?>" />
				</a>
			<? endif;?>
			<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a></h2>
		</div>
      <?}?>
    </div>
	<br>
    <!-- Add Arrows -->
    <button class="button left prevSlideLogo">
		<img src="<?=$templateFolder?>/image/arrow_gray_left.png">
	</button>
	<button class="button right nextSlideLogo" style="float: right;">
		<img src="<?=$templateFolder?>/image/arrow_gray_right.png">
	</button>
  </div><br><br>
<?}?>  
  <?/*
<div class="column is-6 desktop">
	<div class="">
		<div class="partner-carousel1 swiper-container">
			<div class="swiper-wrapper">
		<?$pageCount = floor(count($arResult["ITEMS"]) / 8);
		for ($i = 0, $pi = 0; $pi < $pageCount; $pi++):?>
			<div class="logos columns is-mobile is-gapless is-multiline has-text-centered">
				<?for ($bi = 0; $bi < 8; $bi++, $i++):
					$arItem = $arResult["ITEMS"][$i];?>
					<div class="column is-4">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
							<img class="" src="<?=$arItem["RESIZED"]["src"]?>">
						</a>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a>
					</div>
				<?endfor;?>
				<div class="column is-4 navigate">
					<button class="button left prevSlideLogo">
						<img src="<?=$templateFolder?>/image/arrow_gray_left.png">
					</button>
					<button class="button right nextSlideLogo">
						<img src="<?=$templateFolder?>/image/arrow_gray_right.png">
					</button>
				</div>
			</div>
		<?endfor;?>
	</div>
	</div>	
	</div>
</div>
<?*//*
<div class="column mobile">
	<div class="logos partner-carousel columns is-mobile is-gapless is-multiline has-text-centered">
		<div class="partner-carousel swiper-container">
			<div class="swiper-wrapper">
				<?$pageCount = floor(count($arResult["ITEMS"]) / 4);
				for ($i = 0, $pi = 0; $pi < $pageCount; $pi++):?>
					<div class="swiper-slide">
						<table>
							<?for ($ti = 0; $ti < 2; $ti++):?>
								<tr>
									<?for ($ri = 0; $ri < 2; $ri++, $i++):
										$arItem = $arResult["ITEMS"][$i];?>
										<td>
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">	
												<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>">
											</a>
										</td>
									<?endfor;?>
								</tr>
							<?endfor;?>
						</table>
					</div>
				<?endfor;?>
			</div>
		</div>
	</div>
	<div class="finger-mobile">Перемещайте логотипы пальцем</div>
</div>
*/?>