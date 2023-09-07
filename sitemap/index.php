<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
?>

<div class="goods">
	<div class="container goods__container">
		<div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "main",
                Array(
                    "PATH"      => "",
                    "SITE_ID"   => SITE_ID,
                    "START_FROM"=> "0"
                )
            );?>
		</div>
		<div class="goods__title">
			<h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
		</div>
		<div class="goods__wrapper page_product_viewed">
			<ul>
				<?
				$arSection = $GLOBALS['PAGE_DATA']['MENU']['CATALOG']['ITEMS'];
				
				$arItems = array();
				$tmp = array(0 => &$arItems);
				foreach ($arSection as $arItem)
				{
					$arItem["ITEMS"] = array();
					$dl = (int) $arItem[3]["DEPTH_LEVEL"];
					$tmp[$dl] =& $tmp[$dl - 1][array_push($tmp[$dl - 1], $arItem) - 1]["ITEMS"];
				}
				unset($tmp);
				$arSection["ITEMS"] = $arItems;

				foreach($arSection["ITEMS"] as $arItem){
					?>
					<li><a href="<?=$arItem[1]?>"><?=$arItem[0]?></a></li>
					<?
					if(!empty($arItem["ITEMS"])){
						foreach($arItem["ITEMS"] as $arSubItem){
							?>
							<li><a href="<?=$arSubItem[1]?>"><?//='..'?><?=$arSubItem[0]?></a></li>
							<?
                            /**/
                            if(!empty($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arSubItem[3]["ID"]])){
                                foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arSubItem[3]["ID"]] AS $arTag){
                                    if (isset($arTag['THIS_PARENT'])){
                                        continue;
                                    }?>
                                    <li><a href="<?=$arSubItem[1].$arTag["CODE"].'/'?>"><?//='..'?><?=$GLOBALS['PAGE_DATA']['SEO_FILTER']['PAGE_SEO'][$arTag['CODE']]['HEADER']//($arTag["NAME_MENU"] ? $arTag["NAME_MENU"] : $arTag["NAME"])?></a></li>
                                    <?
                                }
                            }/**/
						}
					}
					
					if(!empty($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem[3]["ID"]])){
						foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem[3]["ID"]] AS $arTag){
							if (isset($arTag['THIS_PARENT'])){
								continue;
							}
							?>
							<li><a href="<?=$arItem[1].$arTag["CODE"].'/'?>"><?//='..'?><?=$GLOBALS['PAGE_DATA']['SEO_FILTER']['PAGE_SEO'][$arTag['CODE']]['HEADER']//($arTag["NAME_MENU"] ? $arTag["NAME_MENU"] : $arTag["NAME"])?></a></li>
							<?
						}
					}
				}
				?>
			</ul>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>