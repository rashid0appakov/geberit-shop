<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Сервисы");
?>
<div class="goods">
	<div class="container goods__container">
		<div class="goods__breadcrumbs">
			<? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "main",
                Array(
                    "PATH"      => "",
                    "SITE_ID"   => SITE_ID,
                    "START_FROM"=> "0"
                )
            );?>
		</div>
		<div class="goods__wrapper">
			<div class="goods__sidebar">
				 <?$APPLICATION->IncludeComponent(
					"bitrix:menu",
					"sidebar",
					array(
						"ALLOW_MULTI_SELECT" => "N",
						"CHILD_MENU_TYPE" => "sidebar",
						"DELAY" => "N",
						"MAX_LEVEL" => "4",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"ROOT_MENU_TYPE" => "sidebar",
						"USE_EXT" => "N",
						"COMPONENT_TEMPLATE" => "sidebar",
						"COMPOSITE_FRAME_MODE" => "A",
						"COMPOSITE_FRAME_TYPE" => "AUTO"
					),
					false
				);?>
			</div>
			<div class="goods__card">
				<div class="goods__title">
					<h1 class="goods__title-title"><?$APPLICATION->ShowTitle(FALSE)?></h1>
				</div>
                <?$APPLICATION->IncludeComponent(
					"bitrix:catalog.section.list",
					"payments",
					Array(
						"ADD_SECTIONS_CHAIN" => "N",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"COUNT_ELEMENTS" => "N",
						"IBLOCK_ID" => CClass::RU_SERVICES_IBLOCK_ID,
						"IBLOCK_TYPE" => "content",
						"SECTION_CODE" => "",
						"SECTION_FIELDS" => array(0=>"NAME",1=>"CODE",2=>"DESCRIPTION"),
						"SECTION_ID" => SERVICE_SECTION_ID,
						"SECTION_URL" => "",
						"SECTION_USER_FIELDS" => array(),
						"SHOW_PARENT_NAME" => "",
						"TOP_DEPTH" => "3",
						"VIEW_MODE" => ""
					)
				);?>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>