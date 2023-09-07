<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog")) {
	die('Err IncludeModule');
}
$coupon = $_POST['coupon'];

CCatalogDiscountCoupon::ClearCoupon();
$set_coupon = CCatalogDiscountCoupon::SetCoupon($coupon);
CSaleBasket::DoSaveOrderBasket();

if($set_coupon){
	echo true;
}
else{
	echo false;
}