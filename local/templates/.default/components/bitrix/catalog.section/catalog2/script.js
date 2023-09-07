(function(window) {
    if (!!window.JSCatalogSection) return;

    window.JSCatalogSection = function(arParams) {
        if (typeof arParams === "object") {
            this.navElementSelector = arParams.navElementSelector;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogSection.prototype.Init = function() {
        this.InitNav();
    };

    window.JSCatalogSection.prototype.InitNav = function() {
        $(window.document).on(
            "click",
            this.navElementSelector+" a",
            $.proxy(this.OnNavClick, this)
        );
    };

    window.JSCatalogSection.prototype.OnNavClick = function(event) {
        var a = $(event.target).closest("a");

        if (!a.attr("disabled")) {
            a.attr("disabled", "disabled");

            $.get(a.attr("href"), {"IS_AJAX": "Y"}, $.proxy(this.OnNavLoaded, this));
        }

        return false;
    };

    window.JSCatalogSection.prototype.OnNavLoaded = function(data) {
        var navBlock = $(this.navElementSelector);

        navBlock.after(data);
        navBlock.remove();
    };

})(window);

