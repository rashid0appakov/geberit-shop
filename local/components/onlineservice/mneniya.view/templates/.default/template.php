<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?
if ( isset($arResult['REVIEWS']["Reviews"]) and count($arResult['REVIEWS']["Reviews"]) > 0 ):
?>

<?
///////rate blok, show one time
if ( $arParams['DOP_REVIEWS'] == 'N' ):?>
<br/>
<p>
    <?=GetMessage('REVIEWS_ALL_RATE')?>: <?=$arResult['REVIEWS']["Stats"]['ReviewsTotalCount']?><br/>
    <?=GetMessage('REVIEWS_RATE_5')?>: <?=$arResult['REVIEWS']["Stats"]['Rate5TotalCount']?><br/>
    <?=GetMessage('REVIEWS_RATE_4')?>: <?=$arResult['REVIEWS']["Stats"]['Rate4TotalCount']?><br/>
    <?=GetMessage('REVIEWS_RATE_3')?>: <?=$arResult['REVIEWS']["Stats"]['Rate3TotalCount']?><br/>
    <?=GetMessage('REVIEWS_RATE_2')?>: <?=$arResult['REVIEWS']["Stats"]['Rate2TotalCount']?><br/>
    <?=GetMessage('REVIEWS_RATE_1')?>: <?=$arResult['REVIEWS']["Stats"]['Rate1TotalCount']?><br/>

</p>
<?endif?>

<? foreach ( $arResult['REVIEWS']["Reviews"] as $review):?>
<div>
    <p>
        <?=GetMessage('REVIEWS_AUTHOR_NAME')?>: <?=$review["AuthorName"]?>
        <br/>
        <?=GetMessage('REVIEWS_RATE')?>: <?=$review["Rate"]?>
        <br/>
        <?=GetMessage('REVIEWS_DATE')?>: <?=$review["Date"]?>
    </p>
    
    <p>
        <?=GetMessage('REVIEWS_TEXT')?>: <?=$review["ReviewText"]?>
        <br/>
        <?=GetMessage('REVIEWS_PROS')?>: <?=$review["Pros"]?>
        <br/>
        <?=GetMessage('REVIEWS_CONS')?>: <?=$review["Cons"]?>
    </p>
    <br/>
    <br/>
    
</div>

<? endforeach;?>

<?if ( $arParams['START']+$arParams['COUNT'] < $arResult['REVIEWS']["Stats"]['ReviewsTotalCount'] ) :?>
<input type="button" value="<?=GetMessage('REVIEWS_BUTTON')?>" id="OS_getDopReviews" onclick="OS_getDopReviews(<?=$arParams['START']+$arParams['COUNT']?>, <?=$arParams["SKU_ID"]?>)"/>
<?endif;?>

<?else:?>
    <p><?=GetMessage('NO_REVIEWS_BY_SKU_ID')?></p>
<? endif; ?>
