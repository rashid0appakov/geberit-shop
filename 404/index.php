<?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

    CHTTP::SetStatus("404 Not Found");
    @define("ERROR_404", "Y");

    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Страница не найдена :(");
    $APPLICATION->AddChainItem("Ошибка", "/");
    $APPLICATION->SetDirProperty("title", "Страница не найдена :(");
    $APPLICATION->SetPageProperty("title", "Страница не найдена :(");
?>
    <div class="goods page-404">
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
                <div class="content">
                    <h1><?=$APPLICATION->ShowTitle(FALSE)?></h1>
                    <div class="gray-block">
                        <div class="sub-title">К сожалению, запрашиваемая Вами страница<br />не найдена на сайте нашей компании.</div>
                        <p><strong>Мы приносим свои извинения за доставленные неудобства<br />и предлагаем следующие пути:</strong></p><br />
                        <div class="padding-left-60">
                            <p>—   Перейти на <a href="/">главную страницу</a></p>
                            <?php
                                $dynamicArea = new \Bitrix\Main\Page\FrameStatic("phone_404");
                                $dynamicArea->setAnimation(true);
                                $dynamicArea->startDynamicArea();
                            ?>
                            <p>—   Связаться с нами по телефону <a href="tel:<?=$arContact['PHONE']['NUMBER']?>"><?=$arContact['PHONE']['VALUE']?></a></p>
                            <? $dynamicArea->finishDynamicArea();?>
                            <p>—   Написать письмо нашим сотрудникам: <a href="mailto:info@shop-neptun.ru">info@shop-neptun.ru</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>