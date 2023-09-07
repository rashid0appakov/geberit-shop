<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
		throw new BitrixNotFoundException();

	$APPLICATION->AddChainItem($arResult["NAME"], '/');