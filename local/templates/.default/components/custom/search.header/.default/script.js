(function(window, $) {
	if (!!window.JSSearchHeader) return;

	window.JSSearchHeader = function(params) {
		this.timeout = null;
		this.ajaxTimeout = 500;

		if (typeof params === "object") {
			this.uid = params.uid;
			this.ajaxUrl = params.ajaxUrl;
			this.searchUrl = params.searchUrl;
		}

		$($.proxy(this.Init, this));
	};

	window.JSSearchHeader.prototype.Init = function() {
		$("#input_"+this.uid).on("keyup", $.proxy(this.OnInputKeyup, this));
		$(window.document).on("click", $.proxy(this.OnDocumentClick, this));
	};

	window.JSSearchHeader.prototype.OnInputKeyup = function(event) {
		var $input = $("#input_"+this.uid);
		var val = $input.val();

		if (!!this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}

		if (!!val) {
			if (event.keyCode == 13) {
				window.location = this.searchUrl + "?q=" + val + "&how=r";
				return;
			}

			this.timeout = window.setTimeout(
				$.proxy(this.OnAjaxTimeout, this, val),
				this.ajaxTimeout
			);
		}
	};

	window.JSSearchHeader.prototype.OnAjaxTimeout = function(val) {
		this.timeout = null;

		this.SendAjax(val);
	};

	window.JSSearchHeader.prototype.SendAjax = function(val) {
		$.ajax({
			url: this.ajaxUrl,
			data: {
				sessid: BX.bitrix_sessid(),
				q: val,
				section: $('body').data('section')
			},
			type: "POST",
			success: $.proxy(this.OnAjaxSuccess, this),
			error: $.proxy(this.OnAjaxError, this)
		});
	};

	window.JSSearchHeader.prototype.OnAjaxSuccess = function(data, textStatus, jqXHR) {
		var $popup = $("#popup_"+this.uid);

		$popup.children().remove();
		$popup.append(data);

		this.OpenPopup();
	};

	window.JSSearchHeader.prototype.OnAjaxError = function(jqXHR, textStatus, errorThrown) {
		console.error(jqXHR, textStatus, errorThrown);
	};

	window.JSSearchHeader.prototype.OnDocumentClick = function(event) {
		var $search = $("#search_"+this.uid);

		if ($search.has(event.target).length === 0) {
			this.ClosePopup();
		}
	};

	window.JSSearchHeader.prototype.OpenPopup = function() {
		var $popup = $("#popup_"+this.uid);

		$popup.addClass("active");
	};

	window.JSSearchHeader.prototype.ClosePopup = function() {
		var $popup = $("#popup_"+this.uid);

		$popup.removeClass("active");
	};

})(window, window.jQuery);