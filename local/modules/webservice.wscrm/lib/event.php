<?php

namespace Webservice\Wscrm;

class Event
{
    /**
     *
     * @param mixed \Bitrix\Sale\Order or \Bitrix\Main\Event $event
     */
    public static function OnSaleOrderSaved($event)
    {
        if (! $event instanceof \Bitrix\Main\Event) {
            Action::log('Webservice\Wscrm\Event::OnSaleOrderSaved', 'event', 'event paremeter expected \Bitrix\Main\Event object, ' . get_class($event) . ' given');
            $bOrder = $event;
            $isNew = $bOrder->isNew();
        } else {
            $bOrder = $event->getParameter('ENTITY');
            $oldValues = $event->getParameter('VALUES');
            $isNew = $event->getParameter('IS_NEW');
        }

        try {
            if (! $isNew) {
                Action::log('Webservice\Wscrm\Event::OnSaleOrderSaved', 'event', 'Order not new. Given: ' . $isNew);
                return;
            }

            Action::log('Webservice\Wscrm\Event::OnSaleOrderSaved', 'event', 'Event start');

            $transformer = new Transform($bOrder);
            $order = $transformer->getOrder();

            $orderSave = Action::api('createOrder', [$order]);
            if (! $orderSave) {
                Action::log('Webservice\Wscrm\Event::OnSaleOrderSaved', 'event', 'Error create order in handling event');
            }

            return true;
        } catch (\Exception $e) {
            Action::log('Webservice\Wscrm\Event::OnSaleOrderSaved', 'event', 'Try catch error: ' . $e->getMessage());
            return false;
        }

        return true;
    }
}