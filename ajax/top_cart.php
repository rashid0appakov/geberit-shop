<?php
    $ajax = true;
    ob_start();
?>
    <? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "top_cart",
        array(
            "PATH_TO_BASKET"    => SITE_DIR."personal/cart/",
            "PATH_TO_PERSONAL"  => SITE_DIR."personal/",
            "SHOW_PERSONAL_LINK"=> "N",
            "SHOW_EMPTY_VALUES" => "N",
            "SHOW_PRODUCTS"     => "N",
            "SHOW_NUM_PRODUCTS" => "Y",
            "SHOW_TOTAL_PRICE"  => "N"
        ),
        false,
        array(
            "0" => ""
        )
    );?>
<?php
    $json['top_cart'] = CClass::clearHTML(ob_get_clean());
?>