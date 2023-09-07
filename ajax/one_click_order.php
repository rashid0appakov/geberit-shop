<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;
use Bitrix\Sale\Delivery\Services\Manager as DeliveryManager;
use Bitrix\Sale\PaySystem\Manager as PaySystemManager;


Loader::includeModule('catalog');
Loader::includeModule('sale');
Loader::includeModule('iblock');



$name = 'TEST'; // Имя пользователя
$email = 'matrosov-stanislav@mail/ru'; // E-mail пользователя
$phone = '89991212'; // Телефон пользователя
$userComment = ''; // Комментарий к заказу
$productId = $_GET['id']; // Id элемента
 
$userId = 683; // либо получаем из данных формы, либо берём по умолчанию


$products = array(
    array('PRODUCT_ID' => $_GET['id'], 'NAME' => 'Товар 1', 'PRICE' => 500, 'CURRENCY' => 'RUB', 'QUANTITY' => 1)
);



$basket = Bitrix\Sale\Basket::create(SITE_ID);

foreach ($products as $product)
{
	$item = $basket->createItem("catalog", $product["PRODUCT_ID"]);
	unset($product["PRODUCT_ID"]);
	$item->setFields($product);
}




$order = Bitrix\Sale\Order::create(SITE_ID, 1);
$order->setPersonTypeId(1);
$order->setBasket($basket);


$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem(
	Bitrix\Sale\Delivery\Services\Manager::getObjectById(2)
);




$shipmentItemCollection = $shipment->getShipmentItemCollection();

foreach ($basket as $basketItem)
{
	$item = $shipmentItemCollection->createItem($basketItem);
	$item->setQuantity($basketItem->getQuantity());
}



$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem(
	Bitrix\Sale\PaySystem\Manager::getObjectById(1)
);



$payment->setField("SUM", $order->getPrice());
$payment->setField("CURRENCY", $order->getCurrency());



$result = $order->save();
if ( !$result->isSuccess() ) {
	//$result->getErrors();
} else {
	
	$ID = $result->getId();
	
	echo $ID;
	
}



if( $_GET['id'] ) {
	
	
	
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>