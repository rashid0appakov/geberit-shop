$(document).ready(function () {
  // document.onkeydown = function (evt) {
  //   evt = evt || window.event;
  //   var isEscape = false;
  //   if ("key" in evt) {
  //     isEscape = (evt.key == "Escape" || evt.key == "Esc");
  //   } else {
  //     isEscape = (evt.keyCode == 27);
  //   }
  //   if (isEscape) {
  //     document.getElementsByTagName('HTML')[0].classList.toggle('debug');
  //   }
  // };

  /*Tabs 2*/
  function tabWidth2() {
    var tabWidth2 = $('.js-tabs-title2.active').width();
    $('.js-tabs-underline2').css('width', tabWidth2 + 'px');
  };

  $('.js-tabs-title2').on('click', function () {
    var openTab = $(this).data('tab'),
      linePosition2 = $(this).position().left;

    $('.js-tabs-underline2').css('transform', 'translateX(' + linePosition2 + 'px)');
    $('.js-tabs-title2').removeClass('active');
    $(this).addClass('active');
    $('.js-tabs-content2').removeClass('active');
    $(openTab).addClass('active');
    tabWidth2();
  });

  /*Tabs 3*/
  function tabWidth3() {
    var tabWidth3 = $('.js-tabs-title3.active').width();
    $('.js-tabs-underline3').css('width', tabWidth3 + 'px');
  };

  $('.js-tabs-title3').on('click', function () {
    var openTab = $(this).data('tab'),
      linePosition3 = $(this).position().left;

    $('.js-tabs-underline3').css('transform', 'translateX(' + linePosition3 + 'px)');
    $('.js-tabs-title3').removeClass('active');
    $(this).addClass('active');
    $('.js-tabs-content3').removeClass('active');
    $(openTab).addClass('active');
    tabWidth3();
  });

  $('.description__link').on('click', function () {
    var el = $(this);
    var dest = el.attr('href'); // получаем направление
    if (dest !== undefined && dest !== '') { // проверяем существование
      $('html').animate({
          scrollTop: $(dest).offset().top // прокручиваем страницу к требуемому элементу
        }, 1500 // скорость прокрутки
      );
    }
    if($(this).hasClass("description__link-1")){
        $("li[data-tab='#tab-1']").trigger("click");
    } else if($(this).hasClass("description__link-2")){
        $("li[data-tab='#tab-4']").trigger("click");
    }else if($(this).hasClass("description__link-3")){
        $("li[data-tab='#tab-5']").trigger("click");
    }
    return false;
  });

});