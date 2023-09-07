;
"use strict";

var SOA     = SOA || {},
    _box    = $('.asideCart'),
    boxEnd  = $('.totalSubmit')
    topEnd  = 0,
    _boxHeight = 0,
    _valid  = true,
    basePrice   = 0;

SOA.Main = {
    vars  : {
    },

    asideOnScroll() {
        if (_box == undefined)
            return false;

        _boxHeight  = (_box.height() + parseInt(_box.css('paddingTop').replace('px'))
            + parseInt(_box.css('paddingBottom').replace('px'))
            + parseInt(_box.css('borderTopWidth').replace('px'))
            + parseInt(_box.css('borderBottomWidth').replace('px'))
        );

        topEnd  = $('#himself').height() + $('#himself').offset().top - _boxHeight;

        if ($(window).width() >= 1280) {
            _box.css('margin-left', '900px');
            _box.css('right', 'auto');
        } else {
            _box.css('margin-left', '0');
            _box.css('right', '0');
        }

        if ($(window).scrollTop() <= $('#himself').offset().top) {
            _box.css('position', 'absolute');
            _box.css('top', 0);
        } else if ($(window).scrollTop() >= topEnd){
            _box.css('position', 'absolute');
            _box.css('top', $('#himself').height() - _boxHeight);
        } else {
            _box.css('position', 'fixed');
            _box.css('top', 0);
        }
    },

    setEvents   : function(){
        $('#tabHimself').click(function(e){
            e.preventDefault();

            SOA.asideOnScroll();
        });

        $(window).scroll(function(){
            SOA.asideOnScroll();
        });
        $(window).resize(function(){
            SOA.asideOnScroll();
        });

        $('#delivery-for-MKAD').click(function() {
            SOA.recalculate();
        }).change(function () {
            $('.if-MKAD').slideToggle();
        });

        $('#select-mkad-km').change(function(){
            SOA.recalculate();
        });

        this.sendOrderEvent();

        this.addService();

        this.setPaymentSystemEvents();

        this.setServicesEvents();

        this.setDeliveryEvents();
    },

    setDeliveryEvents   : function(){
        if (!$('#delivery-list div.itemDelivery').length)
            return false;

        $('#delivery-list div.itemDelivery').unbind('click').bind('click', function(e) {
            if ($(this).hasClass('active'))
                return false;

            // -- Set active item ------------------------------------------- //
            $(this).parents('div.himselfBlock').find('.itemDelivery').removeClass('active');
            $(this).addClass('active');

            // -- Show delivery note block ---------------------------------- //
            $('div.delivery-item-note').slideUp('fast');
            if ($('div.delivery-item-note[data-id=' + $(this).data('parent_id') + ']').length)
                $('div.delivery-item-note[data-id=' + $(this).data('parent_id') + ']').
                    stop().slideDown('fast');

            // -- Set order delivery value ---------------------------------- //
            $('#full-order-form input[name="DELIVERY_ID"]').val($(this).data('id'));
			$('#full-order-form input[name="PARENT_DELIVERY_ID"]').val($(this).data('parent_id'));

            // -- Show MKAD block for Moscow -------------------------------- //
            if ($('#delivery_form_MKAD').length){
                if ($(this).data('is_mkad'))
                    $('#delivery_form_MKAD').slideDown('fast');
                else
                    $('#delivery_form_MKAD').slideUp('fast');
            }
			
			//Если до ТК, скрываем налиный расчет
			if($(this).data('parent_id') == 29){
				$('#pay-system-list .itemDelivery[data-id = 1]').hide();
			}
			else{
				$('#pay-system-list .itemDelivery[data-id = 1]').show();
			}
			
			//Если до ТК или сами, выводим адрес доставки
			if($(this).data('parent_id') == 29 || $(this).data('parent_id') == 21){
				$('.input_adres').show();
				$('.input_adres input[name=ORDER_PROP_26]').attr('required', 'required');
				$('.input_adres input[name=ORDER_PROP_27]').attr('required', 'required');
			}
			else{
				$('.input_adres').hide();
				$('.input_adres input[name=ORDER_PROP_26]').removeAttr('required');
				$('.input_adres input[name=ORDER_PROP_27]').removeAttr('required');
				$('.input_adres input').val('');
			}

            SOA.recalculate();
        });

        if ($('#full-order-form input[name="DELIVERY_ID"]').val())
            $('#delivery-list div.itemDelivery[data-id=' + $('#full-order-form input[name="DELIVERY_ID"]').val() + ']').trigger('click');

        // -- Set active delivery service ----------------------------------- //
        if ($('#full-order-form input[name="DELIVERY_ID"]').val()){
            if ($('#delivery-list div.itemDelivery[data-id=' + $('#full-order-form input[name="DELIVERY_ID"]').val() +']').length)
                $('#delivery-list div.itemDelivery[data-id=' + $('#full-order-form input[name="DELIVERY_ID"]').val() +']').trigger('click');
            else
                $('#delivery-list div.itemDelivery:eq(0)').trigger('click');
        }
    },

    setPaymentSystemEvents  : function(){
        if (!$('#pay-system-list div.itemDelivery').length)
            return false;

        $('#pay-system-list div.itemDelivery').unbind('click').bind('click', function(e) {
            if ($(this).hasClass('active'))
                return false;

            $(this).parents('div.himselfBlock').find('.itemDelivery').removeClass('active');
            $(this).addClass('active');

            $('#full-order-form input[name="PAY_SYSTEM_ID"]').val($(this).data('id'));
        });
    },

    addService  : function(){
        $('div.itemService').click(function() {
            if (!$(this).find('input[type=checkbox]').is(':checked'))
                $('#basket-total-aside-block span[data-service="' + $(this).data('service') + '"]').html($(this).find('label').html());
            else
                $('#basket-total-aside-block span[data-service="' + $(this).data('service') + '"]').html('не требуется');
        });
    },

    setServicesEvents   : function(){
        $('.itemService').click(function () {
            $(this).toggleClass('active');
            if (!$(this).find('.filter__checkbox input').prop('checked'))
                $(this).find('.filter__checkbox input').prop('checked', true).val('Y');
            else
                $(this).find('.filter__checkbox input').prop('checked', false).val('N');
        });

        $('.itemService .filter__checkbox input').change(function () {
            $(this).parent().parent().parent().toggleClass('active');
        });
    },

    validateOrder   : function () {
		_valid = true;

        if ($('.order-error').length)
            $('.order-error').removeClass('order-error');
        if ($('#full-order-form .error').length)
            $('#full-order-form .error').removeClass('error');

		if (!$('#full-order-form input[name="DELIVERY_ID"]').val()) {
			_valid = false;
			$('#delivery-list').addClass('order-error');
		}

        if (!$('#full-order-form input[name="PAY_SYSTEM_ID"]').val()) {
			_valid = false;
			$('#pay-system-list').addClass('order-error');
		}

		if (!$('#full-order-form input[name="ORDER_PROP_1"]').val()) {
			_valid = false;
			$('#contact-data input[name=ORDER_PROP_1]').addClass('order-error');
		}
		
		if (!$('#full-order-form input[name="ORDER_PROP_3"]').val() || (new RegExp('\\([7|8]\\d+\\)')).test($('#full-order-form input[name="ORDER_PROP_3"]').val())) {
			_valid = false;
			$('#contact-data input[name=ORDER_PROP_3]').addClass('order-error');
            $('#contact-data input[name=ORDER_PROP_3]').parent().addClass('error');
            $('#contact-data input[name=ORDER_PROP_3]').parent().attr('data-tooltip','Введите корректный номер телефона');
		}
		
		if (!$('#full-order-form input[name="ORDER_PROP_2"]').val()) {
			_valid = false;
			$('#contact-data input[name=ORDER_PROP_2]').addClass('order-error');
		}
		
		if (!$('#full-order-form input[name="ORDER_PROP_26"]').val() && ($('#full-order-form input[name="PARENT_DELIVERY_ID"]').val() == 21 || $('#full-order-form input[name="PARENT_DELIVERY_ID"]').val() == 29)) {
			_valid = false;
			$('#contact-data input[name=ORDER_PROP_26]').addClass('order-error');
		}
		
		if (!$('#full-order-form input[name="ORDER_PROP_27"]').val() && ($('#full-order-form input[name="PARENT_DELIVERY_ID"]').val() == 21 || $('#full-order-form input[name="PARENT_DELIVERY_ID"]').val() == 29)) {
			_valid = false;
			$('#contact-data input[name=ORDER_PROP_27]').addClass('order-error');
		}

        if (!_valid)
            $("html, body").stop().animate({scrollTop: $('.order-error:eq(0)').offset().top}, 500, 'swing');

		return _valid;
	},

    recalculate : function() {
		basePrice       = $('#basket-total-aside-block .aside-price-full').data('price');
        deliveryPrice   = $('#delivery-list .itemDelivery.active .price').data('price');
        if (deliveryPrice == undefined || !deliveryPrice)
            deliveryPrice = 0;

		if ($('#delivery-for-MKAD').is(':checked') &&
            $('#delivery-list .itemDelivery.active').data('is_mkad')
        )
			deliveryPrice += $('#delivery-for-MKAD').data('price') * $('#select-mkad-km').val();

		$('#basket-total-aside-block .aside-delivery').text(BX.Currency.currencyFormat(deliveryPrice, 'RUB', true));
		$('#basket-total-aside-block .aside-price-full').text(BX.Currency.currencyFormat(deliveryPrice + basePrice, 'RUB', true));
		$('#orderFullPrice').html(BX.Currency.currencyFormat(deliveryPrice + basePrice, 'RUB', true));
	},

    sendOrderEvent  : function(){
        if ($('#full-order-form').length)
            $('#full-order-form').submit(function(e) {
                e.preventDefault();

                if (SOA.validateOrder()) {
                    if (!$('#delivery-list .itemDelivery.active').data('is_mkad'))
                        $('#select-mkad-km').val('');

                    ShowPreload();
                    SC.delObject(_post);
                    $(this).find('input[type=hidden],input[type=checkbox],input[type=radio],input[type=text],input[type=tel],select,textarea').each(function(){
                        _post[$(this).attr('name')] = $(this).val();
                    });
                    SC.ajaxSendPost(
                        window.location.href,
                        _post,
                        function (data) {
                            if (data['order'])
                                window.location.href = "/personal/order/?ORDER_ID=" + data.order.ID;
                            HidePreload();
                            BX.closeWait(SC.vars.wait);
                            SC.vars.send = true;
                        }
                    );
                }
            });
    },

    getDeliveryServices : function(){
        SC.ajaxSendPost(
            '/local/templates/.default/components/bitrix/sale.order.ajax/restyled/ajax_delivery.php',
            {
                PRICE   : $('span.aside-price-full').data('price'),
                REGION  : $('#location_id').val()
            },
            function (data) {
                if (data.status == 'success'){
                    if (SC.count(data.ITEMS)){
                        $('#delivery-list').html('<div class="listDelivery columns"></div>');
                        $.each(data.ITEMS, function(_id, _item){
                            $('#delivery-list div.listDelivery').append($("#deliveryTemplate").render(_item));
                        });
                        SOA.setDeliveryEvents();
                    }
                }
                BX.closeWait(SC.vars.wait);
                SC.vars.send = true;
            }
        );
    },

    init: function () {
        var soa = SOA.Main;

        $(function () {
            soa.setEvents();
            soa.getDeliveryServices();
        });
        return soa;
    }
};

SOA = SOA.Main.init();