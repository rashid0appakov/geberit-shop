<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();

	if (isset($_GET['brands'])){
		$arIDs = explode('_', $_GET['brands']);
		foreach($arIDs AS $k => &$id)
			if ((int)$id)
				$id = (int)$id;
			else
				unset($arIDs[$k]);
	}

	$arResult['S_BRANDS']   = [];
	$arResult['BRANDS']	 = [];
	if (!empty($arResult['ITEMS'])){
		foreach($arResult['ITEMS'] AS $arItem)
			$arResult['S_BRANDS'][$arItem['PROPERTIES']['BRAND']['VALUE']] += 1;


		$arSelect   = [
			"ID", "IBLOCK_ID", "NAME"
		];
		$arFilter   = [
			'IBLOCK_ID' => RU_BRANDS_IBLOCK_ID,
			'ACTIVE'	=> "Y"
		];

		$dbItems = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, FALSE, Array("nPageSize" => 5000), $arSelect);
		while ($arItem = $dbItems->GetNext()){
			if (in_array($arItem['ID'], $arIDs))
				$arItem['SELECTED'] = "Y";
			$arResult['BRANDS'][$arItem['ID']] = $arItem;
		}
	}

	$this->__component->SetResultCacheKeys(array("S_BRANDS", "BRANDS"));