<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$IBLOCK_ID = 15;

$dataHandler = fopen(__DIR__."/data/products.csv", "r");
$row = 0;
while (($data = fgetcsv($dataHandler, 0, ";")) !== FALSE) {
	$row++;
	if($row == 1 || $data[14] == ''){
		continue;
	}
	
	$url = $data[2];
	$arAccess = explode(",", $data[14]);
	
	//Находим товары по старым ИД
	$arProps = array();
	$ob = CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $IBLOCK_ID,
			'=PROPERTY_LAST_ID' => $arAccess,
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID'
		)
	);
	while($item = $ob->Fetch())
	{
		$arProps['ADDITIONAL'][] = $item['ID'];
	}
	
	//Находим текущий товар
	$ob = CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $IBLOCK_ID,
			'=CODE' => $url,
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID'
		)
	);
	if($item = $ob->Fetch())
	{
		$productId = $item['ID'];
	}
	
	//Привязваем аксессуары
	CIBlockElement::SetPropertyValuesEx($productId, $IBLOCK_ID, $arProps);
}
fclose($dataHandler);
?>