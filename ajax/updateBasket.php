<? if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") ||
        empty($_POST["ITEMS"]) || empty($_POST["QTY"]) || count($_POST["ITEMS"]) != count($_POST["QTY"])
    )
        $json['status'] = "error";
    else{
        foreach($_POST["ITEMS"] AS $k => &$product_id)
            CSaleBasket::Update($product_id, [
                "QUANTITY" => $_POST["QTY"][$k],
            ]);


        $ob = CClass::getCartData();
        $json['ITEMS'] = $ob['ITEMS'];

        include (__DIR__."/bottom_cart.php");
    }
?>