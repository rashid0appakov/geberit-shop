<?php
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

CModule::IncludeModule('highloadblock');
$arHLBlockCountry = Bitrix\Highloadblock\HighloadBlockTable::getById(4)->fetch();
$obEntityCountry = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockCountry);
$strEntityDataClassCountry = $obEntityCountry->getDataClass();

$arHLBlockGUARANTEE = Bitrix\Highloadblock\HighloadBlockTable::getById(7)->fetch();
$obEntityGUARANTEE = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockGUARANTEE);
$strEntityDataClassGUARANTEE = $obEntityGUARANTEE->getDataClass();

$arHLBlockTSVET = Bitrix\Highloadblock\HighloadBlockTable::getById(6)->fetch();
$obEntityTSVET = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockTSVET);
$strEntityDataClassTSVET = $obEntityTSVET->getDataClass();

$el = new CIBlockElement;

$IBLOCK_ID = 15;

$dataHandler = fopen(__DIR__."/upload/products.csv", "r");

$row = 0;
while (($data = fgetcsv($dataHandler, 0, ";")) !== FALSE) {
	
	$row++;
	if($row == 1 || $data[2] == ''){
		continue;
	}
	
	//Поиск товара
	$URL = $data[2];
	
	//Свойства
	//Справочники
	$country = $data[155];
	$garantia = $data[188];
	$tsvet = $data[135];

	//Поиск товара
	$ob = CIBlockElement::GetList(
        array(),
        array(
            'IBLOCK_ID' => $IBLOCK_ID,
			'CODE' => $URL,
        ),
        false,
        false,
        array(
            'ID',
            'IBLOCK_ID'
        )
    );
    if ($item = $ob->Fetch())
    {
        $productId = $item['ID'];
    }
	
	//Страна
	$rsDataCountry = $strEntityDataClassCountry::getList(array(
		'select' => array('UF_XML_ID'),
		'order' => array('ID' => 'ASC'),
		'filter' => array('UF_NAME' => $country)
	));
	if($arItemCountry = $rsDataCountry->Fetch()) {
		$countryXML_ID = $arItemCountry['UF_XML_ID'];
	}
	else{
		$XML_ID = CUtil::translit($country, "ru", $params);
		$arElementFields = array(
			'UF_NAME' => $country,
			'UF_XML_ID' => $XML_ID,
		);
		$obResult = $strEntityDataClassCountry::add($arElementFields);
		$countryXML_ID = $XML_ID;
	}
	
	//Гарантия
	$rsDataGUARANTEE = $strEntityDataClassGUARANTEE::getList(array(
		'select' => array('UF_XML_ID'),
		'order' => array('ID' => 'ASC'),
		'filter' => array('UF_NAME' => $garantia)
	));
	if($arItemGUARANTEE = $rsDataGUARANTEE->Fetch()) {
		$garantiaXML_ID = $arItemGUARANTEE['UF_XML_ID'];
	}
	else{
		$XML_ID = CUtil::translit($garantia, "ru", $params);
		$arElementFields = array(
			'UF_NAME' => $garantia,
			'UF_XML_ID' => $XML_ID,
		);
		$obResult = $strEntityDataClassGUARANTEE::add($arElementFields);
		$garantiaXML_ID = $XML_ID;
	}
	
	//Цвет
	$rsDataTSVET = $strEntityDataClassTSVET::getList(array(
		'select' => array('UF_XML_ID'),
		'order' => array('ID' => 'ASC'),
		'filter' => array('UF_NAME' => $tsvet)
	));
	if($arItemTSVET = $rsDataTSVET->Fetch()) {
		$tsvetXML_ID = $arItemTSVET['UF_XML_ID'];
	}
	else{
		$XML_ID = CUtil::translit($tsvet, "ru", $params);
		$arElementFields = array(
			'UF_NAME' => $tsvet,
			'UF_XML_ID' => $XML_ID,
		);
		$obResult = $strEntityDataClassTSVET::add($arElementFields);
		$tsvetXML_ID = $XML_ID;
	}

	//-------------------Добавление свойств---------------------------
	$arProps = array();
	
	//Справочники
	$arProps["COUNTRY"] = $countryXML_ID;
	$arProps["GUARANTEE"] = $garantiaXML_ID;
	$arProps["TSVET"] = $tsvetXML_ID;
	
	//Добавляем свойства к товару
	CIBlockElement::SetPropertyValuesEx($productId, $IBLOCK_ID, $arProps);
}
fclose($dataHandler);
?>
<?
function __SetListPropertyValue(&$arProps, $propCode, $propId, $value)
{
	if (empty($value))
    {
        $arProps[$propCode] = false;
    }
    else
    {
		$arValues = explode("|", $value);
		
		if(count($arValues) >= 2){
			foreach($arValues as $value){
				$propValue = false;
				$ob = CIBlockPropertyEnum::GetList(
					array(),
					array(
						"IBLOCK_ID" => $IBLOCK_ID,
						"PROPERTY_ID" => $propId,
						"VALUE" => $value,
					)
				);
				if ($item = $ob->Fetch())
				{
					$propValue = $item["ID"];
				}
				else
				{
					$propValue = CIBlockPropertyEnum::Add(array(
						"PROPERTY_ID" => $propId,
						"VALUE" => $value,
					));
				}

				$arProps[$propCode][] = $propValue;
			}
		}
		else{
			$propValue = false;
			$ob = CIBlockPropertyEnum::GetList(
				array(),
				array(
					"IBLOCK_ID" => $IBLOCK_ID,
					"PROPERTY_ID" => $propId,
					"VALUE" => $value,
				)
			);
			if ($item = $ob->Fetch())
			{
				$propValue = $item["ID"];
			}
			else
			{
				$propValue = CIBlockPropertyEnum::Add(array(
					"PROPERTY_ID" => $propId,
					"VALUE" => $value,
				));
			}

			$arProps[$propCode][] = $propValue;
		}
    }
}
?>