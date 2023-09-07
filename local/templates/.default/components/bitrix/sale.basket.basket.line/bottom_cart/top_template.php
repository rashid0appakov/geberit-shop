<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<? if ($arResult['NUM_PRODUCTS'] > 0):
    if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'){?>
    <div class="toolbar-bottom__basket">
        <p>
            <?/*<span><?=$arResult['NUM_PRODUCTS'];?></span> <?=GetMessage('TSB_ITEMS_LABEL')?>:*/?>
            <span class="toolbar-bottom__basket-price"><?=customFormatPrice($arResult['TOTAL_PRICE'])?></span>
        </p>
    </div><?}?>
    <a href="<?=$arParams['PATH_TO_BASKET']?>" class="toolbar-bottom__button btn">
<? else:?>
    <div class="toolbar-bottom__button is-desabled">
<? endif;?>
        <span class="toolbar-bottom__button-title"><?=GetMessage('TSB1_2ORDER')?></span>
    <?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
        <span class="tag is-warning" id="count_<?=$arParams['basket_num']?>"><?=$arResult['NUM_PRODUCTS']?></span>
    <?endif?>
<? if ($arResult['NUM_PRODUCTS'] > 0):?></a><? else:?></div><? endif;?>