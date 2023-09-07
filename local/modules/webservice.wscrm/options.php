<?php
/*
    from https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2823&LESSON_PATH=3913.4609.2823

    options.php - данный файл подключается на странице настройки параметров модулей в административном меню Настройки;
 */

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Iblock;
use Bitrix\Catalog;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String as BitrixString;
use Webservice\Wscrm\Action;

$moduleOptions = [
    'MODULE_ID'                 => 'webservice.wscrm',
    'API_KEY_OPTION'            => 'key',
    'API_URL_OPTION'            => 'url',
    'API_ORDER_PREFIX_OPTION'   => 'order_prefix',
    'PAYMENT_LIST_OPTION'       => 'payment_services',
    'DELIVERY_LIST_OPTION'      => 'delivery_services',
    'CUSTOM_FIELDS_OPTION'      => 'custom_fields',
    'PRODUCT_PROPERTIES_OPTION' => 'product_properties',
    'ONECLICK_EMAIL'            => 'oneclick_email'
];

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

if (! Loader::includeModule($mid) || ! Loader::includeModule('sale') || ! Loader::includeModule('catalog') || ! Loader::includeModule('iblock')) {
    return false;
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV"   => "edit1",
        "TAB"   => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
    array(
        "DIV"   => "edit2",
        "TAB"   => Loc::getMessage("WEBSERVICE_WSCRM_SITE_API"),
        "TITLE" => Loc::getMessage("WEBSERVICE_WSCRM_SITE_API_TITLE")
    ),
    array(
        "DIV"   => "edit3",
        "TAB"   => Loc::getMessage("WEBSERVICE_WSCRM_PRODUCTS_TAB_SET"),
        "TITLE" => Loc::getMessage("WEBSERVICE_WSCRM_PRODUCTS_TAB_TITLE_SET")
    ),
));

$productProperties = Action::getCrmProductProperties();
$arResult = [
    'bDeliveryList'        => Action::getDeliveryList(),
    'deliveryList'         => Action::getCrmDeliveryList(),
    'bPaymentList'         => Action::getPaymentList(),
    'paymentList'          => Action::getCrmPaymentList(),
    'bOrderPropertiesList' => Action::getOrderPropsList(),
    'bOrderTypesList'      => Action::getOrderTypesList(),
    'customFields'         => Action::getCustomFields(),
    'arSites'              => Action::getSitesList()
];

foreach ($productProperties as $key => $value) {
    $arResult['productProperties'][$key] = Loc::getMessage('WEBSERVICE_WSCRM_PRODUCT_PROPERTY_' . strtoupper($key));
}

/*
    D - инфоблок является торговым каталогом
    O - инфоблок содержит торговые предложения (SKU)
    P - инфоблок товаров, имеющих торговые предложения, но сам торговым каталогом не является
    X - инфоблок товаров, имеющих торговые предложения, при это сам инфоблок тоже является торговым каталогом.
    Если метод возвращает false - инфоблок не существует или не задействован в модуле Торговый каталог.
 */
$catalogIblocks = [];
$dbCatalogIblock = Catalog\CatalogIblockTable::getList();
while ($catalogIblock = $dbCatalogIblock->fetch()) {
    $catalogIblocks[$catalogIblock['IBLOCK_ID']] = $catalogIblock;
}

// get all properties from iblock with catalog type
$iblocksProperties = [];
foreach ($catalogIblocks as $catalogIblock) {
    // get iblock
    $iblock = Iblock\IblockTable::getById($catalogIblock['IBLOCK_ID'])->fetch();
    $iblocksProperties[$iblock['ID']] = $iblock;

    // product properties for iblock
    $iblockPropertiesDb = Iblock\PropertyTable::getList([
        'order'  => ['SORT' => 'ASC', 'NAME' => 'ASC'],
        'filter' => ['=IBLOCK_ID' => $iblock['ID']],
    ]);

    $productProperties = null;
    while ($iblockProperty = $iblockPropertiesDb->fetch()) {
        $productProperties[] = $iblockProperty;
    }

    if ($catalogIblock['SKU_PROPERTY_ID'] > 0 && $catalogIblock['PRODUCT_IBLOCK_ID'] > 0) {
        if (array_key_exists($catalogIblock['PRODUCT_IBLOCK_ID'], $iblocksProperties)) {
            $iblocksProperties[$catalogIblock['PRODUCT_IBLOCK_ID']]['PRODUCT_SKU_PROPERTIES'] = $productProperties;
        }
        unset($iblocksProperties[$iblock['ID']]);
        break;
    }

    // add product properties to iblock
    $iblocksProperties[$iblock['ID']]['PRODUCT_PROPERTIES'] = $productProperties;
}
$arResult['iblocksProperties'] = $iblocksProperties;

// if form was send with post request
// check and save settings
if ($request->isPost() && check_bitrix_sessid()) {
    $apiUrl = $request->getPost($moduleOptions['API_URL_OPTION']);
    $apiKey = $request->getPost($moduleOptions['API_KEY_OPTION']);
    $apiOrderPrefix = $request->getPost($moduleOptions['API_ORDER_PREFIX_OPTION']);
    if (! $apiUrl || ! $apiKey || ! $apiOrderPrefix) {
        CAdminMessage::ShowMessage(Loc::getMessage('WEBSERVICE_WSCRM_PARAMS_REQUIRED'));
    } else {
        if (!empty($arResult['arSites']))
            foreach($arResult['arSites'] AS $arSite){
                $siteApiKey = $request->getPost($moduleOptions['API_KEY_OPTION'].'_'.$arSite['LID']);
                $siteApiOrderPrefix = $request->getPost($moduleOptions['API_ORDER_PREFIX_OPTION'].'_'.$arSite['LID']);
                if ($siteApiKey)
                    Option::set($mid, $moduleOptions['API_KEY_OPTION'].'_'.$arSite['LID'], $siteApiKey);
                if ($siteApiOrderPrefix)
                    Option::set($mid, $moduleOptions['API_ORDER_PREFIX_OPTION'].'_'.$arSite['LID'], $siteApiOrderPrefix);

                $oneClickEmail  = $request->getPost($moduleOptions['ONECLICK_EMAIL'].'_'.$arSite['LID']);
                Option::set($mid, $moduleOptions['ONECLICK_EMAIL'].'_'.$arSite['LID'], $oneClickEmail);
            }

        Option::set($mid, $moduleOptions['API_URL_OPTION'], $apiUrl);
        Option::set($mid, $moduleOptions['API_KEY_OPTION'], $apiKey);
        Option::set($mid, $moduleOptions['API_ORDER_PREFIX_OPTION'], $apiOrderPrefix);

        // bitrix delivery services
        $arDeliveryServices = [];
        foreach ($arResult['bDeliveryList'] as $bDelivery) {
            $arDeliveryServices[$bDelivery['ID']] = $request->getPost('delivery-service-' . $bDelivery['ID']);
        }

        // bitrix payment services
        $arPaymentServices = [];
        foreach ($arResult['bPaymentList'] as $bPayment) {
            $arPaymentServices[$bPayment['ID']] = $request->getPost('payment-service-' . $bPayment['ID']);
        }

        // crm custom fields
        $arCustomFields = [];
        foreach ($arResult['bOrderTypesList'] as $bitrixOrderType) {
            $_arOrderTypeCustomFields = [];
            foreach ($arResult['customFields'] as $customField) {
                $_arOrderTypeCustomFields[$customField['code']] = $request->getPost('custom-field-' . $customField['code'] . '-' . $bitrixOrderType['ID']);
            }
            $arCustomFields[$bitrixOrderType['ID']] = $_arOrderTypeCustomFields;
        }

        // mapping for product properties
        $arProductProperties = [];
        foreach ($arResult['productProperties'] as $key => $property) {
            foreach ($request->getPost('PRODUCT_PROPERTIES_' . $key) as $iblock => $value) {
                $arProductProperties[$iblock][$key] = $value;
            }
        }

        // mapping for sku product properties
        $arSkuProductProperties = [];
        foreach ($arResult['productProperties'] as $key => $property) {
            foreach ($request->getPost('PRODUCT_SKU_PROPERTIES_' . $key) as $iblock => $value) {
                $arSkuProductProperties[$iblock][$key] = $value;
            }
        }

        // save payment and delivery option list
        Option::set($mid, $moduleOptions['DELIVERY_LIST_OPTION'], serialize($arDeliveryServices));
        Option::set($mid, $moduleOptions['PAYMENT_LIST_OPTION'], serialize($arPaymentServices));
        Option::set($mid, $moduleOptions['CUSTOM_FIELDS_OPTION'], serialize($arCustomFields));
        Option::set($mid, $moduleOptions['PRODUCT_PROPERTIES_OPTION'], serialize($arProductProperties));

        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("WEBSERVICE_WSCRM_SUCCESS_SAVE"),
            "TYPE" => "OK",
        ));
    }
}

$arResult['WEBSERVICE_WSCRM_API_URL'] = Option::get($mid, $moduleOptions['API_URL_OPTION'], Option::get($mid, 'default_url'));
$arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX'] = Option::get($mid, $moduleOptions['API_ORDER_PREFIX_OPTION']);
$arResult['WEBSERVICE_WSCRM_API_KEY'] = Option::get($mid, $moduleOptions['API_KEY_OPTION']);

if (!empty($arResult['arSites']))
    foreach($arResult['arSites'] AS $arSite){
        $arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX_'.$arSite['LID']] = Option::get($mid, $moduleOptions['API_ORDER_PREFIX_OPTION'].'_'.$arSite['LID']);
        $arResult['WEBSERVICE_WSCRM_API_KEY_'.$arSite['LID']] = Option::get($mid, $moduleOptions['API_KEY_OPTION'].'_'.$arSite['LID']);
        $arResult['WEBSERVICE_WSCRM_ONECLICK_EMAIL_'.$arSite['LID']] = Option::get($mid, $moduleOptions['ONECLICK_EMAIL'].'_'.$arSite['LID']);
    }

$arResult['WEBSERVICE_WSCRM_DELIVERY_LIST'] = unserialize(Option::get($mid, $moduleOptions['DELIVERY_LIST_OPTION']));
$arResult['WEBSERVICE_WSCRM_PAYMENT_LIST'] = unserialize(Option::get($mid, $moduleOptions['PAYMENT_LIST_OPTION']));
$arResult['WEBSERVICE_WSCRM_CUSTOM_FIELDS'] = unserialize(Option::get($mid, $moduleOptions['CUSTOM_FIELDS_OPTION']));
$arResult['WEBSERVICE_WSCRM_PRODUCT_PROPERTIES'] = unserialize(Option::get($mid, $moduleOptions['PRODUCT_PROPERTIES_OPTION']));

$tabControl->begin();
?>

<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
        echo bitrix_sessid_post();
        $tabControl->beginNextTab();
    ?>

    <tr class="heading">
        <td colspan="2"><b><?php echo Loc::getMessage('INFO_1');?></b> (<?php echo Loc::getMessage('INFO_2'); ?>)</td>
    </tr>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('WEBSERVICE_WSCRM_API_URL'); ?></td>
        <td width="50%" class="adm-detail-content-cell-r"><input type="text" id="url" name="url" value="<?php if(isset($arResult['WEBSERVICE_WSCRM_API_URL'])) echo $arResult['WEBSERVICE_WSCRM_API_URL'];?>"></td>
    </tr>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('WEBSERVICE_WSCRM_API_KEY'); ?></td>
        <td width="50%" class="adm-detail-content-cell-r"><input type="text" id="key" name="key" value="<?php if(isset($arResult['WEBSERVICE_WSCRM_API_KEY'])) echo $arResult['WEBSERVICE_WSCRM_API_KEY'];?>"></td>
    </tr>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l"><?php echo Loc::getMessage('WEBSERVICE_WSCRM_API_ORDER_PREFIX'); ?></td>
        <td width="50%" class="adm-detail-content-cell-r"><input type="text" id="order_prefix" name="order_prefix" value="<?php if(isset($arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX'])) echo $arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX'];?>"></td>
    </tr>

    <tr class="heading">
        <td colspan="2"><b><?php echo Loc::getMessage('WEBSERVICE_WSCRM_DELIVERY_LIST'); ?></b></td>
    </tr>

    <?php foreach($arResult['bDeliveryList'] AS $bDelivery): ?>
    <tr class="delivery-types">
        <td width="50%" class="adm-detail-content-cell-l" name="<?php echo $bDelivery['ID']; ?>">
    <?php echo $bDelivery['NAME']; ?>
        </td>
        <td width="50%" class="adm-detail-content-cell-r">
            <select name="delivery-service-<?php echo $bDelivery['ID']; ?>" class="typeselect">
                <option value=""></option>
                <?php foreach($arResult['deliveryList'] as $delivery): ?>
                <option value="<?php echo $APPLICATION->ConvertCharset($delivery['name'], 'utf-8', SITE_CHARSET); ?>"
                    <?php if($arResult['WEBSERVICE_WSCRM_DELIVERY_LIST'][$bDelivery['ID']] === $delivery['name']) echo 'selected'; ?>>
                    <?php echo $APPLICATION->ConvertCharset($delivery['name'], 'utf-8', SITE_CHARSET); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php endforeach; ?>

    <tr class="heading">
        <td colspan="2"><b><?php echo Loc::getMessage('WEBSERVICE_WSCRM_PAYMENT_LIST'); ?></b></td>
    </tr>

    <?php foreach($arResult['bPaymentList'] as $bPayment): ?>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l" name="<?php echo $bPayment['ID']; ?>">
        <?php echo $bPayment['NAME']; ?>
        </td>
        <td width="50%" class="adm-detail-content-cell-r">
            <select name="payment-service-<?php echo $bPayment['ID']; ?>" class="typeselect">
                <option value=""></option>
                <?php foreach($arResult['paymentList'] as $payment): ?>
                <option value="<?php echo $APPLICATION->ConvertCharset($payment['name'], 'utf-8', SITE_CHARSET); ?>"
                    <?php if($arResult['WEBSERVICE_WSCRM_PAYMENT_LIST'][$bPayment['ID']] === $payment['name']) echo 'selected'; ?>>
                    <?php echo $APPLICATION->ConvertCharset($payment['name'], 'utf-8', SITE_CHARSET); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php endforeach; ?>

    <?php foreach ($arResult['bOrderTypesList'] as $bitrixOrderType) { ?>
        <tr class="heading">
            <td colspan="2"><b><?php echo Loc::getMessage('WEBSERVICE_WSCRM_ORDER_TYPE_INFO') . ' ' . $bitrixOrderType['NAME']; ?></b></td>
        </tr>

        <?php if (isset($arResult['customFields']) && count($arResult['customFields']) > 0) {?>
            <tr class="heading">
                <td colspan="2"><b><?php echo Loc::getMessage('WEBSERVICE_WSCRM_CUSTOM_FIELDS'); ?></b></td>
            </tr>

            <?php foreach($arResult['customFields'] as $field) { ?>
            <tr>
                <td width="50%" class="adm-detail-content-cell-l" name="<?php echo $field['name']; ?>">
                    <?php echo $field['name']; ?>
                </td>
                <td width="50%" class="adm-detail-content-cell-r">
                    <select name="custom-field-<?php echo $field['code'];?>-<?php echo $bitrixOrderType['ID']?>" class="typeselect">
                        <option value=""></option>
                        <?php foreach($arResult['bOrderPropertiesList'][$bitrixOrderType['ID']] as $prop): ?>
                        <option value="<?php echo $APPLICATION->ConvertCharset($prop['CODE'], 'utf-8', SITE_CHARSET); ?>"
                            <?php if($arResult['WEBSERVICE_WSCRM_CUSTOM_FIELDS'][$bitrixOrderType['ID']][$field['code']] === $prop['CODE']) echo 'selected'; ?>>
                            <?php echo $APPLICATION->ConvertCharset($prop['NAME'], 'utf-8', SITE_CHARSET); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <? } ?>

        <? } ?>
    <? } ?>

    <?php $tabControl->BeginNextTab(); ?>
        <? if (!empty($arResult['arSites'])):
            foreach($arResult['arSites'] AS $arSite):
        ?>
        <tr class="heading">
            <td colspan="2"><b><?=Loc::getMessage('WEBSERVICE_WSCRM_SITE_SETTINGS') . ': ' . $arSite['SERVER_NAME']; ?></b></td>
        </tr>
        <tr>
            <td width="50%" class="adm-detail-content-cell-l"><?=Loc::getMessage('WEBSERVICE_WSCRM_API_KEY'); ?></td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input type="text" id="key_<?=$arSite['LID']?>" name="key_<?=$arSite['LID']?>" value="<?php if (isset($arResult['WEBSERVICE_WSCRM_API_KEY_'.$arSite['LID']])) echo $arResult['WEBSERVICE_WSCRM_API_KEY_'.$arSite['LID']];?>" />
            </td>
        </tr>
        <tr>
            <td width="50%" class="adm-detail-content-cell-l"><?=Loc::getMessage('WEBSERVICE_WSCRM_API_ORDER_PREFIX'); ?></td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input type="text" id="order_prefix_<?=$arSite['LID']?>" name="order_prefix_<?=$arSite['LID']?>" value="<?php if(isset($arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX_'.$arSite['LID']])) echo $arResult['WEBSERVICE_WSCRM_API_ORDER_PREFIX_'.$arSite['LID']];?>" />
            </td>
        </tr>
        <tr>
            <td width="50%" class="adm-detail-content-cell-l"><?=Loc::getMessage('WEBSERVICE_ONCLICK_EMAIL'); ?></td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input type="text" id="oneclick_email_<?=$arSite['LID']?>" name="oneclick_email_<?=$arSite['LID']?>" value="<?php if (isset($arResult['WEBSERVICE_WSCRM_ONECLICK_EMAIL_'.$arSite['LID']])) echo $arResult['WEBSERVICE_WSCRM_ONECLICK_EMAIL_'.$arSite['LID']];?>" />
            </td>
        </tr>
        <?
            endforeach;
        else:?>
        <p><strong><?=Loc::getMessage('WEBSERVICE_WSCRM_NO_SITES')?></strong></p>
        <? endif;?>

    <?php $tabControl->BeginNextTab(); ?>
    <?php
    foreach ($arResult['iblocksProperties'] AS $iblock) { ?>
        <tr class="heading">
            <td colspan="2">
                <b>[<?=$iblock['IBLOCK_TYPE_ID']?>]</b>
                <b><?=$iblock['NAME']?></b>
            </td>
        </tr>
        <tr>
            <td>
                <table class="adm-list-table">
                    <thead>
                        <tr class="adm-list-table-header">
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner"><?=Loc::getMessage("WEBSERVICE_WSCRM_PRODUCT_PROPERTIES_HEADER_MAIN");?></div>
                            </td>
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner"><?=Loc::getMessage("WEBSERVICE_WSCRM_PRODUCT_PROPERTIES_HEADER_PRODUCT");?></div>
                            </td>
                            <? if ($iblock['PRODUCT_SKU_PROPERTIES'] != null) { ?>
                                <td class="adm-list-table-cell">
                                    <div class="adm-list-table-cell-inner"><?=Loc::getMessage("WEBSERVICE_WSCRM_PRODUCT_PROPERTIES_HEADER_SKU");?></div>
                                </td>
                            <? } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach($arResult['productProperties'] as $key => $prop) { ?>
                           <tr class="adm-list-table-row">
                               <td class="adm-list-table-cell"><?=$prop?></td>
                               <td class="adm-list-table-cell">
                                   <select name="PRODUCT_PROPERTIES_<?=$key?>[<?=$iblock['ID']?>]">
                                       <option value=""></option>
                                       <? foreach($iblock['PRODUCT_PROPERTIES'] as $iblockProductProperty) { ?>
                                           <option
                                                value="<?=$iblockProductProperty['CODE']?>"
                                                <? if (isset($arResult['WEBSERVICE_WSCRM_PRODUCT_PROPERTIES']) &&
                                                    $arResult['WEBSERVICE_WSCRM_PRODUCT_PROPERTIES'][$iblock['ID']][$key] === $iblockProductProperty['CODE']) { ?>
                                                    selected="selected"
                                                <? } ?>
                                            >
                                                <?php echo $iblockProductProperty['NAME']?>
                                           </option>
                                       <? } ?>
                                   </select>
                               </td>
                               <? if ($iblock['PRODUCT_SKU_PROPERTIES'] != null) { ?>
                                   <td class="adm-list-table-cell">
                                       <select name="PRODUCT_SKU_PROPERTIES_<?=$key?>[<?=$iblock['ID']?>]">
                                           <option value=""></option>
                                           <? foreach($iblock['PRODUCT_SKU_PROPERTIES'] as $iblockProductSKUProperty) { ?>
                                               <option value="<?=$iblockProductSKUProperty['ID']?>"><?=$iblockProductSKUProperty['NAME']?></option>
                                           <? } ?>
                                       </select>
                                   </td>
                               <? } ?>
                           </tr>
                        <? } ?>
                   </tbody>
               </table>
            </td>
        </tr>
    <? } ?>

    <?php $tabControl->buttons(); ?>
    <input type="submit"
           name="save"
           value="<?=Loc::getMessage("MAIN_SAVE") ?>"
           title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
           />
    <?php $tabControl->end(); ?>

</form>