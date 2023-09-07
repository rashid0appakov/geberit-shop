<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
	"custom:search.header",
	".default",
	Array(),
	false
);
