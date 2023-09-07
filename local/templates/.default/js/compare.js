;
"use strict";

var CP  = CP || {},
    _arTmp = [];

CP.Main = {
    vars  : {
    },

    setEventsAddToCompareList : function(){
        if ($('.buy__item-diff, .icon-diff-big[data-id]').length)
            $('.buy__item-diff, .icon-diff-big[data-id]').click(function(e) {
                e.preventDefault();

                if ($(this).hasClass('compare-added'))
                    location.href = '/compare/';
                else
                    CP.addToCompareList($(this));
            });
    },

    setEventDeleteCompareItem   : function(){
        if ($('a.delete-compare-item').length)
            $('a.delete-compare-item').click(function(){
                BX.addCustomEvent(window, 'onCatalogDeleteCompare', BX.proxy(CP.removeCompareCookie($(this).data('id')), this));
            });
    },

    addToCompareList    : function(_this, type = false){
		
		if(type == 'viewed'){
			SC.vars.send = true;
		}
		
        if (_this == undefined)
            return false;

        if (!_this.data('id') || !SC.vars.send)
            return false;
		
        _this.addClass('added-wait');
        SC.ajaxSendPost(
            '/ajax/?action=ADD_TO_COMPARE_LIST&id=' + _this.data('id'),
            {
                method  : 'compare'
            },
            function(data){
                SC.vars.send = true;
                BX.closeWait('', SC.vars.wait);

                if (data.status == 'success'){
                    if ($('.added-wait').length){
                        $('.added-wait').addClass('compare-added');
                        if ($('.added-wait').find('.buy__item-diff-link').length)
                            $('.added-wait').removeClass('added-wait').addClass('compare-added')
                                .find('.buy__item-diff-link').text($('.compare-item-button').data('added'));
                        else
                            $('.added-wait').removeClass('added-wait')
                                .attr('data-tooltip', $('.compare-item-button').data('added'));

                        CP.addToCompareCookie(data.id);
                    }
                    CP.updateCompareLine();
                }
                //else
                    // console.log(data);
            },
            'json'
        );
    },

    updateCompareLine   : function(){
        SC.ajaxSendPost(
            '/ajax/compare_line2.php',
            {},
            function(data){
                SC.vars.send = true;
                BX.closeWait('', SC.vars.wait);

                $('.diff .tag').html(data.qty);
                if (data.qty)
                    $('.compare-item-button').addClass('compare-added');
                else
                    $('.compare-item-button').removeClass('compare-added');
            },
            'json'
        );
    },

    addToCompareCookie  : function(_id){
        if (_id == undefined || !_id)
            return false;

        _arTmp  = [];
        if ($.cookie('COMPARE_LIST'))
            _arTmp  = $.cookie('COMPARE_LIST').split(',');

        _arTmp[_arTmp.length] = _id;

        $.cookie('COMPARE_LIST', _arTmp.join(','), {
            path    : '/',
            expires : 3
        });
    },

    removeCompareCookie  : function(_id){
        if (_id == undefined || !_id){
            $.cookie('COMPARE_LIST', '', {
                path    : '/',
                expires : 0
            });
            return true;
        }

        _arTmp  = [];
        if ($.cookie('COMPARE_LIST'))
            _arTmp  = $.cookie('COMPARE_LIST').split(',');

        $.each(_arTmp, function(_index, _value){
            if (_value == _id)
               _arTmp.splice(_index, 1);
        });

        $.cookie('COMPARE_LIST', (_arTmp.length ? _arTmp.join(',') : ''), {
            path    : '/',
            expires : _arTmp.length ? 3 : 0
        });

        _timer = setTimeout('CP.updateCompareLine();', 1000);
    },

    checkCompareItems   : function(){
        $('.buy__item-diff, .icon-diff-big[data-id]').removeClass('compare-added');
        if ($.cookie('COMPARE_LIST'))
            _arTmp  = $.cookie('COMPARE_LIST').split(',');
        $.each(_arTmp, function(_index, _value){
            $('.buy__item-diff[data-id=' + _value + '], .icon-diff-big[data-id=' + _value + ']')
                .addClass('compare-added').attr('data-tooltip', $('.compare-item-button').data('added'));
            if ($('.buy__item-diff[data-id=' + _value + ']').length)
                $('.buy__item-diff[data-id=' + _value + ']').find('.buy__item-diff-link')
                    .text($('.compare-item-button').data('added'));
        });
    },

    init: function () {
        var cp = CP.Main;

        $(function () {
            _timer = setTimeout('CP.setEventsAddToCompareList();', 1500);
            //cp.setEventsAddToCompareList();
            cp.checkCompareItems();
            //cp.setEventDeleteCompareItem();
        });
        return cp;
    }
};

CP = CP.Main.init();