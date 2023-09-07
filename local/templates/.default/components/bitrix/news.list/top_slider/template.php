<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (count($arResult["ITEMS"]) <= 0)
    return;

$uniqueId = uniqid("slider");
$jsParams = array(
	"carouselId" => $uniqueId,
);
?>
<div id="top_promo" class="promo hero">
	<div class="container">
		<div id="<?=$uniqueId?>" class="owl-carousel owl-theme">
			<?foreach ($arResult["ITEMS"] AS &$arItem):
				$i=0;
				if($arItem["PROPERTIES"]['PIC']['VALUE']){
					foreach ($arItem["PROPERTIES"]['PIC']['VALUE'] as $key => $value) {
						$link = $arItem["PROPERTIES"]['PIC']['DESCRIPTION'][$i];
						?>
						<div class="owl-carousel__item">
							<?if (!!$link):?><a href="<?=$link?>"<?=$newTab ? ' target="_blank"' : ''?>><?endif;?>
							<img src="<?=CFile::GetPath($value)?>" alt="<?=$arItem['NAME']?>" />
							<?if (!!$link):?></a><?endif;?>
						</div>
						<?
						$i++;
					}	
				}else{
					?>
					<div class="owl-carousel__item">
							<?=$arItem['PREVIEW_TEXT']?>
					</div>
					<?
				}
				
				
			endforeach;?>
		</div>
	</div>	
</div>
<script>
	var mainSlider = new JSMainSlider(<?=json_encode($jsParams)?>);
</script>