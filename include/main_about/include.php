<div class="main-about hero">
    <div class="container is-widescreen">
        <div class="columns is-mobile is-multiline">
            <div class="column is-7 shopinfo">
                <h2 class="animate-about-left is-size-3">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/main_about/title.php"
                        ),
                        false
                    );?>
                </h2>
                <p class="animate-about-left">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/main_about/description.php"
                        ),
                        false
                    );?>
                </p>
                <div class="links animate-about-left">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/main_about/links.php"
                        ),
                        false
                    );?>
                </div>
            </div>
            <div class="column is-4 has-text-right logo">
                <p class="animate-about-right">Приглашаем к сотрудничеству архитекторов, дизайнеров и строительные организации</p>
            </div>
            <div class="animate-about-bottom info column is-4">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_about/item1.php",
                    ),
                    false
                );?>
            </div>
            <div class="animate-about-bottom info column is-4">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_about/item2.php",
                    ),
                    false
                );?>
            </div>
            <div class="animate-about-bottom info column is-4">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_about/item3.php",
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</div>
<hr>