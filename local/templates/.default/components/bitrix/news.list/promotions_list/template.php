<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (count($arResult["ITEMS"]) < 1)
	return;
?>
<div class="promotions-list">
	<? foreach($arResult["ITEMS"] AS $k => &$arItem):
		$arCompareDates = 1;
		if (!empty($arItem["ACTIVE_TO"])):
			$displayActiveToDate = $arItem["ACTIVE_TO"];
			$displayCurrentDate = ConvertTimeStamp(false, "FULL");
			$arCompareDates = $DB->CompareDates($displayActiveToDate, $displayCurrentDate);
		endif;
    ?>
    <div class="promotions__item<?=($arCompareDates <= 0 ? ' completed' : '');?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
			<div class="promotions__item-image-wrap">
				<? if ($arItem["PREVIEW_PICTURE"]["SRC"]):?>
                <a class="promotions__item-image" title="<?=$arItem['NAME']?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem['NAME']?>" />
                </a>
				<? endif;?>
				<?/*if($arItem["PROPERTIES"]["TIMER"]["VALUE"] != false && !empty($arItem["ACTIVE_TO"])):
					$new_date = ParseDateTime($arItem["ACTIVE_TO"], FORMAT_DATETIME);
					if(!$new_date["HH"])
						$new_date["HH"] = 00;
					if(!$new_date["MI"])
						$new_date["MI"] = 00;?>
					<span class="time_buy_cont">
						<span class="time_buy_clock"><i class="fa fa-clock-o"></i></span>
						<span class="time_buy_timer" id="time_buy_timer_<?=$arItem['ID']?>"></span>
					</span>
				<?endif;*/?>
			</div>
			<span class="promotions__item-block">
        <span class="promotions__item-date">
          <?if($arCompareDates <= 0):
            echo GetMessage("PROMOTIONS_ENDED")." ".$arItem["DISPLAY_ACTIVE_TO"];
          else:
            echo GetMessage("PROMOTIONS_RUNNING")." ".(isset($arItem["DISPLAY_ACTIVE_TO"]) && !empty($arItem["DISPLAY_ACTIVE_TO"]) ? GetMessage("PROMOTIONS_UNTIL")." ".$arItem["DISPLAY_ACTIVE_TO"] : GetMessage("PROMOTIONS_ALWAYS"));
          endif;?>
        </span>
        <a class="promotions__item-name" title="<?=$arItem['NAME']?>" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
			</span>
		</div>
		<?/* if($arItem["PROPERTIES"]["TIMER"]["VALUE"] != false && !empty($arItem["ACTIVE_TO"])):?>
		<script type="text/javascript">
			$(function() {
				$("#time_buy_timer_<?=$arItem['ID']?>").countdown({
					until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
					format: "DHMS",
					expiryText: "<span class='over'><?=GetMessage('PROMOTIONS_TIME_BUY_EXPIRY')?></span>",
					alwaysExpire: true
				});
			});
		</script>
		<? endif;*/
        /*if ( !(($k+1) % 2)):?>
        <div class="clear"></div>
        <? endif;*/
	endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):
	echo $arResult["NAV_STRING"];
endif;?>