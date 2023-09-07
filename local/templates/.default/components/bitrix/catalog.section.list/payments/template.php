<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
//pr($arResult["SECTIONS"]);
if(count($arResult["SECTIONS"]) < 1)
	return;?>
<section class="tabs goods__tabs goods__tabs-table-title">
	<div class="goods__tabs-button" id="goods__tabs-button-1"></div>
	<ul class="tabs__header" id="tabs__header-1">
		<?foreach($arResult["SECTIONS"] AS $k => &$arSection):?>
			<li class="tabs__header--title js-tabs-title <?=!$k ? 'active': '' ?>" data-tab="#tab-<?=($k+1)?>" id="click-<?=$arSection['ID']?>"><?=$arSection["NAME"]?></li>
		<?endforeach;?>
	</ul>
	<div class="tabs__underline js-tabs-underline"></div>
</section>
<?/*
<section class="tabs payments__tabs">
	<ul class="tabs__header">
		<?foreach($arResult["SECTIONS"] AS $k => &$arSection):?>
			<li class="tabs__header--title js-tabs-title <?=!$k ? 'active': '' ?>" data-tab="#tab-<?=($k+1)?>" id="click-<?=$arSection['CODE']?>"><?=$arSection["NAME"]?></li>
		<?endforeach;?>
	</ul>
	<div class="tabs__underline js-tabs-underline"></div>
</section>
*/?>
<div class="content">
	<?


foreach($arResult["SECTIONS"] AS $k => &$arSection):?>
	<div class="content tabs__content js-tabs-content <?=!$k ? 'active': '' ?>" id="tab-<?=($k+1)?>">
		<div class="payment-block">
			<?if(!empty($arSection["DESCRIPTION"])):?>
				<div class="payment-block__description"><?=$arSection["DESCRIPTION"]?></div>
			<?endif;?>

			<?if (!empty($arResult["ITEMS"][$arSection['ID']])):?>
				<div class="payment-block__items">
					<?foreach($arResult["ITEMS"][$arSection['ID']] AS &$arItem):?>
						<div class="payment-block__item <?=(!is_array($arItem["LOGO_1"])) ? ' payment-block__item--no-logo' : ''?>">
							<?if($arItem['LOGO_1']['src']):?>
								<div class="payment-block__item-icon-wrapper">
									<div class="payment-block__item-icon" style="background-image: url('<?=$arItem['LOGO_1']['src']?>')"></div>
								</div>
							<?endif;?>
							<div class="payment-block__item-content">
								<h5 class="payment-block__item-title"><?=$arItem["NAME"]?></h5>
								<?if(!empty($arItem["PREVIEW_TEXT"])):?>
									<div class="payment-block__item-text">
										<p><?=$arItem["PREVIEW_TEXT"]?></p>
									</div>
								<?endif;?>
								<div class="payment-block__item-icons"></div>
							</div>
						</div>
					<?endforeach;?>
				</div>
			<?endif;?>
		</div>
	</div>
	<?endforeach;?>
</div>