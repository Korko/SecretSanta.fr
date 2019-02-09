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

/***/ "./resources/js/dearSanta.js":
/*!***********************************!*\
  !*** ./resources/js/dearSanta.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ajaxVue_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ajaxVue.js */ "./resources/js/ajaxVue.js");
/* harmony import */ var _decrypterVue_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./decrypterVue.js */ "./resources/js/decrypterVue.js");



window.app = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
  mixins: [_ajaxVue_js__WEBPACK_IMPORTED_MODULE_1__["default"], _decrypterVue_js__WEBPACK_IMPORTED_MODULE_2__["default"]],
  el: '#form',
  data: {
    challenge: window.global.challenge,
    key: window.location.hash.substr(1),
    text: window.global.text,
    verified: false
  },
  created: function created() {
    this.verified = this.decrypt(this.challenge) === this.text;
  }
});

/***/ }),

/***/ "./resources/js/decrypterVue.js":
/*!**************************************!*\
  !*** ./resources/js/decrypterVue.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var crypto_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! crypto-js */ "./node_modules/crypto-js/index.js");
/* harmony import */ var crypto_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(crypto_js__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ __webpack_exports__["default"] = ({
  data: {
    key: window.location.hash.substr(1)
  },
  methods: {
    decrypt: function decrypt(data) {
      var datab = JSON.parse(atob(data));
      return crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.AES.decrypt(datab.value, crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Base64.parse(this.key), {
        iv: crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Base64.parse(datab.iv)
      }).toString(crypto_js__WEBPACK_IMPORTED_MODULE_0___default.a.enc.Utf8);
    }
  }
});

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