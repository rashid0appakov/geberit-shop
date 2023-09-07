<?php

//pr($templateData['ITEM_INFO']);
if(empty($_SESSION['CUSTOM_VIEWED']))
{
	$_SESSION['CUSTOM_VIEWED'] = [];
}
$arVieved = $_SESSION['CUSTOM_VIEWED'];
$_SESSION['CUSTOM_VIEWED'] = [
	$templateData['ITEM_INFO']['ID'] => $templateData['ITEM_INFO'],
];
$i = 0;
foreach($arVieved as $key=>$arItem)
{
	if($i < 30 and !isset($_SESSION['CUSTOM_VIEWED'][$key]) and $key == $arItem['ID'])
	{
		$_SESSION['CUSTOM_VIEWED'][$key] = $arItem;
		$i ++;
	}
}
unset($arVieved);
//pr($_SESSION['CUSTOM_VIEWED']);

if($templateData['robots'])
{
	$APPLICATION->SetPageProperty("robots", $templateData['robots']);
}

// начало - https://top-santehnika.bitrix24.ru/workgroups/group/10/tasks/task/view/3734/
global $man_show, $locId, $USER;

$arResult['no_index'] = false;
//if ($USER->IsAdmin()){
	
	$db_props = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], array("sort" => "asc"), Array("CODE"=>"MANUFACTURER"));
	if($ar_props = $db_props->Fetch()){
		$MANUFACTURER = IntVal($ar_props["VALUE"]);
		// var_dump($MANUFACTURER);
		// 	echo "<pre>";
		// var_dump($arResult);
		// echo "</pre>";
		// die;
		if (!in_array($MANUFACTURER, $man_show)){
			
			$arResult['no_index'] = true;
			$APPLICATION->SetPageProperty("robots", "noindex, nofollow");
		}
	}

	
	//die;
//}

// конец - https://top-santehnika.bitrix24.ru/workgroups/group/10/tasks/task/view/3734/

if($_REQUEST['AJAX_DELIVERY'] == 'Y')
{
	$GLOBALS['APPLICATION']->RestartBuffer();
}
$deliveryContent = '';
$price = $arResult["MIN_PRICE"]["DISCOUNT_VALUE"];
include($_SERVER['DOCUMENT_ROOT'].'/local/templates/.default/include/catalog_delivery.php');
if($_REQUEST['AJAX_DELIVERY'] == 'Y')
{
	echo $deliveryContent;
	exit;
}
?>
<div id="delivery_container-data" data-encode-json="<?=base64_encode(json_encode($deliveryContent))?>">
<script type="text/javascript">
	$(function() {
		$("#delivery_container").html(JSON.parse(atob($("#delivery_container-data").data('encode-json'))));
	});
</script>

<?
if(!empty($_SERVER['HTTP_IS_SUB_HEADER']) && SITE_ID == 's0'){
	$APPLICATION->SetPageProperty("canonical", 'https://'.SITE_SERVER_NAME.$arResult['DETAIL_PAGE_URL']);
}

$arElementVars = [
    '{min_price}'
];
$min_price = $arResult["MIN_PRICE"]['DISCOUNT_VALUE'] ?? $arResult["MIN_PRICE"]['VALUE'];
$arElementValues = [
    $min_price
];
$APPLICATION->SetTitle(str_replace($arElementVars, $arElementValues, $APPLICATION->GetTitle()));
$APPLICATION->SetPageProperty('title', str_replace($arElementVars, $arElementValues, $APPLICATION->GetPageProperty('title')));
$APPLICATION->SetPageProperty('description', str_replace($arElementVars, $arElementValues, $APPLICATION->GetPageProperty('description')));