<?php
	if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

	if (!empty($arResult['ERRORS'])){
		$arResult['ERROR']  = [];
		foreach($arResult['ERRORS'] AS $error){
			$error  = trim(str_replace(["\n", "\r\n", "<br>"], '', $error));
			if (preg_match('/\'(.*?)\'/msi', $error, $res))
				$arResult['ERROR'][$res[1]] = $error;
			if ($error == "FALSE_AGREEMENT")
				$arResult['ERROR']['agreement'] = $error;
		}
	}