(function(window) {
    if (!!window.JSSearchError) return;

    window.JSSearchError = function(arParams) {
        this.params = null;
        this.formId = null;
        this.textInputId = null;
		this.selectInputId = null;
		this.pageInputId = null;
        this.timeSpanId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.formId = arParams.form_id;
            this.textInputId = arParams.text_input_id;
            this.selectInputId = arParams.select_input_id;
            this.pageInputId = arParams.page_input_id;
            this.timeSpanId = arParams.time_span_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSSearchError.prototype.Init = function() {
        this.GetForm().validate({
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });
    };

    window.JSSearchError.prototype.OnSubmitHandler = function(form) {
		
		if(this.GetSelect().val() == ''){
			console.log($("#"+this.selectInputId));
			$("#"+this.selectInputId).parent().css('border', '1px solid red');
		}
		else{
			this.SendAjax();
		}
       
        return false;
    };

    window.JSSearchError.prototype.GetAjaxData = function() {
        var fd = new FormData;

        fd.append("sessid", BX.bitrix_sessid());

        fd.append("params", this.params);
        fd.append("text", this.GetTextInput().val());
		fd.append("select", this.GetSelect().val());
		fd.append("page", this.GetPage().val());

        return fd;
    };

    window.JSSearchError.prototype.SendAjax = function() {
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

    window.JSSearchError.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
        }
    };

    window.JSSearchError.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
        
    };

    window.JSSearchError.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");
    };

    window.JSSearchError.prototype.GetForm = function() {
        return $("#"+this.formId);
    };

    window.JSSearchError.prototype.GetTextInput = function() {
        return $("#"+this.textInputId);
    };
	
    window.JSSearchError.prototype.GetSelect = function() {
        return $("#"+this.selectInputId);
    };
	
    window.JSSearchError.prototype.GetPage = function() {
        return $("#"+this.pageInputId);
    };

    window.JSSearchError.prototype.GetTimeSpan = function() {
        return $("#"+this.timeSpanId);
    };

})(window);