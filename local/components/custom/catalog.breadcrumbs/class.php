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

class CCatalogBreadcrums extends CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->startResultCache())
        {
            $this->LoadBreadcrumbs();
            $this->includeComponentTemplate();
        }
    }

    protected function LoadBreadcrumbs()
    {
        $this->arResult["ITEMS"] = array();

        $root = $this->GetRootItem();
        $this->AddItem($root);

        $items = $this->GetItems();
        foreach ($items as $item)
        {
            $this->AddItem($item);
        }
    }

    protected function AddItem($data)
    {
        $this->arResult["ITEMS"][] = $data;
    }

    protected function GetRootItem()
    {
        return array(
            "NAME" => "Главная",
            "URL" => "/",
            "ID" => false,
            "ITEMS" => array(),
        );
    }

    protected function GetItems()
    {
        $items = array();

        $chainSections = array();
        $parentSectionIds = array();

        $ob = CIBlockSection::GetNavChain($this->arParams["IBLOCK_ID"], $this->arParams["SECTION_ID"]);
        while ($item = $ob->GetNext())
        {
            $chainSections[] = $item;
            $parentSectionIds[] = $item["IBLOCK_SECTION_ID"];
			
			$SECTION_PAGE_URL = $item['SECTION_PAGE_URL'];
        }

        $sections = array();
        $ob = CIBlockSection::GetList(
            array(
                "SORT" => "ASC",
            ),
            array(
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                "=IBLOCK_SECTION_ID" => $parentSectionIds,
                "GLOBAL_ACTIVE" => "Y"
            ),
			false,
			array(
				"IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME", "SECTION_PAGE_URL", "UF_BREAD_NAME"
			)
        );
        while ($item = $ob->GetNext())
        {
            $sections[] = $item;
        }

        foreach ($chainSections as $chainSection)
        {
            $item = array(
                "NAME" => $chainSection["NAME"],
                "URL" => $chainSection["SECTION_PAGE_URL"],
                "ITEMS" => array(),
            );


            $siblings = array();
            foreach ($sections as $section)
            {
                if (
                    $section["IBLOCK_SECTION_ID"] == $chainSection["IBLOCK_SECTION_ID"]
                    &&
                    $section["ID"] != $chainSection["ID"]
                )
                {
                   /*global $USER;
                    if ($USER->IsAdmin()){*/
                        global $man_show;
                        $series_ids = array();
                        $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
                        $arFilter = Array("IBLOCK_ID"=>SERIES_IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_BRAND' => $man_show);
                        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                        while($ob = $res->GetNextElement())
                        {
                            $arFields = $ob->GetFields();
                            $series_ids[] = $arFields['ID'];
                        }
                        

                        $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
                        $arFilter = Array("IBLOCK_ID"=>$this->arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "!PROPERTY_DISCONTINUED" => "Y", "SECTION_ID"=>$section["ID"], "INCLUDE_SUBSECTIONS"=>"Y", "PROPERTY_SERIES" => $series_ids);
                        // var_dump($arFilter);
                        // die;
                        //$arFilter = Array("IBLOCK_ID"=>$arSection['IBLOCK_ID'], "SECTION_ID"=>$arSection['ID'], "INCLUDE_SUBSECTIONS"=>"Y", "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_DISCONTINUED" => "Y", "PROPERTY_MANUFACTURER" => $man_show);


                        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                        if(intval($res->SelectedRowsCount())){
                         $item["ITEMS"][] = array(
                                "NAME" => $section["NAME"],
                                "URL" => $section["SECTION_PAGE_URL"],
                            );
                        }
                    /*}else{
                        $item["ITEMS"][] = array(
                            "NAME" => $section["NAME"],
                            "URL" => $section["SECTION_PAGE_URL"],
                        );
                    }*/
                }
				
				//Заменяем имя раздела на альтернативное
				if($section["ID"] == $chainSection["ID"] && !empty($section["UF_BREAD_NAME"])){
					$item["NAME"] = $section["UF_BREAD_NAME"];
				}
            }

            $items[] = $item;
        }

        if (!!$this->arParams["ELEMENT_ID"])
        {
            $ob = CIBlockElement::GetList(
                array(),
                array(
                    "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                    "ID" => $this->arParams["ELEMENT_ID"],
                ),
				false,
				false,
				array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_ARTNUMBER", "PROPERTY_SERIES", "PROPERTY_MANUFACTURER")
            );
            if ($arElement = $ob->GetNext())
            {
                $item = array(
                    "NAME" => $arElement["NAME"],
                    "URL" => $arElement["DETAIL_PAGE_URL"],
					"PROP" => array(
						"ARTNUMBER" => $arElement["PROPERTY_ARTNUMBER_VALUE"],
						"MANUFACTURER" => $arElement["PROPERTY_MANUFACTURER_VALUE"],
						"SERIES" => $arElement["PROPERTY_SERIES_VALUE"],
					)
                );

                $items[] = $item;
            }
        }
		
		if (count($this->arParams["TAGS"]) > 0)
        {
			foreach($this->arParams["TAGS"] as $arItem){

				$nameTag = $arItem["NAME_BREAD"] ? $arItem["NAME_BREAD"] : $arItem["NAME"];
				$urlTag = $SECTION_PAGE_URL.$arItem["CODE"].'/';
				
				$item = array(
					"NAME" => $nameTag,
					"URL" => $urlTag,
				);
				
				$items[] = $item;
			}
			
        }
		
		if (count($this->arParams["CUSTOM_ITEMS"]) > 0)
        {
			foreach($this->arParams["CUSTOM_ITEMS"] as $arItem){
				$item = array(
					"NAME" => $arItem["NAME"],
					"URL" => $arItem["URL"],
				);
				
				$items[] = $item;
			}
        }

        return $items;
    }

}