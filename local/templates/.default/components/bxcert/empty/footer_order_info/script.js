(function(window, $) {
    if (!!window.JSFooterOrderInfo) return;

    window.JSFooterOrderInfo = function(params) {
        if (typeof params === "object") {
            this.countSelector = params.count_selector;
            this.priceSelector = params.price_selector;
        }

        $(window.document).on("basketinit", $.proxy(this.OnBasketInit, this));
        $(window.document).on("basketitemadd", $.proxy(this.OnBasketItemAdded, this));
        $(window.document).on("basketitemdelete", $.proxy(this.OnBasketItemDeleted, this));
        $(window.document).on("basketitemquantitychange", $.proxy(this.OnBasketItemQuantityChanged, this));
    };

    window.JSFooterOrderInfo.prototype.OnBasketInit = function(event, data) {
        this.Update(data);
    };

    window.JSFooterOrderInfo.prototype.OnBasketItemAdded = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSFooterOrderInfo.prototype.OnBasketItemDeleted = function(event, data) {
        this.Update(data.BASKET);
    };

    window.JSFooterOrderInfo.prototype.OnBasketItemQuantityChanged = function(event, data) {
        this.Update(data.BASKET);
    };


    window.JSFooterOrderInfo.prototype.Update = function(data) {
        var $count = $(this.countSelector);
        $count.text(data.PRODUCT_COUNT);

        var $price = $(this.priceSelector);
        $price.text(this.FormatPrice(data.PRICE));
    };

    window.JSFooterOrderInfo.prototype.FormatPrice = function(price) {
        return (+price).toFixed(0).split("").reverse().reduce(function(str, letter) {
            return str.length % 4 == 0 ? str + " " + letter : str + letter;
        }, "").substr(1).split("").reverse().join("") + " р.";
    };

})(window, jQuery);