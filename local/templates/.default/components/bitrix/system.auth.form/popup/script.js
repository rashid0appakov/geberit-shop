(function(window) {
    if (!!window.JSAuthFormPopup) return;

    window.JSAuthFormPopup = function(arParams) {
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

    window.JSAuthFormPopup.prototype.Init = function() {
        var form = this.GetForm();
        form.on("submit", $.proxy(this.OnFormSubmit, this));
    };

    window.JSAuthFormPopup.prototype.GetForm = function() {
        if (!this._form) {
            this._form = $("#"+this.formId);
        }

        return this._form;
    }

    window.JSAuthFormPopup.prototype.OnFormSubmit = function() {
        this.SendAjax();
        return false;
    };

    window.JSAuthFormPopup.prototype.SendAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: this.GetAjaxData(),
            success: function (data){
				if( data.error == false ) {
					window.location.reload();
					$('.user-error').hide();
				}  else {
					$('.user-error').show();
				}

				$.proxy(this.OnAjaxSuccess, this)

			},
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSAuthFormPopup.prototype.GetAjaxData = function() {
        var data = {};

        data["sessid"] = BX.bitrix_sessid();

        var form = this.GetForm();
        data["login"] = $("input[name=USER_LOGIN]", form).val();
        data["password"] = $("input[name=USER_PASSWORD]", form).val();
        data["remember"] = $("input[name=USER_REMEMBER]", form).val();

        return data;
    };

    window.JSAuthFormPopup.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!!data.error) {
            this.ShowError(data.message);
        } else {
            window.location.reload(false);
        }
    };

    window.JSAuthFormPopup.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        this.ShowError("Ошибка запроса");
    };

    window.JSAuthFormPopup.prototype.GetErrorContainer = function() {
        if (!this._errorContainer) {
            this._errorContainer = $("#"+this.errorContainerId);
        }

        return this._errorContainer;
    };

    window.JSAuthFormPopup.prototype.ShowError = function(message) {
        this.GetErrorContainer().text(message);
    };

    window.JSAuthFormPopup.prototype.HideError = function() {
        this.GetErrorContainer().text("");
    };
})(window);

$(document).ready(function(){
    $('p.regLink a').click(function(e){
        e.preventDefault();

        $('a.regLink.modal-link').trigger('click');
    });
});