(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/organizer"],{

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

/* harmony default export */ __webpack_exports__["default"] = ({
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
});

/***/ }),

/***/ "./resources/js/organizer.js":
/*!***********************************!*\
  !*** ./resources/js/organizer.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var crypto_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! crypto-js */ "./node_modules/crypto-js/index.js");
/* harmony import */ var crypto_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(crypto_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _ajaxVue_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ajaxVue.js */ "./resources/js/ajaxVue.js");



window.app = new vue__WEBPACK_IMPORTED_MODULE_1___default.a({
  mixins: [_ajaxVue_js__WEBPACK_IMPORTED_MODULE_2__["default"]],
  el: '#form',
  data: {
    challenge: window.global.challenge,
    key: window.location.hash.substr(1),
    text: window.global.text,
    verified: false
  },
  created: function created() {
    var challenge = JSON.parse(atob(this.challenge));
    var text = crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.AES.decrypt(challenge.value, crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Base64.parse(this.key), {
      iv: crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Base64.parse(challenge.iv)
    }).toString(crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Utf8);
    this.verified = text === this.text;
  }
});

/***/ }),

/***/ 3:
/*!*****************************************!*\
  !*** multi ./resources/js/organizer.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/js/organizer.js */"./resources/js/organizer.js");


/***/ })

},[[3,"/js/manifest","/js/vendor"]]]);