<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (count($arResult["ITEMS"]) <= 0) return;?>
<div class="benefits hero">
	<div class="container is-widescreen">
		<div class="columns is-mobile">
			<?foreach ($arResult["ITEMS"] as $arItem):
				$uniqueId = uniqid("benefit");?>
				<div class="column" id="<?=$uniqueId?>">
					<div>
						<p class="title"><strong><?=$arItem["FIELDS"]["NAME"]?></strong></p>
						<p><?=$arItem["FIELDS"]["PREVIEW_TEXT"]?></p>
					</div>
				</div>

			<?endforeach;?>
		</div>
	</div>
</div>