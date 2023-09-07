(function(window, $) {
    if (!!window.JSCatalogItemProduct) return;

    window.JSCatalogItemProduct = function(arParams) {

        if (typeof arParams === 'object') {
            this.basketBtnSelector = arParams.basketBtnSelector;

            this.productInfo = arParams.productInfo;

            this.uniqueId = arParams.uniqueId;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogItemProduct.prototype.Init = function() {
        $(this.basketBtnSelector).on("click", $.proxy(this.OnAdd2BasketClicked, this));
        
        var dot = $('#slaider_' + this.uniqueId + ' .productInSlaiderCallConsultantSpeakerDot');
        var dotClass = $('#dotResult_' + this.uniqueId).html();
        if (!dot.hasClass(dotClass)) {
            dot.addClass(dotClass);
        }
    };

    window.JSCatalogItemProduct.prototype.OnAdd2BasketClicked = function() {
        $(this.basketBtnSelector).attr("disabled", "disabled");

        if (!!this.productInfo.available) {
            window.BasketManager.Add2Basket(
                {
                    productId: this.productInfo.id,
                    quantity: 1,
                }, 
                $.proxy(
                    function(res) {
                        $(this.basketBtnSelector).removeAttr("disabled");
                    }, 
                    this
                )
            );
        } else {
            window.BasketManager.ReportAppearance({
                PRODUCT_ID: this.productInfo.id
            });
            $(this.basketBtnSelector).removeAttr("disabled");
        }

    };

})(window, window.jQuery);