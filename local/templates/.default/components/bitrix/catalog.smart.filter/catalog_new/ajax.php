<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);
	
$URL_ID = $arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'][$GLOBALS[$arParams["FILTER_NAME"]]["=PROPERTY_SERIES"][0]]['URL_ID'];

// ?filter - начало

global $USER;
//if ($USER->isAdmin()){
//		echo "<pre>";
	$sec_url = str_replace('/clear/', '/', $arResult['SEF_DEL_FILTER_URL']);
	$FILTER_URL = str_replace($sec_url, '/', $arResult['FILTER_URL']);

	//var_dump($sec_url);

	$arr = explode('/', $FILTER_URL);
	$count = count($arr) - 1;
	unset($arr[0]);
	unset($arr[$count]);

	//var_dump($arr);

	$FILTER_URL = implode('%2F', $arr);

	//var_dump($FILTER_URL);

	$arResult['FILTER_URL'] = '?filter='.$FILTER_URL;
	//$arResult['FILTER_URL'] = str_replace('?filter=%2F', '?filter=', $arResult['FILTER_URL']);
	//var_dump($arResult['FILTER_URL']);
//		echo "</pre>";
// 	$arResult['FILTER_URL'] = $URL_ID; //'?filter='.str_replace('/', '%2F', $arResult['FILTER_URL']);
//}

// ?filter - конец


$arResult['FILTER_URL'] = str_replace('/'.$URL_ID.'/', '/', $arResult['FILTER_URL']);
$arResult['FILTER_AJAX_URL'] = str_replace('/'.$URL_ID.'/', '/', $arResult['FILTER_AJAX_URL']);
$arResult['SEF_SET_FILTER_URL'] = str_replace('/'.$URL_ID.'/', '/', $arResult['SEF_SET_FILTER_URL']);


$arResult['FILTER_URL'] = customFixUri($arResult['FILTER_URL'], true);
$arResult['FILTER_AJAX_URL'] = customFixUri($arResult['FILTER_AJAX_URL'], true);
$arResult['SEF_SET_FILTER_URL'] = customFixUri($arResult['SEF_SET_FILTER_URL'], true);


echo Bitrix\Main\Web\Json::encode($arResult);
?>