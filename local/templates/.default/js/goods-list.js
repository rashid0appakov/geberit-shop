$(document).on('click', '.card-cell__show-more', function(e) {
	e.preventDefault();
	var url = $(this).find('a').attr('href');
	url += url.indexOf("?") != -1 ? '&' : '?';
	url += 'IS_AJAX2=Y';
	ShowPreload();
	
	console.log(url);

	$.ajax({
		url: url,
		type: 'GET',
		//data: data,
		success: function(result) {
			var pagination = $(result).find('div.pagination-wrapper').html();
			var items = $(result).find('div.card-cell--all').html();
			var url = $(result).find('input[name="current_url"]').val();

			$('div.pagination-wrapper').html(pagination);
			$('div.card-cell--all').append(items);

			window.history.pushState(null, null, url);

			HidePreload();
            CP.setEventsAddToCompareList();
		}
	});

	return false;
});

$(document).ready(function(){
	$('.goods__title-link').click(function(e){
		e.preventDefault();
		var me = $(this);
		if (me.hasClass('open')) {
			me.removeClass('open');
			$(me).text('Подробнее');
			$('.goods__title-description-full').slideUp();
		}
		else {
			me.addClass('open');
			$(me).text('Скрыть');
			$('.goods__title-description-full').slideDown();
		}
	});
	
	$('.count_catalog_items').on('change', function(){
	  var sort = $(this).data('sort');
	  var order = $(this).data('order');
	  var pp = $(this).val();
	  var uri = window.location.pathname;
	  window.location.href = uri + '?sort=' + sort + '&order=' + order + '&pp=' + pp;
	});

      var swiperTabs = new Swiper('.carousel-news--orange .swiper-container', {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: false,
        // observer: true,
        // observeParents: true,
        navigation: {
          prevEl: '.arrow-left--orange',
          nextEl: '.arrow-right--orange',
        },
        breakpoints: {
          1200: {
            slidesPerView: 3
          },
          1199: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          768: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          550: {
            spaceBetween: 0,
            slidesPerView: 2
          }
        }
      });

      var swiperTabs = new Swiper('.carousel-news--green .swiper-container', {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: false,
        // observer: true,
        // observeParents: true,
        navigation: {
          prevEl: '.arrow-left--green',
          nextEl: '.arrow-right--green',
        },
        breakpoints: {
          1200: {
            slidesPerView: 3
          },
          1199: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          768: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          550: {
            spaceBetween: 0,
            slidesPerView: 2
          }
        }
      });

      var swiperTabs = new Swiper('.carousel-news--pink .swiper-container--pink', {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: false,
        // observer: true,
        // observeParents: true,
        navigation: {
          prevEl: '.arrow-left--pink',
          nextEl: '.arrow-right--pink',
        },
        breakpoints: {
          1200: {
            slidesPerView: 3
          },
          1199: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          768: {
            spaceBetween: 0,
            slidesPerView: 3
          },
          550: {
            spaceBetween: 0,
            slidesPerView: 2
          }
        }
      });

      var swiperReviews = new Swiper('.goods__review-swiper', {
        slidesPerView: 1,
        loop: false,
        navigation: {
          prevEl: '.product-new-filter-left',
          nextEl: '.product-new-filter-right',
        },
        // spaceBetween: 50,
      });

    //   document.onkeydown = function(evt) {
    //     evt = evt || window.event;
    //     var isEscape = false;
    //     if ("key" in evt) {
    //         isEscape = (evt.key == "Escape" || evt.key == "Esc");
    //     } else {
    //         isEscape = (evt.keyCode == 27);
    //     }
    //     if (isEscape) {
    //       document.getElementsByTagName('HTML')[0].classList.toggle('debug');
    //     }
    // };


      // var out = document.getElementsByClassName('owl-dot');
      // for (var i = 0; i < out.length; i++) {
      //   out[i].innerHTML = '<span><div class="skill12"></div></span>';
      // }


function addProgressBar(page) {
  page = page === -1 ? 0 : page
  var elems = $(".owl-dot")

  if (elems.length === 0) return

  var target = elems[page]

  target.innerHTML = '<span><div class="skill12"></div></span>'

  var bar = new ProgressBar.Circle($(target).find('.skill12')[0], {
    opacity: 0.5,  color: "rgb(255,255,255)",
    trailColor: 'rgba(255,255,255,0.5)',
from: { color: 'rgba(255,255,255,0.5)', width: 10 },
to: { color: 'rgba(255,255,255,0.5)', width: 10 },
    // This has to be the same size as the maximum width to
    // prevent clipping
    strokeWidth: 10,
    rtl: false,
    trailWidth: 10,
    easing: 'easeInOut',
    duration: 5000,
    text: {
      autoStyleContainer: false
    },
    from: { color: '#fff ', width: 10 },
    to: { color: '#fff', width: 10 },
    // Set default step function for all animate calls
    step: function(state, circle) {
      circle.path.setAttribute('stroke', state.color);
      circle.path.setAttribute('stroke-width', state.width);

      var value = Math.round(circle.value() * 100);
      if (value === 0) {
        circle.setText('');
      } else {
        circle.setText(value);
      }

    }
  });
  bar.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
  bar.text.style.fontSize = '0';

  bar.animate(1.0) // Number from 0.0 to 1.0
  // console.log(elem);
}

      // $('.skill12').each(function( index, elem ) {

      // });

      // $('.owl-dot').on('click', function() {
      //   var target = $(this).find('.skill12')[0];
      //   addProgressBar(target);
      // });

      // addProgressBar($('.skill12').first()[0]);


    var owl = $('.owl-carousel__progressbar').owlCarousel({
        loop:true,
        smartSpeed: 700,
        nav:true,
        autoplay:true,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        },
        onInitialized: function(event) {
          // console.log(event)
          addProgressBar(event.page.index)
        },
        onChanged: function(event) {
          // console.log(event)
          addProgressBar(event.page.index)
        }
    });

    $('.play').on('click',function(){
        owl.trigger('play.owl.autoplay',[1000])
    });
    $('.stop').on('click',function(){
        owl.trigger('stop.owl.autoplay')
    });


//   $('.button__open-menu').on('click',function(){

//     if ($(window).width() < 551) {
//                 TweenMax.fromTo('#popup-catalog-menu-start-mobile', 1, {ease : Power4.easeOut, left : -globalWidth},
//                                 {ease : Power4.easeOut, left : 0});
//     } else {
//         $(this).toggleClass('button__open-menu--white');
//         $('.icon-burger').toggleClass('icon-burger--cross');
//         $('.catalog-menu-popup').toggleClass('catalog-menu-popup--show');
//     }
// });
//     $(document).mouseup(function (e) {
//     var container = $(".catalog-menu-popup"),
//         burger = $(".button__open-menu");
//     if (container.has(e.target).length === 0 && burger.has(e.target).length === 0) {
//         container.removeClass("catalog-menu-popup--show");
//         $('.button__open-menu').removeClass('button__open-menu--white');
//         $('.icon-burger').removeClass('icon-burger--cross');
//     }
//     });

//     $('.navbar-link').on('click',function(){
//           $('.all-sales-popup').toggleClass('all-sales-popup--show');
//       });
//       $(document).mouseup(function (e) {
//       var containerMenu = $(".all-sales-popup"),
//           buttonPink = $(".navbar-link");
//       if (containerMenu.has(e.target).length === 0 && buttonPink.has(e.target).length === 0) {
//         containerMenu.removeClass("all-sales-popup--show");
//         }
//       });

//     $('#current-region__open-change-region').on('click',function(){
//         $('#current-region__popup').css('display', 'block');
//     });

  $('.goods__review-link').on('click',function(){
      $('.goods__review-description').toggleClass('goods__review-description--show');
      $('.goods__review-link-read').toggleClass('goods__review-link--close');
      $('.goods__review-link-close').toggleClass('goods__review-link--close');
    });
    $(document).mouseup(function (e) {
    var containerMenu = $(".goods__review-description"),
          buttonPink = $(".goods__review-link");
      if (containerMenu.has(e.target).length === 0 && buttonPink.has(e.target).length === 0) {
        containerMenu.removeClass("goods__review-description--show");
        $('.goods__review-link-read').removeClass('goods__review-link--close');
        $('.goods__review-link-close').addClass('goods__review-link--close');
        }
      });

    // $('.breadcrumbs__item-dropdown').on('click',function(){
    //   $('.breadcrumbs__item-dropdown-wrap').toggleClass('breadcrumbs__item-dropdown-wrap--close');
    // });
    // $(document).mouseup(function (e) {
    //   var containerMenu = $(".breadcrumbs__item-dropdown-wrap"),
    //       buttonPink = $(".breadcrumbs__item-dropdown");
    //   if (containerMenu.has(e.target).length === 0 && buttonPink.has(e.target).length === 0) {
    //     containerMenu.addClass("breadcrumbs__item-dropdown-wrap--close");
    //     }
    //   });

      $('.filter__button--clean').on('click',function(){
        $('.filter__content-item').removeClass('filter__content-item--active');
        $('.filter__checkbox').children('input:checked').prop('checked', false);
        $('#filter__min-price').change().val(parseInt(0));
        $('#filter__max-price').change().val(parseInt(30000));
      });

  $(".filter__content-item--toggle").click(function () {
    $(this).toggleClass("filter__content-item--active");
  });


});
