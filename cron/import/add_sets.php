<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$IBLOCK_ID = 15;
$dataHandler = fopen(__DIR__."/data/add_sets.csv", "r");

$row = 0;
while (($data = fgetcsv($dataHandler, 0, ",")) !== FALSE) {
	$row++;
	if($data[1] == 'NULL'){
		continue;
	}
	
	$lastId = $data[0];
	$arSetIds = explode(",", $data[1]);

	//Находим товары по старым ИД
	$obIds = CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $IBLOCK_ID,
			'=PROPERTY_LAST_ID' => $arSetIds,
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID'
		)
	);
	$i = 0;
	$arItems = array();
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
					  "TYPE" => 2, // тип 1 = комплект, 2 = набор
					  "SET_ID" => 0, 
					  "ITEM_ID" => $productId,
					  "ITEMS" => $arItems,
					); 

	CCatalogProductSet::add($arFieldsNabor);

}
fclose($dataHandler);
?>