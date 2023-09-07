<?

$itemsIds=[];
foreach ($arResult["ITEMS"] as $key => $value) {
	if(!in_array($value["ID"], $itemsIds))
	{
		$itemsIds[]=$value["ID"];
	}
}
$gifts=CGifts::getGifts($itemsIds);

foreach ($arResult["ITEMS"] as $key=>$item) {
	
	$arResult["ITEMS"][$key]["GIFT"]=isset($gifts[$item["ID"]])?$gifts[$item["ID"]]:[];
		
}
?>