$(document).ready(function () {
	
	//Группа свойств
	$('.group_props').click(function(){
		
		if ($(this).hasClass('active')) {
			$(this).children('.tabs-table__row').hide();
		} else {
			$(this).children('.tabs-table__row').show();
		}
		
		$(this).toggleClass('active');
	});

    if ($('.slider-for').length)
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav'
        });

    if ($('.slider-nav').length)
        $('.slider-nav').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            // centerMode: true,
            focusOnSelect: true,
            prevArrow: '<div class="slick__prev-arrow"><img src="'+$('.slider-nav').data('previous-image-url')+'" alt="button-left"></div>',
            nextArrow: '<div class="slick__next-arrow"><img src="'+$('.slider-nav').data('next-image-url')+'" alt="button-right"></div>',
            centerPadding: '40px',
            responsive: [{
                breakpoint: 549,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            }]
          });

    document.onkeydown = function (evt) {
      evt = evt || window.event;
      var isEscape = false;
      if ("key" in evt) {
        isEscape = (evt.key == "Escape" || evt.key == "Esc");
      } else {
        isEscape = (evt.keyCode == 27);
      }
      if (isEscape) {
        document.getElementsByTagName('HTML')[0].classList.toggle('debug');
      }
    };

    if ($('.goods__tabs-button').length)
        $('.goods__tabs-button').click(function(){
            $(this).next().toggleClass('tabs__header--show');
            $(this).toggleClass('goods__tabs-button--close');
        });

    if ($('.buy__item.middle-header.callback').length){
        $.ajax({
            url: '/local/templates/.default/ajax/catalogElement_callback.php',
            success: function(res) {
                $('.buy__item.middle-header.callback').append(res);
            }
        });

		$('.js-tabs-title2.active').click();
    }

    if ($('.slider-for > div').length)
		$('.slider-for > div').each(function (index, item) {
		  item.addEventListener('click', function(event) {
              event.preventDefault();
              if ($('body').width() < 745)
                  return false;

			var modal = document.querySelector('#open-more');  // assuming you have only 1
			var html = document.querySelector('html');
			modal.classList.add('is-active');
			html.classList.add('is-clipped');

			modal.querySelector('#open-more .modal-background').addEventListener('click', function(e) {
			  e.preventDefault();
			  modal.classList.remove('is-active');
			  html.classList.remove('is-clipped');
			});
			modal.querySelector('button[aria-label="close"]').addEventListener('click', function(e) {
			  e.preventDefault();
			  modal.classList.remove('is-active');
			  html.classList.remove('is-clipped');
			});
		  });
		});
});