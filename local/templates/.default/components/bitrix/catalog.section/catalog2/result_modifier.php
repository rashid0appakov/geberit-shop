<?
foreach($arResult["ITEMS"] as $key=>$arItem ) {
	
	$ownerId = $arItem['ID']; // ID товара-комплекта
	$result = CCatalogProductSet::getAllSetsByProduct($ownerId, CCatalogProductSet::TYPE_SET);
	if (!empty($result))
	{
		$set = reset($result);
		$setId = key($result);

		/*
		echo 'ID комплекта: '.$setId;
		echo "<pre>";
		print_r($set); // описание и состав комплекта
		echo "<pre>";
		*/

		
		foreach( $set['ITEMS'] as $el ) {

			$res = CIBlockElement::GetByID( $el['ITEM_ID'] );
			if($ar_res = $res->GetNext()) {
				
				$ar_res['PRICES'] = CPrice::GetBasePrice($ar_res['ID']);

				

				$arResult["ITEMS"][ $key ]['SET'][] = $ar_res;
				
			}			
			
		}
		
	}
	
}	

/*
echo "<pre>";
print_r($arResult["ITEMS"][0]);
echo "</pre>";
*/
?>