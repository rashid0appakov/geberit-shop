<?php
//<title>wscrm</title>

use Bitrix\Main;
use Bitrix\Catalog;
use Bitrix\Iblock;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_setup_templ.php');

// export errors
$arSetupErrors = [];

// export settings
$settings = [
    'export_default_path' => Option::get('catalog', 'export_default_path', '/bitrix/catalog_export/webservicexml.xml'),
];

$STEP = (int) $STEP;
$STEP = $STEP <= 0 ? 1 : $STEP;


if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1) {
    if (isset($arOldSetupVars['IBLOCK_ID'])) {
        $IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
    }
    if (isset($arOldSetupVars['SETUP_FILE_NAME'])) {
        $SETUP_FILE_NAME = $arOldSetupVars['SETUP_FILE_NAME'];
    }
    if (isset($arOldSetupVars['SETUP_PROFILE_NAME'])) {
        $SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
    }
}

if ($STEP > 1) {
    $IBLOCK_ID = intval($IBLOCK_ID);
    if ($IBLOCK_ID <= 0) {
        $arSetupErrors[] = 'Инфоблок не выбран';
    }
    $iblock = Iblock\IblockTable::getById($IBLOCK_ID)->fetch();
    if (! $iblock) {
        $arSetupError[] = 'Инфоблок не найден';
    }
    if (!isset($SETUP_FILE_NAME) || $SETUP_FILE_NAME == '') {
        $arSetupErrors[] = Loc::getMessage('CET_ERROR_NO_FILENAME');
    }
    if ($ACTION == 'EXPORT_SETUP' && strlen($SETUP_PROFILE_NAME) <= 0) {
        $arSetupErrors[] = 'Не указано имя профиля';
    }
    if (count($arSetupErrors) > 0) {
        $STEP = 1;
    }
}

if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 2) {
    if (isset($arOldSetupVars['PROPERTY_ARTICLE'])) {
        $PROPERTY_ARTICLE = $arOldSetupVars['PROPERTY_ARTICLE'];
    }
    if (isset($arOldSetupVars['PROPERTY_BRAND'])) {
        $PROPERTY_BRAND = $arOldSetupVars['PROPERTY_BRAND'];
    }
}

if ($STEP > 2) {
    if (! $PROPERTY_ARTICLE || ! $PROPERTY_BRAND) {
        $arSetupErrors[] = 'Не указано свойство артикула или бренда';
    }
    if (count($arSetupErrors) > 0) {
        $STEP = 2;
    }
}

$context = new CAdminContextMenu(array(
    array(
        "TEXT"  => Loc::getMessage('CATI_ADM_RETURN_TO_LIST'),
        "TITLE" => Loc::getMessage('CATI_ADM_RETURN_TO_LIST_TITLE'),
        "LINK"  => "/bitrix/admin/cat_export_setup.php?lang="  . LANGUAGE_ID,
        "ICON"  => "btn_list",
    )
));
$context->Show();

if (! empty($arSetupErrors)) {
    ShowError(implode('<br />', $arSetupErrors));
}
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage() ?>" name="ws_export_form" id="ws_export_form">
<?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => "Параметры", "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB1_TITLE")),
        array("DIV" => "edit2", "TAB" => "Свойства", "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB2_TITLE")),
        array("DIV" => "edit3", "TAB" => "Результат", "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB3_TITLE")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
    $tabControl->Begin();
    $tabControl->BeginNextTab();
?>

<? if ($STEP == 1) { ?>
    <tr>
        <td valign="top" width="40%"><? echo GetMessage("CAT_ADM_CSV_EXP_IBLOCK_ID"); ?>:</td>
        <td valign="top" width="60%"><?
            if (!isset($IBLOCK_ID))
                $IBLOCK_ID = 0;
            echo GetIBlockDropDownListEx(
                $IBLOCK_ID,
                'IBLOCK_TYPE_ID',
                'IBLOCK_ID',
                array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'U'),
                '',
                '',
                'class="adm-detail-iblock-types"',
                'class="adm-detail-iblock-list"'
            );
        ?></td>
    </tr>

    <tr>
        <td width="40%"><?echo GetMessage("CET_SAVE_FILENAME");?></td>
        <td width="60%">
            <input type="text" name="SETUP_FILE_NAME"
                   value="<?echo (strlen($SETUP_FILE_NAME)>0) ?
                            htmlspecialchars($SETUP_FILE_NAME) :
                            $settings['export_default_path'] ?>"
                   size="50">
        </td>
    </tr>

    <? if ($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') { ?>
         <tr>
             <td width="40%"><?echo GetMessage("CET_PROFILE_NAME");?></td>
             <td width="60%">
                 <input type="text" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME) ?>" size="30">
             </td>
         </tr>
    <? } ?>
<? } // end 1 tab ?>

<?
    $tabControl->EndTab();
    $tabControl->BeginNextTab();
?>

<? if ($STEP == 2) { ?>
    <?
        $productProperties = [];
        $propertiesDB = Iblock\PropertyTable::getList([
            'filter' => ['=IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y']
        ]);

        while ($property = $propertiesDB->fetch()) {
            $productProperties[$property['ID']] = $property;
        }

        $productSKUProperties = [];
        $iblockOffer = Catalog\CatalogIblockTable::getList(array(
            'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID', 'VERSION' => 'IBLOCK.VERSION'),
            'filter' => array('=PRODUCT_IBLOCK_ID' => $IBLOCK_ID)
        ))->fetch();
        if (! empty($iblockOffer) && is_array($iblockOffer)) {
            $propertiesSKUDB = Iblock\PropertyTable::getList([
                'filter' => ['=IBLOCK_ID' => $iblockOffer['IBLOCK_ID']]
            ]);
            while ($propertySKU = $propertiesSKUDB->fetch()) {
                $productSKUProperties[$property['ID']] = $property;
            }
        }
    ?>

    <tr>
        <td width="40%">Укажите свойство товара для артикула:</td>
        <td width="60%">
            <select name="PROPERTY_ARTICLE">
                <? foreach ($productProperties as $property) { ?>
                    <option value="<?echo $property["CODE"] ?>"
                        <?if ($property["CODE"] === $PROPERTY_ARTICLE)
                          echo " selected";?>
                    >
                        <?echo $property["NAME"];?>
                    </option>
                <? } ?>
            </select>
        </td>
    </tr>

    <tr>
        <td width="40%">Укажите свойство товара для бренда:</td>
        <td width="60%">
            <select name="PROPERTY_BRAND">
                 <? foreach ($productProperties as $property) { ?>
                     <option value="<?echo $property["CODE"] ?>"
                         <?if ($property["CODE"] === $PROPERTY_BRAND)
                           echo " selected";?>
                     >
                         <?echo $property["NAME"];?>
                     </option>
                 <? } ?>
             </select>
        </td>
    </tr>
<? } // end 2 tab?>

<?
    $tabControl->EndTab();
    $tabControl->BeginNextTab();
?>

<?
    if ($STEP == 3) {
        $FINITE = true;
    }
?>

<?
    $tabControl->EndTab();
    $tabControl->Buttons();
?>

<? echo bitrix_sessid_post();?>
<?
if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') {
    ?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
}
?>

<? if ($STEP < 3) { ?>
    <input type="hidden" name="lang" value="<?echo $lang ?>">
    <input type="hidden" name="ACT_FILE"
     value="<?echo htmlspecialchars($_REQUEST["ACT_FILE"]) ?>">
    <input type="hidden" name="ACTION" value="<?echo $ACTION ?>">
    <input type="hidden" name="STEP" value="<?echo $STEP + 1 ?>">

    <? if ($STEP > 1) { ?>
        <input type="hidden" name="IBLOCK_ID" value="<? echo $IBLOCK_ID; ?>">
        <input type="hidden" name="SETUP_FILE_NAME" value="<? echo $SETUP_FILE_NAME; ?>">
        <? if (($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')) { ?>
            <input type="hidden" name="SETUP_PROFILE_NAME" value="<? echo $SETUP_PROFILE_NAME; ?>">
        <? } ?>
        <input type="hidden" name="SETUP_FIELDS_LIST"
               value="IBLOCK_ID,SETUP_FILE_NAME,PROPERTY_ARTICLE,PROPERTY_BRAND">
    <? } ?>
    <? if ($STEP > 1) { ?>
        <input type="submit" class="button" name="backButton" value="&lt;&lt; <?echo Loc::getMessage("CATI_BACK") ?>">
    <? } ?>

     <input type="submit" class="button" value="<?echo ($STEP == 2)?(($ACTION == "EXPORT")?Loc::getMessage("CATI_NEXT_STEP_F"):Loc::getMessage("CET_SAVE")):Loc::getMessage("CATI_NEXT_STEP")." &gt;&gt;" ?>" name="submit_btn">
<? } ?>

<?
    $tabControl->End();
?>
</form>
<script type="text/javascript">
<?if ($STEP < 2):?>
    tabControl.SelectTab("edit1");
    tabControl.DisableTab("edit2");
    tabControl.DisableTab("edit3");
<?elseif ($STEP == 2):?>
    tabControl.SelectTab("edit2");
    tabControl.DisableTab("edit1");
    tabControl.DisableTab("edit3");
<?elseif ($STEP == 3):?>
    tabControl.SelectTab("edit3");
    tabControl.DisableTab("edit1");
    tabControl.DisableTab("edit2");
<?endif;?>
</script>