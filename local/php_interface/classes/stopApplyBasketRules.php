<?
/**
 * 
 */
class stopApplyBasketRules
{
	function __construct()
	{
		# code...
	}

	public function run(&$arFields){
		if($_GET['test'] == 'test'){
			die('omg');
		}
		
	}

	public function coupon(&$arResult){
		if($_GET['test'] == 'test'){
			if($arFields['DISCOUNT_PRICE'] != 0){
				// CSaleBasket::Update($ID,['PRICE'=>$arFields['BASE_PRICE'],$arFields['DISCOUNT_PRICE'] => 0]);
			}
			echo "<pre>";
			print_r($arResult);
			// print_r($Prices);
			echo "</pre>";
		}
		return $arResult;
	}
}