(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/common"],{

/***/ "./resources/js/common.js":
/*!********************************!*\
  !*** ./resources/js/common.js ***!
  \********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function($) {/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery_actual__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery.actual */ "./node_modules/jquery.actual/jquery.actual.js");
/* harmony import */ var jquery_actual__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery_actual__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var jquery_scrollto__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! jquery.scrollto */ "./node_modules/jquery.scrollto/jquery.scrollTo.js");
/* harmony import */ var jquery_scrollto__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(jquery_scrollto__WEBPACK_IMPORTED_MODULE_2__);




(function ($, sr) {
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function debounce(func, threshold, execAsap) {
    var _arguments = arguments,
        _this = this;

    var timeout;
    return function () {
      var obj = _this,
          args = _arguments;

      function delayed() {
        if (!execAsap) func.apply(obj, args);
        timeout = null;
      }

      if (timeout) clearTimeout(timeout);else if (execAsap) func.apply(obj, args);
      timeout = setTimeout(delayed, threshold || 100);
    };
  }; // smartresize


  jquery__WEBPACK_IMPORTED_MODULE_0___default.a.fn[sr] = function (fn) {
    return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
  };
})(jquery__WEBPACK_IMPORTED_MODULE_0___default.a, 'smartresize');

jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).ready(function () {
  ///////////////////////////////
  // Set Home Slideshow Height
  ///////////////////////////////
  function setHomeBannerHeight() {
    var windowHeight = jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).height();
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header').height(windowHeight);
  } ///////////////////////////////
  // Center Home Slideshow Text
  ///////////////////////////////


  function centerHomeBannerText() {
    var bannerText = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header > .center');
    var bannerTextTop = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header').actual('height') / 2 - jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header > .center').actual('height') / 2 - 20; //var bannerTextTop = Math.min(jQuery('#header').actual('height'), (jQuery('#header').actual('height')/2) - (jQuery('#header > .center').actual('height')/2) - 20 + jQuery('html').scrollTop());

    bannerText.css('padding-top', bannerTextTop + 'px');
    bannerText.show();
  }

  setHomeBannerHeight();
  centerHomeBannerText(); //Resize events

  jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).smartresize(function () {
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
    }, 400, 'linear', function () {
      $scrollDownArrow.animate({
        top: -5
      }, 400, 'linear', function () {
        animateScrollDownArrow();
      });
    });
  };

  animateScrollDownArrow(); //Set Down Arrow Button

  jquery__WEBPACK_IMPORTED_MODULE_0___default()('#scrollDownArrow').click(function (e) {
    e.preventDefault();
    jquery__WEBPACK_IMPORTED_MODULE_0___default.a.scrollTo('#what', 1000, {
      offset: -jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header #menu').height(),
      axis: 'y'
    });
  });
  jquery__WEBPACK_IMPORTED_MODULE_0___default()('.nav > li > a, #logo a').click(function (e) {
    e.preventDefault();
    jquery__WEBPACK_IMPORTED_MODULE_0___default.a.scrollTo(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('href'), 400, {
      offset: -jquery__WEBPACK_IMPORTED_MODULE_0___default()('#header #menu').height(),
      axis: 'y'
    });
  });
});

var alertify = __webpack_require__(/*! alertify.js */ "./node_modules/alertify.js/dist/js/alertify.js");

if (window.global && window.global.alert) {
  alertify.alert(window.global.alert);
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

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