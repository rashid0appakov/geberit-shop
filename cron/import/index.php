<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$IBLOCK_ID = 15;
$SECTION_ID = false;
$PRICE_ID = 1;

$pathImg = "https://drvt.shop/uploads/shop/products/origin/";
$pathAddImg = "https://drvt.shop/uploads/shop/products/origin/additional/";

$params = Array(
	"max_len" => "100", // обрезает символьный код до 100 символов
	"change_case" => "L", // буквы преобразуются к нижнему регистру
	"replace_space" => "_", // меняем пробелы на нижнее подчеркивание
	"replace_other" => "_", // меняем левые символы на нижнее подчеркивание
	"delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
	"use_google" => "false", // отключаем использование google
); 

$dataHandler = fopen(__DIR__."/data/products.csv", "r");

$row = 0;
while (($data = fgetcsv($dataHandler, 0, ";")) !== FALSE) {
	$row++;
	if($row == 1 || $data[2] == ''){
		continue;
	}

	//Создание товара
	$name = $data[0];
	$url = $data[2];
	$price = $data[3];
	$active = $data[8] ? "Y" : "N";
	$arCat = explode("/", $data[13]);
	$img = $pathImg.$data[15];
	$number = $data[6];
	
	//Доп. картинки
	$arAddImgs = explode("|", $data[17]);
	
	//Лейблы
	$new = $data[10] ? "Да" : "";
	$hit = $data[9] ? "Да" : "";
	$sale = $data[11] ? "Да" : "";
	
	//Основные свойства
	$brand = $data[152];
	$collection = $data[12];
	$country = $data[155];
	$garantia = $data[188];

	//Другие свойства
	$GIDROZATVOR = $data[24];
	$PROPUSKNAYA_SPOSOBNOST_L_MIN = $data[25];
	$DIAMETR_PODKLYUCHENIYA_SM_ = $data[26];
	$DLINA_MM_ = $data[29];
	$PROIZVODSTVO_ = $data[30];
	$DIAMETR_VOZDUKHOVODA_MM_ = $data[31];
	$NAPRYAZHENIE_PITANIYA_12_V_ = $data[32];
	$PRISOEDINITELNYY_DIAMETR_MM_ = $data[33];
	$SVETOVOY_INDIKATOR_ = $data[34];
	$TIP_ELEKTRODVIGATELYA_ = $data[35];
	$KLASS_ZASHCHITY_IP_ = $data[36];
	$DATCHIK_DVIZHENIYA_ = $data[37];
	$REGULIRUEMYY_GIGROSTAT_ = $data[38];
	$REGULIRUEMYY_TAYMER_ = $data[39];
	$MAKS_RASKHOD_VOZDUKHA_M3_CH_ = $data[40];
	$UROVEN_ZVUKOVOGO_DAVLENIYA_DB_A_ = $data[41];
	$AVTOMATICHESKIY_TAYMER_ = $data[42];
	$OBRATNYY_KLAPAN_ = $data[43];
	$SHNUROVOY_VYKLYUCHATEL_ = $data[44];
	$SHARIKOVYE_PODSHIPNIKI_ = $data[45];
	$CHASTOTA_VRASHCHENIYA_OB_MIN_ = $data[46];
	$KLASS_IZOLYATSII_DVIGATELYA_ = $data[47];
	$NAPRYAZHENIE_PITANIYA_V_ = $data[48];
	$POTREBLYAEMAYA_MOSHCHNOST_KVT_ = $data[49];
	$METOD_KREPLENIYA = $data[50];
	$RAZMER_UPAKOVKI = $data[51];
	$SOVMESTIM_S_LYUBYM_PODVESNYM_UNITAZOM = $data[52];
	$ZAVODSKAYA_NASTROYKA_SMYVA_L = $data[53];
	$TIP_UPRAVLENIYA = $data[54];
	$REGULIROVKA_PO_VYSOTE_MM = $data[56];
	$MAKSIMALNOE_DAVLENIE_BAR = $data[57];
	$DLYA_KLAVISH = $data[58];
	$MINIMALNOE_DAVLENIE_BAR = $data[59];
	$TIP_INSTALLYATSII = $data[60];
	$KLAVISHA = $data[61];
	$SSYLKA_NA_DOP_TOVARY = $data[62];
	$KOLLEKTSIYA_INSTALYATSII = $data[63];
	$TSVET_KLAVISHI = $data[64];
	$POVOROTNYY = $data[65];
	$FUNKTSIYA_OBOGREVA_POMESHCHENIYA = $data[66];
	$SHIRINA_SM = $data[67];
	$MOSHCHNOST_VT = $data[68];
	$NAPRYAZHENIE_V = $data[69];
	$STANDART_PODVODKI_ = $data[70];
	$FUNKTSIYA_EKONOMII_RASKHODA_ = $data[71];
	$DOPOLNITELNYE_FUNKTSII_ = $data[72];
	$VRASHCHENIE_IZLIVA_ = $data[73];
	$ZASHCHITA_OT_OBRATNOGO_POTOKA_ = $data[74];
	$DONNYY_KLAPAN_ = $data[75];
	$OGRANICHENIE_TEMPERATURY_ = $data[76];
	$TIP_PODVODKI_ = $data[77];
	$VYSOTA_IZLIVA_SM_ = $data[78];
	$DLINA_IZLIVA_SM_ = $data[79];
	$FAKTURA_ = $data[80];
	$TIP_PRODUKTA_ = $data[81];
	$TEKHNOLOGII = $data[82];
	$TIP_ZAMKA_ = $data[83];
	$REGULIROVKA_POLOZHENIYA_DVERTSY_ = $data[85];
	$TERMOREGULYATOR = $data[86];
	$REGULIROVKA_PRODOLZHITELNOSTI_SMYVA = $data[87];
	$STILISTIKA_DIZAYNA = $data[88];
	$DLYA_USTANOVKI_V_ = $data[89];
	$AVTOOTKLYUCHENIE_PRI_NAGREVE = $data[90];
	$REGULIROVKA_GLUBINY_MONTAZHA = $data[91];
	$SORTIROVKA_DOP_TOVAROV = $data[92];
	$SPOSOB_OTKRYVANIYA_ = $data[93];
	$ELEKTROVYKLYUCHATEL_ = $data[94];
	$PANEL_SMYVA_V_KOMPLEKTE = $data[95];
	$FORMA = $data[96];
	$ZAPOLNENIE_DVERTSY_ = $data[97];
	$TEPLONOSITEL = $data[98];
	$ZVUKOIZOLIRUYUSHCHAYA_PROKLADKA_V_KOMPLEKTE = $data[99];
	$KONSTRUKTSIYA_DVEREY_ = $data[100];
	$VREMYA_NAGREVA_MIN = $data[101];
	$KREPLENIE_K_STENE_V_KOMPLEKTE = $data[102];
	$MATERIAL = $data[103];
	$REGULIRUEMYE_PETLI_ = $data[104];
	$RABOCHEE_DAVLENIE_BAR = $data[105];
	$DIAMETR_SLIVA_SM = $data[106];
	$OBYEM_L = $data[107];
	$SKRYTYY_ = $data[108];
	$NAPRAVLENIE_PODKLYUCHENIYA = $data[109];
	$DIAMETR_PEREKHODNIKA_DLYA_SLIVA_SM = $data[110];
	$VID_USTANOVKI = $data[111];
	$USILENNYY_ = $data[112];
	$MEZHOSEVOE_RASSTOYANIE_SM = $data[113];
	$MEZHOSEVOE_RASSTOYANIE_POD_KREPEZH_SHPILKI_SM = $data[114];
	$ANTISKOLZYASHCHEE_POKRYTIE = $data[115];
	$PROTIVOPOZHARNYY_ = $data[116];
	$MONTAZHNAYA_GLUBINA_SM = $data[117];
	$DLINA_SM = $data[118];
	$VODONEPRONITSAEMOST_ = $data[119];
	$KOLICHESTVO_SEKTSIY = $data[120];
	$MONTAZHNAYA_VYSOTA_SM = $data[121];
	$VARIANT_USTANOVKI = $data[122];
	$PYLEIZOLYATSIYA_ = $data[123];
	$TIPORAZMER_SHIRINA_SM = $data[125];
	$SHUMOIZOLYATSIYA_ = $data[126];
	$NAMECHENNYKH_OTVERSTIY_DLYA_SMESITELYA = $data[128];
	$RAZMER_DVERTSY_SH_V_MM_ = $data[129];
	$NAZNACHENIE = $data[130];
	$GOTOVYKH_OTVERSTIY_DLYA_SMESITELYA = $data[131];
	$NAGRUZKA_NA_DVERTSU_KG_ = $data[132];
	$OSNASHCHENIE_ = $data[133];
	$PODKLYUCHENIE = $data[134];
	$TSVET = $data[135];
	$UGLOVAYA_KONSTRUKTSIYA = $data[136];
	$SHIRINA_MM_ = $data[137];
	$METOD_USTANOVKI_SLIVNOGO_BACHKA = $data[138];
	$VYSOTA_MM_ = $data[139];
	$TIP_MONTAZHA = $data[140];
	$PODSVETKA_KNOPOK_INDIKATSIEY_ = $data[141];
	$GLUBINA_MM_ = $data[142];
	$DLINA_SHLANGA_SM_ = $data[143];
	$GUARANTEE = $data[144];
	$NAPRAVLENIE_VYPUSKA = $data[145];
	$POKAZAT_NA_GLAVNOY = $data[146];
	$MEKHANIZM_ = $data[147];
	$OBLAST_PRIMENENIYA = $data[148];
	$KOMPLEKTOM_DESHEVLE = $data[149];
	$RASKHOD_VODY_L_MIN_ = $data[150];
	$STRANA_PROIZVODITEL = $data[151];
	$OBEM_ML_ = $data[153];
	$TIP = $data[154];
	$REZHIM_SLIVA_VODY = $data[156];
	$RAZMER_ROZETKI_MM_ = $data[157];
	$UPRAVLENIE_ = $data[158];
	$PODVOD_VODY_V_BACHOK = $data[159];
	$MEKHANIZM_SLIVA = $data[160];
	$POVERKHNOST_ = $data[162];
	$OBEM_SMYVN_BACHKA_L = $data[163];
	$ZASHCHITA_OT_VODYANYKH_BRYZG = $data[164];
	$SHIRINA_SM = $data[165];
	$SISTEMA_ANTIVSPLESK = $data[166];
	$VYSOTA_SM = $data[167];
	$BEZOBODKOVYY = $data[168];
	$GLUBINA_SM = $data[169];
	$POLOCHKA_V_CHASHE = $data[170];
	$FURNITURA = $data[171];
	$KREPLENIE = $data[172];
	$VYSOTA_CHASHI_SM = $data[173];
	$KRYSHKA_SIDENE = $data[174];
	$SIDENE_V_KOMPLEKTE = $data[175];
	$V_SOCHETANII_TOLKO_S_SENSOWASH_ = $data[176];
	$BYSTROSYEMNYY_MEKHANIZM = $data[177];
	$TEMPERATURA_SIDENYA_S = $data[178];
	$SENSOR_DLYA_OBNARUZHENIYA_CHELOVEKA = $data[179];
	$ANTIBAKTERIALNYE_SVOYSTVA_MATERIALOV_SIDENYA_I_ST = $data[180];
	$DUSH_DLYA_DAM = $data[181];
	$TEMPERATURA_PRI_EKSPLUATATSII_C_ = $data[182];
	$SKRYTYY_PODVOD_VODY_ELEKTROPITANIYA = $data[183];
	$MAKS_MOSHCHNOST_VT = $data[184];
	$VOZMOZHNOST_POLNOGO_SNYATIYA_SIDENYA_DLYA_DIZINFEK = $data[185];
	$KOMFORTYNY_DUSH_ = $data[186];
	$VES_KG = $data[187];
	$PROGRAMMIREMYE_PROFILI_POLZOVATELYA_ = $data[189];
	$SIDENE_I_KRYSHKA_LEGKO_SNIMAYUTSYA_ODNOY_RUKOY = $data[190];
	$FUNKTSIYA_NOCHNOY_PODSVETKI_ = $data[191];
	$NOMINALNOE_NAPRYAZHENIE_V = $data[192];
	$TEMPERATURA_FENA_S = $data[193];
	$AVTOMATICHESKIY_DRENAZH_VODY_PRI_DLITELNOM_NEISPOL = $data[194];
	$BESSHUMNOE_OPUSKANIE_SIDENYA_I_KRYSHKI_ = $data[195];
	$MAKS_ZHESTKOST_VODY_MMOL_L = $data[196];
	$PULT_DISTANTSIONNOGO_UPRAVLENIYA_PDU = $data[197];
	$TEMPERATURA_VODY_C_ = $data[198];
	$CHASTOTA_GTS = $data[199];
	$ELEKTROPRIVODNOE_SIDENE_I_KRYSHKA = $data[200];
	$DUSH_DLYA_YAGODITS = $data[201];
	$NAPOR_VODY_MPA = $data[202];
	$MATERIAL_KORPUSA = $data[203];
	$REGULIRUEMAYA_MOSHCHNOST_VODNOY_STRUI_ = $data[204];
	$MONTAZH = $data[205];
	$PODKHODIT_TOLKO_DLYA_UNITAZOV_ = $data[206];
	$SISTEMA_KHRANENIYA = $data[207];
	$BLOKIROVKA_UPRAVLENIYA_FUNKTSIYAMI_S_POMOSHCHYU_PD = $data[208];
	$MATERIAL_FASADA = $data[209];
	$NEZAMEDLITELNYY_PODOGREV_VODY_DLYA_DUSHA = $data[210];
	$AVTOMATICHESKOE_OCHISHCHENIE_DUSHEVOGO_STERZHNYA_I = $data[211];
	$REZHIM_EKONOMII_ENERGII_ = $data[212];
	$FORSUNKA_SNIMAETSYA_DLYA_MYTYA_I_ZAMENY = $data[213];
	$REGULIRUEMAYA_TEMPERATURA_VODY_ = $data[214];
	$REGULIRUEMAYA_TEMPERATURA_SIDENYA = $data[215];
	$FUNKTSIYA_MASSAZHA_S_PULSATSIEY_ = $data[216];
	$REGULIRUEMOE_POLOZHENIE_DUSHEVOGO_STERZHNYA = $data[217];
	$REGULIRUEMAYA_TEMPERATURA_FENA_ = $data[218];
	
	$newProduct = false;
	
	//Ищем товар
	$productId = null;
	$ob = CIBlockElement::GetList(
        array(),
        array(
            'IBLOCK_ID' => $IBLOCK_ID,
            '=CODE' => $url,
        ),
        false,
        false,
        array(
            'ID',
            'IBLOCK_ID'
        )
    );
    if ($item = $ob->Fetch())
    {
        $productId = $item['ID'];
    }
    else
    {
		continue;
		//Раздел
		if(!empty($arCat[0])){
			$db_list = CIBlockSection::GetList(Array(), array("=NAME" => $arCat[0], "IBLOCK_ID" => $IBLOCK_ID), false, array("ID", "IBLOCK_ID"), array());
			if($arSecItem = $db_list->GetNext()){
				$SECTION_ID = $arSecItem["ID"];
			}
			else{
				$bs = new CIBlockSection;
				$arFields = Array(
					"ACTIVE" => "Y",
					"IBLOCK_ID" => $IBLOCK_ID,
					"NAME" => $arCat[0],
					"CODE" => CUtil::translit($arCat[0], "ru" , $params),
				);
				$SECTION_ID = $bs->Add($arFields);
			}
		}
		//Подраздел
		if(!empty($arCat[1])){
			$db_list = CIBlockSection::GetList(Array(), array("=NAME" => $arCat[1], "IBLOCK_ID" => $IBLOCK_ID, "SECTION_ID" => $SECTION_ID), false, array("ID", "IBLOCK_ID"), array());
			if($arSecItem = $db_list->GetNext()){
				$SECTION_ID = $arSecItem["ID"];
			}
			else{
				$bs = new CIBlockSection;
				$arFields = Array(
					"ACTIVE" => "Y",
					"IBLOCK_ID" => $IBLOCK_ID,
					"IBLOCK_SECTION_ID" => $SECTION_ID,
					"NAME" => $arCat[1],
					"CODE" => CUtil::translit($arCat[1], "ru" , $params),
				);
				$SECTION_ID = $bs->Add($arFields);
			}
		}
		
		//Создаем элемент инфоблока
		$el = new CIBlockElement;
		$arLoadProductArray = Array(
			"IBLOCK_ID" => $IBLOCK_ID,
			"IBLOCK_SECTION_ID" => $SECTION_ID,
			"NAME" => $name,
			"ACTIVE" => $active,
			"CODE" => $url,
			"PREVIEW_PICTURE" => CFile::MakeFileArray($img),
			"DETAIL_PICTURE" => CFile::MakeFileArray($img),
			"PROPERTY_VALUES" => array(
				"ARTNUMBER" => $number
			),
		);
		$productId = $el->Add($arLoadProductArray);

		if (!$productId)
		{
			file_put_contents(
				__DIR__.'/error.log', 
				print_r($arLoadProductArray, true), 
				FILE_APPEND
			);
			continue;
		}
		
		$newProduct = true;

		//Создаем товар
		$arFields = array("ID" => $productId);
		CCatalogProduct::Add($arFields);

		//Цена и колличество
		$ob = \Bitrix\Catalog\Model\Price::getList(array(
			'select' => array(
				'ID'
			),
			'filter' => array(
				'PRODUCT_ID' => $productId,
				'CATALOG_GROUP_ID' => $PRICE_ID,
				'CURRENCY' => 'RUB'
			)
		));
		if ($item = $ob->fetch())
		{
			\Bitrix\Catalog\Model\Price::update(
				$item['ID'],
				array(
					'PRICE' => $price,
				)
			);
		}
		else
		{
			\Bitrix\Catalog\Model\Price::add(array(
				'PRODUCT_ID' => $productId,
				'CATALOG_GROUP_ID' => $PRICE_ID,
				'CURRENCY' => 'RUB',
				'PRICE' => $price,
			));
		}

		CCatalogProduct::Update($productId, Array("QUANTITY"=>10)); 

		//Скидка
	}

	//Обновление свойств
	$arProps = array();
	
	//Для всех товаров обновляем свойства
	__SetListPropertyValue($arProps, 'TSVET', 3709, $TSVET);
	__SetListPropertyValue($arProps, 'MATERIAL', 3710, $MATERIAL);
	__SetListPropertyValue($arProps, 'VARIANT_USTANOVKI', 3743, $VARIANT_USTANOVKI);
	__SetListPropertyValue($arProps, 'KOMPLEKTOM_DESHEVLE', 3788, $KOMPLEKTOM_DESHEVLE);
	__SetListPropertyValue($arProps, 'REGULIROVKA_POLOZHENIYA_DVERTSY_', 3807, $REGULIROVKA_POLOZHENIYA_DVERTSY_);
	__SetListPropertyValue($arProps, 'FAKTURA_', 3809, $FAKTURA_);
	__SetListPropertyValue($arProps, 'DOPOLNITELNYE_FUNKTSII_', 3819, $DOPOLNITELNYE_FUNKTSII_);
	
	if($newProduct){
		//Доп. картинки
		/*
		foreach($arAddImgs as $addImg){
			$img = $pathAddImg.$addImg;
			$arProps["MORE_PHOTO"][] = CFile::MakeFileArray($img);
		}
		*/
		//Лейблы
		__SetListPropertyValue($arProps, "NEWPRODUCT", 44, $new);
		__SetListPropertyValue($arProps, "SALELEADER", 45, $hit);
		__SetListPropertyValue($arProps, "DISCOUNT", 46, $sale);

		//Основные свойства
		$arProps["ARTNUMBER"] = $number;
		//Бренд
		if (empty($brand))
		{
			$arProps["MANUFACTURER"] = false;
		}
		else
		{
			$brandId = null;
			$ob = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => 13,
					"=NAME" => $brand,
				)
			);
			if ($item = $ob->Fetch())
			{
				$brandId = $item["ID"];
			}
			else{
				$arLoadBrandArray = Array(
					"IBLOCK_ID" => 13,
					"NAME" => $brand,
					"ACTIVE" => "Y",
					"CODE" => CUtil::translit($brand, "ru" , $params),
				);
				$brandId = $el->Add($arLoadBrandArray);
			}

			if (!!$brandId) 
			{
				$arProps["MANUFACTURER"] = $brandId;
			}
		}

		//Коллекция
		if (empty($collection))
		{
			$arProps["SERIES"] = false;
		}
		else
		{
			$collectionId = null;
			$ob = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => 22,
					"=NAME" => $collection,
				)
			);
			if ($item = $ob->Fetch())
			{
				$collectionId = $item["ID"];
			}
			else{
				$arLoadCollectionArray = Array(
					"IBLOCK_ID" => 22,
					"NAME" => $collection,
					"ACTIVE" => "Y",
					"CODE" => CUtil::translit($collection, "ru" , $params),
				);
				$collectionId = $el->Add($arLoadCollectionArray);
			}

			if (!!$collectionId) 
			{
				$arProps["SERIES"] = $collectionId;
			}
		}

		__SetListPropertyValue($arProps, "COUNTRY", 57, $country);
		__SetListPropertyValue($arProps, "GUARANTEE", 64, $garantia);
		
		//Другие свойства
		$arProps['DLINA_MM_'] = $DLINA_MM_;
		__SetListPropertyValue($arProps, 'PROIZVODSTVO_', 3897, $PROIZVODSTVO_);
		$arProps['DIAMETR_VOZDUKHOVODA_MM_'] = $DIAMETR_VOZDUKHOVODA_MM_;
		__SetListPropertyValue($arProps, 'NAPRYAZHENIE_PITANIYA_12_V_', 3895, $NAPRYAZHENIE_PITANIYA_12_V_);
		__SetListPropertyValue($arProps, 'PRISOEDINITELNYY_DIAMETR_MM_', 3894, $PRISOEDINITELNYY_DIAMETR_MM_);
		__SetListPropertyValue($arProps, 'SVETOVOY_INDIKATOR_', 3893, $SVETOVOY_INDIKATOR_);
		__SetListPropertyValue($arProps, 'TIP_ELEKTRODVIGATELYA_', 3892, $TIP_ELEKTRODVIGATELYA_);
		__SetListPropertyValue($arProps, 'KLASS_ZASHCHITY_IP_', 3891, $KLASS_ZASHCHITY_IP_);
		__SetListPropertyValue($arProps, 'DATCHIK_DVIZHENIYA_', 3890, $DATCHIK_DVIZHENIYA_);
		__SetListPropertyValue($arProps, 'REGULIRUEMYY_GIGROSTAT_', 3889, $REGULIRUEMYY_GIGROSTAT_);
		__SetListPropertyValue($arProps, 'REGULIRUEMYY_TAYMER_', 3888, $REGULIRUEMYY_TAYMER_);
		__SetListPropertyValue($arProps, 'MAKS_RASKHOD_VOZDUKHA_M3_CH_', 3887, $MAKS_RASKHOD_VOZDUKHA_M3_CH_);
		__SetListPropertyValue($arProps, 'UROVEN_ZVUKOVOGO_DAVLENIYA_DB_A_', 3886, $UROVEN_ZVUKOVOGO_DAVLENIYA_DB_A_);
		__SetListPropertyValue($arProps, 'AVTOMATICHESKIY_TAYMER_', 3885, $AVTOMATICHESKIY_TAYMER_);
		__SetListPropertyValue($arProps, 'OBRATNYY_KLAPAN_', 3884, $OBRATNYY_KLAPAN_);
		__SetListPropertyValue($arProps, 'SHNUROVOY_VYKLYUCHATEL_', 3883, $SHNUROVOY_VYKLYUCHATEL_);
		__SetListPropertyValue($arProps, 'SHARIKOVYE_PODSHIPNIKI_', 3882, $SHARIKOVYE_PODSHIPNIKI_);
		__SetListPropertyValue($arProps, 'CHASTOTA_VRASHCHENIYA_OB_MIN_', 3881, $CHASTOTA_VRASHCHENIYA_OB_MIN_);
		__SetListPropertyValue($arProps, 'KLASS_IZOLYATSII_DVIGATELYA_', 3880, $KLASS_IZOLYATSII_DVIGATELYA_);
		$arProps['NAPRYAZHENIE_PITANIYA_V_'] = $NAPRYAZHENIE_PITANIYA_V_;
		$arProps['POTREBLYAEMAYA_MOSHCHNOST_KVT_'] = $POTREBLYAEMAYA_MOSHCHNOST_KVT_;
		__SetListPropertyValue($arProps, 'METOD_KREPLENIYA', 3877, $METOD_KREPLENIYA);
		__SetListPropertyValue($arProps, 'RAZMER_UPAKOVKI', 3876, $RAZMER_UPAKOVKI);
		__SetListPropertyValue($arProps, 'SOVMESTIM_S_LYUBYM_PODVESNYM_UNITAZOM', 3875, $SOVMESTIM_S_LYUBYM_PODVESNYM_UNITAZOM);
		$arProps['ZAVODSKAYA_NASTROYKA_SMYVA_L'] = $ZAVODSKAYA_NASTROYKA_SMYVA_L;
		__SetListPropertyValue($arProps, 'TIP_UPRAVLENIYA', 3873, $TIP_UPRAVLENIYA);
		__SetListPropertyValue($arProps, 'REGULIROVKA_PO_VYSOTE_MM', 3871, $REGULIROVKA_PO_VYSOTE_MM);
		$arProps['MAKSIMALNOE_DAVLENIE_BAR'] = $MAKSIMALNOE_DAVLENIE_BAR;
		__SetListPropertyValue($arProps, 'DLYA_KLAVISH', 3869, $DLYA_KLAVISH);
		$arProps['MINIMALNOE_DAVLENIE_BAR'] = $MINIMALNOE_DAVLENIE_BAR;
		__SetListPropertyValue($arProps, 'TIP_INSTALLYATSII', 3867, $TIP_INSTALLYATSII);
		__SetListPropertyValue($arProps, 'KLAVISHA', 3866, $KLAVISHA);
		__SetListPropertyValue($arProps, 'SSYLKA_NA_DOP_TOVARY', 3865, $SSYLKA_NA_DOP_TOVARY);
		__SetListPropertyValue($arProps, 'KOLLEKTSIYA_INSTALYATSII', 3864, $KOLLEKTSIYA_INSTALYATSII);
		__SetListPropertyValue($arProps, 'TSVET_KLAVISHI', 3863, $TSVET_KLAVISHI);
		__SetListPropertyValue($arProps, 'POVOROTNYY', 3855, $POVOROTNYY);
		__SetListPropertyValue($arProps, 'FUNKTSIYA_OBOGREVA_POMESHCHENIYA', 3854, $FUNKTSIYA_OBOGREVA_POMESHCHENIYA);
		$arProps['SHIRINA_SM'] = $SHIRINA_SM;
		$arProps['MOSHCHNOST_VT'] = $MOSHCHNOST_VT;
		$arProps['NAPRYAZHENIE_V'] = $NAPRYAZHENIE_V;
		__SetListPropertyValue($arProps, 'STANDART_PODVODKI_', 3821, $STANDART_PODVODKI_);
		__SetListPropertyValue($arProps, 'FUNKTSIYA_EKONOMII_RASKHODA_', 3820, $FUNKTSIYA_EKONOMII_RASKHODA_);
		__SetListPropertyValue($arProps, 'DOPOLNITELNYE_FUNKTSII_', 3819, $DOPOLNITELNYE_FUNKTSII_);
		__SetListPropertyValue($arProps, 'VRASHCHENIE_IZLIVA_', 3818, $VRASHCHENIE_IZLIVA_);
		__SetListPropertyValue($arProps, 'ZASHCHITA_OT_OBRATNOGO_POTOKA_', 3817, $ZASHCHITA_OT_OBRATNOGO_POTOKA_);
		__SetListPropertyValue($arProps, 'DONNYY_KLAPAN_', 3816, $DONNYY_KLAPAN_);
		__SetListPropertyValue($arProps, 'OGRANICHENIE_TEMPERATURY_', 3815, $OGRANICHENIE_TEMPERATURY_);
		__SetListPropertyValue($arProps, 'TIP_PODVODKI_', 3814, $TIP_PODVODKI_);
		$arProps['VYSOTA_IZLIVA_SM_'] = $VYSOTA_IZLIVA_SM_;
		$arProps['DLINA_IZLIVA_SM_'] = $DLINA_IZLIVA_SM_;
		__SetListPropertyValue($arProps, 'FAKTURA_', 3809, $FAKTURA_);
		__SetListPropertyValue($arProps, 'TIP_PRODUKTA_', 3793, $TIP_PRODUKTA_);
		__SetListPropertyValue($arProps, 'TEKHNOLOGII', 3862, $TEKHNOLOGII);
		__SetListPropertyValue($arProps, 'TIP_ZAMKA_', 3808, $TIP_ZAMKA_);
		__SetListPropertyValue($arProps, 'REGULIROVKA_POLOZHENIYA_DVERTSY_', 3807, $REGULIROVKA_POLOZHENIYA_DVERTSY_);
		__SetListPropertyValue($arProps, 'TERMOREGULYATOR', 3844, $TERMOREGULYATOR);
		__SetListPropertyValue($arProps, 'REGULIROVKA_PRODOLZHITELNOSTI_SMYVA', 3860, $REGULIROVKA_PRODOLZHITELNOSTI_SMYVA);
		__SetListPropertyValue($arProps, 'STILISTIKA_DIZAYNA', 3733, $STILISTIKA_DIZAYNA);
		__SetListPropertyValue($arProps, 'DLYA_USTANOVKI_V_', 3806, $DLYA_USTANOVKI_V_);
		__SetListPropertyValue($arProps, 'AVTOOTKLYUCHENIE_PRI_NAGREVE', 3843, $AVTOOTKLYUCHENIE_PRI_NAGREVE);
		__SetListPropertyValue($arProps, 'REGULIROVKA_GLUBINY_MONTAZHA', 3859, $REGULIROVKA_GLUBINY_MONTAZHA);
		__SetListPropertyValue($arProps, 'SORTIROVKA_DOP_TOVAROV', 3749, $SORTIROVKA_DOP_TOVAROV);
		__SetListPropertyValue($arProps, 'SPOSOB_OTKRYVANIYA_', 3805, $SPOSOB_OTKRYVANIYA_);
		__SetListPropertyValue($arProps, 'ELEKTROVYKLYUCHATEL_', 3842, $ELEKTROVYKLYUCHATEL_);
		__SetListPropertyValue($arProps, 'PANEL_SMYVA_V_KOMPLEKTE', 3858, $PANEL_SMYVA_V_KOMPLEKTE);
		__SetListPropertyValue($arProps, 'FORMA', 3726, $FORMA);
		__SetListPropertyValue($arProps, 'ZAPOLNENIE_DVERTSY_', 3803, $ZAPOLNENIE_DVERTSY_);
		__SetListPropertyValue($arProps, 'TEPLONOSITEL', 3841, $TEPLONOSITEL);
		__SetListPropertyValue($arProps, 'ZVUKOIZOLIRUYUSHCHAYA_PROKLADKA_V_KOMPLEKTE', 3857, $ZVUKOIZOLIRUYUSHCHAYA_PROKLADKA_V_KOMPLEKTE);
		__SetListPropertyValue($arProps, 'KONSTRUKTSIYA_DVEREY_', 3802, $KONSTRUKTSIYA_DVEREY_);
		$arProps['VREMYA_NAGREVA_MIN'] = $VREMYA_NAGREVA_MIN;
		__SetListPropertyValue($arProps, 'KREPLENIE_K_STENE_V_KOMPLEKTE', 3856, $KREPLENIE_K_STENE_V_KOMPLEKTE);
		__SetListPropertyValue($arProps, 'MATERIAL', 3710, $MATERIAL);
		__SetListPropertyValue($arProps, 'REGULIRUEMYE_PETLI_', 3800, $REGULIRUEMYE_PETLI_);
		$arProps['RABOCHEE_DAVLENIE_BAR'] = $RABOCHEE_DAVLENIE_BAR;
		$arProps['DIAMETR_SLIVA_SM'] = $DIAMETR_SLIVA_SM;
		$arProps['OBYEM_L'] = $OBYEM_L;
		__SetListPropertyValue($arProps, 'SKRYTYY_', 3804, $SKRYTYY_);
		__SetListPropertyValue($arProps, 'NAPRAVLENIE_PODKLYUCHENIYA', 3837, $NAPRAVLENIE_PODKLYUCHENIYA);
		$arProps['DIAMETR_PEREKHODNIKA_DLYA_SLIVA_SM'] = $DIAMETR_PEREKHODNIKA_DLYA_SLIVA_SM;
		__SetListPropertyValue($arProps, 'VID_USTANOVKI', 3725, $VID_USTANOVKI);
		__SetListPropertyValue($arProps, 'USILENNYY_', 3801, $USILENNYY_);
		$arProps['MEZHOSEVOE_RASSTOYANIE_SM'] = $MEZHOSEVOE_RASSTOYANIE_SM;
		$arProps['MEZHOSEVOE_RASSTOYANIE_POD_KREPEZH_SHPILKI_SM'] = $MEZHOSEVOE_RASSTOYANIE_POD_KREPEZH_SHPILKI_SM;
		__SetListPropertyValue($arProps, 'ANTISKOLZYASHCHEE_POKRYTIE', 3728, $ANTISKOLZYASHCHEE_POKRYTIE);
		__SetListPropertyValue($arProps, 'PROTIVOPOZHARNYY_', 3799, $PROTIVOPOZHARNYY_);
		$arProps['MONTAZHNAYA_GLUBINA_SM'] = $MONTAZHNAYA_GLUBINA_SM;
		$arProps['DLINA_SM'] = $DLINA_SM;
		__SetListPropertyValue($arProps, 'VODONEPRONITSAEMOST_', 3798, $VODONEPRONITSAEMOST_);
		__SetListPropertyValue($arProps, 'KOLICHESTVO_SEKTSIY', 3834, $KOLICHESTVO_SEKTSIY);
		$arProps['MONTAZHNAYA_VYSOTA_SM'] = $MONTAZHNAYA_VYSOTA_SM;
		__SetListPropertyValue($arProps, 'VARIANT_USTANOVKI', 3743, $VARIANT_USTANOVKI);
		__SetListPropertyValue($arProps, 'PYLEIZOLYATSIYA_', 3797, $PYLEIZOLYATSIYA_);
		$arProps['TIPORAZMER_SHIRINA_SM'] = $TIPORAZMER_SHIRINA_SM;
		__SetListPropertyValue($arProps, 'SHUMOIZOLYATSIYA_', 3796, $SHUMOIZOLYATSIYA_);
		__SetListPropertyValue($arProps, 'REZHIM_SLIVA_VODY', 3736, $REZHIM_SLIVA_VODY);
		__SetListPropertyValue($arProps, 'NAMECHENNYKH_OTVERSTIY_DLYA_SMESITELYA', 3718, $NAMECHENNYKH_OTVERSTIY_DLYA_SMESITELYA);
		$arProps['RAZMER_DVERTSY_SH_V_MM_'] = $RAZMER_DVERTSY_SH_V_MM_;
		__SetListPropertyValue($arProps, 'NAZNACHENIE', 3839, $NAZNACHENIE);
		__SetListPropertyValue($arProps, 'GOTOVYKH_OTVERSTIY_DLYA_SMESITELYA', 3717, $GOTOVYKH_OTVERSTIY_DLYA_SMESITELYA);
		$arProps['NAGRUZKA_NA_DVERTSU_KG_'] = $NAGRUZKA_NA_DVERTSU_KG_;
		__SetListPropertyValue($arProps, 'OSNASHCHENIE_', 3813, $OSNASHCHENIE_);
		__SetListPropertyValue($arProps, 'PODKLYUCHENIE', 3830, $PODKLYUCHENIE);
		__SetListPropertyValue($arProps, 'TSVET', 3709, $TSVET);
		__SetListPropertyValue($arProps, 'UGLOVAYA_KONSTRUKTSIYA', 3742, $UGLOVAYA_KONSTRUKTSIYA);
		$arProps['SHIRINA_MM_'] = $SHIRINA_MM_;
		__SetListPropertyValue($arProps, 'METOD_USTANOVKI_SLIVNOGO_BACHKA', 3735, $METOD_USTANOVKI_SLIVNOGO_BACHKA);
		$arProps['VYSOTA_MM_'] = $VYSOTA_MM_;
		__SetListPropertyValue($arProps, 'TIP_MONTAZHA', 3835, $TIP_MONTAZHA);
		__SetListPropertyValue($arProps, 'PODSVETKA_KNOPOK_INDIKATSIEY_', 3773, $PODSVETKA_KNOPOK_INDIKATSIEY_);
		$arProps['GLUBINA_MM_'] = $GLUBINA_MM_;
		$arProps['DLINA_SHLANGA_SM_'] = $DLINA_SHLANGA_SM_;
		__SetListPropertyValue($arProps, 'NAPRAVLENIE_VYPUSKA', 3721, $NAPRAVLENIE_VYPUSKA);
		__SetListPropertyValue($arProps, 'POKAZAT_NA_GLAVNOY', 3789, $POKAZAT_NA_GLAVNOY);
		__SetListPropertyValue($arProps, 'MEKHANIZM_', 3826, $MEKHANIZM_);
		__SetListPropertyValue($arProps, 'OBLAST_PRIMENENIYA', 3832, $OBLAST_PRIMENENIYA);
		__SetListPropertyValue($arProps, 'KOMPLEKTOM_DESHEVLE', 3788, $KOMPLEKTOM_DESHEVLE);
		$arProps['RASKHOD_VODY_L_MIN_'] = $RASKHOD_VODY_L_MIN_;
		__SetListPropertyValue($arProps, 'STRANA_PROIZVODITEL', 3831, $STRANA_PROIZVODITEL);
		$arProps['OBEM_ML_'] = $OBEM_ML_;
		__SetListPropertyValue($arProps, 'TIP', 3829, $TIP);
		__SetListPropertyValue($arProps, 'REZHIM_SLIVA_VODY', 3736, $REZHIM_SLIVA_VODY);
		__SetListPropertyValue($arProps, 'RAZMER_ROZETKI_MM_', 3823, $RAZMER_ROZETKI_MM_);
		__SetListPropertyValue($arProps, 'UPRAVLENIE_', 3828, $UPRAVLENIE_);
		__SetListPropertyValue($arProps, 'PODVOD_VODY_V_BACHOK', 3724, $PODVOD_VODY_V_BACHOK);
		__SetListPropertyValue($arProps, 'MEKHANIZM_SLIVA', 3737, $MEKHANIZM_SLIVA);
		__SetListPropertyValue($arProps, 'POVERKHNOST_', 3822, $POVERKHNOST_);
		__SetListPropertyValue($arProps, 'OBEM_SMYVN_BACHKA_L', 3723, $OBEM_SMYVN_BACHKA_L);
		__SetListPropertyValue($arProps, 'ZASHCHITA_OT_VODYANYKH_BRYZG', 3781, $ZASHCHITA_OT_VODYANYKH_BRYZG);
		$arProps['SHIRINA_SM'] = $SHIRINA_SM;
		__SetListPropertyValue($arProps, 'SISTEMA_ANTIVSPLESK', 3719, $SISTEMA_ANTIVSPLESK);
		$arProps['VYSOTA_SM'] = $VYSOTA_SM;
		__SetListPropertyValue($arProps, 'BEZOBODKOVYY', 3734, $BEZOBODKOVYY);
		$arProps['GLUBINA_SM'] = $GLUBINA_SM;
		__SetListPropertyValue($arProps, 'POLOCHKA_V_CHASHE', 3720, $POLOCHKA_V_CHASHE);
		__SetListPropertyValue($arProps, 'FURNITURA', 3738, $FURNITURA);
		__SetListPropertyValue($arProps, 'KREPLENIE', 3716, $KREPLENIE);
		$arProps['VYSOTA_CHASHI_SM'] = $VYSOTA_CHASHI_SM;
		__SetListPropertyValue($arProps, 'KRYSHKA_SIDENE', 3740, $KRYSHKA_SIDENE);
		__SetListPropertyValue($arProps, 'SIDENE_V_KOMPLEKTE', 3739, $SIDENE_V_KOMPLEKTE);
		__SetListPropertyValue($arProps, 'V_SOCHETANII_TOLKO_S_SENSOWASH_', 3787, $V_SOCHETANII_TOLKO_S_SENSOWASH_);
		__SetListPropertyValue($arProps, 'BYSTROSYEMNYY_MEKHANIZM', 3746, $BYSTROSYEMNYY_MEKHANIZM);
		$arProps['TEMPERATURA_SIDENYA_S'] = $TEMPERATURA_SIDENYA_S;
		__SetListPropertyValue($arProps, 'SENSOR_DLYA_OBNARUZHENIYA_CHELOVEKA', 3753, $SENSOR_DLYA_OBNARUZHENIYA_CHELOVEKA);
		__SetListPropertyValue($arProps, 'ANTIBAKTERIALNYE_SVOYSTVA_MATERIALOV_SIDENYA_I_ST', 3756, $ANTIBAKTERIALNYE_SVOYSTVA_MATERIALOV_SIDENYA_I_ST);
		__SetListPropertyValue($arProps, 'DUSH_DLYA_DAM', 3762, $DUSH_DLYA_DAM);
		$arProps['TEMPERATURA_PRI_EKSPLUATATSII_C_'] = $TEMPERATURA_PRI_EKSPLUATATSII_C_;
		__SetListPropertyValue($arProps, 'SKRYTYY_PODVOD_VODY_ELEKTROPITANIYA', 3754, $SKRYTYY_PODVOD_VODY_ELEKTROPITANIYA);
		$arProps['MAKS_MOSHCHNOST_VT'] = $MAKS_MOSHCHNOST_VT;
		__SetListPropertyValue($arProps, 'VOZMOZHNOST_POLNOGO_SNYATIYA_SIDENYA_DLYA_DIZINFEK', 3758, $VOZMOZHNOST_POLNOGO_SNYATIYA_SIDENYA_DLYA_DIZINFEK);
		__SetListPropertyValue($arProps, 'KOMFORTYNY_DUSH_', 3763, $KOMFORTYNY_DUSH_);
		$arProps['VES_KG'] = $VES_KG;
		__SetListPropertyValue($arProps, 'PROGRAMMIREMYE_PROFILI_POLZOVATELYA_', 3769, $PROGRAMMIREMYE_PROFILI_POLZOVATELYA_);
		__SetListPropertyValue($arProps, 'SIDENE_I_KRYSHKA_LEGKO_SNIMAYUTSYA_ODNOY_RUKOY', 3759, $SIDENE_I_KRYSHKA_LEGKO_SNIMAYUTSYA_ODNOY_RUKOY);
		__SetListPropertyValue($arProps, 'FUNKTSIYA_NOCHNOY_PODSVETKI_', 3770, $FUNKTSIYA_NOCHNOY_PODSVETKI_);
		$arProps['NOMINALNOE_NAPRYAZHENIE_V'] = $NOMINALNOE_NAPRYAZHENIE_V;
		$arProps['TEMPERATURA_FENA_S'] = $TEMPERATURA_FENA_S;
		__SetListPropertyValue($arProps, 'AVTOMATICHESKIY_DRENAZH_VODY_PRI_DLITELNOM_NEISPOL', 3760, $AVTOMATICHESKIY_DRENAZH_VODY_PRI_DLITELNOM_NEISPOL);
		__SetListPropertyValue($arProps, 'BESSHUMNOE_OPUSKANIE_SIDENYA_I_KRYSHKI_', 3750, $BESSHUMNOE_OPUSKANIE_SIDENYA_I_KRYSHKI_);
		$arProps['MAKS_ZHESTKOST_VODY_MMOL_L'] = $MAKS_ZHESTKOST_VODY_MMOL_L;
		__SetListPropertyValue($arProps, 'PULT_DISTANTSIONNOGO_UPRAVLENIYA_PDU', 3771, $PULT_DISTANTSIONNOGO_UPRAVLENIYA_PDU);
		$arProps['TEMPERATURA_VODY_C_'] = $TEMPERATURA_VODY_C_;
		$arProps['CHASTOTA_GTS'] = $CHASTOTA_GTS;
		__SetListPropertyValue($arProps, 'ELEKTROPRIVODNOE_SIDENE_I_KRYSHKA', 3751, $ELEKTROPRIVODNOE_SIDENE_I_KRYSHKA);
		__SetListPropertyValue($arProps, 'DUSH_DLYA_YAGODITS', 3761, $DUSH_DLYA_YAGODITS);
		$arProps['NAPOR_VODY_MPA'] = $NAPOR_VODY_MPA;
		__SetListPropertyValue($arProps, 'MATERIAL_KORPUSA', 3741, $MATERIAL_KORPUSA);
		__SetListPropertyValue($arProps, 'REGULIRUEMAYA_MOSHCHNOST_VODNOY_STRUI_', 3766, $REGULIRUEMAYA_MOSHCHNOST_VODNOY_STRUI_);
		__SetListPropertyValue($arProps, 'MONTAZH', 3729, $MONTAZH);
		__SetListPropertyValue($arProps, 'PODKHODIT_TOLKO_DLYA_UNITAZOV_', 3786, $PODKHODIT_TOLKO_DLYA_UNITAZOV_);
		__SetListPropertyValue($arProps, 'SISTEMA_KHRANENIYA', 3730, $SISTEMA_KHRANENIYA);
		__SetListPropertyValue($arProps, 'BLOKIROVKA_UPRAVLENIYA_FUNKTSIYAMI_S_POMOSHCHYU_PD', 3772, $BLOKIROVKA_UPRAVLENIYA_FUNKTSIYAMI_S_POMOSHCHYU_PD);
		__SetListPropertyValue($arProps, 'MATERIAL_FASADA', 3731, $MATERIAL_FASADA);
		__SetListPropertyValue($arProps, 'NEZAMEDLITELNYY_PODOGREV_VODY_DLYA_DUSHA', 3774, $NEZAMEDLITELNYY_PODOGREV_VODY_DLYA_DUSHA);
		__SetListPropertyValue($arProps, 'AVTOMATICHESKOE_OCHISHCHENIE_DUSHEVOGO_STERZHNYA_I', 3755, $AVTOMATICHESKOE_OCHISHCHENIE_DUSHEVOGO_STERZHNYA_I);
		__SetListPropertyValue($arProps, 'REZHIM_EKONOMII_ENERGII_', 3775, $REZHIM_EKONOMII_ENERGII_);
		__SetListPropertyValue($arProps, 'FORSUNKA_SNIMAETSYA_DLYA_MYTYA_I_ZAMENY', 3757, $FORSUNKA_SNIMAETSYA_DLYA_MYTYA_I_ZAMENY);
		__SetListPropertyValue($arProps, 'REGULIRUEMAYA_TEMPERATURA_VODY_', 3767, $REGULIRUEMAYA_TEMPERATURA_VODY_);
		__SetListPropertyValue($arProps, 'REGULIRUEMAYA_TEMPERATURA_SIDENYA', 3752, $REGULIRUEMAYA_TEMPERATURA_SIDENYA);
		__SetListPropertyValue($arProps, 'FUNKTSIYA_MASSAZHA_S_PULSATSIEY_', 3764, $FUNKTSIYA_MASSAZHA_S_PULSATSIEY_);
		__SetListPropertyValue($arProps, 'REGULIRUEMOE_POLOZHENIE_DUSHEVOGO_STERZHNYA', 3765, $REGULIRUEMOE_POLOZHENIE_DUSHEVOGO_STERZHNYA);
		__SetListPropertyValue($arProps, 'REGULIRUEMAYA_TEMPERATURA_FENA_', 3768, $REGULIRUEMAYA_TEMPERATURA_FENA_);
	}

	//Добавляем свойства к товару
	CIBlockElement::SetPropertyValuesEx($productId, $IBLOCK_ID, $arProps);

}
fclose($dataHandler);

/*
echo "<pre>";
print_r($arProps);
echo "</pre>";
*/
?>
<?
function __SetListPropertyValue(&$arProps, $propCode, $propId, $value)
{
	if (empty($value))
    {
        $arProps[$propCode] = false;
    }
    else
    {
		$arValues = explode("|", $value);
		
		if(count($arValues) >= 2){
			foreach($arValues as $value){
				$propValue = false;
				$ob = CIBlockPropertyEnum::GetList(
					array(),
					array(
						"IBLOCK_ID" => $IBLOCK_ID,
						"PROPERTY_ID" => $propId,
						"VALUE" => $value,
					)
				);
				if ($item = $ob->Fetch())
				{
					$propValue = $item["ID"];
				}
				else
				{
					$propValue = CIBlockPropertyEnum::Add(array(
						"PROPERTY_ID" => $propId,
						"VALUE" => $value,
					));
				}

				$arProps[$propCode][] = $propValue;
			}
		}
		else{
			$propValue = false;
			$ob = CIBlockPropertyEnum::GetList(
				array(),
				array(
					"IBLOCK_ID" => $IBLOCK_ID,
					"PROPERTY_ID" => $propId,
					"VALUE" => $value,
				)
			);
			if ($item = $ob->Fetch())
			{
				$propValue = $item["ID"];
			}
			else
			{
				$propValue = CIBlockPropertyEnum::Add(array(
					"PROPERTY_ID" => $propId,
					"VALUE" => $value,
				));
			}

			$arProps[$propCode][] = $propValue;
		}
    }
}
?>