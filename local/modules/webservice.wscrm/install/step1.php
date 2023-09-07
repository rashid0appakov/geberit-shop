<?php

use Bitrix\Main\Localization\Loc;

if ( !check_bitrix_sessid()) {
    return;
}

if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage([
        'TYPE'    => 'ERROR',
        'MESSAGE' => Loc::getMessage('MOD_INST_ERR'),
        'DETAILS' => $ex->GetString(),
        'HTML'    => true
    ]);
}
?>

<div class="adm-detail-content-item-block">
<form action="<?php echo $APPLICATION->GetCurPage() ?>" method="POST">
    <?php echo bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<?php echo LANGUAGE_ID ?>">
    <input type="hidden" name="id" value="webservice.wscrm">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">

    <table class="adm-detail-content-table edit-table" id="edit1_edit_table">
        <tbody>
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

            <?php foreach($arResult['bDeliveryList'] as $bDelivery): ?>
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
                        <td colspan="2" style="background: transparent;"><b><?php echo Loc::getMessage('WEBSERVICE_WSCRM_CUSTOM_FIELDS'); ?></b></td>
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
        </tbody>
    </table>
    <br />
    <div style="padding: 1px 13px 2px; height:28px;">
        <div align="right" style="float:right; position:relative;">
            <input type="submit" name="inst" value="<?php echo Loc::getMessage("WEBSERVICE_WSCRM_SAVE_TEXT"); ?>" class="adm-btn-save">
        </div>
    </div>
</form>
</div>