<?php

use Bitrix\Main\Application;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Application::getInstance()->getContext()->getRequest();


if (!CModule::IncludeModule("search") || !CModule::IncludeModule("iblock"))
{
	echo json_encode(array("error" => "Ошибка"));
	die();
}

$arSections = CClass::Instance()->getCatalogSection();
$q=preg_replace('/([\d])[x,х]([\d])/ismU','$1*$2',$request["q"]);

$q=str_replace('*', '-', $q);



$arElements = [];
$arSphinx = new CSearchSphinx;
$arSphinx->connect(
	COption::GetOptionString("search", "sphinx_connection"),
	COption::GetOptionString("search", "sphinx_index_name")
);

$aSort = array("TITLE_RANK" => "DESC", "TITLE" => "DESC", "CUSTOM_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC");
$obSearch = $arSphinx->search(
	array("SITE_ID" => SITE_ID, "QUERY" => $q,"MODULE_ID" => 'iblock',"CHECK_DATES" => 'Y',"PARAM1" => CATALOG_IBLOCK_TYPE,"PARAM2" => CATALOG_IBLOCK_ID)
	, $aSort, array(), false
);

if(count($obSearch)==0)
{
	$arResult["alt_query"] = "";
	$arLang = CSearchLanguage::GuessLanguage($q);
	if(is_array($arLang) && $arLang["from"] != $arLang["to"])
	{
		$qq=str_replace('.', '', $q);
		$arResult["alt_query"] = CSearchLanguage::ConvertKeyboardLayout($qq, $arLang["from"], $arLang["to"]);
	}
	$arResult["q"] = $q;
	$arResult["phrase"] = stemming_split($q, LANGUAGE_ID);
	$q=strlen($arResult["alt_query"])>0?$arResult["alt_query"]:$q;

	$obSearch = $arSphinx->search(
	array("SITE_ID" => SITE_ID, "QUERY" => $q,"MODULE_ID" => 'iblock',"CHECK_DATES" => 'Y',"PARAM1" => CATALOG_IBLOCK_TYPE,"PARAM2" => CATALOG_IBLOCK_ID)
	, $aSort, array(), false);

}


$result = array();
$SectList=CIBlockSection::GetList(["NAME"=>"asc"],
	["GLOBAL_ACTIVE"=>"Y","ACTIVE"=>"Y","IBLOCK_ID"=>CATALOG_IBLOCK_ID,"IBLOCK_TYPE"=>CATALOG_IBLOCK_TYPE,"%NAME"=>$q],
	false,
	["PICTURE","ID","NAME","SECTION_PAGE_URL"]
);
while ($arSec = $SectList->GetNext())
{
	$arResult['SECTIONS'][]=[
			"URL"=>$arSec["SECTION_PAGE_URL"],
			"TITLE"=>$arSec["NAME"],
			"IMAGE"=>"",
		];
	if (sizeof($arResult['SECTIONS']) == 3) break;
}


foreach ($obSearch as $res) {
	$itemData = CIBlockElement::GetByID($res['item'])->GetNext();
	if (!$itemData || !key_exists($itemData['IBLOCK_SECTION_ID'], $arSections))
        continue;
	$priceData = CCatalogProduct::GetOptimalPrice($res['item']);
	$sectionData[] = $itemData['IBLOCK_SECTION_ID'];
	if (!$itemData['PREVIEW_PICTURE'])
	{
		$image = CFile::GetPath($itemData["DETAIL_PICTURE"]);
	}
	else
	{
		$image = CFile::GetPath($itemData["PREVIEW_PICTURE"]);
	}
	$geo_id = $APPLICATION->get_cookie("GEOLOCATION_ID");
	$STORE_ID = 4;
	if ($geo_id == 817){ // питер
		$STORE_ID = 1;
	}

	if ($geo_id == 2201){ // питер
		$STORE_ID = 2;
	}

	$set_q = array();
	$QUANTITY = 0;
	$ar_res = CCatalogProduct::GetByIDEx($itemData["ID"]);
	$product_id = $ar_res["PRODUCT"]['ID'];
	$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
    ));
    while($arStoreProduct=$rsStoreProduct->fetch())
    {
        $QUANTITY = $arStoreProduct['AMOUNT'];
    }
	foreach($arResult["SET_ITEMS"] AS $arItem){
			$product_id = $arItem["PRODUCT"]['ID'];
			$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
		        'filter' => array('=PRODUCT_ID'=>$product_id, 'STORE.ACTIVE'=>'Y', 'STORE.ID'=>$STORE_ID),
		        'select' => array('ID', 'AMOUNT','STORE_ID','STORE_TITLE' => 'STORE.TITLE'),
		    ));
		    while($arStoreProduct=$rsStoreProduct->fetch())
		    {
		        $arItem['PRODUCT']['QUANTITY'] = $arStoreProduct['AMOUNT'];
		    }
    	$set_q[] = intval($arItem['PRODUCT']['QUANTITY']);
	}
	if (is_array($set_q) && count($set_q)>0){
		$QUANTITY = min($set_q);
	}
	$arResult['ITEMS'][] = [
		'URL' => $itemData['DETAIL_PAGE_URL'],
		'IMAGE' => $image,
		'ART' => $itemData['ID'],
		'TITLE' => $itemData['NAME'],
		'OLD_PRICE' => customFormatPrice(CurrencyFormat(
			$priceData['RESULT_PRICE']['BASE_PRICE'],
			$priceData['RESULT_PRICE']['CURRENCY']
		)),
		'NEW_PRICE' => customFormatPrice(CurrencyFormat(
			$priceData['RESULT_PRICE']['DISCOUNT_PRICE'],
			$priceData['RESULT_PRICE']['CURRENCY']
		)),
		'QUANTITY'=>$QUANTITY
	];
	
	if (sizeof($arResult['ITEMS']) == 3) break;

}

$notFound=true;

if (!empty($sectionData)) {
	$dbProductDiscounts = CCatalogDiscount::GetList(
		array("SORT" => "ASC"),
		array("ACTIVE" => "Y"),
		false,
		false,
		array("ID", "PRODUCT_ID")
	);
	while ($arProductDiscounts = $dbProductDiscounts->Fetch())
	{
		$discountElement = CIBlockElement::GetByID($arProductDiscounts['PRODUCT_ID'])->GetNext();
		if (in_array($discountElement['IBLOCK_SECTION_ID'], $sectionData)) {
			$priceData = CCatalogProduct::GetOptimalPrice($arProductDiscounts['PRODUCT_ID']);

			$arResult['DISCOUNT_ELEMENT'] = [
				'URL' => $discountElement['DETAIL_PAGE_URL'],
				'IMAGE' => CFile::GetPath($discountElement['PREVIEW_PICTURE']),
				'ART' => $discountElement['ID'],
				'TITLE' => $discountElement['NAME'],
				'OLD_PRICE' => customFormatPrice(CurrencyFormat(
					$priceData['RESULT_PRICE']['BASE_PRICE'],
					$priceData['RESULT_PRICE']['CURRENCY']
				)),
				'NEW_PRICE' => customFormatPrice(CurrencyFormat(
					$priceData['RESULT_PRICE']['DISCOUNT_PRICE'],
					$priceData['RESULT_PRICE']['CURRENCY']
				))
			];
			break;
		}
	}
}


foreach ($arResult['SECTIONS'] AS $item){
	$notFound=false;
	?>
		<div class="line" style="min-height:auto">
			<a href="<?= $item['URL'] ?>">
				<?if($item['IMAGE']!=""){?><img src="<?= $item['IMAGE'] ?>"><?}?>
				<div><?= $item['TITLE'] ?></div>
			</a>
		</div>
	<?
}?>

<?foreach ($arResult['ITEMS'] AS $item)
{
	$notFound=false;
	?>
	<div class="line">
		<a href="<?= $item['URL'] ?>">
			<img src="<?= $item['IMAGE'] ?>">
			<div><?= $item['TITLE'] ?></div>
			<div class="article">Код товара: <?= $item['ART'] ?></div>
			<?if($item['QUANTITY']>0){?>
				<?if ($item['OLD_PRICE'] != $item['NEW_PRICE']):?>
					<span class="old"><?= $item['OLD_PRICE'] ?></span>
				<?endif;?>
				<span class="new"><?= $item['NEW_PRICE'] ?></span>
			<?}?>
		</a>
	</div>
	<?}?>
	<?/*if (array_key_exists('DISCOUNT_ELEMENT', $arResult)):?>
	<div class="line big">
		<a href="<?= $arResult['DISCOUNT_ELEMENT']['URL'] ?>">
			<img src="<?= $arResult['DISCOUNT_ELEMENT']['IMAGE'] ?>">
			<div><?= $arResult['DISCOUNT_ELEMENT']['TITLE'] ?></div>
			<div class="article">Код товара: <?= $arResult['DISCOUNT_ELEMENT']['ART'] ?></div>
			<span class="old"><?= $arResult['DISCOUNT_ELEMENT']['OLD_PRICE'] ?></span>
			<span class="new"><?= $arResult['DISCOUNT_ELEMENT']['NEW_PRICE'] ?></span>
		</a>
	</div>
<?endif;*/
if($notFound){?>
	<div class="line" style="min-height:auto">
		<div class="article">
			По вашему запросу ничего не найдено.
		</div>
	</div>
<?}?>
