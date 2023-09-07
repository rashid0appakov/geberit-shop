<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="pagination-wrapper" data-items="<?=$arResult['NavRecordCount'];?>">
<?
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

$count = $arResult['NavPageCount'];
?>
<? if( $arResult["NavRecordCount"] > 0 ) {?>
	<div class="b-pagination">
		<div class="b-pagination__row">
			<div class="b-pagination__item">
				<? if( $arResult['NavPageNomer'] == 1 ) { ?>
					<div class="b-pagination__item-link b-pagination__item-link--disabled  ">
						<span class="b-pagination__item-text"><?=GetMessage('P_PREV')?></span>
						<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
					</div>
				<? } else { ?>
					<?if(($arResult['NavPageNomer']-1)==1){?>
					<a href="<?=$arResult["sUrlPath"]?><?if(strlen($strNavQueryString)>0){?>?<?}?><?=$strNavQueryString?>" class="b-pagination__item-link">
						<span class="b-pagination__item-text"><?=GetMessage('P_PREV')?></span>
						<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
					</a>
					<?}else{?>	
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']-1;?>" class="b-pagination__item-link">
						<span class="b-pagination__item-text"><?=GetMessage('P_PREV')?></span>
						<span class="b-pagination__icon b-pagination__icon--hide-desktop b-pagination__icon--begin"></span>
					</a>
					<?}?>
				<? } ?>
			</div>
			<?
			$i=1;
			while( $i <= $count ) {?>
				<div class="b-pagination__item <? if( $arResult['NavPageNomer'] == $i ) { ?> b-pagination__item--current <? } ?>">
					<?if($i==1){?>
						<a href="<?=$arResult["sUrlPath"]?><?if(strlen($strNavQueryString)>0){?>?<?}?><?=$strNavQueryString?>" class="b-pagination__item-link">
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
				$i++;

				if( $i == 15 ) { break; }
				?>
			<? } ?>
			<div class="b-pagination__item">
				<? if( $arResult['NavPageNomer'] == $count ) { ?>
					<div class="b-pagination__item-link b-pagination__item-link--disabled  ">
						<span class="b-pagination__item-text"><?=GetMessage('P_NEXT')?></span>
						<span class="b-pagination__icon b-pagination__icon--next"></span>
					</div>
				<? } else { ?>
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']+1;?>" class="b-pagination__item-link">
						<span class="b-pagination__item-text"><?=GetMessage('P_NEXT')?></span>
						<span class="b-pagination__icon b-pagination__icon--next"></span>
					</a>
				<? } ?>
			</div>
		</div>
	</div>
<?}?>
</div>