(function(window) {
    if (!!window.JSMainSlider) return;

    window.JSMainSlider = function(arParams) {
        this.carouselId = null;
        
        this.owl = null;

        if (typeof arParams === "object") {
            this.carouselId = arParams.carouselId;
        }

        $($.proxy(this.Init, this));
    };

    window.JSMainSlider.prototype.Init = function() {
        if (!!this.carouselId) {
            this.owl = $("#"+this.carouselId).owlCarousel({
                loop: true,
                smartSpeed: 700,
                nav: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    1000: {
                        items: 1
                    }
                },
                onInitialized: $.proxy(function(event) {
                    this.AddProgressBar(event.page.index);
                }, this),
                onChanged: $.proxy(function(event) {
                    this.AddProgressBar(event.page.index);
                }, this)
            });
        }
    };

    window.JSMainSlider.prototype.AddProgressBar = function(page) {
        page = page === -1 ? 0 : page;
        
        var elems = $("#"+this.carouselId+" .owl-dot");
        if (elems.length === 0) return;
    
        var target = elems[page];
        target.innerHTML = '<span><div class="skill12"></div></span>';
    
        var bar = new ProgressBar.Circle($(target).find('.skill12')[0], {
            opacity: 0.5,
            color: "rgb(255,255,255)",
            trailColor: 'rgba(255,255,255,0.5)',
            from: {
                color: 'rgba(255,255,255,0.5)',
                width: 10
            },
            to: {
                color: 'rgba(255,255,255,0.5)',
                width: 10
            },
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
            from: {
                color: '#fff',
                width: 10
            },
            to: {
                color: '#fff',
                width: 10
            },
            // Set default step function for all animate calls
            step: function (state, circle) {
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
    };
})(window);