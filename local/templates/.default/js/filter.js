;
"use strict";

var SFTimer = null;
var SFID = null;

var SF  = SF || {},
    _filter_form    = $('#smart-filter');

SF.Main = {
    vars  : {},

    initSortFilter  : function(){
        if ($('select[name=catalog-sort]').length)
            $('select[name=catalog-sort]').change(function(){
                window.location.href = $(this).val();
            });
    },

    checkSelectedOptions    : function(){
        if ($('div.filter div.selected-items').length)
            $('div.goods__card-sort-filter div.tag').css('display', 'none')
                .removeClass('hide').fadeIn('fast');
    },

    submitFilter    : function(event){
        ShowPreload();

        setTimeout(function () {
            $.ajax({
                url : window.location.pathname + '?ajax=y',
                type: 'GET',
                data: $('#smart-filter').serialize(),
                success: function(data) {
                    HidePreload();
					
					var matches, category, arFilterUrl, strFilterUrl, URL;
					
					//URL текущей категории
					matches = data.SEF_DEL_FILTER_URL.match(/(.*)clear\//);
					if(matches !== null){
						category = matches[1];
						
						//Выделяем что выбранно в фильтре
						arFilterUrl = data.FILTER_URL.split(category);
						strFilterUrl = arFilterUrl[1];
					}		
					
					$.each(arLinksFilterTag, function(){
						if(this.filter == strFilterUrl){
							URL = category + this.code + '/';
							
							data.FILTER_URL = URL;
						}
					});
					
                    _filter_form.find('.filter__button-mobile--blue .tag')
                        .html(data.ELEMENT_COUNT);
                    _filter_form.find('input[name=current_url]').val(data.FILTER_URL);
                    _filter_form.attr('action', data.FILTER_URL);
                    if(event=='delete'){
                        var str =data.FILTER_URL;
                        var deleteUrl =str.replace('/clear/','/');
                        window.location.href = (deleteUrl);
                    }
                    if (globalWidth > 768){
                        $('#modef_popup div.tag').text(data.ELEMENT_COUNT);
                        var top = (_filter_form.find('.selected').offset().top - $('.goods').offset().top)
                                    - ($('#bx-panel').length ? $('#bx-panel').height() : 0)
                                    - ( ($('#modef_popup').height()+ parseInt($('#modef_popup').css('paddingTop').replace('px'))) / 2 );
                        var offContainer = $('.container.goods__container').offset();
                        var offFilter = $('.goods__filter').offset();
                        var left = offFilter.left - offContainer.left + $('.goods__filter').width() + 10;

                        $('#modef_popup').css('top', top+'px');
                        $('#modef_popup').css('left', left+'px');
                        $('#modef_popup').fadeIn('fast');
                    }
                },
                dataType: 'json'
            });
        }, 500);
    },

    setSmartFilterEvents    : function(){
        if (!_filter_form.length)
            return false;

        _filter_form.find('button[type=reset]').click(function(){
            if (_filter_form.find('input[name=clear_filter]').val())
                window.location.href    = _filter_form.find('input[name=clear_filter]').val();
        });

        _filter_form.find('input[type=text]:not(.filter-fast-search-input)').keyup(function(){
			SFID = $(this).attr('id');
			if(!!SFTimer)
			{
				clearTimeout(SFTimer);
			}
			SFTimer = setTimeout(BX.delegate(function(){
				_filter_form.find('.selected').removeClass('selected');
				$('#'+SFID).addClass('selected');
				SF.submitFilter();
			}), 500);
		});

        _filter_form.find('input[type=checkbox], select').change(function(){
            if ($(this).hasClass('dropdown-open'))
            {
            	if ($(this).is(':checked'))
            	{
            		$('#'+$(this).data('id')).show();
            		$('#'+$(this).data('id')+' input').focus();
            	}
            	else
            	{
            		$('#'+$(this).data('id')).hide();
            		$('#fs'+$(this).data('key')).val('');
            		$('.filter-fast-search-checkbox-'+$(this).data('key')).show();
            	}
                return true;
                
            }

            _filter_form.find('.selected').removeClass('selected');
            $(this).addClass('selected');
            if($(this).data('info')=='delete'){
                SF.submitFilter('delete');
            }else{
                SF.submitFilter();
            }
        });

        if ($('#modef_popup .show-items').length)
            $('#modef_popup .show-items').click(function(e){
                e.preventDefault();

                window.location.href = (_filter_form.find('input[name=current_url]').val());
            });

        _filter_form.submit(function(e){
            e.preventDefault();

            window.location.href = ($(this).find('input[name=current_url]').val());
        });

        if ($('.selected-items .selected-item .close').length)
            $('.selected-items .selected-item .close').click(function(e){
                $(this).parent().slideUp('fast');
                _filter_form.find('input[name=' + $(this).parent().data('id') + ']').data('info', 'delete');
                _filter_form.find('input[name=' + $(this).parent().data('id') + ']').trigger('click');
            });
    },

    closeHintInit   : function(){
        if ($('#modef_popup .close-hint').length)
            $('#modef_popup .close-hint').click(function(){
                $('#modef_popup').fadeOut('fast');
            });
    },

    init    : function(){
        var sf = SF.Main;

        $(function () {
            if (!_filter_form.length)
                _filter_form    = $('#smart-filter');

            $('.filter__button-mobile--blue .tag')
                .html(($('.pagination-wrapper').data('items') != undefined ? $('.pagination-wrapper').data('items') : ''));
            sf.initSortFilter();
            sf.checkSelectedOptions();
            sf.setSmartFilterEvents();
            sf.closeHintInit();
        });

        return sf;
    }
};

SF = SF.Main.init();

(function(window) {
    if (!!window.JSCatalogSmartFilter) return;

    window.JSCatalogSmartFilter = function(params) {

    };
})(window);

(function(window) {
    if (!!window.JSCatalogSmartFilterSlider) return;

    window.JSCatalogSmartFilterSlider = function(params) {
        this.instance = null;

        if (typeof params === "object") {
            this.sliderSelector = params.sliderSelector;
            this.minInputSelector = params.minInputSelector;
            this.maxInputSelector = params.maxInputSelector;
            this.minPrice = params.minPrice;
            this.maxPrice = params.maxPrice;
            this.curMinPrice = params.curMinPrice;
            this.curMaxPrice = params.curMaxPrice;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogSmartFilterSlider.prototype.GetSlider = function() {
        return $(this.sliderSelector);
    };

    window.JSCatalogSmartFilterSlider.prototype.GetMinInput = function() {
        return $(this.minInputSelector);
    };

    window.JSCatalogSmartFilterSlider.prototype.GetMaxInput = function() {
        return $(this.maxInputSelector);
    };

    window.JSCatalogSmartFilterSlider.prototype.Init = function() {
        this.CreateSlider();
        this.BindInputs();
    };

    window.JSCatalogSmartFilterSlider.prototype.CreateSlider = function() {
        var slider = this.GetSlider();

        slider.ionRangeSlider({
            type: "double",
            min: this.minPrice,
            max: this.maxPrice,
            from: this.curMinPrice,
            to: this.curMaxPrice,
            // prefix: 'Rp. ',
            //onStart: $.proxy(this.UpdateInputs, this),
            onChange: $.proxy(this.UpdateInputs, this),
            onFinish: $.proxy(this.SendForm, this),
            step: 1,
            prettify_enabled: true,
            prettify_separator: " ",
            values_separator: " - ",
            force_edges: true
        });
        this.instance = slider.data("ionRangeSlider");
    };

    window.JSCatalogSmartFilterSlider.prototype.UpdateInputs = function(data) {
        this.GetMinInput().val(data.from);
        this.GetMaxInput().val(data.to);
    };

	window.JSCatalogSmartFilterSlider.prototype.SendForm = function(data) {
        _filter_form.find('.selected').removeClass('selected');
        $(data.input).parents('.range-slider').addClass('selected');
		SF.submitFilter();
    };

	window.JSCatalogSmartFilterSlider.prototype.onChange = function(data) {
    };

    window.JSCatalogSmartFilterSlider.prototype.BindInputs = function() {
        var minInput = this.GetMinInput();
        var maxInput = this.GetMaxInput();

        minInput.on("input", $.proxy(function() {
            var minValue = +minInput.val();
            var maxValue = +maxInput.val();

            var value = minValue;
            if (value < this.minPrice) value = this.minPrice;
            else if (value > maxValue) value = maxValue;

            this.instance.update({
                from: value
            });
        }, this));
        maxInput.on("input", $.proxy(function() {
            var minValue = minInput.val();
            var maxValue = maxInput.val();

            var value = maxValue;
            if (value < minValue) value = minValue;
            else if (value > this.maxPrice) value = this.maxPrice;

            this.instance.update({
                to: value
            });
        }, this));
    };

    window.JSCatalogSmartFilterSlider.prototype.Reset = function() {
        this.instance.update({
            from: this.curMinPrice,
            to: this.curMaxPrice
        });
    };
})(window);