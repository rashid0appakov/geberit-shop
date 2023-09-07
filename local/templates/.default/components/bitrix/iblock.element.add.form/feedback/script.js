;
var Form    = Form || {};

Form.Common = {
    Config : {
        _show   : null,
        send    : true
    },

    onAjaxSuccess : function() {
        //BX.addCustomEvent("onAjaxSuccess", scripts.errors);

        BX.addCustomEvent('onAjaxSuccess', function(){
            $("div[id^='wait_comp_']").remove();
            $(".fancybox-inner").height("auto");
            $('input.phone__field').mask('+7(999)999-99-99');
        });
    },

    init: function () {
        var b = this;

        $(function () {
            b.onAjaxSuccess();
            //scripts.errors();
            $("div[id^='wait_comp_']").remove();
            $('input.phone__field').mask('+7(999)999-99-99');
        });

        return b;
    }
};
Form = Form.Common.init();