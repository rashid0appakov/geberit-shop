<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}
?>
<div class="descr">
<?
$pId=$_REQUEST['pid']??false;
if($pId>0 && \Bitrix\Main\Loader::includeModule('iblock'))
{
	$item=\Bitrix\Iblock\ElementTable::getList([
		"select"=>['DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL',"CODE","ID"],
		"filter"=>["IBLOCK_ID"=>"84","ID"=>$pId],
		"limit"=>1,
	])->fetch();
	if($item)
	{
		$item['DETAIL_PAGE_URL']=CIBlock::ReplaceDetailUrl($item['DETAIL_PAGE_URL'], $item, false, 'E');
		$gifts=CGifts::getGifts([$item["ID"]]);
		$temp=[];
		foreach ($gifts[$item["ID"]] as $gift) {
			$temp[]=$gift["NAME"];
		}
		
		echo '<b>'.implode('</b> или <b>', $temp).'</b>';?>
		<a href="<?=$item["DETAIL_PAGE_URL"]?>">Подробнее на странице товара</a>
		
		<?
	}
}?>
</div>