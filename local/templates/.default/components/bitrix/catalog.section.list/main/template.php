<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = $this->randString();

$params = base64_encode(serialize($arParams));
$containerId = "container_$uniqueId";

$jsParams = array(
	"params" => $params,
	"containerSelector" => "#$containerId",
);
?>



<div class="goods">


	<div class="categoryWrapper">

		<div class="container goods__container">
			<div class="goods__breadcrumbs">
				<ul class="breadcrumbs">
					<li class="breadcrumbs__item">
						<a href="#">Главная</a>
					</li>
					<li class="breadcrumbs__item">
						<span>Каталог</span>

					</li>
				</ul>

			</div>

			<div class="goods__title">
				<h2 class="goods__title-title">Каталог</h2>
				<!-- <p class="goods__title-description">В каталоге от 35 моделей инсталляций GEBERIT для: умывальников, унитазов, биде, писсуаров в 6 коллекциях. Прямоугольные конструкции сделанные из нержавеющей стали – являются прочными и устойчивыми изделиями, которые прошли сертификацию качества.</p>
				<a href="#" class="goods__title-link">Подробнее</a> -->
			</div>
			<div class="columns is-gapless is-multiline categoryCardsWrapper">

				<?foreach ($arResult["SECTIONS"] as $arSection):?>
					<?php
					/*$arShowImages = [];
					$res = CIBlockElement::GetList([], ['SECTION_ID' => $arSection['ID']], false, [], ['PREVIEW_PICTURE']);
					
					for($i = 0; $i < 3; $i++){
						$ob = $res->GetNextElement();
						$arFields = $ob->GetFields();
						$arShowImages[] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
					}*/
					?>

					<div class="column is-12-mobile is-4-tablet is-3-desktop">
						<div class="categoryCardWrapper">
							<div class="categoryCard">
								<a href="<?=$arSection["SECTION_PAGE_URL"]?>">
									<div class="categoryImages">
										<div class="categoryImage"><!--
											<?foreach ($arShowImages as $img):?>
												--><img src="<?=$img?>"><!--
											<?endforeach;?>
										--></div>
									</div>
									<div class="categoryTitle">
										<span class="title"><?=$arSection["NAME"]?></span>
										<span class="categoryNum"><?=$arSection["ELEMENT_CNT"]?> товаров</span>
									</div>
								</a>
							</div>
						</div>
					</div>

				<?endforeach;?>

			</div>
		</div>


</div>

<br/>
<br/>
<br/>
<br/>