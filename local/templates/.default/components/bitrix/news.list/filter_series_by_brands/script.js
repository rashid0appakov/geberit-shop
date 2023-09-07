;
"use strict";

var CFilter  = CFilter || {},
    _filter_time    = null,
    _vals   = [];

CFilter.Main = {
    setFormEvents   : function(){
        $('#filter_brands form').submit(function(e){
            e.preventDefault();
        });
        $('#filter_brands form input[type=checkbox]').change(function(){
            clearTimeout(_filter_time);
            _filter_time = setTimeout('CFilter.setFilter()', 1500);
        });
    },

    setFilter   : function(){
        _vals   = [];
        $('#filter_brands form input[type=checkbox]').each(function(){
            if ($(this).is(':checked'))
                _vals[_vals.length] = $(this).data('id');
        });

        if (_vals.length){
            ShowPreload();
            setTimeout(function(){
                window.location.href = '?brands=' + _vals.join('_');
            }, 500);
        }
    },

    get : function(_name){
        if (_name=(new RegExp('[?&]' + encodeURIComponent(_name)+'=([^&]*)')).exec(location.search))
           return decodeURIComponent(_name[1]);
    },

    checkFilterItems    : function(){
        if (CFilter.get('brands') != undefined){
            _vals = CFilter.get('brands').split('_');
            $.each(_vals, function(_i, _v){
                if ($('#brand_' + _v).length)
                    $('#brand_' + _v).trigger('click');
            });
        }
    },

    init: function () {
        var cf = CFilter.Main;

        $(function () {
            cf.checkFilterItems();
            setTimeout(function(){
                CFilter.setFormEvents();
            }, 500);
        });
        return cf;
    }
};

CFilter = CFilter.Main.init();