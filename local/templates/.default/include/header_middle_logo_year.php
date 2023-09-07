<?
$default = "01.01.2010";
$dateCreate = \Bitrix\Main\Config\Option::get("tiptop", "template_date_create", $default);

try
{
	$dateCreate = new DateTime($dateCreate);
}
catch (Exception $ex)
{
	$dateCreate = new DateTime($default);
}

$dateDiff = (new DateTime())->diff($dateCreate);
?>
Нам <?=$dateDiff->format("%y")?> лет!