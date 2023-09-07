$( document ).ready(function() {
  $('.slideshow .slideshowBig').slick({
	  slidesToShow: 1,
	  slidesToScroll: 1,
	  arrows: true,
	  asNavFor: '.slideshowMini'
	});
  $('.slideshow .slideshowMini').slick({
	  slidesToShow: 3,
	  slidesToScroll: 1,
	  asNavFor: '.slideshowBig',
	  dots: false,
	  arrows: false,
	  focusOnSelect: true,
	  centerMode: true,
	  infinite: true,
	  centerPadding: '0px',
	  draggable: false,
	  centerMode: true
	});
});

