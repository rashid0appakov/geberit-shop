<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$resizeHeight = $resizeWidth = 230;

foreach ($arResult["SECTIONS"] as &$arSection)
{
	$imageIds = array();

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