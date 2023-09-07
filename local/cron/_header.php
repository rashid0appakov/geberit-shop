<?php
$sapi = php_sapi_name();
if ($sapi != 'cli')
{
	die('Hacker? Sadis na unitaz i dui otsuda!');
}

$SITE_ID = false;
if(isset($argv[1]))
{
	$SITE_ID = $argv[1];
}
if(!$SITE_ID )
{
	die('No SITE_ID');
}

$file = __DIR__.'/../cache/'.$SITE_ID.'-cache_page.php';
if(file_exists($file))
{
	$data = file_get_contents($file);
	list($php, $json) = explode("\n", $data, 2);
	$GLOBALS['PAGE_DATA'] = json_decode($json, true);
}
else
{
	die('No valide SITE_ID');
}

$_SERVER['DOCUMENT_ROOT'] = $GLOBALS['PAGE_DATA']['SITE']['DOC_ROOT'];

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/site/config.php'))
{
    include_once($_SERVER['DOCUMENT_ROOT'].'/site/config.php');
}
else
{
	die('ERROR include site config');
}
?>