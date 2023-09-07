<?include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
    CHTTP::SetStatus("404 Not Found");
    @define("ERROR_404","Y");

    include($_SERVER["DOCUMENT_ROOT"]."/404/index.php");
?>