$(document).ready(function () {
	$('.goods__tabs-button').click(function(){
		$(this).next().toggleClass('tabs__header--show');
		$(this).toggleClass('goods__tabs-button--close');
	});

    var tabWidth = function () {
        var tabWidth = $('.tabs__header--title.active').width();
        $('.js-tabs-underline').css('width', tabWidth + 'px');
    }
    /*
        This function set auto width for underline element in tabs
        This function call on .tabs__header--title class
        And may init in template js file
     */
    tabWidth();
});