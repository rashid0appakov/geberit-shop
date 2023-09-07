<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 208;
$arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 140;

if(is_array($arResult["PREVIEW_PICTURE"])) {
	if($arResult["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["PREVIEW_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	}
} elseif(is_array($arResult["DETAIL_PICTURE"])) {
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	} else {
		$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = $arResult["DETAIL_PICTURE"];
	}
}

$arPhotos = array();
if(is_array($arResult["PROPERTIES"]["FOTO"]["VALUE"])) {
	foreach($arResult["PROPERTIES"]["FOTO"]["VALUE"] as $photoKey => $photoValue) {
		$arFileTmp = CFile::GetFileArray($photoValue);
		$arPhotos[] = array(
			"SRC" => $arFileTmp["SRC"],
			"WIDTH" => $arFileTmp["WIDTH"],
			"HEIGHT" => $arFileTmp["HEIGHT"],
		);
		unset($arFileTmp);
	}
	unset($photoKey, $photoValue);
	$arResult["PHOTOS"] = $arPhotos;
}

/*
echo "<pre>";
print_r($arResult["PROPERTIES"]);
echo "</pre>";
*/
?>