<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>
<div class="main-navigate column is-narrow">
	<?foreach ($arResult as $itemIdex => $arItem):?>
	<?if($arItem["LINK"]=='/delivery/' || $arItem["LINK"]=='/contacts/'){
		if($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'||$geo_id ==817){
			$arItem["LINK"] .='sankt-peterburg/';
		}elseif($geo_id==129){
			$arItem["LINK"] .='moskva/';
		}else{
			$arItem["LINK"] .='other/';
		}
		if($geo_id ==817){
			$SERV_NAME = 'spb.geberit-shop.ru';
		}else{
			$SERV_NAME = 'geberit-shop.ru';
		}
	}else{$SERV_NAME = 'geberit-shop.ru';}?>		
		<a href="https://<?=$SERV_NAME?><?=$arItem["LINK"]?>" <? if( $arItem['SELECTED'] == 1 || preg_match("/".str_replace("/", "", $arItem["LINK"])."/i", $APPLICATION->GetCurPage())) { ?>class="active"<? } ?> ><?=$arItem["TEXT"]?></a>
	<?endforeach;?>
</div>