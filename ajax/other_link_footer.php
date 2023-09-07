<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?$APPLICATION->IncludeComponent(
	"custom:menu",
	"footer_right",
	array(
		"TYPE" => "footer_right",
	)
);?>