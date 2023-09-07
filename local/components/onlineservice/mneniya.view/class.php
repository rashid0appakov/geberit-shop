<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Tools;
use Bitrix\Main\Config\Option;
use Bitrix\Crm\EntityRequisite;


class onlineservice_mneniya_view extends CBitrixComponent{

    public function executeComponent(){     
        $this->getReviewsBySKUID();
        $this->includeComponentTemplate();
    }
    
    
    public function getReviewsBySKUID(){

        if ( isset($this->arParams['SKU_ID']) and  $this->arParams['SKU_ID'] > 0 ){
            $url = 'https://api.mneniya.pro/v1.3/clients/'.$this->arParams['CLIENT_ID'].'/reviews/Product/'.$this->arParams['SKU_ID'].'/'.$this->arParams['TYPE_REVIEWS'];
            
            if ( 
                    ( isset($this->arParams['START']) and $this->arParams['START'] != '' ) 
                        or
                    ( isset($this->arParams['COUNT']) and $this->arParams['COUNT'] != '' ) 
                        or
                    ( isset($this->arParams['ORDER_BY']) and $this->arParams['ORDER_BY'] != '' ) 
                        or
                    ( isset($this->arParams['SORTING_ORDER']) and $this->arParams['SORTING_ORDER'] != '' ) 
                        or
                    ( isset($this->arParams['FILTER_BY']) and $this->arParams['FILTER_BY'] != '' ) 
                        or
                    ( isset($this->arParams['FILTER_VALUES']) and $this->arParams['FILTER_VALUES'] != '' ) 
                )
            {
                $url .= '?';
            }
            
            if ( isset($this->arParams['START']) and $this->arParams['START'] != '' ){
                $url .= 'offset='.$this->arParams['START'].'&';
            }
            if ( isset($this->arParams['COUNT']) and $this->arParams['COUNT'] != '' ){
                $url .= 'count='.$this->arParams['COUNT'].'&';
            }
            if ( isset($this->arParams['ORDER_BY']) and $this->arParams['ORDER_BY'] != '' ){
                $url .= 'filterBy='.$this->arParams['ORDER_BY'].'&';
            }
            if ( isset($this->arParams['SORTING_ORDER']) and $this->arParams['SORTING_ORDER'] != '' ){
                $url .= 'filterValues='.$this->arParams['SORTING_ORDER'].'&';
            }
            if ( isset($this->arParams['FILTER_BY']) and $this->arParams['FILTER_BY'] != '' ){
                $url .= 'orderBy='.$this->arParams['FILTER_BY'].'&';
            }
            if ( isset($this->arParams['FILTER_VALUES']) and $this->arParams['FILTER_VALUES'] != '' ){
                $url .= 'sortingOrder='.$this->arParams['FILTER_VALUES'].'&';
            }
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $json = curl_exec($ch);
            curl_close($ch);
            
            $this->arResult['REVIEWS'] = json_decode($json, true);
        }
    }
}