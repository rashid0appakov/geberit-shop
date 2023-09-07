;
"use strict";

var SBB  = SBB || {},
    _sbb_items_count= 0,
    _sbb_start_load = 0,
    _sbb_timer_load = 0,
    _delay_load     = _delay_load || 0;

SBB.Main = {
    vars  : {
    },

    setCartEvents   : function(){
        if ($('.cart .cart__counter-plus, .cart .cart__counter-minus').length)
            $('.cart .cart__counter-plus, .cart .cart__counter-minus').unbind('click').bind('click', function(e){
                e.preventDefault();

                _sbb_items_count    = parseInt($(this).parent().find('.cart__counter-counter').text());

                if ($(this).hasClass('cart__counter-minus')){
                    if (_sbb_items_count >= 1){
                        _sbb_items_count -= 1;

                        if (_sbb_items_count == 0)
                            _sbb_items_count = 1;
                        else
                            $(this).parents('div.trCart').addClass('has-changed');
                    }else
                        _sbb_items_count = 1;
                }else{
                    _sbb_items_count += 1;
                    $(this).parents('div.trCart').addClass('has-changed');
                }

                $(this).parent().find('.cart__counter-counter').text(_sbb_items_count);
                $(this).parent().find('input').val(_sbb_items_count);
                if (SC.getCurrentTime() - _sbb_start_load <= _delay_load)
                    _sbb_timer_load = clearTimeout(_sbb_timer_load);

                _sbb_timer_load = setTimeout('SBB.updateCartProducts();', _delay_load);
                //UpdateBasketAjax()
                _sbb_start_load = SC.getCurrentTime();
            });

        /*if ($('.enterCoupon label').length)
            $('.enterCoupon label').click(function(e) {
                e.preventDefault();
                $('.enterCoupon label input').show('fast');
                $('.forCoupon').show();
                $('.enterCoupon label span').hide('fast');
                $(this).addClass('active');
            });*/


    },

    updateCartProducts  : function() {
         // if ($('div.cart div.has-changed').length){
            SC.delObject(_post);

            _post['method'] = 'cart-data';
            $('form.basket input[name^="BASKET"]').each(function(_index, _value){
                _post[$(this).attr('name')] = $(this).val();
            });

            SC.ajaxSendPost(
                '/ajax/',
                _post,
                function(data){
                    BX.onCustomEvent('itco:OnBasketChange', [data]);
                    //console.log(data);
                    SC.vars.send = true;
                    BX.closeWait('', SC.vars.wait);
                    if (data['status'] == 'success'){
                        $.each(data.BASKET.ITEMS, function(_index, _value){
                            if ($('div.cart div.trCart[data-id=' + _value['ID'] + ']').length){
                                // $(this).removeClass('has-changed');
                                if(_value['DISCOUNT_INT'] != 0){
                                    $('#basket-item-price-old-text-'+ _value['ID']).text(_value['FULL_PRICE']);
                                    $('#basket-item-price-'+ _value['ID']).text(_value['PRICE']);
                                    $('#basket-item-sum-price-old-'+ _value['ID']).text(_value['FULL_PRICE_TOTAL']);
                                    $('#basket-item-sum-price-difference-'+ _value['ID']).html('<div class="basket-item-price-difference">Экономия ' + _value['DISCOUNT_TOTAL']+'</div>');
                                }
                                else{
                                    $('#basket-item-price-old-text-'+ _value['ID']).html('');
                                    $('#basket-item-price-'+ _value['ID']).text(_value['PRICE']);
                                    $('#basket-item-sum-price-old-'+ _value['ID']).html('');
                                    $('#basket-item-sum-price-difference-'+ _value['ID']).html('');                                  
                                }
                                $('div.cart div.trCart div.cart__counter-counter[data-id=' + _value['ID'] + ']').text(_value['QUANTITY']);
                                $('div.cart div.trCart input[name="BASKET[' + _value['ID'] + ']"]').val(_value['QUANTITY']);
                                $('#basket-item-sum-price-' + _value['ID']).text(_value['PRICE_TOTAL']);
                            }
                        });

                        $('#basket-full-total').text(data.BASKET.SUMM_FORMATED)
                            .attr('data-price', data.BASKET.SUMM_FORMATED);

                        if (typeof SOA == 'object' && $('span.aside-price-full').length){
                            $('span.aside-price-full').data('price', data.BASKET.SUMM);
                            $('span.aside-price').text(data.BASKET.SUMM_FORMATED);
                            $('span.aside-products-count i.value').text(data.BASKET.QUANTITY);

                            SOA.getDeliveryServices();
                        }
                    }
                }
            );
        // }
    },

    setLinksEvents  : function(){
        $('.sendOrder a[href="#make-order"]').click(function(e) {
            e.preventDefault();

            $('#tabHimself').click();
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top
            }, 800);
        });
        $('#basket-total-aside-block .buyOneClick').click(function(e) {
            e.preventDefault();

            $('#tabExpress').click();
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top
            }, 800);
        });
    },

    /**
     * Выбор по умолчанию первого подарка
     */
    selectDefaultGift   : function(){
        $('.listPodarok').each(function(){
            if(!$(this).find('.choosePodarok:checked').length){ // Если нет уже выбранных
                if($(this).find('.choosePodarok').first().length) { // Если есть элемент для выбора
                    $(this).find('.choosePodarok').first().click(); // Выбор первого элемента
                }
            }
        });
    },

    init: function () {
        var sbb = SBB.Main;

        $(function () {
            sbb.setCartEvents();
            sbb.setLinksEvents();
            sbb.selectDefaultGift();
        });
        return sbb;
    }
};

SBB = SBB.Main.init();