(function(window, $) {
    if (!!window.JSCatalogBreadcrumbs) return;

    window.JSCatalogBreadcrumbs = function(params) {
        if (typeof params === "object") {
            this.uid = params.uid;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogBreadcrumbs.prototype.Init = function() {
        $("#breadcrumbs_"+this.uid).on("click", ".dropdown-icon", $.proxy(this.OnDropdownClick, this));
        $(window.document).on("click", $.proxy(this.OnDocumentClick, this));
    };

    window.JSCatalogBreadcrumbs.prototype.OnDropdownClick = function(event) {
        var $dropdown = $(event.target).closest(".dropdown-icon");
        var $dropdownWrap = $dropdown.find(".dropdown-wrap");
        
        $dropdownWrap.toggleClass("close");
    };

    window.JSCatalogBreadcrumbs.prototype.OnDocumentClick = function(event) {
        var $breadcrumbs = $("#breadcrumbs_"+this.uid);
        var $dropdown = $breadcrumbs.find(".dropdown-icon");
        var $dropdownWrap = $breadcrumbs.find(".dropdown-wrap");

        if ($dropdown.has(event.target).length === 0) {
            $dropdownWrap.addClass("close");
        }
    };

})(window, window.jQuery);