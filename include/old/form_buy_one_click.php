<?global $arBuyOneClickFilter;?>
<?$APPLICATION->IncludeComponent("altop:buy.one.click", "", 
	array(																
		"ELEMENT_ID" => $arBuyOneClickFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arBuyOneClickFilter["ELEMENT_AREA_ID"],		
		"REQUIRED" => array(
			0 => "NAME",
			1 => "PHONE",
			3 => "EMAIL",
		),
		"BUY_MODE" => $arBuyOneClickFilter["BUY_MODE"],		
		"CONS_IDS" => $arBuyOneClickFilter["CONS_IDS"],	
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);?>