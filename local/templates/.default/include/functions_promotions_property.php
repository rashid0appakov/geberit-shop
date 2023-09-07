<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

\Bitrix\Main\Loader::includeModule('iblock');

\Bitrix\Main\Loader::includeModule('iblock');

$rsEnum = \Bitrix\Iblock\PropertyEnumerationTable::getList([
	'filter' => [
		'PROPERTY_ID'=> PROMOTIONS_SITE_PROPERTY_ID,
	],
	'cache' => [
		'ttl' => 3600
	]
]);

$filterVal = '';
while($arEnum = $rsEnum->fetch())
{
	if($arEnum['XML_ID'] == SITE_ID)
	{
		$filterVal = $arEnum['VALUE'];
		break;
	}
}

?>