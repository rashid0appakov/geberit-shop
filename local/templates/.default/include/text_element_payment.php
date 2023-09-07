<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;

?>
<div class="payment-block__items">
<?
$res = CSalePaySystem::GetList($arOrder = ["SORT"=>"ASC", "PSA_NAME"=>"ASC"], ["LID"=>SITE_ID, "ACTIVE"=>"Y"], false, false, ['*']);
while($arPayment = $res->Fetch())
{
	//pr($arPayment);
	?>
	<div class="payment-block__item">
		<div class="payment-block__item-icon-wrapper">
			<div class="payment-block__item-icon"<?if($arPayment['PSA_LOGOTIP']):?> style="background-image: url('<?echo CFile::GetPath($arPayment['PSA_LOGOTIP']);?>')"<?endif?>></div>
		</div>
		<div class="payment-block__item-content">
			<h5 class="payment-block__item-title"><?=$arPayment['NAME']?></h5>
			<div class="payment-block__item-text">
				<p><?=$arPayment['DESCRIPTION']?></p>
			</div>
			<div class="payment-block__item-icons"></div>
		</div>
	</div>
	<?
}
?>
</div>
