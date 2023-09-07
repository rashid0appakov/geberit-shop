<?
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
define ( "PUBLIC_AJAX_MODE",
         true
);
\Bitrix\Main\Loader::includeModule ( 'iblock' );
\Bitrix\Main\Loader::includeModule ( 'catalog' );
\Bitrix\Main\Loader::includeModule ( 'sale' );

if (!empty( $_POST['phone'] ) && !empty( $_POST['code'] )) {
    $context = \Bitrix\Main\Application::getInstance()->getContext();
    $request = $context->getRequest();

    $siteId = \Bitrix\Main\Context::getCurrent ()->getSite ();
    $userID = ($GLOBALS['USER']->GetID () > 0) ? $GLOBALS['USER']->GetID () : \CSaleUser::GetAnonymousUserID ();

    $order = \Bitrix\Sale\Order::create ( $siteId, $userID );
    $order->setPersonTypeId ( 1 );

    $basket = \Bitrix\Sale\Basket::loadItemsForFUser ( \CSaleBasket::GetBasketUserID (), $siteId )->getOrderableItems ();
    $order->setBasket ( $basket );

    /**
     * создаем отгрузку
     */
    $shipmentCollection = $order->getShipmentCollection ();
    $shipment = $shipmentCollection->createItem ();
    $service = \Bitrix\Sale\Delivery\Services\Manager::getById ( \Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId () );
    $shipment->setFields ( [
                               'DELIVERY_ID'   => $service['ID'],
                               'DELIVERY_NAME' => $service['NAME'],
                           ]
    );
    $shipmentItemCollection = $shipment->getShipmentItemCollection ();

    $basket = $order->getBasket ();
    $basketempty = true;
    foreach ($basket as $basketItem) {
        // кладем корзину в отгрузку
        $shipmentItem = $shipmentItemCollection->createItem ( $basketItem );
        $shipmentItem->setQuantity ( $basketItem->getQuantity () );
        $basketempty = false;
    }
    if ($basketempty) {
        exit();
    }

    /**
     * Создаём оплату
     */
    $paymentCollection = $order->getPaymentCollection ();
    $payment = $paymentCollection->createItem ();
    $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById ( 1 );
    $payment->setFields ( [
                              'PAY_SYSTEM_ID'   => $paySystemService->getField ( "PAY_SYSTEM_ID" ),
                              'PAY_SYSTEM_NAME' => $paySystemService->getField ( "NAME" ),
                          ]
    );

    /**
     * добавление свойств
     */
    $propertyCollection = $order->getPropertyCollection ();
    // имя
    $StoreIDPropValue = $propertyCollection->getItemByOrderPropertyId ( 1 );
    $StoreIDPropValue->setValue ( ($GLOBALS['USER']->isAuthorized() ? $GLOBALS['USER']->GetFullName() : 'Купить&nbsp;в&nbsp;один&nbsp;клик') );

    $regionId = $request->getCookie ( "GEOLOCATION_ID" );

    $prop = $propertyCollection->getItemByOrderPropertyId ( 6 );
    if (is_object ($prop)) {
        $prop->setValue ( ($regionId ? $regionId : DEFAULT_GEOLOCATION_ID) );
    }

    $prop = $propertyCollection->getItemByOrderPropertyId ( 5 );
    if (is_object ($prop)) {
        $prop->setValue ( 'Москва' );
    }

    $prop = $propertyCollection->getItemByOrderPropertyId ( 4 );
    if (is_object ($prop)) {
        $prop->setValue ( '101000' );
    }

    $prop = $propertyCollection->getItemByOrderPropertyId ( 3 );
    if (is_object ($prop)) {
        $prop->setValue ( htmlspecialcharsbx ( '+7 ('.$_POST['code'].') '.$_POST['phone'] ) );
    }

    $order->setField ( 'CURRENCY', "RUB" );

    // комментарий
    $order->setField ( 'USER_DESCRIPTION', "Экспресс оформление" );
    $order->save ();
    $ORDER_ID = $order->GetId ();

    // очищаем корзину
    \CSaleBasket::DeleteAll ( CSaleBasket::GetBasketUserID () );

    ?>
    <script type="text/javascript" data-skip-moving="true">
        document.location.href = '/personal/order/?ORDER_ID=<?=$ORDER_ID?>&access=<?=$order->getHash()?>'
    </script>
    <?
}