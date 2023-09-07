(function(window) {
    if (!!window.JSFeedbackHelpPickup) return;

    window.JSFeedbackHelpPickup = function(arParams) {
        this.params = null;
        this.formId = null;
        this.nameInputId = null;
        this.phoneInputId = null;
        this.textInputId = null;
        this.submitNameSpanId = null;
        this.timeSpanId = null;
        this.privateInputId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.formId = arParams.form_id;
            this.nameInputId = arParams.name_input_id;
            this.phoneInputId = arParams.phone_input_id;
            this.textInputId = arParams.text_input_id;
            this.submitNameSpanId = arParams.submit_name_span_id;
            this.timeSpanId = arParams.time_span_id;
            this.privateInputId = arParams.private_input_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSFeedbackHelpPickup.prototype.Init = function() {
        var form = this.GetForm();

        form.validate({
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });
    };

    window.JSFeedbackHelpPickup.prototype.OnSubmitHandler = function(form) {
        this.SendAjax();
        return false;
    };

    window.JSFeedbackHelpPickup.prototype.GetAjaxData = function() {
        var data = {};

        data["sessid"] = BX.bitrix_sessid();

        data["params"] = this.params;
        data["name"] = this.GetNameInput().val();
        data["phone"] = this.GetPhoneInput().val();
        data["text"] = this.GetTextInput().val();
        data["message"] = this.GetPrivateInput().val();
        
        return data;
    };

    window.JSFeedbackHelpPickup.prototype.SendAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: this.GetAjaxData(),
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSFeedbackHelpPickup.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
            this.SetCountdown(3);
        }
    };

    window.JSFeedbackHelpPickup.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        
    };


    window.JSFeedbackHelpPickup.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");

        this.SetName();
    };

    window.JSFeedbackHelpPickup.prototype.SetName = function() {
        var name = this.GetNameInput().val();
        this.GetSubmitNameSpan().text(name);
    };

    window.JSFeedbackHelpPickup.prototype.SetCountdown = function(time) {
        this.GetTimeSpan().text(time);

        if (time > 0) {
            window.setTimeout(
                $.proxy(function() { this.SetCountdown(time - 1); }, this),
                1000
            );
        } else {
            this.CloseWindow();
        }
    };

    window.JSFeedbackHelpPickup.prototype.CloseWindow = function() {
        $( ".popup69" ).animate({"right":"-10000px"}, "slow");
        setTimeout(function () {
            $("body").removeClass("overlay69");
            $("html").removeClass("overlay");
            $(".popup69").hide();
        }, 250);
        $( ".popup69" ).animate({"right":"0"}, "slow");
        $(".okSendLine").slideDown();
    };



    window.JSFeedbackHelpPickup.prototype.GetForm = function() {
        return $("#"+this.formId);
    };

    window.JSFeedbackHelpPickup.prototype.GetNameInput = function() {
        return $("#"+this.nameInputId);
    };

    window.JSFeedbackHelpPickup.prototype.GetPhoneInput = function() {
        return $("#"+this.phoneInputId);
    };

    window.JSFeedbackHelpPickup.prototype.GetTextInput = function() {
        return $("#"+this.textInputId);
    };

    window.JSFeedbackHelpPickup.prototype.GetSubmitNameSpan = function() {
        return $("#"+this.submitNameSpanId);
    };

    window.JSFeedbackHelpPickup.prototype.GetTimeSpan = function() {
        return $("#"+this.timeSpanId);
    };

    window.JSFeedbackHelpPickup.prototype.GetPrivateInput = function() {
        return $("#"+this.privateInputId);
    };

})(window);