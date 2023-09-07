<?
global $NavNum,$APPLICATION;
if(isset($_REQUEST["PAGEN_".$NavNum]))
{
    CMain::IsHTTPS() ? $s = 's' : $s = '';
    $canon_url = 'http' . $s . '://' . SITE_SERVER_NAME . $APPLICATION->GetCurPage();
    if(intval($_REQUEST["PAGEN_".$NavNum])==1)
	{
		localredirect($canon_url, false, '301 Moved permanently');
	}
    //$APPLICATION->AddHeadString('<link href="' . $canon_url . '" rel="canonical" />', true);
}
?>