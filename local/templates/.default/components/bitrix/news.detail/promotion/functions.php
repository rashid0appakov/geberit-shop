<?php
/**
 * Get products sections
 * @param array $arFilter   - filter array
 * @return boolean|array
 */
function getProductSections($arFilter, $ID){
    if (empty($arFilter) || !$ID)
        return FALSE;

    $arResult   = [];

    $cache = new CPHPCache();
    $cache_id = 'PROMOTION_ITEM_'.$ID;
    if ($cache->InitCache(CClass::CACHE_TIME, $cache_id, "/promotions/")){
        $res = $cache->GetVars();
        if (is_array($res["arSections"]) && (count($res["arSections"]) > 0))
           $arResult = $res["arSections"];
    }

    if (empty($arResult)){
        $rsElements = CIBlockElement::GetList(
            [],
            array_merge([
                'IBLOCK_ID' => CATALOG_IBLOCK_ID,
                'ACTIVE'    => 'Y',
                'SECTION_GLOBAL_ACTIVE'    => 'Y'
            ], $arFilter),
            false,
            [],
            array("ID", "IBLOCK_SECTION_ID")
        );
        while($arItem = $rsElements->GetNext()){
            if ($arItem['IBLOCK_SECTION_ID'])
                $arResult[$arItem['IBLOCK_SECTION_ID']]['COUNT']++;
        }

        if (!empty($arResult)){
            $arSections = CClass::getCatalogSection();
            foreach($arResult AS $sID  => &$arItem){
                if (!$arSections[$sID])
                    continue;
                $arItem['NAME']     = $arSections[$sID]['NAME'];
                $arResult['ALL']    += $arItem['COUNT'];
            }
        }

        $cache->StartDataCache(CClass::CACHE_TIME, $cache_id, "/");
        $cache->EndDataCache(array("arSections" => $arResult));
    }

    return $arResult;
}