<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
	die();


if (strpos($_SERVER['REQUEST_URI'], '/catalog/newproduct/') !== false){
	return '';
}

$arResult = $arParams['arSection'];

$additional_filter = '';
//if($GLOBALS['USER']->IsAdmin()){
	//pr($_SERVER);
	foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']["PAGE_SEO"] as $k=>$v){
		// var_dump($_SERVER['REQUEST_URI']);
		// var_dump($k);
		if (strpos($_SERVER['REQUEST_URI'], $k)!==false){
			// var_dump($k);
			// var_dump($v);
			//var_dump($_GET['filter']);
			//var_dump($v['FILTER_URL_PAGE']);
			if (strpos($_GET['filter'], $v['FILTER_URL_PAGE'])===false){
				$additional_filter = $v['FILTER_URL_PAGE'];
			//	var_dump($additional_filter);
			}
		}
	}
	// echo "<pre>";
	// 	var_dump($arSeo = $GLOBALS['PAGE_DATA']['SEO_FILTER']);
	// echo "<pre>";
//}

$arFilterWizzardProperties = array();
$arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'ID'=>$arResult['ID']);
//var_dump($arFilter);
$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array('UF_*'));
if($ar_result = $db_list->GetNext())
{
    $arFilterWizzardProperties = $ar_result['UF_FILTER_WIZZART'];
}

//var_dump($arFilterWizzardProperties);


if (is_array($arFilterWizzardProperties) && count($arFilterWizzardProperties)>0){

	// $list = CIBlockSection::GetNavChain(false, $arResult['ID'], array(), true);
	// foreach ($list as $arSectionPath){
	//     $arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'ID'=>$arSectionPath['ID']);
	//     $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array('UF_*'));
	//     if($ar_result = $db_list->GetNext())
	//     {
	//         $arFilterWizzardProperties = $ar_result['UF_FILTER_WIZZART'];
	//     }

	//     if (is_array($arFilterWizzardProperties) && count($arFilterWizzardProperties)>0){
	//         break;
	//     }

	//     // if (count($arAnalogueProperties)<0)
	//  //           echo '<pre>';print_r($arSectionPath);echo '</pre>';
	//     // }
	// }

	//var_dump($arFilterWizzardProperties);

	global $filter_data;


	if (is_array($arFilterWizzardProperties) && count($arFilterWizzardProperties)>0){
		$temp_arr = array();
		foreach ($arFilterWizzardProperties as $code){
			foreach ($filter_data['ITEMS'] as $id => $item){
				//var_dump(strtolower($item['CODE']));
				if (strtolower($item['CODE']) == $code){
					$temp_arr[$id] = $item;
				}
			}
		}

		$filter_data['ITEMS'] = $temp_arr;
	}

	// echo "<pre>";
	// var_dump($filter_data);
	// echo "</pre>";


	//pr(count($filter_data['ITEMS']));


	$show_item = false;
	foreach ($filter_data['ITEMS'] as $id => $item){

		if (in_array($item['CODE'], array('MANUFACTURER', 'SERIES'))){
			continue;
		}

		// if ($item['PROPERTY_TYPE'] != 'N'){
		// 	continue;
		// }

		if (count($item['VALUES'])<=0)
			continue;	

		// if($GLOBALS['USER']->IsAdmin()){
		// 	pr($item);
		// }

		//var_dump($item);
		
		$code = strtolower($item['CODE']);
		if (isset($_GET['filter'])){
			if (strpos($_GET['filter'], $code) !== false) {
				continue;
			}else{
				$show_item = $item;
				break;
			}
		}else{
			$show_item = $item;
			break;
		}

		// foreach ($item['VALUES'] as $value_arr) {
			
		// }
	}


	//var_dump($show_item);

	?>
	<?
	global $USER;
	if (is_array($show_item) && (count($show_item['VALUES'])>0)){
	?>
		<style>
			div.filter-helper__value:hover { color: #363636; }
		</style>
		<div class="filter-helper filter-helper_active">
			<div class="filter-helper__title"><?=$show_item['NAME']?>:</div>
			<div class="filter-helper__items">
				<?
				$counter = 0;
				$vals = array();
				$is_number = false;
				foreach($show_item['VALUES'] as $arItem){
					if ($item['PROPERTY_TYPE'] == 'N'){
						$is_number = true;
						$vals[] = $arItem["VALUE"];
					}
				}

				if ($is_number){
					foreach($show_item['VALUES'] as $arItem){
						$from = $vals[0];
						//var_dump($vals);
						
						while ($from < $vals[1]){
							$to = $from+10;
							if ($to > $vals[1]){
								$to = $vals[1];
							}
							$obElement = \CIBlockElement::GetList(
			                    [],
			                    array_merge($GLOBALS[$arParams["FILTER_NAME"]],['IBLOCK_ID'=>$arResult['IBLOCK_ID'],'ACTIVE'=>'Y','INCLUDE_SUBSECTIONS'=>'Y','SECTION_ID'=>$arResult['ID'],'><PROPERTY_'.$show_item["CODE"]=>[$from,$to]]),
			                    false,
			                    ['nTopCount'=>1],
			                    ["ID","NAME"]
			                );
			                if($arItem = $obElement->fetch()) {
								$url = '?filter='.$code.'-from-'.$from.'-to-'.$to;
								if (isset($_GET['filter'])){
									$url = '?filter='.$_GET['filter'].'%2F'.$code.'-from-'.$from.'-to-'.$to;
								}
								?>
								<div class="filter-helper__item">
									<div <?/*/?>href="<?=$url?>"<?/**/?> title="<?=$arItem["VALUE"]?>" class="filter-helper__value"  data-url="<?=$url?>">
										<?/*<img src="https://top-santehnika.ru/upload/iblock/0a2/0a2d116f59ff9c7ac03a098e10f10841.svg" class="filter-helper__img">*/?>
										<?
										echo $from.'-'.$to;
										?>
									</div>
								</div>
								<?
							}
							$from += 10;
						}
						break;
					}
				}


				foreach($show_item['VALUES'] as $arItem):
					$counter++;
					if($arItem["SELECTED"]=='Y') { continue; }
					$code = strtolower($item['CODE']);

					$predlog = '-is-';
					$val = $arItem["URL_ID"];
					if ($item['PROPERTY_TYPE'] == 'N'){
						continue;
						$val = $arItem["VALUE"];
						$predlog = '-from-';
						if ($counter > 1){
							$predlog = '-to-';
						}
					}

					



					$url = '?filter='.$code.$predlog.$val;
					if (isset($_GET['filter'])){
						$url = '?filter='.$_GET['filter'].'%2F'.$code.$predlog.$val;
					}

					if (strlen($additional_filter)>0){
						// var_dump($url);
						// var_dump($additional_filter);
						$url = $url.'%2F'.$additional_filter;
					}

					
					?>
					<div class="filter-helper__item">
						<div <?/*/?>href="<?=$url?>"<?/**/?> title="<?=$arItem["VALUE"]?>" class="filter-helper__value" data-url="<?=$url?>">
							<?/*<img src="https://top-santehnika.ru/upload/iblock/0a2/0a2d116f59ff9c7ac03a098e10f10841.svg" class="filter-helper__img">*/?>
							<?
							if ($item['PROPERTY_TYPE'] == 'N') {
								$str =  "от ";
								if ($counter > 1){
									$str =  "до ";
								}

								echo $str;
							}
							?>
							<?=$arItem["VALUE"]?>
							<span class="filter-helper__count"><?if ($arItem["ELEMENT_COUNT"]>0){ ?>(<?=$arItem["ELEMENT_COUNT"]?>)<?}?></span>
						</div>
					</div>
					<?
					?>
				<? endforeach; ?>
			</div>
		</div>
		<script>
			$(function() {
				$("div.filter-helper__value").click(function() {
					document.location.href = $(this).data("url");
				});
			});
		</script>
	<? } /*else: ?>
		<?=$show_item['NAME']?>: 
					<?foreach($show_item['VALUES'] as $arItem){
						
						if($arItem["SELECTED"]=='Y') { continue; }

						$code = strtolower($item['CODE']);
						$url = '?filter='.$code.'-is-'.$arItem["URL_ID"];

						if (isset($_GET['filter'])){
							$url = '?filter='.$_GET['filter'].'%2F'.$code.'-is-'.$arItem["URL_ID"];
						}
						?>
						<a href="<?=$url?>" title="<?=$arItem["VALUE"]?>"><?=$arItem["VALUE"]?></a>
					<?}?>
	<? endif;  */?>	
<?
}

?>