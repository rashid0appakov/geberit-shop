<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$return = '';
if(!empty($arParams["PATH"]))
{
	if(strpos($arParams["PATH"], '/') === 0)
	{
		$arParams["PATH"] = substr($arParams["PATH"], 1);
	}
	if(!empty($arParams["PATH"]))
	{
		$arPath = [
			$_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/',
			$_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/',
			$_SERVER['DOCUMENT_ROOT'].'/',
		];
		foreach($arPath as $path)
		{
			$path .= $arParams["PATH"];
			if(file_exists($path))
			{
				$return = include($path);
				break;
			}
		}
	}
}

return $return;
?>