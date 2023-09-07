<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?php
    $json = [
        "status"    => 'success',
        "qty"       => count($_SESSION['CATALOG_COMPARE_LIST'][CATALOG_IBLOCK_ID]['ITEMS'])
    ];

    CClass::Instance()->RenderJSON($json);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>