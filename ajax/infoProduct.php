<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
CModule::IncludeModule('iblock');

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_1", "IBLOCK_SECTION_ID");
$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$_REQUEST["id"]);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
if($ob = $res->GetNextElement()){
	$arFields = $ob->GetFields();	
	$arProps = $ob->GetProperties();
	
	$arDiscounts = CCatalogProduct::GetOptimalPrice($_REQUEST["id"], 1, $USER->GetUserGroupArray(), 'N');

	$arProductInfo['NAME'] = $arFields['NAME'];
	$arProductInfo['PRICE'] = $arDiscounts['RESULT_PRICE']['DISCOUNT_PRICE'];
}
echo json_encode($arProductInfo);
?>