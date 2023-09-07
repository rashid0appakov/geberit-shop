<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!$USER->IsAdmin())
{
	die('Nafig-Nafig!');
}

shell_exec('/usr/bin/php '.$_SERVER["DOCUMENT_ROOT"].'/local/cron/pretty_sort.php l1');

$arResult['status'] = 'ok';

header('Content-Type: application/json');
echo json_encode($arResult);
?>