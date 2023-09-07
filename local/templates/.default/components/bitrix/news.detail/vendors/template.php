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
//pr($arResult);
$templateData['robots'] = 'index, follow';
if(defined('MAIN_SITE_BRAND') and !in_array($arResult['ID'], MAIN_SITE_BRAND))
{
	$templateData['robots'] = 'noindex, nofollow';
}

	$this->SetViewTarget("BRAND_BG");
	if ($arResult['DETAIL_PICTURE']['SRC'])
		$style  = ' style="background-image: url(\''.$arResult['DETAIL_PICTURE']['SRC'].'\')"';
?>
<div class="series-background<?=(!$style ? ' no-bg' : '')?>"<?=$style?>></div>
<?$this->EndViewTarget();?>
<div class="goods__wrapper<?=(!$style ? ' no-bg' : '')?>">
	<div class="content">
		<? if (!empty($arResult['RESIZED'])):?>
		<div class="series-title-right">
			<img src="<?=$arResult['RESIZED']['src']?>" alt="<?=$arResult['NAME']?>" />
		</div>
		<? endif;?>
		<div class="series-title-left">
			<h1><?=$arResult['NAME'].' '.CClass::getNameSubdomain()?></h1>
			<? if (!empty($arResult['PROPERTIES']['COUNTRY']['VALUE'])):?>
			<div class="country-block"><?=$arResult['DISPLAY_PROPERTIES']['COUNTRY']['DISPLAY_VALUE']?></div>
			<? endif;?>
			<div class="series-description"><?=(is_array($arResult['DETAIL_TEXT']) ? $arResult['~DETAIL_TEXT']['TEXT'] : $arResult['DETAIL_TEXT'])?></div>
			<? if (!empty($arResult['PROPERTIES']['SITE_LINK']['VALUE'])):?>
			<div class="brand-detail-link">
				<a href="<?=$arResult['PROPERTIES']['SITE_LINK']['VALUE']?>" target="_blank"><?= str_replace(['http://', 'https://'], '',$arResult['PROPERTIES']['SITE_LINK']['VALUE'])?></a>
			</div>
			<? endif;?>
		</div>
	</div>

	<? if (!empty($arResult['SECTIONS'])):
		$uniqueId = $this->randString();

		$params = base64_encode(serialize($arParams));
		$containerId = "container_$uniqueId";

		$jsParams = array(
			"params" => $params,
			"containerSelector" => "#$containerId",
		);
	?>
	<h2>Каталог <?=$arResult['NAME']?></h2>
	<div class="categoryWrapper">
		<div id="<?=$containerId?>" class="columns is-gapless is-multiline categoryCardsWrapper">
			<? foreach($arResult['SECTIONS'] AS &$arSection):?>
				<div class="column is-12-mobile is-4-tablet is-3-desktop">
					<div class="categoryCardWrapper">
						<div class="categoryCard">
							<? if (!empty($arSection['RESIZED'])):?>
							<div class="categoryImages">
								<div class="categoryImage">
									<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
									<?foreach ($arSection['RESIZED'] AS &$arImg):?>
										<div class="categoryImageWrapper">
											<img src="<?=$arImg['src']?>" alt="" />
										</div>
									<?endforeach;?>
									</a>
								</div>
							</div>
							<? endif;?>
							<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="categoryTitle">
								<span class="title"><?=$arSection["NAME"]?></span>
								<span class="categoryNum"><?=$arSection["ITEMS_COUNT"]?> <?=GetMessage('HDR_ITEM_TITLE').CClass::getFilesEnds($arSection["ITEMS_COUNT"])?></span>
							</a>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
	<script type="text/javascript">
		window.sectionList = new JSCatalogSectionListCategorySections(<?=json_encode($jsParams)?>);
	</script>
	<? endif;?>
	<?/*<div class="go-back">
		<a href="<?=$arParams['SECTION_URL']?>" class="news-page-back__link"><?=GetMessage('T_NEWS_DETAIL_BACK')?></a>
	</div>*/?>
</div>

<? if (!empty($arResult['SERIES'])):
	$this->SetViewTarget("BRAND_SERIES");?>
	<div class="brand-collections-wrapper">
		<div class="container goods__container">
			<h2>Коллекции <?=$arResult['NAME']?></h2>
			<div class="series-items">
		<?
		$lettersFilter = (count($arResult['SERIES']) > 20);
		if($lettersFilter):
			foreach($arResult["SERIES"] AS &$arItem)
			{
				$arItem['LETTER'] = ToLower(substr($arItem['NAME'], 0, 1));
				if(isset($arResult["LETTERS"][$arItem['LETTER']]))
				{
					$arResult["LETTERS"][$arItem['LETTER']] ++;
				}
				else
				{
					$arResult["LETTERS"][$arItem['LETTER']] = 1;
				}
			}
			?>
			<div id="brand-letters">
				<ul>
					<?foreach($arResult['LETTERS'] as $letter=>$cnt):?>
						<li><a href="#" data-letter="<?=$letter?>" title="<?=$cnt?> брендов на эту букву"><?=ToUpper($letter);?></a></li>
					<?endforeach?>
					<li><a href="#" data-letter="-" title="Будут выведены все бренды" class="active">Все коллекции</a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
		<?
		endif;
		foreach($arResult['SERIES'] AS &$arItem):?>
				<div class="series-item<?if($lettersFilter):?> brand-letters brand-letter-<?=$arItem['LETTER']?><?endif?>">
					<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><span><?=$arItem['NAME']?></span></a>
				</div>
		<? endforeach;?>
			</div>
		</div>
	</div>
	<?$this->EndViewTarget();?>
<?endif; ?>
<?$this->SetViewTarget("BRAND_TITLE");?><?=$arResult['NAME']?><?$this->EndViewTarget();?>
