<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?><?

$id = $_REQUEST['id'];
if(!$id) { return; }
$arItem = CIBlockElement::GetList([],["ID"=>$id],false,false,["DETAIL_PAGE_URL","GIFT"])->GetNext();
$gifts=CGifts::getGifts([$id]);
$arItem["GIFT"]=$gifts[$id];
?>
<div class="h4">Подарок при покупке</div>
<div class="descr"><?
    $temp=[];
    foreach ($arItem["GIFT"] as $gift) { $temp[]=$gift["NAME"]; }
    echo '<b>'.implode('</b> или <b>', $temp).'</b>';?>
    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее на странице товара</a>
</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>