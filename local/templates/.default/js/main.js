;
"use strict";

var SC  = SC || {},
        _timer  = null,
        _timer_delivery = null,
        _post   = new Object(),
        _tmp    = '',
        _items_count    = 0,
        _delay_load = 1200,
        _start_load = 0,
        _timer_load = null,
        globalWidth = window.screen.width,
        _sbbs_items_count = 0,
        f_name  = '';

SC.Main = {
    vars  : {
        send    : true,
        wait    : null
    },

    count   : function(obj) {
        var count = 0;

        for(var prop in obj)
            if (obj.hasOwnProperty(prop))
                ++count;

        return count;
    },

    delObject : function(_obj){
        for(var x in _obj)
            delete _obj[x];
    },

    getCurrentTime : function(){
        return new Date().getTime();
    },

    ajaxSendPost: function(url, _post, _callback, type){
        if (!SC.vars.send)
            return false;

        SC.vars.wait = BX.showWait('');
        SC.vars.send = false;
        _post['sessid'] = BX.message("bitrix_sessid");
        $.post(url, _post, _callback, (!type ? 'json' : type ));
        SC.delObject(_post);
    },

    ajaxSendGet: function(url, _post, _callback, type){
        if (!SC.vars.send)
            return false;

        SC.vars.wait = BX.showWait('');
        SC.vars.send = false;
        _post['sessid'] = BX.message("bitrix_sessid");
        $.get(url, _post, _callback, (!type ? 'json' : type ));
        SC.delObject(_post);
    },

    setFormEvents   : function(){
        if ($('#formOneClick').length)
            $('#formOneClick').submit(function(e){
                e.preventDefault();

                SC.sendOneClick($(this));
            });
    },

    sendOneClick    : function(_form) {
        if (!_form.length)
            return false;

        _form.find('.error').removeClass('error').addClass('hide');
        if (!$('.popupOneClick input[name=inputTel]').val().length){
            $('.popupOneClick input[name=inputTel]').addClass('error');
            return false;
        }

        if (!_form.find('.error').length)
            var ID_VAR  = $('.popupOneClick input[name="item_id"]').val();
            var NAME_VAR    = $('.popupOneClick input[name="name"]').val();
            var EMAIL_VAR   = $('.popupOneClick input[name="email"]').val();
            var PHONE_VAR   =  $('.popupOneClick input[name=inputTel]').val();

            SC.ajaxSendPost(
                '/local/templates/.default/components/altop/buy.one.click/restyled/script.php',
                {
                    ID      : ID_VAR,
                    NAME    : NAME_VAR,
                    EMAIL   : EMAIL_VAR,
                    PHONE   : PHONE_VAR,
                    QUANTITY: 1,
                    BUY_MODE: 'ONE',
                    PERSONAL_DATA: 'Y',
                    PARAMS_STRING: $('.popupOneClick input[name=params]').val(),
                    IBLOCK_ID: $('.popupOneClick input[name="iblock_id"]').val()
                },
                function(data){
                    $('.popupOneClick .callBackLink').disabled = true;
                    SC.vars.send = true;
                    BX.closeWait('', SC.vars.wait);

                    // console.log('alert1');

                    // console.log(data);
                    // console.log(data.success.text);
                    // console.log(data.success.text.length);

                    if (data['error'] != undefined ){ // data.success.text.length <= 0
                        $('.popupOneClick input[name=inputTel]').addClass('error')
                            .next().html(data.error.text)
                            .addClass('error').removeClass('hide');
                    }else{
                        dashamail("async", {
                            "operation": "OneClickOrder",
                            "data": {
                                "customer": {
                                    "name": NAME_VAR,
                                    "email": EMAIL_VAR,
                                    "mobilePhone": PHONE_VAR
                                },
                                "order": {
                                    "totalPrice": $('.popup-window input[name="sum"]').val(),
                                    "status": "created",
                                    "lines": [
                                        {
                                            "productId": ID_VAR ,
                                            "quantity": "1",
                                            "price": "<Цена для клиента>"
                                        }
                                    ]
                                }
                            }
                        });

                        console.log('alert2');
                        console.log(data);

                        $('.popupOneClick .OrderInfo').html(data.success.text);

                        $('.popupOneClick .OrderInfo').removeClass('hide');
                        $('.popupOneClick .hideOnSubmit').hide();
                    }
                },
                'json'
            );
    },

    setSmallBasketEvents    : function(){
        if ($('.slider-info__counter-minus, .slider-info__counter-plus').length)
            $('.slider-info__counter-minus, .slider-info__counter-plus').click(function(e){
                e.preventDefault();

                SC.setPlusMinusButtonEvents($(this));
            });

        if ($('.delete-basket').length)
            $('.delete-basket').click(function(e){
                e.preventDefault();

                SC.deleteBasketItem($(this));
            });
    },

    setPlusMinusButtonEvents : function(_this){
        _items_count    = parseInt(_this.parent().find('.slider-info__counter-counter').text());

        if (_this.hasClass('slider-info__counter-minus')){
            if (_items_count >= 1){
                _items_count -= 1;

                if (_items_count == 0)
                    _items_count = 1;
                else
                    _this.parents('div._toolbarItem').addClass('has-changed');
            }else
                _items_count = 1;
        }else{
            _items_count += 1;
            _this.parents('div._toolbarItem').addClass('has-changed');
        }

        _this.parent().find('.slider-info__counter-counter').text(_items_count);
        if (SC.getCurrentTime() - _start_load <= _delay_load)
            _timer_load = clearTimeout(_timer_load);

        _timer_load = setTimeout('SC.updateBasketProducts();', _delay_load);
        _start_load = SC.getCurrentTime();
    },

    deleteBasketItem    : function(_this){
        if (!_this.attr('data-id'))
            return false;

        _this.parent().parent().hide();
        $('.slider-toolbar').slick('unslick');
        $('.toolbar-bottom__slider-slide[data-id="' + _this.attr('data-id') + '"]').remove();

        SC.ajaxSendPost(
            '/ajax/',
            {
                method  : 'delete-cart-item',
                ID      : _this.attr('data-id')
            },
            function(data){
                SC.vars.send = true;
                BX.closeWait('', SC.vars.wait);

                if (data['status'] == 'success'){
                    SC.initSliderToolbar();

                    $('.bottom_cart').replaceWith(data.bottom_cart);
                    $('.top_cart').replaceWith(data.top_cart);
                }else{
                    console.log(data.msg);
                }
            },
            'json'
        );
    },

    updateBasketProducts    : function(){
        if ($('div.toolbar-bottom__slider div.has-changed').length){
            SC.delObject(_post);
            _post   = {
                ITEMS   : [],
                QTY     : [],
                method  : 'bottom-basket'
            };
            $('div.toolbar-bottom__slider div.has-changed').each(function(){
                $(this).removeClass('has-changed');
                _post.ITEMS[_post.ITEMS.length] = $(this).find('.toolbar-bottom__slider-info').attr('data-id');
                _post.QTY[_post.QTY.length]     = parseInt($(this).find('.slider-info__counter-counter').text());
            });

            SC.ajaxSendPost(
                '/ajax/',
                _post,
                function(data){
                    SC.vars.send = true;
                    BX.closeWait('', SC.vars.wait);

                    if (data['status'] == 'success'){
                        $('.bottom_cart').replaceWith(data.bottom_cart);

                        if ($(data.ITEMS.length))
                            $.each(data.ITEMS, function(_index, _value){
                                $('div.slider-info[data-id=' + _value['ID'] + '] span.slider-info__counter-counter').text(_value['QUANTITY']);
                                $('div.slider-info[data-id=' + _value['ID'] + '] p._sum_price').text(_value['PRICE_TOTAL']);
                                $('div.toolbar-bottom__slider-slide[data-id=' + _value['ID'] + '] span.tag').text(_value['QUANTITY']);
                            });
                    }else{
                        console.log(data);
                    }
                },
                'json'
            );
        }
    },

    setCatlogMenuEvent  : function(){
        if ($('.categories__list-item').length){
            $('.categories__list-item').mouseover(function () {
                if (!$($(this).data('cat')).length)
                    return true;

                $('.categories__list-item').removeClass('categories__list-item--active');
                $(this).addClass('categories__list-item--active');
                $('.categories__content').removeClass('categories__content--active');
                $($(this).data('cat')).addClass('categories__content--active');
            });

            /*$('.categories__list-item').click(function(e) {
                if (!$(this).attr('href'))
                    return false;

                window.location.href = $(this).attr('href');
            });*/
        }
    },

    setPopupsLinks  : function(){
        if ($('a.modal-link').length)
            $('a.modal-link').click(function(e){
                e.preventDefault();

                if (!$($(this).attr('href')).length)
                    return false;

                $("body").addClass("overlay69");
                $("html").addClass("overlay");

                $('.form-success').addClass('hide');
                $('.form-wrapper').removeClass('hide');
                $($(this).attr('href')).show();
            });

        if ($('.close69, .close96').length)
            $('.close69, .close96').unbind('click').bind('click', function(e){
                e.preventDefault();

                return SC.closePopup();
        });
    },

    closePopup  : function(){
        $("body").removeClass("overlay69");
        $("html").removeClass("overlay");
        $(".popup69").hide();

        return false;
    },

    initSliders : function(){
        if ($('.slider-toolbar').length)
            SC.initSliderToolbar();
    },

    initSliderToolbar   : function(){
        if ($('.slider-toolbar').hasClass('slick-initialized'))
            $('.slider-toolbar').slick('unslick');

        $('.slider-toolbar').slick({
            slidesToShow    : 3,
            slidesToScroll  : 1,
            prevArrow       : '<div class="toolbar-bottom__slider-prev-arrow"></div>',
            nextArrow       : '<div class="toolbar-bottom__slider-next-arrow"></div>',
        });

        $('.toolbar-bottom__slider-slide').hover(
            function () {
                $('.toolbar-bottom__slider-info[data-id=' + $(this).data('id') + ']').fadeIn('fast');
                $('.toolbar-bottom__slider-info[data-id!=' + $(this).data('id') + ']').css('display', 'none');
            },
            function(){
                return true;
            }
        );
    },

    initPageEvents  : function(){
        $(document).mouseup(function(e) {
            if (!$(e.target).parents('div._toolbarItem').length)
                $(".toolbar-bottom__slider-info").css('display', 'none');

            // -- hide mobile menu ------------------------------------------ //
            if (!$(".button__open-menu").get(0).contains(e.target) &&
                !$(".catalog-menu-popup").get(0).contains(e.target))
                if ($(".catalog-menu-popup").hasClass('catalog-menu-popup--show')) {
                    $(".catalog-menu-popup").removeClass("catalog-menu-popup--show");
                    $('.button__open-menu').removeClass('button__open-menu--white');
                    $('.icon-burger').removeClass('icon-burger--cross');

                    SC.mobileMenuClosed();
                }
        });

        $(document).resize(function (e) {
            SC.mobileMenuClosed();

            globalWidth = window.screen.width;
        });
    },

    mobileMenuOpened    : function(){
        if ($('.toolbar-bottom').length)
            $('.toolbar-bottom').slideUp('fast', function(){
                $(this).addClass('hide');
            });

        $('body').addClass('is-clipped').width($(window).width());
    },

    mobileMenuClosed    : function(){
        if ($('.toolbar-bottom').length && $('.toolbar-bottom').hasClass('hide'))
            $('.toolbar-bottom').slideDown('fast', function(){
                $(this).removeClass('hide');
            });

        if ($('body').hasClass('is-clipped')){
            $('body').removeClass('is-clipped');
            $('body').removeAttr('style');
        }
    },

    initMobileMenuButtons   : function(){
        // -- Main menu buttons --------------------------------------------- //
        $('#popup-main-menu-start-mobile, #popup-catalog-menu-start-mobile-button--bottom-header2').click(function () {
            SC.mobileMenuOpened();
            TweenMax.fromTo('#popup-main-main-menu', 1, {
                ease: Power4.easeOut,
                left: -globalWidth
            }, {
                ease: Power4.easeOut,
                left: 0
            });
        });

        $('#popup-main-menu-mobile-back.close').click(function () {
            SC.mobileMenuClosed();
            TweenMax.fromTo('#popup-main-main-menu', 1, {
                ease: Power4.easeOut,
                left: 0
            }, {
                ease: Power4.easeOut,
                left: -globalWidth
            });
        });

        $('#popup-catalog-menu-start-mobile-button, #popup-catalog-menu-start-mobile-button--bottom-header').click(function () {
            SC.mobileMenuOpened();
            TweenMax.fromTo('.popup-catalog-menu-start-mobile', 1, {
                ease: Power4.easeOut,
                left: -globalWidth
            }, {
                ease: Power4.easeOut,
                left: 0
            });
        });

        $('#popup-catalog-menu-mobile-close').on('click', function () {
            SC.mobileMenuClosed();
            TweenMax.fromTo('.popup-catalog-menu-mobile, .popup-catalog-menu-start-mobile', 1, {
                ease: Power4.easeOut,
                left: 0
            }, {
                ease: Power4.easeOut,
                left: -globalWidth
            });
        });

        $('#popup-catalog-menu-start-mobile').on('click', '.section', function (e) {
            var href = $(this).find('a').attr('data-href');

            if ($(this).find('a').attr('data-sub') == 'true') {
            	//e.preventDefault();
            	
                $(href).show();

                TweenMax.fromTo(href, 1, {
                    ease: Power4.easeOut,
                    left: 0
                }, {
                    ease: Power4.easeOut,
                    left: -globalWidth
                });
                TweenMax.fromTo(href, 1, {
                    ease: Power4.easeOut,
                    left: globalWidth
                }, {
                    ease: Power4.easeOut,
                    left: 0
                });
                
                return false;
            }
        });

        $('#popup-catalog-menu-mobile-back').click(function () {
            TweenMax.fromTo('popup-catalog-menu-start-mobile', 1, {
                ease: Power4.easeOut,
                left: -globalWidth
            }, {
                ease: Power4.easeOut,
                left: 0
            });
            TweenMax.fromTo('.popup-catalog-menu-mobile', 1, {
                ease: Power4.easeOut,
                left: 0
            }, {
                ease: Power4.easeOut,
                left: globalWidth
            });
        });


        // -- Catalog menu buttons ------------------------------------------ //
        $('.button__open-menu').click(function(e) {
            e.preventDefault();

            if ($(window).width() < 551 || !$('body .catalog-menu-popup.hero').length){
                $('#popup-catalog-menu-start-mobile').css("display","block");
                $('.catalog-menu-popup .header.container').css("height","auto");
                SC.mobileMenuOpened();
                TweenMax.fromTo('#popup-catalog-menu-start-mobile', 1, {
                    ease: Power4.easeOut,
                    left: -globalWidth
                }, {
                    ease: Power4.easeOut,
                    left: 0
                });
            } else {
                $('.button__open-menu').toggleClass('button__open-menu--white');
                $('.icon-burger').toggleClass('icon-burger--cross');
                $('.catalog-menu-popup').toggleClass('catalog-menu-popup--show');
            }
        });

        $('#popup-catalog-menu-start-mobile-close').click(function(){
            SC.mobileMenuClosed();
            TweenMax.fromTo('.popup-catalog-menu-start-mobile', 1, {
                ease: Power4.easeOut,
                left: 0
            }, {
                ease: Power4.easeOut,
                left: -globalWidth
            });
        });

        $('#mobile_catalog_btn').click(function(){
            if (!$('#popup-main-main-menu').offset().left)
                $('#popup-main-main-menu').animate(
                    {left: -globalWidth},
                    'fast'
                );
        });
    },

    initJivoSite    : function(){
//        if ($('.product__consultant-link a, a.actionChatConsultant, .toolbar-bottom__phone-link a:eq(1)').length)
            $('.product__consultant.middle-header .speaker, .product__consultant-link a, a.actionChatConsultant, .toolbar-bottom__phone-link a:eq(1)').click(function(e){
                e.preventDefault();

                if (typeof jivo_api == 'object')
                    jivo_api.open();
                if (typeof Chatra == 'function')
                    Chatra('openChat', true);
            });
		$('a.actionCallRequest, .toolbar-bottom__phone-link a:eq(0)').remove();
        if ($('a.actionCallRequest, .toolbar-bottom__phone-link a:eq(0)').length)
            $('a.actionCallRequest, .toolbar-bottom__phone-link a:eq(0)').click(function(e){
                e.preventDefault();

                if (typeof jivo_api == 'object')
                {
                    ym(23796220,'reachGoal','call_me');
                    jivo_api.open({start: 'call'});
                }
            });
    },

    showMoreLink    : function(){
        if ($('.show-more-items').length)
            $('.show-more-items').click(function(e){
                e.preventDefault();

                $(this).parent().find('.hidden-items').slideDown('fast');
                $(this).slideUp('fast');
            });
    },

    connectWithLink : function(){
        if ($('.toolbar-bottom__callback-toggle > span').length)
            $('.toolbar-bottom__callback-toggle > span').click(function(){
                $(this).next().toggleClass('toolbar-bottom__callback-dropdown--close');
            });
    },

    addSetItemToBasket : function(_id){
        if (!_id)
            return false;

        SC.ajaxSendPost(
            '/ajax/add2basket.php',
            {
                ID      : _id,
                MODE    : 'CART',
                quantity: $('div.goodsSupply .goodsSupplyTableString[data-id=' + _id + '] .cart__counter-counter').data('value')
            },
            function(data){
                SC.vars.send = true;
                BX.closeWait('', SC.vars.wait);

                if (data['status'] == 'success'){
                    $("#popupAddCart .yourCart").parent().replaceWith(data.cart);
                    $('.goodsSupplyTableString[data-id=' + data.id + ']').find('.goodsAddTitle').fadeIn('fast');
                    setTimeout(function(){
                        $('.goodsSupplyTableString').find('.goodsAddTitle').fadeOut('fast');
                    }, 2000);
                }
            },
            'json'
        );
    },

    setAddToBasketEvent : function(){
        if ($('div.goodsSupply a.btnPlaceOrder').length)
            $('div.goodsSupply a.btnPlaceOrder').unbind('click').bind('click', function(e){
                e.preventDefault();

                SC.addSetItemToBasket($(this).data('id'));
            });

        if ($('div.goodsSupply .cart__counter-plus, div.goodsSupply .cart__counter-minus').length)
            $('div.goodsSupply .cart__counter-plus, div.goodsSupply .cart__counter-minus')
                .unbind('click').bind('click', function(e){
                e.preventDefault();

                _sbbs_items_count   = parseInt($(this).parent().find('.cart__counter-counter').text());

                if ($(this).hasClass('cart__counter-minus')){
                    if (_sbbs_items_count >= 1){
                        _sbbs_items_count -= 1;

                        if (_sbbs_items_count == 0)
                            _sbbs_items_count = 1;
                    }else
                        _sbbs_items_count = 1;
                }else{
                    if (parseInt($(this).parent().find('.cart__counter-counter').data('max')) >= (_sbbs_items_count + 1))
                        _sbbs_items_count += 1;
                    else
                        return false;
                }

                $(this).parent().find('.cart__counter-counter').text(_sbbs_items_count)
                    .data('value', _sbbs_items_count);
            });
    },

    getForms : function (){
        if (!$('div.form_container').length)
            return false;

        $('div.form_container').each(function(index, val){
            if (SC.vars.send)
                SC.getFormContent($(this).data('name'));
            else
                setTimeout('SC.getFormContent(\'' + $(this).data('name') + '\');', 300);
        });
    },

    loadRegionDeliveryPlugin    : function(){
        if ($('a.call-ec-widget').length)
            $('a.call-ec-widget').click(function(e){
                e.preventDefault();

                if (!$('#dcsbl').length){
                    $('body').append(
                        '<script id="dcsbl" src="https://dostavka.sbl.su/api/delivery.js?comp=10,8,80,24&startCt=Москва&startCntr=RU&btnBg=#D24B44&pos=right&btn=no&dopLathing=1&dopInsure=1&autoEnd=1"></script>'
                    );
                    _timer_delivery = setInterval(function(){
                        if ($('#ecCalcLay').length && !$('html').hasClass('ec-lock')){
                            $('#ecCalcLay').fadeIn();
                            $('html').addClass('ec-lock');
                            clearInterval(_timer_delivery);
                        }
                    }, 1000);
                }
            });
    },

	addToCompareListPageViewed: function(){
		var item = $(".page_product_viewed .icon-diff-big[data-id]:not(.compare-added)").first();

		if(item.length){
			CP.addToCompareList(item, 'viewed');
		}
		else{
			SC.mobileMenuClosed();
			$('div.filter__item-button').animate({bottom: -80}, 200);
			TweenMax.fromTo('div.goods__filter', 1, {
				ease: Power4.easeOut,
				left: 0
			}, {
				ease: Power4.easeOut,
				left: -globalWidth
			});

			window.location = '/compare/';

			return false;
		}
    },

	provider_info: function(number, brand){
		if(number.length > 0) {
			SC.ajaxSendPost(
                '/ajax/provider_info.php',
                {
                    number: number,
                    brand: brand,

                },
                function(data){
                    SC.vars.send = true;
                    BX.closeWait('', SC.vars.wait);

					if(data.status == 'success'){
						$('.provider_info').css('display', 'flex');
						var res_prov, tr_all_provider;
						/*
						$.each(data.result, function( index, value ) {
							res_prov = "<p>Артикул <b>" + value.number + "</b> Поставщик - <b>" + value.provider + "</b> Цена поставщика - <b>" + value.price_provider + "</b> Остаток - <b>" + value.remainder + "</b></p>";

							$('.provider_info .min_price').append(res_prov);
						});
						*/
						$.each(data.result_all, function( index, value ) {
							tr_all_provider = ("<tr><td>" + value.name_provider + "</td><td>" + value.number + "</td><td>" + value.price_provider + "</td><td>" + value.presence + "</td><td>" + value.remainder + "</td><td>" + value.relevance + "</td></tr>");

							$(".provider_table tr:last").after(tr_all_provider);
						});

					}
                },
                'json'
            );
		}
    },

    goTo   : function(_ob){
        if (!$(_ob).length)
            return true;
        
        $('html, body').animate({
            scrollTop: $(_ob).offset().top
        }, 500, 'swing');
    },
    
    setGoToEvent : function(){
        if (!$('.goto-star').length)
            return true;
        
        $('.goto-star').click(function(e){
            e.preventDefault();
            
            if ($('.star-one:visible').length)
            SC.goTo($('.star-one:visible'));
        });
    },


    init: function () {
        var sc = SC.Main;

        $(function () {
            sc.setFormEvents();
            sc.setSmallBasketEvents();
            sc.setCatlogMenuEvent();
            sc.setPopupsLinks();
            sc.initSliders();
            sc.initPageEvents();
            sc.initMobileMenuButtons();
            sc.initJivoSite();
            sc.showMoreLink();
            sc.connectWithLink();
            sc.getForms();
            sc.loadRegionDeliveryPlugin();
            sc.setGoToEvent();
        });
        return sc;
    }
};

SC = SC.Main.init();

if (window.frameCacheVars !== undefined) {
	console.log('Страница из кеша');
    BX.addCustomEvent("onFrameDataReceived", runFunction);
} else {
	console.log('Страница БЕЗ кеша');
	runFunction();
}

function runFunction() {
	console.log('Функция иниициализации');
	SC.initSliders();
}

function openRecallPopup(){
	var authPopup = BX.PopupWindowManager.create("RecallPopup", null, {
	 autoHide: true,
	 offsetLeft: 0,
	 offsetTop: 0,
	 overlay : true,
	 draggable: {restrict:true},
	 closeByEsc: true,
	 closeIcon: { right : "12px", top : "10px"},
	 content: '<div style="width:400px;height:400px; text-align: center;"><span style="position:absolute;left:50%; top:50%"><img src="/bitrix/templates/eshop_adapt_yellow/img/wait.gif"/></span></div>',
		events: {
		   onAfterPopupShow: function()
		   {
				 this.setContent(BX("bx_recall_popup_form"));
		   }
	 }
	});

	authPopup.show();
}

function SubmitStatus() {
	var orderId = $('#formStatusZakaza input[name="OrderId"]').val();

	$.ajax({
		url: "/ajax/order_status.php",
		data: { id: orderId },
		success: function(data){

			$('#formStatusZakaza .OrderInfo').html( data );

			$('.orderTitle').html( '№ ' + orderId );
		}
	});
}


function UpdateBasketBars() {

	$.ajax({
		url: window.location.href,
		//data: { id: orderId },
		success: function(data){

			var bottom = $(data).find('.toolbar-bottom__content').html();
			$('.toolbar-bottom__content').html( bottom );

			var topp = $(data).find('.order.column').html();
			$('.order.column').html( topp );

			var bottom_button = $(topp).find('span.tag').html();
			$('.toolbar-bottom__button span.tag').html( bottom_button );

			$('.slider-toolbar').slick({
				slidesToShow: 3,
				slidesToScroll: 1,
				prevArrow: '<div class="toolbar-bottom__slider-prev-arrow"></div>',
				nextArrow: '<div class="toolbar-bottom__slider-next-arrow"></div>',
			});

			$('.toolbar-bottom__slider-slide').hover(
                function () {
                    $('.toolbar-bottom__slider-info[data-id=' + $(this).data('id') + ']').fadeIn('fast');
                    $('.toolbar-bottom__slider-info[data-id!=' + $(this).data('id') + ']').css('display', 'none');
                },
                function(){
                    return true;
                }
            );

            SC.setSmallBasketEvents();
		}
	});
}


function ShowPreload() {
	$('body').addClass('overlay preloader');
}


function HidePreload() {
	$('body').removeClass('overlay preloader');
}

$(document).on('click', 'a.updateBasket', function() {
	var inp = $('form.basket').serialize();

	$(this).html('Подождите...');

	$.ajax({
		url: "/ajax/updateBasketAll.php",
		data: inp,
		success: function(data) {
			location.reload();
		}
	});

	return false;
});


function OS_getDopReviews( start, sku_id ){
    
    $('#OS_getDopReviews').remove();
    
    var request = $.ajax({
        url: "/ajax/getReviewsBySKUID.php",
        type: "get",
        data: {sku_id : sku_id, start : start, dop_reviews : 'Y'},
        dataType: "html"
    });

        request.done(function(msg) {
            $('#tab-otzivy').append(msg);
        });

        request.fail(function(jqXHR, textStatus) {
            $('#tab-otzivy').html( "Request failed: " + textStatus );
        });
}


$(document).on('click', '.add-to-basket', function(e) {
    e.preventDefault();

	//Получаем инфу о товаре
	var productId = $(this).attr('data-id');
	$.ajax({
		type: "POST",
		url: "/ajax/infoProduct.php",
		data: {id: $(this).attr('data-id')},
		dataType: "json",
		success: function(jsonData){
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
				'ecommerce': {
					'currencyCode': 'RUB',
					'add': {
						'products': [{
							'name': jsonData.NAME,       // Обязательно
							'id': productId,              // Обязательно
							'price': jsonData.PRICE,
							'quantity': 1           // количество товарных единиц, добавленных в корзину
						}]
					}
				},
				'goods_id': productId,
				'goods_price': jsonData.PRICE,
				'page_type': 'add_product',	// Обязательно
				'event': 'pixel-mg-event',
				'pixel-mg-event-category': 'Enhanced Ecommerce',
				'pixel-mg-event-action': 'Adding a Product to a Shopping Cart',
				'pixel-mg-event-non-interaction': 'False',
			});
		}
	});


    SC.ajaxSendPost(
        '/ajax/add2basket.php',
        {
            ID: $(this).attr('data-id')
        },
        function(data){
            SC.vars.send = true;
            BX.closeWait('', SC.vars.wait);

            $("body").addClass("overlay69");
            $("html").addClass("overlay");
            $("#popupAddCart").show();

            $("#popupAddCart .info, #itemSet").html( '' );

            if (data.set != undefined && data.set.length){
                $("#popupAddCart").addClass('popupSupply');
                $("#popupAddCart .goodsSupply").removeClass('hide');
                setTimeout('SC.setAddToBasketEvent();', 500);
            }else{
                $("#popupAddCart").removeClass('popupSupply');
                $("#popupAddCart .goodsSupply").addClass('hide');
            }

            if (data['status'] == 'success'){
                $("#popupAddCart .info").html(data.basket);
                if (data.set != undefined)
                    $('#itemSet').html(data.set);

                UpdateBasketBars();
            }else
                $("#popupAddCart .info").html(data.msg);
        },
        'json'
    );
});


$(document).on('click', 'input[name="arrFilter_P1_MAX"], input[name="arrFilter_P1_MIN"]', function() {
	$(this).select();
});

$(document).on("click", "a.one-click", function () {
	$("body").addClass("overlay69");
	$("html").addClass("overlay");
	$(".popupOneClick").show();

	var id = $(this).attr('data-id');

	$('#formOneClick input[name="item_id"]').val( id );

	$("#formOneClick input[name=inputTel]").focus();

	return false;

});

$(document).on('click', '.catalog-menu-popup .back, .catalog-menu-popup .close', function(){
	$(this).closest('.popup-catalog-menu-mobile').hide();
});

$(document).on('click', '.header-submit', function() {

	var q = $(this).parent().find('input').val();
	var url = '/search/';

	window.location = url + "?q=" + q + "&how=r";
});


$(document).on('click', '.filter__title--toggle', function() {
	var collapsed = $(this).children().hasClass('is-collapsed');
	var expanded = $(this).children().hasClass('is-expanded');

	if( collapsed || ( !collapsed && !expanded ) ) {

		$(this).children().removeClass('is-collapsed');
		$(this).children().addClass('is-expanded');

		$('#'+$(this).data('id')).removeClass('is-collapsed');
		$('#'+$(this).data('id')).addClass('is-expanded');
		
		if($('#'+$(this).data('id')+'_dropdown_search').hasClass('over-over'))
		{
			$('#'+$(this).data('id')+'_dropdown_search').show();
		}

	} else {

		$(this).children().removeClass('is-expanded');
		$(this).children().addClass('is-collapsed');

		$('#'+$(this).data('id')).removeClass('is-expanded');
		$('#'+$(this).data('id')).addClass('is-collapsed');
		
		$('#'+$(this).data('id')+'_dropdown_search').hide();
		$('#'+$(this).data('id')+'_dropdown').prop("checked", false);

	}

	return false;
});

$(document).on('keyup', '.js-input-from.form-control.filter-fast-search-input', function() {
	$('.filter-fast-search-checkbox-'+$(this).data('key')).each(function(i, elem) {
		var search = $('#fs'+$(this).data('key')).val();
		console.log('Ищем ' + search);
		var label = ''+$(this).data('fast-search');
		console.log('Текущие ' + label);
		if(-1 != label.toLowerCase().indexOf(search.toLowerCase()))
		{
			$(this).show();
		}
		else
		{
			$(this).hide();
		}
	});
	return false;
});


$(document).on('click', '.description__item.etc a', function() {
	var _href = $(this).attr("href");

    $("html, body").animate({scrollTop: $(_href).offset().top+"px"}, 1000);

	return false;
});

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

$(document).ready(function () {
	$('#full-order-form').sisyphus();
	
	$('iframe[src*="youtube"]').parent().fitVids();
	$('label[for="open-modal-region"]').click(function() {
		$('.modal-content').show();
		//$('#open-modal-region').next().find('.modal-content').show();
	});

	var count = $('.order .tag').html();

	$('.toolbar-bottom__button .tag').html( count );

	$(".provider_href").on("click", function(){
		window.location.href = $(".donor_href a").attr("href");
	});

	$(".checkbox_list").on("change", function() {
        if ($(this).is(':checked')){
			$('.toggle-button-cover_list .short').addClass('not_active');
			$('.toggle-button-cover_list .detail').removeClass('not_active');
			$('body').addClass('short-desc');
			$.cookie('short-desc', 'yes', {
                path    : '/'
            });
		}
		else {
			$('.toggle-button-cover_list .short').removeClass('not_active');
			$('.toggle-button-cover_list .detail').addClass('not_active');
			$('body').removeClass('short-desc');
			$.cookie('short-desc', 'no', {
                path    : '/'
            });
		}
    });

	/*
	$('.viewed_list_title').click(function(){
		if ($(this).hasClass('click1')) {
			$('.viewed_mobile').hide();
		} else {
			$('.viewed_mobile').show();
		}

		$(this).toggleClass('click1');
		return false;
	});
	*/
});


$(document).ready(function () {
  var swiper = new Swiper('.promo .swiper-container', {
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      navigation: {
        nextEl: '.promo-right',
        prevEl: '.promo-left'
      },
      renderBullet: function (index, className) {
        return '<span class="' + className + '">' + '</span>';
        // return '<span class="' + className + '">' + (index + 1) + '</span>';
      },
    }
  });

  var swiperTabs = new Swiper('.carousel-tabs .swiper-container-hit', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: false,
    observer: true,
    observeParents: true,
    navigation: {
      prevEl: '.product-tabs-hit-left',
      nextEl: '.product-tabs-hit-right',
    },
    breakpoints: {
      1200: {
        slidesPerView: 3
      },
      768: {
        spaceBetween: 0,
        slidesPerView: 3
      },
      550: {
        spaceBetween: 0,
        slidesPerView: 2
      }
    }
  });

   var swiperTabs = new Swiper('.carousel-tabs .swiper-container-hit', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: false,
    observer: true,
    observeParents: true,
    navigation: {
      prevEl: '.product-tabs-hit-left',
      nextEl: '.product-tabs-hit-right',
    },
    breakpoints: {
      1200: {
        slidesPerView: 3
      },
      768: {
        spaceBetween: 0,
        slidesPerView: 3
      },
      550: {
        spaceBetween: 0,
        slidesPerView: 2
      }
    }
  });

  var swiperTabs = new Swiper('.carousel-tabs .swiper-container-sell', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: false,
    observer: true,
    observeParents: true,
    navigation: {
      prevEl: '.product-tabs-sell-left',
      nextEl: '.product-tabs-sell-right',
    },
    breakpoints: {
      1200: {
        slidesPerView: 3
      },
      768: {
        spaceBetween: 0,
        slidesPerView: 3
      },
      550: {
        spaceBetween: 0,
        slidesPerView: 2
      }
    }
  });

  var swiperTabs = new Swiper('.carousel-showroom .swiper-container', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: false,
    // observer: true,
    // observeParents: true,
    navigation: {
      prevEl: '.product-showroom-left',
      nextEl: '.product-showroom-right',
    },
    breakpoints: {
      1200: {
        slidesPerView: 3
      },
      768: {
        spaceBetween: 0,
        slidesPerView: 3
      },
      550: {
        spaceBetween: 0,
        slidesPerView: 2
      }
    }
  });

  var swiperVideos = new Swiper('.main-videos .swiper-container', {
    slidesPerView: 2,
    spaceBetween: 50,
    loop: false,
    navigation: {
      prevEl: '.main-videos-left',
      nextEl: '.main-videos-right',
    },
    breakpoints: {
      768: {
        slidesPerView: 1
      }
    }
  });

  var swiperVideos = new Swiper('.toolbar-bottom__slider .swiper-container', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: false,
    navigation: {
      prevEl: '.main-videos-left',
      nextEl: '.main-videos-right',
    },
    breakpoints: {
      768: {
        slidesPerView: 1
      }
    }
  });

  var swiperReviews = new Swiper('.goods__review-swiper', {
    slidesPerView: 1,
    loop: false,
    navigation: {
      prevEl: '.product-new-filter-left',
      nextEl: '.product-new-filter-right',
    },
    // spaceBetween: 50,
  });

  // document.onkeydown = function (evt) {
  //   evt = evt || window.event;
  //   var isEscape = false;
  //   if ("key" in evt) {
  //     isEscape = (evt.key == "Escape" || evt.key == "Esc");
  //   } else {
  //     isEscape = (evt.keyCode == 27);
  //   }
  //   if (isEscape) {
  //     document.getElementsByTagName('HTML')[0].classList.toggle('debug');
  //   }
  // };

  var swiperPartner = new Swiper('.partner-carousel .swiper-container', {});

  function addProgressBar(page) {
    page = page === -1 ? 0 : page
    var elems = $(".owl-dot")

    if (elems.length === 0) return

    var target = elems[page]

    target.innerHTML = '<span><div class="skill12"></div></span>'

    var bar = new ProgressBar.Circle($(target).find('.skill12')[0], {
      opacity: 0.5,
      color: "rgb(255,255,255)",
      trailColor: 'rgba(255,255,255,0.5)',
      from: {
        color: 'rgba(255,255,255,0.5)',
        width: 10
      },
      to: {
        color: 'rgba(255,255,255,0.5)',
        width: 10
      },
      // This has to be the same size as the maximum width to
      // prevent clipping
      strokeWidth: 10,
      rtl: false,
      trailWidth: 10,
      easing: 'easeInOut',
      duration: 5000,
      text: {
        autoStyleContainer: false
      },
      from: {
        color: '#fff ',
        width: 10
      },
      to: {
        color: '#fff',
        width: 10
      },
      // Set default step function for all animate calls
      step: function (state, circle) {
        circle.path.setAttribute('stroke', state.color);
        circle.path.setAttribute('stroke-width', state.width);

        var value = Math.round(circle.value() * 100);
        if (value === 0) {
          circle.setText('');
        } else {
          circle.setText(value);
        }

      }
    });
    bar.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
    bar.text.style.fontSize = '0';

    bar.animate(1.0) // Number from 0.0 to 1.0
    // console.log(elem);
  }


  var owl = $('.owl-carousel__progressbar').owlCarousel({
    loop: true,
    smartSpeed: 700,
    nav: true,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 1
      },
      600: {
        items: 1
      },
      1000: {
        items: 1
      }
    },
    onInitialized: function (event) {
      // console.log(event)
      addProgressBar(event.page.index)
    },
    onChanged: function (event) {
      // console.log(event)
      addProgressBar(event.page.index)
    }
  });

  $('.play').on('click', function () {
    owl.trigger('play.owl.autoplay', [1000])
  });
  $('.stop').on('click', function () {
    owl.trigger('stop.owl.autoplay')
  });


        var hash = window.location.hash;
        if(hash && hash.indexOf('#click-') == 0)
        {
	        $(hash).trigger('click');
	        console.log(hash);
        }
});


$(function() {
    $('.dropdown-items .tab-content-button').each(function(i, n) {
            var self = $(this);
            self.on("click", function(e) {
                e.preventDefault();
                var parent = self.parent(),
                    next = self.next();
                if (next.hasClass('active')) {
                    next.slideUp().removeClass('active');
                    self.find('.close-it-parent').removeClass('clicked').html('+');
                } else {
                    if (parent.find('.active').length) {
                        parent.find('.active').prev().find('.close-it-parent').removeClass('clicked').html('+');
                        parent.find('.active').slideUp().removeClass('active');
                    }
                    next.slideDown().addClass('active');
                    self.find('.close-it-parent').addClass('clicked').html('-');
                }
            });
        });
});

function tabShow()
{

	$('.carousel.carousel-tabs.goods__carousel-tabs').each(function(index, block)
	{
		var global_width = $('.container.goods__container').width();
		var all_width = 0;
		var blockId = $(block).attr('id');
		$('#'+blockId+' .tabs__header .tabs__header--title').each(function(index, item)
		{
			all_width += $(item).width() + 45;
			if(all_width < global_width)
			{
				$(item).removeClass('hide-not-mobile');
			}
			else
			{
				$(item).addClass('hide-not-mobile');
			}
		});
	});
}

  function tabWidth() {
    var tabWidth = $('.tabs__header--title.active').width();
    $('.js-tabs-underline').css('width', tabWidth + 'px');
  };

  $('.js-tabs-title').on('click', function () {
    var openTab = $(this).data('tab'),
      linePosition = $(this).position().left;
    $('.js-tabs-underline').css('transform', 'translateX(' + linePosition + 'px)');
    $('.js-tabs-title').removeClass('active');
    $(this).addClass('active');
    $('.js-tabs-content').removeClass('active');
    $(openTab).addClass('active');
    tabWidth();
  });

$(document).ready(function () {
	tabShow();
    tabWidth();
	
	setTimeout(function () {
		$('.dev-main-tabs .js-tabs-content').each(function(index, block){
			if(index > 0){
				$(block).removeClass('active');
			}
		});
	}, 500);
	
});

$(window).resize(function() {
    tabShow();
    tabWidth();
});

var customSwiper = [];
(function(window) {
    if (!!window.JSCatalogSectionCarousel) return;

    window.JSCatalogSectionCarousel = function(arParams) {
        this.carouselSelector = null;
        this.leftArrowSelector = null;
        this.rightArrowSelector = null;
        this.showCount = null;

        this.swiper = null;

        if (typeof arParams === "object") {
            this.carouselSelector = arParams.carouselSelector;
            this.leftArrowSelector = arParams.leftArrowSelector;
            this.rightArrowSelector = arParams.rightArrowSelector;
            this.showCount = arParams.showCount;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogSectionCarousel.prototype.Init = function() {
        this.CreateSwiper();
    };

    window.JSCatalogSectionCarousel.prototype.CreateSwiper = function() {
        customSwiper[this.carouselSelector] = new Swiper(this.carouselSelector, {
            slidesPerView: this.showCount,
			slidesPerGroup: this.showCount,
            spaceBetween: 30,
            loop: false,
			speed: 600,
            navigation: {
                prevEl: this.leftArrowSelector,
                nextEl: this.rightArrowSelector,
            },
            breakpoints: {
                768: {
                    spaceBetween: 0,
                    slidesPerView: 3,
					slidesPerGroup: 3,
                },
                550: {
                    spaceBetween: 0,
                    slidesPerView: 1,
					slidesPerGroup: 1,
                }
            }
        });
    };
})(window);

$(document).ready(function () {
	
    setTimeout(function(){
      $(".our_shops").load("/ajax/our_shops.php");
    }, 1000);
	//Подгрузка ссылок на другие сайты
	$(".other_link_header_pack").load("/ajax/other_link_header_pack.php");
	$(".other_link_header_install").load("/ajax/other_link_header_install.php");
	$(".other_link_footer").load("/ajax/other_link_footer.php");
	
	
	$('.custom-ajax-carousel-preload').on('click', function () {

		var tab = $(this).data('tab');
		var filter_key = $(this).data('filter-key');
		var skip = $(this).data('skip');
		var id = $(this).data('id');
		var stop = $(this).data('stop');
		var pp = $(this).data('pp');
		var section = $(this).data('section');
		var element = $(this).data('element');
		var type = $(this).data('type');
		console.log(filter_key);
		if(!stop)
		{
            let obj=this;
            $(obj).attr('disabled', true);

			BX.showWait();
			$.ajax({
				url: "/ajax/swiper.php",
				data: {
					tab: tab,
					filter_key: filter_key,
					skip: skip,
					id: id,
					pp: pp,
					section: section,
					element: element,
					type: type,
				},
				success: function(data)
				{
                    $(obj).attr('disabled', false);

					var btn = $('#right_arrow_'+data.id);
					if(data.skip)
					{
						btn.data('skip', data.skip);
					}
					if(data.success == 'stop')
					{
						btn.data('stop', 'stop');
					}
					if(data.slides)
					{
						customSwiper['#'+id].appendSlide(data.slides);
                        $.ajax({
                            type: "POST",
                            url: '/local/templates/.default/include/express_delivery.php',
                            data: {}
                        }).done(function( msg ) {
                            $(".tag.expressBtn .descr[data-loaded]").append(msg);
                            $(".tag.expressBtn .descr[data-loaded]").removeAttr('data-loaded');
                        });
                        $(".podarok_list[data-key]").each(function() {
                            $.ajax({
                                type: "POST",
                                url: '/ajax/podarok_list.php',
                                data: {id: $(this).data('id'), key: $(this).data('key')}
                            }).done(function( msg ) {
                                var data = this.data.split("&");
                                $(".podarok_list[data-"+data[1]+"]").append(msg);
                                $(".podarok_list[data-"+data[1]+"]").removeAttr('data-key').removeAttr('data-id');
                            });
                        });                        
					}
					BX.closeWait();
				}
			});
		}
	});
});

function buyRasrochka(data_products, event, mode) {
	
	var content = null;
	
	//Получаем HTML формы
	$.ajax({
		url: '/local/templates/.default/ajax/rasrochka_form.php', 
		method: "GET",
		data: {products: data_products, mode: mode},
		async: false,
		success: function (data) {
			content = data;
		}
	});
	
	//Формируем модальное окно
	var rasrochka_form = BX.PopupWindowManager.create("popup-rasrochka", null, {
		content: content,
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 0,
		overlay : true,
		draggable: {restrict:true},
		closeByEsc: true,
		closeIcon: { right : "10px", top : "10px"},
	});
	
	close = BX.findChildren(BX("popup-rasrochka"), {className: "popup-window-close-icon"}, true);
	if(!!close && 0 < close.length) {
		for(i = 0; i < close.length; i++) {					
			close[i].innerHTML = "<img src='/local/templates/.default/images/close.png'>";
		}
	}	
	
	//Показываем модальное окно
	rasrochka_form.show();
	
	$('[name = customerPhone]').mask("+7 (999) 999-99-99");
	
	event.preventDefault();
	
}

function validRasrochka(event){
	var pattern_mail = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,6}\.)?[a-z]{2,6}$/i;
	var error = 0;
	var this_form = $('.rasrochka_form_tag');
	var form_data = {};
	
	this_form.find ('input').each(function() {
		form_data[this.name] = $(this).val();
	});
	
	if (form_data['customerEmail'] == '' && form_data['customerEmail'].match(pattern_mail)) {
		this_form.find('[name = customerEmail]').addClass('error');
		error = 1;
	} else {
		this_form.find('[name = customerEmail]').removeClass('error');
	}
	if (form_data['customerPhone'] == '') {
		this_form.find('[name = customerPhone]').addClass('error');
		error = 1;
	} else {
		this_form.find('[name = customerPhone]').removeClass('error');
	}
	
	if (error) {
		event.preventDefault();
	} else {	
		
		//Создаем заказ на сайте
		$.ajax({
			url: '/ajax/addOrderSales.php',
			type: 'post',
			data: (form_data),
			success: function(data) {
				var order = JSON.parse(data);
			}
		});
		
		$('.showOnSubmit').show();
		$('.hideOnSubmit').hide();
        ym(23796220,'reachGoal','rassrochka');
	}

	
}
$(document).ready(function () {
    $('.close_pop').click(function(){
        
                $('.button__open-menu').toggleClass('button__open-menu--white');
                $('.icon-burger').toggleClass('icon-burger--cross');
                $('.catalog-menu-popup').toggleClass('catalog-menu-popup--show');
    });

});

$(function() {
    $("body").on("mouseenter",'.tag.is-gift', function () {
        let elem=this;
        let pid=$(this).data('pid');

        if($(elem).data("loaded")!=true)
        {
            $(elem).find('.descr').hide();
            $.ajax({
                type: "GET",
                url: '/local/templates/.default/ajax/gift.php',
                data: {'pid':pid}
            }).done(function(respHtml) {
                $(elem).find('.descr').replaceWith(respHtml);
                $(elem).data("loaded",true);         
            }); 
        }
    })
});