<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
$json = [
	'status'=> 'error'
];
if((in_array('8', $USER->GetUserGroupArray()) || in_array('1', $USER->GetUserGroupArray()))){
	
	$arAll = array();
	$arResProv = array();
	
	if(count($_REQUEST['number']) > 0){
		$a = 1;
		
		$number = "IN (";
		
		foreach($_REQUEST['number'] as $number_item){
			
			if($GLOBALS['PAGE_DATA']['INFO_BRAND'][$_REQUEST['brand']]['NAME'] == 'Grohe'){
				$number_item .= "gh";
			}
			
			$number .= "'".$number_item."'";
			
			if(count($_REQUEST['number']) > $a){
				$number .= ", ";
			}
			
			$a++;
		}
		
		$number .= ")";
	}
	
	$queryProvider = "SELECT
						  *
						FROM
						  a_custom_product_new_price cpap
						WHERE
							cpap.number ".$number."
						";
	
	$json['TEST'] = $queryProvider;
	
	$resProv = $DB->Query($queryProvider);
	while($arProv = $resProv->Fetch()){
		$arResProv[] = $arProv;
	}	

	$queryAllProvider = "SELECT
						  *
						FROM
						  a_custom_product_all_provider cpap
						WHERE
							cpap.number ".$number."
						ORDER BY
							cpap.`number` ASC, cpap.`price_provider` ASC
						";			
	$resAllProv = $DB->Query($queryAllProvider);
	while($arAllProv = $resAllProv->Fetch()){
		$arAll[] = $arAllProv;
	}	
	
	$json['status'] = 'success';
	$json['result'] = $arResProv;	
	$json['result_all'] = $arAll;	

}
CClass::Instance()->RenderJSON($json);
?>