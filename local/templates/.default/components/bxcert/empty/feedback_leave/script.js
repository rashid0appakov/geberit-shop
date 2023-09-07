(function(window) {
    if (!!window.JSFeedbackLeave) return;

    window.JSFeedbackLeave = function(arParams) {
        this.params = null;
        this.formId = null;
        this.orderInputId = null;
        this.phoneInputId = null;
        this.statusOrderInputId = null;
        this.sumOrderInputId = null;
        this.paymentOrderInputId = null;
        this.nameUserInputId = null;
        this.phoneUserInputId = null;
        this.ajaxUrl = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.formId = arParams.form_id;
            this.orderInputId = arParams.order_input_id;
            this.phoneInputId = arParams.phone_input_id;
            this.statusOrderInputId = arParams.status_input_id;
            this.sumOrderInputId = arParams.sum_order_input_id;
            this.paymentOrderInputId = arParams.payment_order_input_id;
            this.nameUserInputId = arParams.name_user_input_id;
            this.phoneUserInputId = arParams.phone_user_input_id;
            this.ajaxUrl = arParams.ajax_url;
        }

        $($.proxy(this.Init, this));
    };

    window.JSFeedbackLeave.prototype.Init = function() {
        var form = this.GetForm();

        form.validate({
            submitHandler: $.proxy(this.OnSubmitHandler, this)
        });

    };

    window.JSFeedbackLeave.prototype.OnSubmitHandler = function(form) {
        //debugger;
        this.SendAjax();
        return false;
    };

    window.JSFeedbackLeave.prototype.GetAjaxData = function() {
        var data = {};

        data["sessid"] = BX.bitrix_sessid();
        data["params"] = this.params;
        data["order"] = this.GetOrderInput().val();
        data["phone"] = this.GetPhoneInput().val();
        
        return data;
    };

    window.JSFeedbackLeave.prototype.SendAjax = function() {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: this.GetAjaxData(),
            success: $.proxy(this.OnAjaxSuccess, this),
            error: $.proxy(this.OnAjaxError, this)
        });
    };

    window.JSFeedbackLeave.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.ChangeView();
        }
        order = data.orderNum;
        status = data.statusOrder;
        sumOrder = data.sumOrder;
        paymentOrder = data.payment;
        nameUser = data.nameUser;
        phoneUser = data.phoneUser;
        paymentOrder = '';
        if(paymentOrder == 1){
            paymentOrder = 'Наличными';
        }
        if(paymentOrder == 9){
            paymentOrder = 'Карта';
        }
        if(paymentOrder == 6){
            paymentOrder = 'Сбербанк';
        }
		status = data.message;
        if(status == 'P'){
            status = 'Отправка';
        }
        if(status == 'N'){
            status = 'Принят';
        }
        if(status == 'F'){
            status = 'Выполнен';
        }
        this.SetInfo();
        this.ChangeView();
    };

    window.JSFeedbackLeave.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
    };


    window.JSFeedbackLeave.prototype.ChangeView = function() {
        var form = this.GetForm();
        form.find(".showOnSubmit").removeClass("hide");
        $(".myClassShow").removeClass("hide");
        form.find(".hideOnSubmit").addClass("hide");
        //debugger;

        this.SetInfo();
    };

    window.JSFeedbackLeave.prototype.SetInfo = function() {
        this.OrderIdNum().text(order);
        this.GetStatus().text(status);
        this.GetSumOrder().text(sumOrder);
        this.GetPaymentOrder().text(paymentOrder);
        this.GetNameUser().text(nameUser);
        this.GetPhoneUser().text(phoneUser);
        //debugger;
    };

    window.JSFeedbackLeave.prototype.CloseWindow = function() {
        $( ".popupOrder" ).animate({"right":"-10000px"}, "slow");
        setTimeout(function () {
            $("body").removeClass("overlay69");
            $("html").removeClass("overlay");
            $(".popupOrder").hide();
        }, 250);
        $( ".popupOrder" ).animate({"right":"0"}, "slow");
        $(".okSendLine").slideDown();
    };

    window.JSFeedbackLeave.prototype.GetForm = function() {
        return $("#"+this.formId);
    };
    window.JSFeedbackLeave.prototype.OrderIdNum = function() {
        return $("#orderIdNum");
    };

    window.JSFeedbackLeave.prototype.GetOrderInput = function() {
        return $("#"+this.orderInputId);
    };

    window.JSFeedbackLeave.prototype.GetPhoneInput = function() {
        return $("#"+this.phoneInputId);
    };

    window.JSFeedbackLeave.prototype.GetStatus = function() {
        return $("#"+this.statusOrderInputId);
    };

    window.JSFeedbackLeave.prototype.GetSumOrder = function() {
        return $("#"+this.sumOrderInputId);
    };
    window.JSFeedbackLeave.prototype.GetPaymentOrder = function() {
        return $("#"+this.paymentOrderInputId);
    };
    window.JSFeedbackLeave.prototype.GetNameUser = function() {
        return $("#"+this.nameUserInputId);
    };
    window.JSFeedbackLeave.prototype.GetPhoneUser = function() {
        return $("#"+this.phoneUserInputId);
    };


})(window);