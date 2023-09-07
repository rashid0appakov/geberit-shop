<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;

global $APPLICATION;

include($_SERVER['DOCUMENT_ROOT'].SITE_DEFAULT_PATH.'/include/element_functions.php');

$arParams = json_decode($_REQUEST['params'],true);

$arOdds = CClass::getProductOds();
$arSections = CClass::getCatalogSection();

$arResult = [];
$arResult['ID'] = $_REQUEST['id'];
$arResult['PROPERTIES']['COLLECTION']['VALUE'] = json_decode($_REQUEST['prop1'],true);
$arResult['PROPERTIES']['COLLECTION_ITEMS']['~VALUE'] = $_REQUEST['prop2'];

$MESS = json_decode($_REQUEST['mess'],true);

$carouselId = $_REQUEST['carouselId'];
$leftArrowId = $_REQUEST['leftArrowId'];
$rightArrowId = $_REQUEST['rightArrowId'];
$active = $_REQUEST['tab'];

$cacheProps = [
	'COLLECTION' => 'COLLECTION_ITEMS',
];

foreach($cacheProps as $prop1=>$prop2){
    // -- If not empty CASHED value of current properrty -------------------- //
    $arCASHedValue  = $arResult['PROPERTIES'][$prop2]['~VALUE'] ? json_decode($arResult['PROPERTIES'][$prop2]['~VALUE'], true) : [];
	if (!empty($arCASHedValue)){
		$arAddItemsId[$prop1] = [];
		foreach($arCASHedValue AS &$sec){
			$i = 0;
			foreach($sec AS $id){
				$i ++;
				/*if($i > 8)
					break;*/
				$arAddItemsId[$prop1][] = $id;
			}
		}
	}else{
        if (!empty($arResult['PROPERTIES'][$prop1]['VALUE']))
            $arAddItemsId[$prop1] = $arResult['PROPERTIES'][$prop1]['VALUE'];
    }
}
//Получаем все доп. товары
$arResAddItems = GetAdditionalsNew($arAddItemsId, $arOdds, $arSections, [], $arParams["IBLOCK_ID"]);

//Раскидываем всякие доп. товары
$arResult['COLLECTION'] = $arResAddItems['COLLECTION'];

//var_dump(count($arResult['COLLECTION']));
//return;

$i = 2;
foreach( $arResult['COLLECTION'] AS $key => &$sec){
    $carouselId .= '_'.$i;
    $leftArrowId .= '_'.$i;
    $rightArrowId .= '_'.$i;
    ?>
    <div class="content tabs__content js-tabs-content-collection<?=strpos($active,$i)!==false?' active':''?>" id="tab-goods-collection-<?=$i;?>">
        <div class="swiper-container swiper-container-hit3 preview-products" id="<?=$carouselId?>">
            <div class="swiper-wrapper"><?
                $ii = 0;
                foreach($sec AS &$arItem){
                    $ii ++;
                    if($ii > 8) { break; }?>
                    <div class="swiper-slide">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:catalog.item",
                            "product",
                            array(
                                "RESULT" => array(
                                    "ITEM" => $arItem,
                                ),
                                "PARAMS" => $arParams
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );?>
                    </div><? 
                } ?>
            </div>
        </div>
        <button id="<?=$leftArrowId?>" class="arrow left product-tabs-hit3-left"></button>
        <button id="<?=$rightArrowId?>" class="arrow right product-tabs-hit3-right custom-ajax-carousel-preload" data-tab="<?=$key;?>" data-id="<?=$carouselId?>" data-filter-key="ELEMENT" data-skip="2" data-pp="4" data-element="<?=$arResult['ID']?>" data-type="COLLECTION"></button>
        <div class="finger-mobile"><?=GetMessage('CT_MOVE_FINGER')?></div>
    </div>
    <?
    $jsParams = array(
        "carouselSelector" => "#$carouselId",
        "leftArrowSelector" => "#$leftArrowId",
        "rightArrowSelector" => "#$rightArrowId",
        "showCount" => 4
    );?>
    <script type="text/javascript">
        new JSCatalogSectionCarousel(<?=json_encode($jsParams)?>);
        new Swiper('<?=$jsParams['carouselSelector']?>', {
        slidesPerView: 4,
        spaceBetween: 30,
        loop: false,
        observer: true,
        observeParents: true,
        navigation: {
            prevEl: '<?=$jsParams['leftArrowSelector']?>',
            nextEl: '<?=$jsParams['rightArrowSelector']?>',
        },
        breakpoints: {
            1200: {
            slidesPerView: 3
            },
            768: {
            spaceBetween: 0,
            slidesPerView: 3
            },
            550: {
            spaceBetween: 0,
            slidesPerView: 2
            }
        }
        });
    </script>
    <?
    $i++;
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
