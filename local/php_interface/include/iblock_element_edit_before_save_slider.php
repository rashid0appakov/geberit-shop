<?
$arSize = [
	28 => [
		'width' => 1170,
		'height' => 420,
	],
	39 => [
		'width' => 870,
		'height' => 137,
	],
];
if($REQUEST_METHOD == "POST" and strlen($Update)>0 and $view != "Y" and (!$error) and empty($dontsave))
{
	if(isset($arSize[$_REQUEST['IBLOCK_ID']]) and !empty($_POST['DETAIL_PICTURE']['tmp_name']))
	{
		$dir = (defined('BX_TEMPORARY_FILES_DIRECTORY') ? BX_TEMPORARY_FILES_DIRECTORY : $_SERVER['DOCUMENT_ROOT'].'/upload/tmp');
		$file = CFile::GetImageSize($dir.$_POST['DETAIL_PICTURE']['tmp_name']);
		if($file !== false and is_array($file))
		{
			$errors = [];
			if($file[0] < $arSize[$_REQUEST['IBLOCK_ID']]['width'])
			{
				   $errors[] = 'Ширина изображения должна быть не меньше '.$arSize[$_REQUEST['IBLOCK_ID']]['width'].' пикселей. Ширина загружаемого файла '.$file[0].' пикселей';
			} 
			if($file[1] < $arSize[$_REQUEST['IBLOCK_ID']]['height'])
			{
				$errors[] = 'Высота изображения должна быть не меньше '.$arSize[$_REQUEST['IBLOCK_ID']]['height'].' пикселей. Ширина загружаемого файла '.$file[1].' пикселей';
			}
			if(count($errors))
			{
				$errors = implode('<br>', $errors);
				$error = new _CIBlockError(2, "SIZE_REQUIRED", $errors);
			}
		}
	}
}