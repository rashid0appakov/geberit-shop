<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Простое подключение файла',
	"DESCRIPTION" => 'Файл ищется сначала в текущем шаблоне, потом в дефолтовом, потом в корне',
	"ICON" => "/images/include.gif",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "include_area",
			"NAME" => 'Утилиты',
		),
	),
);
?>