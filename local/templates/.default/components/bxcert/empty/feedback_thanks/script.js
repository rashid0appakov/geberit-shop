(function(window) {
    if (!!window.JSFeedbackThanks) return;

    window.JSFeedbackThanks = function(arParams) {
        this.params = null;
        this.formId = null;
        this.nameInputId = null;
        this.orderInputId = null;
        this.textInputId = null;
        this.submitNameSpanId = null;
        this.timeSpanId = null;
        this.privateInputId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.formId = arParams.form_id;
            this.nameInputId = arParams.name_input_id;
            this.orderInputId = arParams.order_input_id;
            this.textInputId = arParams.text_input_id;
            this.submitNameSpanId = arParams.submit_name_span_id;
            this.timeSpanId = arParams.time_span_id;
            this.privateInputId = arParams.private_input_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSFeedbackThanks.prototype.Init = function() {
        var form = this.GetForm();

        form.validate({
            rules: {
                inputNum: {
                    required: true,
                    digits: true
                }
            },
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });
    };

    window.JSFeedbackThanks.prototype.OnSubmitHandler = function(form) {
        this.SendAjax();
        return false;
    };

    window.JSFeedbackThanks.prototype.GetAjaxData = function() {
        var data = {};

        data["sessid"] = BX.bitrix_sessid();

        data["params"] = this.params;
        data["name"] = this.GetNameInput().val();
        data["order_id"] = this.GetOrderInput().val();
        data["text"] = this.GetTextInput().val();
        data["message"] = this.GetPrivateInput().val();
        
        return data;
    };

    window.JSFeedbackThanks.prototype.SendAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: this.GetAjaxData(),
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSFeedbackThanks.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
            this.SetCountdown(3);
        }
    };

    window.JSFeedbackThanks.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        
    };


    window.JSFeedbackThanks.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");

        this.SetName();
    };

    window.JSFeedbackThanks.prototype.SetName = function() {
        var name = this.GetNameInput().val();
        this.GetSubmitNameSpan().text(name);
    };

    window.JSFeedbackThanks.prototype.SetCountdown = function(time) {
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

    window.JSFeedbackThanks.prototype.CloseWindow = function() {
        $( ".popup69" ).animate({"right":"-10000px"}, "slow");
        setTimeout(function () {
            $("body").removeClass("overlay69");
            $("html").removeClass("overlay");
            $(".popup69").hide();
        }, 250);
        $( ".popup69" ).animate({"right":"0"}, "slow");
        $(".okSendLine").slideDown();
    };



    window.JSFeedbackThanks.prototype.GetForm = function() {
        return $("#"+this.formId);
    };

    window.JSFeedbackThanks.prototype.GetNameInput = function() {
        return $("#"+this.nameInputId);
    };

    window.JSFeedbackThanks.prototype.GetOrderInput = function() {
        return $("#"+this.orderInputId);
    };

    window.JSFeedbackThanks.prototype.GetTextInput = function() {
        return $("#"+this.textInputId);
    };

    window.JSFeedbackThanks.prototype.GetSubmitNameSpan = function() {
        return $("#"+this.submitNameSpanId);
    };

    window.JSFeedbackThanks.prototype.GetTimeSpan = function() {
        return $("#"+this.timeSpanId);
    };

    window.JSFeedbackThanks.prototype.GetPrivateInput = function() {
        return $("#"+this.privateInputId);
    };

})(window);