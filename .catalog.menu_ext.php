<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

if (!Loader::includeModule("iblock")) return;

$iblockId = CATALOG_IBLOCK_ID;
$cacheTime = 36000000;
$cacheId = "";
$cacheDir = "/menu/catalog/".CATALOG_IBLOCK_ID;

$obCache = new CPHPCache;
if ($obCache->InitCache($cacheTime, $cacheId, $cacheDir))
{
    $aMenuLinksExt = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
    $aMenuLinksExt = array();

    global $CACHE_MANAGER;
    $CACHE_MANAGER->StartTagCache($cacheDir);
    $CACHE_MANAGER->RegisterTag("iblock_id_$iblockId");

    $rows = array();
    $ob = CIBlockSection::GetList(
        array(
            "LEFT_MARGIN" => "ASC",
        ),
        array(
            "IBLOCK_ID" => $iblockId,
            "GLOBAL_ACTIVE" => "Y",
        ),
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "DEPTH_LEVEL",
            "NAME",
            "SECTION_PAGE_URL",
            "UF_MENU_HIGHLIGHT",
        )
    );
    while ($row = $ob->GetNext())
    {
        $rows[] = $row;
    }

    $rowCount = count($rows);
    for ($i = 0; $i < $rowCount; $i++)
    {
        $row = $rows[$i];

        $aMenuLinksExt[$i] = array(
            htmlspecialcharsbx($row["~NAME"]),
            $row["~SECTION_PAGE_URL"],
            array(),
            array(
                "FROM_IBLOCK" => true,
                "ID" => $row["ID"],
                "IS_PARENT" => false,
                "DEPTH_LEVEL" => $row["DEPTH_LEVEL"],
                "HIGHLIGHT" => !!$row["UF_MENU_HIGHLIGHT"],
            )
        );

        if ($i > 0)
        {
            $previous = $rows[$i - 1];
            $aMenuLinksExt[$i - 1][3]["IS_PARENT"] = $row["DEPTH_LEVEL"] > $aMenuLinksExt[$i - 1][3]["DEPTH_LEVEL"];
        }
    }

    $CACHE_MANAGER->EndTagCache();
    $obCache->EndDataCache($aMenuLinksExt);
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);