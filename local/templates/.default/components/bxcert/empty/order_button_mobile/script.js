(function(window, $) {
    if (!!window.JSOrderButtonMobile) return;

    window.JSOrderButtonMobile = function(params) {
        if (typeof params === "object") {
            this.buttonSelector = params.buttonSelector;
            this.countSelector = params.countSelector;
        }

        $(window.document).on("basketinit", $.proxy(this.OnBasketInit, this));
        $(window.document).on("basketitemadd", $.proxy(this.OnBasketItemAdd, this));
        $(window.document).on("basketitemdelete", $.proxy(this.OnBasketItemDeleted, this));
    };

    window.JSOrderButtonMobile.prototype.OnBasketInit = function(event, data) {
        this.Update(data);
    };

    window.JSOrderButtonMobile.prototype.OnBasketItemAdd = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSOrderButtonMobile.prototype.OnBasketItemDeleted = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSOrderButtonMobile.prototype.Update = function(data) {
        var count = $(this.countSelector);
        count.text(data.PRODUCT_COUNT);

        if (data.PRODUCT_COUNT <= 0) {
            $(this.buttonSelector).addClass("is-disabled");
        } else {
            $(this.buttonSelector).removeClass("is-disabled");
        }
    };

})(window, jQuery);