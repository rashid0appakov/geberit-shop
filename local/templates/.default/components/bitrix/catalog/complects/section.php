<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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

	//BANNER//
	/*if (is_array($arCurSection["BANNER"]["PICTURE"])):?>
		<div class="catalog-item-banner">
			<a href="<?=!empty($arCurSection["BANNER"]["URL"]) ? $arCurSection["BANNER"]["URL"] : 'javascript:void(0)'?>">
				<img src="<?=$arCurSection['BANNER']['PICTURE']['SRC']?>" width="<?=$arCurSection['BANNER']['PICTURE']['WIDTH']?>" height="<?=$arCurSection['BANNER']['PICTURE']['HEIGHT']?>" alt="<?=$arCurSection['NAME']?>" title="<?=$arCurSection['NAME']?>" />
			</a>
		</div>
	<?endif;?>
	<?//DESCRIPTION//
	if((!empty($arCurSection["DESCRIPTION"]) || !empty($pageSeo["SEO_TEXT"])) && (!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1)) {?>
		<div class="catalog_preview vendor_description" style="height:100px;">
			<i class="fa fa-arrow-down" aria-hidden="true"></i>
			<i class="fa fa-arrow-up" aria-hidden="true" style="display:none;"></i>
			<?=(!empty($arCurSection["DESCRIPTION"]) && empty($pageSeo["SEO_TEXT"]) ? $arCurSection["DESCRIPTION"] : $pageSeo["SEO_TEXT"])?></div>
	<?}?>
	<?//PREVIEW//
	if(!empty($arCurSection["PREVIEW"])):
		if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1):?>
			<div class="catalog_preview">
				<?=$arCurSection["PREVIEW"];?>
			</div>
		<?endif;
	endif;*/

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
						"SEF_MODE" => $arParams["SEF_MODE"],
						"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
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
						<?$APPLICATION->IncludeFile(
							$templateFolder.'/include/seo_tags.php',
							[
								'BACK'	=> $arBack,
								'ITEMS'	=> $arSeoItems,
								'ALL'	=> $arSeo
							],
							array(
								"MODE"	  => "file",
								"TEMPLATE"  => "include"
							)
						);?>
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
					${$arParams["FILTER_NAME"]}['SECTION_ACTIVE'] = "Y";
					
					/*
					if($USER->GetID() == 733)
					{
						pr(["SORT"  => $sort, "ORDER" => $order,
							"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
						]);
						pr(${$arParams["FILTER_NAME"]});
					}
					*/

					$APPLICATION->IncludeComponent(
						"bitrix:catalog.section",
						"catalog",
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
							"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
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
							"PAGE_ELEMENT_COUNT" => ($_SESSION['CATALOG_PP'] ? : $arParams["PAGE_ELEMENT_COUNT"]),
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

							/*"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],*/

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
					<div class="goods__card-cell card-cell preview-products">
						<?
						global $arFilterNews;
						
						$arFilterNews = $GLOBALS['CUSTOM_FILTER']['NEWPRODUCT'];
						$arFilterNews['SECTION_ID'] = $arCurSection["ID"];
						
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"carousel_catalog",
							array(
								"TYPE" => "NEWS",
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "ASC",
								"ELEMENT_SORT_FIELD2" => "",
								"ELEMENT_SORT_ORDER2" => "",
								"PROPERTY_CODE" => array(
									0 => "OLD_ID",
								),
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"SHOW_ALL_WO_SECTION" => "Y",
								"BASKET_URL" => "/personal/cart/",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"FILTER_NAME" => "arFilterNews",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "N",
								"SET_TITLE" => "N",
								"MESSAGE_404" => "",
								"SET_STATUS_404" => "N",
								"SHOW_404" => "N",
								"FILE_404" => "",
								"DISPLAY_COMPARE" => "Y",
								"PAGE_ELEMENT_COUNT" => "6",
								"LINE_ELEMENT_COUNT" => "",
								"PRICE_CODE" => CClass::getCurrentPriceCode(),
								"USE_PRICE_COUNT" => "Y",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"USE_PRODUCT_QUANTITY" => "Y",
								"ADD_PROPERTIES_TO_BASKET" => "N",
								"PARTIAL_PRODUCT_PROPERTIES" => "N",
								"PRODUCT_PROPERTIES" => array(
								),
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "<span>Новинки</span> в разделе ".$arCurSection["NAME"],
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_BASE_LINK" => "",
								"PAGER_PARAMS_NAME" => "",
								"SECTION_ID" => $arCurSection['ID'],
								"SECTION_CODE" => "",
								"SECTION_URL" => "",
								"DETAIL_URL" => "",
								"USE_MAIN_ELEMENT_SECTION" => "Y",
								"CONVERT_CURRENCY" => "N",
								"CURRENCY_ID" => "",
								"HIDE_NOT_AVAILABLE" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"COMPARE_PATH" => "",
								"BACKGROUND_IMAGE" => "",
								"DISABLE_INIT_JS_IN_COMPONENT" => "N",
								"PROPERTY_CODE_MOD" => array(
									0 => "GUARANTEE",
									1 => "",
								),
								"COMPONENT_TEMPLATE" => "filtered",
								"SECTION_USER_FIELDS" => array(
									0 => "",
									1 => "",
								),
								"CUSTOM_FILTER" => "",
								"HIDE_NOT_AVAILABLE_OFFERS" => "N",
								"SEF_MODE" => "N",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"BROWSER_TITLE" => "-",
								"META_KEYWORDS" => "-",
								"META_DESCRIPTION" => "-",
								"COMPATIBLE_MODE" => "Y",

								"SHOW_COUNT" => 3,
								"FILTER_KEY" => 'NEWPRODUCT',
							),
							false
						);
						?>
						
						<?
						global $arFilterHits;
						
						$arFilterHits = $GLOBALS['CUSTOM_FILTER']['SALELEADER'];
						$arFilterHits['SECTION_ID'] = $arCurSection["ID"];
						
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"carousel_catalog",
							array(
								"TYPE" => "HITS",
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "ASC",
								"ELEMENT_SORT_FIELD2" => "",
								"ELEMENT_SORT_ORDER2" => "",
								"PROPERTY_CODE" => array(
									0 => "OLD_ID",
								),
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"SHOW_ALL_WO_SECTION" => "Y",
								"BASKET_URL" => "/personal/cart/",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"FILTER_NAME" => "arFilterHits",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "N",
								"SET_TITLE" => "N",
								"MESSAGE_404" => "",
								"SET_STATUS_404" => "N",
								"SHOW_404" => "N",
								"FILE_404" => "",
								"DISPLAY_COMPARE" => "Y",
								"PAGE_ELEMENT_COUNT" => "6",
								"LINE_ELEMENT_COUNT" => "",
								"PRICE_CODE" => CClass::getCurrentPriceCode(),
								"USE_PRICE_COUNT" => "Y",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"USE_PRODUCT_QUANTITY" => "Y",
								"ADD_PROPERTIES_TO_BASKET" => "N",
								"PARTIAL_PRODUCT_PROPERTIES" => "N",
								"PRODUCT_PROPERTIES" => array(
								),
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "<span>Хиты</span> в разделе ".$arCurSection["NAME"],
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_BASE_LINK" => "",
								"PAGER_PARAMS_NAME" => "",
								"SECTION_ID" => "",
								"SECTION_CODE" => "",
								"SECTION_URL" => "",
								"DETAIL_URL" => "",
								"USE_MAIN_ELEMENT_SECTION" => "Y",
								"CONVERT_CURRENCY" => "N",
								"CURRENCY_ID" => "",
								"HIDE_NOT_AVAILABLE" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"COMPARE_PATH" => "",
								"BACKGROUND_IMAGE" => "",
								"DISABLE_INIT_JS_IN_COMPONENT" => "N",
								"PROPERTY_CODE_MOD" => array(
									0 => "GUARANTEE",
									1 => "",
								),
								"COMPONENT_TEMPLATE" => "filtered",
								"SECTION_USER_FIELDS" => array(
									0 => "",
									1 => "",
								),
								"CUSTOM_FILTER" => "",
								"HIDE_NOT_AVAILABLE_OFFERS" => "N",
								"SEF_MODE" => "N",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"BROWSER_TITLE" => "-",
								"META_KEYWORDS" => "-",
								"META_DESCRIPTION" => "-",
								"COMPATIBLE_MODE" => "Y",

								"SHOW_COUNT" => 3,
								"FILTER_KEY" => 'SALELEADER',
							),
							false
						);?>

						<?
						global $arFilterSales;
						
						$arFilterSales = $GLOBALS['CUSTOM_FILTER']['DISCOUNT'];
						$arFilterSales['SECTION_ID'] = $arCurSection["ID"];
						
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"carousel_catalog",
							array(
								"TYPE" => "SALES",
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "ASC",
								"ELEMENT_SORT_FIELD2" => "",
								"ELEMENT_SORT_ORDER2" => "",
								"PROPERTY_CODE" => array(
									0 => "OLD_ID",
								),
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"SHOW_ALL_WO_SECTION" => "Y",
								"BASKET_URL" => "/personal/cart/",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"FILTER_NAME" => "arFilterSales",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "N",
								"SET_TITLE" => "N",
								"MESSAGE_404" => "",
								"SET_STATUS_404" => "N",
								"SHOW_404" => "N",
								"FILE_404" => "",
								"DISPLAY_COMPARE" => "Y",
								"PAGE_ELEMENT_COUNT" => "6",
								"LINE_ELEMENT_COUNT" => "",
								"PRICE_CODE" => CClass::getCurrentPriceCode(),
								"USE_PRICE_COUNT" => "Y",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"USE_PRODUCT_QUANTITY" => "Y",
								"ADD_PROPERTIES_TO_BASKET" => "N",
								"PARTIAL_PRODUCT_PROPERTIES" => "N",
								"PRODUCT_PROPERTIES" => array(
								),
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "<span>Скидки</span> в разделе ".$arCurSection["NAME"],
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_BASE_LINK" => "",
								"PAGER_PARAMS_NAME" => "",
								"SECTION_ID" => "",
								"SECTION_CODE" => "",
								"SECTION_URL" => "",
								"DETAIL_URL" => "",
								"USE_MAIN_ELEMENT_SECTION" => "Y",
								"CONVERT_CURRENCY" => "N",
								"CURRENCY_ID" => "",
								"HIDE_NOT_AVAILABLE" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"COMPARE_PATH" => "",
								"BACKGROUND_IMAGE" => "",
								"DISABLE_INIT_JS_IN_COMPONENT" => "N",
								"PROPERTY_CODE_MOD" => array(
									0 => "GUARANTEE",
									1 => "",
								),
								"COMPONENT_TEMPLATE" => "filtered",
								"SECTION_USER_FIELDS" => array(
									0 => "",
									1 => "",
								),
								"CUSTOM_FILTER" => "",
								"HIDE_NOT_AVAILABLE_OFFERS" => "N",
								"SEF_MODE" => "N",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"BROWSER_TITLE" => "-",
								"META_KEYWORDS" => "-",
								"META_DESCRIPTION" => "-",
								"COMPATIBLE_MODE" => "Y",

								"SHOW_COUNT" => 3,
								"FILTER_KEY" => 'DISCOUNT',
							),
							false
						);?>
						
						<?
						global $arrFilterSalegoods;
						
						$arrFilterSalegoods = $GLOBALS['CUSTOM_FILTER']['SALEGOODS'];
						$arrFilterSalegoods['SECTION_ID'] = $arCurSection["ID"];
						
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"carousel_catalog",
							array(
								"TYPE" => "SALEGOODS",
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"ELEMENT_SORT_FIELD" => "RAND",
								"ELEMENT_SORT_ORDER" => "ASC",
								"ELEMENT_SORT_FIELD2" => "",
								"ELEMENT_SORT_ORDER2" => "",
								"PROPERTY_CODE" => array(
									0 => "OLD_ID",
								),
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"SHOW_ALL_WO_SECTION" => "Y",
								"BASKET_URL" => "/personal/cart/",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"FILTER_NAME" => "arrFilterSalegoods",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "N",
								"SET_TITLE" => "N",
								"MESSAGE_404" => "",
								"SET_STATUS_404" => "N",
								"SHOW_404" => "N",
								"FILE_404" => "",
								"DISPLAY_COMPARE" => "Y",
								"PAGE_ELEMENT_COUNT" => "6",
								"LINE_ELEMENT_COUNT" => "",
								"PRICE_CODE" => CClass::getCurrentPriceCode(),
								"USE_PRICE_COUNT" => "Y",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"USE_PRODUCT_QUANTITY" => "Y",
								"ADD_PROPERTIES_TO_BASKET" => "N",
								"PARTIAL_PRODUCT_PROPERTIES" => "N",
								"PRODUCT_PROPERTIES" => array(
								),
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "<span>Распродажа</span> в разделе ".$arCurSection["NAME"],
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_BASE_LINK" => "",
								"PAGER_PARAMS_NAME" => "",
								"SECTION_ID" => "",
								"SECTION_CODE" => "",
								"SECTION_URL" => "",
								"DETAIL_URL" => "",
								"USE_MAIN_ELEMENT_SECTION" => "Y",
								"CONVERT_CURRENCY" => "N",
								"CURRENCY_ID" => "",
								"HIDE_NOT_AVAILABLE" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"COMPARE_PATH" => "",
								"BACKGROUND_IMAGE" => "",
								"DISABLE_INIT_JS_IN_COMPONENT" => "N",
								"PROPERTY_CODE_MOD" => array(
									0 => "GUARANTEE",
									1 => "",
								),
								"COMPONENT_TEMPLATE" => "filtered",
								"SECTION_USER_FIELDS" => array(
									0 => "",
									1 => "",
								),
								"CUSTOM_FILTER" => "",
								"HIDE_NOT_AVAILABLE_OFFERS" => "N",
								"SEF_MODE" => "N",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"BROWSER_TITLE" => "-",
								"META_KEYWORDS" => "-",
								"META_DESCRIPTION" => "-",
								"COMPATIBLE_MODE" => "Y",

								"SHOW_COUNT" => 3,
								"FILTER_KEY" => 'SALEGOODS',
							),
							false
						);
						?>

						<div class="card-cell__review">
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.include",
								"",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DEFAULT_PATH."/include/category_reviews.php",
									"SECTION" => $arCurSection,
									"IS_MOBILE" => 'Y',
									'CACHE_TIME' => $arParams['CACHE_TIME'],
								),
								false,
								array(
									"ACTIVE_COMPONENT" => "N"
								)
							);?>
						</div>
					</div>
				</div>
	  		</div>
		</div>
  	</div>
<?php	
	//FILTER_SEO
	if(SITE_ID == 's0' && isset($GLOBALS[$arParams["FILTER_NAME"]]['=PROPERTY_4093']) && count($GLOBALS[$arParams["FILTER_NAME"]]['=PROPERTY_4093']) == 1){
		$arInfoBrands = CClass::getInfoBrands();
		$idSelectBrand = $GLOBALS[$arParams["FILTER_NAME"]]['=PROPERTY_4093'][0];

		$APPLICATION->SetPageProperty("title", "".$arCurSection['NAME']." ".$arInfoBrands[$idSelectBrand]['NAME'].": купить ".strtolower($arCurSection['NAME'])." ".$arInfoBrands[$idSelectBrand]['OTHER_NAME']." на официальном сайте в Москве с доставкой по России - ".$arInfoBrands[$idSelectBrand]['CODE'].".tiptop-shop.ru");
		
		$APPLICATION->SetPageProperty("description", "".$arCurSection['NAME']." ".$arInfoBrands[$idSelectBrand]['NAME']." по лучшей цене с доставкой и установкой, Офисы магазинов ".$arInfoBrands[$idSelectBrand]['NAME']." в Москве, Санкт Петербурге и Екатеринбурге, Доставим ".strtolower($arCurSection['NAME'])." ".$arInfoBrands[$idSelectBrand]['OTHER_NAME']." по всей России - Звоните бесплатно 8-800-777-08-92.");
	}
	elseif(isset($pageSeo) && !empty($pageSeo)) {
		$APPLICATION->SetTitle(!empty($pageSeo["HEADER"]) ? $pageSeo["HEADER"] : (!empty($pageSeo["NAME"]) ? $pageSeo["NAME"] : ""));
		$APPLICATION->SetPageProperty("title", !empty($pageSeo["TITLE"]) ? $pageSeo["TITLE"] : "");
		$APPLICATION->SetPageProperty("keywords", !empty($pageSeo["KEYWORDS"]) ? $pageSeo["KEYWORDS"] : "");
		$APPLICATION->SetPageProperty("description", !empty($pageSeo["DESCRIPTION"]) ? $pageSeo["DESCRIPTION"] : "");
	} 
	else {
		//META_PROPERTY//
		if (!empty($arCurSection["SECTION_TITLE_H1"]))
			$APPLICATION->SetTitle($arCurSection["SECTION_TITLE_H1"]);
		if (empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]) && !empty($arCurSection["PREVIEW"]))
			$APPLICATION->SetPageProperty("description", strip_tags($arCurSection["PREVIEW"]));
	}

	//PAGEN_META_PROPERTY//
	if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
		if($arParams["USE_FILTER_SEO"] == "Y" && !empty($arParams["USE_FILTER_SEO_IBLOCK"])  && !empty($pageSeo)) {
		   $APPLICATION->SetPageProperty("title", $pageSeo["NAME"]." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
		}else{
		   $APPLICATION->SetPageProperty("title", (!empty($arCurSection["SECTION_TITLE_H1"]) ? $arCurSection["SECTION_TITLE_H1"] : (!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"]))." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
		}
		$APPLICATION->SetPageProperty("keywords", "");
		$APPLICATION->SetPageProperty("description", "");
		
		$APPLICATION->SetPageProperty("canonical", "https://".SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/");
	}
	
	if(!empty($_SERVER['HTTP_IS_SUB_HEADER']) && SITE_ID == 's0'){
		$APPLICATION->SetPageProperty("canonical", 'https://'.SITE_SERVER_NAME."/catalog/".$arCurSection['CODE']."/manufacturer-is-".$_SERVER['HTTP_IS_SUB_HEADER']."/");
	}

	//Имя города при региональных поддоменах
	$APPLICATION->SetTitle(str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetTitle()));
	$APPLICATION->SetPageProperty('title', str_replace('{geo}', CClass::getNameSubdomain(), $APPLICATION->GetPageProperty('title')));
	
	//На странице фильтра вставляем имя выбранной серии
	$nameSeries = $min_price = $max_price = "";
	$arSeriesPropsId = [
        's2' => '=PROPERTY_5480',
        's8' => '=PROPERTY_5760',
    ];

	$filterSection = ${$arParams["FILTER_NAME"]};
	if(isset($arSeriesPropsId[SITE_ID])) {
        $seriesPropId = $arSeriesPropsId[SITE_ID];
	    if($filterSection[$seriesPropId] && count($filterSection[$seriesPropId])==1) {
            $idSelectSeries = $filterSection[$seriesPropId][0];
            $nameSeries = $GLOBALS['PAGE_DATA']['INFO_SERIES'][$idSelectSeries]['NAME'] ? $GLOBALS['PAGE_DATA']['INFO_SERIES'][$idSelectSeries]['NAME'] : "";
        }
	}


	$arSectionVars = [
        '{series_name}',
        '{min_price}',
        '{max_price}',
        '{count_products}',
    ];

    $arSectionValues = [
        $nameSeries,
        $arCurSection["SEO_MIN_PRICE"],
        $arCurSection["SEO_MAX_PRICE"],
        $arCurSection["ELEMENT_COUNT"]//$arCurSection["SEO_ELEMENTS_COUNT"],
    ];

	$APPLICATION->SetTitle(str_replace($arSectionVars, $arSectionValues, $APPLICATION->GetTitle()));
	$APPLICATION->SetPageProperty('title', str_replace($arSectionVars, $arSectionValues, $APPLICATION->GetPageProperty('title')));
	$APPLICATION->SetPageProperty('description', str_replace($arSectionVars, $arSectionValues, $APPLICATION->GetPageProperty('description')));
	
	//Скрываем ссылки на подразделы если выбран фильтр
	unset($GLOBALS[$arParams["FILTER_NAME"]]["SECTION_ACTIVE"]);
	if(count($GLOBALS[$arParams["FILTER_NAME"]]) > 0){
		$APPLICATION->SetPageProperty("hidden_subsection", "hidden");
	}
	else{
		$APPLICATION->SetPageProperty("hidden_subsection", "");
	}

	$APPLICATION->SetPageProperty('currentSectionId', $arCurSection["ID"]);
?>
