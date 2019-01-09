(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/dearSanta"],{

/***/ "./resources/js/ajaxVue.js":
/*!*********************************!*\
  !*** ./resources/js/ajaxVue.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

var alertify = __webpack_require__(/*! alertify.js */ "./node_modules/alertify.js/dist/js/alertify.js");

var Vue = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");

/* harmony default export */ __webpack_exports__["default"] = (Vue.extend({
  data: function data() {
    return {
      fieldErrors: [],
      sending: false,
      sent: false
    };
  },
  computed: {
    errors: function errors() {
      var errors = [];

      for (var field in this.fieldErrors) {
        errors = errors.concat(this.fieldErrors[field]);
      }

      return errors;
    }
  },
  methods: {
    submit: function submit(event) {
      var postData = $(event.target).serializeArray();
      var formURL = $(event.target).attr("action");

      if (!this.sending && !this.sent) {
        this.sending = true;
        var app = this;
        $.ajax({
          url: formURL,
          type: "POST",
          data: postData,
          success: function success(data, textStatus, jqXHR) {
            alertify.alert(jqXHR.responseJSON.message);
            app.sending = false;
            app.sent = true;
          },
          error: function error(jqXHR, textStatus, errorThrown) {
            app.fieldErrors = jqXHR.responseJSON.errors;
            app.sending = false;
          }
        });
      }
    }
  }
}));

/***/ }),

/***/ "./resources/js/dearSanta.js":
/*!***********************************!*\
  !*** ./resources/js/dearSanta.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var CryptoJS = __webpack_require__(/*! crypto-js */ "./node_modules/crypto-js/index.js");

var VueAjax = __webpack_require__(/*! ./ajaxVue.js */ "./resources/js/ajaxVue.js");

window.app = new VueAjax({
  el: '#form',
  data: {
    challenge: global.challenge,
    key: window.location.hash.substr(1),
    text: global.text,
    verified: false
  },
  created: function created() {
    //var key = CryptoJS.enc.Hex.parse(this.key);
    //var text = CryptoJS.AES.decrypt(this.challenge, key, {iv: iv}).toString(CryptoJS.enc.Utf8);
    this.verified = false; //(text === this.text);
  }
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ 2:
/*!*****************************************!*\
  !*** multi ./resources/js/dearSanta.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/js/dearSanta.js */"./resources/js/dearSanta.js");


/***/ })

},[[2,"/js/manifest","/js/vendor"]]]);