<div class="main-popular-category hero">
    <div class="container is-widescreen">
        <div class="columns">
            <div class="column list is-6">
                <h4 class="main-popular-left is-size-4">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/main_popular_category/title.php",
                        ),
                        false
                    );?>
                </h4>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_popular_category/links.php",
                    ),
                    false
                );?>
            </div>
            <div class="column is-6 attachment">
                <div class="main-popular-right pdf-file has-text-right">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR."include/main_popular_category/attachment.php",
                        ),
                        false
                    );?>
                </div>
                <p><?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_popular_category/description.php",
                    ),
                    false
                );?></p>
                <p class="small"><?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR."include/main_popular_category/description_small.php",
                    ),
                    false
                );?></p>
            </div>
        </div>
    </div>
</div>