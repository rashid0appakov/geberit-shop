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
//pr($arResult);
$this->setFrameMode(true);
$arImg = CFile::ResizeImageGet(
	$arResult['~PREVIEW_PICTURE'],
	array('width' => 582, 'height' => 300),
	BX_RESIZE_IMAGE_EXACT,
	true
);
?>
<div class="blog-detail">
	<?/*?>
	<div class="blog-detail-image">
		<img
			src="<?=$arImg["src"]?>"
			width="<?=$arImg["width"]?>"
			height="<?=$arImg["height"]?>"
			alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>"
			title="<?=$arResult["PREVIEW_PICTURE"]["TITLE"]?>"
			/>
	</div>
	<?*/?>
	<div>
		<?echo $arResult["~DETAIL_TEXT"];?>
		<p class="blog_author">Автор: <a href="https://yandex.ru/znatoki/user/tiptopshopru/" target="_blank">Василиса Домовая</p>
	</div>
	<div class="clearfix"></div>
	<div class="blog-detail-footer">
		<a href="/" class="blog-detail-link">К списку статей</a>
		<div class="blog-detail-share">
			<script src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
			<script src="https://yastatic.net/share2/share.js"></script>
			<div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir" data-counter="ig"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "BlogPosting",
	"headline": "<?=$arResult["NAME"]?>",
	"image": "https://blog.tiptop-shop.ru<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>",
	"editor": "Специалист по сантехнике ",
	"keywords": "<?=$arResult['IPROPERTY_VALUES']['ELEMENT_META_KEYWORDS']?>",
	"publisher": "ТипТоп-Шоп.ру",
	"url": "https://blog.tiptop-shop.ru/<?=$arResult["CODE"]?>/",
	"datePublished": "<?=date('Y-m-d', strtotime($arResult['ACTIVE_FROM']));?>",
	"dateCreated": "<?=date('Y-m-d', strtotime($arResult['ACTIVE_FROM']));?>",
	"dateModified": "<?=date('Y-m-d', strtotime($arResult['TIMESTAMP_X']));?>",
	"description": "<?=$arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION']?>",
	"articleBody": "<?echo str_replace('"', '', strip_tags($arResult["DETAIL_TEXT"]));?>",
	"author": {
		"@type": "Person",
		"name": "Александр"
	}
}
</script>