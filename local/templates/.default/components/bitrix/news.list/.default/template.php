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
?>
<div class="goods__review-slider">
    <div class="goods__review-swiper swiper-container swiper-container-horizontal">
        <div class="swiper-wrapper">
            <? foreach ($arResult["ITEMS"] as $arFeedback) { ?>
<!--            --><?// if ($arFeedback["ACTIVE"] === 'N') continue; ?>
            <div class="swiper-slide">
                <div class="goods__review">
                    <p class="goods__review-title">Отзывы о товарах в разделе <?=$arResult["sectionName"]?></p>
                    <p class="goods__review-username"><?=$arFeedback["userName"]?></p>
                    <div class="goods__review-rating">
<!--                        <img src="/local/templates/.default/images/goods__review-rating-4-stars.png" alt="stars">-->
                        <span><?=$arFeedback["DATE_CREATE"]?></span>
                    </div>
                    <p class="goods__review-description"><?=$arFeedback["DETAIL_TEXT"]?></p>
                    <div class="goods__review-link">
                        <a class="goods__review-link-read">Читать полностью</a>
                        <a class="goods__review-link-close goods__review-link--close">Скрыть</a>
                    </div>
                    <div class="goods__review-button">
                        <a href="#" class="btn is-primary is-outlined">Оставить отзыв на Маркете</a>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        <div class="goods__review-slider-button carousel">
            <div class="goods__review-slider-button--container">
                <div class="arrow left product-new-filter-left"></div>
                <div class="arrow right  product-new-filter-right"></div>
            </div>
        </div>
    </div>
</div>