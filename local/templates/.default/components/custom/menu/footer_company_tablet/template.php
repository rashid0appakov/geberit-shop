<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<div class="is-size-5">Компания</div>
<?foreach ($arResult as $itemIndex => $arItem):
if($arItem["LINK"]=='/delivery/' || $arItem["LINK"]=='/contacts/'){
			if($_SERVER["HTTP_HOST"]=='ekb.geberit-shop.ru'||$geo_id ==2201){
				$arItem["LINK"] .='ekaterinburg/';
			}elseif($_SERVER["HTTP_HOST"]=='krasnodar.geberit-shop.ru'||$geo_id ==1095){
				$arItem["LINK"] .='krasnodar/';
			}elseif($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru'||$geo_id ==817){
				$arItem["LINK"] .='sankt-peterburg/';
			}elseif($geo_id==129){
				$arItem["LINK"] .='moskva/';
			}else{
				$arItem["LINK"] .='moskva/';
			}
			if($_SERVER["SERVER_NAME"]=='krasnodar.geberit-shop.ru' || $geo_id ==1095){
				$SERV_NAME = 'spb.geberit-shop.ru';
			}elseif($_SERVER["SERVER_NAME"]=='spb.geberit-shop.ru' || $geo_id ==817){
				$SERV_NAME = 'spb.geberit-shop.ru';
			}elseif($_SERVER["HTTP_HOST"]=='ekb.geberit-shop.ru' || $geo_id ==2201){
				$SERV_NAME = 'ekb.geberit-shop.ru';
			}else{
				$SERV_NAME = 'geberit-shop.ru';
			}
		}else{$SERV_NAME = 'geberit-shop.ru';}
	if ($arItem["PARAMS"]["SHOW_TABLET"] == "Y"):?>
		<a class="column is-size-5" href="https://<?=$SERV_NAME?><?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
	<?endif;
endforeach;?>