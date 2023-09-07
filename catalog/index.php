<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Новинки Geberit | Geberit Shop");?>
<?php
//            $APPLICATION->SetTitle('Крупнейший каталог лучшей сантехники с ценами 2019 года');
$APPLICATION->SetTitle(str_replace(["{brand}","{текущий год}"],["Geberit",date('Y')],"Каталог сантехники {brand} {текущий год} года"));

global $seo_tag;
$dop = '';
$arFilter = Array('IBLOCK_ID' => SEO_FILTER_IBLOCK_ID, 'GLOBAL_ACTIVE'=>'Y');
$db_list = CIBlockSection::GetList(Array("DEPTH_LEVEL"=>"DESC"), $arFilter, false,Array('UF_FILTER_FOR_SEC'));
while($ar_result = $db_list->GetNext())
{
	//берем урл раздела и плюсуем сим код
	$res = CIBlockSection::GetByID($ar_result["UF_FILTER_FOR_SEC"]);
	if($ar_res = $res->GetNext())
	  $section_page_url = $ar_res['SECTION_PAGE_URL'];
		//собираем символьный код
	$code = '';
	$nav = CIBlockSection::GetNavChain(SEO_FILTER_IBLOCK_ID,$ar_result['ID']);
	while($arSectionPath = $nav->GetNext()){
	   $code .= $arSectionPath["CODE"].'/';
	} 
	$code = $section_page_url.$code;

	$arr_seo_links[] = $code;
}

$url = explode('?', $_SERVER['REQUEST_URI']);
$url = explode('/', $url[0]);
$code='';
$url = array_diff($url, array('', null));
$dop = '';
$useSeries=0;

if(count($url)==3||count($url)==4){
	if ((count($url)==3) && strpos($_SERVER['REQUEST_URI'], '/newproduct/')!==false){
		\Bitrix\Iblock\Component\Tools::process404(
            ""
            ,($arParams["SET_STATUS_404"] === "Y")
            ,($arParams["SET_STATUS_404"] === "Y")
            ,($arParams["SHOW_404"] === "Y")
            ,$arParams["FILE_404"]
        );
        CHTTP::SetStatus("404 Not Found");
        @define("ERROR_404","Y");

        include($_SERVER["DOCUMENT_ROOT"]."/404/index.php");
	}elseif ((count($url)==4) && strpos($_SERVER['REQUEST_URI'], '/newproduct/')!==false){
		\Bitrix\Iblock\Component\Tools::process404(
            ""
            ,($arParams["SET_STATUS_404"] === "Y")
            ,($arParams["SET_STATUS_404"] === "Y")
            ,($arParams["SHOW_404"] === "Y")
            ,$arParams["FILE_404"]
        );
        CHTTP::SetStatus("404 Not Found");
        @define("ERROR_404","Y");

        include($_SERVER["DOCUMENT_ROOT"]."/404/index.php");
	}
	
	foreach ($url as $value) {
		if(strlen($value)>0 && $useSeries==0){
			$code=$value;
			$arSelect = Array("ID", "NAME", "CODE");
			$arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "CODE"=>$code, "ACTIVE"=>"Y");
			$res = CIBlockElement::GetList(Array(), $arFilter, false,false, $arSelect);
			while($ob = $res->GetNextElement())
			{
			 $arFields = $ob->GetFields();
			 $GLOBALS['arrFilter']['=PROPERTY_SERIES'] = array($arFields['ID']);
			 $arr_seo_links[] = strtolower($arFields['CODE']);
			 $useSeries++;	
			}

			if (strpos($_SERVER['REQUEST_URI'], '/newproduct/')!==false){
				if (isset($GLOBALS['arrFilter']['=PROPERTY_SERIES'])){
					\Bitrix\Iblock\Component\Tools::process404(
	                    ""
	                    ,($arParams["SET_STATUS_404"] === "Y")
	                    ,($arParams["SET_STATUS_404"] === "Y")
	                    ,($arParams["SHOW_404"] === "Y")
	                    ,$arParams["FILE_404"]
	                );
	                CHTTP::SetStatus("404 Not Found");
	                @define("ERROR_404","Y");

	                include($_SERVER["DOCUMENT_ROOT"]."/404/index.php");

					}
			}
			
		}
	}
	foreach ($arr_seo_links as $arr_seo_link){
		if (strpos($_SERVER['REQUEST_URI'], '/'.$arr_seo_link.'/') !== false){
			$seo_tag = $arr_seo_link;
			$dop = $seo_tag.'/';
		}
	}
}

foreach ($arr_seo_links as $arr_seo_link){
	if (strpos($_SERVER['REQUEST_URI'],$arr_seo_link) !== false){
		$seo_tag = $arr_seo_link;
		foreach ($GLOBALS['PAGE_DATA']['SEO_FILTER']['TAGS'] as $key => $variable) {
			foreach ($variable as $k => $value) {
				if($k == $arr_seo_link ){
					$dop = $value['CODE'].'/';
				}
			}
		}
	}
}

 //301 redirect SECTION_CODE => SECTION_CODE_PATH
global $APPLICATION;
$dir = $APPLICATION->GetCurDir();
$file = $_SERVER['DOCUMENT_ROOT'].'/utilites/301.csv';
$row = 1;
if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    	if (strpos($dir,$data[0])!==false){
    		 $dir =str_replace($data[0], $data[1], $dir); 
    		 LocalRedirect($dir, false, '301 Moved permanently');
    	}
        $row++;
    }
    fclose($handle);
}   

$status_404 = 'Y';

//var_dump($_SERVER['REQUEST_URI']);
if (strpos($_SERVER['REQUEST_URI'], '/catalog/newproduct/') !== false){
	$status_404 = 'N';
	global $arrFilter;
	$arrFilter['PROPERTY_NEWPRODUCT'] = 'Y';

}  

 
?>
<?$APPLICATION->IncludeComponent("bart:stopsovetnik", "", array(), false);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	".default", 
	array(
		"ACTION_VARIABLE" => "",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BASKET_URL" => "/personal/cart/",
		"BIG_DATA_RCM_TYPE" => "any",
		"BUTTON_CREDIT_HREF" => "/credit/",
		"BUTTON_DELIVERY_HREF" => "/delivery/",
		"BUTTON_PAYMENTS_HREF" => "/payments/",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"COMPARE_ELEMENT_SORT_FIELD" => "sort",
		"COMPARE_ELEMENT_SORT_ORDER" => "asc",
		"COMPARE_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "",
		),
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"COMPARE_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPARE_OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "PROP2",
			2 => "PROP3",
			3 => "",
		),
		"COMPARE_PROPERTY_CODE" => array(
			0 => "",
			1 => "ARTNUMBER",
			2 => "GUARANTEE",
			3 => "SERIES",
			4 => "COUNTRY",
			5 => "AVTOMATICHESKIY_DRENAZH_VODY_PRI_DLITELNOM_NEISPOL",
			6 => "AVTOMATICHESKIY_TAYMER_",
			7 => "AVTOMATICHESKOE_OCHISHCHENIE_DUSHEVOGO_STERZHNYA_I",
			8 => "AVTOOTKLYUCHENIE_PRI_NAGREVE",
			9 => "ANTIBAKTERIALNYE_SVOYSTVA_MATERIALOV_SIDENYA_I_ST",
			10 => "ANTISKOLZYASHCHEE_POKRYTIE",
			11 => "BEZOBODKOVYY",
			12 => "BESSHUMNOE_OPUSKANIE_SIDENYA_I_KRYSHKI_",
			13 => "BLOKIROVKA_UPRAVLENIYA_FUNKTSIYAMI_S_POMOSHCHYU_PD",
			14 => "BYSTROSYEMNYY_MEKHANIZM",
			15 => "V_SOCHETANII_TOLKO_S_SENSOWASH_",
			16 => "VARIANT_USTANOVKI",
			17 => "VES_KG",
			18 => "VID_USTANOVKI",
			19 => "VODONEPRONITSAEMOST_",
			20 => "VOZMOZHNOST_POLNOGO_SNYATIYA_SIDENYA_DLYA_DIZINFEK",
			21 => "VRASHCHENIE_IZLIVA_",
			22 => "VREMYA_NAGREVA_MIN",
			23 => "VYSOTA_IZLIVA_SM_",
			24 => "VYSOTA_MM_",
			25 => "VYSOTA_CHASHI_SM",
			26 => "VYSOTA_SM",
			27 => "GLUBINA_MM_",
			28 => "GLUBINA_SM",
			29 => "GOTOVYKH_OTVERSTIY_DLYA_SMESITELYA",
			30 => "DATCHIK_DVIZHENIYA_",
			31 => "DIAMETR_VOZDUKHOVODA_MM_",
			32 => "DIAMETR_PEREKHODNIKA_DLYA_SLIVA_SM",
			33 => "DIAMETR_SLIVA_SM",
			34 => "DLINA_IZLIVA_SM_",
			35 => "DLINA_SHLANGA_SM_",
			36 => "DLINA_MM_",
			37 => "DLINA_SM",
			38 => "DLYA_KLAVISH",
			39 => "DLYA_USTANOVKI_V_",
			40 => "DONNYY_KLAPAN_",
			41 => "DOPOLNITELNYE_FUNKTSII_",
			42 => "DUSH_DLYA_DAM",
			43 => "DUSH_DLYA_YAGODITS",
			44 => "ZAVODSKAYA_NASTROYKA_SMYVA_L",
			45 => "ZAPOLNENIE_DVERTSY_",
			46 => "ZASHCHITA_OT_VODYANYKH_BRYZG",
			47 => "ZASHCHITA_OT_OBRATNOGO_POTOKA_",
			48 => "ZVUKOIZOLIRUYUSHCHAYA_PROKLADKA_V_KOMPLEKTE",
			49 => "KLAVISHA",
			50 => "KLASS_ZASHCHITY_IP_",
			51 => "KLASS_IZOLYATSII_DVIGATELYA_",
			52 => "KOLICHESTVO_SEKTSIY",
			53 => "KOLLEKTSIYA_INSTALYATSII",
			54 => "KOMPLEKTOM_DESHEVLE",
			55 => "KOMFORTYNY_DUSH_",
			56 => "KONSTRUKTSIYA_DVEREY_",
			57 => "KREPLENIE",
			58 => "KREPLENIE_K_STENE_V_KOMPLEKTE",
			59 => "KRYSHKA_SIDENE",
			60 => "MAKS_ZHESTKOST_VODY_MMOL_L",
			61 => "MAKS_MOSHCHNOST_VT",
			62 => "MAKS_RASKHOD_VOZDUKHA_M3_CH_",
			63 => "MAKSIMALNOE_DAVLENIE_BAR",
			64 => "MATERIAL",
			65 => "MATERIAL_KORPUSA",
			66 => "MATERIAL_FASADA",
			67 => "MEZHOSEVOE_RASSTOYANIE_POD_KREPEZH_SHPILKI_SM",
			68 => "MEZHOSEVOE_RASSTOYANIE_SM",
			69 => "METOD_KREPLENIYA",
			70 => "METOD_USTANOVKI_SLIVNOGO_BACHKA",
			71 => "MEKHANIZM_",
			72 => "MEKHANIZM_SLIVA",
			73 => "MINIMALNOE_DAVLENIE_BAR",
			74 => "MONTAZH",
			75 => "MONTAZHNAYA_VYSOTA_SM",
			76 => "MONTAZHNAYA_GLUBINA_SM",
			77 => "MOSHCHNOST_VT",
			78 => "NAGRUZKA_NA_DVERTSU_KG_",
			79 => "NAZNACHENIE",
			80 => "NAMECHENNYKH_OTVERSTIY_DLYA_SMESITELYA",
			81 => "NAPOR_VODY_MPA",
			82 => "NAPRAVLENIE_VYPUSKA",
			83 => "NAPRAVLENIE_PODKLYUCHENIYA",
			84 => "NAPRYAZHENIE_PITANIYA_12_V_",
			85 => "NAPRYAZHENIE_PITANIYA_V_",
			86 => "NAPRYAZHENIE_V",
			87 => "NEZAMEDLITELNYY_PODOGREV_VODY_DLYA_DUSHA",
			88 => "NOMINALNOE_NAPRYAZHENIE_V",
			89 => "OBLAST_PRIMENENIYA",
			90 => "OBRATNYY_KLAPAN_",
			91 => "OBEM_ML_",
			92 => "OBEM_SMYVN_BACHKA_L",
			93 => "OBYEM_L",
			94 => "OGRANICHENIE_TEMPERATURY_",
			95 => "OSNASHCHENIE_",
			96 => "PANEL_SMYVA_V_KOMPLEKTE",
			97 => "POVERKHNOST_",
			98 => "POVOROTNYY",
			99 => "PODVOD_VODY_V_BACHOK",
			100 => "PODKLYUCHENIE",
			101 => "PODSVETKA_KNOPOK_INDIKATSIEY_",
			102 => "PODKHODIT_TOLKO_DLYA_UNITAZOV_",
			103 => "POLOCHKA_V_CHASHE",
			104 => "POTREBLYAEMAYA_MOSHCHNOST_KVT_",
			105 => "PRISOEDINITELNYY_DIAMETR_MM_",
			106 => "PROGRAMMIREMYE_PROFILI_POLZOVATELYA_",
			107 => "PROIZVODSTVO_",
			108 => "PROTIVOPOZHARNYY_",
			109 => "PULT_DISTANTSIONNOGO_UPRAVLENIYA_PDU",
			110 => "PYLEIZOLYATSIYA_",
			111 => "RABOCHEE_DAVLENIE_BAR",
			112 => "RAZMER_DVERTSY_SH_V_MM_",
			113 => "RAZMER_ROZETKI_MM_",
			114 => "RAZMER_UPAKOVKI",
			115 => "RASKHOD_VODY_L_MIN_",
			116 => "REGULIROVKA_GLUBINY_MONTAZHA",
			117 => "REGULIROVKA_PO_VYSOTE_MM",
			118 => "REGULIROVKA_POLOZHENIYA_DVERTSY_",
			119 => "REGULIROVKA_PRODOLZHITELNOSTI_SMYVA",
			120 => "REGULIRUEMAYA_MOSHCHNOST_VODNOY_STRUI_",
			121 => "REGULIRUEMAYA_TEMPERATURA_VODY_",
			122 => "REGULIRUEMAYA_TEMPERATURA_SIDENYA",
			123 => "REGULIRUEMAYA_TEMPERATURA_FENA_",
			124 => "REGULIRUEMOE_POLOZHENIE_DUSHEVOGO_STERZHNYA",
			125 => "REGULIRUEMYE_PETLI_",
			126 => "REGULIRUEMYY_GIGROSTAT_",
			127 => "REGULIRUEMYY_TAYMER_",
			128 => "REZHIM_SLIVA_VODY",
			129 => "REZHIM_EKONOMII_ENERGII_",
			130 => "SVETOVOY_INDIKATOR_",
			131 => "SENSOR_DLYA_OBNARUZHENIYA_CHELOVEKA",
			132 => "SIDENE_V_KOMPLEKTE",
			133 => "SIDENE_I_KRYSHKA_LEGKO_SNIMAYUTSYA_ODNOY_RUKOY",
			134 => "SISTEMA_ANTIVSPLESK",
			135 => "SISTEMA_KHRANENIYA",
			136 => "SKRYTYY_",
			137 => "SKRYTYY_PODVOD_VODY_ELEKTROPITANIYA",
			138 => "SOVMESTIM_S_LYUBYM_PODVESNYM_UNITAZOM",
			139 => "SPOSOB_OTKRYVANIYA_",
			140 => "STANDART_PODVODKI_",
			141 => "STILISTIKA_DIZAYNA",
			142 => "STRANA_PROIZVODITEL",
			143 => "TEMPERATURA_VODY_C_",
			144 => "TEMPERATURA_PRI_EKSPLUATATSII_C_",
			145 => "TEMPERATURA_SIDENYA_S",
			146 => "TEMPERATURA_FENA_S",
			147 => "TEPLONOSITEL",
			148 => "TERMOREGULYATOR",
			149 => "TEKHNOLOGII",
			150 => "TIP",
			151 => "TIP_ZAMKA_",
			152 => "TIP_INSTALLYATSII",
			153 => "TIP_MONTAZHA",
			154 => "TIP_PODVODKI_",
			155 => "TIP_PRODUKTA_",
			156 => "TIP_UPRAVLENIYA",
			157 => "TIP_ELEKTRODVIGATELYA_",
			158 => "TIPORAZMER_SHIRINA_SM",
			159 => "UGLOVAYA_KONSTRUKTSIYA",
			160 => "UPRAVLENIE_",
			161 => "UROVEN_ZVUKOVOGO_DAVLENIYA_DB_A_",
			162 => "USILENNYY_",
			163 => "FAKTURA_",
			164 => "FORMA",
			165 => "FORSUNKA_SNIMAETSYA_DLYA_MYTYA_I_ZAMENY",
			166 => "FUNKTSIYA_MASSAZHA_S_PULSATSIEY_",
			167 => "FUNKTSIYA_NOCHNOY_PODSVETKI_",
			168 => "FUNKTSIYA_OBOGREVA_POMESHCHENIYA",
			169 => "FUNKTSIYA_EKONOMII_RASKHODA_",
			170 => "FURNITURA",
			171 => "TSVET",
			172 => "TSVET_KLAVISHI",
			173 => "CHASTOTA_VRASHCHENIYA_OB_MIN_",
			174 => "CHASTOTA_GTS",
			175 => "SHARIKOVYE_PODSHIPNIKI_",
			176 => "SHIRINA_MM_",
			177 => "SHIRINA_SM",
			178 => "SHNUROVOY_VYKLYUCHATEL_",
			179 => "SHUMOIZOLYATSIYA_",
			180 => "ELEKTROVYKLYUCHATEL_",
			181 => "ELEKTROPRIVODNOE_SIDENE_I_KRYSHKA",
			182 => "GIDROZATVOR",
			183 => "DIAMETR_PODKLYUCHENIYA_SM_",
			184 => "PROPUSKNAYA_SPOSOBNOST_L_MIN",
			185 => "",
		),
		"COMPATIBLE_MODE" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "STATIC",
		"CONVERT_CURRENCY" => "N",
		"COUNT_REVIEW" => "5",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTNUMBER",
			1 => "COLOR",
			2 => "PROP2",
			3 => "PROP3",
			4 => "proizvoditely",
			5 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"DETAIL_STRICT_SECTION_CHECK" => "Y",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DETAIL_IMG_HEIGHT" => "390",
		"DISPLAY_DETAIL_IMG_WIDTH" => "390",
		"DISPLAY_ELEMENT_SELECT_BOX" => "N",
		"DISPLAY_IMG_HEIGHT" => "178",
		"DISPLAY_IMG_WIDTH" => "178",
		"DISPLAY_MORE_PHOTO_HEIGHT" => "86",
		"DISPLAY_MORE_PHOTO_WIDTH" => "86",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => GetSortField(),
        "ELEMENT_SORT_FIELD2" => "SORT",//"PROPERTY_PRICE_UPDATE".CClass::getCurrentAvalCode(),
        "ELEMENT_SORT_ORDER" => "ASC",
        "ELEMENT_SORT_ORDER2" => "ASC",
		"FIELDS" => array(
			0 => "TITLE",
			1 => "ADDRESS",
			2 => "DESCRIPTION",
			3 => "PHONE",
			4 => "SCHEDULE",
			5 => "EMAIL",
			6 => "IMAGE_ID",
			7 => "COORDINATES",
			8 => "",
		),
		"FILE_404" => "",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "arrFilter",
		"FILTER_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PRICE_CODE" => array(
			0 => "BASE",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"HIDE_BUTTON_ALL" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
		"IBLOCK_ID_REVIEWS" => "17",
		"IBLOCK_TYPE" => "catalog_tiptop",
		"IBLOCK_TYPE_REVIEWS" => "catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"INSTANT_RELOAD" => "N",
		"LINE_ELEMENT_COUNT" => "",
		"LINK_ELEMENTS_URL" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_PROPERTY_SID" => "",
		"LIST_BROWSER_TITLE" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_META_KEYWORDS" => "-",
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_OFFERS_LIMIT" => "",
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTNUMBER",
			1 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "",
			1 => "ARTNUMBER",
			2 => "MANUFACTURER",
			3 => "ODDS",
			4 => "MORE_PHOTO",
			5 => "",
		),
		"MAIN_TITLE" => "Наличие на складах",
		"NUMBER_ACCESSORIES" => "8",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "COLOR,PROP2,PROP3",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "0",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "load_more",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "27",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PATH_TO_SHIPPING" => "/delivery/",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "SPB",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_ID_VARIABLE" => "",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PROPERTY_CODE_MOD" => array(
			0 => "",
			1 => "Width",
			2 => "",
		),
		"RELATED_PRODUCTS_SHOW" => "Y",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"SECTION_COUNT_ELEMENTS" => "N",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_TOP_DEPTH" => "",
		"SEF_FOLDER" => "/",
		"SEF_MODE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => $status_404,
		"SET_TITLE" => "Y",
		"SHOW_404" => $status_404,
		"SHOW_DEACTIVATED" => "N",
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_TOP_ELEMENTS" => "N",
		"STORES" => array(
			0 => "",
			1 => "",
		),
		"STORE_PATH" => "/store/#store_id#",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"USE_ALSO_BUY" => "N",
		"USE_BIG_DATA" => "Y",
		"USE_COMPARE" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FILTER" => "Y",
		"USE_GIFTS_DETAIL" => "Y",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
		"USE_GIFTS_SECTION" => "Y",
		"USE_MAIN_ELEMENT_SECTION" => "Y",
		"USE_MIN_AMOUNT" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"USE_REVIEW" => "N",
		"USE_STORE" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"USE_FILTER_SEO" => "Y",
		"USE_FILTER_SEO_IBLOCK" => SEO_FILTER_IBLOCK_ID,
		"DISPLAY_INSTALLMENT_PLAN" => "Y",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "Y",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "catalog/#SECTION_CODE_PATH#/".$dop,
			"element" => "product/#ELEMENT_CODE#/",
			"compare" => "compare/",
			"smart_filter" => "catalog/#SECTION_CODE_PATH#/".$dop."#SMART_FILTER_PATH#/",
		)
	),
	false
);?>

<?
// }

if (strpos($_SERVER['REQUEST_URI'], '/catalog/newproduct/') !== false){
	$APPLICATION->SetPageProperty("title", "Новинки — купить новинки Геберит в Москве, цены в каталоге интернет-магазина Geberit Shop");
	$APPLICATION->SetTitle("Новинки");
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>