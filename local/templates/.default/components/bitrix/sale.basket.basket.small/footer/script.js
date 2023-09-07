(function(window, $) {
    if (!!window.JSSaleBasketBasketSmallFooter) return;

    window.JSSaleBasketBasketSmallFooter = function(params) {
        if (typeof params === "object") {
            this.tooltipContainerSelector = params.tooltipContainerSelector;
            this.toolbarSelector = params.toolbarSelector;

            this.params = params.params;
            this.ajaxUrl = params.ajaxUrl;

            this.basketInfo = params.basketInfo;
        }

        $(window.document).on("basketitemquantitychange", $.proxy(this.OnBasketItemQuantityChanged, this));
        $(window.document).on("basketitemadd", $.proxy(this.OnBasketItemAdded, this));
        $(window.document).on("basketitemdelete", $.proxy(this.OnBasketItemDeleted, this));

        $($.proxy(this.Init, this));
    };

    window.JSSaleBasketBasketSmallFooter.prototype.Init = function() {
        $(window.document).on("mouseup", $.proxy(this.OnDocumentMouseUp, this));

        $(this.tooltipContainerSelector).on("click", ".slider-info__counter-plus", $.proxy(this.OnQuantityPlusClicked, this));
        $(this.tooltipContainerSelector).on("click", ".slider-info__counter-minus", $.proxy(this.OnQuantityMinusClicked, this));
        $(this.tooltipContainerSelector).on("click", ".slider-info__content-button", $.proxy(this.OnDeleteClicked, this));

        this.InitTemplate();

        $(window.document).trigger("basketinit", [this.basketInfo]);
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnDocumentMouseUp = function(event) {
        var tooltips = $(this.tooltipContainerSelector).find(".toolbar-bottom__slider-info");
        if (tooltips.has(event.target).length === 0) {
            this.HideTooltips();
        }
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnQuantityPlusClicked = function(event) {
        var plusBtn = $(event.target).closest(".slider-info__counter-plus");
        var sliderInfoBlock = plusBtn.parents(".slider-info");
        var quantitySpan = sliderInfoBlock.find(".slider-info__counter-counter");

        var basketId = sliderInfoBlock.data("id");
        var quantity = +quantitySpan.text();

        plusBtn.attr("disabled", "disabled");
        window.BasketManager.ChangeQuantity(
            {
                basketId: basketId,
                quantity: quantity + 1,
            }, 
            $.proxy(function(res) {
                plusBtn.removeAttr("disabled");
            }, this)
        );
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnQuantityMinusClicked = function(event) {
        var minusBtn = $(event.target).closest(".slider-info__counter-minus");
        var sliderInfoBlock = minusBtn.parents(".slider-info");
        var quantitySpan = sliderInfoBlock.find(".slider-info__counter-counter");

        var basketId = sliderInfoBlock.data("id");
        var quantity = +quantitySpan.text();

        if (quantity > 1) {
            minusBtn.attr("disabled", "disabled");
            window.BasketManager.ChangeQuantity(
                {
                    basketId: basketId,
                    quantity: quantity - 1,
                }, 
                $.proxy(function(res) {
                    minusBtn.removeAttr("disabled");
                }, this)
            );
        } else {
            minusBtn.attr("disabled", "disabled");
            window.BasketManager.DeleteFromBasket(
                {
                    basketId: basketId
                },
                $.proxy(function(res) {
                    minusBtn.removeAttr("disabled");
                })    
            );
        }
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnDeleteClicked = function(event) {
        var deleteBtn = $(event.target).closest(".slider-info__content-button");
        var sliderInfoBlock = deleteBtn.parents(".slider-info");

        var basketId = sliderInfoBlock.data("id");

        deleteBtn.attr("disabled", "disabled");
        window.BasketManager.DeleteFromBasket(
            {
                basketId: basketId
            },
            $.proxy(function(res) {
                deleteBtn.removeAttr("disabled");
            })    
        );
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnBasketItemQuantityChanged = function(event, data) {
        var basketId = data.BASKET_ID;
        var quantity = data.QUANTITY;

        var toolbarCount = $(this.toolbarSelector).find("[data-id="+basketId+"] .tag");
        toolbarCount.text(quantity);

        var tooltipCount = $(this.tooltipContainerSelector).find("[data-id="+basketId+"] .slider-info__counter-counter");
        tooltipCount.text(quantity);

        var tooltipSum = $(this.tooltipContainerSelector).find("[data-id="+basketId+"] ._sum_price");
        var sum = this.FormatPrice(data.PRODUCT.DISCOUNT_PRICE * data.QUANTITY);
        tooltipSum.text(sum);
    };


    window.JSSaleBasketBasketSmallFooter.prototype.OnBasketItemAdded = function(event, data) {
        this.ReloadBasket();
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnBasketItemDeleted = function(event, data) {
        this.ReloadBasket();
    };

    window.JSSaleBasketBasketSmallFooter.prototype.ReloadBasket = function() {
        this.SendReloadAjax();
    };

    window.JSSaleBasketBasketSmallFooter.prototype.SendReloadAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            data: {
                params: this.params,
                sessid: BX.bitrix_sessid()
            },
            dataType: "json",
            method: "POST",
            success: $.proxy(this.OnReloadAjaxSuccess, this),
            error: $.proxy(this.OnReloadAjaxError, this)
        });
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnReloadAjaxSuccess = function(data, textStatus, jqXHR) {
        var tooltipContent, toolbarContent;
        try {
            tooltipContent = JSON.parse(atob(data.tooltip_content));
            toolbarContent = JSON.parse(atob(data.toolbar_content));
        } catch (err) {
            //console.log(err);
            return;
        }

        var $toolbar = $(this.toolbarSelector);
        $toolbar.slick('unslick');
        $toolbar.children().remove();
        $toolbar.append(toolbarContent);

        var $tooltipContainer = $(this.tooltipContainerSelector);
        $tooltipContainer.find("._toolbarItem").remove();
        $tooltipContainer.prepend(tooltipContent);

        this.InitTemplate();
    };

    window.JSSaleBasketBasketSmallFooter.prototype.OnReloadAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
    };



    window.JSSaleBasketBasketSmallFooter.prototype.InitTemplate = function() {
        $(this.toolbarSelector).slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow: '<div class="toolbar-bottom__slider-prev-arrow"></div>',
            nextArrow: '<div class="toolbar-bottom__slider-next-arrow"></div>',
        });

        $(this.toolbarSelector).find(".toolbar-bottom__slider-slide").on("mouseenter", $.proxy(function(event) {
            var toolbar = $(this.toolbarSelector);
            var toolbarItem = $(event.target).closest(".toolbar-bottom__slider-slide");
            var element = $(event.target).closest(".toolbar-bottom__slider-slide");
            var productId = element.data("id");
            var tooltips = $(this.tooltipContainerSelector).find(".toolbar-bottom__slider-info");

            tooltips.each($.proxy(function(index, item) {
                var tooltip = $(item);

                var tooltipProductId = tooltip.data("id");
                if (tooltipProductId == productId) {
                    tooltip.css({
                        "opacity": "1",
                        "height": "137px",
                        "overflow": "visible"
                    });
                    var tooltipCorner = tooltip.find(".slider-info__corner");
                    tooltipCorner.css({
                        "left": 203 - 8 + (toolbarItem.width() / 2) - (toolbar.offset().left - toolbarItem.offset().left)
                    });
                } else {
                    tooltip.css({
                        "opacity": "0",
                        "height": "0",
                        "overflow": "hidden"
                    });
                }
            }, this));
        }, this));
    };

    window.JSSaleBasketBasketSmallFooter.prototype.HideTooltips = function() {
        var tooltips = $(this.tooltipContainerSelector).find(".toolbar-bottom__slider-info");
        tooltips.css({
            "opacity": "0",
            "height": "0",
            "overflow": "hidden"
        });
    };

    window.JSSaleBasketBasketSmallFooter.prototype.FormatPrice = function(price) {
        return (+price).toFixed(0).split("").reverse().reduce(function(str, letter) {
            return str.length % 4 == 0 ? str + " " + letter : str + letter;
        }, "").substr(1).split("").reverse().join("") + " р.";
    };

})(window, jQuery);