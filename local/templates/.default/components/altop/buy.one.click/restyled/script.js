if(!window.BX.BocFormSubmit) {
	BX.BocFormSubmit = function() {
		var target = BX.proxy_context,
			popup = BX.findParent(target, {"className" : "pop-up"}),
			form = BX.findParent(target, {"tag" : "form"}),
			alert = BX.findChild(form, {"className": "alert"}, true, false),
			captchaWord = BX.findChild(form, {"attribute": {"name": "CAPTCHA_WORD"}}, true, false),
			captchaImg = BX.findChild(form, {"tagName": "img"}, true, false),
			captchaSid = BX.findChild(form, {"attribute": {"name": "CAPTCHA_SID"}}, true, false),
			formInput,
			formTextarea,
			data = [];

		formInput = BX.findChildren(form, {"tag" : "input"}, true);
		if(!!formInput && 0 < formInput.length) {
			for(i = 0; i < formInput.length; i++) {
				data[formInput[i].getAttribute("name")] = formInput[i].value;
			}
		}

		formTextarea = BX.findChildren(form, {"tag": "textarea"}, true);
		if(!!formTextarea && 0 < formTextarea.length) {
			for(i = 0; i < formTextarea.length; i++) {
				data[formTextarea[i].getAttribute("name")] = formTextarea[i].value;
			}
		}
		
		var wait = BX.showWait(popup);
		BX.ajax({
			url: form.getAttribute("action"),
			data: data,
			method: "POST",
			dataType: "json",
			onsuccess: function(data) {
				if(!!data.success) {
					logServer({log: 'Создался заказ в рассрочку - ' + data.media.order_id});
					addOrderBox(data.media.order_id, 'one');

					//Для рекламщиков
					window.dataLayer = window.dataLayer || [];
					dataLayer.push({
					 'ecommerce': {
					   'currencyCode': 'RUB',
					   'purchase': {
						 'actionField': {
						   'id': data.media.order_id,            // уникальный идентификатор транзакции(обязательно)
						   'affiliation': 'tiptop-shop', // магазин или филиал, в котором была совершена транзакция
						   'revenue': data.media.order_sum,             // полная сумма транзакции, включая стоимость доставки и налог
						   'tax': '',                  // сумма всех налогов для этой транзакции
						   'shipping': '',              // стоимость доставки заказа
						   'coupon': ''           // купон или промокод, который был использован при оформлении заказа
						 },
						 'products': data.media.products
					   }
					 },
					 'goods_id': data.media.order_id,
					 'goods_price': data.media.order_sum,
					 'event': 'pixel-mg-event',
					 'pixel-mg-event-category': 'Enhanced Ecommerce',
					 'pixel-mg-event-action': 'Purchase',
					 'pixel-mg-event-non-interaction': 'False',
					});


					if(!!alert)
						BX.adjust(alert, {html: "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'>" + data.success.text + "</span></span>"});
					BX.adjust(target, {props: {disabled: true}});
				} else if(!!data.error) {
					if(!!alert)
						BX.adjust(alert, {html: "<span class='alertMsg bad'><i class='fa fa-exclamation-triangle'></i><span class='text'>" + data.error.text + "</span></span>"});
					if(!!data.error.captcha_code && data.error.captcha_code != "") {
						if(!!captchaWord)
							captchaWord.value = "";
						if(!!captchaImg)
							BX.adjust(captchaImg, {props: {"src": "/bitrix/tools/captcha.php?captcha_sid=" + data.error.captcha_code}});
						if(!!captchaSid)
							captchaSid.value = data.error.captcha_code;
					}
				}
				BX.closeWait(popup, wait);
			}
		});
	}
}