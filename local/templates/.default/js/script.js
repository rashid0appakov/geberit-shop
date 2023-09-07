(function(window, $) {
    if (!!window.JSBasketManager) return;

    window.JSBasketManager = function(params) {
        if (typeof params === 'object') {
            this.add2BasketUrl = params.add2BasketUrl;
            this.changeQuantityUrl = params.changeQuantityUrl;
            this.deleteFromBasketUrl = params.deleteFromBasketUrl;
        }
    };


    // add2Basket
    window.JSBasketManager.prototype.Add2Basket = function(data, cb) {
        this.SendAdd2BasketAjax({
            productId: data.productId,
            quantity: data.quantity,
            sessid: BX.bitrix_sessid(),
        }, cb);
    };

    window.JSBasketManager.prototype.SendAdd2BasketAjax = function(data, cb) {
        $.ajax({
            url: this.add2BasketUrl,
            data: data,
            dataType: "json",
            method: "POST",
            success: $.proxy(
                function(data, textStatus, jqXHR) { 
                    return this.OnSendAdd2BasketAjaxSuccess(data, textStatus, jqXHR, cb); 
                }, 
                this
            ),
            error: $.proxy(
                function(jqXHR, textStatus, errorThrown) { 
                    return this.OnSendAdd2BasketAjaxError(jqXHR, textStatus, errorThrown, cb); 
                }, 
                this
            ),
        });
    };

    window.JSBasketManager.prototype.OnSendAdd2BasketAjaxSuccess = function(data, textStatus, jqXHR, cb) {
		
        if (!data.error) {
			
			UpdateBasketBars();
			
            /*if (typeof cb === "function") cb(data);
            $(window.document).trigger("basketitemadd", [data]);*/

            if (!!window.BasketPopup) {
                window.BasketPopup.OpenModal(data);
            } else {
                console.error("BasketPopup is not defined");
            }
        } else {
            console.error(data.error);
            if (typeof cb === "function") cb(false);
        }
    };

    window.JSBasketManager.prototype.OnSendAdd2BasketAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
        if (typeof cb === "function") cb(false);
    };


    // changeQuantity
    window.JSBasketManager.prototype.ChangeQuantity = function(data, cb) {
        /*this.SendChangeQuantityAjax({
            basketId: data.basketId,
            quantity: data.quantity,
            sessid: BX.bitrix_sessid(),
        }, cb);*/
    };

    window.JSBasketManager.prototype.SendChangeQuantityAjax = function(data, cb) {
        $.ajax({
            url: this.changeQuantityUrl,
            data: data,
            dataType: "json",
            method: "POST",
            success: $.proxy(
                function(data, textStatus, jqXHR) { 
                    return this.OnChangeQuantityAjaxSuccess(data, textStatus, jqXHR, cb); 
                }, 
                this
            ),
            error: $.proxy(
                function(jqXHR, textStatus, errorThrown) { 
                    return this.OnChangeQuantityAjaxError(jqXHR, textStatus, errorThrown, cb); 
                }, 
                this
            ),
        });
    };

    window.JSBasketManager.prototype.OnChangeQuantityAjaxSuccess = function(data, textStatus, jqXHR, cb) {
        if (!data.error) {
            if (typeof cb === "function") cb(data);
            $(window.document).trigger("basketitemquantitychange", [data]);
        } else {
            console.error(data.error);
            if (typeof cb === "function") cb(false);
        }
    };

    window.JSBasketManager.prototype.OnChangeQuantityAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
        if (typeof cb === "function") cb(false);
    };


    // deleteFromBasket
    window.JSBasketManager.prototype.DeleteFromBasket = function(data, cb) {
        this.SendDeleteFromBasketAjax({
            basketId: data.basketId,
            sessid: BX.bitrix_sessid(),
        }, cb);
    };

    window.JSBasketManager.prototype.SendDeleteFromBasketAjax = function(data, cb) {
        $.ajax({
            url: this.deleteFromBasketUrl,
            data: data,
            dataType: "json",
            method: "POST",
            success: $.proxy(
                function(data, textStatus, jqXHR) { 
                    return this.OnDeleteFromBasketAjaxSuccess(data, textStatus, jqXHR, cb); 
                }, 
                this
            ),
            error: $.proxy(
                function(jqXHR, textStatus, errorThrown) { 
                    return this.OnDeleteFromBasketAjaxError(jqXHR, textStatus, errorThrown, cb); 
                }, 
                this
            ),
        });
    };

    window.JSBasketManager.prototype.OnDeleteFromBasketAjaxSuccess = function(data, textStatus, jqXHR, cb) {
        if (!data.error) {
            if (typeof cb === "function") cb(data);
            $(window.document).trigger("basketitemdelete", [data]);
        } else {
            console.error(data.error);
            if (typeof cb === "function") cb(false);
        }
    };

    window.JSBasketManager.prototype.OnDeleteFromBasketAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
        if (typeof cb === "function") cb(false);
    };

    // reportAppearance
    window.JSBasketManager.prototype.ReportAppearance = function(data) {
        if (!!window.ReportAppearancePopup) {
            window.ReportAppearancePopup.OpenModal(data);
        } else {
            console.error("ReportAppearancePopup is not defined");
        }
    };

})(window, window.jQuery);