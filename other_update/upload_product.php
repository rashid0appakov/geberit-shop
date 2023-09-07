<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

// Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.
$action = $_POST['action'];
if (empty($action)) {return;}

//Обработать 100000 строк, с шагом 500
$count = 5000;
$step = 10;

// Получаем от клиента номер итерации
$url = $_POST['url']; if (empty($url)) return;
$offset = $_POST['offset'];

$offset = $offset + $step;

//Тело скрипта
CModule::IncludeModule('iblock');

$el = new CIBlockElement;
$ibpenum = new CIBlockPropertyEnum;
$ibp = new CIBlockProperty;

$params = Array(
   "max_len" => "100", // обрезает символьный код до 100 символов
   "change_case" => "L", // буквы преобразуются к нижнему регистру
   "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
   "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
   "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
   "use_google" => "false", // отключаем использование google
); 

$IBLOCK_ID = 15;

$row = 1;
$arProcessing = array();

$handle = fopen("Misty.csv", "r");
while (($data = fgetcsv($handle, 0, ";")) !== FALSE ) {
	
	$number = $data[0];	
	$barcode = $data[1];
	
	if($row <= $offset && $row > $offset - $step && $number != '' && $barcode != ''){
		
	
		//Ищем товар
		$arSelect = Array("ID");
		$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "PROPERTY_ARTNUMBER" => $barcode);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
		
		//Если нашли
		if($arItem = $res->GetNext()){
			
			$PRODUCT_ID = $arItem['ID'];
			
			CIBlockElement::SetPropertyValueCode($PRODUCT_ID, "ARTNUMBER", $number);
			
			$arProcessing[] = $PRODUCT_ID;
		}	
	}
	
	$row++;	
	
}
fclose($handle);

// Проверяем, все ли строки обработаны
if ($offset >= $count) {
	$sucsess = 1;
} else {
	$sucsess = round($offset / $count, 2);
}

// И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
$output = Array('offset' => $offset, 'sucsess' => $sucsess, 'last_products' => $arProcessing);
echo json_encode($output);
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>