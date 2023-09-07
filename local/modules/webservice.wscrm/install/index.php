<?php

/*
    index.php - файл с описанием модуля, содержащий инсталлятор/деинсталлятор модуля.
    from https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=101&LESSON_ID=3216&LESSON_PATH=8781.4793.3216
*/

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Webservice\Wscrm\Action;

class webservice_wscrm extends CModule
{
    /**
     * @var string
     */
    public $MODULE_ID = 'webservice.wscrm';

    /**
     * @var string
     */
    public $MODULE_VERSION;

    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;

    /**
     * @var string
     */
    public $MODULE_NAME;

    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;

    /**
     * @var string
     */
    public $MODULE_CSS;

    /**
     * @var string
     */
    public $MODULE_GROUP_RIGHTS = "Y";

    /**
     * @var string
     */
    public $API_ORDER_PREFIX_OPTION = 'order_prefix';

    /**
     * @var string
     */
    public $PAYMENT_LIST_OPTION = 'payment_services';

    /**
     * @var string
     */
    public $DELIVERY_LIST_OPTION = 'delivery_services';

    /**
     * @var string
     */
    public $CUSTOM_FIELDS_OPTION = 'custom_fields';

    /**
     * @var string
     */
    public $API_KEY_OPTION = 'key';

    /**
     * @var string
     */
    public $API_URL_OPTION = 'url';

    /**
     * @var string
     */
    private $INSTALL_PATH;

    /**
     * @var string
     */
    private $WSCRM_API_URL = 'http://app.shop-neptun.ru';


    public function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        $this->INSTALL_PATH = $path;
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = Loc::getMessage('WEBSERVICE_WSCRM_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('WEBSERVICE_WSCRM_PARNTER_URI');

        $this->MODULE_NAME = Loc::getMessage("WEBSERVICE_WSCRM_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("WEBSERVICE_WSCRM_DESCRIPTION");
    }

    /**
     * Method DoInstall is required
     * @return void
     */
    public function DoInstall()
    {
        global $APPLICATION, $arResult;

        // check if required modules are installed
        // check if sale module install
        if (! Loader::includeModule('sale')) {
            $APPLICATION->ThrowException(Loc::getMessage('WEBSERVICE_WSCRM_SALE_REQUIRED'));
            return false;
        }

        // check if iblock module install
        if (! Loader::includeModule('iblock')) {
            $APPLICATION->ThrowException(Loc::getMessage('WEBSERVICE_WSCRM_IBLOCK_REQUIRED'));
            return false;
        }

        if (! $this->correctBitrixVersion()) {
            $APPLICATION->ThrowException(Loc::getMessage('WEBSERVICE_WSCRM_NOT_SUPPORTED'));
            return false;
        }

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $step = $request->getPost('step');

        // include module special classes
        include($this->getInstallPath() . '/../lib/action.php');

        if ($step <= 1) {
            if (! $arResult['WEBSERVICE_CRM_API_URL'] &&
                ($apiUrl = Option::get($this->MODULE_ID, $this->API_URL_OPTION, $this->defaultApiUrl))) {
                $arResult['WEBSERVICE_WSCRM_API_URL'] = $apiUrl;
            }
            if (! $arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX']
                && ($apiOrderPrefix = Option::get($this->MODULE_ID, $this->API_ORDER_PREFIX_OPTION))) {
                $arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX'] = $apiOrderPrefix;
            }
            if (! $arResult['WEBSERVICE_WSCRM_API_KEY']
                && ($apiKey = Option::get($this->MODULE_ID, $this->API_KEY_OPTION))) {
                $arResult['WEBSERVICE_WSCRM_API_KEY'] = $apiKey;
            }

            /*
             * Get crm data
             */
            $arResult['deliveryList'] = Action::getCrmDeliveryList();
            $arResult['paymentList'] = Action::getCrmPaymentList();
            $arResult['customFields'] = Action::getCustomFields();

            /*
             * Get bitrix data
             */
            $arResult['bOrderTypesList'] = Action::getOrderTypesList();
            $arResult['bDeliveryList'] = Action::getDeliveryList();
            $arResult['bPaymentList'] = Action::getPaymentList();
            $arResult['bOrderPropertiesList'] = Action::getOrderPropsList();

            if (! $arResult['WEBSERVICE_WSCRM_DELIVERY_LIST'] && ($deliveryList = Option::get($this->MODULE_ID, $this->DELIVERY_LIST_OPTION))) {
                if (($unDeliveryList = unserialize($deliveryList)) && is_array($unDeliveryList) && count($unDeliveryList) > 0)  {
                    $arResult['WEBSERVICE_WSCRM_DELIVERY_LIST'] = $unDeliveryList;
                }
            }

            if (! $arResult['WEBSERVICE_WSCRM_PAYMENT_LIST'] && ($paymentList = Option::get($this->MODULE_ID, $this->PAYMENT_LIST_OPTION))) {
                if (($unPaymentList = unserialize($paymentList)) && is_array($unPaymentList) && count($unPaymentList) > 0)  {
                    $arResult['WEBSERVICE_WSCRM_PAYMENT_LIST'] = $unPaymentList;
                }
            }

            // get custom fields
            if ($customFields = Option::get($this->MODULE_ID, $this->CUSTOM_FIELDS_OPTION)) {
                $arResult['WEBSERVICE_WSCRM_CUSTOM_FIELDS'] = unserialize($customFields);
            }

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('WEBSERVICE_WSCRM_INSTALL_TITLE'), $this->getInstallPath() . '/step1.php'
            );

        } elseif ($step == 2) {
            $apiUrl = $request->getPost($this->API_URL_OPTION);
            $apiKey = $request->getPost($this->API_KEY_OPTION);
            $apiOrderPrefix = $request->getPost($this->API_ORDER_PREFIX_OPTION);

            if (! $apiUrl || ! $apiKey || ! $apiOrderPrefix) {
                $APPLICATION->ThrowException(Loc::getMessage('WEBSERVICE_WSCRM_PARAMS_REQUIRED'));

                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage('WEBSERVICE_WSCRM_INSTALL_TITLE'), $this->getInstallPath() . '/step1.php'
                );

                return;
            } else {
                Option::set($this->MODULE_ID, $this->API_URL_OPTION, $apiUrl);
                Option::set($this->MODULE_ID, $this->API_KEY_OPTION, $apiKey);
                Option::set($this->MODULE_ID, $this->API_ORDER_PREFIX_OPTION, $apiOrderPrefix);
            }

            // bitrix delivery services
            $arResult['bDeliveryList'] = Action::getDeliveryList();
            $arDeliveryServices = [];
            foreach ($arResult['bDeliveryList'] as $bDelivery) {
                $arDeliveryServices[$bDelivery['ID']] = $request->getPost('delivery-service-' . $bDelivery['ID']);
            }

            // bitrix payment services
            $arResult['bPaymentList'] = Action::getPaymentList();
            $arPaymentServices = [];
            foreach ($arResult['bPaymentList'] as $bPayment) {
                $arPaymentServices[$bPayment['ID']] = $request->getPost('payment-service-' . $bPayment['ID']);
            }

            // bitrix order types
            $bOrderTypesList = Action::getOrderTypesList();
            $arResult['customFields'] = Action::getCustomFields();

            // crm custom fields
            $arCustomFields = [];
            foreach ($bOrderTypesList as $bitrixOrderType) {
                $_arOrderTypeCustomFields = [];
                foreach ($arResult['customFields'] as $customField) {
                    $_arOrderTypeCustomFields[$customField['code']] = $request->getPost('custom-field-' . $customField['code'] . '-' . $bitrixOrderType['ID']);
                }
                $arCustomFields[$bitrixOrderType['ID']] = $_arOrderTypeCustomFields;
            }

            // save payment and delivery option list
            Option::set($this->MODULE_ID, $this->DELIVERY_LIST_OPTION, serialize($arDeliveryServices));
            Option::set($this->MODULE_ID, $this->PAYMENT_LIST_OPTION, serialize($arPaymentServices));
            Option::set($this->MODULE_ID, $this->CUSTOM_FIELDS_OPTION, serialize($arCustomFields));

            if (! ModuleManager::isModuleInstalled($this->MODULE_ID)) {
                $this->InstallDB();
                $this->InstallEvents();
                $this->InstallFiles();

                // register module
                ModuleManager::registerModule($this->MODULE_ID);
            }

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('WEBSERVICE_WSCRM_INSTALL_TITLE'), $this->getInstallPath() . '/step2.php'
            );
        }
    }

    /**
     * Method DoUninstall is required
     * @return void
     */
    public function DoUninstall()
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            // unregister module
            ModuleManager::UnRegisterModule($this->MODULE_ID);

            $this->UnInstallFiles();
            $this->UnInstallEvents();
            $this->UnInstallDB();
        }
    }

    /**
     * @override CModule::InstallDB
     */
    public function InstallDB()
    {
        RegisterModuleDependences('sale', 'OnSaleOrderSaved', $this->MODULE_ID, '\Webservice\Wscrm\Event', 'OnSaleOrderSaved');

        return true;
    }

    /**
     * @override CModule::UnInstallDB
     */
    public function UnInstallDB()
    {
        UnRegisterModuleDependences('sale', 'OnSaleOrderSaved', $this->MODULE_ID, '\Webservice\Wscrm\Event', 'OnSaleOrderSaved');

        return true;
    }

    /**
     * @override CModule::InstallEvents
     */
    public function InstallEvents()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function InstallFiles()
    {
        // check where module locate
        $moduleDir = (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/export'))
            ? 'bitrix' : 'local';

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/'. $moduleDir. '/modules/' . $this->MODULE_ID . '/install/export',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/catalog_export',
            true,
            true
        );
    }

    public function UnInstallFiles()
    {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/catalog_export/webservice_run.php');
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/catalog_export/webservice_setup.php');
    }

    /**
     * Get the module install path
     * @return string
     */
    private function getInstallPath()
    {
        return $this->INSTALL_PATH;
    }

    /**
     * Check bitrix main module version
     * @return boolean
     */
    private function correctBitrixVersion()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

}