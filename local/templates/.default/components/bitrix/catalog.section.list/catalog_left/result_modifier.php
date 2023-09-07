<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$resizeHeight = $resizeWidth = 230;

global $man_show;
// $series_ids = array();
// $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
// $arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_BRAND' => $man_show);
// $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
// while($ob = $res->GetNextElement())
// {
//     $arFields = $ob->GetFields();
//     $series_ids[] = $arFields['ID'];
// }

// if ($USER->IsAdmin()){
// 	echo "<pre>";
// 	var_dump($man_show);	
// 	echo "</pre>";
// }

foreach ($arResult["SECTIONS"] as $k=>&$arSection)
{

	$imageIds = array();
	

	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "SECTION_ID"=>$arSection['ID'], "ACTIVE"=>"Y", 'INCLUDE_SUBSECTIONS' => "Y", 'PROPERTY_MANUFACTURER' => $man_show, "!PROPERTY_DISCONTINUED" => "Y"); // series_ids
	//var_dump($arFilter);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	$count = $res->SelectedRowsCount();
	if($count<=0){
		unset($arResult["SECTIONS"][$k]);
		continue;
	}else{
		$arSection['ELEMENT_CNT'] = $count;
	}

	if ($arSection["ELEMENT_CNT"]<=0) unset($arResult["SECTIONS"][$k]);


	//var_dump($arSection["ELEMENT_CNT"]);


	if (!!$arSection["PICTURE"]["ID"]) 
	{
		$imageIds[] = $arSection["PICTURE"]["ID"];
	}
	foreach ($arSection["UF_IMAGES"] as $imgId)
	{
		$imageIds[] = $imgId;
	}

	$arSection["SHOW_IMAGES"] = array();
	foreach ($imageIds as $imgId)
	{
		$arSection["SHOW_IMAGES"][] = CFile::ResizeImageGet($imgId, array('width' => $resizeWidth, 'height' => $resizeHeight), BX_RESIZE_IMAGE_PROPORTIONAL);   
	}
}
unset($arSection);