(function(){
    var swiperPartner = new Swiper('.partner-carousel .swiper-container', {});
 var swiper = new Swiper('.swiper-container1', {
 	slidesPerView: 4,
      navigation: {
        nextEl: '.nextSlideLogo',
        prevEl: '.prevSlideLogo',
      },
	    breakpoints: {
	      1200: {
	        slidesPerView: 3
	      },
	      768: {
	        slidesPerView: 3
	      },
	      550: {
	        slidesPerView: 2
	      }
	    }
    });
    var swiperTabs = new Swiper('.partner-carousel1 .swiper-container', {
	    slidesPerView: 4,
	    navigation: {
	      prevEl: '.prevSlideLogo',
	      nextEl: '.nextSlideLogo',
	    },
	    breakpoints: {
	      1200: {
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
})();