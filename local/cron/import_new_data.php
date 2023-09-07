<?
include_once(__DIR__.'/_header.php');
if(substr($_SERVER["DOCUMENT_ROOT"], -1) == '/') {
    $_SERVER["DOCUMENT_ROOT"] = substr_replace($_SERVER["DOCUMENT_ROOT"], '', -1);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//die();
$IBLOCK_ID = CATALOG_IBLOCK_ID;

$arMapUnicProp = array('MORE_PHOTO', 'MANUFACTURER', 'GUARANTEE', 'COUNTRY', 'ARTNUMBER', 'NAZNACHENIE', 'MONTAZH', 'MATERIAL', 'FUNKTSIYA_BIDE', 'TIP', 'TIP_UNITAZA', 'TIP_AKSESSUARA', 'TIP_MEBELI', 'TIP_KLAVISHI', 'TIP_RAKOVINY', 'TIP_PODVODKI', 'TIP_INSTALLYACII', 'TIP_UTSENKI', 'TIP_LAMPY', 'TIP_SVETILNIKA', 'TIP_RISUNKA', 'TIP_OSVEZHITELYA', 'TIP_VENTILYATSII', 'TIP_VESHCHESTVA', 'TIP_MYTYA', 'TIP_MYLA', 'TIP_SHVA', 'TIP_BUMAGI', 'TIP_VODONAGREVATELYA', 'TIP_VODY', 'TIP_UMYAGCHITELYA', 'TIP_MEMBRANNOY_OCHISTKI', 'TIP_IZMELCHITELYA', 'TIP_MOTORA_IZMELCHITELYA', 'TIP_ZAMKA', 'TIP_VODOROZETKI', 'TIP_BAKA', 'TIP_PRISOEDINENIYA', 'TIP_MEMBRANY', 'TIP_ZATVORA', 'TIP_NASOSA', 'TIP_DINAMICHESKOGO_NASOSA', 'TIP_REDUKTORA', 'TIP_KONDITSIONERA', 'TIP_KHLADAGENTA', 'TIP_FAZY', 'TIP_VYVODA', 'TIP_PANELNOGO_RADIATORA', 'TIP_OBOGREVATELYA', 'TIP_KAMERY_SGORANIYA', 'TIP_GORELKI', 'TIP_OBEMNOGO_NASOSA', 'TIP_KRANA', 'TIP_STIRKI', 'TIP_SREDSTVA_DLYA_STIRKI', 'TIP_SMYVNOY_KLAVISHI', 'TIP_AERATORA', 'tip_shpuli', 'tip_shtangi', 'tip_noja', 'tip_zajiganiya', 'tip_rejucshego_instrumenta', 'tip_biotualeta', 'TIP_VIKL');

$data = [];
$ar = array("ID", "Статус товара", "Производитель", "Название", "Категория", "Цена", "Цена без скидки", "Валюта", "Ссылка на картинку", "Ссылки на др. картинки", "Характеристики товара", "Описание", "Гарантия производителя", "Страна происхождения", "Штрихкод", "Артикул", "Кол-во на складе", "Назначение", "Монтаж", "Материал", "Функция биде", "Тип", "Тип унитаза", "Тип аксессуара", "Тип мебели", "Тип клавиши", "Тип раковины", "Тип подводки", "Тип инсталляции", "Тип уценки", "Тип лампы", "Тип светильника", "Тип рисунка", "Тип освежителя", "Тип вентиляции", "Тип вещества", "Тип мытья", "Тип мыла", "Тип шва", "Тип бумаги", "Тип водонагревателя", "Тип воды", "Тип умягчителя", "Тип мембранной очистки", "Тип измельчителя", "Тип мотора измельчителя", "Тип замка", "Тип водорозетки", "Тип бака", "Тип присоединения", "Тип мембраны", "Тип затвора", "Тип насоса", "Тип динамического насоса", "Тип редуктора", "Тип кондиционера", "Тип хладагента", "Тип фазы", "Тип вывода", "Тип панельного радиатора", "Тип обогревателя", "Тип камеры сгорания", "Тип горелки", "Тип объемного насоса", "Тип крана", "Тип стирки", "Тип средства для стирки", "Тип смывной клавиши", "Тип аэратора", "Тип шпули", "Тип штанги", "Тип ножа", "Тип зажигания", "Тип режущего инструмента", "Тип биотуалета", "Тип выключателя");

$ob = \CIBlockElement::GetList(
    array('ID' => 'ASC'),
    array(
        'IBLOCK_ID' => $IBLOCK_ID,
        //'>ID' => 448913 //387726
    ),
    false,
    false,
    array(
        'ID',
        'NAME',
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'CATALOG_STORE_AMOUNT_4',
        'CATALOG_PRICE_1'
    )
);

$dataHandler = fopen(__DIR__."/upload/dataNewData.csv", "wb");
$strData = implode(";", $ar);
fwrite($dataHandler, str_replace("\"", "\"\"", $strData) . PHP_EOL);
while($item = $ob->GetNextElement()){
    $arFields = $item->GetFields(); // поля элемента
    $arProps = $item->GetProperties(); // свойства элемента

    $arOtherProps = array();
    foreach($arProps as $codeProp => $arItemProp){
        if(in_array($codeProp, $arMapUnicProp) || empty($arItemProp['VALUE']) || ($arItemProp['VALUE'] == 'N' && $arItemProp['USER_TYPE'] == 'SASDCheckbox')){
            continue;
        }

        $arOtherProps[] = $arItemProp['NAME'].": ".strip_tags(\CClass::getNormalValueProp($arItemProp));
    }

    $strOtherProps = implode("|", $arOtherProps);

    $status = $arFields["CATALOG_STORE_AMOUNT_4"] > 0 ? 'В наличии' : 'Не в наличии';
    $img_path = '';
    if($arFields["PREVIEW_PICTURE"]) {
        $img_path = "https://".$GLOBALS['PAGE_DATA']['SITE']['DOMAINS'].CFile::GetPath($arFields["PREVIEW_PICTURE"]);
    }

    $images = [];
    if(!empty($arProps['MORE_PHOTO']['VALUE'])) {
        foreach ($arProps['MORE_PHOTO']['VALUE'] as $imgId) {
            $images[] = "https://".$GLOBALS['PAGE_DATA']['SITE']['DOMAINS'].CFile::GetPath($imgId);
        }
    }

    $brand = strip_tags(\CClass::getNormalValueProp($arProps['MANUFACTURER']));
    $garant = strip_tags(\CClass::getNormalValueProp($arProps['GUARANTEE']));
    $strana = strip_tags(\CClass::getNormalValueProp($arProps['COUNTRY']));
    $EAN = "";
    $number = strip_tags(\CClass::getNormalValueProp($arProps['ARTNUMBER']));
    $naznachenie = strip_tags(\CClass::getNormalValueProp($arProps['NAZNACHENIE']));
    $montaze = strip_tags(\CClass::getNormalValueProp($arProps['MONTAZH']));
    $material = strip_tags(\CClass::getNormalValueProp($arProps['MATERIAL']));
    $FUNKTSIYA_BIDE = strip_tags(\CClass::getNormalValueProp($arProps['FUNKTSIYA_BIDE']));

    $tip = strip_tags(\CClass::getNormalValueProp($arProps['TIP']));
    $TIP_UNITAZA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_UNITAZA']));
    $TIP_AKSESSUARA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_AKSESSUARA']));
    $TIP_MEBELI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MEBELI']));
    $TIP_KLAVISHI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_KLAVISHI']));
    $TIP_RAKOVINY = strip_tags(\CClass::getNormalValueProp($arProps['TIP_RAKOVINY']));
    $TIP_PODVODKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_PODVODKI']));
    $TIP_INSTALLYACII = strip_tags(\CClass::getNormalValueProp($arProps['TIP_INSTALLYACII']));
    $TIP_UTSENKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_UTSENKI']));
    $TIP_LAMPY = strip_tags(\CClass::getNormalValueProp($arProps['TIP_LAMPY']));
    $TIP_SVETILNIKA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_SVETILNIKA']));
    $TIP_RISUNKA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_RISUNKA']));
    $TIP_OSVEZHITELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_OSVEZHITELYA']));
    $TIP_VENTILYATSII = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VENTILYATSII']));
    $TIP_VESHCHESTVA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VESHCHESTVA']));
    $TIP_MYTYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MYTYA']));
    $TIP_MYLA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MYLA']));
    $TIP_SHVA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_SHVA']));
    $TIP_BUMAGI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_BUMAGI']));
    $TIP_VODONAGREVATELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VODONAGREVATELYA']));
    $TIP_VODY = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VODY']));
    $TIP_UMYAGCHITELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_UMYAGCHITELYA']));
    $TIP_MEMBRANNOY_OCHISTKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MEMBRANNOY_OCHISTKI']));
    $TIP_IZMELCHITELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_IZMELCHITELYA']));
    $TIP_MOTORA_IZMELCHITELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MOTORA_IZMELCHITELYA']));
    $TIP_ZAMKA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_ZAMKA']));
    $TIP_VODOROZETKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VODOROZETKI']));
    $TIP_BAKA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_BAKA']));
    $TIP_PRISOEDINENIYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_PRISOEDINENIYA']));
    $TIP_MEMBRANY = strip_tags(\CClass::getNormalValueProp($arProps['TIP_MEMBRANY']));
    $TIP_ZATVORA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_ZATVORA']));
    $TIP_NASOSA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_NASOSA']));
    $TIP_DINAMICHESKOGO_NASOSA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_DINAMICHESKOGO_NASOSA']));
    $TIP_REDUKTORA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_REDUKTORA']));
    $TIP_KONDITSIONERA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_KONDITSIONERA']));
    $TIP_KHLADAGENTA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_KHLADAGENTA']));
    $TIP_FAZY = strip_tags(\CClass::getNormalValueProp($arProps['TIP_FAZY']));
    $TIP_VYVODA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VYVODA']));
    $TIP_PANELNOGO_RADIATORA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_PANELNOGO_RADIATORA']));
    $TIP_OBOGREVATELYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_OBOGREVATELYA']));
    $TIP_KAMERY_SGORANIYA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_KAMERY_SGORANIYA']));
    $TIP_GORELKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_GORELKI']));
    $TIP_OBEMNOGO_NASOSA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_OBEMNOGO_NASOSA']));
    $TIP_KRANA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_KRANA']));
    $TIP_STIRKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_STIRKI']));
    $TIP_SREDSTVA_DLYA_STIRKI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_SREDSTVA_DLYA_STIRKI']));
    $TIP_SMYVNOY_KLAVISHI = strip_tags(\CClass::getNormalValueProp($arProps['TIP_SMYVNOY_KLAVISHI']));
    $TIP_AERATORA = strip_tags(\CClass::getNormalValueProp($arProps['TIP_AERATORA']));
    $tip_shpuli = strip_tags(\CClass::getNormalValueProp($arProps['tip_shpuli']));
    $tip_shtangi = strip_tags(\CClass::getNormalValueProp($arProps['tip_shtangi']));
    $tip_noja = strip_tags(\CClass::getNormalValueProp($arProps['tip_noja']));
    $tip_zajiganiya = strip_tags(\CClass::getNormalValueProp($arProps['tip_zajiganiya']));
    $tip_rejucshego_instrumenta = strip_tags(\CClass::getNormalValueProp($arProps['tip_rejucshego_instrumenta']));
    $tip_biotualeta = strip_tags(\CClass::getNormalValueProp($arProps['tip_biotualeta']));
    $TIP_VIKL = strip_tags(\CClass::getNormalValueProp($arProps['TIP_VIKL']));

    $preview_text = getCsvFormat($arFields['PREVIEW_TEXT']);
    $strOtherProps = getCsvFormat($strOtherProps);
    $naznachenie = getCsvFormat($naznachenie);
    $arFields['NAME'] = getCsvFormat($arFields['NAME']);

//    $sectionName = $arSections[$arFields['IBLOCK_SECTION_ID']] ?? '';
    $ar = array(
        $arFields['ID'],
        $status,
        $brand,
        $arFields['NAME'],
        $GLOBALS['PAGE_DATA']['CATALOG_SECTIONS_ALL'][$arFields['IBLOCK_SECTION_ID']]['NAME'],
        $arFields['CATALOG_PRICE_1'],
        $arFields['CATALOG_PRICE_1'],
        $arFields['CATALOG_CURRENCY_1'],
        $img_path,
        implode("|", $images),
        $strOtherProps,
        $preview_text,
        $garant,
        $strana,
        $EAN,
        $number,
        $arFields["CATALOG_STORE_AMOUNT_4"],
        $naznachenie,
        $montaze,
        $material,
        $FUNKTSIYA_BIDE,
        $tip,
        $TIP_UNITAZA, $TIP_AKSESSUARA, $TIP_MEBELI, $TIP_KLAVISHI, $TIP_RAKOVINY, $TIP_PODVODKI, $TIP_INSTALLYACII, $TIP_UTSENKI, $TIP_LAMPY, $TIP_SVETILNIKA, $TIP_RISUNKA, $TIP_OSVEZHITELYA, $TIP_VENTILYATSII, $TIP_VESHCHESTVA, $TIP_MYTYA, $TIP_MYLA, $TIP_SHVA, $TIP_BUMAGI, $TIP_VODONAGREVATELYA, $TIP_VODY, $TIP_UMYAGCHITELYA, $TIP_MEMBRANNOY_OCHISTKI, $TIP_IZMELCHITELYA, $TIP_MOTORA_IZMELCHITELYA, $TIP_ZAMKA, $TIP_VODOROZETKI, $TIP_BAKA, $TIP_PRISOEDINENIYA, $TIP_MEMBRANY, $TIP_ZATVORA, $TIP_NASOSA, $TIP_DINAMICHESKOGO_NASOSA, $TIP_REDUKTORA, $TIP_KONDITSIONERA, $TIP_KHLADAGENTA, $TIP_FAZY, $TIP_VYVODA, $TIP_PANELNOGO_RADIATORA, $TIP_OBOGREVATELYA, $TIP_KAMERY_SGORANIYA, $TIP_GORELKI, $TIP_OBEMNOGO_NASOSA, $TIP_KRANA, $TIP_STIRKI, $TIP_SREDSTVA_DLYA_STIRKI, $TIP_SMYVNOY_KLAVISHI, $TIP_AERATORA, $tip_shpuli, $tip_shtangi, $tip_noja, $tip_zajiganiya, $tip_rejucshego_instrumenta, $tip_biotualeta, $TIP_VIKL);

    $data = implode(';', $ar);
    fwrite($dataHandler, $data . PHP_EOL);
}
fclose($dataHandler);



function getCsvFormat($str) {
    $retStr = str_replace("\"", "\"\"", $str);
    return "\"{$retStr}\"";
}
