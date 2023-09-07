<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Простой компонент - только шаблон',
	"DESCRIPTION" => 'Компонент ничего не считает и не вычисляет, только подключает шаблон, без кешрованя!!!',
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