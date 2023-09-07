<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*if($_GET['PAGEN_2']>$arResult["nEndPage"]){
	$page = $APPLICATION->GetCurPageParam("", array("PAGEN_2")); 
	LocalRedirect($page, false, '301 Moved permanently');
}*/
if(strpos($arResult["sUrlPath"], '/blog/') === 0)
{
	$arResult["sUrlPath"] = str_replace('/blog/', '/', $arResult["sUrlPath"]);
}
$arResult["sUrlPath"] = customFixUri($arResult["sUrlPath"], true);
?>
<div class="pagination-wrapper" data-items="<?=$arResult['NavRecordCount'];?>">
<?if ($arResult["NavLastRecordShow"] < $arResult["NavRecordCount"]):
	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$moreCount = min($arResult["NavPageSize"], $arResult["NavRecordCount"] - $arResult["NavLastRecordShow"]);?>
	<div class="card-cell__show-more">
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">
			<p>Показать еще (+<span><?=$moreCount?></span>)</p>
		</a>
	</div>
<?endif;?>
<?
$strNavQueryString = str_replace('IS_AJAX2=Y&amp;', '', ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : ""));
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

$count = $arResult['NavPageCount'];

$countLink = 4;
$step = reduce($arResult['NavPageNomer'], $count, $countLink);
$all_step = reduce($arResult['NavPageCount'], $count, $countLink);

?>
<? if( $arResult["NavRecordCount"] > 0 && $count > 1 ) {?>
	<div class="info_pagination">Страница <?=$arResult['NavPageNomer']?> из <?=$count?></div>
	<div class="b-pagination">
		<div class="b-pagination__row">
			<div class="b-pagination__item">
				<? if( $arResult['NavPageNomer'] == 1 ) { ?>
					<div class="b-pagination__item-link b-pagination__item-link--disabled  ">
						<span class="b-pagination__item-text">Назад</span>
						<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
					</div>
				<? } else { 
					if($arResult['NavPageNomer']==2)
					{
					?>
						<a href="<?=$arResult["sUrlPath"]?>" class="b-pagination__item-link">
							<span class="b-pagination__item-text">Назад</span>
							<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
						</a>
					<?}
					else{?>
						<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']-1;?>" class="b-pagination__item-link">
							<span class="b-pagination__item-text">Назад</span>
							<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
						</a>
					<?}?>
				<? } ?>
			</div>
			<?
			if($step > 1){
				?>
				<div class="b-pagination__item <? if( $arResult['NavPageNomer'] == $i ) { ?> b-pagination__item--current <? } ?>">
					<a href="<?=$arResult["sUrlPath"]?>" class="b-pagination__item-link">
						<span class="b-pagination__item-text">
							1
						</span>
					</a>
				</div>
				<div class="b-pagination__item">
					<span class="b-pagination__item-text">
						...
					</span>
				</div>
				<?
			}	
			$i = 0;
			while( $i <= $count ) {
				$i++;
				$show = false;
				if($step == 1 && $i <= ($step * $countLink) && $i <= $count){
					$show = true;
				}
				elseif($step > 1 && $i <= ($step * $countLink) && $i >= (($step * $countLink) - ($countLink + 1)) && $i <= $count){
					$show = true;
				}
				
				if($show){
					?>
					<div class="b-pagination__item <? if( $arResult['NavPageNomer'] == $i ) { ?> b-pagination__item--current <? } ?>">
						<?if($i==1){?>
							<a href="<?=$arResult["sUrlPath"]?>" class="b-pagination__item-link">
								<span class="b-pagination__item-text">
									<?=$i;?>
								</span>
							</a>
						<?}else{?>
							<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i;?>" class="b-pagination__item-link">
								<span class="b-pagination__item-text">
									<?=$i;?>
								</span>
							</a>
						<?}?>
					</div>
					<?
				}
			} ?>
			<?
			/*global $USER;
			if ($USER->IsAdmin()){*/
				if($step < ($all_step-1)){
					?>
					<div class="b-pagination__item">
						<span class="b-pagination__item-text">
							...
						</span>
					</div>
					<div class="b-pagination__item <? if( $arResult['NavPageNomer'] == $i ) { ?> b-pagination__item--current <? } ?>">
						<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageCount'];?>" class="b-pagination__item-link">
							<span class="b-pagination__item-text">
								<?=$arResult['NavPageCount']?>
							</span>
						</a>
					</div>
					<?
				}
			//}?>
			<div class="b-pagination__item">
				<? if( $arResult['NavPageNomer'] == $count ) { ?>
					<div class="b-pagination__item-link b-pagination__item-link--disabled  ">
						<span class="b-pagination__item-text">Вперед</span>
						<span class="b-pagination__icon b-pagination__icon--next"></span>
					</div>
				<? } else { ?>
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']+1;?>" class="b-pagination__item-link">
						<span class="b-pagination__item-text">Вперед</span>
						<span class="b-pagination__icon b-pagination__icon--next"></span>
					</a>
				<? } ?>
			</div>
		</div>
	</div>
<?}?>
</div>
<?
//if (function_exists('reduce')) {
	
//}
?>
<?/*?>
<div class="catalog-pagination">

	<?
	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

	$count = $arResult['NavPageCount'];

	$i=1;
	while( $i <= $count ) {
	?>

		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i;?>">
			<?=$i;?>
		</a>

	<?
		$i++;
	}
	?>

</div>
<?*/?><?/*
<input type="hidden" name="catalog-count" value="<?=$arResult['NavRecordCount'];?>" />*/?>