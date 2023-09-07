<?
function customFixUri($uri, $return = false)
{
	$check_uri = current(explode('?', $uri, 2));
	$check_uri = explode('/', $check_uri);
	if($check_uri[1] == 'catalog')
	{
		if($return)
		{
			$new = str_replace('-is-%D0%B4%D0%B0/', '-is-yes/', $uri);
			return $new;
		}
		else
		{
			$new = str_replace('-is-yes/', '-is-%D0%B4%D0%B0/', $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = $new;
		}
	}
	return $uri;
}
customFixUri($_SERVER['REQUEST_URI']);

$arMapSiteIds = array(
	"tiptop-shop.ru" => "s0",
	"swet-online.ru" => "l1",
	"drvt.shop" => "s1",
	"shop-roca.ru" => "s2",
	"hg-online.ru" => "s3",
	"shop-cezares.ru" => "s4",
	"shop-jd.ru" => "s5",
	"shop-gr.ru" => "s6",
	"vb-shop.ru" => "s7",
	//"geberit-shop.ru" => "s8",
	"fbs-market.ru" => "s9",
	"ravak-shop.ru" => "sa",
	"shop-aquaton.ru" => "sb",
);

if(isset($arMapSiteIds[$_SERVER['SERVER_NAME']]))
{
	$uri = current(explode('?', $_SERVER['REQUEST_URI'], 2));
	$uri = explode('/', $uri);
	if($uri[1] == 'catalog')
	{
		if(!empty($uri[3])/* and empty($uri[4])*/ and $uri[3] != 'clear' and strpos($uri[3], '-is-') === false and strpos($uri[3], '-or-') === false and strpos($uri[3], '-to-') === false and strpos($uri[3], '-from-') === false)
		{
			$file = $_SERVER['DOCUMENT_ROOT'].'/local/cache/'.$arMapSiteIds[$_SERVER['SERVER_NAME']].'-cache_page.php';
			if(file_exists($file))
			{
				$data = file_get_contents($file);
				list($php, $json) = explode("\n", $data, 2);
				$GLOBALS['PAGE_DATA'] = json_decode($json, true);
				
				$search = $uri[3];
				$check = false;
				foreach($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'] as $arTags)
				{
					if(isset($arTags[$search]))
					{
						$check = true;
						break;
					}
				}
				if(!$check)
				{
					//if($_SERVER['REMOTE_ADDR'] == '93.94.148.234')
					//{
					//	$GLOBALS['global_debug'] = true;
					//}
					//else
					//{
						$_SERVER['REQUEST_URI'] = '/404.php';
					//}
					//exit;
					//echo '<pre>'; print_r($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS']); echo '</pre>';
					//echo '<pre>'; print_r($uri); echo '</pre>';
					//die('Old link!');
				}
			}
		}
	}
}
?>