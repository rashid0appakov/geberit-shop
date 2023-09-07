<?
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$catalogId = 76; //id инфоблока продукции shop-jd.ru
$deliveryDays = 3; //количество дней доставки

//$arSelect = Array("IBLOCK_ID", "NAME", "ARTNUMBER" "ACTIVE","ID","PRICE", "DETAIL_PAGE_URL", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_ARTNUMBER", "PROPERTY_EAN_KOD", "DETAIL_PAGE_URL","QUANTITY","CATALOG_GROUP_1");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
$arFilter = Array("IBLOCK_ID"=>IntVal($catalogId));
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>7000), $arSelect);

//ID;LINK;ARTICLE;PRICE;VAT;STOCK;DELIVERY_DAYS
//ID_VAL;LINK_VAL;ARTICLE_VAL;PRICE_VAL;PRICE_VAT;STOCK_VAL;DELIVERY_DAYS_VAL
$colTitleCodes = array('EAN_KOD','LINK','ARTNUMBER','PRICE','PRICE_VAT','QUANTITY','DELIVERY_DAYS');
$colTitle = array('EAN_KOD'=>'EAN код','LINK'=>'Гиперссылка','ARTNUMBER'=>'Артикул','PRICE_VAT'=>'Цена без НДС','PRICE'=>'Цена с НДС','QUANTITY'=>'Наличие товара','DELIVERY_DAYS'=>'Время достваки');

echo "<pre>";

//$colTitle=array('NAME'=>'Название','LINK'=>'URL','QUANTITY'=>'Количество','PRICE'=>'Цена');
//$colTitleCodes=array('NAME','LINK','QUANTITY','PRICE');



while($ob = $res->GetNextElement()){ 
 
 $arFields = $ob->GetFields();  
	$Item["LINK"]="https://shop-jd.ru".$arFields["DETAIL_PAGE_URL"]."?utm_source=JacobDelafon&utm_medium=CommerceConnector";
    //$Item["QUANTITY"]=$arFields["QUANTITY"];
    $Item["QUANTITY"]='10';
    $Item["PRICE"]=$arFields["CATALOG_PRICE_1"];
    $Item["DELIVERY_DAYS"]=$deliveryDays;
 $arProps = $ob->GetProperties();

 foreach($arProps as $v){
 	if(!!$v["VALUE"] && in_array($v["CODE"],$colTitleCodes))
 	{
 		if(is_array($v["VALUE"])) //in_array($v["CODE"],$arArrayProps)
 		{
 			$v["VALUE"]=implode(", ", $v["VALUE"]);
 		}

        $col_val = $v["VALUE"];

        $row_delimiter = "\r\n";

        if( $col_val && preg_match('/[",;\r\n]/', $col_val) )
        {
                // поправим перенос строки
                if( $row_delimiter === "\r\n" ){
                    $col_val = str_replace( "\r\n", '\n', $col_val );
                    $col_val = str_replace( "\r", '', $col_val );
                }
                elseif( $row_delimiter === "\n" ){
                    $col_val = str_replace( "\n", '\r', $col_val );
                    $col_val = str_replace( "\r\r", '\r', $col_val );
                }

                $col_val = str_replace( '"', '""', $col_val ); // предваряем "
                $col_val = '"'. $col_val .'"'; // обрамляем в "
        }

 		$Item[$v["CODE"]]=$col_val;
/*
        array(
								"NAME" => $v["NAME"],
								"VALUE" => $col_val,
								"CODE" => $v["CODE"]
		); */

      /*  if(!in_array($v["CODE"],$colTitleCodes))
        {
            $colTitleCodes[]=$v["CODE"];
            $colTitle[$v["CODE"]]=$v["NAME"];
        } */
 	}

 }
	
$arResult[]=$Item;


}
//print_r($arResult);
//var_dump($arResult[6]["PROPERTIES"]["SOSTAV_TOVARA"]["VALUE"]);
//print_r($colTitleCodes);
//print_r($colTitle);

$csv="";
foreach($colTitleCodes as $code)
{
    $csv .= $colTitle[$code].";";
}
$csv .= "\r\n";
foreach($arResult as $arItem)
{
    
    foreach($colTitleCodes as $code)
    {
        
        $csv .= $arItem[$code].";";
    }   
    $csv .= "\r\n";
}

$csv_handler = fopen ('/var/www/clients/client0/web1/private/shop-jd.ru/feed/jacobdelafon.csv','w');
fwrite ($csv_handler,$csv);
fclose ($csv_handler);
echo "</pre>";
echo 'Data saved to csvfile.csv';
die();






$data_array = array (
            array ('1','2'),
            array ('2','2'),
            array ('3','6'),
            array ('4','2'),
            array ('6','5')
            );

$csv = "col1,col2 \n";//Column headers
foreach ($data_array as $record){
    $csv.= $record[0].','.$record[1]."\n"; //Append data to csv
    }

$csv_handler = fopen ('jacobdelafon.csv','w');
fwrite ($csv_handler,$csv);
fclose ($csv_handler);

echo 'Data saved to csvfile.csv';








/*
"name : Название";"article : Артикул";"hidden : Скрыто";"kind_id : ID";"price : Цена";"cf_yml_categoryidnew : YML - categoryID";"cf_gabarity : Габариты";"cf_cvet : Цвет";"cf_cvetovoe_ispolnenie : Цветовое исполнение";"cf_tip_mebeli_298 : Тип мебели";"cf_forma_vanny : Форма ванны";"cf_dlina_vanny : Длина ванны";"cf_tip_ograzdenia : Тип Ограждения";"cf_verhniy_dush_dush_paneli : Верхний душ";"cf_link_na_tovar : Линк на товар";"cf_razmer : Размер (Ш)";"cf_tip_stekla : Тип Стекла";"cf_razmer_g : Размер (Г)";"cf_smesitel_dush_paneli : Смеситель";"cf_ean_code : EAN CODE";"cf_sirina_vanny : Ширина ванны";"cf_material_poddona : Материал поддона";"cf_ustanovka : Установка";"cf_tip_smesitelya_dush_paneli : Тип смесителя";"cf_nalicie_rucek : Наличие ручек";"cf_forrma_poddona : Форма поддона";"cf_tip_santehniki : Тип сантехники";"cf_belevaa_korzina : Бельевая корзина";"cf_gidromassaznoe_oborudovanie : Гидромассажное оборудование";"cf_ustanovka_santehnica : Установка";"cf_tip_poddona_kabiny : Тип поддона"
"Комплект 5 в 1 (унитаз Jacob Delafon Escale + инсталляция Geberit)";"E1306-00  E70004  458.124.21.1";0;131263009;33299.00;52136409;;;;;;;;;www.stroymag66.ru/magazin/product/komplekt-4-v-1-unitaz-jacob-delafon-patio-installyaciya-geberit-1;37.5;;60;;;;;;;;;унитаз+инсталляция;;;подвесная;

*/
?>




