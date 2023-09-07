(function(window) {
    if (!!window.JSFeedbackComplaint) return;

    window.JSFeedbackComplaint = function(arParams) {
        this.params = null;
        this.formId = null;
        this.nameInputId = null;
        this.phoneInputId = null;
        this.orderInputId = null;
        this.fileInputId = null;
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
            this.orderInputId = arParams.order_input_id;
            this.textInputId = arParams.text_input_id;
            this.fileInputId = arParams.file_input_id;
            this.submitNameSpanId = arParams.submit_name_span_id;
            this.timeSpanId = arParams.time_span_id;
            this.privateInputId = arParams.private_input_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSFeedbackComplaint.prototype.Init = function() {
        this.GetForm().validate({
            rules: {
                inputNum: {
                    required: true,
                    digits: true
                }
            },
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });

        this.GetFileInput().on("change", $.proxy(this.OnFileChanged, this));
    };

    window.JSFeedbackComplaint.prototype.OnSubmitHandler = function(form) {
        this.SendAjax();
        return false;
    };

    window.JSFeedbackComplaint.prototype.GetAjaxData = function() {
        var fd = new FormData;

        fd.append("sessid", BX.bitrix_sessid());

        fd.append("params", this.params);
        fd.append("name", this.GetNameInput().val());
        fd.append("phone", this.GetPhoneInput().val());
        fd.append("order_id", this.GetPhoneInput().val());
        fd.append("text", this.GetTextInput().val());
        fd.append("file", this.GetFileInput().prop('files')[0]);
        fd.append("message", this.GetPrivateInput().val());

        return fd;
    };

    window.JSFeedbackComplaint.prototype.SendAjax = function() {
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

    window.JSFeedbackComplaint.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
            this.SetCountdown(3);
        }
    };

    window.JSFeedbackComplaint.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        
    };


    window.JSFeedbackComplaint.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");

        this.SetName();
    };

    window.JSFeedbackComplaint.prototype.SetName = function() {
        var name = this.GetNameInput().val();
        this.GetSubmitNameSpan().text(name);
    };

    window.JSFeedbackComplaint.prototype.SetCountdown = function(time) {
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

    window.JSFeedbackComplaint.prototype.CloseWindow = function() {
        $( ".popup69" ).animate({"right":"-10000px"}, "slow");
        setTimeout(function () {
            $("body").removeClass("overlay69");
            $("html").removeClass("overlay");
            $(".popup69").hide();
        }, 250);
        $( ".popup69" ).animate({"right":"0"}, "slow");
        $(".okSendLine").slideDown();
    };


    window.JSFeedbackComplaint.prototype.OnFileChanged = function() {
        var fileInput = this.GetFileInput();

        var filename = fileInput.val().replace(/.*\\/, "");
        fileInput.parent().parent().next().val(filename);
        fileInput.parent().removeClass("inputFileFree");
    };



    window.JSFeedbackComplaint.prototype.GetForm = function() {
        return $("#"+this.formId);
    };

    window.JSFeedbackComplaint.prototype.GetNameInput = function() {
        return $("#"+this.nameInputId);
    };

    window.JSFeedbackComplaint.prototype.GetPhoneInput = function() {
        return $("#"+this.phoneInputId);
    };

    window.JSFeedbackComplaint.prototype.GetOrderInput = function() {
        return $("#"+this.orderInputId);
    };

    window.JSFeedbackComplaint.prototype.GetTextInput = function() {
        return $("#"+this.textInputId);
    };

    window.JSFeedbackComplaint.prototype.GetFileInput = function() {
        return $("#"+this.fileInputId);
    };

    window.JSFeedbackComplaint.prototype.GetSubmitNameSpan = function() {
        return $("#"+this.submitNameSpanId);
    };

    window.JSFeedbackComplaint.prototype.GetTimeSpan = function() {
        return $("#"+this.timeSpanId);
    };

    window.JSFeedbackComplaint.prototype.GetPrivateInput = function() {
        return $("#"+this.privateInputId);
    };

})(window);