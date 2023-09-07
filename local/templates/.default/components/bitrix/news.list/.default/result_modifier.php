<?

$currentSectionId = (int)$arParams["CURRENT_SECTION_ID"];
$res = CIBlockSection::GetByID($currentSectionId);
if($ar_res = $res->GetNext())
    $arResult["sectionName"] = $ar_res['NAME'];

foreach ($arResult["ITEMS"] as $key => &$arFeedback) {
    $productId = (int)$arFeedback["PROPERTIES"]["product_id"]["VALUE"];
    $obResult = CIBlockElement::GetByID($productId);
    while($result = $obResult->GetNext()){
        if ($currentSectionId !== (int)$result["IBLOCK_SECTION_ID"]){
            //$arFeedback["ACTIVE"] = 'N';
            unset($arResult["ITEMS"][$key]);
        }
    }

    $rsUser   = CUser::GetByID($arFeedback["PROPERTIES"]["user"]["VALUE"]);
    $userName = $rsUser->Fetch()["NAME"];
    $arFeedback["userName"] = $userName;
}