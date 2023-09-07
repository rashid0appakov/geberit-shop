<?php
//<title>wscrm</title>
$moduleDir = file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/webservice.wscrm/export/webservice_setup.php')
    ? 'bitrix' : 'local';

require($_SERVER["DOCUMENT_ROOT"] . '/' . $moduleDir . '/modules/webservice.wscrm/export/webservice_setup.php');