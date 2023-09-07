;
"use strict";

var CSection  = CSection || {};

CSection.Main = {
    vars  : {},

    initFilterButtons   : function(){
        if ($('.goods__card-sort-filter').length)
            $('.goods__card-sort-filter').click(function(){
                SC.mobileMenuOpened();
                $('div.filter__item-button').animate({bottom: 0}, 500);
                TweenMax.fromTo('div.goods__filter', 1, {
                    ease: Power4.easeOut,
                    left: -globalWidth
                }, {
                    ease: Power4.easeOut,
                    left: 0
                });
            });

        if ($('.goods__filter-close-button').length)
            $('.goods__filter-close-button').click(function(){
                SC.mobileMenuClosed();
                $('div.filter__item-button').animate({bottom: -80}, 200);
                TweenMax.fromTo('div.goods__filter', 1, {
                    ease: Power4.easeOut,
                    left: 0
                }, {
                    ease: Power4.easeOut,
                    left: -globalWidth
                });
            });
    },


    init    : function(){
         var cs = CSection.Main;

        $(function () {
            cs.initFilterButtons();
        });
        return cs;
    }
}

CSection = CSection.Main.init();

$(document).ready(function(){

    var swiperReviews = new Swiper('.goods__review-swiper', {
      slidesPerView: 1,
      loop: true,
      navigation: {
        prevEl: '.product-new-filter-left',
        nextEl: '.product-new-filter-right',
      },
      // spaceBetween: 50,
    });

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
        //console.log(event)
        addProgressBar(event.page.index)
      },
      onChanged: function(event) {
        //console.log(event)
        addProgressBar(event.page.index)
      }
  });

  $('.play').on('click',function(){
      owl.trigger('play.owl.autoplay',[1000])
  });
  $('.stop').on('click',function(){
      owl.trigger('stop.owl.autoplay')
  });



    /*$('.goods__card-sort-filter-button').unbind('click').bind('click', function(){
        if ($(this).parent().next().css('display') == 'none')
            $(this).parent().next().slideDown('fast');
        else
            $(this).parent().next().slideUp('fast');
    });*/


  $('.js-tabs-title').on('click', function() {
    var openTab = $(this).data('tab'),
        linePosition = $(this).position().left;
    $('.js-tabs-underline').css('transform', 'translateX(' + linePosition + 'px)');
    $('.js-tabs-title').removeClass('active');
    $(this).addClass('active');
    $('.js-tabs-content').removeClass('active');
    $(openTab).addClass('active');
    //tabWidth();
  });

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

    $('.filter__button--clean').on('click',function(){
      $('.filter__content-item').removeClass('filter__content-item--active');
      $('.filter__checkbox').children('input:checked').prop('checked', false);
      $('#filter__min-price').change().val(parseInt(0));
      $('#filter__max-price').change().val(parseInt(30000));
    });
});

(function(window) {
    if (!!window.JSCatalogSectionListCategorySections) return;

    window.JSCatalogSectionListCategorySections = function(arParams) {
        this.params = null;
        this.containerSelector = null;
        this.imageContainerSelector = null;

        if (typeof arParams === "object") {
            this.params = arParams.params;
            this.containerSelector = arParams.containerSelector;
            this.imageContainerSelector = arParams.imageContainerSelector;
        }

        $($.proxy(this.Init, this));
    };

    window.JSCatalogSectionListCategorySections.prototype.GetContainer = function() {
        return $(this.containerSelector);
    };

    window.JSCatalogSectionListCategorySections.prototype.GetItems = function() {
        return this.GetContainer().find(".categoryCardWrapper");
    };

    window.JSCatalogSectionListCategorySections.prototype.Init = function() {
        this.AnimateItems();
    };

    window.JSCatalogSectionListCategorySections.prototype.AnimateItems = function() {
        var items = this.GetItems();
        items.each($.proxy(function(i, item) {
            this.AnimateItem($(item));
        }, this));
    };

    window.JSCatalogSectionListCategorySections.prototype.AnimateItem = function(item) {
        var imageContainer = item.find(".categoryImage");

        var imageWidth = 230;
        var imageCount = imageContainer.find(".categoryImageWrapper").length;
        var containerWidth = imageCount * imageWidth;
        imageContainer.width(containerWidth);

        if (imageCount > 1) {
            item.on("mouseenter", function() {
                imageContainer.animate({
                    right: containerWidth - imageWidth
                }, 2000)
            });
            item.on("mouseleave", function() {
                imageContainer.stop();
            });
        }
    };
})(window);