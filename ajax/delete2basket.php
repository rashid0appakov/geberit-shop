<? if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog"))
	return;

    if (!isset($_POST["ID"]) || !(int)$_POST["ID"]){
        $json['status'] = "error";
    }else{
        CSaleBasket::Delete($_POST['ID']);

        include (__DIR__."/top_cart.php");
        include (__DIR__."/bottom_cart.php");
    }
?>