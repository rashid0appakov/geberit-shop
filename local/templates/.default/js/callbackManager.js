(function(window, $){
	if (!!window.JSCallbackManager) return;

	window.JSCallbackManager = function(params) {

		$(window.document).on('click', '.actionChatConsultant', this.chatConsultant);
		$(window.document).on('click', '.actionCallRequest', this.callRequest);
	};

	window.JSCallbackManager.prototype.chatConsultant = function() {
		//alert("Чат консультант вызван!");
	};

	window.JSCallbackManager.prototype.callRequest = function() {
		//alert("Мы перезвоним вам в течение 27 секунд");
	};

})(window, jQuery);