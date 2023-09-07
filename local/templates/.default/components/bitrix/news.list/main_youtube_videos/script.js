$(function() {
    var swiperVideos = new Swiper('.main-videos .swiper-container', {
        slidesPerView: 2,
        spaceBetween: 50,
        loop: true,
        navigation: {
            prevEl: '.main-videos-left',
            nextEl: '.main-videos-right',
        },
        breakpoints: {
            768: {
                slidesPerView: 1
            }
        }
    });
});
