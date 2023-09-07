<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}
echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
?>

<form action="<?php echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?php echo LANGUAGE_ID; ?>">
    <input type="hidden" name="install" value="Y">
    <input type="submit" name="" value="<?php echo Loc::getMessage("MOD_BACK"); ?>">
<form>