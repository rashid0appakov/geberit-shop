(function(window) {
    if (!!window.JSRegFormPopup) return;

    window.JSRegFormPopup = function(arParams) {
        this.formId = null;
        this.errorContainerId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.formId = arParams.form_id;
            this.errorContainerId = arParams.error_container_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSRegFormPopup.prototype.Init = function() {
        var form = this.GetForm();
        form.on("submit", $.proxy(this.OnFormSubmit, this));

        this.MaskPhone();
    };

    window.JSRegFormPopup.prototype.GetForm = function() {
        if (!this._form) {
            this._form = $("#"+this.formId);
        }

        return this._form;
    }

    window.JSRegFormPopup.prototype.OnFormSubmit = function() {
        var data = this.GetData();
        var errorMsg = this.ValidateData(data);
        if (!!errorMsg) {
            this.ShowError(errorMsg);
        } else {
            this.SendAjax(data);
        }

        return false;
    };

    window.JSRegFormPopup.prototype.ValidateData = function(data) {
        var errors = [];

        if (data["email"].length <= 0) errors.push("Не указан email");
        if (data["name"].length <= 0) errors.push("Не указано имя");
        if (data["last_name"].length <= 0) errors.push("Не указана фамилия");
        if (data["phone"].length <= 0) errors.push("Не указан телефон");
        if (data["password"].length <= 0) errors.push("Не указан пароль");
        if (data["confirm"].length <= 0) errors.push("Не указано подтверждение пароль");
        if (data["password"] != data["confirm"]) errors.push("Пароли не совпадают");
        
        //return errors.length > 0 ? errors.join("<br>") : false;
    };

    window.JSRegFormPopup.prototype.SendAjax = function(data) {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: data,
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSRegFormPopup.prototype.GetData = function() {
        var data = {};

        data["sessid"] = BX.bitrix_sessid();

        var form = this.GetForm();
        data["email"] = $("input[name=\"REGISTER[EMAIL]\"]", form).val();
        data["name"] = $("input[name=\"REGISTER[NAME]\"]", form).val();
        data["last_name"] = $("input[name=\"REGISTER[LAST_NAME]\"]", form).val();
        data["phone"] = $("input[name=\"REGISTER[PERSONAL_PHONE]\"]", form).val();
        data["password"] = $("input[name=\"REGISTER[PASSWORD]\"]", form).val();
        data["confirm"] = $("input[name=\"REGISTER[CONFIRM_PASSWORD]\"]", form).val();
        
        return data;
    };

    window.JSRegFormPopup.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!!data.error) {
            this.ShowError(data.message);
        } else {
            window.location.reload(false);
        }
    };

    window.JSRegFormPopup.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        this.ShowError("Ошибка запроса");
    };

    window.JSRegFormPopup.prototype.GetErrorContainer = function() {
        if (!this._errorContainer) {
            this._errorContainer = $("#"+this.errorContainerId);
        }

        return this._errorContainer;
    };

    window.JSRegFormPopup.prototype.ShowError = function(message) {
        this.GetErrorContainer().text(message);
    };

    window.JSRegFormPopup.prototype.HideError = function() {
        this.GetErrorContainer().text("");
    };

    window.JSRegFormPopup.prototype.MaskPhone = function() {
        var form = this.GetForm();
        $("input[name=\"REGISTER[PERSONAL_PHONE]\"]", form).mask("+7 (999) 999-99-99");
    };
})(window);