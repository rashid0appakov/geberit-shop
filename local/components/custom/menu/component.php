<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arParams['TYPE']))
{
	$arParams['TYPE'] = 'catalog';
}
$key = strtoupper($arParams['TYPE']);
if(!isset($GLOBALS['PAGE_DATA']['MENU'][$key]))
{
	$key = 'CATALOG';
}
$arParams['KEY'] = $key;
if(empty($arParams["MAX_LEVEL"]))
{
	$arParams["MAX_LEVEL"] = 1;
}

$arResult = [];

foreach($GLOBALS['PAGE_DATA']['MENU'][$key]['ITEMS'] as $arItem)
{
	$arResult[] = [
		"TEXT" => $arItem[0],
		"LINK" => $arItem[1],
		"PARAMS" => $arItem[3],
		"DEPTH_LEVEL" => (isset($arItem[3]["DEPTH_LEVEL"]) ? $arItem[3]["DEPTH_LEVEL"] : 1)
	];
}

$return = '';

echo '<!-- custom.menu, key = '.$key.' start -->';

$this->includeComponentTemplate();

echo '<!-- custom.menu, key = '.$key.' end -->';

return $return;
?>