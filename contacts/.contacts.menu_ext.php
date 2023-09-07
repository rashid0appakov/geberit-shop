<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    if (!CModule::IncludeModule("iblock"))
        return;

    $arSelect   = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CODE"];
    $arFilter = [
        "IBLOCK_ID" => CClass::RU_CONTACTS_IBLOCK_ID,
        "ACTIVE"    => "Y",
        "PROPERTY_SITE_ID"  => SITE_ID
    ];

    $rsElement = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, ['nPageSize' => 20], $arSelect);
    while($arItem   = $rsElement->GetNext())
    	$aMenuLinksExt[] = [
            htmlspecialcharsbx($arItem["~NAME"]),
            $arItem["~DETAIL_PAGE_URL"],
            $arItem["~DETAIL_PAGE_URL"],
            array(
                "FROM_IBLOCK"   => TRUE,
                "IS_PARENT"     => false,
                "DEPTH_LEVEL"   => 1
            )
        ];
    $aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);?>