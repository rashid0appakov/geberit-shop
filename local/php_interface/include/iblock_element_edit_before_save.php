<?
function BXIBlockAfterSave(&$arFields) {
    CEventHandler::UpdateSetsProductPrice($arFields['ID']);
}
?>