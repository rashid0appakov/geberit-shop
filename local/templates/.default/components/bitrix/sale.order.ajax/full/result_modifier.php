<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);


$rsUser = CUser::GetByID( $USER->GetID() );
$arUser = $rsUser->Fetch();

$arResult['USER_INFO'] = $arUser;

//print_r($arResult['USER_INFO']);

$phone = substr($arResult['USER_INFO']['PERSONAL_PHONE'], 1, 100);

$arResult['USER_PHONE'][] = substr($phone, 0, 3);
$arResult['USER_PHONE'][] = substr($phone, 3);
