<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;

if (!!$arResult["ITEM"]["PROPERTIES"]["PARAMS"]["VALUE"])
{
	if (Loader::includeModule("highloadblock"))
	{
		$arHLBlock = HL\HighloadBlockTable::getById(3)->fetch();
		$obEntity = HL\HighloadBlockTable::compileEntity($arHLBlock);
		$paramsEntity = $obEntity->getDataClass();

		$ob = $paramsEntity::getList(array(
			"select" => array(
				"UF_NAME",
				"UF_FILE"
			),
			"filter" => array(
				"UF_XML_ID" => $arResult["ITEM"]["PROPERTIES"]["PARAMS"]["VALUE"]
			),
			"order" => array(
				"UF_SORT" => "ASC",
			),
		));
		while ($item = $ob->fetch())
		{
			$name = $item["UF_NAME"];
			$fileId = $item["UF_FILE"];
			$fileUrl = CFile::GetPath($fileId);

			$arResult["ITEM"]["DISPLAY_PROPERTIES"]["PARAMS"][] = array(
				"NAME" => $name,
				"IMAGE" => $fileUrl,
			);
		}
	}
}

$uniqueId = uniqid();
$arResult['uniqueId'] = $uniqueId;