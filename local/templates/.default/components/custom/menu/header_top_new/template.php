<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
$this->setFrameMode(true);
//global $USER;
//if ($USER->IsAdmin()){
	?>
	<div class="menu">
	<ul>
		<li class="has_subMenu" ><a href="javascript:void(0);" id="has_subMenu-link">Другие бренды <div></div></a></li>
		<?foreach ($arResult as $itemIdex => $arItem):
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

		?>
			<li><a href="https://<?=$SERV_NAME?><?=$arItem["LINK"]?>" <? if( $arItem['SELECTED'] == 1 || preg_match("/".str_replace("/", "", $arItem["LINK"])."/i", $APPLICATION->GetCurPage())) { ?>class="active"<? } ?> ><?=$arItem["TEXT"]?></a></li>
			
		<?endforeach;?>
	</ul>
</div>
	<?
/*}else{
?>
<div class="menu">
	<ul>
		<li class="has_subMenu" ><a href="javascript:void(0);" id="has_subMenu-link">Другие бренды <div></div></a></li>
		<?foreach ($arResult as $itemIdex => $arItem):
		?>
			<li><a href="<?=$arItem["LINK"]?>" <? if( $arItem['SELECTED'] == 1 || preg_match("/".str_replace("/", "", $arItem["LINK"])."/i", $APPLICATION->GetCurPage())) { ?>class="active"<? } ?> ><?=$arItem["TEXT"]?></a></li>
		<?endforeach;?>
	</ul>
</div>
<?
}*/

?>
