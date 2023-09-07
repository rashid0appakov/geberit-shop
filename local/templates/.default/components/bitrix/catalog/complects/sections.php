<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();



$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>15000), $arSelect);
while($ob = $res->GetNextElement())
{
	$arFields = $ob->GetFields();
	
	if( CCatalogProductSet::isProductHaveSet($arFields['ID'], CCatalogProductSet::TYPE_SET) ) {
		$names[] = $arFields['NAME'];
		$ids[] = $arFields['ID'];
	}
	
}


global $arFilter;

if( count( $ids ) > 0 ) {
	$arFilter['ID'] = $ids;
} else {
	$arFilter['ID'] = 1111111;
}

/*
echo "<pre>";
print_r( $arFilter );
echo "</pre>";

die();
*/

use Bitrix\Main\Page\Asset;

$filter = SITE_ID == 's0' ? SITE_DEFAULT_PATH."/js/filter.js" : SITE_DEFAULT_PATH."/js/filter.js";

Asset::getInstance()->addJs($filter);

if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y")
{
	foreach($_REQUEST as $k=>$v)
	{
		if(isset($_REQUEST[$k.'-default']))
		{
			if($v == $_REQUEST[$k.'-default'])
			{
				unset($_REQUEST[$k]);
			}
			unset($_REQUEST[$k.'-default']);
		}
	}
}

if(empty($_SESSION['CATALOG_PP']))
{
	$_SESSION['CATALOG_PP'] = $arParams["PAGE_ELEMENT_COUNT"];
}

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

	$arSpecialSections = [
		'discount'   => ['id' => 46, 'code' => 'DISCOUNT', 'name' => 'Скидки'],
		'newproduct' => ['id' => 44, 'code' => 'NEWPRODUCT', 'name' => 'Новинки'],
		'saleleader' => ['id' => 45, 'code' => 'SALELEADER', 'name' => 'Хиты продаж'],
		'recomend'  => ['id' => 3516, 'code' => 'RECOMEND', 'name' => 'Рекомендуем'],
		'showroom'   => ['id' => 3517, 'code' => 'SHOWROOM', 'name' => 'Шоу-рум г.Москва'],
		'sell-out'   => ['id' => 3521, 'code' => 'SALEGOODS', 'name' => 'Распродажа']
	];
	if (array_key_exists($arResult['VARIABLES']['SECTION_CODE'], $arSpecialSections)) {
		include 'special_section.php';
		return;
	}

	$arGroups = $USER->GetUserGroupArray();
	$check = false;
	foreach([1, 7, 11, 12] as $cgoupId)
	{
		if(in_array($cgoupId, $arGroups))
		{
			$check = true;
			break;
		}
	}

	global $arSetting;

	// current section
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y"
	);
	if (intval($arResult["VARIABLES"]["SECTION_ID"]) > 0){
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	}elseif ("" != $arResult["VARIABLES"]["SECTION_CODE"]){
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}
	$arSelect = [
		"ID", "CODE", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PICTURE",
		"DESCRIPTION", "DEPTH_LEVEL", "UF_BANNER", "UF_BANNER_URL", "UF_BACKGROUND_IMAGE",
		"UF_PREVIEW", "UF_VIEW", "UF_VIEW_COLLECTION", "UF_SECTION_TITLE_H1", "UF_DISPLAY_PARAMS"
	];
	$cacheId = md5(serialize($arFilter));
	$cacheDir = "/catalog/section1";
	$obCache = new CPHPCache();
	if ($obCache->InitCache($arParams["CACHE_TIME"], $cacheId, $cacheDir)){
		$arCurSection = $obCache->GetVars();
	}elseif ($obCache->StartDataCache()){
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cacheDir);
		$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

		$arFilter["ELEMENT_SUBSECTIONS"] = "N";
		$ob = CIBlockSection::GetList(array(), $arFilter, true, $arSelect);
		if ($arSection = $ob->GetNext()){
			$arCurSection = [];

			$arCurSection["ID"]   = $arSection["ID"];
			$arCurSection["NAME"] = $arSection["NAME"];
			$arCurSection["CODE"] = $arSection["CODE"];
			$arCurSection["ELEMENT_COUNT"] = $arSection["ELEMENT_CNT"];

			$arCurSection["DEPTH_LEVEL"]  = $arSection["DEPTH_LEVEL"];
			$arCurSection["CUSTOM_DISPLAY_PARAMS"] = $arCurSection["DISPLAY_PARAMS"] = [];
			if(!empty($arSection["UF_DISPLAY_PARAMS"]) and SITE_ID != 'l1')
			{
				$curUserField = CUserTypeEntity::GetList([], ["ENTITY_ID" => "IBLOCK_".$arParams["IBLOCK_ID"]."_SECTION", "FIELD_NAME" => "UF_DISPLAY_PARAMS"])->Fetch();
				if($curUserField)
				{
					$obEnum = new \CUserFieldEnum;
	        		$rsEnum = $obEnum->GetList([], ["USER_FIELD_ID" => $curUserField['ID']]);
			        while($arEnum = $rsEnum->Fetch())
	        		{
	        			if(in_array($arEnum['ID'], $arSection["UF_DISPLAY_PARAMS"]))
	        			{
		        			$arCurSection["DISPLAY_PARAMS"][] = $arEnum['XML_ID'];
		        		}
					}
				}
				$arCurSection["CUSTOM_DISPLAY_PARAMS"] = $arCurSection["DISPLAY_PARAMS"];
			}
			if(empty($arCurSection["DISPLAY_PARAMS"]))
			{
				$arCurSection["DISPLAY_PARAMS"] = $arParams['LIST_PROPERTY_CODE'];
			}

			if($arSection["PICTURE"] > 0)
				$arCurSection["PICTURE"] = CFile::GetFileArray($arSection["PICTURE"]);
			$arCurSection["DESCRIPTION"] = $arSection["DESCRIPTION"];
			$arCurSection["BANNER"] = array(
				"PICTURE" => $arSection["UF_BANNER"] > 0 ? CFile::GetFileArray($arSection["UF_BANNER"]) : "",
				"URL" => $arSection["UF_BANNER_URL"]
			);
			$arCurSection["PREVIEW"] = $arSection["UF_PREVIEW"];
			if ($arSection["UF_VIEW_COLLECTION"] > 0)
				$arCurSection["VIEW_COLLECTION"] = true;

			if ($arSection["UF_VIEW"] > 0) {
				$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_VIEW"]));
				if ($UserFieldAr = $UserField->Fetch())
					$arCurSection["VIEW"] = $UserFieldAr["XML_ID"];
			};
			if (($arSection["UF_BACKGROUND_IMAGE"] <= 0 || $arSection["UF_VIEW"] <= 0) && $arSection["DEPTH_LEVEL"] > 1) {
				if($arSection["DEPTH_LEVEL"] > 2) {
					$rsParentSectionPath = CIBlockSection::GetNavChain($arSection["IBLOCK_ID"], $arSection["IBLOCK_SECTION_ID"]);
					while($arParentSectionPath = $rsParentSectionPath->GetNext()) {
						$parentSectionPathIds[] = $arParentSectionPath["ID"];
					}
				} else {
					$parentSectionPathIds = $arSection["IBLOCK_SECTION_ID"];
				}
				if(!empty($parentSectionPathIds)) {
					$rsSections = CIBlockSection::GetList(
						array("DEPTH_LEVEL" => "DESC"),
						array("IBLOCK_ID" => $arSection["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "ID" => $parentSectionPathIds),
						false,
						array("ID", "IBLOCK_ID", "DEPTH_LEVEL", "UF_BACKGROUND_IMAGE", "UF_VIEW")
					);
					while($arSection = $rsSections->GetNext()) {
						if(!isset($arCurSection["BACKGROUND_IMAGE"]) && $arSection["UF_BACKGROUND_IMAGE"] > 0) {
							$arCurSection["BACKGROUND_IMAGE"] = CFile::GetFileArray($arSection["UF_BACKGROUND_IMAGE"]);
						}
						if(!isset($arCurSection["VIEW"]) && $arSection["UF_VIEW"] > 0) {
							$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_VIEW"]));
							if($UserFieldAr = $UserField->Fetch()) {
								$arCurSection["VIEW"] = $UserFieldAr["XML_ID"];
							}
						}
					}
				}
			}
			$arCurSection["SECTION_TITLE_H1"] = $arSection["UF_SECTION_TITLE_H1"];
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection["IBLOCK_ID"], $arSection["ID"]);
			$arCurSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
		}else{
			$obCache->AbortDataCache();
		}

        // MIN and MAX PRICE
        $dbElements = CIBlockElement::GetList(
            array('catalog_PRICE_1' => 'ASC'),
            array(
                "ACTIVE" => 'Y',
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
//                "INCLUDE_SUBSECTIONS" => "Y",
//                "!PROPERTY_DISCONTINUED" => "Y",
            ),
            false,
            false
            //array("IBLOCK_ID", "ID", "NAME", "PRICE")
        );
        $arCurSection["SEO_ELEMENTS_COUNT"] = $dbElements->SelectedRowsCount();
        if ($arrElement = $dbElements->fetch()) {
            $arCurSection["SEO_MIN_PRICE"] = intval($arrElement['CATALOG_PRICE_1']);
        }

        $dbElements = CIBlockElement::GetList(
            array('catalog_PRICE_1' => 'DESC'),
            array(
                "ACTIVE" => 'Y',
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            ),
            false,
            false//array('nTopCount' => 1)
        //array("IBLOCK_ID", "ID", "NAME", "PRICE")
        );
        if ($arrElement = $dbElements->fetch()) {
            $arCurSection["SEO_MAX_PRICE"] = intval($arrElement['CATALOG_PRICE_1']);
        }

        $elSection = CIBlockElement::GetList(Array(), [
            "ACTIVE" => 'Y',
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            'SECTION_CODE'=>$arResult["VARIABLES"]["SECTION_CODE"]
        ]);

        $productsId = [];
        while($ob = $elSection->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $productsId[] = $arFields['ID'];
        }
        $arCurSection['availableProducts'] = CCatalogProduct::GetList([],['@ID'=>$productsId,'>=QUANTITY'=>1],[]);

        $arCurSection['underOrder'] = CIBlockElement::GetList(
        	[],
        	[
        		"ACTIVE" => 'Y',
        		'SECTION_CODE'=>$arResult["VARIABLES"]["SECTION_CODE"],
        		'IBLOCK_ID'=>$arParams["IBLOCK_ID"],
                'PROPERTY_SYS_UNDER_THE_ORDER'=>'Y'
        	],
    		[]
    	);

        $arCurSection['elements_count'] = CIBlockElement::GetList(
        	[],
        	[
        		"ACTIVE" => 'Y',
        		'SECTION_CODE'=>$arResult["VARIABLES"]["SECTION_CODE"],
        		'IBLOCK_ID'=>$arParams["IBLOCK_ID"],
        		'INCLUDE_SUBSECTIONS' => 'Y'
        	],
    		[]
    	);


		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($arCurSection);
	}

	if (CModule::IncludeModule('grishchenko.mask')) {
		hashTags::getInstance()->set('#elements_count#',$arCurSection["elements_count"]);
	    hashTags::getInstance()->set('#min_price#',$arCurSection["SEO_MIN_PRICE"]);
	    hashTags::getInstance()->set('#max_price#',$arCurSection["SEO_MAX_PRICE"]);
	    hashTags::getInstance()->set('#aviable_products#',$arCurSection['availableProducts']);
	    hashTags::getInstance()->set('#under_order#',$arCurSection['underOrder']);
	}

	//pr($arCurSection);
	$arResult["VARIABLES"]["SECTION_ID"] = $arCurSection['ID'];

	if (isset($_GET['new']) && $_GET['new'] == 'Y') {
		include 'section_newproducts.php';
		return;
	}

	// -- Set filter params only for series goods --------------------------- //
	$arIDs = array();
	if ($arCurSection["VIEW_COLLECTION"])
	{
		global $arFilterCollection;
		$arFilterCollection["!PROPERTY_THIS_COLLECTION"] = false;
		$arFilterCollection["CATALOG_AVAILABLE"] = 'Y';
		$arParams["FILTER_NAME"] = "arFilterCollection";
	}

	$pageUrl= $arResult["VARIABLES"]["SMART_FILTER_PATH"];
	$pageSeo = $arSeo = $arBtn = [];
	$arSeo = getSEOFilterValues($arParams["USE_FILTER_SEO_IBLOCK"], $arParams["IBLOCK_ID"], $arParams["CACHE_TIME"]);
	if($arParams["USE_FILTER_SEO"] == "Y" and !empty($arParams["USE_FILTER_SEO_IBLOCK"]) and $pageUrl)
	{
		if (!empty($arSeo['PAGE_SEO'][$pageUrl]))
		{
			$pageSeo = $arSeo['PAGE_SEO'][$pageUrl];
		}
	}

	//Для всех тегов текущего раздела наход ссылку на фильтр
	?>
	<script type="text/javascript">
		var arLinksFilterTag = [];
	</script>
	<?
	$a = 0;
	foreach($arSeo['TAGS'][$arCurSection["ID"]] as $code => $arItemTags){
		?>
		<script type="text/javascript">
			arLinksFilterTag['<?=$a?>'] = {
					'code': '<?=$code?>',
					'filter': '<?=$arSeo['PAGE_SEO'][$code]['FILTER_URL_PAGE']?>/',
				};
		</script>
		<?
		$a++;
	}
	
	//Хлебные крошки тегов
	$arBreadcrumbsTags = array();
	getBreadcrumbTags($arBreadcrumbsTags, $arSeo['TAGS'][$arCurSection["ID"]], $pageUrl, $pageUrl);
	$arBreadcrumbsTagsResult = $arBreadcrumbsTags[$pageUrl];
	krsort($arBreadcrumbsTagsResult);

	// -- Get SEO island fast links ----------------------------------------- //
	if(isset($arCurSection) and !empty($arCurSection))
	{
		if(!empty($arSeo['TAGS'][$arCurSection["ID"]]))
		{
			$arTags = $arSeo['TAGS'][$arCurSection["ID"]];
		}

		if(!empty($arTags))
		{
			$beChild = false;

			//Для раздела или обычной страницы фильтра или тега у которого нету дочерних выводим главные теги и тег не должен быть дочерним
			if((!$pageUrl or count($arTags[$pageUrl]) == 0 or count($arTags[$pageUrl]['CHILD']) == 0) and !$arTags[$pageUrl]['THIS_PARENT'])
			{
				foreach($arTags as &$arItem)
				{
					if (!$arItem['THIS_PARENT'] and $arItem["ID"] != '')
					{
						$arBtn[$arItem['GROUPS']][]=array(
							"SORT"  => $arItem["SORT"],
							"NAME"  => $arItem["NAME"],
							"LINK"  => "//".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/".$arItem["CODE"]."/",
							"SRC"   => $arItem["IMG"]["src"],
							"SELECTED" => $pageUrl == $arItem["CODE"] ? "Y" : ""
						);
					}
				}
			}
			//Для тега с дочернмими выводим дочерние
			if(count($arTags[$pageUrl]['CHILD']) > 0)
			{
				$beChild = true;
				foreach($arTags[$pageUrl]['CHILD'] as &$childCode)
				{
					$arBtn[$arTags[$childCode]['GROUPS']][] = array(
						"SORT"  => $arTags[$childCode]["SORT"],
						"NAME"  => $arTags[$childCode]["NAME"],
						"LINK"  => "//".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/".$arTags[$childCode]["CODE"]."/",
						"SRC"   => $arTags[$childCode]["IMG"]["src"],
						"SELECTED" => $pageUrl == $arTags[$childCode]["CODE"] ? "Y" : "",
					);
				}

				$arBack["BACK"] = "//".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/".(!empty($arTags[$pageUrl]['THIS_PARENT']) ? $arTags[$pageUrl]['THIS_PARENT']."/" : '');
			}

			//Для дочернего тега, без дочерних тегов выводим теги этого родителя
			if($arTags[$pageUrl]['THIS_PARENT'] and !$beChild)
			{
				foreach($arTags[$arTags[$pageUrl]['THIS_PARENT']]['CHILD'] as &$childCode)
				{
					$arBtn[$arTags[$childCode]['GROUPS']][] = array(
						"SORT"  => $arTags[$childCode]["SORT"],
						"NAME"  => $arTags[$childCode]["NAME"],
						"LINK"  => "//".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/".$arTags[$childCode]["CODE"]."/",
						"SRC"   => $arTags[$childCode]["IMG"]["src"],
						"SELECTED"  => $pageUrl == $arTags[$childCode]["CODE"] ? "Y" : "",
					);
				}

				$arBack["BACK"] = "//".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/".$arTags[$pageUrl]['THIS_PARENT']."/";
			}
		}

	//FILTER//
	if($arParams["USE_FILTER"] == "Y" && $arSetting["SMART_FILTER_VISIBILITY"]["VALUE"] != "DISABLE" && !$arCurSection["VIEW_COLLECTION"]):?>
		<?
		$APPLICATION->AddViewContent("isSection", "<div class='isSection'>Y</div>");
		?>
		<div class="filter_indent<?=($arSetting['SMART_FILTER_LOCATION']['VALUE'] == 'VERTICAL') ? ' vertical' : '';?> clr"></div>

		<?global $arSmartFilter;
	else:
		$arSmartFilter = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SECTION_ID" => $arCurSection["ID"]
		);
		if($arCurSection["VIEW_COLLECTION"]) {
			$arSmartFilter["!PROPERTY_THIS_COLLECTION"] = false;
		}
	endif;
	}

	$sort   = $_REQUEST["sort"] ? $_REQUEST["sort"] : $arParams['ELEMENT_SORT_FIELD'];
	$order  = in_array(strtolower($_REQUEST["order"]), ['asc', 'desc']) ? ($_REQUEST["order"] == "desc" ? "desc" : "asc") : $arParams['ELEMENT_SORT_ORDER'];?>
	<div class="goods">
		<div class="container goods__container">
			<div class="goods__breadcrumbs">
				<?$APPLICATION->IncludeComponent(
					"custom:catalog.breadcrumbs",
					"",
					array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => $arCurSection['ID'],
						"TAGS" => $arBreadcrumbsTagsResult,
						//"ELEMENT_ID" => $currentElement["ID"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					),
					$component
				);?>
				<?/*
				<div class="breadcrumbs__need-help">
					<a href="#">Нужна помощь в выборе душевой кабины?</a>
				</div>
				*/?>
			</div>

			<div class="goods__title">
				<h1 class="goods__title-title"><?=$APPLICATION->ShowTitle(FALSE)?></h1>
				<?
				ob_start();
				
				global ${$arParams["FILTER_NAME"]};
				${$arParams["FILTER_NAME"]}['ID'] = $ids;
				
				/*
				echo "<pre>";
				print_r(${$arParams["FILTER_NAME"]});
				echo "</pre>";
				*/
				
				$APPLICATION->IncludeComponent(
					"bitrix:catalog.smart.filter",
					"catalog",
					array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => $arCurSection["ID"],
						"FILTER_NAME" => $arParams["FILTER_NAME"],
						"PRICE_CODE" => CClass::getCurrentPriceCode(),
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"SAVE_IN_SESSION" => "N",
						"FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
						"XML_EXPORT" => "N",
						"SECTION_TITLE" => "NAME",
						"SECTION_DESCRIPTION" => "DESCRIPTION",
						'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
						"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
						'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
						'CURRENCY_ID' => $arParams['CURRENCY_ID'],
						//"SEF_MODE" => $arParams["SEF_MODE"],
						"SEF_MODE" => $arParams["SEF_MODE"],
						//"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
						"SEF_RULE" => '/catalog2/filter/#SMART_FILTER_PATH#/apply/',
						"SMART_FILTER_PATH" => ($pageSeo["FILTER_URL_PAGE"] ? $pageSeo["FILTER_URL_PAGE"] : $arResult["VARIABLES"]["SMART_FILTER_PATH"]),
						"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
						"INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
						"SHOW_ONE_VALUE" => 'Y',//($check ? 'Y' : 'N'),
					),
					$component,
					array('HIDE_ICONS' => 'Y')
				);
				$catalog_smart_filter = ob_get_clean();


                if(empty($_SERVER["HTTP_IS_SUB_HEADER"]) ){
                    $text = ['', ''];
                    if ($arCurSection["DESCRIPTION"] or $pageSeo['SEO_TEXT'])
                    {
                        $desc = $pageSeo['SEO_TEXT'] ? $pageSeo['SEO_TEXT'] : $arCurSection["DESCRIPTION"];
                        if(true and strpos($desc, '</p>') !== false)
                        {
                            $text = explode('</p>', $desc, 2);
                            $text[0] .= '</p>';
                        }
                        else
                        {
                            $desc = preg_replace('#(<br[^>]*>)#uis', '<br>', $desc);
                            $text = explode('<br>', $desc, 2);
                        }
                    }
                    ?>
                    <?if(count($GLOBALS[$arParams['FILTER_NAME']]) == 0 && $text[0]):?>
                        <div class="goods__title-description" style="width:100%"><?=$text[0]?>
                            <?if($text[1]):?>
                                <div class="goods__title-description-full" style="width:100%"><?=$text[1]?></div>
                                <a href="#" class="goods__title-link">Подробнее</a>
                            <?endif;?>
                        </div>
                    <?endif;?>
                    <?
                }
				?>

			</div>
	  		<div class="goods__wrapper">
				<div class="goods__card-sort--container hide mobile-show">
					<?$APPLICATION->IncludeFile(
						SITE_DEFAULT_PATH."/include/catalog_filter.php",
						[
							"SORT"  => $sort,
							"ORDER" => $order,
							"MOBILE" => true
						],
						array(
							"MODE"	  => "file",
							"SHOW_BORDER"   => FALSE
						)
					);?>
				</div>
				<div class="goods__filter">
		  			<div class="goods__filter-container">
						<div id="modef_popup">
							<div class="info"><a href="#" class="show-items">Показать</a> <div class="tag is-warning"></div></div>
							<span class="close-hint">X</span>
						</div>
						<div class="goods__filter-title--mobile">
			  				<?/*<p class="goods__filter-title">Фильтры</p>*/?>
			  				<div class="goods__filter-close-button"></div>
						</div>
						<div class="goods__filter-content goods__filter-content--mobile">
			  				<p class="goods__filter-title">Фильтры</p>
			  				<div class="goods__filter-close-button"></div>
			  				<div class="filter">
								<div class="<?$APPLICATION->ShowProperty('hidden_subsection')?>">
									<?$APPLICATION->IncludeComponent(
										"bitrix:catalog.section.list",
										"catalog_left",
										array(
											"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											"IBLOCK_ID" => $arParams["IBLOCK_ID"],
											"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
											"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
											"CACHE_TYPE" => $arParams["CACHE_TYPE"],
											"CACHE_TIME" => $arParams["CACHE_TIME"],
											"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
											"COUNT_ELEMENTS" => "",
											"TOP_DEPTH" => $arCurSection["DEPTH_LEVEL"] + 1,
											"SECTION_FIELDS" => [],
											"ADD_SECTIONS_CHAIN" => "Y"
										),
										$component
									);?>
								</div>
								<div class="filter__item filter__item--mobile sorting">
									<select name="" id="">
										<option value="">По популярности<span><img src="<?=SITE_DEFAULT_PATH?>/images/icons/filter__select-option.pdf" alt=""></span></option>
										<option value="">По цене <span><img src="<?=SITE_DEFAULT_PATH?>/images/icons/filter__select-option.pdf" alt=""></span></option>
									</select>
								</div>
								<?
									echo $catalog_smart_filter;
								?>
							</div>

							<?$APPLICATION->IncludeComponent(
								"bitrix:main.include",
								"",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DEFAULT_PATH."/include/category_reviews.php",
									"SECTION" => $arCurSection,
									"IS_MOBILE" => 'N',
									'CACHE_TIME' => $arParams['CACHE_TIME'],
								),
								false
							);?>
						</div>
		  			</div>
	   			</div>

				<div class="goods__card">
					<?//FAST_LINKS*/?>
					<?
					if(true or $check):
						//pr($arBtn);
						$arSeoItems = $arSeoSortNames = $arSeoSortNum = $arSeoSortIndex = [];
						foreach($arBtn as $arGroup)
						{
							foreach($arGroup as $arItem)
							{
								$arSeoItems[] = $arItem;
								$arSeoSortNames[] = $arItem['NAME'];
								$arSeoSortIndex[] = $arItem['SORT'];
								$num = 0;
								$i = current(explode('-', $arItem['NAME'], 2));
								//$i = intval($i);
								if($i > 0)
								{
									$num = $i;
								}
								$arSeoSortNum[] = $num;
							}
						}
//						pr($arSeoSortNames);
//						pr($arSeoSortIndex);
//						pr($arSeoSortNum);
						array_multisort($arSeoSortIndex, SORT_ASC, SORT_NUMERIC, $arSeoSortNum, SORT_ASC, SORT_NUMERIC, $arSeoSortNames, SORT_ASC, SORT_STRING, $arSeoItems);
					?>

					<?endif?>
		  			<div class="goods__card-sort--container hide-mobile">
						<?$APPLICATION->IncludeFile(
							SITE_DEFAULT_PATH."/include/catalog_filter.php",
							[
								"SORT"  => $sort,
								"ORDER" => $order,
								"MOBILE" => false
							],
							array(
								"MODE"	  => "file",
								"SHOW_BORDER"   => FALSE
							)
						);?>
					</div>
					<?
					$isAjax = $_REQUEST["IS_AJAX"] == "Y";
					if ($isAjax)
					{
						$APPLICATION->RestartBuffer();
					}

					$sortOptions = array(
						"price" => "CATALOG_PRICE_1",
						"name" => "NAME",
						"rating" => "PROPERTY_rating",
					);
					$sort = $sortOptions[$_REQUEST["sort"]] ? $sortOptions[$_REQUEST["sort"]] : $sort;
					$intSectionid = $arCurSection["ID"];
					?>
					<?
					if(!empty($_REQUEST['json_get_filter']) and $_REQUEST['json_get_filter'] == 'y')
					{
						$filter = ['iblock' => $arParams["IBLOCK_ID"], 'section' => $arCurSection["ID"], 'filter' => $GLOBALS[$arParams["FILTER_NAME"]]];
						$GLOBALS['APPLICATION']->RestartBuffer();
						echo json_encode($filter);
						die();
					}
					?>
					<script data-skip-moving="true">
					var cookie = document.cookie.match('(^|;) ?short-desc=([^;]*)(;|$)');
					if(cookie)
					{
						cookie = (unescape(cookie[2]));
						if(cookie == 'yes')
						{
							document.querySelector('body').classList.add('short-desc');
							var elements = document.getElementsByClassName('short-desc-box');
							for (var i = 0; i < elements.length; i++)
							{
								elements[i].checked = true;
							}
							var elements = document.getElementsByClassName('short-desc-on');
							for (var i = 0; i < elements.length; i++)
							{
								elements[i].classList.remove("not_active");
							}
							var elements = document.getElementsByClassName('short-desc-off');
							for (var i = 0; i < elements.length; i++)
							{
								elements[i].classList.add("not_active");
							}
						}
					}
					</script>
					<? 
					global ${$arParams["FILTER_NAME"]};
					${$arParams["FILTER_NAME"]}['ID'] = $ids;
					
					//pr(${$arParams["FILTER_NAME"]});

					$APPLICATION->IncludeComponent(
						"bitrix:catalog.section",
						"catalog2",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"ELEMENT_SORT_FIELD" => $sort,
							"ELEMENT_SORT_ORDER" => $order,
							"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
							"PROPERTY_CODE" => ['OLD_ID'],//$arCurSection["DISPLAY_PARAMS"],
							"PROPERTY_CODE_MOBILE" => ['OLD_ID'],//$arCurSection["DISPLAY_PARAMS"],
							"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
							"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
							"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
							"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
							"INCLUDE_SUBSECTIONS" => "Y",
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"FILTER_NAME" => $arParams["FILTER_NAME"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"SET_TITLE" => $arParams["SET_TITLE"],
							"MESSAGE_404" => $arParams["~MESSAGE_404"],
							"SET_STATUS_404" => $arParams["SET_STATUS_404"],
							"SHOW_404" => $arParams["SHOW_404"],
							"FILE_404" => $arParams["FILE_404"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
							"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
							"PRICE_CODE" => CClass::getCurrentPriceCode(),
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

							"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
							"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
							"PAGER_TITLE" => $arParams["PAGER_TITLE"],
							"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
							"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
							"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
							"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
							"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
							"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
							"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
							"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
							"LAZY_LOAD" => $arParams["LAZY_LOAD"],
							"MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
							"LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

							"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
							'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
							'CURRENCY_ID' => $arParams['CURRENCY_ID'],
							'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
							'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

							'LABEL_PROP' => $arParams['LABEL_PROP'],
							'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
							'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
							'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
							'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
							'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
							'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
							'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
							'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
							'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
							'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
							'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

							/*'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
							'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],*/
							'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
							'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
							'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
							'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
							'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
							'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
							'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
							'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
							'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
							'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
							'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
							'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
							'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
							'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
							'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

							'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
							'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
							'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

							'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							"ADD_SECTIONS_CHAIN" => "N",
							'ADD_TO_BASKET_ACTION' => $basketAction,
							'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
							'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
							'COMPARE_NAME' => $arParams['COMPARE_NAME'],
							'USE_COMPARE_LIST' => 'Y',
							'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
							'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
							'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),

							"IS_AJAX" => ($isAjax or $_REQUEST["IS_AJAX2"] == 'Y') ? "Y" : "N",

							"CUSTOM_DISPLAY_PARAMS" => $arCurSection["CUSTOM_DISPLAY_PARAMS"],
						),
						$component
					);
					if ($isAjax)
					{
						die();
					}?>

				</div>
	  		</div>
		</div>
  	</div>
	
