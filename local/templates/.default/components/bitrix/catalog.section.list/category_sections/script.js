(function(window) {
    if (!!window.JSCatalogSectionListCategorySections) return;

    window.JSCatalogSectionListCategorySections = function(arParams) {
        this.params = null;
        this.containerSelector = null;
        this.imageContainerSelector = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.containerSelector = arParams.containerSelector;
            this.imageContainerSelector = arParams.imageContainerSelector;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogSectionListCategorySections.prototype.GetContainer = function() {
        return $(this.containerSelector);
    };

    window.JSCatalogSectionListCategorySections.prototype.GetItems = function() {
        return this.GetContainer().find(".categoryCardWrapper");
    };

    window.JSCatalogSectionListCategorySections.prototype.Init = function() {
        this.AnimateItems();
    };

    window.JSCatalogSectionListCategorySections.prototype.AnimateItems = function() {
        var items = this.GetItems();
        items.each($.proxy(function(i, item) {
            this.AnimateItem($(item));
        }, this));
    };

    window.JSCatalogSectionListCategorySections.prototype.AnimateItem = function(item) {
        var imageContainer = item.find(".categoryImage");

        var imageWidth = 230;
        var imageCount = imageContainer.find("img").length;
        var containerWidth = imageCount * imageWidth;
        imageContainer.width(containerWidth);

        if (imageCount > 1) {
            item.on("mouseenter", function() {
                imageContainer.animate({
                    right: containerWidth - imageWidth
                }, 2000)
            });
            item.on("mouseleave", function() {
                imageContainer.stop();
            });
        }
    };
})(window);