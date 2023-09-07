<?php

namespace Webservice\Wscrm;

use Bitrix\Sale;
use Bitrix\Main\Config\Option;
use WebServiceCrm\ApiClient\Client as ApiClient;

class Action
{
    /**
     * @var string
     */
    protected static $API_ORDER_PREFIX_OPTION = 'order_prefix';

    /**
     * @var string
     */
    protected static $API_KEY_OPTION = 'key';

    /**
     * @var string
     */
    protected static $API_URL_OPTION = 'url';

    /**
     * @var string
     */
    protected static $MODULE_ID = 'webservice.wscrm';

    /**
     * @var string
     */
    protected static $PAYMENT_LIST_OPTION = 'payment_services';

    /**
     * @var string
     */
    protected static $DELIVERY_LIST_OPTION = 'delivery_services';

    /**
     * Send request to api with data
     *
     * @param  string|null $method
     * @param  array  $params
     * @return null|WebServiceCrm\ApiClient\Response
     */
    public static function api($method = null, $params = array())
    {
        try {
            $result = call_user_func_array([self::getApiClient(), $method], $params);

            if ($result->isSuccess() && $result->getStatusCode() == 200) {
                self::log('Webservice\WsCrm\Action::action', 'api', 'Success request and response');
            } else {
                self::log('Webservice\WsCrm\Action::action', 'api', 'error request');
            }

            self::log('Webservice\WsCrm\Action::action', 'api', 'Ответ от WebserviceCrm: '. $result->getResponse()['response']);

            return $result;

        } catch (Exception $e) {
            self::log('Webservice\WsCrm\Action::action', 'api', 'Throwing the exctption with message: ' . $e->getMessage() . ', with code: ' . $e->getCode());
        }

        return null;
    }

    /**
     * Get delivery services (only main delivery and not groups)
     *
     * @return array
     */
    public static function getDeliveryList()
    {
        $return = [];
        $deliveryServices = Sale\Delivery\Services\Manager::getActiveList();
        foreach ($deliveryServices as $delivery) {
            if (($delivery['PARENT_ID'] == '0' || $delivery['PARENT_ID'] == null) &&
                $delivery['CLASS_NAME'] != '\Bitrix\Sale\Delivery\Services\Group'
            ) {
                $return[] = $delivery;
            }
        }

        $return[] = [
            'ID'    => 21,
            'NAME'  => 'Доставка собственным транспортом'
        ];
        $return[] = [
            'ID'    => 29,
            'NAME'  => 'Доставка до терминала ТК'
        ];

        return $return;
    }

    /**
     * Get bitrix payment services
     *
     * @return array
     */
    public static function getPaymentList()
    {
        $return = [];
        $paymentServicesDb = Sale\PaySystem\Manager::getList([
            'select' => ['ID' , 'NAME'],
            'filter' => ['ACTIVE' => 'Y']
        ]);

        while ($paymentService = $paymentServicesDb->fetch()) {
            $return[] = $paymentService;
        }

        return $return;
    }

    /**
     * Get crm payments
     *
     * @todo get payments from crm by api
     * @return array
     */
    public static function getCrmPaymentList()
    {
        return [
            0 => ['name' => 'Наличными'],
            1 => ['name' => 'Банковской картой'],
            2 => ['name' => 'Безналичная оплата (для физический лиц)'],
            3 => ['name' => 'Безналичная оплата (для юридический лиц)'],
            4 => ['name' => 'Не указан'],
            5 => ['name' => 'Онлайн на карту Сбербанка'],
            6 => ['name' => 'Наличными(чек)'],
            7 => ['name' => 'Оплата картой на сайте'],
            8 => ['name' => 'Купить в рассрочку']
        ];
    }

    /**
     * Get crm delivery list
     *
     * @todo  get delivery methods from crm by api
     * @return
     */
    public static function getCrmDeliveryList()
    {
        return [
            0 => ['name' => 'Курьером'],
            1 => ['name' => 'Самовывоз из офиса магазина'],
            2 => ['name' => 'До транспортной компании']
        ];
    }

    /**
     * Get crm product properties codes
     *
     * @return array
     */
    public static function getCrmProductProperties()
    {
        return [
            'articul'  => 'artnumber',
            'brand'    => 'manufacturer',
        ];
    }

    /**
     * Get crm specific order custom fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        return [
            0 => ['code' => 'roistat_visit', 'name' => 'Идентификатор roistat'],
        ];
    }

    /**
     * Get order properties list
     *
     * @return array
     */
    public function getOrderPropsList()
    {
        $bPropertiesList = array();
        $arOrderProperties = Sale\Internals\OrderPropsTable::getList(array(
            'select' => array('*'),
            'filter' => array('CODE' => '_%')
        ));
        while ($prop = $arOrderProperties->Fetch()) {
            $bPropertiesList[$prop['PERSON_TYPE_ID']][] = $prop;
        }

        return $bPropertiesList;
    }

    /**
     * Get sites list
     * @return array
     */
    public function getSitesList()
    {
        $arSites = array();
        $rsSites = \CSite::GetList($by = "LID", $sort = "ASC", array('ACTIVE' => 'Y'));
        while ($ar = $rsSites->Fetch()) {
            $arSites[] = $ar;
        }

        return $arSites;
    }

    /**
     * Get bitrix person types
     *
     * @return array
     */
    public function getOrderTypesList()
    {
        $arSites = self::getSitesList();

        $bOrderTypesList = [];
        foreach ($arSites as $site) {
            $arPersonTypes = Sale\PersonType::load($site['ID']);
            foreach ($arPersonTypes as $personType) {
                $bOrderTypesList[$personType['ID']] = $personType;
            }
        }

        return $bOrderTypesList;
    }

    /**
     * Static method for logging
     *
     * @param  mixed $auditTypeId
     * @param  mixed $itemId
     * @param  string|null $description
     * @return [type]
     */
    public static function log($auditTypeId = null, $itemId = null, $description = null)
    {
        /*
            Поля добавляемого события. Значения:
            SEVERITY - степень важности записи. Доступны значения: SECURITY или WARNING, для иного система установит UNKNOWN.
            AUDIT_TYPE_ID - собственный ID типа события.
            MODULE_ID - модуль, с которого происходит запись в лог.
            ITEM_ID - ID объекта, в связи с которым происходит добавление (пользователь, элемент ИБ, ID сообщения, ...)
            REMOTE_ADDR - IP, с которого обратились.
            USER_AGENT - браузер.
            REQUEST_URI - URL страницы.
            SITE_ID - ID сайта, к которому относится добавляемое событие.
            USER_ID - ID пользователя.
            GUEST_ID - ID пользователя из модуля статистики
            DESCRIPTION - собственно описание записи лога, или техническая информация.
        */
        \CEventLog::Add(array(
            "SEVERITY"      => "SECURITY",
            "AUDIT_TYPE_ID" => $auditTypeId,
            "MODULE_ID"     => self::$MODULE_ID,
            "ITEM_ID"       => $itemId,
            "DESCRIPTION"   => $description,
        ));
    }

    /**
     * Get api client object
     *
     * @return WebServiceCrm\ApiClient\Client obj
     */
    private static function getApiClient()
    {
        $key    = Option::get(self::$MODULE_ID, self::$API_KEY_OPTION.'_'.SITE_ID);
        if (!$key)
            $key    = Option::get(self::$MODULE_ID, self::$API_KEY_OPTION);
        $prefix = Option::get(self::$MODULE_ID, self::$API_ORDER_PREFIX_OPTION.'_'.SITE_ID);
        if (!$prefix)
            $prefix = Option::get(self::$MODULE_ID, self::$API_ORDER_PREFIX_OPTION);

        return new ApiClient(
            Option::get(self::$MODULE_ID, self::$API_URL_OPTION),
            $key,
            $prefix
        );
    }
}