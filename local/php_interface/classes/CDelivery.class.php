<?php
    use Bitrix\Sale;
    use Bitrix\Main\Loader;
    use Bitrix\Sale\Delivery\Restrictions;
	use Bitrix\Sale\Delivery\Services;

	/**
	 * Class CDelivery
	 *
	 */
    class CDelivery {
        /**
         * Get delivery locations data
         * @param array $arLocations    - location IDs
         * @return boolean|array
         */
        private static function getLocationsData($arLocations){
            if (empty($arLocations))
                return FALSE;

            $arResult   = [];
            $arRegions  = [];
            $res = \Bitrix\Sale\Location\LocationTable::getList(array(
                'filter' => array(
                    '=ID' => $arLocations,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID
                ),
                'select' => array(
                    'ID',
                    'TYPE_ID',
                    'NAME_RU' => 'NAME.NAME'
                )
            ));
            while($arItem = $res->fetch())
                $arResult[$arItem['ID']] = $arItem;

            if ($arRegions  = self::getRegionLocationsID($arLocations))
                $arResult   = array_replace($arResult, $arRegions);

            return $arResult;
        }

        /**
         * Get region locations data
         * @param array $arLocations    - locations IDs
         * @return boolean|array
         */
        private static function getRegionLocationsID($arLocations){
            if (empty($arLocations))
                return FALSE;

            $arResult   = [];
            $arRegions  = [];

            $res = \Bitrix\Sale\Location\LocationTable::getList(array(
                'filter' => array(
                    '=PARENT.ID' => $arLocations,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID
                ),
                'select' => array(
                    'ID',
                    'TYPE_ID',
                    'NAME_RU' => 'NAME.NAME'
                )
            ));
            while($arItem = $res->fetch()){
                $arResult[$arItem['ID']] = $arItem;
                if ($arItem['TYPE_ID'] < 5)
                    $arRegions[] = $arItem['ID'];
            }

            // -- Add region sub locations data ----------------------------- //
            if (!empty($arRegions))
                if ($arRegions  = self::getRegionLocationsID($arRegions))
                    $arResult   = array_replace($arResult, $arRegions);

            return $arResult;
        }


        private static function getLocationsDeliveryServices($arRegionLocationIDs){
            if (empty($arRegionLocationIDs))
                return FALSE;

            $arResult   = [];
            $arDelivery = [];

            $arDelivery = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
            foreach($arDelivery AS &$arDelivery)
                foreach($arRegionLocationIDs AS $locID => &$arLocation)
                    if (\Bitrix\Sale\Delivery\Restrictions\ByLocation::check($locID, [], $arDelivery['ID']))
                        $arResult[] = [
                            'LOCATION'  => $arLocation['NAME_RU'],
                            'DELIVERY'  => $arDelivery['NAME']
                        ];

            return $arResult;
        }

        public static function getRegionDeliveryData($arLocationIDs){
            if (empty($arLocationIDs))
                return FALSE;

            $arResult   = [];
            $arLocations= [];
            $arRegionLocationIDs= self::getLocationsData($arLocationIDs);
            if (empty($arRegionLocationIDs))
                return FALSE;

            $arResult   = self::getLocationsDeliveryServices($arRegionLocationIDs);

            return "";

            return $arResult;
        }
    }