<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<? if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
	return;

if ($_POST['PRODUCT_ID']) {?>
    <? $APPLICATION->IncludeComponent(
            "altop:buy.one.click",
            "restyled",
            Array(
                'ELEMENT_ID'=> $_POST['PRODUCT_ID'],
                'IBLOCK_ID' => CATALOG_IBLOCK_ID,
                'REQUIRED'  => []
            ),
            false
        );
    ?>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>