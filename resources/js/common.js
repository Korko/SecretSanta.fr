import jQuery from 'jquery';

import actual from 'jquery.actual'; // eslint-disable-line no-unused-vars
import scrollTo from 'jquery.scrollto'; // eslint-disable-line no-unused-vars

(function($, sr) {
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function(func, threshold, execAsap) {
        var timeout;
        return () => {
            var obj = this;
            var args = arguments;
            function delayed() {
                if (!execAsap) func.apply(obj, args);
                timeout = null;
            }
            if (timeout) clearTimeout(timeout);
            else if (execAsap) func.apply(obj, args);
            timeout = setTimeout(delayed, threshold || 100);
        };
    };
    // smartresize
    jQuery.fn[sr] = function(fn) {
        return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
    };
})(jQuery, 'smartresize');

jQuery(document).ready(function() {
    /// ////////////////////////////
    // Set Home Slideshow Height
    /// ////////////////////////////
    function setHomeBannerHeight() {
        var windowHeight = jQuery(window).height();
        jQuery('#header').height(windowHeight);
    }
    /// ////////////////////////////
    // Center Home Slideshow Text
    /// ////////////////////////////
    function centerHomeBannerText() {
        var bannerText = jQuery('#header > .center');
        var bannerTextTop =
            jQuery('#header').actual('height') / 2 - jQuery('#header > .center').actual('height') / 2 - 20;
        // var bannerTextTop = Math.min(jQuery('#header').actual('height'), (jQuery('#header').actual('height')/2) - (jQuery('#header > .center').actual('height')/2) - 20 + jQuery('html').scrollTop());
        bannerText.css('padding-top', bannerTextTop + 'px');
        bannerText.show();
    }
    setHomeBannerHeight();
    centerHomeBannerText();
    // Resize events
    jQuery(window).smartresize(function() {
        setHomeBannerHeight();
        centerHomeBannerText();
    });

    function scroll() {
        centerHomeBannerText();
        if (jQuery(document).scrollTop() > 200) {
            jQuery('body').addClass('scrolled');
        } else {
            jQuery('body').removeClass('scrolled');
        }
    }
    document.onscroll = scroll;
    var $scrollDownArrow = jQuery('#scrollDownArrow');
    var animateScrollDownArrow = function() {
        $scrollDownArrow.animate(
            {
                top: 5
            },
            400,
            'linear',
            function() {
                $scrollDownArrow.animate(
                    {
                        top: -5
                    },
                    400,
                    'linear',
                    function() {
                        animateScrollDownArrow();
                    }
                );
            }
        );
    };
    animateScrollDownArrow();
    // Set Down Arrow Button
    jQuery('#scrollDownArrow').click(function(e) {
        e.preventDefault();
        jQuery.scrollTo('#what', 1000, {
            offset: -jQuery('#header #menu').height(),
            axis: 'y'
        });
    });
    jQuery('.nav > li > a, #logo a').click(function(e) {
        e.preventDefault();
        jQuery.scrollTo(jQuery(this).attr('href'), 400, {
            offset: -jQuery('#header #menu').height(),
            axis: 'y'
        });
    });
});

var alertify = require('alertify.js');
if (window.global && window.global.alert) {
    alertify.alert(window.global.alert);
}

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
