<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$uniqueId = $this->randString();

$params = base64_encode(serialize($arParams));
$inputId = "input_$uniqueId";
$containerId = "container_$uniqueId";
$ajaxUrl = $this->__component->GetPath()."/ajax.php";

$jsParams = array(
	"params" => $params,
	"inputSelector" => "#$inputId",
	"containerSelector" => "#$containerId",
	"ajaxUrl" => $ajaxUrl,
	"storeCity" => $arParams["STORE_CITY"],
	"deliveryCity" => $arParams["DELIVERY_CITY"],
	"images" => array(
		"store" => $templateFolder."/images/box-mini.png",
		"delivery" => $templateFolder."/images/auto-mini.png",
	),
	"defaultCity" => $arResult["DEFAULT_CITY"],
);?>

<input type="radio" id="open-modal-region" name="popup-change-region" class="system">
<div class="modal">
	<div class="modal-background"></div>
	<div class="modal-content">
		<h4 class="is-size-3">Выбор региона</h4>
        <?/*/?>
        <?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DEFAULT_PATH."/include/template_popup_region1.php"
			),
			false
		);?><?/**/?>
		<div class="field search">
			<p class="control has-icons-right">
				<input id="<?=$inputId?>" class="input" type="text" placeholder="Начинайте вводить название города для поиска">
			</p>
		</div>
		<div id="<?=$containerId?>" class="cities">
            <?foreach($arResult["DEFAULT_CITY"] as $item){
                $mark = in_array($item['ID'],$arParams["STORE_CITY"]) || in_array($item['ID'],$arParams["DELIVERY_CITY"]) ;
                $href = '';
                if($item['ID']==129){ $href='https://geberit-shop.ru'; }
                if($item['ID']==817){ $href='https://spb.geberit-shop.ru'; }
                if($item['ID']==2201){ $href='https://ekb.geberit-shop.ru'; }
                if($item['ID']==2622){ $href='https://novosibirsk.geberit-shop.ru'; }
                if($item['ID']==1095){ $href='https://krasnodar.geberit-shop.ru'; }
                if(!$href){ continue; }?>
                <span class="city<?=$mark?' mark':''?>">
                    <a href="<?=$href?>" title="<?=$item['PATH']?>" data-id="<?=$item['ID']?>"><?=$item['NAME']?></a>
                    <?if($mark){?>
                        <img src="<?=$templateFolder."/images/box-mini.png"?>"> 
                        <img src="<?=$templateFolder."/images/auto-mini.png"?>">
                    <?}?>
                </span><?
            }?>
        </div>
        <?/*/?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DEFAULT_PATH."/include/template_popup_region2.php"
			),
			false
		);?><?/**/?>
		<label class="close" for="close-modal-region"></label>
		<input type="radio" id="close-modal-region" name="popup-change-region" class="system">
	</div>
</div>
<script type="text/javascript">
    //$(function() {
        $('input[id^="open_change_region_"]').change(function() { if($(this).attr('data-loaded')){return;} $(this).attr('data-loaded','true'); 
            new JSGeolocationSelectPopup(<?=json_encode($jsParams)?>); 
            $.ajax({
                url: "<?=SITE_DEFAULT_PATH.'/include/template_popup_region1.php'?>",
                method: "POST",
                success: function(data) { $('.modal h4.is-size-3').after(data); },
            });
            $.ajax({
                url: "<?=SITE_DEFAULT_PATH.'/include/template_popup_region2.php'?>",
                method: "POST",
                success: function(data) { $('.modal div.cities').after(data); },
            });
        });
    //});
</script>