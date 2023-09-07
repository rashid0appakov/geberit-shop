<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity;
use \Bitrix\Highloadblock as HL;

include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_functions.php');

// // начало - https://top-santehnika.bitrix24.ru/workgroups/group/10/tasks/task/view/3734/
// global $man_show, $locId;


// $arResult['no_index'] = false;
// if ($USER->IsAdmin()){
// 	if (!in_array($arResult['PROPERTIES']['MANUFACTURER']['VALUE'], $man_show)){
// 		var_dump($arResult['PROPERTIES']['MANUFACTURER']['VALUE']);
// 		$arResult['no_index'] = true;
// 		$APPLICATION->SetPageProperty("robots", "noindex, nofollow");
// 	}
// 	//die;
// }

// // конец - https://top-santehnika.bitrix24.ru/workgroups/group/10/tasks/task/view/3734/




$arOdds = CClass::getProductOds();
$arSections = CClass::getCatalogSection();

$arSection  = $arSections[$arResult['SECTION']['ID']];


if (!!$arResult["PROPERTIES"]["PARAMS"]["VALUE"] && !empty($arOdds))
	foreach($arResult["PROPERTIES"]["PARAMS"]["VALUE"] AS &$oddID)
		$arResult["PARAMS_PROPERTIES"][]	= $arOdds[$oddID];

// -- Get installation price ------------------------------------------------ //
$installationPrice  = $arSection['UF_INSTALLATION'];
$parentId		   = $arSection['IBLOCK_SECTION_ID'];
$count = 0;
while (!$installationPrice && $parentId) {
	$count++;
	if ($count>100) break;
	$parentId   = $arSections[$arSection['IBLOCK_SECTION_ID']]['IBLOCK_SECTION_ID'];
	$installationPrice = $arSections[$arSection['IBLOCK_SECTION_ID']]['UF_INSTALLATION'];
}


if ($installationPrice)
	$arResult['INSTALLATION_PRICE'] = CCurrencyLang::CurrencyFormat($installationPrice, 'RUB', false);

// global $USER;
// if ($USER->IsAdmin()){
// 	die(date('d.m.Y H:i:s'));
// }

// -- Show parameters short list -------------------------------------------- //
if(!empty($arResult['PROPERTIES']['ARTNUMBER']['VALUE'])){
	if(SITE_ID == 'l1')
	{
		$arResult['SHOW_IN_DESCRIPTION'][$arResult['PROPERTIES']['ARTNUMBER']['NAME']] = $arResult['PROPERTIES']['ARTNUMBER']['VALUE'];
	}
}
if (!empty($arSection['DISPLAY_PARAMS'])){
	foreach ($arSection['DISPLAY_PARAMS'] AS &$code){
		switch($code)
		{
			case 'ID':
				$arResult['SHOW_IN_DESCRIPTION'][GetMessage('CT_PRODUCT_CODE')] = $arResult['ID'];
				break;
			default:
				if ($arResult['PROPERTIES'][$code])
				{
					$arResult['SHOW_IN_DESCRIPTION'][CClass::getNormalNameProp($arResult["PROPERTIES"][$code]["NAME"])] = CClass::getNormalValueProp($arResult['PROPERTIES'][$code]);
				}
		}
	}
}




//Если в текущей разделе нету свойств, ищем в родительском
if(count($arResult['SHOW_IN_DESCRIPTION']) == 0){

	$arSectionParent  = $arSections[$arResult['SECTION']['IBLOCK_SECTION_ID']];
	
	if (!empty($arSectionParent['DISPLAY_PARAMS'])){
		foreach ($arSectionParent['DISPLAY_PARAMS'] AS &$code){
			switch($code)
			{
				case 'ID':
					$arResult['SHOW_IN_DESCRIPTION'][GetMessage('CT_PRODUCT_CODE')] = $arResult['ID'];
					break;
				default:
					if ($arResult['PROPERTIES'][$code])
					{
						$arResult['SHOW_IN_DESCRIPTION'][CClass::getNormalNameProp($arResult["PROPERTIES"][$code]["NAME"])] = CClass::getNormalValueProp($arResult['PROPERTIES'][$code]);
					}
			}
		}
	}
}

// -- Get Smart filter links for item properties ---------------------------- //
$propIDs	= [];
$arSectionProperties	= CIBlockSectionPropertyLink::GetArray($arParams['IBLOCK_ID'], $arResult['SECTION']['ID']);
if (!empty($arSectionProperties)){
	foreach($arSectionProperties AS &$arLink){
		if ($arLink["SMART_FILTER"] !== "Y")
			continue;
		$propIDs[] = $arLink['PROPERTY_ID'];
	}
	/*
	if (!empty($propIDs))
		foreach($arResult['DISPLAY_PROPERTIES'] AS $key => &$value)
			if (in_array($value['ID'], $propIDs))
				$arResult['DISPLAY_PROPERTIES'][$key]['FILTER_LINK'] =
					'/catalog/'.$arResult['SECTION']['CODE'].'/?arrFilter_'.$value['ID']
					.'_'.abs(crc32(htmlspecialcharsbx($value['VALUE_ENUM_ID'])))
					.'=Y&set_filter=';
	*/
}

//disount
use Bitrix\Sale\Internals\DiscountTable;
Loader::includeModule("iblock");

$res = DiscountTable::getList()->fetchAll();
$is_discount="N";
foreach($res as $arItem){
	if($arItem["LID"]!=SITE_ID || $arItem["ACTIVE"]!="Y") { continue; }
	preg_match("/in_array\((\d*)\,.*\[\\'(.*)\\'\]\[\\'(.*)\\'\]\)/U", $arItem["APPLICATION"], $discountArr);
	if(!empty($discountArr) && $arItem['LID']==SITE_ID){
		$discount_el_id[]=$discountArr[1];
		$discount_iblock_type=$discountArr[2];
		$discount_property=explode("_",$discountArr[3]);
		$discount_name=$arItem["NAME"];
		$discount_value[]=$arItem['SHORT_DESCRIPTION_STRUCTURE']['VALUE'];
		$is_discount="Y";
	}
}

if($is_discount=="Y"){
    $res1 = CIBlockProperty::GetByID($discount_property[1]);
    if($ar_res = $res1->GetNext())
        $CODE=$ar_res["CODE"];
    if(in_array($arResult["PROPERTIES"][$CODE]["VALUE"],$discount_el_id)){
        $k_d=array_search($arResult["PROPERTIES"][$CODE]["VALUE"],$discount_el_id);
	    $arResult["DISCOUNT_BASKET"]=array("IS_DISCOUNT"=>"Y","VALUE"=>$discount_value[$k_d]);
	    }

}

// PRODUCT SET
$prices = CCatalogProduct::GetOptimalPrice($arResult["ID"]);

$arResult["MIN_PRICE"]['VALUE'] = $prices['RESULT_PRICE']['BASE_PRICE'];
$arResult["MIN_PRICE"]['DISCOUNT_VALUE'] = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
if (CCatalogProductSet::isProductInSet($arResult["ID"]))
{
	$allSets = CCatalogProductSet::getAllSetsByProduct($arResult["ID"], CCatalogProductSet::TYPE_SET);

	/*$prices = CCatalogProduct::GetOptimalPrice($arResult["ID"]);
	$arResult["MIN_PRICE"]['VALUE'] = $prices['RESULT_PRICE']['BASE_PRICE'];
	$arResult["MIN_PRICE"]['DISCOUNT_VALUE'] = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];*/

	if(is_array($allSets)){	
		foreach($allSets as $arrItemsKits){
			foreach($arrItemsKits['ITEMS'] as $oneKits){

				//Информация о комплектующих
				$resSetInfo = CIBlockElement::GetList(array('sort' => 'asc'),array("ID" => $oneKits['ITEM_ID'], "IBLOCK_ID" => $arParams['IBLOCK_ID']),false,false,array("PROPERTY_ARTNUMBER", "PROPERTY_SOSTAV_TOVARA", "NAME", "ACTIVE", "DETAIL_PAGE_URL", "PREVIEW_PICTURE"));
				if($arSetInfo = $resSetInfo->GetNext()) {
					if($arSetInfo['ACTIVE'] == 'N'){
						continue;
					}

					//Артикулы комплектующих
					$arNumbers[] = $arSetInfo['PROPERTY_ARTNUMBER_VALUE'];
				}
				
				//Ид комплектующих
				$arSetIds[] = $oneKits['ITEM_ID'];

				//Цена комплектующих
				$arSetPrice = CPrice::GetBasePrice($oneKits['ITEM_ID']);
				$setPrice = $arSetPrice['PRICE'];
				
				$sumOrigPice += $setPrice * $oneKits['QUANTITY'];
				
				//Скидка на комплектующие
				$arDiscountsSets = CCatalogDiscount::GetDiscountByProduct(
					$oneKits['ITEM_ID'],
					$USER->GetUserGroupArray(),
					"N",
					array(),
					SITE_ID
				);
				
				$setPrice = CCatalogProduct::CountPriceWithDiscount($setPrice, "RUB", $arDiscountsSets);
				
				$setPrice = $setPrice * $oneKits['QUANTITY'];
				$sumPrice += $setPrice;
				
				//Собираем информацию об объеме поставки
				$arResult['SCOPE_DEL'][$oneKits['ITEM_ID']]['PREVIEW_PICTURE'] = $arSetInfo['PREVIEW_PICTURE'];
				$arResult['SCOPE_DEL'][$oneKits['ITEM_ID']]['NAME'] = $arSetInfo['NAME'];
				$arResult['SCOPE_DEL'][$oneKits['ITEM_ID']]['URL'] = $arSetInfo['DETAIL_PAGE_URL'];
				$arResult['SCOPE_DEL'][$oneKits['ITEM_ID']]['PRICE'] = $arSetPrice['PRICE'];
				$arResult['SCOPE_DEL'][$oneKits['ITEM_ID']]['STR_SCOPE'] = $arSetInfo['PROPERTY_SOSTAV_TOVARA_VALUE'];

			}
		}
	}
foreach ($allSets as $key => $value) {
	foreach ($value["ITEMS"] as $key1 => $value1) {
		if(!in_array($value1["ITEM_ID"], $arSetIds)){
			unset($allSets[$key]["ITEMS"][$key1]);
		}
	}
}
	$currentSet = false;
	foreach ($allSets as $set) {
		if ($set["ACTIVE"] == "Y")
		{
			$currentSet = $set;
			break;
		}
	}

	unset($set, $allSets);
	if (!empty($currentSet)){
		$setItemIds = array();
		foreach ($currentSet["ITEMS"] AS $arItem)
			$setItemIds[] = $arItem["ITEM_ID"];

		$select = array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DETAIL_PAGE_URL",
			"DETAIL_PICTURE",
			"PROPERTY_TYPE_FOR_SET",
			"PROPERTY_SET_ITEMS",
			"PROPERTY_ARTNUMBER",
		);

		$setItems = array();
		$dbSetItems = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $setItemIds,
				"IBLOCK_ID" => $arParams["IBLOCK_ID"]
			),
			false,
			false,
			$select
		);

		while ($arSetItem = $dbSetItems->GetNext()){

			$arPrice = CCatalogProduct::GetOptimalPrice($arSetItem["ID"]);
			$ar_res = CCatalogProduct::GetByIDEx($arSetItem["ITEM_ID"]);
			$setItems[$arSetItem["ID"]] = array(
				"ID" => $arSetItem["ID"],
				"NAME" => $arSetItem["NAME"],
				"URL" => $arSetItem["DETAIL_PAGE_URL"],
				"IMAGE" => $arSetItem["DETAIL_PICTURE"],
				"PRICE" => $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"],
				"OLD_PRICE" => $arPrice["RESULT_PRICE"]["BASE_PRICE"],
				"ARTNUMBER" => $arSetItem["PROPERTY_ARTNUMBER_VALUE"],
				"QUANTITY" => $ar_res['PRODUCT']['QUANTITY']
			);
		}

		$arResult["SET_ITEMS"] = array();
		foreach ($currentSet["ITEMS"] AS $arItem){
			$arItem["PRODUCT"] = $setItems[$arItem["ITEM_ID"]];
			if ($arItem["PRODUCT"]["IMAGE"])
				$arItem["PRODUCT"]["IMAGE"] = CFile::ResizeImageGet(
					$arItem["PRODUCT"]["IMAGE"],
					array('width' => 33, 'height' => 33),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
			$arResult["SET_ITEMS"][] = $arItem;
		}

	}
}

$arAddItemsId = array();

$cacheProps = [
	'KOMLP' => 'KOMLP_CACHE',
	'ADDITIONAL' => 'ADDITIONAL_CACHE',
	'SPARE' => 'SPARE_PARTS_CACHE',
	'COLLECTION' => 'COLLECTION_ITEMS',
	'SIMILAR' => 'SIMILAR_ITEMS',
	'IN_SET' => 'IN_SET_CACHE'
];

foreach($cacheProps as $prop1=>$prop2)
{
	if(!empty($arResult['PROPERTIES'][$prop2]['VALUE']))
	{
		$arResult['PROPERTIES'][$prop2]['VALUE'] = json_decode($arResult['PROPERTIES'][$prop2]['~VALUE'], true);
		$arAddItemsId[$prop1] = [];
		foreach($arResult['PROPERTIES'][$prop2]['VALUE'] as &$sec)
		{
			$i = 0;
			foreach($sec as $id)
			{
				$i ++;
				/*if($i > 8)
				{
					break;
				}*/
				$arAddItemsId[$prop1][] = $id;
			}
		}
	}
}

//Получаем все доп. товары
$arResAddItems = GetAdditionalsNew($arAddItemsId, $arOdds, $arSections, [], $arParams["IBLOCK_ID"]);

//Раскидываем всякие доп. товары
$arResult['SIMILAR'] = $arResAddItems['SIMILAR'];
$arResult['COLLECTION'] = $arResAddItems['COLLECTION'];
$arResult['SPARE'] = $arResAddItems['SPARE'];
$arResult['ADDITIONAL'] = $arResAddItems['ADDITIONAL'];
$arResult['KOMLP'] = $arResAddItems['KOMLP'];

if ($arResult['DETAIL_PICTURE']['ID'] ){
	array_unshift( $arResult["MORE_PHOTO"], $arResult['DETAIL_PICTURE'] );
	//$arResult["MORE_PHOTO"][] = $arResult['DETAIL_PICTURE']['ID'];
}

// Документы
$arResult['DOCS'] = array();

if (!empty($arResult['PROPERTIES']['FILES_DOCS']['VALUE']))
	foreach($arResult['PROPERTIES']['FILES_DOCS']['VALUE'] AS $item ) {
		//$arResult['DOCS'][] = CFile::GetPath( $item );

		$rsFile = CFile::GetByID( $item );
		$arFile = $rsFile->Fetch();
		$arFile['SIZE'] = strtoupper(pathinfo($arFile['FILE_NAME'],  PATHINFO_EXTENSION)).' '.CFile::FormatSize($arFile['FILE_SIZE']);
		$arFile['PATH'] = CFile::GetPath( $item );
		$arResult['DOCS'][] = $arFile;
	}

//К документам добавляем инструкцию
if (!empty($arResult['PROPERTIES']['INSTRUKTSIYA']['VALUE'])){
	$arAddDocs = array();
	
	$arAddDocs['PATH'] = $arResult['PROPERTIES']['INSTRUKTSIYA']['VALUE'];
	$arAddDocs['DESCRIPTION'] = "Инструкция";
	$arAddDocs['SIZE'] = "Ссылка";
	
	$arResult['DOCS'][] = $arAddDocs;
}
	
if (!empty($arResult["MORE_PHOTO"]))
	foreach($arResult["MORE_PHOTO"] AS &$arItem){
		if($arItem['CONTENT_TYPE'] == 'image/gif'){
			$arResult['PHOTO'][$arItem['ID']] = array('SMALL' => array('src' => $arItem['SRC']), 'MEDIUM' => array('id' => $arItem['ID'], 'src' => $arItem['SRC']));
		}
		else{
			$arFile = CFile::GetFileArray($arItem['ID']);
			if ($arFile)
				$arResult['PHOTO'][$arItem['ID']]	= [
					'SMALL' => CFile::ResizeImageGet(
						$arFile,
						array('width' => 67, 'height' => 67),
						BX_RESIZE_IMAGE_PROPORTIOONAL,
						true
					),
					'MEDIUM' => CFile::ResizeImageGet(
						$arFile,
						array('width' => 535, 'height' => 450),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					)/*,
					'BIG' => CFile::ResizeImageGet(
						$arFile,
						array('width' => 1280, 'height' => 1024),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					)*/
				];
		}
		
	}

// ATTENTION!!!
// кастомизация монобрендов для блока вкладок
if (isset($arParams["IS_BRAND_SITE"]) && "Y" == $arParams["IS_BRAND_SITE"])
{
	$arManufacturer = Array();
	if (!empty($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"]) && !empty($arResult["PROPERTIES"]["MANUFACTURER"]["LINK_IBLOCK_ID"]))
	{
		$arManufacturer = \CIBlockElement::GetByID($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"])->Fetch();
	}
	
	foreach ($arResult["PROPERTIES"] as $key => $arProp)
	{
		if ("TIP" == $key)
		{
			if (!empty($arResult["SECTION"]["SECTION_PAGE_URL"]))
			{
				$res = \CIBlockSection::GetList(
						Array(),
						Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $arResult["SECTION"]["ID"]),
						false,
						Array("ID", "IBLOCK_ID", "UF_NAME_SINGULAR")
					);
				if (($ar_res = $res->Fetch()) && !empty($ar_res["UF_NAME_SINGULAR"]))
					$name = mb_strtolower($ar_res["UF_NAME_SINGULAR"]);
				elseif (!empty(($arResult['SECTION']['NAME'])))
					$name = mb_strtolower($arResult['SECTION']['NAME']);
				else
					$name = CClass::getNormalValueProp($arProp);
				
				$title = $name;
				if (!empty($arManufacturer))
				{
					if (!empty($arManufacturer["NAME"]))
						$title .= " " . $arManufacturer["NAME"];
				}
				
				$arResult["PROPERTIES"][$key]["VALUE_CUSTOMIZED"] = "<a href='{$arResult['SECTION']['SECTION_PAGE_URL']}' title='{$title}' target='_blank'>{$name}</a>";
			}
		}
		elseif ("MANUFACTURER" == $key && !empty($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"]) && 
							!empty($arParams["MAIN_SITE_BRAND"]) && is_array($arParams["MAIN_SITE_BRAND"]) && 
							in_array($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"], $arParams["MAIN_SITE_BRAND"]) && !empty($arManufacturer))
		{
			$name = $arManufacturer["NAME"];
			$title = "Официальный сайт " . $arManufacturer["NAME"];
			
			$arResult["PROPERTIES"][$key]["VALUE_CUSTOMIZED"] = "<a href='/' title='{$title}' target='_blank'>{$name}</a>";
		}
	}
}

// =============== REVIEWS ===================
$obReviewsResult = CIBlockElement::GetList(
    ["SORT" => "ASC"],
    [
        "IBLOCK_ID" => 107,
        "ACTIVE"    => 'Y',
    ],
    false, false,
    ["IBLOCK_ID", "ID", "NAME", "DETAIL_TEXT", "DATE_CREATE"]
);

while($obFeedback = $obReviewsResult->GetNextElement()){

    $arProps  = $obFeedback->GetProperties();

    if ((int)$arResult["ID"] === (int)$arProps["product_id"]["VALUE"]){
        $arFields = $obFeedback->GetFields();
        $arResult["reviews"][] = [
            "message"    => $arFields["DETAIL_TEXT"],
            "created_at" => $arFields["DATE_CREATE"]
        ];
    }
}
// =============== REVIEWS ===================

$this->__component->SetResultCacheKeys(array('MIN_PRICE', 'DETAIL_PAGE_URL', 'DISCOUNT_BASKET'));

//pr($arResult);
