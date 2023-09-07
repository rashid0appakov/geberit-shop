<?

class FeedParams
{
    public function getArSkructure()
    {
        $arSkructure = array(
            's1' => array(
                672 => array('Цвет', 'Назначение', 'Управление', 'Форма', 'Стилистика дизайна', 'Монтаж'),
                640 => array(
                    'Цвет',
                    'Форма',
                    'Монтаж',
                    'Организация смывающего потока',
                    'Система антивсплеск',
                    'Безободковый',
                    'Стилистика дизайна'
                ),
                645 => array(
                    'Ширина',
                    'Форма',
                    'Стилистика дизайна',
                    'Монтаж',
                    'Цвет',
                    'Перелив',
                    'Тип раковины',
                    'Отверстия под смеситель'
                ),
                643 => array('Форма', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Отверстия под смеситель'),
                647 => array('Длина', 'Ширина', 'Форма', 'Стилистика дизайна', 'Цвет', 'Количество человек'),
            ),

            's8' => array(
                469 => array('Цвет', 'Назначение', 'Управление', 'Форма', 'Стилистика дизайна', 'Монтаж'),
                468 => array(
                    'Цвет',
                    'Форма',
                    'Монтаж',
                    'Организация смывающего потока',
                    'Система антивсплеск',
                    'Безободковый',
                    'Стилистика дизайна'
                ),
                456 => array(
                    'Ширина',
                    'Форма',
                    'Стилистика дизайна',
                    'Монтаж',
                    'Цвет',
                    'Перелив',
                    'Тип раковины',
                    'Отверстия под смеситель'
                ),
                577 => array('Форма', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Отверстия под смеситель'),
                493 => array('Ширина', 'Форма', 'Материал', 'Монтаж', 'Цвет', 'Стилистика дизайна', 'Покрытие корпуса'),
                463 => array('Длина', 'Ширина', 'Форма', 'Стилистика дизайна', 'Цвет', 'Количество человек'),
                513 => array('Ширина', 'Высота', 'Форма', 'Терморегулятор', 'Материал', 'Цвет'),
                470 => array('Ширина', 'Высота', 'Страна', 'Тип', 'Метод крепления'),
            )
        );
        $arSkructure['l1'] = $arSkructure['s8'];
        $arSkructure['s0'] = $arSkructure['s8'];
        $arSkructure['s2'] = $arSkructure['s8'];
        $arSkructure['s3'] = $arSkructure['s8'];
        $arSkructure['s5'] = $arSkructure['s8'];
        $arSkructure['s6'] = $arSkructure['s8'];
        return $arSkructure;
    }

    public function getArElementParam()
    {
        $arElementParam = array(
            's1' => array(
                            'ID',
                            'IBLOCK_ID',
                            'NAME',
                            'CODE',
                            'IBLOCK_SECTION_ID',
                            'DETAIL_PAGE_URL',
                            'DETAIL_PICTURE',
                            'PROPERTY_TIP',
                            'PROPERTY_ARTNUMBER',
                            'PROPERTY_MANUFACTURER',
                            'PROPERTY_TSVET',
                            'PROPERTY_NAZNACHENIE',
                            'PROPERTY_UPRAVLENIE_',
                            'PROPERTY_FORMA',
                            'PROPERTY_STILISTIKA_DIZAYNA',
                            'PROPERTY_MONTAZH',
            				'PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA',
                            'PROPERTY_GUARANTEE',
                            'PROPERTY_SISTEMA_ANTIVSPLESK',
                            'PROPERTY_BEZOBODKOVIY',
                            'PROPERTY_SHIRINA_SM',
                            'PROPERTY_MATERIAL',
            				'PROPERTY_COLOR',
            				'PROPERTY_PERELIV',
            				'PROPERTY_TIP_RAKOVINY',
            				'PROPERTY_OTVERSTIYA_POD_SMESITEL',
            				'PROPERTY_POKRYTIE_KORPUSA',
            				'PROPERTY_DLINA_SM',
            				'PROPERTY_KOLICHESTVO_CHELOVEK',
            				'PROPERTY_VYSOTA_SM',
            				'PROPERTY_TERMOREGULYATOR',
            				'PROPERTY_COUNTRY',
            				'PROPERTY_METOD_KREPLENIYA',
            				'PROPERTY_GLUBINA_SM',
                        ),
            's8' => array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
                'IBLOCK_SECTION_ID',
                'DETAIL_PAGE_URL',
                'DETAIL_PICTURE',
                'PROPERTY_TIP',
                'PROPERTY_ARTNUMBER',
                'PROPERTY_MANUFACTURER',
                'PROPERTY_COLOR',
                'PROPERTY_NAZNACHENIE',
                'PROPERTY_UPRAVLENIE',
                'PROPERTY_FORMA',
                'PROPERTY_STILISTIKA',
                'PROPERTY_MONTAZH',
                'PROPERTY_GUARANTEE',
                'PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA',
                'PROPERTY_SISTEMA_ANTIVSPLESK',
                'PROPERTY_BEZOBODKOVIY',
                'PROPERTY_SHIRINA',
                'PROPERTY_PERELIV',
                'PROPERTY_TIP_RAKOVINY',
                'PROPERTY_OTVERSTIYA_POD_SMESITEL',
                'PROPERTY_MATERIAL',
                //'PROPERTY_POKRYTIE_KORPUSA',
                //'PROPERTY_DLINA',
                //'PROPERTY_KOLICHESTVO_CHELOVEK',
                //'PROPERTY_VYSOTA',
                //'PROPERTY_TERMOREGULYATOR',
                //'PROPERTY_COUNTRY',
                //'PROPERTY_TIP',
                //'PROPERTY_METOD_KREPLENIYA',
            ),
            'l1' => array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
                'IBLOCK_SECTION_ID',
                'DETAIL_PAGE_URL',
                'DETAIL_PICTURE',
                'PROPERTY_VID_SVETILNIKA',
                'PROPERTY_ARTNUMBER',
                'PROPERTY_MANUFACTURER',
                'PROPERTY_COLOR',
                'PROPERTY_NAZNACHENIE',
                'PROPERTY_UPRAVLENIE',
                'PROPERTY_FORMA',
                'PROPERTY_STILISTIKA',
                'PROPERTY_MONTAZH',
                'PROPERTY_GUARANTEE',
                'PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA',
                'PROPERTY_SISTEMA_ANTIVSPLESK',
                'PROPERTY_BEZOBODKOVIY',
                'PROPERTY_SHIRINA',
                'PROPERTY_PERELIV',
                'PROPERTY_TIP_RAKOVINY',
                'PROPERTY_SERIES',
                'PROPERTY_VID_SEO',
            )
        );
        $arElementParam['s0'] = $arElementParam['s8'];
        $arElementParam['s2'] = $arElementParam['s8'];
        $arElementParam['s3'] = $arElementParam['s8'];
        $arElementParam['s5'] = $arElementParam['s8'];
        $arElementParam['s6'] = $arElementParam['s8'];

        return $arElementParam;
    }

    public function getArVariable()
    {
        $arVariable = array(
            's1' => array(
                'arPropDesc' => function ($row) {
                    CModule::IncludeModule('highloadblock');
                    $arHLBlockTSVET = Bitrix\Highloadblock\HighloadBlockTable::getById(5)->fetch();
                    $obEntityTSVET = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockTSVET);
                    $strEntityDataClassTSVET = $obEntityTSVET->getDataClass();
                    $tsvets = array();
                    foreach ($row['PROPERTY_TSVET_VALUE'] as $arTsvet) {
                        $rsDataTSVET = $strEntityDataClassTSVET::getList(array(
                            'select' => array('UF_NAME'),
                            'order' => array('ID' => 'ASC'),
                            'filter' => array('UF_XML_ID' => $arTsvet)
                        ));
                        if ($arItemTSVET = $rsDataTSVET->Fetch()) {
                            $tsvets[] = $arItemTSVET['UF_NAME'];
                        }
                    }

                    $arPropDesc = array(
                        'Цвет' => $row['PROPERTY_COLOR_VALUE'],
                        'Назначение' => $row['PROPERTY_NAZNACHENIE_VALUE'],
		                'Управление' => $row['PROPERTY_UPRAVLENIE_VALUE'],
		                'Форма' => $row['PROPERTY_FORMA_VALUE'],
		                'Стилистика дизайна' => $row['PROPERTY_STILISTIKA_DIZAYNA_VALUE'],
		                'Монтаж' => $row['PROPERTY_MONTAZH_VALUE'],
		                'Гарантия' => $row['PROPERTY_GUARANTEE_VALUE'],
		                'Организация смывающего потока' => $row['PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA_VALUE'],
		                'Система антивсплеск' => $row['PROPERTY_SISTEMA_ANTIVSPLESK_VALUE'],
		                'Безободковый' => $row['PROPERTY_BEZOBODKOVIY_VALUE'],
		                'Ширина' => $row['PROPERTY_SHIRINA_SM_VALUE'],
		                'Перелив' => $row['PROPERTY_PERELIV_VALUE'],
		                'Тип раковины' => $row['PROPERTY_TIP_RAKOVINY_VALUE'],
		                'Отверстия под смеситель' => $row['PROPERTY_OTVERSTIYA_POD_SMESITEL_VALUE'],
		                'Материал' => $row['PROPERTY_MATERIAL_VALUE'],
		                'Покрытие корпуса' => $row['PROPERTY_POKRYTIE_KORPUSA_VALUE'],
		                'Длина' => $row['PROPERTY_DLINA_SM_VALUE'],
		                'Количество человек' => $row['PROPERTY_KOLICHESTVO_CHELOVEK_VALUE'],
		                'Высота' => $row['PROPERTY_VYSOTA_SM_VALUE'],
		                'Терморегулятор' => $row['PROPERTY_TERMOREGULYATOR_VALUE'],
		                'Страна' => $row['PROPERTY_COUNTRY_VALUE'],
		                 'Тип' => $row['PROPERTY_TIP_VALUE'],
		                 'Метод крепления' => $row['PROPERTY_METOD_KREPLENIYA_VALUE'],
		                 'Глубина' => $row['PROPERTY_GLUBINA_SM'],
                    );

                    return $arPropDesc;
                },
                'typePrefix' => function ($row) {
                    return $row['PROPERTY_TIP_VALUE'];
                },
            ),
            's8' => array(
                'typePrefix' => function ($row) {
                    return $row['PROPERTY_TIP_VALUE'];
                },
                'arPropDesc' => function ($row) {
                    return $arPropDesc = array(
                        'Цвет' => $row['PROPERTY_COLOR_VALUE'],
                        'Назначение' => $row['PROPERTY_NAZNACHENIE_VALUE'],
                        'Управление' => $row['PROPERTY_UPRAVLENIE_VALUE'],
                        'Форма' => $row['PROPERTY_FORMA_VALUE'],
                        'Стилистика дизайна' => $row['PROPERTY_STILISTIKA_VALUE'],
                        'Монтаж' => $row['PROPERTY_MONTAZH_VALUE'],
                        'Гарантия' => $row['PROPERTY_GUARANTEE_VALUE'],
                        'Организация смывающего потока' => $row['PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA_VALUE'],
                        'Система антивсплеск' => $row['PROPERTY_SISTEMA_ANTIVSPLESK_VALUE'],
                        'Безободковый' => $row['PROPERTY_BEZOBODKOVIY_VALUE'],
                        'Ширина' => $row['PROPERTY_SHIRINA_VALUE'],
                        'Перелив' => $row['PROPERTY_PERELIV_VALUE'],
                        'Тип раковины' => $row['PROPERTY_TIP_RAKOVINY_VALUE'],
                        'Отверстия под смеситель' => $row['PROPERTY_OTVERSTIYA_POD_SMESITEL_VALUE'],
                        'Материал' => $row['PROPERTY_MATERIAL_VALUE'],
                        'Покрытие корпуса' => $row['PROPERTY_POKRYTIE_KORPUSA_VALUE'],
                        'Длина' => $row['PROPERTY_DLINA_VALUE'],
                        'Количество человек' => $row['PROPERTY_KOLICHESTVO_CHELOVEK_VALUE'],
                        'Высота' => $row['PROPERTY_VYSOTA_VALUE'],
                        'Терморегулятор' => $row['PROPERTY_TERMOREGULYATOR_VALUE'],
                        'Страна' => $row['PROPERTY_COUNTRY_VALUE'],
                        'Тип' => $row['PROPERTY_TIP_VALUE'],
                        'Метод крепления' => $row['PROPERTY_METOD_KREPLENIYA_VALUE'],
                    );
                }
            ),
            'l1' => array(
                'arPropDesc' => function ($row) {
                    return array(
                        'Цвет' => $row['PROPERTY_COLOR_VALUE'],
                        'Назначение' => $row['PROPERTY_NAZNACHENIE_VALUE'],
                        'Управление' => $row['PROPERTY_UPRAVLENIE_VALUE'],
                        'Форма' => $row['PROPERTY_FORMA_VALUE'],
                        'Стилистика дизайна' => $row['PROPERTY_STILISTIKA_VALUE'],
                        'Монтаж' => $row['PROPERTY_MONTAZH_VALUE'],
                        'Гарантия' => $row['PROPERTY_GUARANTEE_VALUE'],
                        'Организация смывающего потока' => $row['PROPERTY_ORGANIZACIYA_SMIVAYUSCHEGO_POTOKA_VALUE'],
                        'Система антивсплеск' => $row['PROPERTY_SISTEMA_ANTIVSPLESK_VALUE'],
                        'Безободковый' => $row['PROPERTY_BEZOBODKOVIY_VALUE'],
                        'Ширина' => $row['PROPERTY_SHIRINA_VALUE'],
                        'Перелив' => $row['PROPERTY_PERELIV_VALUE'],
                        'Тип раковины' => $row['PROPERTY_TIP_RAKOVINY_VALUE'],
                        'Покрытие корпуса' => $row['PROPERTY_POKRYTIE_KORPUSA_VALUE'],
                        'Длина' => $row['PROPERTY_DLINA_VALUE'],
                        'Количество человек' => $row['PROPERTY_KOLICHESTVO_CHELOVEK_VALUE'],
                        'Высота' => $row['PROPERTY_VYSOTA_VALUE'],
                        'Терморегулятор' => $row['PROPERTY_TERMOREGULYATOR_VALUE'],
                        'Страна' => $row['PROPERTY_COUNTRY_VALUE'],
                        'Тип' => $row['PROPERTY_TIP_VALUE'],
                        'Метод крепления' => $row['PROPERTY_METOD_KREPLENIYA_VALUE'],
                    );
                },
                'typePrefix' => function ($row) {
                    return $row['PROPERTY_VID_SVETILNIKA_VALUE'];
                },
                'seriesId' => function ($row) {
                    return $row['PROPERTY_SERIES_VALUE'];
                },
                'vid_seo' => function ($row) {
                    return $row['PROPERTY_VID_SEO_VALUE'];
                },
            )
        );
        $arVariable['s0'] = $arVariable['s8'];
        $arVariable['s2'] = $arVariable['s8'];
        $arVariable['s3'] = $arVariable['s8'];
        $arVariable['s5'] = $arVariable['s8'];
        $arVariable['s6'] = $arVariable['s8'];

        return $arVariable;
    }

    public function getIblockId()
    {
        return array(
            'l1' => array(
                'PRODUCT_IBLOCK_ID' => 50,
                'MANUFACTURER_IBLOCK_ID' => 48,
                'MANUFACTURER_IDS' => array(),
            ),
            's0' => array(
                'PRODUCT_IBLOCK_ID' => 54,
                'MANUFACTURER_IBLOCK_ID' => 52,
                'MANUFACTURER_IDS' => array(
                    329265,
                    329219,
                    329261,
                    329281,
                    329280,
                    329217,
                    329252,
                    329275,
                    329251,
                    329331,
                    329496,
                    329326,
                    329273,
                    329417,
                    329313,
                    329395,
                    329489,
                    329488,
                    329394,
                    329285,
                    329266,
                    329291,
                    413323,
                    329354,
                    329393,
                    329223,
                    329392,
                    329348,
                    329234,
                    329325,
                    329255,
                    329351,
                    329314,
                    329283,
                    329307,
                    329324,
                    329278
                ),
            ),
            's1' => array(
                'PRODUCT_IBLOCK_ID' => 15,
                'MANUFACTURER_IBLOCK_ID' => 13,
                'MANUFACTURER_IDS' => array(175796),
            ),
            's2' => array(
                'PRODUCT_IBLOCK_ID' => 64,
                'MANUFACTURER_IBLOCK_ID' => 61,
                'MANUFACTURER_IDS' => array(420758),
            ),
            's3' => array(
                'PRODUCT_IBLOCK_ID' => 68,
                'MANUFACTURER_IBLOCK_ID' => 65,
                'MANUFACTURER_IDS' => array(420759, 427389),
            ),
            's5' => array(
                'PRODUCT_IBLOCK_ID' => 76,
                'MANUFACTURER_IBLOCK_ID' => 73,
                'MANUFACTURER_IDS' => array(420762, 429577, 432410, 432439),
            ),
            's6' => array(
                'PRODUCT_IBLOCK_ID' => 60,
                'MANUFACTURER_IBLOCK_ID' => 57,
                'MANUFACTURER_IDS' => array(416094),
            ),
            's8' => array(
                'PRODUCT_IBLOCK_ID' => 84,
                'MANUFACTURER_IBLOCK_ID' => 81,
                'MANUFACTURER_IDS' => array(420764),
            ),
        );
    }

    public function getStorageIdByCatalogGroup(){
        $storageId = array(
            's0' => ['1' => 4, '2' => 1, '4' => 2]
        );
        $storageId['l1'] = $storageId['s0'];
        $storageId['s1'] = $storageId['s0'];
        $storageId['s2'] = $storageId['s0'];
        $storageId['s3'] = $storageId['s0'];
        $storageId['s5'] = $storageId['s0'];
        $storageId['s6'] = $storageId['s0'];
        $storageId['s8'] = $storageId['s0'];
        return $storageId;
    }

    public function getArPathSite()
    {
        $arPathSite = [
            'l1' => '/var/www/clients/client0/web1/private/swet-online.ru/',
            's0' => '/var/www/clients/client0/web1/web/',
            's1' => '/var/www/clients/client0/web1/private/drvt.shop/',
            's2' => '/var/www/clients/client0/web1/private/shop-roca.ru/',
            's3' => '/var/www/clients/client0/web1/private/hg-online.ru/',
            's5' => '/var/www/clients/client0/web1/private/shop-jd.ru/',
            's6' => '/var/www/clients/client0/web1/private/shop-gr.ru/',
            's8' => '/var/www/clients/client0/web1/private/geberit-shop.ru/',
        ];

        return $arPathSite;
    }

    public function getArPathCatalogGroup()
    {
        $arPathCatalogGroup = [
            '1' => 'feed/yandex.xml',
            '4' => 'feed/yandex_ekb.xml',
            '2' => 'feed/yandex_spb.xml'
        ];

        return $arPathCatalogGroup;
    }

    public function getArPropPriceUpdate()
    {
        $arPropPriceUpdate = [
            '1' => '>PROPERTY_PRICE_UPDATE',
            '2' => '>PROPERTY_PRICE_UPDATE_SPB',
            '4' => '>PROPERTY_PRICE_UPDATE_EKB'
        ];

        return $arPropPriceUpdate;
    }
}
