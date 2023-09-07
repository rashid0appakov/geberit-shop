$(document).ready(function() {
    $('#orderFullPrice').html($('#basket-full-total').html());

    $('.cart__counter-minus').on('click', function(event){
        event.preventDefault();
        var basketId = $(this).parents('.trCart').data('id');
        var quantity  = parseInt($('#basket-item-' + basketId + ' .cart__content-counter .cart__counter-counter').html()) - 1;

        window.BasketManager.ChangeQuantity(
            {
                basketId: basketId,
                quantity: quantity,
            },
            $.proxy(function(res) {
                window.location.reload();
                updateBasket(res);
            }, this)
        );
    });

    $('.cart__counter-plus').on('click', function(event){
        event.preventDefault();
        var basketId = $(this).parents('.trCart').data('id');
        var quantity  = parseInt($('#basket-item-' + basketId + ' .cart__content-counter .cart__counter-counter').html()) + 1;

        window.BasketManager.ChangeQuantity(
            {
                basketId: basketId,
                quantity: quantity,
            },
            $.proxy(function(res) {
                window.location.reload();
                updateBasket(res);
            }, this)
        );
    });

    $('#delCupon').on('click', function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/local/templates/.default/ajax/basket/processCoupon.php',
            data: {
                clear: true,
                sessid: BX.bitrix_sessid(),
            },
            success: function(res) {
                window.location.reload();
            }
        });
    });

    $('.basket-item-action-remove').on('click', function(event){
        event.preventDefault();
        var basketId = $(this).parents('.trCart').data('id');
        SC.vars.wait = BX.showWait('');
        window.BasketManager.DeleteFromBasket(
            {
                basketId: basketId
            },
            $.proxy(function(res) {

                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                 'ecommerce': {
                   'currencyCode': 'RUB',	// Обязательно
                   'remove': {
                     'products': [{
                       'name': res.PRODUCT.NAME,	// Обязательно
                       'id': res.PRODUCT.ID,	// Обязательно
                       'price': res.PRODUCT.DISCOUNT_PRICE,
                       'quantity': 1	// количество товарных единиц, удаленных из корзины
                     }]
                   },
                 },
                 'event': 'pixel-mg-event',	// Обязательно
                 'pixel-mg-event-category': 'Enhanced Ecommerce',	// Обязательно
                 'pixel-mg-event-action': 'Removing a Product from a Shopping Cart',	// Обязательно
                 'pixel-mg-event-non-interaction': 'False'	// Обязательно
                });

                $("input[class='choosePodarok']").each(function(_index, _value){
                    var giftid = $(this).val();
                    var gift=$(this).closest('.podarok_busket');
                    var productid = $(this).closest('.listPodarok').data('productid');
                    if(giftid){
                        BX.ajax.loadJSON(
                            "/ajax/checkGift.php",
                            {giftid:giftid,productid:productid},
                            function (response){
                                
                                if(response==false)
                                {
                                    gift.remove();
                                }
                                else
                                {
                                    gift.find('.listPodarok').html("");
                                    $.each( response, function( key, value ) {
                                        var item='<div class="item" data-id="'+key+'"></div>';
                                        var label='<label class="radio__outer"><input type="radio" name="choosePodarok_'+productid+'" class="choosePodarok" value="'+key+'"><span class="checkmark"></span></label>';
                                        var links='<a target="_blank" href="'+value.DETAIL_PAGE_URL+'" class="img"><img src="'+value.PIC+'" alt="'+value.NAME+'"></a>';
                                        links+='<a target="_blank" href="'+value.DETAIL_PAGE_URL+'" class="name">'+value.NAME+'</a>';
                                        gift.find('.listPodarok').append($(item).append(label).append(links));
                                    });
                                }
                            },
                            function (){
                            
                            }
                        );
                    }
                });

                if (!res.BASKET.PRODUCT_COUNT)
                    window.location.reload();

                if (res.BASKET_ID)
                    $('#basket-item-' + res.BASKET_ID).slideUp('fast', function(){$(this).remove();});

                updateBasket(res);
                BX.onCustomEvent('itco:onDelFromBasket', [res]);

                BX.closeWait('', SC.vars.wait);
            }, this)
        );
    });

    var updateBasket = function(data) {
        var fullPrice = data.BASKET.PRICE_FORMATTED;
        var basketCount = data.BASKET.PRODUCT_COUNT;
        var basketId = data.BASKET_ID;
        var productId = data.PRODUCT.ID;
        var productBasePrice = data.PRODUCT.BASE_PRICE;
        var productDiscountPrice = data.PRODUCT.DISCOUNT_PRICE;
        var productQuantity = data.QUANTITY;

        if (data.PRODUCT.FULL_PRICE == 0) {
            $('#basket-item-' + basketId).remove();
        }

        $('#basket-item-' + basketId + ' .cart__content-counter .cart__counter-counter').html(productQuantity);
        $('#basket-full-total').html(fullPrice);
        $('#basket-item-sum-price-' + basketId).html(data.PRODUCT.FULL_PRICE_FORMATTED);
        $('.aside-price').html(fullPrice);
        $('.aside-price-full').html(fullPrice);
        $('.aside-products-count').html(basketCount + ' шт.');
    };

    $(document).on('basketitemquantitychange', function(event, res) {
        window.location.reload();
        updateBasket(res);
    });

    /*$(document).on('basketitemdelete', function(event, res) {
        window.location.reload();
        updateBasket(res);
    });*/

    $('.installation').on('click', function(event) {
        // event.preventDefault();

        var basketId = $(this).data('id');
        var check = 0;
        if ($(this).is(':checked')) {
            check = 1;
        }
        t = $(this);

        $.ajax({
            type: 'POST',
            url: '/local/templates/.default/ajax/basket/checkInstallation.php',
            data: {
                basketId: basketId,
                value: check,
                sessid: BX.bitrix_sessid(),
            },
            success: function(res) {
                t.attr('checked', res.checked);
            }
        });
    });

    $('#applyCouponBtn').on('click', function() {
        var coupon = $('input.couponInput').val();

        $.ajax({
            type: 'POST',
            url: '/local/templates/.default/ajax/basket/processCoupon.php',
            data: {
                apply: true,
                coupon: coupon,
                sessid: BX.bitrix_sessid(),
            },
            success: function(res) {
                window.location.reload();
            }
        });
    });

    $('.empty-cart-btn').on('click', function() {
        $.ajax({
            type: 'POST',
            url: '/local/templates/.default/ajax/basket/emptyBasket.php',
            data: {
                sessid: BX.bitrix_sessid(),
            },
            success: function() {
                window.location.reload();
            }
        });
    });
});