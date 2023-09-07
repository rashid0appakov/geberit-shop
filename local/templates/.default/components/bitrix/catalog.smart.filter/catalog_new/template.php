<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


?>
<style>
	.filter__checkbox-link{
		margin-top:  -5px !important;
	}

	.dropdown-inner .filter__checkbox a{
		padding-left:  1 em;
	}
</style>
<script type="text/javascript">
	function checkedFilter(target){
		  $("#"+target).click();
		return false;
	}
	function clearfilter(target){
		window.location.href = target;
	}
</script>
<?if(!$GLOBALS['global_debug']):
	$this->setFrameMode(true);

	$paramsPropId = 3520;

	$accordionId = 3;
	$this->addExternalJS(SITE_DEFAULT_PATH."/js/filter-price/filter-price.js");
	
	$arChecked = [];
	
	// pr($arResult);

	// die;
	
	// NOTE эту логику стоило бы убрать из шаблона, если потом будет время //Латык Р.
	$arSelected['BRAND'] = [];
	$arSelected['BRAND_CNT'] = 0;
	$part1 = $part2 = [];
	
	$arItemsBrand = Array();
	if ("Y" == $arResult["IS_ORDERED_PARAMS"])
	{
		$arItemsBrand = $arResult["ORDERED_ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER']]['VALUES'];
	}
	else
	{
		$arItemsBrand = $arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER']]['VALUES'];
	}

	foreach($arItemsBrand as $id=>$arBrand)
	{
		$arBrand['KEY'] = $id;
		if($arBrand['CHECKED'])
		{
			$arSelected['BRAND'][$id] = $id;
			$arSelected['BRAND_CNT'] ++;
			$part1[$id] = $arBrand;
		}
		else
		{
			$part2[$id] = $arBrand;
		}
	}

	// допустимые серии
	global $man_show;

	//var_dump($man_show);
	$series_codes = array();
    $arSelect = Array("ID", "CODE");
    $arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_BRAND' => $man_show);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $series_codes[] = $arFields['CODE'];
    }	

    // if ($USER->IsAdmin()){
    // 	echo "<pre>";
    // 		var_dump($series_codes);	
    // 	echo "</pre>";
    // 	die;
    // }


    
	
	
	if ("Y" == $arResult["IS_ORDERED_PARAMS"])
	{
		$arResult["ORDERED_ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER']]['VALUES'] = array_values($part1 + $part2);
	}
	else
	{
		$arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER']]['VALUES'] = array_values($part1 + $part2);
	}
	unset($arItemsBrand);
	
	$arSelected['SER'] = [];
	$part1 = $part2 = [];
	foreach($arSelected['BRAND'] as $idBrand)
	{
		foreach($GLOBALS['PAGE_DATA']['INFO_BRAND'][$idBrand]['SERIES'] as $idSer)
		{
			$arSelected['SER'][$idSer] = $idSer;
		}
	}
	
	$arItemsSeries = Array();
	if ("Y" == $arResult["IS_ORDERED_PARAMS"])
	{
		$arItemsSeries = $arResult["ORDERED_ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'];
	}
	else
	{
		$arItemsSeries = $arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'];
	}
	
                    if(isset($GLOBALS[$arParams["FILTER_NAME"]]["=PROPERTY_SERIES"]) && count($GLOBALS[$arParams["FILTER_NAME"]])<=2 &&count($GLOBALS[$arParams["FILTER_NAME"]]["=PROPERTY_SERIES"])==1){
	                    $arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'][$GLOBALS[$arParams["FILTER_NAME"]]["=PROPERTY_SERIES"][0]]['CHECKED']=true;
	                    $arResult["CHECKED"] = true;
	                    
	                    $URL_ID = $arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'][$GLOBALS[$arParams["FILTER_NAME"]]["=PROPERTY_SERIES"][0]]['URL_ID'];
	                    $arResult ["JS_FILTER_PARAMS"]["SEF_SET_FILTER_URL"]=str_replace('/'.$URL_ID.'/', '/series-is-'.$URL_ID.'/', $arResult ["JS_FILTER_PARAMS"]["SEF_SET_FILTER_URL"]);
						$arResult ["JS_FILTER_PARAMS"]["SEF_DEL_FILTER_URL"]=str_replace('/'.$URL_ID.'/', '/', $arResult ["JS_FILTER_PARAMS"]["SEF_DEL_FILTER_URL"]);

						$arResult ["FILTER_URL"]=str_replace('/'.$URL_ID.'/', '/series-is-'.$URL_ID.'/', $arResult["FILTER_URL"]);
						$arResult["FILTER_AJAX_URL"]=str_replace('/'.$URL_ID.'/', '/series-is-'.$URL_ID.'/', $arResult["FILTER_AJAX_URL"]);
						$arResult["SEF_SET_FILTER_URL"]= str_replace('/'.$URL_ID.'/', '/series-is-'.$URL_ID.'/', $arResult["SEF_SET_FILTER_URL"]);

						$arResult["SEF_DEL_FILTER_URL"]=str_replace('/'.$URL_ID.'/', '/', $arResult ["SEF_DEL_FILTER_URL"]);;

						$arResult["FORM_ACTION"]=str_replace('/'.$URL_ID.'/', '/series-is-'.$URL_ID.'/', $arResult["FORM_ACTION"]);

	                 }
	                  $arParams["URL"] = str_replace('/clear/', '/', $arResult ["JS_FILTER_PARAMS"]["SEF_DEL_FILTER_URL"]);
	                
	$REQUEST_URI_arr = explode('?', $_SERVER['REQUEST_URI']);
    $url = explode('/', $REQUEST_URI_arr[0]);
    $arParams["URL"] = '/'.$url[1].'/'.$url[2].'/';

    if (!in_array($url[3], $series_codes)){
        if (strlen($url[3])>0){	
        	$arParams["URL"] .= $url[3].'/';	
        }	                	
    }

    $url_params = '';
    if (count($REQUEST_URI_arr)>1){
    	$url_params = '?'.$REQUEST_URI_arr[1];
    }

 //    $url = explode('/', $_SERVER['REQUEST_URI']);
	// $arParams["URL"] = '/'.$url[1].'/'.$url[2].'/';

	// if (!in_array($url[3], $series_codes)){
 //        if (strlen($url[3])>0){	
 //        	$arParams["URL"] .= $url[3].'/';	
 //        }	                	
 //    }

	$countSer = count($arSelected['SER']);
	$setSer='';
	foreach($arItemsSeries as $id => $arSerie)
	{
		if(true || $countSer <= 0 || ($countSer > 0 && !isset($arSelected['SER'][$id]))) // ATTENTION пока выводим все коллекции!!!
		{
			$arSerie['KEY'] = $id;
			if($arSerie['CHECKED'])
			{
				$part1[$id] = $arSerie;
				$setSer = $arSerie["URL_ID"];
			}
			else
			{
				$part2[$id] = $arSerie;
			}
		}
	}
	
	if ("Y" == $arResult["IS_ORDERED_PARAMS"])
	{
		$arResult["ORDERED_ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'] = array_values($part1 + $part2);
	}
	else
	{
		$arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES'] = array_values($part1 + $part2);
	}
	unset($arItemsSeries);

	
	// */
	//pr($part1 + $part2);
	//pr($arResult["ITEMS"][$GLOBALS['PAGE_DATA']['CONFIG']['SERIES']]['VALUES']);
	//pr($arResult);
	/*$setFilerNoSeo=false;
	foreach($arResult["ITEMS"] AS &$arItem ) {
		if($arItem["CODE"]=='SERIES') continue;
		foreach($arItem["VALUES"] AS $val => &$arValue) {
			echo "<pre>";
			var_dump($arValue);
			echo "</pre>";
			if (!$arValue["CHECKED"])
				continue;
			$setFilerNoSeo=true;
		}
	}
	global $USER;
	if ($USER->IsAdmin()){
	echo "<pre>";
		var_dump($setFilerNoSeo);
		echo "</pre>";	
	}
	
	if($setFilerNoSeo){
		if(strlen($setSer)>0){
			$arResult["FORM_ACTION"] = str_replace($setSer.'/', '', $arResult["FORM_ACTION"]);
			$arResult["FILTER_AJAX_URL"] = str_replace($setSer.'/', '', $arResult["FILTER_AJAX_URL"]);
		}
	}*/


?>
<? if ($arResult['CHECKED']) {?>
	<div class="selected-items">
		<div class="filter__content">
				<p class="title"><?=GetMessage('SF_SELECTED_FILTERS')?></p>
				<div class="list">
					<? foreach($arResult["ORDERED_ITEMS"] AS &$arItem ) {
						foreach($arItem["VALUES"] AS $val => &$arValue) {
							if (!$arValue["CHECKED"])
								continue;
							//pr($arValue);

							if ($arValue['VALUE'] == 'Да')
								$arValue['VALUE'] = $arItem['NAME'];
							?>
							<span class="selected-item" data-id="<?=$arValue['CONTROL_ID']?>">
								<span class="close filterVariantClose"></span><?=$arValue['VALUE'];?>
							</span>
							<?
						}
					}?>
					<? foreach($arResult["ITEMS"] AS &$arItem ) {
						foreach($arItem["VALUES"] AS $val => &$arValue) {
							if (!$arValue["CHECKED"])
								continue;
							//pr($arValue);

							if ($arValue['VALUE'] == 'Да')
								$arValue['VALUE'] = $arItem['NAME'];
							?>
							<span class="selected-item" data-id="<?=$arValue['CONTROL_ID']?>">
								<span class="close filterVariantClose"></span><?=$arValue['VALUE'];?>
							</span>
							<?
						}
					}?>
					<?foreach($arResult["PROPERTY_GROUPS"] AS $groupProperties) {
						foreach($groupProperties AS &$arItem ) {
							foreach($arItem["VALUES"] AS $val => &$arValue) {
								if (!$arValue["CHECKED"])
									continue;

								if ($arValue['VALUE'] == 'Да')
									$arValue['VALUE'] = $arItem['NAME'];
								?>
								<span class="selected-item" data-id="<?=$arValue['CONTROL_ID']?>">
									<span class="close filterVariantClose"></span><?=$arValue['VALUE'];?>
								</span>
								<?
							}
						}
					}
					?>
				</div>
				<a href="javascript:;" class="clear-filter" onclick="clearfilter('<?if(defined('SITE_SERVER_NAME')):?>https://<?=SITE_SERVER_NAME?><?endif?><?=str_replace('/clear/', '/', $arResult['SEF_DEL_FILTER_URL'])?>');"><?=GetMessage('SF_CLEAR')?></a>
		</div>
	</div>
<? } ?>

<form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="<?=$arResult["FORM_ACTION"]?>" method="get" id="smart-filter">
	<input type="hidden" name="set_filter" value="sent" />
	<input type="hidden" name="current_url" value="<?=$arResult['SEF_FILTER_URL'];?>" />
	<input type="hidden" name="clear_filter" value="<?=$arResult['SEF_DEL_FILTER_URL'];?>" />

	<?//HIDDEN
	foreach($arResult["HIDDEN"] as $arItem):?>
		<input type="hidden" name="<?=$arItem["CONTROL_NAME"]?>" id="<?=$arItem["CONTROL_ID"]?>" value="<?=$arItem["HTML_VALUE"]?>" />
	<?endforeach;?>
	<?// PARAMS
	if (!empty($arResult["ITEMS"][$paramsPropId]["VALUES"])):
		$arItem = $arResult["ITEMS"][$paramsPropId];?>
		<div class="filter__item special">
			<div class="filter__title--toggle <?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
				<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>">Показать только</div>
			</div>
			<div class="filter__content accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
				<ul class="filter__content-list">
					<?foreach($arItem["VALUES"] as $val => $ar):?>
						<li class="filter__content-item">
							<label for="<?=$ar["CONTROL_ID"]?>">
								<input type="checkbox"
									value="<?=$ar["HTML_VALUE"]?>"
									name="<?=$ar["CONTROL_NAME"]?>"
									id="<?=$ar["CONTROL_ID"]?>"
									<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
									<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
									/>
								<span class="text">
									<img src="<?=CFile::ResizeImageGet($ar["FILE"]["ID"], array('width' => 15, 'height' => 15), BX_RESIZE_IMAGE_PROPORTIONAL)["src"]?>">
									<span><?=$ar["VALUE"]?></span>
									<span><?=$ar["ELEMENT_COUNT"]?></span>
								</a>
							</label>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
		<?$accordionId++;
	endif;?>
	<?//PRICES
	foreach ($arResult["ITEMS"] as $key => $arItem):
		if (isset($arItem["PRICE"]) && !!$arItem["PRICE"]):
			if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0) continue;
				$arItem["DISPLAY_EXPANDED"] = 'Y';
                $arItem["VALUES"]["MIN"]["VALUE"] = (int)$arItem["VALUES"]["MIN"]["VALUE"];
                $arItem["VALUES"]["MAX"]["VALUE"] = (int)$arItem["VALUES"]["MAX"]["VALUE"];
				if(isset($arChecked[$arItem["ID"]]))
				{
					continue;
				}
				$arChecked[$arItem["ID"]] = $arItem["ID"];
				?>
			<div class="filter__item">
				<div class="filter__title--toggle<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
					<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>">Цена, руб</div>
				</div>
				<div class="filter__content accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
					<div class="wrapper">
						<div class="extra-controls form-inline">
							<div class="form-group">
								<label for="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>">
									от
									<input type="text"
										class="js-input-from form-control"
										id="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										value="<?echo ($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? : $arItem["VALUES"]["MIN"]["VALUE"]);?>"
										/>
									<input type="hidden" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>">
								</label>
								<label for="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>">
									до
									<input type="text"
										class="js-input-to form-control"
										id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
										value="<?echo ($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? : $arItem["VALUES"]["MAX"]["VALUE"]);?>"
										data-default-value="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>"
										/>
									<input type="hidden" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>">
								</label>
							</div>
						</div>
						<div class="range-slider">
							<input type="text" id="arrFilter_price_<?=$arItem["ID"]?>_slider" value="" />
						</div>
						<?$jsParams = array(
							"sliderSelector" => "#arrFilter_price_{$arItem["ID"]}_slider",
							"minInputSelector" => "#{$arItem["VALUES"]["MIN"]["CONTROL_NAME"]}",
							"maxInputSelector" => "#{$arItem["VALUES"]["MAX"]["CONTROL_ID"]}",

							"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
							"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
							"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
							"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
						);?>
						<script type="text/javascript">
							window.filterItems = window.filterItems || [];
							window.filterItems.push(new JSCatalogSmartFilterSlider(<?=json_encode($jsParams)?>));
						</script>
					</div>
				</div>
			</div>
		<?$accordionId++;
		endif;
	endforeach;?>
	<?//SPECIAL
	$arSpecial = array_filter($arResult["ITEMS"], function($arItem) {
		return in_array($arItem["CODE"], array(
			"NEWPRODUCT",
			"SALELEADER",
			"DISCOUNT",
			"RECOMEND",
			"SHOWROOM",
			"SALEGOODS",
		)) && count($arItem["VALUES"]) > 0;
	});
	//pr($arSpecial);
	$arCheck = [];
	if (count($arSpecial) > 0):
		$arItem["DISPLAY_EXPANDED"] = 'Y';
		?>
		<div class="filter__item">
			<div class="filter__title--toggle<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
				<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>">Акции</div>
			</div>
			<div class="filter__content filter__content--checkbox accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
				<?foreach ($arSpecial as $arItem):
					if(!empty($arCheck[$arItem["CODE"]]))
					{
						continue;
					}
					$arCheck[$arItem["CODE"]] = $arItem["CODE"];
					$arValue = reset($arItem["VALUES"]);?>
					<div class="filter__checkbox">
						<input type="checkbox"
							id="<?=$arValue["CONTROL_ID"]?>"
							name="<?=$arValue["CONTROL_NAME"]?>"
							value="<?=$arValue["HTML_VALUE"]?>"
							<?=$arValue["CHECKED"]? 'checked="checked"': ''?>
							<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
							/>
							
						<label for="<?=$arValue["CONTROL_ID"]?>"><?=$arItem["NAME"]?></label>
						<span><?=$arValue["ELEMENT_COUNT"]?></span>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<?$accordionId++;
	endif;?>
	<?//GROUP
	foreach($arResult["PROPERTY_GROUPS"] as $groupName => $groupProperties):?>
		<?/**
		* Считаем сколько в группе свойств которые нужно показать открытыми
		*/
		$isGrtoupActive = $groupName == 'Производитель' || count(array_filter($groupProperties, function($a) {
			return $a['DISPLAY_EXPANDED'] == 'Y';
		})) ? ' active' : '';
		?>

		<div class="filter__item">
			<div class="filter__title--toggle<?=!$isGrtoupActive ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
				<div aria-expanded="<?=$isGrtoupActive == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=!$isGrtoupActive ? " is-collapsed" : " is-expanded"?>"><?=$groupName?></div>
			</div>
			<div class="filter__content filter__content--checkbox accordion-content accordionItem<?=!$isGrtoupActive ? " is-collapsed" : " is-expanded"?> custom-full-height" id="accordion<?=$accordionId?>" aria-hidden="<?=$isGrtoupActive ? 'false' : 'true'?>">
				<?php foreach($groupProperties as $key => $arItem):
					if (empty($arItem["VALUES"])) continue;
					if( $arItem["NAME"] == '' ) { continue; }
					$accordionId++;
					switch ($arItem["DISPLAY_TYPE"]):
						case "A":
						case "B":
                            $arItem["VALUES"]["MIN"]["VALUE"] = (int)$arItem["VALUES"]["MIN"]["VALUE"];
                            $arItem["VALUES"]["MAX"]["VALUE"] = (int)$arItem["VALUES"]["MAX"]["VALUE"];
                ?>
							<div class="filter__item">
								<div class="filter__title--toggle <?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
									<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>"
									class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"
									><?=$arItem["NAME"]?></div>
								</div>
								<div class="filter__content accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
									<div class="wrapper">
										<div class="extra-controls form-inline">
											<div class="form-group">
												<label for="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>">
													от
													<input type="text"
														class="js-input-from form-control"
														id="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
														name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
														value="<?echo ($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? : $arItem["VALUES"]["MIN"]["VALUE"]);?>"
														/>
													<input type="hidden" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>">
												</label>
												<label for="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>">
													до
													<input type="text"
														class="js-input-to form-control"
														id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
														name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
														value="<?echo ($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? : $arItem["VALUES"]["MAX"]["VALUE"]);?>"
														/>
													<input type="hidden" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>">
												</label>
											</div>
										</div>
										<div class="range-slider">
											<input type="text" id="arrFilter_price_<?=$arItem["ID"]?>_slider" value="" />
										</div>
										<?$jsParams = array(
											"sliderSelector" => "#arrFilter_price_{$arItem["ID"]}_slider",
											"minInputSelector" => "#{$arItem["VALUES"]["MIN"]["CONTROL_NAME"]}",
											"maxInputSelector" => "#{$arItem["VALUES"]["MAX"]["CONTROL_ID"]}",

											"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
											"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
											"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
											"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
										);?>
										<script>
											window.filterItems = window.filterItems || [];
											window.filterItems.push(new JSCatalogSmartFilterSlider(<?=json_encode($jsParams)?>));
										</script>
									</div>
								</div>
							</div>
							<?break;
						default:
							if (count($arItem["VALUES"]) < 2 and $arParams['SHOW_ONE_VALUE'] != 'Y')
								continue;
							?>
							<div class="filter__item">
								<div class="filter__title--toggle<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
									<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"><?=$arItem["NAME"]?></div>
								</div>
								<div class="filter__content filter__content--checkbox accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
									<?$keys = array_keys($arItem["VALUES"]);
									$count = count($keys);
									for ($i = 0; ($showAll or $i < 4 or $arItem["DISPLAY_CHECKED"] == 'Y') && $i < $count; $i++):
										$val = $keys[$i];
										$ar = $arItem["VALUES"][$val];?>
										<div class="filter__checkbox">
											<input type="checkbox"
												id="<?=$ar["CONTROL_ID"]?>"
												name="<?=$ar["CONTROL_NAME"]?>"
												value="<?=$ar["HTML_VALUE"]?>"
												<?=$ar["CHECKED"]? 'checked="checked"': ''?>
												<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
												/>

											<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
											<span><?=$ar["ELEMENT_COUNT"]?></span>
										</div>
									<?endfor;
									if (!$showAll and $count > 4 and $arItem["DISPLAY_CHECKED"] != 'Y'):?>
										<div class="dropdown filter__checkbox-link">
											<input class="dropdown-open" type="checkbox" id="accordion<?=$accordionId?>_dropdown" aria-hidden="true" hidden />
											<div class="dropdown-inner">
												<div class="filter__checkbox-link-content">
													<?for (;$i < $count; $i++):
														$val = $keys[$i];
														$ar = $arItem["VALUES"][$val];?>
														<div class="filter__checkbox">
															<input type="checkbox"
																id="<?=$ar["CONTROL_ID"]?>"
																name="<?=$ar["CONTROL_NAME"]?>"
																value="<?=$ar["HTML_VALUE"]?>"
																<?=$ar["CHECKED"]? 'checked="checked"': ''?>
																<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
																/>
															<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
															<span><?=$ar["ELEMENT_COUNT"]?></span>
														</div>
													<?endfor;?>
												</div>
											</div>
											<label for="accordion<?=$accordionId?>_dropdown" class="dropdown-overlay"></label>
										</div>
									<?endif;?>
								</div>
							</div>
							<?break;
					endswitch;
					$accordionId++;
				endforeach;?>
			</div>
		</div>
		<?$accordionId++;
	endforeach;?>
	<?//ORDERED ITEMS
	if (!empty($arResult["ORDERED_ITEMS"]))
	{
		$arDouble = [];
		foreach ($arResult["ORDERED_ITEMS"] as $key => $arItem):
			$showAll = false;
			if(!defined('MAIN_SITE_BRAND') and $key == $GLOBALS['PAGE_DATA']['CONFIG']['SERIES'])
			{
				if(empty($arSelected['SER']))
				{
					continue;
				}
				//$showAll = true;
				$arItem["DISPLAY_EXPANDED"] = "Y";
			}
			if(isset($arDouble[$arItem['ID']])) continue;
			$arDouble[$arItem['ID']] = $arItem['ID'];
			if (isset($arItem["PRICE"]) && !!$arItem["PRICE"]) continue;
			if (empty($arItem["VALUES"])) continue;
			if ($key == $paramsPropId) continue;
			if (array_key_exists($key, $arSpecial)) continue;

			if( $arItem["NAME"] == '' ) { continue; }

			if(isset($arChecked[$arItem["ID"]]))
			{
				continue;
			}
			$arChecked[$arItem["ID"]] = $arItem["ID"];

			switch ($arItem["DISPLAY_TYPE"]):
				case "A":
				case "B":
									$arItem["VALUES"]["MIN"]["VALUE"] = (int)$arItem["VALUES"]["MIN"]["VALUE"];
									$arItem["VALUES"]["MAX"]["VALUE"] = (int)$arItem["VALUES"]["MAX"]["VALUE"];
					?>
					<div class="filter__item">
						<div class="filter__title--toggle <?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
							<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>"
							class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"
							><?=CClass::getNormalNameProp($arItem["NAME"])?></div>
						</div>
						<div class="filter__content accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
							<div class="wrapper">
								<div class="extra-controls form-inline">
									<div class="form-group">
										<label for="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>">
											от
											<input type="text"
												class="js-input-from form-control"
												id="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
												name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
												value="<?echo ($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? : $arItem["VALUES"]["MIN"]["VALUE"]);?>"
												/>
											<input type="hidden" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>">
										</label>
										<label for="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>">
											до
											<input type="text"
												class="js-input-to form-control"
												id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
												name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
												value="<?echo ($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? : $arItem["VALUES"]["MAX"]["VALUE"]);?>"
												/>
											<input type="hidden" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>">
										</label>
									</div>
								</div>
								<div class="range-slider">
									<input type="text" id="arrFilter_price_<?=$arItem["ID"]?>_slider" value="" />
								</div>
								<?$jsParams = array(
									"sliderSelector" => "#arrFilter_price_{$arItem["ID"]}_slider",
									"minInputSelector" => "#{$arItem["VALUES"]["MIN"]["CONTROL_NAME"]}",
									"maxInputSelector" => "#{$arItem["VALUES"]["MAX"]["CONTROL_ID"]}",

									"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
									"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
									"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
									"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
								);?>
								<script>
									window.filterItems = window.filterItems || [];
									window.filterItems.push(new JSCatalogSmartFilterSlider(<?=json_encode($jsParams)?>));
								</script>
							</div>
						</div>
					</div>
					<?break;
				default:
					if (count($arItem["VALUES"]) < 2 and $arParams['SHOW_ONE_VALUE'] != 'Y')
						continue;
					$countTarget = 0;
				foreach ($arItem["VALUES"] as $k=>$val){
					$arItem["VALUES"][$k]['VALUE'] = trim($val['VALUE']);
					$arItem["VALUES"][$k]['UPPER'] = trim($val['UPPER']);
				}
				//asort($arItem["VALUES"]);

				// if ($USER->IsAdmin()){
				// 	echo "<pre>";
				// 	var_dump($arItem["VALUES"]);
				// 	echo "</pre>";
				// 	die;
				// }

				uasort($arItem["VALUES"], function($a, $b) {
				 	return ($a['CHECKED'] < $b['CHECKED']);	
				});


				$val1_arr = array();
				$val2_arr = array();

				foreach ($arItem["VALUES"] as $k=>$v){
					if ($v['CHECKED']){
						$val1_arr[$k] = $v;
					}else{
						$val2_arr[$k] = $v;
					}
				}

				
				uasort($val2_arr, function($a, $b) {
					// if ($a['CHECKED']){
					// 	return ($a['CHECKED']+$b['CHECKED']);	
					// }

					return strnatcmp($a['VALUE'], $b['VALUE']);
					//return ($a['CHECKED'] < $b['CHECKED']) && strnatcmp($a['VALUE'], $b['VALUE']);
				    //
				});

				$arItem["VALUES"] = array_merge($val1_arr, $val2_arr);

				// uasort($arItem["VALUES"], function($a, $b) {
				//  	return ($a['CHECKED'] < $b['CHECKED']);	
				// });


				$keys = array_keys($arItem["VALUES"]);
				$count = count($keys);
				for ($i = 0; $i < $count; $i++)
				{
					$val = $keys[$i];
					$countTarget ++;
				}
					$maxCount = ($key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'] and $arSelected['BRAND_CNT'] > 5) ? $arSelected['BRAND_CNT'] : 5;
					if($countTarget > 0):
					?>
					<div class="filter__item">
						<div class="filter__title--toggle<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
							<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"><?=CClass::getNormalNameProp($arItem["NAME"])?></div>
						</div>
						<?if($countTarget > 10 and ($key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'] or $key == $GLOBALS['PAGE_DATA']['CONFIG']['SERIES'])):?>
							<div class="extra-controls form-inline filter-fast-search-input-block<?if($showAll):?> over-over<?endif?>" id="accordion<?=$accordionId?>_dropdown_search">
								<div class="form-group form-group-single">
									<label>
										<input type="text" class="js-input-from form-control filter-fast-search-input" data-key="<?=$key?>" id="fs<?=$key?>" value="" placeholder="Поиск">
									</label>
								</div>
							</div>
						<?endif?>
						<div class="filter__content filter__content--checkbox accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
							<?
							$keys = array_keys($arItem["VALUES"]);
							$count = count($keys);
							for ($i = 0; (
									$showAll or 
									$i < $maxCount /*or 
									($arItem["DISPLAY_CHECKED"] == 'Y' and $key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'])*/
								) 
								&& 
								$i < $count; 
							$i++):
								$val = $keys[$i];
								$ar = $arItem["VALUES"][$val];?>
								<div class="filter__checkbox filter-fast-search-checkbox filter-fast-search-checkbox-<?=$key?>" data-key="<?=$key?>" data-fast-search="<?=$ar["VALUE"]?>">
									<input type="checkbox"
										id="<?=$ar["CONTROL_ID"]?>"
										name="<?=$ar["CONTROL_NAME"]?>"
										value="<?=$ar["HTML_VALUE"]?>"
										<?=$ar["CHECKED"]? 'checked="checked"': ''?>
										<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
										/>

									<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
									<span><?=$ar["ELEMENT_COUNT"]?></span>
								</div>
							<?endfor;
							if (!$showAll and $count >= $maxCount and $i < $count):?>
								<div class="dropdown filter__checkbox-link">
									<input class="dropdown-open" type="checkbox" id="accordion<?=$accordionId?>_dropdown" data-key="<?=$key?>" data-id="accordion<?=$accordionId?>_dropdown_search" aria-hidden="true" hidden />
									<div class="dropdown-inner">
										<div class="filter__checkbox-link-content">
											<?for (;$i < $count; $i++):
												$val = $keys[$i];
												$ar = $arItem["VALUES"][$val];?>
												<div class="filter__checkbox filter-fast-search-checkbox filter-fast-search-checkbox-<?=$key?>" data-key="<?=$key?>" data-fast-search="<?=$ar["VALUE"]?>">
													<input type="checkbox"
														id="<?=$ar["CONTROL_ID"]?>"
														name="<?=$ar["CONTROL_NAME"]?>"
														value="<?=$ar["HTML_VALUE"]?>"
														<?=$ar["CHECKED"]? 'checked="checked"': ''?>
														<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
														/>
													<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
													<span><?=$ar["ELEMENT_COUNT"]?></span>
												</div>
											<?endfor;?>
										</div>
									</div>
									<?if(!$showAll):?>
										<label for="accordion<?=$accordionId?>_dropdown" class="dropdown-overlay"></label>
									<?endif;?>
								</div>
							<?endif;?>
						</div>
					</div>
					<?
						endif;
					break;
			endswitch;
			$accordionId++;
		endforeach;
	}
	?>
	<?//NOT_PRICES
	$arDouble = [];
	foreach ($arResult["ITEMS"] as $key => $arItem):
		$showAll = false;
		if(!defined('MAIN_SITE_BRAND') and $key == $GLOBALS['PAGE_DATA']['CONFIG']['SERIES'])
		{
			if(empty($arSelected['SER']))
			{
				continue;
			}
			//$showAll = true;
			$arItem["DISPLAY_EXPANDED"] = "Y";
		}
		if(isset($arDouble[$arItem['ID']])) continue;
		$arDouble[$arItem['ID']] = $arItem['ID'];
		if (isset($arItem["PRICE"]) && !!$arItem["PRICE"]) continue;
		if (empty($arItem["VALUES"])) continue;
		if ($key == $paramsPropId) continue;
		if (array_key_exists($key, $arSpecial)) continue;

		if( $arItem["NAME"] == '' ) { continue; }

		if(isset($arChecked[$arItem["ID"]]))
		{
			continue;
		}
		$arChecked[$arItem["ID"]] = $arItem["ID"];

		switch ($arItem["DISPLAY_TYPE"]):
			case "A":
			case "B":
                $arItem["VALUES"]["MIN"]["VALUE"] = (int)$arItem["VALUES"]["MIN"]["VALUE"];
                $arItem["VALUES"]["MAX"]["VALUE"] = (int)$arItem["VALUES"]["MAX"]["VALUE"];
        ?>
				<div class="filter__item">
					<div class="filter__title--toggle <?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
						<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>"
						class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"
						><?=CClass::getNormalNameProp($arItem["NAME"])?></div>
					</div>
					<div class="filter__content accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
						<div class="wrapper">
							<div class="extra-controls form-inline">
								<div class="form-group">
									<label for="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>">
										от
										<input type="text"
											class="js-input-from form-control"
											id="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
											name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
											value="<?echo ($arItem["VALUES"]["MIN"]["HTML_VALUE"] ? : $arItem["VALUES"]["MIN"]["VALUE"]);?>"
											/>
										<input type="hidden" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>">
									</label>
									<label for="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>">
										до
										<input type="text"
											class="js-input-to form-control"
											id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
											name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
											value="<?echo ($arItem["VALUES"]["MAX"]["HTML_VALUE"] ? : $arItem["VALUES"]["MAX"]["VALUE"]);?>"
											/>
										<input type="hidden" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>-default" value="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>">
									</label>
								</div>
							</div>
							<div class="range-slider">
								<input type="text" id="arrFilter_price_<?=$arItem["ID"]?>_slider" value="" />
							</div>
							<?$jsParams = array(
								"sliderSelector" => "#arrFilter_price_{$arItem["ID"]}_slider",
								"minInputSelector" => "#{$arItem["VALUES"]["MIN"]["CONTROL_NAME"]}",
								"maxInputSelector" => "#{$arItem["VALUES"]["MAX"]["CONTROL_ID"]}",

								"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
								"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
								"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
								"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
							);?>
							<script>
								window.filterItems = window.filterItems || [];
								window.filterItems.push(new JSCatalogSmartFilterSlider(<?=json_encode($jsParams)?>));
							</script>
						</div>
					</div>
				</div>
				<?break;
			default:
				if (count($arItem["VALUES"]) < 2 and $arParams['SHOW_ONE_VALUE'] != 'Y')
					continue;
				/*uasort($arItem["VALUES"], function($a,$b) {
				    return ($a['CHECKED']+$b['CHECKED']);
				});*/
				$countTarget = 0;

				foreach ($arItem["VALUES"] as $k=>$val){
					$arItem["VALUES"][$k]['VALUE'] = trim($val['VALUE']);
					$arItem["VALUES"][$k]['UPPER'] = trim($val['UPPER']);
				}
				//asort($arItem["VALUES"]);

				// if ($USER->IsAdmin()){
				// 	echo "<pre>";
				// 	var_dump($arItem["VALUES"]);
				// 	echo "</pre>";
				// 	die;
				// }

				uasort($arItem["VALUES"], function($a, $b) {
				 	return ($a['CHECKED'] < $b['CHECKED']);	
				});


				$val1_arr = array();
				$val2_arr = array();

				foreach ($arItem["VALUES"] as $k=>$v){
					if ($v['CHECKED']){
						$val1_arr[$k] = $v;
					}else{
						$val2_arr[$k] = $v;
					}
				}

				
				uasort($val2_arr, function($a, $b) {
					// if ($a['CHECKED']){
					// 	return ($a['CHECKED']+$b['CHECKED']);	
					// }

					return strnatcmp($a['VALUE'], $b['VALUE']);
					//return ($a['CHECKED'] < $b['CHECKED']) && strnatcmp($a['VALUE'], $b['VALUE']);
				    //
				});

				$arItem["VALUES"] = array_merge($val1_arr, $val2_arr);

				if (strpos($_SERVER['REQUEST_URI'], '/catalog/newproduct/') !== false){
					if($arItem['CODE'] == 'SERIES'){
						continue;
					}
				}

				// uasort($arItem["VALUES"], function($a, $b) {
				//  	return ($a['CHECKED'] < $b['CHECKED']);	
				// });
				
				$keys = array_keys($arItem["VALUES"]);
				$count = count($keys);
				for ($i = 0; $i < $count; $i++)
				{
					$val = $keys[$i];
					$countTarget ++;
				}
				$maxCount = ($key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'] and $arSelected['BRAND_CNT'] > 5) ? $arSelected['BRAND_CNT'] : 5;
				if($countTarget > 0):
				?>
				<div class="filter__item">
					<div class="filter__title--toggle<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" data-id="accordion<?=$accordionId?>">
						<div aria-expanded="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'true' : 'false'?>" aria-controls="accordion<?=$accordionId?>" class="filter__title accordion-title accordionTitle js-accordionTrigger1<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>"><?=CClass::getNormalNameProp($arItem["NAME"])?></div>
					</div>
					<?if($countTarget > 10 and ($key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'] or $key == $GLOBALS['PAGE_DATA']['CONFIG']['SERIES'])):?>
						<div class="extra-controls form-inline filter-fast-search-input-block<?if($showAll):?> over-over<?endif?>" id="accordion<?=$accordionId?>_dropdown_search">
							<div class="form-group form-group-single">
								<label>
									<input type="text" class="js-input-from form-control filter-fast-search-input" data-key="<?=$key?>" id="fs<?=$key?>" value="" placeholder="Поиск">
								</label>
							</div>
						</div>
					<?endif?>
					<div class="filter__content filter__content--checkbox accordion-content accordionItem<?=$arItem["DISPLAY_EXPANDED"] != "Y" ? " is-collapsed" : " is-expanded"?>" id="accordion<?=$accordionId?>" aria-hidden="<?=$arItem["DISPLAY_EXPANDED"] == 'Y' ? 'false' : 'true'?>">
						<?
						$keys = array_keys($arItem["VALUES"]);
						$count = count($keys);
						for ($i = 0; (
								$showAll or 
								$i < $maxCount /*or 
								($arItem["DISPLAY_CHECKED"] == 'Y' and $key == $GLOBALS['PAGE_DATA']['CONFIG']['MANUFACTURER'])*/
							) 
							&& 
							$i < $count; 
						$i++):
							$val = $keys[$i];
							$ar = $arItem["VALUES"][$val];


							if($arItem["CODE"]=='SERIES'){
								if (!in_array($ar["URL_ID"], $series_codes)){
									continue;
								}
							}

							?>
							<div class="filter__checkbox filter-fast-search-checkbox filter-fast-search-checkbox-<?=$key?>" data-key="<?=$key?>" data-fast-search="<?=$ar["VALUE"]?>">
								<input type="checkbox"
									id="<?=$ar["CONTROL_ID"]?>"
									name="<?=$ar["CONTROL_NAME"]?>"
									value="<?=$ar["HTML_VALUE"]?>"
									<?=$ar["CHECKED"]? 'checked="checked"': ''?>
									<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
									/>
                                <?
                                $allowLinks = true;
                                foreach($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arParams['SECTION_ID']] as $tag) {
                                    if($arParams["URL"]==$tag['CODE_NEW']) { $allowLinks = false; }
                                }?>
								<?if($arItem["CODE"]=='SERIES' && $allowLinks){
									?>
										<label for="<?=$ar["CONTROL_ID"]?>"></label>
										<a href="<?=$arParams["URL"]?><?=$ar["URL_ID"]?>/" style="padding-left: .75em;font-size: 13px;color: #161515" onclick="checkedFilter('<?=$ar["CONTROL_ID"]?>'); return false;"><?=$ar["VALUE"]?></a>
										<?
								}else{?>
									<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
								<?}?>
								
								<span><?=$ar["ELEMENT_COUNT"]?></span>
							</div>
						<?endfor;
						if (!$showAll and $count >= $maxCount and $i < $count):?>
							<div class="dropdown filter__checkbox-link">
								<input class="dropdown-open" type="checkbox" id="accordion<?=$accordionId?>_dropdown" data-key="<?=$key?>" data-id="accordion<?=$accordionId?>_dropdown_search" aria-hidden="true" hidden />
								<div class="dropdown-inner">
									<div class="filter__checkbox-link-content">
										<?for (;$i < $count; $i++):
											$val = $keys[$i];
											$ar = $arItem["VALUES"][$val];

											if($arItem["CODE"]=='SERIES'){
												if (!in_array($ar["URL_ID"], $series_codes)){
													continue;
												}
											}

											?>
											<div class="filter__checkbox filter-fast-search-checkbox filter-fast-search-checkbox-<?=$key?>" data-key="<?=$key?>" data-fast-search="<?=$ar["VALUE"]?>">
												<input type="checkbox"
													id="<?=$ar["CONTROL_ID"]?>"
													name="<?=$ar["CONTROL_NAME"]?>"
													value="<?=$ar["HTML_VALUE"]?>"
													<?=$ar["CHECKED"]? 'checked="checked"': ''?>
													<? if( $ar["DISABLED"] ) { ?> disabled <? } ?>
													/>
                                                    <?
                                                    $allowLinks = true;
                                                    foreach($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'][$arParams['SECTION_ID']] as $tag) {
                                                        if($arParams["URL"]==$tag['CODE_NEW']) { $allowLinks = false; }
                                                    }?>
                                                    <?if($arItem["CODE"]=='SERIES' && $allowLinks){

														?>
														<label for="<?=$ar["CONTROL_ID"]?>"></label>
														<a href="<?=$arParams["URL"]?><?=$ar["URL_ID"]?>/" style="padding-left: .75em;font-size: 13px;color: #161515" onclick="checkedFilter('<?=$ar["CONTROL_ID"]?>'); return false;"><?=$ar["VALUE"]?></a>
														<?
													}else{?>
														<label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
													<?}?>
												
												<span><?=$ar["ELEMENT_COUNT"]?></span>
											</div>
										<?endfor;?>
									</div>
								</div>
								<?if(!$showAll):?>
									<label for="accordion<?=$accordionId?>_dropdown" class="dropdown-overlay"></label>
								<?endif;?>
							</div>
						<?endif;?>
					</div>
				</div>
				<?
					endif;
				break;
		endswitch;
		$accordionId++;
	endforeach;?>
	<div class="filter__item filter__item-button">
		<button type="reset" name="del_filter" class="filter__button filter__button--clean"><?=GetMessage('SF_CLEAR_BUTTON')?></button>
		<button type="submit" name="set_filter" class="filter__button"><?=GetMessage('SF_SUBMIT_BUTTON')?></button>
		<button type="reset" name="del_filter" class="filter__button filter__button-mobile filter__button--clean"><?=GetMessage('SF_CLEAR_BUTTON')?></button>
		<button type="submit" name="set_filter" class="filter__button filter__button-mobile filter__button-mobile--blue"><?=GetMessage('SF_SUBMIT_BUTTON')?> <div class="tag is-warning"></div></button>
	</div>
</form>
<?endif?>