<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$uid = uniqid();

$jsParams = array(
	"uid" => $uid,
);
$i = 0;

$jsonArray = array();
$a = 0;
?>

<ul id="breadcrumbs_<?=$uid?>" class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
	<?foreach ($arResult["ITEMS"] as $item):

	if ($item["URL"] == '/catalog/karta-razdelov-tiptop/') {$item["URL"] = '/catalog/'; $item["NAME"] = 'Каталог';}

		//var_dump($item["URL"]);

		

		$jsonArray[$a]['@type'] = 'ListItem';
		$jsonArray[$a]['position'] = $i;
		$jsonArray[$a]['item']['@id'] = $item["URL"];
		$jsonArray[$a]['item']['name'] = $item["NAME"];
		
		$hasChildren = count($item["ITEMS"]) > 0;
		
		$altNameProduct = "";
		if(!empty($GLOBALS['PAGE_DATA']['INFO_BRAND'][$item["PROP"]["MANUFACTURER"]]['NAME'])){
			$altNameProduct .= $GLOBALS['PAGE_DATA']['INFO_BRAND'][$item["PROP"]["MANUFACTURER"]]['NAME'];
		}
		if(!empty($GLOBALS['PAGE_DATA']['INFO_SERIES'][$item["PROP"]["SERIES"]]['NAME'])){
			$altNameProduct .= " ".$GLOBALS['PAGE_DATA']['INFO_SERIES'][$item["PROP"]["SERIES"]]['NAME'];
		}
		if(!empty($item["PROP"]["ARTNUMBER"])){
			$altNameProduct .= " ".$item["PROP"]["ARTNUMBER"];
		}
		
		$itemName = preg_match('/\/product\//', $item["URL"]) && SITE_ID == 'l1' ? $altNameProduct : $item["NAME"];
		?>
		<li class="item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<?if(count($arResult["ITEMS"]) > $i){?>
				<a href="<?=$item["URL"]?>" itemprop="item"><span itemprop="name"><?=$itemName?></span></a>
			<?}
			else{

				    $url = explode('?', $_SERVER['REQUEST_URI']);
				    $url = explode('/', $url[0]);
				    $code='';
				    $url = array_diff($url, array('', null));
				    //$dop_name='';
				        if(count($url)==3||count($url)==4){
				            foreach ($url as $value) {
				                if(strlen($value)>0){
				                    $code=$value;
				                    $arSelect = Array("ID", "NAME", "CODE");
				                    $arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "CODE"=>$code, "ACTIVE"=>"Y");
				                    $res = CIBlockElement::GetList(Array(), $arFilter, false,false, $arSelect);
				                    while($ob = $res->GetNextElement())
				                    {
				                     $arFields = $ob->GetFields();
				                     $dop_name=$arFields['NAME'];//выводим серию в хк(чпу фильтр)
				                    }
				                }
				            }
				            
				        }
				if($dop_name||(isset($_GET['PAGEN_2']) && $_GET['PAGEN_2']>1)){
					?>
					<a href="<?=$item["URL"]?>"<?if(isset($_GET['PAGEN_2']) && $_GET['PAGEN_2']>1){?>style="text-decoration: underline;"<?}?> itemprop="item"><span  itemprop="name"><?=$itemName?></span></a>
					<?
				}else{
					?>
					<span class="not_link"><span><?=$itemName?></span></span>
					<meta itemprop="item" content="https://<?=$_SERVER['SERVER_NAME']?><?=$item["URL"]?>">
					<meta itemprop="name" content="<?=$itemName?>">
					<?
				}
			}?>
			
			<?if ($hasChildren):?>
				<span class="dropdown-icon">
					<img src="<?=$templateFolder?>/images/breadcrumbs__rectangle.png">
					<ul class="dropdown-wrap close">
						<?foreach ($item["ITEMS"] as $child):?>
							<li><a href="<?=$child["URL"]?>"><?=$child["NAME"]?></a></li>
						<?endforeach;?>
					</ul>
				</span>
			<?endif;?>
			<meta itemprop="position" content="<?=$i?>">
		</li>
		<?if($dop_name){
					?>
					<li class="item">
					<span class="not_link"><span><?=$dop_name?></span></span></li>
					<?
				}?>
		<?$i++;$a++;?>
	<?endforeach;?>
</ul>
<script>
	window.CatalogBreadcrumbs = new JSCatalogBreadcrumbs(<?=json_encode($jsParams)?>);
</script>

