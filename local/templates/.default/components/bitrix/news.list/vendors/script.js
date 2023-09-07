$(document).ready(function () {
	$('#brand-letters a').click(function(e){
		e.preventDefault();
		var letter = $(this).data('letter');
		$('#brand-letters a').removeClass('active');
		$(this).addClass('active');
		if(letter == '-')
		{
			$('.brand-letters').show();
			
			$('.hidden-brands').hide();
			$('.show-more-brands').show();
		}
		else
		{
			$('.brand-letters').hide();
			$('.brand-letter-'+letter).show();
			
			$('.hidden-brands').show();
			$('.show-more-brands').hide();
		}
	});
	
	$('.show-more-brands').click(function(e){
		e.preventDefault();

		$('.hidden-brands').show('fast');
		$(this).hide('fast');
	});
});