/* ===== Logic for creating fake Select Boxes ===== */
;
"use strict";

var SelBOX  = SelBOX || {};

SelBOX.Main = {
    initSelect  : function(){
        if ($('.sel').length)
            $('.sel').each(function() {
                $(this).children('select').css('display', 'none');

                var $current = $(this);
                var _index = $current.children('select').find('option[value="' + $current.children('select').val() + '"]').index();
                $current.prepend($('<div>', {
                    class: $current.attr('class').replace(/sel/g, 'sel__box')
                }));
                $(this).find('option').each(function(i) {
                    if (i == _index) {
                        var placeholder = $(this).text();
                        $current.prepend($('<span>', {
                            class: $current.attr('class').replace(/sel/g, 'sel__placeholder'),
                            text: placeholder/*,
                            'data-placeholder': placeholder*/
                        }));
                        $current.animate({
                            opacity: 1
                        }, 1000);
                    }

                    $current.find('div.sel__box').append($('<span>', {
                        class: $current.attr('class').replace(/sel/g, 'sel__box__options') + (i == _index ? ' selected' : ''),
                        text: $(this).text(),
                        'data-index'    : i
                    }));
                });
            });
    },

    setActiveMode : function(){
        // Toggling the `.active` state on the `.sel`.
        if ($('.sel').length)
            $('.sel').click(function() {
                $(this).toggleClass('active');
            });
    },

    setActiveOption : function(){
        // Toggling the `.selected` state on the options.
        if ($('.sel__box__options').length)
            $('.sel__box__options').click(function() {
                var $currentSel = $(this).closest('.sel');

                if ($(this).data('index') == $currentSel.children('select').prop('selectedIndex'))
                    return true;

                $(this).siblings('.sel__box__options').removeClass('selected');
                $(this).addClass('selected');

                $currentSel.children('.sel__placeholder').text($(this).text())
                    .attr('data-placeholder', $(this).text());
                $currentSel.children('select').prop('selectedIndex', $(this).data('index'))
                    .val($currentSel.children('select').find('option:eq(' + $(this).data('index') + ')').attr('value'));
                $currentSel.children('select').trigger('change');
            });
    },

    init: function () {
        var sb = SelBOX.Main;

        $(function () {
            sb.initSelect();
            sb.setActiveMode();
            sb.setActiveOption();
        });
        return sb;
    }
};

SelBOX = SelBOX.Main.init();