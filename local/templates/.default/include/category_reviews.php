<?php

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;

$arCurSection = $arParams['SECTION'];
$arReviews = $arUsersNames = [];

if(false and $GLOBALS['USER']->IsAdmin())
{
	$cache = new CPHPCache();
	$cache_id = 'SECTION_REVIEWS_'.$arCurSection['ID'];
	if($cache->InitCache($arParams['CACHE_TIME'], $cache_id, "/"))
	{
		$arResult = $cache->GetVars();
		if(!empty($arResult['arReviews']))
		{
			$arReviews = $arResult['arReviews'];
		}
		else
		{
			$arReviews = false;
		}
		$arUsersNames = $arResult['arUsersNames'];
	}

	if(empty($arReviews) and $arReviews !== false)
	{
		if(CModule::IncludeModule('highloadblock'))
		{
			$hlbl = 8;
			$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

			$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
			$entity_data_class = $entity->getDataClass(); 

			$suffix = strtoupper(SITE_ID);
			$rsData = $entity_data_class::getList([
			   "select" => [
					"*"
			   ],
			   "order" => [
					"UF_DATE" => "DESC"
			   ],
			   "filter" => [
			    	"UF_SECTION_".$suffix => $arCurSection['ID'],
			    	"UF_ACTIVE" => 1
				],
				"limit" => 10,
			]);
			
			$arUsers = [];
			while($arData = $rsData->Fetch())
			{
				if(!empty($arData['UF_DATE']))
				{
					$arData['UF_DATE'] = $arData['UF_DATE']->toString();
				}
				$arReviews[] = [
					'CATEGORY_NAME' => $arCurSection['NAME'],
					'PERSON_NAME' => $arData['UF_USER'],
					'DATE' => $arData['UF_DATE'],
					'RATING' => $arData['UF_RATING'],
					'TEXT' => $arData['UF_TEXT']
				];
				$arUsers[$arData['UF_USER']] = $arData['UF_USER'];
			}
			if(count($arUsers))
			{
				$order = $sort = '';
				$arFilter = [
					'ID' => implode('|', $arUsers)
				];
				$rsUsers = CUser::GetList($order, $sort, $arFilter);
				while($arUser = $rsUsers->GetNext())
				{
					$arUsersNames[$arUser['ID']] = trim(implode(' ', [$arUser['NAME'], $arUser['LAST_NAME']]));
				}
			}
		}
		$cache->StartDataCache($arParams['CACHE_TIME'], $cache_id, "/");
		$cache->EndDataCache(array("arReviews" => $arReviews, "arUsersNames" => $arUsersNames));
	}
}

$marketURL = Option::get("tiptop", "template_market_url", "");
if(!empty($arReviews)):
?>
<div class="goods__review-slider">
<div class="goods__review-swiper swiper-container ">
	<div class="swiper-wrapper">
		<?php foreach($arReviews as $arReview): ?>
		<div class="swiper-slide">
			<div class="goods__review<?php if($arParams['IS_MOBILE'] == 'Y') echo ' goods__review--right' ?>">
				<p class="goods__review-title">Отзывы о товарах в разделе <?= $arReview['CATEGORY_NAME'] ?></p>
				<p class="goods__review-username"><?echo (!empty($arUsersNames[$arReview['PERSON_NAME']]) ? $arUsersNames[$arReview['PERSON_NAME']] : 'Гость');?></p>
				<div class="goods__review-rating">
					<img src="<?=SITE_DEFAULT_PATH?>/images/icons/goods__review-rating-<?= $arReview['RATING'] ?>-stars.png" alt="stars">
					<span><?= $arReview['DATE'] ?></span>
				</div>
				<p class="goods__review-description"><?= $arReview['TEXT'] ?></p>
				<div class="goods__review-link">
					<a class="goods__review-link-read">Читать полностью</a>
					<a class="goods__review-link-close goods__review-link--close">Скрыть</a>
				</div>
				<?if($marketURL):?>
					<div class="goods__review-button">
						<a href="<?= $marketURL ?>" class="btn is-primary is-outlined">Оставить отзыв на Маркете</a>
					</div>
				<?endif?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="goods__review-slider-button carousel">
		<div class="goods__review-slider-button--container">
			<div class="arrow left product-new-filter-left"></div>
			<div class="arrow right  product-new-filter-right"></div>
		</div>
	</div>
</div>
<?/*
<div class="owl-carousel owl-theme goods__review-owl">
	<?php foreach($arReviews as $arReview): ?>
	<div class="goods__review">
		<p class="goods__review-title">Отзывы о товарах в разделе <?= $arReview['CATEGORY_NAME'] ?></p>
		<p class="goods__review-username"><?echo (!empty($arUsersNames[$arReview['PERSON_NAME']]) ? $arUsersNames[$arReview['PERSON_NAME']] : 'Гость');?></p>
		<div class="goods__review-rating">
			<img src="<?=SITE_DEFAULT_PATH?>/images/icons/goods__review-rating-<?= $arReview['RATING'] ?>-stars.png" alt="stars">
			<span><?= $arReview['DATE'] ?></span>
		</div>
		<p class="goods__review-description"><?= $arReview['TEXT'] ?></p>
		<div class="goods__review-link">
			<a href="#">Читать полностью</a>
			<div class="goods__review-slider-button carousel">
				<div class="arrow left product-new-left"></div>
				<div class="arrow right product-new-right product-new-right--active"></div>
			</div>
		</div>
		<?if($marketURL):?>
			<div class="goods__review-button">
				<a href="<?= $marketURL ?>" class="btn is-primary is-outlined">Оставить отзыв на Маркете</a>
			</div>
		<?endif?>
	</div>
	<?php endforeach; ?>
</div>
*/?>
</div>
<? endif;?>