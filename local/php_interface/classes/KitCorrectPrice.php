<?
/**
 * 
 */
class KitCorrectPrice
{
	
	function __construct()
	{
		# code...
	}

	protected static function getGeo(){
		$geo = $_COOKIE['BITRIX_TANK_GEOLOCATION_ID'];

		if($geo == 1095){
			$CATALOG_PRICE_ID = 8;
		}
		elseif($geo == 2201){
			$CATALOG_PRICE_ID = 4;
		}
		elseif($geo == 817){
			$CATALOG_PRICE_ID = 2;
		}
		else{
			$CATALOG_PRICE_ID = 1;
		}
		return $CATALOG_PRICE_ID;
	}

	public static function run(
		$intProductID,
		$quantity,
		$arUserGroups,
		$renewal,
		$arPrices,
		$siteID,
		$arDiscountCoupons
	)
	{
		$kit = CCatalogProductSet::getAllSetsByProduct($intProductID,CCatalogProductSet::TYPE_SET);
		if($kit && !$arPrices[0]['NO_CONTINUE']){
			foreach ($kit as $key => $value) {
				$kit = $kit[$key];
				break;
			}

			if(count($arPrices) > 0){
				foreach ($arPrices as $key => $arPrice) {
					if($arPrice['CATALOG_PRICE_ID']){
						$CATALOG_PRICE_ID = $arPrice['CATALOG_PRICE_ID'];
					}
					else{
						$CATALOG_PRICE_ID = self::getGeo();
					}
				}
			}
			else{
				$CATALOG_PRICE_ID = self::getGeo();
			}

			$discount_val = 0;
			$basePrice = 0;
			foreach ($kit['ITEMS'] as $product) {
				$productId = $product['ITEM_ID'];

		        $obPrice = \Bitrix\Catalog\PriceTable::getList(array(
		            'filter' => array(
		                'PRODUCT_ID' => $productId,
		                'CATALOG_GROUP_ID' => $CATALOG_PRICE_ID,
		            ),
		            'select' => array(
		                'ID',
		                'PRICE',
		                'PRODUCT_ID',
		                'CURRENCY',
		                'CATALOG_GROUP_ID',
		            ),
		        ));

		        if($rowPrice = $obPrice->fetch()){
			        $arPrice = [
			        	[
			                'ID' => $rowPrice['ID'],
			                'PRICE' => $rowPrice['PRICE'],
			                'CURRENCY' => $rowPrice['CURRENCY'],
			                'CATALOG_GROUP_ID' => $rowPrice['CATALOG_GROUP_ID'],
			        	]
			        ];
					$basePrice += $rowPrice['PRICE'];
			    }


				$arPrice = CCatalogProduct::GetOptimalPrice($productId, $product['QUANTITY'], [], 'N', $arPrice, $siteID);
				
				$discount_val += $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
			}
	        $obPrice = \Bitrix\Catalog\PriceTable::getList(array(
	            'filter' => array(
	                'PRODUCT_ID' => $kit['ITEM_ID'],
	                'CATALOG_GROUP_ID' => $CATALOG_PRICE_ID,
	            ),
	            'select' => array(
	                'ID',
	                'CURRENCY',
	                'CATALOG_GROUP_ID',
	            ),
	        ));

	        if($rowPrice = $obPrice->fetch()){
		        $arPrice = [
		        	[
		                'ID' => $rowPrice['ID'],
		                'PRICE' => $discount_val,
		                'CURRENCY' => $rowPrice['CURRENCY'],
		                'CATALOG_GROUP_ID' => $rowPrice['CATALOG_GROUP_ID'],
		                'NO_CONTINUE' => true
		        	]
		        ];
		    }


	    	$result = CCatalogProduct::GetOptimalPrice($kit['ITEM_ID'], 2, [], 'N', $arPrice, $siteID);

			$result['PRICE']['PRICE'] = $basePrice;
			$result['RESULT_PRICE']['BASE_PRICE'] = $basePrice;
			$result['RESULT_PRICE']['DISCOUNT_PRICE'] = $discount_val;
			$result['DISCOUNT_PRICE'] = $discount_val;
			return $result;
		}
		else{
			return true;
		}
	}
}