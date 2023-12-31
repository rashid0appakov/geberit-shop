$(document).ready(function () {
    if ($('.fancybox-media').length)
        $('.fancybox-media').fancybox({
                openEffect  : 'none',
                closeEffect : 'none',
                helpers : {
                    media : {}
                }
            });

  $('.navbar-link-sales').on('click', function () {
    $('.all-sales-popup').toggleClass('all-sales-popup--show');
  });
  $(document).mouseup(function (e) {
    var containerMenu = $(".all-sales-popup"),
      buttonPink = $(".navbar-link-sales");
    if (containerMenu.has(e.target).length === 0 && buttonPink.has(e.target).length === 0) {
      containerMenu.removeClass("all-sales-popup--show");
    }
  });

  $('.breadcrumbs__item-dropdown').on('click', function () {
    $('.breadcrumbs__item-dropdown-wrap').not($(this).children()).addClass('breadcrumbs__item-dropdown-wrap--close');
    $(this).children('.breadcrumbs__item-dropdown-wrap').toggleClass('breadcrumbs__item-dropdown-wrap--close');
  });
  $(document).mouseup(function (e) {
    var containerMenu = $(".breadcrumbs__item-dropdown-wrap"),
      buttonPink = $(".breadcrumbs__item-dropdown");
    if (containerMenu.has(e.target).length === 0 && buttonPink.has(e.target).length === 0) {
      containerMenu.addClass("breadcrumbs__item-dropdown-wrap--close");
    }
  });


    $('.sliderLogos').slick({
        prevArrow: $('.prevSlideLogo'),
        nextArrow: $('.nextSlideLogo'),
        infinite: false
    });
    $('.slider-info__content-button').hover(function(){
        $('.slider-info__content-button').toggleClass("hover")
    })
    $('.need-help__title').on('click', function () {
        $(".goods__need-help").toggleClass("active");
    });
//    if (window.location.pathname == '/')
//    {
//        setTimeout(function(){
//            $('.need-help__title').trigger('click');
//        }, 1000);
//    }
    $(function($){
        $("input[type='tel']").mask("+7 (999) 999-99-99");
    });

    $( "#formStatusZakaza" ).validate({
        rules: {
            inputNum: {
                required: true,
                digits: true
            }
        },
        submitHandler: function(form) {
            // $.ajax({
            //     url: form.action,
            //     type: form.method,
            //     data: $(form).serialize(),
            //     success: function() {
            //         $(".hideOnSubmit").addClass("hide");
            //         $(".showOnSubmit").removeClass("hide");
            //     }
            // });
            $(".hideOnSubmit").addClass("hide");
            $(".showOnSubmit").removeClass("hide");
            return false;
        }
    });
    $( ".validateFormLogin" ).validate({
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function() {

                }
            });
            return false;
        }
    });
    $( ".validateFormReg" ).validate({
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function() {

                }
            });
            return false;
        }
    });
    //
    // Popup Callback
    //
    $( ".valForm1" ).validate({
        submitHandler: function(form) {
            // $.ajax({
            //     url: form.action,
            //     type: form.method,
            //     data: $(form).serialize(),
            //     success: function() {
            //         $(".hideOnSubmit").addClass("hide");
            //         $(".showOnSubmit").removeClass("hide");
            //         setTimeout(function () {
            //             $("body").removeClass("overlay69");
            //             $("html").removeClass("overlay");
            //             $(".popup69").hide();
            //         }, 3000)
            //     }
            // });
            $( ".popup69" ).animate({"right":"-10000px"}, "slow");
            setTimeout(function () {
                $("body").removeClass("overlay69");
                $("html").removeClass("overlay");
                $(".popup69").hide();
            }, 250);
            $( ".popup69" ).animate({"right":"0"}, "slow");
            $(".okSendLine").slideDown();
            return false;
        }
    });
    $( ".valForm2" ).validate({
        rules: {
            inputNum: {
                required: true,
                digits: true
            }
        },
        submitHandler: function(form) {
            $( ".popup69" ).animate({"right":"-10000px"}, "slow");
            setTimeout(function () {
                $("body").removeClass("overlay69");
                $("html").removeClass("overlay");
                $(".popup69").hide();
            }, 250);
            $( ".popup69" ).animate({"right":"0"}, "slow");
            $(".okSendLine").slideDown();
            return false;
        }
    });
    $( ".valForm3" ).validate({
        rules: {
            inputNum: {
                required: true,
                digits: true
            }
        },
        submitHandler: function(form) {
            $( ".popup69" ).animate({"right":"-10000px"}, "slow");
            setTimeout(function () {
                $("body").removeClass("overlay69");
                $("html").removeClass("overlay");
                $(".popup69").hide();
            }, 250);
            $( ".popup69" ).animate({"right":"0"}, "slow");
            $(".okSendLine").slideDown();
            return false;
        }
    });
    $( ".valForm4" ).validate({
        submitHandler: function(form) {
            $( ".popup69" ).animate({"right":"-10000px"}, "slow");
            setTimeout(function () {
                $("body").removeClass("overlay69");
                $("html").removeClass("overlay");
                $(".popup69").hide();
            }, 250);
            $( ".popup69" ).animate({"right":"0"}, "slow");
            $(".okSendLine").slideDown();
            return false;
        }
    });
    $( ".valForm5" ).validate({
        submitHandler: function(form) {
            $( ".popup69" ).animate({"right":"-10000px"}, "slow");
            setTimeout(function () {
                $("body").removeClass("overlay69");
                $("html").removeClass("overlay");
                $(".popup69").hide();
            }, 250);
            $( ".popup69" ).animate({"right":"0"}, "slow");
            $(".okSendLine").slideDown();
            return false;
        }
    });
    //
    $('input[type="password"]').passField({
        showGenerate: false,
        showWarn: false,
        showTip: false,
        maskBtn : {
            textMasked : "",
            textUnmasked: "&bull;&bull;&bull;",
            className: "eye",
            classMasked: "eyeOpen",
            classUnmasked: "eyeClose"
        }
    });
    $(document).on("click", ".closeOK", function () {
        $(".okSendLine").slideUp();
    });
    $(".file-upload input[type=file]").change(function(){
        var filename = $(this).val().replace(/.*\\/, "");
        $(this).parent().parent().next().val(filename);
        $("label.inputFileFree").removeClass("inputFileFree");
    });
    if (window.screen.width <= 600) {
        $('.mobileBnrToggle').click(function(){
            if($(this).hasClass('active')){
                $('.topHeaderBnr__inner .right').slideUp();
                $(this).removeClass('active');
            }else{
                $('.topHeaderBnr__inner .right').slideDown();
                $(this).addClass('active');
            }
        });
        $('.topHeaderBnr__inner .left').click(function(){
            if($('.mobileBnrToggle').hasClass('active')){
                $('.topHeaderBnr__inner .right').slideUp();
                $('.mobileBnrToggle').removeClass('active');
            }else{
                $('.topHeaderBnr__inner .right').slideDown();
                $('.mobileBnrToggle').addClass('active');
            }
        });
    }
});

