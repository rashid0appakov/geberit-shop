<?
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(defined('CUSTOM_SERVER_STATUS'))
{
	if(COption::GetOptionString("main", "cookie_name", "BITRIX_SM") !== "BITRIX_".CUSTOM_SERVER_STATUS)
	{
		COption::SetOptionString("main", "cookie_name", "BITRIX_".CUSTOM_SERVER_STATUS);
	}
}

$arFiles = $arSites = [];

$rsSites = CSite::GetList($by = "sort", $order = "asc", ['ACTIVE' => 'Y']);
while($arSite = $rsSites->Fetch())
{
	//pr($arSite);
	if(!file_exists($arSite['DOC_ROOT'].'site/config.php'))
	{
		continue;
	}
	$file = __DIR__.'/../cache/'.$arSite['LID'].'-cache_page.php';
	if(!file_exists($file))
	{
		file_put_contents($file, '{}');
	}
	$arFiles[$arSite['LID']] = filemtime($file);
	$arSites[$arSite['LID']] = $arSite;
}

asort($arFiles);
pr($arFiles);
//pr($arSites);

foreach($arFiles as $lid=>$time)
{
	if($arSites[$lid]['SERVER_NAME'])
	{
		$url = 'https://'.$arSites[$lid]['SERVER_NAME'].'/local/cron/cache_page.php';
		$ip = gethostbyname('drvt.shop');
		pr($arSites[$lid]['LID'].': '.$url.' ('.$ip.')');
		flush();
		$ch = curl_init($ip);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: '.$arSites[$lid]['SERVER_NAME']]);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RESOLVE, [
			$arSites[$lid]['SERVER_NAME'].':80:'.$ip,
			$arSites[$lid]['SERVER_NAME'].':443:'.$ip,
		]);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		$result = curl_exec($ch);
		if(!$result)
		{
			$result = curl_getinfo($ch);
		}
		pr($result);
		break;
	}
}
pr('end.');
?>