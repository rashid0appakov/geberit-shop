function customDeliveryClick()
{
	var price = $('#delivery-and-payment').data('price');
	var loc = $('#ajax-input-city').data('id');
	if(price)
	{
		if($('#delivery-and-payment') && $('#delivery-and-payment').hasClass('active'))
		{
			$('#delivery-and-payment').html('');
        	$.ajax({
				url: '/ajax/delivery-and-payment.php?price='+price+'&loc='+loc,
				success: function(data){
					$('#delivery-and-payment').html(data);
				}
			});
		}
	}
}

$(document).ready(function () {
	
	$('#delivery-and-payment-click').on('click', function () {
		customDeliveryClick();
	});
	
});

(function(window) {
    if (!!window.JSGeolocationSelectPopup) return;

    window.JSGeolocationSelectPopup = function(arParams) {
        this.params = null;
        this.inputSelector = null;
        this.containerSelector = null;
        this.ajaxUrl = null;
        this.storeCity = null;
        this.deliveryCity = null;
        this.images = null;

        this.timeoutTime = 300;

        this.timeout = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.inputSelector = arParams.inputSelector;
            this.containerSelector = arParams.containerSelector;
            this.ajaxUrl = arParams.ajaxUrl;
            this.storeCity = arParams.storeCity;
            this.deliveryCity = arParams.deliveryCity;
            this.images = arParams.images;
            this.defaultCity = arParams.defaultCity;
        }

        $($.proxy(this.Init, this));
    };

    window.JSGeolocationSelectPopup.prototype.GetInput = function() {
        return $(this.inputSelector);
    };

    window.JSGeolocationSelectPopup.prototype.GetContainer = function() {
        return $(this.containerSelector);
    };

    window.JSGeolocationSelectPopup.prototype.Init = function() {
        this.GetInput().on("input", $.proxy(this.OnInputChanged, this));
        this.SetDefaultCity();
    };


    window.JSGeolocationSelectPopup.prototype.OnInputChanged = function() {
        if (!!this.timeout) {
            window.clearTimeout(this.timeout);
        }

        this.timeout = window.setTimeout($.proxy(this.OnInputTimeout, this), this.timeoutTime);
    };

    window.JSGeolocationSelectPopup.prototype.OnInputTimeout = function() {
        var text = this.GetInput().val();

        if (text.length >= 3) {
            this.SendSearchAjax(text);
        }
    };

    window.JSGeolocationSelectPopup.prototype.SendSearchAjax = function(text) {
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: {
                action: "search",
                params: this.params,
                sessid: BX.bitrix_sessid(),
                text: text
            },
            success: $.proxy(this.OnSearchAjaxSuccess, this),
            error: $.proxy(this.OnSearchAjaxError, this)
        });
    };

    window.JSGeolocationSelectPopup.prototype.OnSearchAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            this.SetItems(data.items);
        }
    };

    window.JSGeolocationSelectPopup.prototype.OnSearchAjaxError = function(jqXHR, textStatus, errorThrown) {

    };

    window.JSGeolocationSelectPopup.prototype.SetItems = function(items) {
        var container = this.GetContainer();
        container.children().remove();

        if (items != undefined)
            items.forEach($.proxy(this.AddItem, this));
    };

    window.JSGeolocationSelectPopup.prototype.AddItem = function(item) {
        var container = this.GetContainer();
        var span = $("<span/>");
        container.append(span);
        span.addClass("city");

        var isStore = this.storeCity.indexOf(item.ID) !== -1;
        var isDelivery = this.deliveryCity.indexOf(item.ID) !== -1;

        if (isStore || isDelivery) span.addClass("mark");

        var a = $("<a/>");
        span.append(a);
        
        if($('#ajax-input-city').data('id')!=item.ID&&item.ID==129){
            a.attr("href",'https://geberit-shop.ru');
        }
        if($('#ajax-input-city').data('id')!=item.ID&&item.ID==817){
            a.attr("href",'https://spb.geberit-shop.ru');
        }
        if($('#ajax-input-city').data('id')!=item.ID&&item.ID==2201){
            a.attr("href",'https://ekb.geberit-shop.ru');
        }
        if($('#ajax-input-city').data('id')!=item.ID&&item.ID==2622){
            a.attr("href",'https://novosibirsk.geberit-shop.ru');
        }
        if($('#ajax-input-city').data('id')!=item.ID&&item.ID==1095){
            a.attr("href",'https://krasnodar.geberit-shop.ru');
        }
        a.text(item.NAME);
        a.attr("title", item.PATH);
        a.attr("data-id", item.ID);
        a.on("click", $.proxy(this.OnItemClick, this));

        if (isStore) {
            var img = $("<img/>");
            span.append(" ", img);
            img.attr("src", this.images.store)
        }

        if (isDelivery) {
            var img = $("<img/>");
            span.append(" ", img);
            img.attr("src", this.images.delivery)
        }
    };

    window.JSGeolocationSelectPopup.prototype.OnItemClick = function(event) {
		event.preventDefault();
        var element = $(event.target);

        var id = element.attr("data-id");
        this.SendSelectAjax(id);
    };

    window.JSGeolocationSelectPopup.prototype.SendSelectAjax = function(id) {
		ShowPreload();
        $.ajax({
            url: this.ajaxUrl,
            method: "POST",
            dataType: "json",
            data: {
                action: "select",
                params: this.params,
                sessid: BX.bitrix_sessid(),
                id: id
            },
            success: $.proxy(this.OnSelectAjaxSuccess, this),
            error: $.proxy(this.OnSelectAjaxError, this)
        });
    };

    window.JSGeolocationSelectPopup.prototype.OnSelectAjaxSuccess = function(data, textStatus, jqXHR) {
        if (!data.error) {
            window.location.reload(false);
			return '';
        	$('#ajax-input-city').text(data.locationData.CITY_NAME);
        	$('#ajax-input-region').text(data.locationData.REGION_NAME);
            $('#location_id').val(data.locationData.ID);

			var time = new Date().getTime();
			var url = window.location.pathname+'?ncc&rand='+time;
			$.get(url, function (html)
			{
				$(html).find('.custom-live-ajax-update').each(function()
				{
					var id = $(this).attr('id');
					$('#'+id).html($(this).html());
					console.log('Update Block ID '+id);
				});
				var json = $(html).find("#delivery_container-data").data('encode-json');
				if(json)
				{
					$("#delivery_container").html(JSON.parse(atob(json)));
				}
	        	$('#close-modal-region').trigger('click');
	        	$('#ajax-input-city').data('id', $('#location_id').val());
	        	customDeliveryClick();
			});
            if (typeof SOA == 'object')
                SOA.getDeliveryServices();
        }
    };

    window.JSGeolocationSelectPopup.prototype.OnSelectAjaxError = function(jqXHR, textStatus, errorThrown) {
    };

    window.JSGeolocationSelectPopup.prototype.SetDefaultCity = function() {
        this.SetItems(this.defaultCity);
    };

})(window);