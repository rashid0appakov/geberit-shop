<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule('iblock');

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_1", "PROPERTY_MANUFACTURER", "IBLOCK_SECTION_ID");
$arFilter = Array("IBLOCK_ID"=>15, "ACTIVE"=>"Y", "ID"=>$_REQUEST["id"]);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

if($ob = $res->GetNextElement()){
	$arFields = $ob->GetFields();
	$arProps = $ob->GetProperties();

	$resBrands = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>13, "ACTIVE"=>"Y", "ID"=>$arProps['MANUFACTURER']['VALUE']), false, Array(), Array("ID", "NAME", "IBLOCK_ID"));
	if($obBrands = $resBrands->GetNextElement()){
		$arFieldsBrands = $obBrands->GetFields();
	}

	$resSection = CIBlockSection::GetList(Array(), Array('ID' => $arFields['IBLOCK_SECTION_ID']), true);
	$ar_resSection = $resSection->GetNext();



	$arProductInfo['NAME'] = $arFields['NAME'];
	$arProductInfo['PRICE'] = $arFields['CATALOG_PRICE_1'];
	$arProductInfo['BRANDS'] = $arFieldsBrands['NAME'];
	$arProductInfo['CATEGORY'] = $ar_resSection['NAME'];
}
echo json_encode($arProductInfo);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>