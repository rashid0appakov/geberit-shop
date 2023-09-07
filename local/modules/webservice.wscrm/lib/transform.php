<?php

namespace Webservice\Wscrm;

use Bitrix\Sale\Order;
use Bitrix\Main\Context;
use Bitrix\Sale\Location;
use Bitrix\Main\UserTable;
use Bitrix\Main\Config\Option;

class Transform
{
    const CITY_ID_SPB = 2;
    const CITY_ID_MSK = 1;
    const ORDER_SEPARATOR = "-";

    private $MODULE_ID = 'webservice.wscrm';
    private $API_ORDER_PREFIX_OPTION = 'order_prefix';
    private $PAYMENT_LIST_OPTION = 'payment_services';
    private $DELIVERY_LIST_OPTION = 'delivery_services';
    private $CUSTOM_FIELDS_OPTION = 'custom_fields';
    private $PRODUCT_PROPERTIES_OPTION = 'product_properties';

    protected $order;
    protected $properties = array();
    protected $serverName;

    /**
     * Create new order transform instance
     *
     * @param Bitrix\Sale\Order $order
     */
    public function __construct(Order $order)
    {
        $this->initProperties($order);
        $this->initServer();
        $this->transform($order);
    }

    /**
     * Get result order after transform
     *
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Process transform
     *
     * @param  Bitrix\Sale\Order $order
     * @return
     */
    private function transform($order)
    {
        $data = [
            'number'               => $this->getOrderNumber($order->getField('ACCOUNT_NUMBER') ? $order->getField('ACCOUNT_NUMBER') : $order->getId()),
            'city'                 => $this->getCity($order),
            'delivery'             => $order->getDeliveryPrice(),
            'discount'             => $order->getDiscountPrice(),
            'doc_prepay'           => $order->getSumPaid(), // Оплаченная сумма
            'comment'              => $order->getField('USER_DESCRIPTION'), // комментарий пользователя
            'doc_user_type'        => $order->getPersonTypeId() == 1 ? '100' : '101',
            'montage'              => $this->getProperties("SERVICE_INSTALLATION") == 'Y' ? 1 : 0,
            'lifting'              => $this->getProperties("SERVICE_LIFT_ON") == 'Y' ? 1 : 0,
            'doc_source'           => 'from_cart',
            'phone_number'         => [],
            'doc_person_details'   => [],
            'doc_address_details'  => [
                'subject'             => '' , // Субъект РФ
                'city'                => '' , // Населенный пункт
                'street'              => '' , // Улица
                'house_number'        => '' , // Дом
                'room_number'         => '' , // Квартира
                'build'               => '' , // Строение
                'exit'                => '' , // Подъезд
                'build_number'        => '' , // Корпус
                'stage'               => '' , // Этаж
                'ownership'           => '' , // Владение
            ],
        ];

        if ($this->getProperties("DELIVERY_MKAD"))
            $data['comment']    .= "\n\nДоставка за МКАД: ".$this->getProperties("DELIVERY_MKAD").'км';

        // get order user phone
        if (($phone = $this->getProperties('PHONE')) !== null && ! empty($phone)) {
            $data['phone_number'][] = $phone;
        }

        // if its not new user
        if (($user = $this->getOrderUser($order))) {
            $data['doc_person_details'] = array(
                'last_name'    => $user['LAST_NAME'],
                'first_name'   => $user['NAME'],
                'middle_name'  => $user['SECOND_NAME'],
            );
            if (! empty($user['EMAIL'])) {
                $data['email'] = $user['EMAIL'];
            }
            if($user['LOGIN']!="anonymous_0bWdA8wrg")   
            {
                if (!empty($user['PERSONAL_PHONE'])) {
                    $data['phone_number'][] = $user['PERSONAL_PHONE'];
                }

                if (!empty($user['WORK_PHONE'])) {
                    $data['phone_number'][] = $user['WORK_PHONE'];
                }
            }
        }

        // get user location
        $cityName = null;
        if (($location = $this->getProperties('LOCATION')) != null && ! empty($location)) {
            if (($bLocation = $this->getLocation($location)) !== null) {
                $cityName = $bLocation['NAME_RU'];
            }
        }
        // get city name
        if ((is_null($cityName) || empty($cityName)) && ($cityProp = $this->getProperties('CITY')) && ! empty($cityProp)) {
            $data['doc_address_details']['city'] = $cityProp;
        } else {
            $data['doc_address_details']['city'] = $cityName;
        }
        // get delivery address
        if (($deliveryAddress = $this->getProperties('ADDRESS')) !== null && ! empty($deliveryAddress)) {
            $data['doc_address_details']['street'] = $deliveryAddress;
        }
		
		//Адрес доставки
		if (($STREET = $this->getProperties('STREET')) !== null && ! empty($STREET)) {
            $data['doc_address_details']['street'] = $STREET;
        }
		
		if (($HOME = $this->getProperties('HOME')) !== null && ! empty($HOME)) {
            $data['doc_address_details']['house_number'] = $HOME;
        }
		
		if (($KVARTIRA = $this->getProperties('KVARTIRA')) !== null && ! empty($KVARTIRA)) {
            $data['doc_address_details']['room_number'] = $KVARTIRA;
        }
		
		if (($STROENIE = $this->getProperties('STROENIE')) !== null && ! empty($STROENIE)) {
            $data['doc_address_details']['build'] = $STROENIE;
        }
		
		if (($PODIEZD = $this->getProperties('PODIEZD')) !== null && ! empty($PODIEZD)) {
            $data['doc_address_details']['exit'] = $PODIEZD;
        }
		
		if (($KORPUS = $this->getProperties('KORPUS')) !== null && ! empty($KORPUS)) {
            $data['doc_address_details']['build_number'] = $KORPUS;
        }
		
		if (($ETAZ = $this->getProperties('ETAZ')) !== null && ! empty($ETAZ)) {
            $data['doc_address_details']['stage'] = $ETAZ;
        }

		if (($VLADENIE = $this->getProperties('VLADENIE')) !== null && ! empty($VLADENIE)) {
            $data['doc_address_details']['ownership'] = $VLADENIE;
        }

        // ---------------------
        // get order user data
        // ---------------------
        // get order user email
        if (($email = $this->getProperties('EMAIL')) != null && ! empty($email)) {
            $data['email'] = $email;
        }
        /*// get order user phone
        if (($phone = $this->getProperties('PHONE')) !== null && ! empty($phone)) {
            $data['phone_number'][] = $phone;
        }*/

        // -- Reset email for test order ------------------------------------ //
        if (!empty($data['phone_number'])){
            $data['phone_number'] = array_unique($data['phone_number']);
            foreach($data['phone_number'] AS $phone)
                if (\CClass::clearPhone($phone) == '+71111111111'){
                    $data['email'] = 'test@test.ru';
                    break;
                }
        }

        //get deliveries
        $deliveryList = unserialize(Option::get($this->MODULE_ID, $this->DELIVERY_LIST_OPTION));
        if (is_array($deliveryList) && count($deliveryList) > 0) {
            $shipmentCollection = $order->getShipmentCollection();
            foreach ($shipmentCollection as $shipment) {
                // get first delivery
                if ($shipment->getDeliveryId()) {
                    $delivery = \Bitrix\Sale\Delivery\Services\Manager::getById($shipment->getDeliveryId());
                    if (array_key_exists($shipment->getDeliveryId(), $deliveryList)) {
                        $data['delivery_method'] = $deliveryList[$shipment->getDeliveryId()];
                        // we need only first delivery method
                        break;
                    }
                    if (array_key_exists($delivery['PARENT_ID'], $deliveryList)) {
                        $data['delivery_method'] = $deliveryList[$delivery['PARENT_ID']];
                        break;
                    }
                }
            }
        }

        if (!$data['delivery_method'] && !empty($delivery))
            $data['comment']    .= "\n\nСпособ доставки: ".$delivery['NAME'];

        // get payments
        $paymentList = unserialize(Option::get($this->MODULE_ID, $this->PAYMENT_LIST_OPTION));
        if (is_array($paymentList) && count($paymentList) > 0) {
            $paymentCollection = $order->getPaymentCollection();
            foreach ($paymentCollection as $payment) {
                // $sum = $payment->getSum(); // сумма к оплате
                // $isPaid = $payment->isPaid(); // true, если оплачена
                // $isReturned = $payment->isReturn(); // true, если возвращена

                // $ps = $payment->getPaySystem(); // платежная система (объект Sale\PaySystem\Service)
                // $psID = $payment->getPaymentSystemId(); // ID платежной системы
                // $psName = $payment->getPaymentSystemName(); // название платежной системы
                // $isInnerPs = $payment->isInner(); // true, если это оплата с внутреннего счета
                // $paymentFields = $payment->getFields()->getValues(); // массив данных оплаты

                if (array_key_exists($payment->getPaymentSystemId(), $paymentList)) {
                    $data['payment_method'] = $paymentList[$payment->getPaymentSystemId()];
                }
            }
        }

        // merge transformed basket data
        $data = array_merge($data, $this->getProductsData($order));

        // merge custom fields if exists
        $data = array_merge($data, [
            'customFields' => array_merge($this->getCustomFields($order), [
                // hard coded cos in module roistat use another event type
                // onOrderAdd - this is old event, which fire always after OnSaleOrderSaved
                'roistat_visit' =>  array_key_exists('visit', $_REQUEST) ? $_REQUEST['visit'] : $_COOKIE["roistat_visit"]
            ])
        ]);

        $this->order = $data;
    }

    /**
     * Init protocol and server name
     *
     * @return string
     */
    private function initServer()
    {
        $server = Context::getCurrent()->getServer();
        $protocol = 'http://';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $server->getServerName());
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode === 200) {
            $protocol = 'https://';
        }

        $this->serverName = $protocol . $server->getServerName();
    }

    /**
     * Init order properties
     *
     * @param  Bitrix\Sale\Order  $order
     * @return void
     */
    private function initProperties($order)
    {
        $propertyCollection = $order->getPropertyCollection();

        // fast values for order
        $propertyValues = [
            'USER_EMAIL'             => $propertyCollection->getUserEmail(),
            'PAYER_NAME'             => $propertyCollection->getPayerName(),
            'DELIVERY_LOCATION'      => $propertyCollection->getDeliveryLocation(),
            'TAX_LOCATION'           => $propertyCollection->getTaxLocation(),
            'PROFILE_NAME'           => $propertyCollection->getProfileName(),
            'DELIVERY_LOCATION_ZIP'  => $propertyCollection->getDeliveryLocationZip(),
            'PHONE'                  => $propertyCollection->getPhone(),
            'ADDRESS'                => $propertyCollection->getAddress()
        ];
        $this->properties = $propertyCollection->getArray()['properties'];
    }

    /**
     * Get order property by code
     *
     * @param  string|null $item
     * @return string|null
     */
    private function getProperties($item = null)
    {
        if (! is_null($item) && ! empty($item)) {
            if (($search = array_search($item, array_column($this->properties, 'CODE'))) !== false) {
                return $this->properties[$search]['VALUE'][0];
            }
            return null;
        }

        return $this->properties;
    }

    /**
     * Get order products for crm
     *
     * @param  Bitrix\Sale\Order  $order
     * @return array
     */
    private function getProductsData(Order $order)
    {
        $basket = $order->getBasket();
        $result = [];

        $crmProductProperties = Action::getCrmProductProperties();
        $productProperties = unserialize(Option::get($this->MODULE_ID, $this->PRODUCT_PROPERTIES_OPTION));

        if (count($basket) > 0) {
            foreach ($basket as $item) {
                $itemFields = $item->getFields();
                $productInfo = \CCatalogProduct::GetByIDEx($itemFields["PRODUCT_ID"]);
                // if product item is sku
                if (isset($productInfo['PRODUCT']) && (int) $productInfo['PRODUCT']['TYPE'] == 4) {
                    $productInfo = \CCatalogSku::GetProductInfo($itemFields["PRODUCT_ID"]);
                }

                $result['name'][]         = $itemFields['NAME'];
                $result['quantity'][]     = $itemFields['QUANTITY'];
                if ($discount = $itemFields['DISCOUNT_PRICE'] > 0) {
                    $result['price'][]          = $itemFields['BASE_PRICE'];
                    $result['discount_rub'][]   = floatval($itemFields['DISCOUNT_PRICE'] * $itemFields['QUANTITY']);
                } else {
                    $result['price'][]          = $itemFields['PRICE'];
                    $result['discount_rub'][]   = 0;
                }
                $result['product_site_url'][] = $this->serverName . $itemFields['DETAIL_PAGE_URL'];

                $productItemSelect = [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'DETAIL_PICTURE',
                    'PREVIEW_PICTURE'
                ];

                // get property types
                if (isset($productInfo['IBLOCK_ID']) && isset($productProperties[$productInfo['IBLOCK_ID']])) {
                    foreach ($productProperties[$productInfo['IBLOCK_ID']] as $key => $value) {
                        $productItemSelect[] = 'PROPERTY_' . strtoupper($value);
                        $productItemSelect[] = 'PROPERTY_' . strtoupper($value) . '.NAME';
                    }
                }

                $productItem = \CIBlockElement::GetList(
                    array("SORT" => "ASC"),
                    array("ID" => $productInfo["ID"], "IBLOCK_ID" => $productInfo['IBLOCK_ID']), false, false,
                    $productItemSelect
                )->fetch();

                if ($productItem) {
                    // get picture
                    $productPicture = intval($productItem['DETAIL_PICTURE'])
                        ? $productItem['DETAIL_PICTURE']
                        : $productItem['PREVIEW_PICTURE'];
                    if ($productPicture) {
                        if (($productImage = \CFile::GetPath($productPicture))) {
                            $result['product_image'][] = $this->serverName . $productImage;
                        }
                    }

                    // map properties
                    foreach ($crmProductProperties as $key => $property) {
                        $result[$key][] =  $this->getProductPropertyValue(
                            $productItem, $productProperties[$productInfo['IBLOCK_ID']][$key]
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get product/offer property value
     *
     * @param  array $offer
     * @param  string $property
     * @return string|null
     */
    protected function getProductPropertyValue($offer, $property)
    {
        $result = null;
        if (isset($offer["PROPERTY_" . strtoupper($property) . "_NAME"])) {
            $result =  $offer["PROPERTY_" . strtoupper($property) . "_NAME"];
        } elseif (isset($offer["PROPERTY_" . strtoupper($property) . "_VALUE"])) {
            $result =  $offer["PROPERTY_" . strtoupper($property) . "_VALUE"];
        } elseif (isset($offer[strtoupper($property)])) {
            $result =  $offer[strtoupper($property)];
        }
        return $result;
    }

    /**
     * Get custom fields for order
     *
     * @param  Bitrix\Sale\Order  $order
     * @return array
     */
    private function getCustomFields(Order $order)
    {
        if (empty($customFields = Option::get($this->MODULE_ID, $this->CUSTOM_FIELDS_OPTION))) {
            return [];
        }

        $fields = [];
        $customFields = unserialize($customFields);

        if (array_key_exists($order->getPersonTypeId(), $customFields)) {
            foreach ($customFields[$order->getPersonTypeId()] as $key => $field) {
                if (($value = $this->getProperties($field)) != null) {
                    $fields[$key] = $value;
                }
            }
        }

        return $fields;
    }

    /**
     * Get order user
     *
     * @param  Bitrix\Sale\Order  $order
     * @return array
     */
    private function getOrderUser(Order $order)
    {
        $userId = $order->getUserId();
        if ($userId) {
            return UserTable::getById($userId)->fetch();
        }

        return [];
    }

    /**
     * Get number for crm order
     *
     * @param string $orderId
     * @return string
     */
    private function getOrderNumber($orderId)
    {
        $orderPrefix = Option::get($this->MODULE_ID, $this->API_ORDER_PREFIX_OPTION.'_'.SITE_ID);
        if (!$orderPrefix)
            $orderPrefix = Option::get($this->MODULE_ID, $this->API_ORDER_PREFIX_OPTION);

        if (is_null($orderPrefix) || empty($orderPrefix)) {
            return $orderId;
        }

        if (isset($orderPrefix) && !empty($orderPrefix)) {
            $orderNumber = $orderId . self::ORDER_SEPARATOR . $orderPrefix;
        }

        return $orderNumber;
    }

    /**
     * Get city for order
     *
     * @param  Bitrix\Sale\Order  $order
     * @return int
     */
    private function getCity(Order $order)
    {
        $cityName = $this->getLocation($this->getProperties('LOCATION'))['NAME_RU'] ?: $this->getProperties('CITY');
        $city = null;

        if (! empty($cityName)) {
            if (mb_stripos($cityName, "Санкт-Петербург", null, "utf-8") !== false) {
                $city = self::CITY_ID_SPB;
            }
            elseif (mb_stripos($cityName, "Москва", null, "utf-8") !== false) {
                $city = self::CITY_ID_MSK;
            }
        }

        // if city is null, set city id = moscow id
        // this is for managers
        if (is_null($city) || empty($city)) {
            $city = self::CITY_ID_MSK;
        }

        return $city;
    }

    /**
     * Get order location
     *
     * @param  null|string $code
     * @return null|array
     */
    private function getLocation($code = null)
    {
        if (! is_null($code) && ! empty($code)) {
            $bLocation = Location\LocationTable::getList([
                'filter' => ['NAME.LANGUAGE_ID' => LANGUAGE_ID, '=CODE' => $code],
                'select' => ['*', 'NAME_RU' => 'NAME.NAME']
            ])->fetch();

            if ($bLocation['NAME_RU']) {
                return $bLocation;
            }
        }

        return null;
    }
}