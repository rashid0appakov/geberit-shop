<?php
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
		die();

	//determine if child selected
	$bWasSelected = false;
	$arParents = array();
	$depth = 1;
	$arLinks	= [];
	$i = 0;
	foreach($arResult AS $k => $arMenu){
		if (in_array($arMenu['LINK'], $arLinks)){
			unset($arResult[$k]);
			continue;
		}

		$arLinks[] = $arMenu['LINK'];
	}

	foreach($arResult AS $k => $arMenu){
		$depth = $arMenu['DEPTH_LEVEL'];

		if ($arMenu['IS_PARENT'] == true){
			$arParents[$arMenu['DEPTH_LEVEL']-1] = $i;
		}
		elseif($arMenu['SELECTED'] == true)
		{
			$bWasSelected = true;
			break;
		}
		$i++;
	}

	if ($bWasSelected){
		for($i=0; $i < $depth-1; $i++)
			$arResult[$arParents[$i]]['CHILD_SELECTED'] = true;
	}
?>
