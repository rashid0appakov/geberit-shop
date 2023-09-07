(function(window, $) {
    if (!!window.JSBasketPopup) return;

    window.JSBasketPopup = function(params) {
        this.autoOpen = false;

        if (typeof params === 'object') {
            this.ajaxUrl = params.ajaxUrl;
            this.autoOpen = params.autoOpen;
            this.popupSelector = params.popupSelector;
            this.containerSelector = params.containerSelector;
        }
    };

    window.JSBasketPopup.prototype.OpenModal = function(data) {
        if (!!this.autoOpen) {
            this.SendAjax(data.BASKET_ID);
        }
    };

    window.JSBasketPopup.prototype.SendAjax = function(basketItemId) {
        $.ajax({
            url: this.ajaxUrl,
            data: {
                sessid: BX.bitrix_sessid(),
                basketItemId: basketItemId,
            },
            dataType: "json",
            method: "GET",
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSBasketPopup.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            var html;
            try {
                html = JSON.parse(atob(data.html));
            } catch (err) {
                console.err(err);
                html = "";
            }

            this.ShowPopup(html);
        } else {
            console.error(data.error);
        }
    };

    window.JSBasketPopup.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
    };

    window.JSBasketPopup.prototype.ShowPopup = function(html) {
        $(this.containerSelector).html(html);

        $("body").addClass("overlay69");
        $("html").addClass("overlay");
        $(this.popupSelector).show();
    };

})(window, window.jQuery);