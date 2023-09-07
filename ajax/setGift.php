<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$productId=intval($_REQUEST["productid"]);
$giftId=intval($_REQUEST["giftid"]);
$resp=false;
if($productId>0 && $giftId>0)
{
	$resp=CGifts::setGiftToBasketItem($productId,$giftId);
}
ob_end_clean();
ob_start();
echo json_encode($resp);
?>