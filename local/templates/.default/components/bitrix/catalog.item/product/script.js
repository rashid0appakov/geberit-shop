$(function() {
    $(".product-img-cover").mouseenter(function() {
    //$("body").on("mouseenter", ".product .product__link-img-1 img", function() {
        var hv = $(this).data('hover');
        if(hv.length > 0){
            $('#img'+$(this).data('id')).attr('src',$(this).data('hover'));
        }
    }).mouseleave(function() {
        var hv = $(this).data('hover');
        if(hv.length > 0){
            $('#img'+$(this).data('id')).attr('src',$(this).data('origin'));
        }
    });
	$('.product').each(function(){
		let height = $(this).find('.product__inner').height();
		$(this).attr('style', 'min-height: ' + height + 'px !important');
	})
	//$('.swiper-container .product').hover(function(){
		//let height = $(this).find('.product__inner').height();
		//$(this).attr('style', 'min-height: ' + height + 'px !important');
	//})
});
$(function() {
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
});

/*window.addEventListener('load', function() {
    document.querySelectorAll('.swiper-container').forEach((element) => {
        element.swiper.on('transitionEnd', function(){
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
                	console.log(this.data);
                    var data = this.data.split("&");
                    $(".podarok_list[data-"+data[1]+"]").append(msg);
                    $(".podarok_list[data-"+data[1]+"]").removeAttr('data-key').removeAttr('data-id');
                });
            });
        });
    });
}, false);
*/
/*(function(window, $) {
    if (!!window.JSCatalogItemProduct) return;

    window.JSCatalogItemProduct = function(params) {
        if (typeof params === 'object') {
            this.uid = params.uid;
            this.productInfo = params.productInfo;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogItemProduct.prototype.Init = function() {
        $("#buy_"+this.uid).on("click", $.proxy(this.OnAdd2BasketClicked, this));
    };

    window.JSCatalogItemProduct.prototype.OnAdd2BasketClicked = function() {
        $(this.basketBtnSelector).attr("disabled", "disabled");

        if (!!this.productInfo.available) {
            window.BasketManager.Add2Basket(
                {
                    productId: this.productInfo.id,
                    quantity: 1,
                }, 
                $.proxy(
                    function(res) {
                        $(this.basketBtnSelector).removeAttr("disabled");
                    }, 
                    this
                )
            );
        } else {
            window.BasketManager.ReportAppearance({
                PRODUCT_ID: this.productInfo.id
            });
            $(this.basketBtnSelector).removeAttr("disabled");
        }

    };

})(window, window.jQuery);
*/