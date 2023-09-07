<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>
<?foreach ($arResult as $itemIndex => $arItem):
	$target = '';
	if(strpos($arItem["LINK"], 'http') === 0)
	{
		$target = '_blank';
	}
	?>
	<a href="<?=$arItem["LINK"]?>" class="is-size-5"<?if($target):?> target="<?=$target?>"<?endif?>><?=$arItem["TEXT"]?></a>
<?endforeach;?>