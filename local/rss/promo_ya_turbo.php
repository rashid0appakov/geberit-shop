<?
$_SERVER["DOCUMENT_ROOT"]=realpath(dirname(__FILE__,3));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

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

	$SITE_ID=S8;

	$rsSites = CSite::GetByID($SITE_ID);
	$arSite = $rsSites->Fetch();

	

	
	$promoIB=18;
	$catalogIB=84;

	$stFileName = 'promo_yandex_turbo.xml';
	if (!$fp = @fopen($_SERVER['DOCUMENT_ROOT'].'/feeds/'.$stFileName, 'wb'))
		die('YANDEX_ERR_FILE_OPEN_WRITING');

	$stPHPHead = '<rss' .
		' xmlns:yandex="http://news.yandex.ru"' .
		' xmlns:media="http://search.yahoo.com/mrss/"' .
		' xmlns:turbo="http://turbo.yandex.ru"' .
	' version="2.0"><channel>'."\n";
	fwrite($fp, $stPHPHead);

	$stXML = '';
	$props=[];
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$promoIB));
    while ($prop_fields = $properties->GetNext())
    {
    	if(strpos($prop_fields["CODE"], "_".$SITE_ID)!==false || $prop_fields["CODE"]==="FILTER_JSON")
    	{
    		$props[$prop_fields["CODE"]]=$prop_fields["ID"];
    	}
    	
    }
    $p_main_type_admin=[];
    $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$promoIB, "CODE"=>"MAIN_TYPE_ADMIN"));
	while($enum_fields = $property_enums->GetNext())
	{
		$p_main_type_admin[strtoupper($enum_fields["XML_ID"])]=$enum_fields["VALUE"];
	}
    
	
	$arActions=[];
	$rsActions = CIBlockElement::GetList(array(), array(
		'IBLOCK_ID' => $promoIB,
		'ACTIVE' => 'Y',
		'INCLUDE_SUBSECTIONS' => 'Y',
		'PROPERTY_MAIN_TYPE_ADMIN_VALUE'=>$p_main_type_admin[$SITE_ID],
		"<=DATE_ACTIVE_FROM" => array(false, ConvertTimeStamp(false, "FULL")),
  ">=DATE_ACTIVE_TO"   => array(false, ConvertTimeStamp(false, "FULL")),
	), false, false/*array('nPageSize' => 100)*/, array(
		'ID',
		'NAME',
		'IBLOCK_SECTION_ID',
		'DETAIL_PICTURE',
		'DETAIL_TEXT',
		'DETAIL_PAGE_URL',
		'PROPERTY_BRANDS_'.$SITE_ID,
		'PROPERTY_PRODUCTS_'.$SITE_ID,
		'PROPERTY_SECTIONS_'.$SITE_ID,
	));
	while ($obAction = $rsActions->GetNextElement()) {
		$arAction=$obAction->GetFields();
		$arAction["PROPERTIES"]=array_change_key_case($obAction->GetProperties(["ID"=>"ASC"],["ID"=>$props]),CASE_UPPER);
		\Bitrix\Iblock\Component\Tools::getFieldImageData(
			$arAction,
			array('DETAIL_PICTURE'),
			\Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);
		/* -- Get products filter params ---------------------------------------- */
	    $arAction["PRODUCTS_FILTER"] = [];
	    if (!empty($arAction["PROPERTIES"]['SECTIONS_'.$SITE_ID]["VALUE"]))
	        $arAction["PRODUCTS_FILTER"]["SECTION_ID"][] = $arAction["PROPERTIES"]['SECTIONS_'.$SITE_ID]["VALUE"];

	    if (!empty($arAction['PROPERTIES']['BRANDS_'.$SITE_ID]["VALUE"]))
	        $arAction["PRODUCTS_FILTER"]["PROPERTY_MANUFACTURER"] = $arAction['PROPERTIES']['BRANDS_'.$SITE_ID]["VALUE"];

	    if (!empty($arAction["PROPERTIES"]['PRODUCTS_'.$SITE_ID]["VALUE"]))
	        $arAction["PRODUCTS_FILTER"]["ID"]  = $arAction["PROPERTIES"]['PRODUCTS_'.$SITE_ID]["VALUE"];

	    if (count($arAction["PRODUCTS_FILTER"]) > 1)
	        $arAction["PRODUCTS_FILTER"]= [array_merge(
	            ['LOGIC' => 'OR'],
	            $arAction["PRODUCTS_FILTER"]
	        )];
	    if (!empty($arAction["PROPERTIES"]['FILTER_JSON']["~VALUE"])) {
	        $arrayJson = json_decode($arAction["PROPERTIES"]['FILTER_JSON']["~VALUE"], true);
	        if ($arrayJson['section'])
	            $arAction["PRODUCTS_FILTER"]["SECTION_ID"][]   = $arrayJson['section'];
	        if ($arrayJson['filter'])
	            $arAction["PRODUCTS_FILTER"] = array_merge($arAction["PRODUCTS_FILTER"], $arrayJson['filter']);
	    }
	    if (count($arAction["PRODUCTS_FILTER"]) >= 1)
	    {
	    	$arAction["PRODUCTS_FILTER"]['ACTIVE'] = 'Y';
			$arAction["PRODUCTS_FILTER"]['SECTION_ACTIVE'] = 'Y';
			$arActions[]=$arAction;
		}
	}

	global $arrFilter;
	
	foreach ($arActions as $arAction) {
		ob_start();
		?>
		<header>
		
		<?
		if (is_array($arAction["DETAIL_PICTURE"])){?>
			<figure>
				<img
					src="<?=$arAction["DETAIL_PICTURE"]["SRC"]?>"
					width="<?=$arAction["DETAIL_PICTURE"]["WIDTH"]?>"
					height="<?=$arAction["DETAIL_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arAction["DETAIL_PICTURE"]["ALT"]?>"
					title="<?=$arAction["DETAIL_PICTURE"]["TITLE"]?>"
					/>
			</figure>
		<?}
		
		?>
		</header>

		<?if ($arAction['DETAIL_TEXT']){?>
			<div class="descr"><p><?=$arAction['DETAIL_TEXT']?></p></div>
		<?}

		$arrFilter=$arAction["PRODUCTS_FILTER"];
	  	$APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "catalog",
            array(
                "IBLOCK_TYPE" => "",
                "IBLOCK_ID" => "$catalogIB",
                "ELEMENT_SORT_FIELD" => $sort,
                "ELEMENT_SORT_ORDER" => $order,
                "ELEMENT_SORT_FIELD2" => "",
                "ELEMENT_SORT_ORDER2" => "",
                "PROPERTY_CODE" => array(
                    0 => "NEWPRODUCT",
                    1 => "SALELEADER",
                    2 => "DISCOUNT",
                    3 => "",
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
                "FILTER_NAME" => "arrFilter",
                "CACHE_TYPE" => "N",
                "CACHE_TIME" => "36000000",
                "CACHE_FILTER" => "Y",
                "CACHE_GROUPS" => "Y",
                "SET_TITLE" => "N",
                "MESSAGE_404" => "",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "FILE_404" => "",
                "DISPLAY_COMPARE" => "Y",
                "PAGE_ELEMENT_COUNT" => "12",
                "LINE_ELEMENT_COUNT" => "3",
                "PRICE_CODE" => "",
                "USE_PRICE_COUNT" => "Y",
                "SHOW_PRICE_COUNT" => "1",
                "PRICE_VAT_INCLUDE" => "Y",
                "USE_PRODUCT_QUANTITY" => "Y",
                "ADD_PROPERTIES_TO_BASKET" => "N",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRODUCT_PROPERTIES" => array(
                ),
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "load_more",
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
                "COMPATIBLE_MODE" => "Y"
            ),
            false
        );
		$content = ob_get_contents();
		$content=preg_replace("/href=\"(\/+)/","href=\"https://".$arSite["SERVER_NAME"]."$1", $content);
		$content=preg_replace("/src=\"(\/+)/","src=\"https://".$arSite["SERVER_NAME"]."$1", $content);
		$content=str_replace('id="buy_"', '', $content);

		$content=preg_replace('/<div class=\"pagination-wrapper\".*?>(<div.*?>(?1)*?<\/div>|.)*?<\/div>/is','<div class="more"><a href="https://'.$arSite["SERVER_NAME"].$arAction["DETAIL_PAGE_URL"].'">
			<p>Показать еще</p>
		</a></div>',$content);

		ob_end_clean();
		$stXML .=
			'<item turbo="true">'.
				'<title>'.$arAction["NAME"].'</title>'.
				' <turbo:extendedHtml>true</turbo:extendedHtml>'.
				'<link>https://'.$arSite["SERVER_NAME"].$arAction['DETAIL_PAGE_URL'].'</link>'.
				'<turbo:content><![CDATA[' .$content.
				']]></turbo:content>'.
			'</item>'."\n";
	}
	fwrite($fp, $stXML);
	fwrite($fp, '</channel></rss>');
	fclose($fp);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>