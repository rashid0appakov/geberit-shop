<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//pr($arResult);
/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

$strReturn .= '<ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++){
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if(strpos($arResult[$index]["LINK"], '/blog/') === 0)
	{
		$arResult[$index]["LINK"] = str_replace('/blog/', '/', $arResult[$index]["LINK"]);
	}
	$arrow = ($index > 0? '<i class="fa fa-angle-right"></i>' : '');

if(isset($_GET['PAGEN_4']) && $_GET['PAGEN_4']>1 && $index == $itemSize-1){
	global $APPLICATION;
	$dir = $APPLICATION->GetCurDir();
	$arResult[$index]["LINK"]=$dir;
}

		if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1||(isset($_GET['PAGEN_4']) && $_GET['PAGEN_4']>1) ){
			$st='';
			if(isset($_GET['PAGEN_4']) && $_GET['PAGEN_4']>1){
				$st = ' style="text-decoration: underline;"';
			}
			$strReturn .= '<li class="item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"> <a href="'.$arResult[$index]["LINK"].'"'.$st.'itemprop="item"><span itemprop="name">'.$title.'</span></a><meta itemprop="position" content="'.$index.'"></li>';
		}else{
			$strReturn .= '<li class="item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span>'.$title.'</span>';
			$strReturn .= '<meta itemprop="item" content="https://'.$_SERVER['SERVER_NAME'].$arResult[$index]["LINK"].'">';
			$strReturn .= '<meta itemprop="name" content="'.$title.'"><meta itemprop="position" content="'.$index.'"></li>';
		}
}

$strReturn .= '</ul>';

return $strReturn;