<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("iblock"))
{
	ShowError("Модуль iblock не подключен");
	return;
}

class CCatalogSectionText extends CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->startResultCache())
        {
            $this->arResult = array();

            $this->LoadSectionData();
            $this->LoadProductData();
            $this->ReplaceTemplate();

            $this->includeComponentTemplate();
        }
    }

    protected function LoadSectionData()
    {
        $ob = \CIBlockSection::GetList(
            array(),
            array(
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                "ID" => $this->arParams["SECTION_ID"],
                "ELEMENT_SUBSECTIONS" => "N",
                "CNT_ACTIVE" => "Y",
            ),
            true
        );
        if ($item = $ob->Fetch())
        {
            $this->arResult["ID"] = (int) $item["ID"];
            $this->arResult["ELEMENT_COUNT"] = (int) $item["ELEMENT_CNT"];
            $this->arResult["TEXT"] = (string) $item["DESCRIPTION"];
        }
    }

    protected function LoadProductData()
    {
        $params = array(
            "filter" => array(
                "=ELEMENT.IBLOCK_SECTION_ID" => $this->arResult["ID"],
                "=ELEMENT.ACTIVE" => true,
                "CATALOG_GROUP_ID" => $this->arParams["CATALOG_GROUP_ID"],
                ">PRICE" => 0,
            ),
            "select" => array(
                "PRICE",
            ),
            "limit" => 1,
        );

        // min price
        $ob = \Bitrix\Catalog\PriceTable::getList(array_merge($params, array(
            "order" => array(
                "PRICE" => "ASC",
            ),
        )));
        if ($item = $ob->fetch())
        {
            $this->arResult["MIN_PRICE"] = (float) $item["PRICE"];
        }
        else
        {
            $this->arResult["MIN_PRICE"] = 0.0;
        }

        // max price
        $ob = \Bitrix\Catalog\PriceTable::getList(array_merge($params, array(
            "order" => array(
                "PRICE" => "DESC",
            ),
        )));
        if ($item = $ob->fetch())
        {
            $this->arResult["MAX_PRICE"] = (float) $item["PRICE"];
        }
        else
        {
            $this->arResult["MAX_PRICE"] = 0.0;
        }

        // avg price
        $this->arResult["AVG_PRICE"] = ($this->arResult["MIN_PRICE"] + $this->arResult["MAX_PRICE"]) / 2;
    }

    protected function ReplaceTemplate()
    {
        if (!empty($this->arResult["TEXT"]))
        {
            $this->arResult["TEXT"] = str_replace("#MIN_PRICE#", $this->arResult["MIN_PRICE"], $this->arResult["TEXT"]);
            $this->arResult["TEXT"] = str_replace("#MAX_PRICE#", $this->arResult["MAX_PRICE"], $this->arResult["TEXT"]);
            $this->arResult["TEXT"] = str_replace("#AVG_PRICE#", $this->arResult["AVG_PRICE"], $this->arResult["TEXT"]);
            $this->arResult["TEXT"] = str_replace("#ELEMENT_COUNT#", $this->arResult["ELEMENT_COUNT"], $this->arResult["TEXT"]);
        }
    }

}