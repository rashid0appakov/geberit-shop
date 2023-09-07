<?php

/*
    include.php - подключаемый файл (файл подключается при подключении модуля во время выполнения скриптов сайта), в нем должны
    находиться включения всех файлов с библиотеками функций и классов модуля.
    В этом файле так же объявляются используемые модулем константы, если они общие (https://dev.1c-bitrix.ru/api_help/main/general/constants.php).
    Если же константы относятся к какому-либо классу модуля, то они объявляются в самом классе.

    from https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=101&LESSON_ID=3216&LESSON_PATH=8781.4793.3216
 */

CModule::AddAutoloadClasses(
    'webservice.wscrm',
    array (
        'WebServiceCrm\ApiClient\Exception\CurlException'         => 'classes/general/Exception/CurlException.php',
        'WebServiceCrm\ApiClient\Response\Response'     => 'classes/general/Response/Response.php',
        'WebServiceCrm\ApiClient\Http\Client'           => 'classes/general/Http/Client.php',
        'WebServiceCrm\ApiClient\Client'                => 'classes/general/Client.php',
        'WebServiceCrm\Xml'                             => 'classes/general/Xml.php'
    )
);