(function(window, $) {
    if (!!window.JSReportAppearancePopup) return;

    window.JSReportAppearancePopup = function(params) {
        if (typeof params === "object") {
            this.ajaxUrl = params.ajaxUrl;
            this.uniqueId = params.uniqueId;
        }

        $($.proxy(this.Init, this));
    };

    window.JSReportAppearancePopup.prototype.Init = function() {
        $("#form_"+this.uniqueId).on("submit", $.proxy(this.OnFormSubmit, this));
    };

    window.JSReportAppearancePopup.prototype.OpenModal = function(data) {
        var productId = data.PRODUCT_ID;
        this.SendGetInfoAjax(productId);
    };

    window.JSReportAppearancePopup.prototype.SendGetInfoAjax = function(productId) {
        $.ajax({
            url: this.ajaxUrl,
            data: {
                sessid: BX.bitrix_sessid(),
                action: "getinfo",
                productId: productId,
            },
            dataType: "json",
            method: "GET",
            success: $.proxy(this.OnGetInfoAjaxSuccess, this),
            error: $.proxy(this.OnGetInfoAjaxError, this)
        });
    };

    window.JSReportAppearancePopup.prototype.OnGetInfoAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ShowPopup(data);
        } else {
            console.error(data.error);
        }
    };

    window.JSReportAppearancePopup.prototype.OnGetInfoAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
    };

    window.JSReportAppearancePopup.prototype.ShowPopup = function(data) {
        $("#product_id_"+this.uniqueId).val(data.ID);
        $("#product_name_"+this.uniqueId).text(data.NAME);
        $("#product_image_"+this.uniqueId).attr("src", data.IMAGE);

        $("body").addClass("overlay69");
        $("html").addClass("overlay");
        $("#popup_"+this.uniqueId).show();
    };

    window.JSReportAppearancePopup.prototype.ClosePopup = function(data) {
        $("body").removeClass("overlay69");
        $("html").removeClass("overlay");
        $("#popup_"+this.uniqueId).hide();
    };

    window.JSReportAppearancePopup.prototype.OnFormSubmit = function(event) {
        var productId = $("#product_id_"+this.uniqueId).val();
        var name = $("#name_"+this.uniqueId).val();
        var email = $("#email_"+this.uniqueId).val();
        var phone = $("#phone_"+this.uniqueId).val();

        this.SendSubmitAjax({
            productId: productId,
            name: name,
            email: email,
            phone: phone
        });

        return false;
    };

    window.JSReportAppearancePopup.prototype.SendSubmitAjax = function(data) {
        $.ajax({
            url: this.ajaxUrl,
            data: {
                sessid: BX.bitrix_sessid(),
                action: "submit",
                productId: data.productId,
                name: data.name,
                email: data.email,
                phone: data.phone
            },
            dataType: "json",
            method: "POST",
            success: $.proxy(this.OnSubmitAjaxSuccess, this),
            error: $.proxy(this.OnSubmitAjaxError, this)
        });
    };

    window.JSReportAppearancePopup.prototype.OnSubmitAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ClosePopup();
            alert("Добавлено");
        } else {
            console.error(data.error);
        }
    };

    window.JSReportAppearancePopup.prototype.OnSubmitAjaxError = function(jqXHR, textStatus, errorThrown) {
        console.error(jqXHR, textStatus, errorThrown);
    };

})(window, window.jQuery);