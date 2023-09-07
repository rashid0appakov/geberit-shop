<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$arJS = [
	'/include/footer/middle_text.php',
	'/include/template_popup_region1.php',
	'/include/template_popup_region2.php',
];

$check = false;

foreach($arJS as $file)
{
	if(strpos($arResult["FILE"], $file))
	{
		$check = true;
		break;
	}
}

if($check)
{
	ob_start();
	include($arResult["FILE"]);
	$html = ob_get_contents();
	ob_end_clean();	
	
	$uniqueId = $this->randString();
	$js = '<div id="insert_'.$uniqueId.'"></div>'."\n";
	$js .= '<script>'."\n";
	$js .= 'BX.ready(function(){'."\n";
	$js .= '$("#insert_'.$uniqueId.'").html(base64_decode("'.base64_encode($html).'"));'."\n";
	$js .= '});'."\n";
	$js .= '</script>'."\n";
	echo $js;
}
else
{
	if($arResult["FILE"] <> '')
	{
		include($arResult["FILE"]);
	}
}
