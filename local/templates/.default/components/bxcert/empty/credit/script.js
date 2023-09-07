(function(window) {
    if (!!window.JSCredit) return;

    window.JSCredit = function(arParams) {
        this.params = null;
        this.formId = null;
        this.mailInputId = null;
		this.phoneInputId = null;
        this.timeSpanId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.formId = arParams.form_id;
            this.mailInputId = arParams.mail_input_id;
            this.phoneInputId = arParams.phone_input_id;
            this.timeSpanId = arParams.time_span_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCredit.prototype.Init = function() {
        this.GetForm().validate({
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });
    };

    window.JSCredit.prototype.OnSubmitHandler = function(form) {
		this.SendAjax();
        //return false;
    };

    window.JSCredit.prototype.GetAjaxData = function() {
        var fd = new FormData;

        fd.append("sessid", BX.bitrix_sessid());

        fd.append("params", this.params);
        fd.append("mail", this.GetMail().val());
		fd.append("phone", this.GetPhone().val());

        return fd;
    };

    window.JSCredit.prototype.SendAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: this.GetAjaxData(),
            processData: false,
            contentType: false,
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSCredit.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
        }
    };

    window.JSCredit.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        
    };

    window.JSCredit.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");
    };

    window.JSCredit.prototype.GetForm = function() {
        return $("#"+this.formId);
    };

    window.JSCredit.prototype.GetMail = function() {
        return $("#"+this.mailInputId);
    };
	
    window.JSCredit.prototype.GetPhone = function() {
        return $("#"+this.phoneInputId);
    };

    window.JSCredit.prototype.GetTimeSpan = function() {
        return $("#"+this.timeSpanId);
    };

})(window);