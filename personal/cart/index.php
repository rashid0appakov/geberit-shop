<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Geberit Shop");
$APPLICATION->SetPageProperty("description", "Личный кабинет Geberit Shop - оформление заказа, корзина");
$APPLICATION->SetPageProperty("title", "Личный кабинет Geberit Shop");
$APPLICATION->SetTitle("Моя корзина");
?>
<? $APPLICATION->IncludeComponent ( "bitrix:sale.basket.basket",
                                    "restyle",
                                    array (
                                        "COLUMNS_LIST"                  => array (
                                            0 => "NAME",
                                            1 => "DISCOUNT",
                                            2 => "PROPS",
                                            3 => "DELETE",
                                            4 => "DELAY",
                                            5 => "PRICE",
                                            6 => "QUANTITY",
                                            7 => "SUM",
                                        ),
                                        "PATH_TO_ORDER"                 => "/personal/order/make/",
                                        "HIDE_COUPON"                   => "N",
                                        "PRICE_VAT_SHOW_VALUE"          => "N",
                                        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
                                        "USE_PREPAYMENT"                => "N",
                                        "QUANTITY_FLOAT"                => "Y",
                                        "SET_TITLE"                     => "Y",
                                        "ACTION_VARIABLE"               => "action",
                                        "IBLOCK_TYPE"                   => CATALOG_IBLOCK_TYPE,
                                        "IBLOCK_ID"                     => CATALOG_IBLOCK_ID,
                                        "OFFERS_FIELD_CODE"             => "",
                                        "OFFERS_PROPERTY_CODE"          => array (
                                            0 => "COLOR",
                                            1 => "PROP2",
                                            2 => "PROP3",
                                        ),
                                        "OFFERS_SORT_FIELD"             => "sort",
                                        "OFFERS_SORT_ORDER"             => "asc",
                                        "OFFERS_SORT_FIELD2"            => "id",
                                        "OFFERS_SORT_ORDER2"            => "asc",
                                        "OFFERS_LIMIT"                  => "",
                                        "PRICE_CODE"                    => array ( 0 => "BASE", ),
                                        "PRICE_VAT_INCLUDE"             => "Y",
                                        "CONVERT_CURRENCY"              => "N",
                                        "OFFERS_CART_PROPERTIES"        => array (
                                            0 => "COLOR",
                                            1 => "PROP2",
                                            2 => "PROP3",
                                        ),
                                        "DISPLAY_IMG_WIDTH"             => "178",
                                        "DISPLAY_IMG_HEIGHT"            => "178",
                                        "ELEMENT_SORT_FIELD"            => "RAND",
                                        "ELEMENT_SORT_ORDER"            => "ASC",
                                        "DISPLAY_COMPARE"               => "Y",
                                        "PROPERTY_CODE_MOD"             => array ( 0 => "GUARANTEE", ),
                                        "HIDE_NOT_AVAILABLE"            => "N",
                                        "USE_BIG_DATA"                  => "Y",
                                        "BIG_DATA_RCM_TYPE"             => "any",
                                        "COMPONENT_TEMPLATE"            => "restyle",
                                        "CORRECT_RATIO"                 => "Y",
                                        "AUTO_CALCULATION"              => "Y",
                                        "USE_GIFTS"                     => "N",
                                        "DEFERRED_REFRESH"              => "N",
                                        "USE_DYNAMIC_SCROLL"            => "Y",
                                        "SHOW_FILTER"                   => "Y",
                                        "SHOW_RESTORE"                  => "Y",
                                        "COLUMNS_LIST_EXT"              => array (
                                            0 => "PREVIEW_PICTURE",
                                            1 => "DISCOUNT",
                                            2 => "DELETE",
                                            3 => "DELAY",
                                            4 => "TYPE",
                                            5 => "SUM",
                                        ),
                                        "COLUMNS_LIST_MOBILE"           => array (
                                            0 => "PREVIEW_PICTURE",
                                            1 => "DISCOUNT",
                                            2 => "DELETE",
                                            3 => "DELAY",
                                            4 => "TYPE",
                                            5 => "SUM",
                                        ),
                                        "TEMPLATE_THEME"                => "blue",
                                        "TOTAL_BLOCK_DISPLAY"           => array ( 0 => "top", ),
                                        "DISPLAY_MODE"                  => "extended",
                                        "PRICE_DISPLAY_MODE"            => "Y",
                                        "SHOW_DISCOUNT_PERCENT"         => "Y",
                                        "DISCOUNT_PERCENT_POSITION"     => "bottom-right",
                                        "PRODUCT_BLOCKS_ORDER"          => "props,sku,columns",
                                        "USE_PRICE_ANIMATION"           => "Y",
                                        "LABEL_PROP"                    => array (),
                                        "COMPATIBLE_MODE"               => "Y",
                                        "LABEL_PROP_MOBILE"             => "",
                                        "LABEL_PROP_POSITION"           => "",
                                        "OFFERS_PROPS"                  => array (),
                                        "ADDITIONAL_PICT_PROP_15"       => "-",
                                        "ADDITIONAL_PICT_PROP_16"       => "-",
                                        "BASKET_IMAGES_SCALING"         => "adaptive",
                                        "USE_ENHANCED_ECOMMERCE"        => "N",
                                        "OUTSIDE_MKAD_PRICE"            => 30
                                    ),
                                    false
); ?>
<? if ($GLOBALS['_SHOW_FORM_ORDER']) { ?>
    <div class="cart">
        <div class="categoryWrapper">
            <div class="container goods__container">
                <div class="wrapper">
                    <div class="cart" id="basket-items-list-wrapper">
                        <div id="make-order">
                            <section class="tabs cartTabs">
                                <ul class="tabs__header" id="tabs__header-1">
                                    <li id="tabExpress" class="tabs__header--title js-tabs-title active" data-tab="#express">Экспресс оформление</li>
                                    <li id="tabHimself" class="tabs__header--title js-tabs-title" data-tab="#himself">Оформить самостоятельно</li>
                                </ul>
                                <div class="tabs__underline js-tabs-underline"></div>
                            </section>
                            <div class="tabs__content js-tabs-content active" id="express">
                                <div class="express">
                                    <div>
                                        <p>Мы ценим ваше время!</p>
                                        <p>Введите только номер телефона. Адрес, удобную дату доставки и способ оплаты вы сможете согласовать с менеджером по телефону.</p>
                                    </div>
                                    <form onsubmit="return false;" id="buyOnClick">
                                        <div class="columns">
                                            <div class="column">
                                                <span class="sevenNum">+7</span>
                                            </div>
                                            <div class="column shortNum">
                                                <div>
                                                    <input class="phone-input" name="phone-input-code" type="text" size="3" value="" onkeyup="this.value=this.value.replace(/[^\d]|^7|^8/,'')" maxlength="3" >
                                                </div>
                                            </div>
                                            <div class="column longNum">
                                                <div>
                                                    <input class="phone-input" name="phone-input-phone" type="text" size="7" value="" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="column submitExpressForm">
                                                <div><input type="submit"></div>
                                            </div>
                                        </div>
                                        <script>
                                            $(function(){
                                                // Заказ в 1 клик в корзине
                                                $(document).on('click', '#buyOnClick input[type=submit]', function () {
                                                    var $error = false,
                                                        $form = $('#buyOnClick'),
                                                        $phoneCode = $form.find('[name=phone-input-code]'),
                                                        $phonePhone = $form.find('[name=phone-input-phone]');
                                                    if ($phoneCode.val() == '' || $phoneCode.val().length!=$phoneCode.attr('size')){
                                                        $phoneCode.addClass('is-invalid-input');
                                                        $error = true;
                                                    } else {
                                                        $phoneCode.removeClass('is-invalid-input');
                                                    }
                                                    if ($phonePhone.val() == '' || $phonePhone.val().length!=$phonePhone.attr('size')){
                                                        $phonePhone.addClass('is-invalid-input');
                                                        $error = true;
                                                    } else {
                                                        $phonePhone.removeClass('is-invalid-input');
                                                    }
                                                    if ($error){
                                                        return false;
                                                    }
                                                    var _ajax = $('#buyOnClick').data('ajax');
                                                    if (_ajax) {
                                                        _ajax.abort();
                                                    }
                                                    ym(23796220,'reachGoal','express_buy');
                                                    _ajax = $.ajax({
                                                        url: '/ajax/order.php',
                                                        type: "POST",
                                                        data: {
                                                            code: $phoneCode.val(),
                                                            phone: $phonePhone.val()
                                                        }
                                                    }).done(function (data) {
                                                        if (data != ''){
                                                            $('#buyOnClick').html(data);
                                                        }
                                                    });
                                                    $('#buyOnClick').data('ajax', _ajax);
                                                    return false;
                                                });
                                            });
                                        </script>
                                        <style>
                                            .is-invalid-input {
                                                border: 1px solid red !important;
                                            }
                                        </style>
                                    </form>
                                    <p class="weCallYou"><?=GetMessage('CT_MANAGER_CALLBACK_TEXT')?></p>
                                </div>
                            </div>
                            <div class="tabs__content js-tabs-content" id="himself">
                                <?CModule::IncludeModule("sale");CModule::IncludeModule("catalog");?>
                                <? $APPLICATION->IncludeComponent ( "bitrix:sale.order.ajax",
                                                                    "restyled",
                                                                    Array (
                                                                        "ADDITIONAL_PICT_PROP_8"         => "-",
                                                                        "ALLOW_AUTO_REGISTER"            => "Y",
                                                                        "ALLOW_APPEND_ORDER"             => "Y",
                                                                        "ALLOW_NEW_PROFILE"              => "Y",
                                                                        "ALLOW_USER_PROFILES"            => "N",
                                                                        "BASKET_IMAGES_SCALING"          => "standard",
                                                                        "BASKET_POSITION"                => "after",
                                                                        "COMPATIBLE_MODE"                => "Y",
                                                                        "DELIVERIES_PER_PAGE"            => "8",
                                                                        "DELIVERY_FADE_EXTRA_SERVICES"   => "Y",
                                                                        "DELIVERY_NO_AJAX"               => "Y",
                                                                        "DELIVERY_NO_SESSION"            => "Y",
                                                                        "DELIVERY_TO_PAYSYSTEM"          => "d2p",
                                                                        "DISABLE_BASKET_REDIRECT"        => "Y",
                                                                        "MESS_DELIVERY_CALC_ERROR_TEXT"  => "Вы можете продолжить оформление заказа, а чуть позже менеджер магазина
свяжется с вами и уточнит информацию по доставке.",
                                                                        "MESS_DELIVERY_CALC_ERROR_TITLE" => "Не удалось рассчитать стоимость доставки.",
                                                                        "MESS_FAIL_PRELOAD_TEXT"         => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически.
Обратите внимание на развернутый блок с информацией о заказе. Здесь вы можете внести необходимые изменения или оставить
как есть и нажать кнопку \"#ORDER_BUTTON#\".",
                                                                        "MESS_SUCCESS_PRELOAD_TEXT"      => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные
автоматически. Если все заполнено верно, нажмите кнопку \"#ORDER_BUTTON#\".",
                                                                        "ONLY_FULL_PAY_FROM_ACCOUNT"     => "N",
                                                                        "PATH_TO_AUTH"                   => "/auth/",
                                                                        "PATH_TO_BASKET"                 => "/personal/cart/empty.php",
                                                                        "PATH_TO_PAYMENT"                => "payment.php",
                                                                        "PATH_TO_PERSONAL"               => "index.php",
                                                                        "PAY_FROM_ACCOUNT"               => "Y",
                                                                        "PAY_SYSTEMS_PER_PAGE"           => "8",
                                                                        "PICKUPS_PER_PAGE"               => "5",
                                                                        "PRODUCT_COLUMNS_HIDDEN"         => array ( "PROPERTY_MATERIAL" ),
                                                                        "PRODUCT_COLUMNS_VISIBLE"        => array (
                                                                            "PREVIEW_PICTURE",
                                                                            "PROPS"
                                                                        ),
                                                                        "PROPS_FADE_LIST_1"              => array (
                                                                            "17",
                                                                            "19"
                                                                        ),
                                                                        "SEND_NEW_USER_NOTIFY"           => "Y",
                                                                        "SERVICES_IMAGES_SCALING"        => "standard",
                                                                        "SET_TITLE"                      => "Y",
                                                                        "SHOW_BASKET_HEADERS"            => "N",
                                                                        "SHOW_COUPONS_BASKET"            => "Y",
                                                                        "SHOW_COUPONS_DELIVERY"          => "Y",
                                                                        "SHOW_COUPONS_PAY_SYSTEM"        => "Y",
                                                                        "SHOW_DELIVERY_INFO_NAME"        => "Y",
                                                                        "SHOW_DELIVERY_LIST_NAMES"       => "Y",
                                                                        "SHOW_DELIVERY_PARENT_NAMES"     => "Y",
                                                                        "SHOW_MAP_IN_PROPS"              => "N",
                                                                        "SHOW_NEAREST_PICKUP"            => "N",
                                                                        "SHOW_ORDER_BUTTON"              => "final_step",
                                                                        "SHOW_PAY_SYSTEM_INFO_NAME"      => "Y",
                                                                        "SHOW_PAY_SYSTEM_LIST_NAMES"     => "Y",
                                                                        "SHOW_STORES_IMAGES"             => "Y",
                                                                        "SHOW_TOTAL_ORDER_BUTTON"        => "Y",
                                                                        "SHOW_VAT_PRICE"                 => "Y",
                                                                        "SKIP_USELESS_BLOCK"             => "Y",
                                                                        "TEMPLATE_LOCATION"              => "popup",
                                                                        "TEMPLATE_THEME"                 => "site",
                                                                        "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
                                                                        "USE_CUSTOM_ERROR_MESSAGES"      => "Y",
                                                                        "USE_CUSTOM_MAIN_MESSAGES"       => "N",
                                                                        "USE_PREPAYMENT"                 => "N",
                                                                        "USE_YM_GOALS"                   => "N",
                                                                        "USER_CONSENT"                   => "Y",
                                                                        "USER_CONSENT_ID"                => "1",
                                                                        "USER_CONSENT_IS_CHECKED"        => "Y",
                                                                        "USER_CONSENT_IS_LOADED"         => "N"
                                                                    )
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? } ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>