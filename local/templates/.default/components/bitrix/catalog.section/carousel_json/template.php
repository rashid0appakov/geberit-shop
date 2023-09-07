<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//pr($arParams);
//pr($arResult["NAV_RESULT"]);

$templateData["ITEMS"] = [];
if($arResult["NAV_RESULT"]->NavPageCount >= $arResult["NAV_RESULT"]->PAGEN and $arResult["NAV_RESULT"]->NavPageNomer == $arParams['PAGE'])
{
	foreach ($arResult["ITEMS"] as &$arItem)
	{
		$item = '<div class="swiper-slide" style="width: 270px; margin-right: 30px;">';
		ob_start();
		$APPLICATION->IncludeComponent(
			"bitrix:catalog.item",
			"product",
			array(
				"RESULT" => array(
					"ITEM" => $arItem,
					"AREA_ID" => $areaId,
				),
				"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
		$item .= ob_get_contents();
		ob_end_clean();
		$item .= '</div>';
		$templateData["ITEMS"][] = $item;
	}
}
