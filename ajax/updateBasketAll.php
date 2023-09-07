<? 
if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !isset($_POST['BASKET']) || empty($_POST['BASKET'])){
        $json['status'] = "error";
    }else{
        foreach($_POST['BASKET'] AS $product_id => &$qty)
            CSaleBasket::Update($product_id, ["QUANTITY" => (int)$qty]);
        $json['BASKET'] = CClass::getCartData();
    	if(true && $USER->GetID() == 2422){
    		$coupon = (\Bitrix\Sale\DiscountCouponsManager::get());
			$dbBasketItems = CSaleBasket::GetList(array(), array(
			    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
			    "LID" => SITE_ID,
			    "ORDER_ID" => "NULL"
			), false, false, array());
    		if(count($coupon) != 0){
				while ($arItem = $dbBasketItems->Fetch()) {
					if((int)$arItem['DISCOUNT_PRICE'] == 0){
				  		$arOrder["BASKET_ITEMS"][] = $arItem;
					}
				}
				$arOrder['SITE_ID'] = SITE_ID;
				$arOrder['USER_ID'] = $USER->GetID();
				CSaleDiscount::DoProcessOrder($arOrder, array(), $arErrors);
			}
			else{
				while ($arItem = $dbBasketItems->Fetch()) {
					$price = CCatalogProduct::GetOptimalPrice($arItem['PRODUCT_ID'],$qty,[],"N",[],SITE_ID,$coupon);
					$arItem['PRICE'] = $price['DISCOUNT_PRICE'];
					$arItem['DISCOUNT_PRICE'] = $arItem['BASE_PRICE'] - $arItem['PRICE'];
					$arOrder["BASKET_ITEMS"][] = $arItem;
			  		 
				}
			}

			foreach ($arOrder["BASKET_ITEMS"] as $basketItem) { 
				CSaleBasket::Update($basketItem['ID'],[
					'PRICE' => $basketItem['PRICE'],
					'DISCOUNT_PRICE' => $basketItem['DISCOUNT_PRICE'],
				]);
			} 	


    		$json['BASKET'] = CClass::getCartData();
    	}	
        
    }