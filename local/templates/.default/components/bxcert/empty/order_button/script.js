(function(window, $) {
    if (!!window.JSOrderButton) return;

    window.JSOrderButton = function(params) {
        if (typeof params === "object") {
            this.buttonSelector = params.buttonSelector;
            this.countSelector = params.countSelector;
        }

        $(window.document).on("basketinit", $.proxy(this.OnBasketInit, this));
        $(window.document).on("basketitemadd", $.proxy(this.OnBasketItemAdd, this));
        $(window.document).on("basketitemdelete", $.proxy(this.OnBasketItemDeleted, this));
    };

    window.JSOrderButton.prototype.OnBasketInit = function(event, data) {
        this.Update(data);
    };

    window.JSOrderButton.prototype.OnBasketItemAdd = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSOrderButton.prototype.OnBasketItemDeleted = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSOrderButton.prototype.Update = function(data) {
        var count = $(this.countSelector);
        count.text(data.PRODUCT_COUNT);

        if (data.PRODUCT_COUNT <= 0) {
            $(this.buttonSelector).addClass("is-disabled");
        } else {
            $(this.buttonSelector).removeClass("is-disabled");
        }
    };

})(window, jQuery);