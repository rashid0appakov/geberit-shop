<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

    use Bitrix\Sale\Compatible\DiscountCompatibility;
	use Bitrix\Sale\Basket;
	use Bitrix\Sale\Discount\Gift;
	use Bitrix\Sale\Fuser;
	use Bitrix\Main\Loader;

	/**
	 * Class CGifts
	 *
	 * Contains most commonly used site-wide methods
	 *
	 */
	class CGifts {
		const CACHE_TIME    =   1;
	    const GIFT_PROPS = "PROPERTY_GIFTS";
	    private static $sets=[];
	    private static $basketItems=[];

	    public static function getGiftIds($productId)
	    {
	        $giftProductIds = [];

	        if (!$productId) {
	            return $giftProductIds;
	        }

	        return $giftProductIds;
	    }

	    public static function onSaleOrderBeforeSaved(\Bitrix\Main\Event $event){
	        foreach($event->getResults() as $previousResult)
	        {
	            if($previousResult->getType()!=\Bitrix\Main\EventResult::SUCCESS)
	            {
	                return;
	            }
	        }

	        $order = $event->getParameter("ENTITY");    
	        if($order->getId())
	            return;
	        $descr=$order->getField('USER_DESCRIPTION');
	        $basket = $order->getBasket();



	        $basketItems = $basket->getBasketItems();
	        global $USER;

	        $gifts=[];
	        foreach ($basketItems as $basketItem) {
	            $basketPropertyCollection = $basketItem->getPropertyCollection();
	            $temp="";
	            foreach ($basketPropertyCollection as $propertyItem) {
	                

	                if ($propertyItem->getField('CODE') == 'GIFT') {
	                    $temp.=$propertyItem->getField('VALUE');
	                }
	                if ($propertyItem->getField('CODE') == 'GIFT_ID') {
	                    $temp.=" [".(int)$propertyItem->getField('VALUE')."]";
	                }

	                
	            }
	            if(strlen($temp)>0)
	            {
	                $temp="к товару [".$basketItem->getProductId()."] подарок ".$temp;
	                $gifts[]=$temp;
	            }
	        }
	        if(count($gifts)>0)
	        {
	              
	            $descr.=strlen($descr)>0?". ":"";
	            $descr.="Подарки: ".implode(", ", $gifts).".";
	            $order->setField('USER_DESCRIPTION',$descr);
	        }
	    }

	    public static function setGiftToBasketItem($basketItemID,$giftID=0)
	    {
	        
	        $gifts=self::getComplect($basketItemID);
   
			$gifts=count($gifts)>0?$gifts:self::getGifts([$basketItemID]);

	        //$gifts=self::getGifts([$basketItemID]);
	        
	        if(is_array($gifts[$basketItemID]) && count($gifts[$basketItemID])>0 && $giftID==0)
	        { 
	            $giftID=array_key_first($gifts[$basketItemID]);
	        }
	        if(!isset($gifts[$basketItemID][$giftID]))
	        { 
	            return false;
	        }
	        $basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
	        $basketItems = $basket->getBasketItems();
	        foreach ($basketItems as $basketItem) {
	            if($basketItem->getProductId()==$basketItemID)
	            {
	                $basketPropertyCollection = $basketItem->getPropertyCollection();
	                $basketPropertyCollection->setProperty(array(
	                    array(
	                       'NAME' => 'Подарок',
	                       'CODE' => 'GIFT',
	                       'VALUE' => $gifts[$basketItemID][$giftID]['NAME'],
	                       'SORT' => 100,
	                    ),
	                    array(
	                       'NAME' => 'ID подарка',
	                       'CODE' => 'GIFT_ID',
	                       'VALUE' => $giftID,
	                       'SORT' => 101,
	                    )
	                ));
	                $basket->save();
	                return $giftID;
	            }
	        }
	        return false;

	    }

	    public static function getGiftBasketItem($basketItemID)
	    {
	        
	        $basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
	        $basketItems = $basket->getBasketItems();
	        foreach ($basketItems as $basketItem) {
	            if($basketItem->getProductId()==$basketItemID)
	            {
	                $basketPropertyCollection = $basketItem->getPropertyCollection();
	                foreach ($basketPropertyCollection as $propertyItem) {
	                    if ($propertyItem->getField('CODE') == 'GIFT_ID') {
	                        return (int)$propertyItem->getField('VALUE');
	                    }
	                }
	                
	                
	            }
	        }
	        return false;

	    }

	    public static function getGifts($pIds=[])
	    {
	        global $USER;
	        
	        $data=[];

	        //if(!$USER->IsAdmin()) return $data;

	        $obCache = new CPHPCache;
	        if($obCache->InitCache(self::CACHE_TIME, md5(serialize($pIds)), "/gifts"))
	        {
	            $data = $obCache->GetVars();
	        }
	        elseif(Loader::includeModule("iblock") && $obCache->StartDataCache())
	        {
	            $giftsIds=[];

	            $rsItems = CIBlockElement::getList(
	                [], 
	                ['IBLOCK_ID' => CATALOG_IBLOCK_ID,'ACTIVE' => 'Y','ID'=>$pIds], 
	                false, 
	                false,
	                ['ID',self::GIFT_PROPS]);

	            while ($arItem = $rsItems->fetch()) {

	                
	                foreach ($arItem[self::GIFT_PROPS."_VALUE"] as $gift) {
	                    $data[$arItem["ID"]][$gift]=$giftsIds[$gift]=$gift;
	                }
	            }

	            if(count($giftsIds)>0)
	            {
	                $rsItems = \Bitrix\Iblock\ElementTable::getList([
	                    'select' => ['ID','NAME','ACTIVE','DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL','CODE','DETAIL_PICTURE'],
	                    'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID,
	                    	'ACTIVE' => 'Y',
	                    	[
								"LOGIC" => "OR",
								'<ACTIVE_FROM' => new \Bitrix\Main\DB\SqlExpression("NOW()"),
								'ACTIVE_FROM' => null,
							],
							[
								"LOGIC" => "OR",
								'>ACTIVE_TO' => new \Bitrix\Main\DB\SqlExpression("NOW()"),
								'ACTIVE_TO' => null,
							],
							'ID'=>$giftsIds
						]
	                ]);

	                $temp=[];
	                while ($arItem = $rsItems->fetch()) {
	                    $arItem['DETAIL_PAGE_URL']=CIBlock::ReplaceDetailUrl($arItem['DETAIL_PAGE_URL'], $arItem, false, 'E');
	                    if(intval($arItem["DETAIL_PICTURE"])>0)
	                    {
	                    	$arItem['PIC']=CGifts::getPreviewPhoto($arItem["DETAIL_PICTURE"]);
	                    }
	                    $temp[$arItem['ID']]=$arItem;
	                }
	                $giftsIds=$temp;
	            }
	            foreach ($pIds as $pId) {
	                foreach ($data[$pId] as $giftId=>$gift) {
	                    if(isset($giftsIds[$giftId]))
	                    {
	                        $data[$pId][$giftId]=$giftsIds[$giftId];
	                    }
	                    else
	                    {
	                        unset($data[$pId][$giftId]);
	                    }
	                }
	            }
	            $obCache->EndDataCache($data);
	        }
	        return $data;
	    }

	    public static function getComplect($pId)
	    {
	        if(count(self::$basketItems)==0)
	        {
	            $basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
	            $basketItems = $basket->getBasketItems();
	            foreach ($basketItems as $basketItem) {
	                self::$basketItems[]=$basketItem->getProductId();
	            }
	        }
	        $rsSets = CCatalogProductSet::getList(
	            array(),
	            array("!SET_ID" =>0, "ACTIVE"=>"Y", "ITEM_ID"=>$pId, 'TYPE' => CCatalogProductSet::TYPE_SET),
	            false,
	            false,
	            ["OWNER_ID","ITEM_ID"]
	        );
	        $copmplects=[];
	        while ($arSet = $rsSets->Fetch())
	        {
	            if(in_array($arSet["OWNER_ID"], self::$sets))
	            {
	                return [];
	            }
	            else
	            {
	                self::$sets[]=$arSet["OWNER_ID"];
	            }
	            $rsSets2 = CCatalogProductSet::GetList(
	                array(), array( "TYPE" => 1, "ACTIVE"=>"Y", "OWNER_ID" => $arSet["OWNER_ID"] ), false, false, array()
	            );
	            
	            $copmplects[$arSet["OWNER_ID"]]=$arSet["OWNER_ID"];
	            while( $arSet2 = $rsSets2->Fetch() ){
	                if( $arSet2["OWNER_ID"]!=$arSet2["ITEM_ID"]  ){
	                    if(!in_array($arSet2["ITEM_ID"], self::$basketItems))
	                    {
	                        unset($copmplects[$arSet["OWNER_ID"]]);
	                        break;
	                    }
	                }
	            }
	            
	        }   
	        if(count($copmplects)>0)
	        {	
	        	$g=self::getGifts($copmplects);
	        	if(count($g)>0)
	        	{
	        		$temp[$pId]=array_shift($g);
	        		return $temp;
	        	}
	            
	        }
	        return [];
	    }

	    public static function getPreviewPhoto($imageID = 0, $with=31, $height=31){
	        if($imageID>0)
	        {
	            return CFile::ResizeImageGet(
	                $imageID,
	                array('width' => $with, 'height' => $height),
	                BX_RESIZE_IMAGE_PROPORTIONAL,
	                true
	            )["src"];
	        }
	        return false;
	    }


    }
?>