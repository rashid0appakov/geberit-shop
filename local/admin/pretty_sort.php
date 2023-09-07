<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Config\Option;


$arTabs[] = array(
    "DIV" => "tab_settings",
    "TAB" => "Сортировка",
    "TITLE" => "Запуск сортировки",
);
$tabControl = new CAdminTabControl("tabControl", $arTabs);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<div id="pretty_sort-ok" style="display: none;">
	<?
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => "Пересортировано",
		"TYPE" => "OK",
	));
	?>
</div>
<div id="pretty_sort-error" style="display: none;">
	<?
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => "Ошибка при сортировке",
		"TYPE" => "ERROR",
	));
	?>
</div>
    <?$tabControl->Begin();?>
    <?$tabControl->BeginNextTab();?>
    <tr>
        <td width="100%">
<div id="pretty_sort-run" style="display: none;">
	<div style="text-align: center;">
		<img src="/local/templates/.default/images/lampochka.gif">
	</div>
</div>
		</td>
    </tr>
    <?$tabControl->Buttons();?>
<input type="button" id="pretty_sort-button" class="adm-btn-save" value="Да, я прямо сейчас хочу пересортировать товары на сайте Свет-Онлайн!" title="" />
	   <?$tabControl->End();?>
<script>
BX.ready(function(){
	BX.bind(BX('pretty_sort-button'), 'click', function() {
		BX.style(BX('pretty_sort-run'),'display','block');
		BX.style(BX('pretty_sort-ok'),'display','none');
		BX.style(BX('pretty_sort-error'),'display','none');
		BX.ajax.loadJSON('/ajax/pretty_sort.php', {}, function(res)
		{
			if(res.status == 'ok')
			{
				BX.style(BX('pretty_sort-run'),'display','none');
				BX.style(BX('pretty_sort-ok'),'display','block');
			}
			else
			{
				BX.style(BX('pretty_sort-run'),'display','none');
				BX.style(BX('pretty_sort-error'),'display','block');
			}
		}, function(res)
		{
			BX.style(BX('pretty_sort-run'),'display','none');
			BX.style(BX('pretty_sort-error'),'display','block');
		});
	});
});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>