	$(document).ready(function() {
		$('.catalog-pagination a').click(function() {
			var url = $(this).attr('href');

			ShowPreload();

			$.ajax({
				url: url,
				type: 'GET',
				//data: data,
				success: function(result) {
					var items = $(result).find('.preview-products').html();
					var url = $(result).find('input[name="current_url"]').val();

					$('.preview-products').eq(0).html(items);

					window.history.pushState(null, null, url);

					HidePreload();
				}
			});

			return false;

		});
	});