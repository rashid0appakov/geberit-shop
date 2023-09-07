(function(window, $) {
    if (!!window.JSMenuCatalogPopup) return;

    window.JSMenuCatalogPopup = function(params) {


        $($.proxy(this.Init, this));
    };

    window.JSMenuCatalogPopup.prototype.Init = function() {
        $("._popup-catalog-menu-start-mobile-button").on("click", $.proxy(function(event) {
            this.OpenMobileCatalogMenu();
        }, this));

        $("._popup-catalog-menu-start-mobile-button--bottom-header").on("click", $.proxy(function(event) {
            this.OpenMobileCatalogMenu();
        }, this));

        $("._popup-catalog-menu-start-mobile-close").on("click", $.proxy(function(event) {
            this.CloseMobileCatalogMenu();
        }, this));

        $("._popup-catalog-menu-start-mobile").on("click", ".section a", $.proxy(function(event) {
            var $a = $(event.target).closest("a");
            var submenuId = $a.data("id");

            this.CloseMobileCatalogMenu();
            this.OpenMobileCatalogSubmenu(submenuId);
            return false;
        }, this));

        $("._popup-catalog-menu-mobile-close").on("click", $.proxy(function(event) {
            var $btn = $(event.target).closest("._popup-catalog-menu-mobile-close");
            var $modal = $btn.parents("._popup-catalog-menu-mobile");
            var submenuId = $modal.data("id");

            // this.CloseMobileCatalogMenu();
            this.CloseMobileCatalogSubmenu(submenuId);
        }, this));

        $("._popup-catalog-menu-mobile-back").on("click", $.proxy(function(event) {
            var $btn = $(event.target).closest("._popup-catalog-menu-mobile-back");
            var $modal = $btn.parents("._popup-catalog-menu-mobile");
            var submenuId = $modal.data("id");

            this.CloseMobileCatalogSubmenu(submenuId);
            this.OpenMobileCatalogMenu();
        }, this));

        $('.button__open-menu').on("click", $.proxy(function(event) {
            if ($(window).width() < 551) {
                this.OpenMobileCatalogMenu();
            } else {
                $('.button__open-menu').toggleClass('button__open-menu--white');
                $('.icon-burger').toggleClass('icon-burger--cross');
                $('.catalog-menu-popup').toggleClass('catalog-menu-popup--show');
            }
        }, this));
    };



    window.JSMenuCatalogPopup.prototype.OpenMobileCatalogMenu = function() {
        TweenMax.fromTo(
            '._popup-catalog-menu-start-mobile',
            1,
            {
                ease: Power4.easeOut,
                left: -window.screen.width
            },
            {
                ease: Power4.easeOut,
                left: 0
            }
        );
    };

    window.JSMenuCatalogPopup.prototype.CloseMobileCatalogMenu = function() {
        TweenMax.fromTo(
            '._popup-catalog-menu-start-mobile',
            1,
            {
                ease: Power4.easeOut,
                left: 0
            },
            {
                ease: Power4.easeOut,
                left: -window.screen.width
            }
        );
    };

    window.JSMenuCatalogPopup.prototype.OpenMobileCatalogSubmenu = function(i) {
        TweenMax.fromTo(
            "._popup-catalog-menu-mobile[data-id="+i+"]",
            1,
            {
                ease: Power4.easeOut,
                left: window.screen.width
            },
            {
                ease: Power4.easeOut,
                left: 0
            }
        );
    };

    window.JSMenuCatalogPopup.prototype.CloseMobileCatalogSubmenu = function(i) {
        TweenMax.fromTo(
            "._popup-catalog-menu-mobile[data-id="+i+"]",
            1,
            {
                ease: Power4.easeOut,
                left: 0
            },
            {
                ease: Power4.easeOut,
                left: window.screen.width
            }
        );
    };

})(window, window.jQuery);