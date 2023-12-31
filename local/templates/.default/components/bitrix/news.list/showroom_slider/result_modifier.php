<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


foreach($arResult["ITEMS"] as $key => $arItem) {
	if(is_array($arItem["PREVIEW_PICTURE"])) {
		$arFilter = '';

		$arFileTmp = CFile::ResizeImageGet(
			$arItem["PREVIEW_PICTURE"],
			array("width" => 90, "height" => 59),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arResult["ITEMS"][$key]['PICTURE_PREVIEW_SMALL'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
}

