<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?php
    $isAjax     = ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        $_REQUEST["AJAX_CALL"] == "Y");

	if (!$isAjax || !check_bitrix_sessid() || !$_REQUEST['method'])
        die();
    $_REQUEST["AJAX_CALL"] == "Y";

     $json = [
        "status"    => 'success',
        "msg"       => ''
    ];
    switch ($_REQUEST['method']){
    case 'compare':
        include_once(__DIR__.'/compare_item.php');
        break;
    case 'bottom-basket':
        include_once(__DIR__.'/updateBasket.php');
        break;
    case 'cart-data':
        include_once(__DIR__.'/updateBasketAll.php');
        break;
    case 'delete-cart-item':
        include_once(__DIR__.'/delete2basket.php');
        break;
    case 'get-form':
        $name   = trim(strip_tags($_REQUEST['name']));
        if (file_exists(__DIR__.'/form/'.$name.'.php'))
            include_once(__DIR__.'/form/'.$name.'.php');
        die();
        break;
    }
    CClass::Instance()->RenderJSON($json);

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>