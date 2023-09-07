$(document).ready(function() {
    $('#oneclickOrderForm input').on('input', function(e) {
        $(this).css('border', '1px solid #e1e1e1');
    });

    $('#oneclickOrderForm input[type="submit"]').on('click', function(e) {
        e.preventDefault();
        if (validateForm()) {
            var phone = 7 + $('#oneclickOrderForm .shortNum input').val() + $('#oneclickOrderForm .longNum input').val();
            $('#oneclickOrderForm input[name="ORDER_PROP_3"]').val(phone);

            ShowPreload();
            setTimeout(function(){
                $.ajax({
                    type: 'POST',
                    data: $('#oneclickOrderForm').serialize(),
                    success: function(res) {
                        var orderId = res.order.ID;
                        $.ajax({
                            type: "POST",
                            url: "/ajax/infoOrder.php",
                            data: {id: orderId},
                            dataType: "json",
                            success: function(jsonData){
                                window.dataLayer = window.dataLayer || [];
                                dataLayer.push({
                                 'ecommerce': {
                                   'currencyCode': 'RUB',
                                   'purchase': {
                                     'actionField': {
                                       'id': orderId,
                                       'revenue': jsonData.sum,
                                     },
                                     'products': jsonData.products
                                   }
                                 },
                                 'goods_id': jsonData.good_ids,
                                 'goods_price': jsonData.sum,
                                 'page_type': 'purchase',
                                 'event': 'pixel-mg-event',
                                 'pixel-mg-event-category': 'Enhanced Ecommerce',
                                 'pixel-mg-event-action': 'Purchase',
                                 'pixel-mg-event-non-interaction': 'False'
                                });
                            }
                        });

                        if (res['order'])
                            window.location.href = "/personal/order/?ORDER_ID=" + res.order.ID;
                    },
                    dataType	: 'json'
                })},
            1000);
        }
    });

    var validateForm = function() {
        var valid = true;
        if ($('#oneclickOrderForm .shortNum input').val().length < 3) {
            valid = false;
            $('#oneclickOrderForm .shortNum input').css('border', '1px red solid');
        }
        if ($('#oneclickOrderForm .longNum input').val().length < 7) {
            valid = false;
            $('#oneclickOrderForm .longNum input').css('border', '1px red solid');
        }

        return valid;
    }
});