<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();
if (empty($arParams['ITEMS']))
    return "";
$maxCount = 9;
$arMainTags = [];
$arTags = [];
//pr($arParams['ITEMS']);
$SELECTED = false;
foreach($arParams['ITEMS'] as $arItem) {
    if($arItem["SELECTED"]=='Y') { $SELECTED = true; }
    if($arItem["LINK"]==$APPLICATION->GetCurDir()){ $SELECTED = true; continue; }
    if(intval($arItem['UF_DONT_SHOW_PUBLIC'])==1) { continue; }
    if(intval($arItem['UF_MAIN_CHECK'])==1 && count($arMainTags)<$maxCount) { $arMainTags[] = $arItem; }
}
foreach($arParams['ITEMS'] as $arItem) {
    if(in_array($arItem, $arMainTags)) { continue; }
    if($arItem["LINK"]==$APPLICATION->GetCurDir()){ $SELECTED = true; continue; }
    if(count($arMainTags)<$maxCount) { $arMainTags[] = $arItem; }
    else { $arTags[] = $arItem; }
}
if($GLOBALS['USER']->IsAdmin()){
//    var_dump($arTags);
}
if(count($arParams['ITEMS'])==1 && $SELECTED) return '';
?>
    <div class="seo-catalog-section-list" data-nosnippet>
        <div class="seo-catalog-section">
            <div class="seo-catalog-section-childs">
                <?foreach($arMainTags as $arItem){
                    if($arItem["SELECTED"]=='Y') { continue; }
                    ?>
                    <div class="seo-catalog-section-child <?=$arItem["SELECTED"]=='Y' ? 'fast_link_selected' : ''?>">
                        <div class="seo-image">
                            <a href="<?=$arItem["LINK"]?>">
                                <img src="<?=$arItem["IMG"]['src']?>" width="<?=$arItem["IMG"]['width']?>" height="<?=$arItem["IMG"]['height']?>" />
                            </a>
                        </div>
                        <div class="text-cont">
                            <a href="<?=$arItem["LINK"]?>" title="<?=$arItem["HEADER"]?>"><?=$arItem["HEADER"]?></a>
                        </div>
                    </div>
                <?}?>
            </div>
        </div>
        <?if(count($arTags)>0){?>
            <a href="javascript:void(0)" onclick="$('#show_all_tags').hide(); $('#show2').show(); $(this).hide();" class="back_tag" id="hide2" style="display: none;">Скрыть дополнительные</a>
            <a href="javascript:void(0)" onclick="$('#show_all_tags').show(); $('#hide2').show(); $(this).hide();" class="back_tag" id="show2">Показать дополнительные</a>
        <?}?>
        <?/*if (!empty($arParams["BACK"])){?><a href="<?=$arParams["BACK"]["BACK"]?>" class="back_tag">Назад</a><?}*/?>
   </div>
<?if(count($arTags)>0){?>
    <div class="seo-catalog-section-list" style="display: none; " id="show_all_tags" data-nosnippet>
        <div class="seo-catalog-section">
            <div class="seo-catalog-section-childs">
                <?foreach($arTags as $arItem){?>
                    <div class="seo-catalog-section-child <?=$arItem["SELECTED"]=='Y' ? 'fast_link_selected' : ''?>">
                        <div class="seo-image">
                            <a href="<?=$arItem["LINK"]?>">
                                <img src="<?=$arItem["IMG"]['src']?>" width="<?=$arItem["IMG"]['width']?>" height="<?=$arItem["IMG"]['height']?>" />
                            </a>
                        </div>
                        <div class="text-cont">
                            <a href="<?=$arItem["LINK"]?>" title="<?=$arItem["HEADER"]?>"><?=$arItem["HEADER"]?></a>
                        </div>
                    </div>
                <?}?>
            </div>
        </div>
        <?/*if(!empty($arParams["BACK"])){?><a href="<?=$arParams["BACK"]?>" class="back_tag">Назад</a><?}*/?>
    </div>
<?}?>