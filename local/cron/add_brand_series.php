<?php
include_once(__DIR__.'/_header.php');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$IBLOCK_ID = 0;
$IBLOCK_ID = CATALOG_IBLOCK_ID;
if(!$IBLOCK_ID){
	die('No IBLOCK_ID');
}

file_put_contents(
	$_SERVER['DOCUMENT_ROOT'].'/local/log/add_brand_series_'.$IBLOCK_ID.'.log', 
	''
);

$arSerBrands = array();

//Достаем все товары
$ob = CIBlockElement::GetList(
	array(),
	array(
		'IBLOCK_ID' => $IBLOCK_ID
	),
	false,
	false,
	array(
		'ID',
		'IBLOCK_ID',
		'PROPERTY_SERIES',
		'PROPERTY_MANUFACTURER',
	)
);
while ($item = $ob->Fetch()){
	
	//Формируем массив серия - бренда
	if(!empty($item['PROPERTY_SERIES_VALUE']) && !empty($item['PROPERTY_MANUFACTURER_VALUE']) && is_numeric($item['PROPERTY_SERIES_VALUE']) && is_numeric($item['PROPERTY_MANUFACTURER_VALUE'])){
		$arSerBrands[$item['PROPERTY_SERIES_VALUE']] = $item['PROPERTY_MANUFACTURER_VALUE'];
	}

}

foreach($arSerBrands as $idSeries => $idBrand){
	
	//Добавляем к серии бренд
	$arProps = array();
	
	$arProps['BRAND'] = $idBrand;
	
	CIBlockElement::SetPropertyValuesEx($idSeries, SERIES_IBLOCK_ID, $arProps);
	
	//Пишем в лог
	file_put_contents(
		$_SERVER['DOCUMENT_ROOT'].'/local/log/add_brand_series_'.$IBLOCK_ID.'.log', 
		json_encode(array('IBLOCK_ID' => $IBLOCK_ID, 'idSeries' => $idSeries, 'arProps' => $arProps), JSON_UNESCAPED_UNICODE).PHP_EOL,
		FILE_APPEND
	);	

}	
?>