(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/common"],{

/***/ "./resources/js/common.js":
/*!********************************!*\
  !*** ./resources/js/common.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery, $) {jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

__webpack_require__(/*! jquery.actual */ "./node_modules/jquery.actual/jquery.actual.js");

__webpack_require__(/*! jquery.scrollto */ "./node_modules/jquery.scrollto/jquery.scrollTo.js");

(function ($, sr) {
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function debounce(func, threshold, execAsap) {
    var timeout;
    return function debounced() {
      var obj = this,
          args = arguments;

      function delayed() {
        if (!execAsap) func.apply(obj, args);
        timeout = null;
      }

      ;
      if (timeout) clearTimeout(timeout);else if (execAsap) func.apply(obj, args);
      timeout = setTimeout(delayed, threshold || 100);
    };
  }; // smartresize


  jQuery.fn[sr] = function (fn) {
    return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
  };
})(jQuery, 'smartresize');

jQuery(document).ready(function () {
  ///////////////////////////////
  // Set Home Slideshow Height
  ///////////////////////////////
  function setHomeBannerHeight() {
    var windowHeight = jQuery(window).height();
    jQuery('#header').height(windowHeight);
  } ///////////////////////////////
  // Center Home Slideshow Text
  ///////////////////////////////


  function centerHomeBannerText() {
    var bannerText = jQuery('#header > .center');
    var bannerTextTop = jQuery('#header').actual('height') / 2 - jQuery('#header > .center').actual('height') / 2 - 20; //var bannerTextTop = Math.min(jQuery('#header').actual('height'), (jQuery('#header').actual('height')/2) - (jQuery('#header > .center').actual('height')/2) - 20 + jQuery('html').scrollTop());

    bannerText.css('padding-top', bannerTextTop + 'px');
    bannerText.show();
  }

  setHomeBannerHeight();
  centerHomeBannerText(); //Resize events

  jQuery(window).smartresize(function () {
    setHomeBannerHeight();
    centerHomeBannerText();
  });

  function scroll() {
    centerHomeBannerText();

    if ($(document).scrollTop() > 200) {
      $('body').addClass('scrolled');
    } else {
      $('body').removeClass('scrolled');
    }
  }

  document.onscroll = scroll;
  var $scrollDownArrow = $('#scrollDownArrow');

  var animateScrollDownArrow = function animateScrollDownArrow() {
    $scrollDownArrow.animate({
      top: 5
    }, 400, "linear", function () {
      $scrollDownArrow.animate({
        top: -5
      }, 400, "linear", function () {
        animateScrollDownArrow();
      });
    });
  };

  animateScrollDownArrow(); //Set Down Arrow Button

  jQuery('#scrollDownArrow').click(function (e) {
    e.preventDefault();
    jQuery.scrollTo("#what", 1000, {
      offset: -jQuery('#header #menu').height(),
      axis: 'y'
    });
  });
  jQuery('.nav > li > a, #logo a').click(function (e) {
    e.preventDefault();
    jQuery.scrollTo(jQuery(this).attr('href'), 400, {
      offset: -jQuery('#header #menu').height(),
      axis: 'y'
    });
  });
});

var alertify = __webpack_require__(/*! alertify.js */ "./node_modules/alertify.js/dist/js/alertify.js");

if (window.global && window.global.alert) {
  alertify.alert(window.global.alert);
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js"), __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./resources/sass/dearSanta.scss":
/*!***************************************!*\
  !*** ./resources/sass/dearSanta.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/layout.scss":
/*!************************************!*\
  !*** ./resources/sass/layout.scss ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/organizer.scss":
/*!***************************************!*\
  !*** ./resources/sass/organizer.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/randomForm.scss":
/*!****************************************!*\
  !*** ./resources/sass/randomForm.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!********************************************************************************************************************************************************************!*\
  !*** multi ./resources/js/common.js ./resources/sass/layout.scss ./resources/sass/randomForm.scss ./resources/sass/dearSanta.scss ./resources/sass/organizer.scss ***!
  \********************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/js/common.js */"./resources/js/common.js");
__webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/sass/layout.scss */"./resources/sass/layout.scss");
__webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/sass/randomForm.scss */"./resources/sass/randomForm.scss");
__webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/sass/dearSanta.scss */"./resources/sass/dearSanta.scss");
module.exports = __webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/sass/organizer.scss */"./resources/sass/organizer.scss");


/***/ })

},[[0,"/js/manifest","/js/vendor"]]]);