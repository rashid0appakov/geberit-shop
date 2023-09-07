<?php
#file stopsovetnik # http://marketplace.1c-bitrix.ru/solutions/bart.stopsovetnik/
# http://marketplace.1c-bitrix.ru/solutions/bart.stopsovetnik/
#


$site = 'https://geberit-shop.ru';
if(!$_SERVER['DOCUMENT_ROOT']) { $_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__); };

function sovetnik_generate($var,$var1){
	global $site;
	$file = file_get_contents($var);


$file = preg_replace_callback ("!<url>(.*)</url>!siU",function ($input){
		global $site;
	return "<url>".$site."/?a=".base64_encode($input[1])."</url>\r";
},$file);



if(file_put_contents($var1, $file)){
	echo "ok, file created";
}


}

##
#https://geberit-shop.ru/feednew/yandex.xml был изменен: April 19 2021 10:06:05.
#https://geberit-shop.ru/feednew/yandex_spb.xml был изменен: April 19 2021 10:06:13.
#https://geberit-shop.ru/feednew/yandex_ekb.xml был изменен: April 19 2021 10:06:09.
#https://geberit-shop.ru/feednew/yandex_kdr.xml был изменен: April 19 2021 10:06:17.
#
#


# выполнение каждые 15 минут напримре по CRON или сразу после генерации прайс-листов..
echo sovetnik_generate($site."/feednew/yandex.xml",$_SERVER['DOCUMENT_ROOT']."/feednew/yandex_sov.xml");
echo sovetnik_generate($site."/feednew/yandex_spb.xml",$_SERVER['DOCUMENT_ROOT']."/feednew/yandex_spb_sov.xml");
echo sovetnik_generate($site."/feednew/yandex_ekb.xml",$_SERVER['DOCUMENT_ROOT']."/feednew/yandex_ekb_sov.xml");
echo sovetnik_generate($site."/feednew/yandex_kdr.xml",$_SERVER['DOCUMENT_ROOT']."/feednew/yandex_kdr_sov.xml");






?>