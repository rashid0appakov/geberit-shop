<?
/**
 * 
 */
class customGetOptimalPrice
{
	
	function __construct()
	{
		# code...
	}

	public static function run(
		$intProductID,
		$quantity,
		$arUserGroups,
		$renewal,
		$arPrices,
		$siteID,
		$arDiscountCoupons
	) {
		$price = self::kitCorrctPrice(
			$intProductID,
			$quantity,
			$arUserGroups,
			$renewal,
			$arPrices,
			$siteID,
			$arDiscountCoupons
		);
		if($price !== true){
			return $price;
		}

		$price = self::geoCorrectPrice(
			$intProductID,
			$quantity,
			$arUserGroups,
			$renewal,
			$arPrices,
			$siteID,
			$arDiscountCoupons
		);
		if($price !== true){
			return $price;
		}
		
		return true;
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

	protected static function kitCorrctPrice(
		$intProductID,
		$quantity,
		$arUserGroups,
		$renewal,
		$arPrices,
		$siteID,
		$arDiscountCoupons
	) {
		if(!self::isRecursion($arPrices) && !$arPrices[0]['CATALOG_GROUP_ID']){

	        $obPrice = \Bitrix\Catalog\PriceTable::getList(array(
	            'filter' => array(
	                'PRODUCT_ID' => $intProductID,
	                'CATALOG_GROUP_ID' => self::getGeo(),
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
		        $arPrices = [
		        	[
		                'ID' => $rowPrice['ID'],
		                'PRICE' => $rowPrice['PRICE'],
		                'CURRENCY' => $rowPrice['CURRENCY'],
		                'CATALOG_GROUP_ID' => $rowPrice['CATALOG_GROUP_ID'],
		        	]
		        ];
		    }
		     if(intval($arPrices[0]["PRICE"])==0){
		    	//если доставка из Москвы и цена 0, то берем цену Москвы
	    		$res = CIBlockElement::GetByID($intProductID);
	    		if($ar_res = $res->GetNext()){
	    			$db_props = CIBlockElement::GetProperty($ar_res['IBLOCK_ID'], $intProductID, array("sort" => "asc"), Array("CODE"=>"delivery_from_msk"));
					if($ar_props = $db_props->Fetch()){
						
						if($ar_props["VALUE"]=='Y'){
							$obPrice = \Bitrix\Catalog\PriceTable::getList(array(
					            'filter' => array(
					                'PRODUCT_ID' => $intProductID,
					                'CATALOG_GROUP_ID' => 1,
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

								
						        $arPrices = [
						        	[
						                'ID' => $rowPrice['ID'],
						                'PRICE' => $rowPrice['PRICE'],
						                'CURRENCY' => $rowPrice['CURRENCY'],
						                'CATALOG_GROUP_ID' => self::getGeo(),
						        	]
						        ];
						        
						    }
						}
						
					}
	    		}
		    }
			$prices = CCatalogProduct::GetOptimalPrice(
				$intProductID,
				$quantity,
				$arUserGroups,
				$renewal,
				self::addProtectionFromRecursion($arPrices),
				$siteID,
				$arDiscountCoupons	
			);
			$result['PRICE']['PRICE'] = $basePrice;
			$result['RESULT_PRICE']['BASE_PRICE'] = $basePrice;
			$result['RESULT_PRICE']['DISCOUNT_PRICE'] = $discount_val;
			$result['DISCOUNT_PRICE'] = $discount_val;
			//return $result;
			return $prices;
		}

		return true;

		/*$kit = CCatalogProductSet::getAllSetsByProduct($intProductID,CCatalogProductSet::TYPE_SET);
		if($kit && !self::isRecursion($arPrices)){
			foreach ($kit as $key => $value) {
				$kit = $kit[$key];
				break;
			}

			$CATALOG_PRICE_ID = self::addGeoIfNotExist($arPrices)[0]['CATALOG_PRICE_ID'];

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
		        	]
		        ];
		    }

	    	$result = CCatalogProduct::GetOptimalPrice($kit['ITEM_ID'], 2, [], 'N', self::addProtectionFromRecursion($arPrice), $siteID);

			$result['PRICE']['PRICE'] = $basePrice;
			$result['RESULT_PRICE']['BASE_PRICE'] = $basePrice;
			$result['RESULT_PRICE']['DISCOUNT_PRICE'] = $discount_val;
			$result['DISCOUNT_PRICE'] = $discount_val;
			return $result;
		}
		else{
			return true;
		}*/
	}

	protected static function addGeoIfNotExist($arPrices){
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
		$arPrices[0]['CATALOG_PRICE_ID'] = $CATALOG_PRICE_ID;
		return $arPrices;
	}

	protected static function geoCorrectPrice(
		$intProductID,
		$quantity,
		$arUserGroups,
		$renewal,
		$arPrices,
		$siteID,
		$arDiscountCoupons
	) {
		if(!self::isRecursion($arPrices) && !$arPrices[0]['CATALOG_GROUP_ID']){

	        $obPrice = \Bitrix\Catalog\PriceTable::getList(array(
	            'filter' => array(
	                'PRODUCT_ID' => $intProductID,
	                'CATALOG_GROUP_ID' => self::getGeo(),
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
		        $arPrices = [
		        	[
		                'ID' => $rowPrice['ID'],
		                'PRICE' => $rowPrice['PRICE'],
		                'CURRENCY' => $rowPrice['CURRENCY'],
		                'CATALOG_GROUP_ID' => $rowPrice['CATALOG_GROUP_ID'],
		        	]
		        ];
		    }
		     if(intval($arPrices[0]["PRICE"])==0){
		    	//если доставка из Москвы и цена 0, то берем цену Москвы
	    		$res = CIBlockElement::GetByID($intProductID);
	    		if($ar_res = $res->GetNext()){
	    			$db_props = CIBlockElement::GetProperty($ar_res['IBLOCK_ID'], $intProductID, array("sort" => "asc"), Array("CODE"=>"delivery_from_msk"));
					if($ar_props = $db_props->Fetch()){
						
						if($ar_props["VALUE"]=='Y'){
							$obPrice = \Bitrix\Catalog\PriceTable::getList(array(
					            'filter' => array(
					                'PRODUCT_ID' => $intProductID,
					                'CATALOG_GROUP_ID' => 1,
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

								
						        $arPrices = [
						        	[
						                'ID' => $rowPrice['ID'],
						                'PRICE' => $rowPrice['PRICE'],
						                'CURRENCY' => $rowPrice['CURRENCY'],
						                'CATALOG_GROUP_ID' => self::getGeo(),
						        	]
						        ];
						        
						    }
						}
						
					}
	    		}
		    }
			$prices = CCatalogProduct::GetOptimalPrice(
				$intProductID,
				$quantity,
				$arUserGroups,
				$renewal,
				self::addProtectionFromRecursion($arPrices),
				$siteID,
				$arDiscountCoupons	
			);

			return $prices;
		}

		return true;
	}

	protected static function addProtectionFromRecursion($arPrices){
		if($arPrices[0]){
			$arPrices[0]['NO_CONTINUE'] = true;
		}
		else{
			$arPrices[] = ['NO_CONTINUE' => true];
		}

		return $arPrices;
	}

	protected static function isRecursion($arPrices){		
		return $arPrices[0]['NO_CONTINUE'];
	}
}