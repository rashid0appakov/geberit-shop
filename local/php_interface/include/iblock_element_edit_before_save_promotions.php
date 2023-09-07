<?
use Bitrix\Main\Web\HttpClient;

//pr($_POST);

$GLOBALS['CUSTOM_UPDATE_PROPERTY'] = [];

$rsEnum = \Bitrix\Iblock\PropertyTable::getList([
	'filter' => [
		'IBLOCK_ID'=>$_GET['IBLOCK_ID'],
	],
	'cache' => [
		'ttl' => 3600
	]
]);
$codeUrl = $codeJson = '';

$filterVal = '';
while($arProperty = $rsEnum->fetch())
{
	if($arProperty['CODE'] == 'FILTER_URL')
	{
		$codeUrl = $arProperty['ID'];
	}
	elseif($arProperty['CODE'] == 'FILTER_JSON')
	{
		$codeJson = $arProperty['ID'];
	}
}
if($codeUrl and $codeJson)
{
	$valueUrl = $valueJson = '';
	$valueUrl = current($_POST['PROP'][$codeUrl]);
	
	if(!empty($valueUrl))
	{
		list($valueUrl) = explode('?', $valueUrl, 2);
		$valueUrl .= '?json_get_filter=y';
		//pr($valueUrl);
		$httpClient = new HttpClient(); 
		$valueJson = $httpClient->get($valueUrl);
		//pr($valueUrl);
		//pr($valueJson);
		if($valueJson)
		{
			$arrayJson = json_decode($valueJson, true);
			//pr($arrayJson);
			if(!is_array($arrayJson) or empty($arrayJson['iblock']) or empty($arrayJson['section']) or empty($arrayJson['filter']))
			{
				$error = new _CIBlockError(2, "URL_REQUIRED", "Ссылка на фильтр неправильная, попробуйте ещё раз или обратитесь к адмнистратору");
			}
		}
	}
	$GLOBALS['CUSTOM_UPDATE_PROPERTY'] = [
		$codeJson => [
			$valueJson
		]
	];
}

if($REQUEST_METHOD == "POST")
{
	//pr($_POST);
	//die();
	//$bVarsFromForm = true;
}

function BXIBlockAfterSave(&$arFields)
{
	if(!empty($GLOBALS['CUSTOM_UPDATE_PROPERTY']) and is_array($GLOBALS['CUSTOM_UPDATE_PROPERTY']))
	{
		CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], $GLOBALS['CUSTOM_UPDATE_PROPERTY']);
	}
}
?>