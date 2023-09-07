<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//pr($arResult);
global $man_show;

$this->setFrameMode(true);
if (empty($arResult["ITEMS"]))
	return "";
if (!empty($arResult["ITEMS"])){
	// $series_ids = array();
	// $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	// $arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_BRAND' => $man_show);
	// $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	// while($ob = $res->GetNextElement())
	// {
	//     $arFields = $ob->GetFields();
	//     $series_ids[] = $arFields['ID'];
	// }
    foreach ($arResult["ITEMS"] AS $k => &$arItem){
        $html_text = '';
        if(!empty($arItem["ITEMS"])){
            foreach ($arItem["ITEMS"] AS &$arItem2){
				$obCache = new CPHPCache; 
				$life_time = 24*60*60; 
				$cache_id = 'ib_'.CATALOG_IBLOCK_ID.'_sec_id_'.$arItem2['PARAMS']['ID']; 
				// если кеш есть и он ещё не истек то
				if($obCache->InitCache($life_time, $cache_id, "/")){
				    // получаем закешированные переменные
				    $vars = $obCache->GetVars();
				    $html_text .= $vars["html_text"];
				}

				// начинаем буферизирование вывода
				if($obCache->StartDataCache()):
					$text_temp = '';
	                $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	                $arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y","SECTION_ID"=> $arItem2['PARAMS']['ID'],"INCLUDE_SUBSECTIONS"=>"Y","!PROPERTY_DISCONTINUED"=>"Y", 'PROPERTY_MANUFACTURER' => $man_show);
	                $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>1), $arSelect);
	                if (intval($res->SelectedRowsCount())>0){
	                    $text_temp .= '<a href="'.$arItem2["LINK"].'" class="is-size-4">'.$arItem2["TEXT"].'</a>';
	                }
	                foreach ($arItem2["ITEMS"] AS &$arItem3){
	                    $text_temp .= '<a href="'.$arItem3["LINK"].'">- '.$arItem3["TEXT"].'</a>';
	                }

	                $html_text .= $text_temp;

				    $obCache->EndDataCache(array(
				        "html_text"    => $text_temp
				        )); 
				endif;
            }
        }                            
        foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]] AS $arTag){
            if (isset($arTag['THIS_PARENT'])){ continue; }
            if ($arTag['UF_HIDE_IN_MENU'] == 1){ continue; }
            $html_text .= '<a href="'.$arItem["LINK"].$arTag["CODE"].'/'.'" class="is-size-4">'.($arTag["NAME_MENU"] ? $arTag["NAME_MENU"] : $arTag["NAME"]).'</a>';
        }
        $obCache = new CPHPCache; 
		$life_time = 24*60*60; 
		$cache_id = 'ib_'.SERIES_IBLOCK_ID.'_popular'; 
		
		$arRes = [];

		if($obCache->InitCache($life_time, $cache_id, "/")){
		    // получаем закешированные переменные
		    $vars = $obCache->GetVars();
		    $arRes["ITEMS"] = $vars["ITEMS"];
		}

		// начинаем буферизирование вывода
		if($obCache->StartDataCache()):
			$res = CIBlockElement::GetList(["NAME"=>"ASC", "SORT"=>"ASC"], ["IBLOCK_ID" => SERIES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_POPULAR" => "Y"], false, false, ["ID", "NAME", "CODE", "DATE_ACTIVE_FROM"]);
	        while ($item = $res->GetNext()) {
	            if(!CIBlockElement::GetList(Array(), ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", 'PROPERTY_MANUFACTURER' => $man_show, "!PROPERTY_DISCONTINUED" => "Y", "PROPERTY_SERIES" => $item['ID'], "SECTION_ID" => $arItem["PARAMS"]["ID"]], false, false, ["ID"],["nTopCount"=>1])->GetNext()['ID']) { continue; }
	            $arRes["ITEMS"][] = $item;
	        }

		    $obCache->EndDataCache(array(
		        "ITEMS"    => $arRes["ITEMS"]
		        )); 
		endif;
        
        /*$arRes = [];
        $res = CIBlockElement::GetList(["NAME"=>"ASC", "SORT"=>"ASC"], ["IBLOCK_ID" => SERIES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_POPULAR" => "Y"], false, false, ["ID", "NAME", "CODE", "DATE_ACTIVE_FROM"]);
        while ($item = $res->GetNext()) {
            if(!CIBlockElement::GetList(Array(), ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", 'PROPERTY_MANUFACTURER' => $man_show, "!PROPERTY_DISCONTINUED" => "Y", "PROPERTY_SERIES" => $item['ID'], "SECTION_ID" => $arItem["PARAMS"]["ID"]], false, false, ["ID"],["nTopCount"=>1])->GetNext()['ID']) { continue; }
            $arRes["ITEMS"][] = $item;
        }
        */
        
        $arResult["ITEMS"][$k]["html_text"] = $html_text;
        $arResult["ITEMS"][$k]["series"] = $arRes["ITEMS"];
    }
}

$arResult["ITEMS_CHUNK"] = array_chunk($arResult["ITEMS"], 70);
?>
<div class="catalog-menu-popup hero">
	<div class="container is-widescreen">
<?/*		
		<button class="close_pop"><img src="/local/templates/.default/images/icon__cross.png" alt=""></button>
*/?>
		<div class="columns">
			<?
			if (!empty($arResult["ITEMS_CHUNK"])):?>
				<div class="column categories categories__list">
				<? foreach($arResult["ITEMS_CHUNK"] AS &$item)
					foreach($item AS $k => &$arItem):
						$classes = array("btn", "categories__list-item");
                        if (count($arResult["ITEMS"][$k]["series"])>0 || strlen($arResult["ITEMS"][$k]["html_text"])>0) { $classes[]="has-child"; }
						if ($arItem["PARAMS"]["HIGHLIGHT"])
							$classes[] = "categories__list-item--active";
						?>
						<a class="<?=implode(" ", $classes)?>" href="<?=$arItem["LINK"]?>" data-cat="#categories__tab-<?=$arItem["PARAMS"]["ID"]?>" title="<?=$arItem["TEXT"]?>"><?=$arItem["TEXT"]?></a>
					<?endforeach;?>
				</div>
			<?endif;

			if (!empty($arResult["ITEMS"])){
				foreach ($arResult["ITEMS"] AS $k => &$arItem):
					?>
					<div class="categories__content<?=(!$k ? " categories__content--active" : "")?>" id="categories__tab-<?=$arItem["PARAMS"]["ID"]?>">
						<?/**/
                        if(strlen($arItem["html_text"])>0){?>
                            <div class="column subcategories"><?=$arItem["html_text"]?></div><?
                        }    
                        if(count($arItem["series"])>0){?>
                            <div class="column subcategories">
                                <div class="is-size-4" style="line-height:18px;margin-top:13px;font-size:15px!important;font-family:RobotoBold;">Популярные серии</div><?
                                foreach ($arItem["series"] as $item){?>
                                    <a href="<?=$arItem["LINK"].$item["CODE"].'/'?>" class="is-size-4" style="font-family:'RobotoLight';line-height:10px;"><?=$item["NAME"]?></a><?
                                }?>
                            </div><?
                        }
					/*
					if (empty($arItem["ITEMS"]) && empty($arResult["PROMO"][$arItem["PARAMS"]["ID"]]) && empty($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]]))
						continue;
					?>
					<div class="categories__content<?=(!$k ? " categories__content--active" : "")?>" id="categories__tab-<?=$arItem["PARAMS"]["ID"]?>">
						<?
						if(!empty($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]]) || !empty($arItem["ITEMS"])){
							?>
							<div class="column subcategories">
								<?
								if(!empty($arItem["ITEMS"])){
									foreach ($arItem["ITEMS"] AS &$arItem2){
										$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
								        $arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y","SECTION_ID"=> $arItem2['PARAMS']['ID'],"INCLUDE_SUBSECTIONS"=>"Y","!PROPERTY_DISCONTINUED"=>"Y");
								        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>1), $arSelect);
								        if (intval($res->SelectedRowsCount())>0){
										?>
										<a href="<?=$arItem2["LINK"]?>" class="is-size-4"><?=$arItem2["TEXT"]?></a>
										<?
										 }
										foreach ($arItem2["ITEMS"] AS &$arItem3){
											?>
											<a href="<?=$arItem3["LINK"]?>">- <?=$arItem3["TEXT"]?></a>
											<?
										}
									}									
								}
								
								if(!empty($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]])){
									foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arItem["PARAMS"]["ID"]] AS $arTag){
										if (isset($arTag['THIS_PARENT'])){
											continue;
										}

										if ($arTag['UF_HIDE_IN_MENU'] == 1){
											continue;
										}

										?>
										<a href="<?=$arItem["LINK"].$arTag["CODE"].'/'?>" class="is-size-4"><?=$arTag["NAME_MENU"] ? $arTag["NAME_MENU"] : $arTag["NAME"]?></a>
										<?
									}
								}
								?>
							</div>
						<?
						}/*
						if (!empty($arResult["PROMO"][$arItem["PARAMS"]["ID"]])){?>
							<div class="popular-column column">
								<p class="is-size-4"><?=GetMessage('PM_POPULAR_TITLE')?> <?=$arItem["TEXT"]?></p>
								<div class="popular">
								<? foreach ($arResult["PROMO"][$arItem["PARAMS"]["ID"] ] AS &$arPromo) { ?>
									<a class="product-promo" href="<?=$arPromo["DETAIL_PAGE_URL"]?>" target="_blank">
										<img src="<?=$arPromo["DETAIL_PICTURE"]["src"]?>" alt="<?=$arPromo["PRODUCT"]["NAME"]?>">
										<div class="info">
											<div class="name"><?=$arPromo["NAME"]?></div>
											<div class="price-block">
												<?/*if ( $arPromo['PRICE']['PRICE'] != $arPromo['PRICE']["DISCOUNT_PRICE"] ):?>
													<div class="old-price"><?=number_format($arPromo["PRODUCT"]["BASE_PRICE"], 0, ".", " ")?> р.</div>
												<?endif;*//*?>
												<div class="price"><?=customFormatPrice($arPromo['PRICE']['PRICE'])?></div>
											</div>
										</div>
									</a>
								<? } ?>
								</div>
							</div>
						<? } */?>
					</div>
				<?endforeach;
			}?>
		</div>
	</div>
</div>