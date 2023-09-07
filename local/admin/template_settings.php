<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Config\Option;


$arTabs[] = array(
    "DIV" => "tab_settings",
    "TAB" => "Настройки",
    "TITLE" => "Настройки шаблона",
);
$tabControl = new CAdminTabControl("tabControl", $arTabs);

if (
    $REQUEST_METHOD == "POST"
    &&
    ($save!="" || $apply!="")
    &&
    check_bitrix_sessid()
)
{
    Option::set("tiptop", "template_date_create", $DATE_CREATE);
    Option::set("tiptop", "template_phone1", $PHONE_1);
    Option::set("tiptop", "template_phone2", $PHONE_2);
    Option::set("tiptop", "worktime_start", $WORKTIME_START);
    Option::set("tiptop", "worktime_end", $WORKTIME_END);
    Option::set("tiptop", "template_email", $EMAIL);
    Option::set("tiptop", "template_address", $ADDRESS);
    Option::set("tiptop", "template_market_url", $MARKET_URL);

    if ($apply != "")
    {
        LocalRedirect($_SERVER["REQUEST_URI"]."?mess=saved&".$tabControl->ActiveTabParam());
    }
    else
    {
        if (!empty($_REQUEST["back_url"]))
        {
            LocalRedirect($_REQUEST["back_url"]);
        }
        else
        {
            LocalRedirect($_SERVER["REQUEST_URI"]."?mess=saved&".$tabControl->ActiveTabParam());
        }
    }
}
else
{
    $arData = array();

    $arData["DATE_CREATE"] = Option::get("tiptop", "template_date_create", "01.01.2010");
    $arData["PHONE_1"] = Option::get("tiptop", "template_phone1", "");
    $arData["PHONE_2"] = Option::get("tiptop", "template_phone2", "");
    $arData["WORKTIME_START"] = Option::get("tiptop", "worktime_start", "");
    $arData["WORKTIME_END"] = Option::get("tiptop", "worktime_end", "");
    $arData["EMAIL"] = Option::get("tiptop", "template_email", "");
    $arData["ADDRESS"] = Option::get("tiptop", "template_address", "");
    $arData["MARKET_URL"] = Option::get("tiptop", "template_market_url", "");
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>

<?if ($_REQUEST["mess"] == "saved"):
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Сохранено",
        "TYPE" => "OK",
    ));
endif;?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="template_settings">
    <?=bitrix_sessid_post();?>
    <?$tabControl->Begin();?>
    <?$tabControl->BeginNextTab();?>
    <tr>
        <td width="40%">Дата создания</td>
        <td width="60%">
            <input type="text" class="typeinput" name="DATE_CREATE" size="12" value="<?=$arData["DATE_CREATE"]?>">
            <?=Calendar("DATE_CREATE", "template_settings")?>
        </td>
    </tr>
    <tr>
        <td width="40%">Телефон 1</td>
        <td width="60%">
            <input type="text" class="typeinput" name="PHONE_1" size="75" value="<?=$arData["PHONE_1"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">Телефон 2</td>
        <td width="60%">
            <input type="text" class="typeinput" name="PHONE_2" size="75" value="<?=$arData["PHONE_2"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">Начало рабоченго дня</td>
        <td width="60%">
            <input type="text" class="typeinput" name="WORKTIME_START" size="75" value="<?=$arData["WORKTIME_START"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">Завершение рабочего дня</td>
        <td width="60%">
            <input type="text" class="typeinput" name="WORKTIME_END" size="75" value="<?=$arData["WORKTIME_END"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">Email</td>
        <td width="60%">
            <input type="text" class="typeinput" name="EMAIL" size="75" value="<?=$arData["EMAIL"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">Адрес</td>
        <td width="60%">
            <input type="text" class="typeinput" name="ADDRESS" size="75" value="<?=$arData["ADDRESS"]?>">
        </td>
    </tr>
    <tr>
        <td width="40%">URL Маркета</td>
        <td width="60%">
            <input type="text" class="typeinput" name="MARKET_URL" size="75" value="<?=$arData["MARKET_URL"]?>">
        </td>
    </tr>
    <?$tabControl->Buttons(array(
        "disabled" => false,
        "back_url" => $_REQUEST["back_url"],
    ));?>
    <?$tabControl->End();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>