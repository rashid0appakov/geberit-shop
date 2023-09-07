<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");

$arResult = array(
	"INITIAL" => $arResult,
	"ITEMS" => null,
	"PROMO" => null,
);

// restore tree structure
$arItems = array();
$tmp = array(0 => &$arItems);
foreach ($arResult["INITIAL"] as $arItem)
{
	$arItem["ITEMS"] = array();
	$dl = (int) $arItem["PARAMS"]["DEPTH_LEVEL"];
	$tmp[$dl] =& $tmp[$dl - 1][array_push($tmp[$dl - 1], $arItem) - 1]["ITEMS"];
}
unset($tmp);
$arResult["ITEMS"] = $arItems;





$obCache = new CPHPCache();
$cacheLifetime = 0; 
$cacheID = 'PROMO_MENU'; 
$cachePath = '/'.$cacheID;

if( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
{
   $vars = $obCache->GetVars();

   $arResult["PROMO"] = $vars;
   
}
elseif( $obCache->StartDataCache()  )
{
   
   
   
   
   // fetch promos
	$sectionIds = array(false);
	foreach ($arResult["ITEMS"] as $arItem)
	{
		$sectionId = $arItem["PARAMS"]["ID"];
		if (!!$sectionId && !in_array($sectionId, $sectionIds)) $sectionIds[] = $sectionId;
	}

	$arResult["PROMO"] = array();
	$ob = CIBlockElement::GetList(
		array(
			"SORT" => "ASC",
			"ID" => "ASC",
		),
		array(
			"IBLOCK_ID" => 33,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"PROPERTY_SECTION" => $sectionIds,
		),
		false,
		false,
		array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DETAIL_PICTURE",
			"PROPERTY_SECTION",
			"PROPERTY_URL",
			"PROPERTY_NEW_TAB",
			"PROPERTY_PRODUCT",
		)
	);
	while ($arItem = $ob->Fetch())
	{
		$id = $arItem["ID"];
		$name = $arItem["NAME"];
		$sectionId = $arItem["PROPERTY_SECTION_VALUE"];
		$newTab = $arItem["PROPERTY_NEW_TAB_VALUE"];
		$detailPicture = $arItem["DETAIL_PICTURE"];
		$url = $arItem["PROPERTY_URL_VALUE"];
		$productId = $arItem["PROPERTY_PRODUCT_VALUE"];

		if (!array_key_exists($id, $arResult["PROMO"]))
		{
			$arResult["PROMO"][$id] = array(
				"ID" => $id,
				"NAME" => $name,
				"NEW_TAB" => $newTab,

				"SECTIONS" => array(),
			);

			if (!!$detailPicture && !empty($url))
			{
				$arResult["PROMO"][$id]["BANNER"] = array(
					"IMAGE" => $detailPicture,
					"URL" => $url,
				);
			}

			if (!!$productId)
			{
				$dbProduct = CIBlockElement::GetList(
					array(),
					array(
						"ID" => $productId,
					),
					false,
					array(
						"nTopCount" => 1,
					),
					array(
						"ID",
						"NAME",
						"DETAIL_PAGE_URL",
						"DETAIL_PICTURE",
					)
				);
				if ($arProduct = $dbProduct->GetNext())
				{
					$productName = $arProduct["NAME"];
					$productUrl = $arProduct["DETAIL_PAGE_URL"];
					$productImage = $arProduct["DETAIL_PICTURE"];
		
					$arProductPrice = CCatalogProduct::GetOptimalPrice($productId);
		
					$arResult["PROMO"][$id]["PRODUCT"] = array(
						"ID" => $productId,
						"NAME" => $productName,
						"URL" => $productUrl,
						"IMAGE" => $productImage,
						"BASE_PRICE" => $arProductPrice["RESULT_PRICE"]["BASE_PRICE"],
						"DISCOUNT_PRICE" => $arProductPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]
					);
				}
			}
		}

		if (!!$sectionId && !in_array($sectionId, $arResult["PROMO"][$id]["SECTIONS"])) $arResult["PROMO"][$id]["SECTIONS"][] = $sectionId;
	}
	   
   
   
	$obCache->EndDataCache(array('arAllItemsIDs' => $arResult["PROMO"]));
}


/*
echo "<pre>";
print_r($arResult);
echo "</pre>";
*/

