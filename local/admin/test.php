<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!$GLOBALS["USER"]->IsAdmin()) { LocalRedirect( "/", false, "301 Moved permanently" ); }
$APPLICATION->ShowHead(); 

?>
<style>
	table.internal{
		background-color: #f5f9f9;
		border: 1px solid;
		border-color: #c4ced2 #dce7ed #dce7ed;
	}
    input[type="submit" i] {
        width: auto;
        height: 30px;
    }
</style>

<? $fieldName = 'redirect'; ?>
<div style="width: fit-content;">
	<form action="?read=y" method="POST">
		<table class="internal">
			<tr><td>
					<?=\Bitrix\Main\UI\FileInput::createInstance([
						"name" => $fieldName,
						"description" => false,
						"upload" => true,
						"allowUpload" => "F",
						"allowUploadExt" => "csv",
						"medialib" => false,
						"fileDialog" => true,
						"cloud" => false,
						"delete" => true,
						"maxCount" => 1,
					])->show($_POST[$fieldName]);?>
			</td></tr>
			<tr align='center'><td>
				<input type="submit" value="Прочитать файл">
			</td></tr>
		</table>
	</form>

<?
$fileInfo = ['file'=>$_SERVER["DOCUMENT_ROOT"].'/upload/tmp'.$_POST[$fieldName]['tmp_name'], 'name'=>$_POST[$fieldName]['name']];
if(!file_exists($fileInfo['file'])) { return; }

echo'<pre>';print_r('Загрузка файла "'.$fileInfo['name'].'"');echo'</pre>';
echo'<pre>';var_dump($fileInfo);echo'</pre>';

$col_delimiter = ',';
$hasHead = true;

$csv = array_map(function($item){ 
	$item = str_getcsv(mb_convert_encoding($item,'UTF-8','cp1251'),$col_delimiter);
	$item = array_map(function($value){ return trim($value); }, $item);
	return $item; 
}, file($fileInfo['file']));
if($hasHead) { unset($csv[0]); }
//echo'<pre>';var_dump($csv);echo'</pre>';
	

foreach($csv as $col) {
	$request = ["ACTIVE" => 'Y', "REDIRECT_FROM"=>$col[0], "REDIRECT_TO"=>$col[1]];
	$request["REDIRECT_FROM"] = parse_url($request["REDIRECT_FROM"])["path"];
	if(pathinfo($request["REDIRECT_FROM"], PATHINFO_EXTENSION)!="") { continue; }
	
	$ID = h2o\Redirect\RedirectTable::getList([
		'filter' => [
			"IS_REGEXP" => "N",
			"=REDIRECT_FROM" => $request["REDIRECT_FROM"],
		]
	])->fetch()['ID']?:0;
	//echo'<pre>';var_dump([$ID,$request]);echo'</pre>';
	//continue;

	$arMap = \h2o\Redirect\RedirectTable::getMap();
	$arFields = [];
	foreach($arMap as $key => $field){
		if(isset($request[$key]) && $field['editable']){
			$arFields[$key] = $request[$key];
		} elseif($field['data_type'] == 'boolean' && $field['editable']){
			$arFields[$key] = "N";
		}
	}
	// сохранение данных
	if($ID > 0) { $result = \h2o\Redirect\RedirectTable::update($ID, $arFields); }
	else { $result = \h2o\Redirect\RedirectTable::add($arFields); if($result->isSuccess()){ $ID = $result->getId(); } }
	echo'<pre>';var_dump([$ID,$request["REDIRECT_FROM"],$request["REDIRECT_TO"]]);echo'</pre>';
}