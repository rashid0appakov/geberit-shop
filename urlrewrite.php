<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  44 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  42 => 
  array (
    'CONDITION' => '#^/product/[A-Za-z0-9_-]+/\\??.*$#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  43 => 
  array (
    'CONDITION' => '#^/acrit.exportproplus/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.exportproplus/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  21 => 
  array (
    'CONDITION' => '#^/partnership/pub/site/#',
    'RULE' => NULL,
    'ID' => 'bitrix:landing.pub',
    'PATH' => '/partnership/pub/site/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  20 => 
  array (
    'CONDITION' => '#^/partnershipphoto/#',
    'RULE' => '',
    'ID' => 'bitrix:photogallery',
    'PATH' => '/partnershipphoto.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/vend_and_ser/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/vend_and_ser/index.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/kak-vybrat/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/kak-vybrat/index.php',
    'SORT' => 100,
  ),
  19 => 
  array (
    'CONDITION' => '#^/partnership#',
    'RULE' => '',
    'ID' => 'bitrix:blog',
    'PATH' => '/partnershipindex.php',
    'SORT' => 100,
  ),
  31 => 
  array (
    'CONDITION' => '#^/promotions/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/promotions/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => 100,
  ),
  36 => 
  array (
    'CONDITION' => '#^/delivery/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/delivery/index.php',
    'SORT' => 100,
  ),
  35 => 
  array (
    'CONDITION' => '#^/contacts/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/contacts/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/reviews/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/reviews/index.php',
    'SORT' => 100,
  ),
  38 => 
  array (
    'CONDITION' => '#^/vendors/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/vendors/index.php',
    'SORT' => 100,
  ),
  41 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  40 => 
  array (
    'CONDITION' => '#^/series/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/series/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
  17 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
);
