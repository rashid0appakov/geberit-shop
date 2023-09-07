var scrollTops = [];

$(document).ready(function(){
    if ($('.main-popular-category').length)
        scrollTops[scrollTops.length] = {
            top : $('.main-popular-category').offset().top - ($(window).height() / 2),
            func : function() {
                TweenMax.staggerFromTo('.main-popular-category .main-popular-left', 2,
                    {ease : Power4.easeOut, opacity : 0, x: -300},
                    {ease : Power4.easeOut, opacity : 1, x: 0}, 0.3);
                TweenMax.staggerFromTo('.main-popular-category .main-popular-right', 2,
                    {ease : Power4.easeOut, opacity : 0, x: 300},
                    {ease : Power4.easeOut, opacity : 1, x: 0}, 0.3);
            }
        };


    if ($('.main-videos').length)
        scrollTops[scrollTops.length] = {
            top : $('.main-videos').offset().top - ($(window).height() / 2),
            func : function() {
                TweenMax.staggerFromTo('.main-videos .video', 2,
                    {ease : Power4.easeOut, opacity : 0},
                    {ease : Power4.easeOut, opacity : 1}, 0.3);
            }
        };


    if ($('.main-partners').length)
        scrollTops[scrollTops.length] = {
            top : $('.main-partners').offset().top - ($(window).height() / 2),
            func : function() {
                TweenMax.staggerFromTo('.animate-partner', 1,
                    {ease : Power4.easeOut, opacity : 0},
                    {ease : Power4.easeOut, opacity : 1, delay: 1}, 0.1);
                TweenMax.staggerFromTo('.animate-partner-left', 1,
                    {ease : Power4.easeOut, x: -100, opacity : 0},
                    {ease : Power4.easeOut, x: 0, opacity : 1}, 0.3);
            }
        };
});

$(window).scroll(function() {
    var scroll = $(this).scrollTop();
    $(scrollTops).each(function(i, item) {
        if (scroll > item.top && !item.runed) {
            item.func();
            item.runed = true;
        }
    });
});