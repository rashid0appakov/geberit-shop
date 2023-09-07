<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$IBLOCK_ID = 15;

//Формируем нормальный массив
$row = 0;
$arKits = array();
$dataHandler = fopen(__DIR__."/data/add_kit.csv", "r");
while (($data = fgetcsv($dataHandler, 0, ",")) !== FALSE) {
	
	$arKits[$data[0]][] = $data[1];
	
	$row++;
}	

foreach($arKits as $lastId => $arKitIds){

	//Находим товары по старым ИД
	$obIds = CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $IBLOCK_ID,
			'=PROPERTY_LAST_ID' => $arKitIds,
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID'
		)
	);
	$i = 0;
	while($itemIds = $obIds->Fetch())
	{
		$arItems[$i]["ACTIVE"] = "Y";
		$arItems[$i]["ITEM_ID"] = $itemIds['ID'];
		$arItems[$i]["QUANTITY"] = 1;
		
		$i++;
	}
	
	//Находим текущий товар на старому ИД
	$ob = CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $IBLOCK_ID,
			'=PROPERTY_LAST_ID' => $lastId,
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
		$productId = $item['ID'];
	}

	//Создаем набор		
	$arFieldsNabor = array( 
					  "TYPE" => 1, // тип 1 = комплект, 2 = набор
					  "SET_ID" => 0, 
					  "ITEM_ID" => $productId,
					  "ITEMS" => $arItems,
					); 
	CCatalogProductSet::add($arFieldsNabor);
}
fclose($dataHandler);
?>