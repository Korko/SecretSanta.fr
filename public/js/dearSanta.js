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
    call: function call(url, options) {
      if (!this.sending && !this.sent) {
        this.sending = true;
        var app = this;
        $.ajax({
          url: url,
          type: options.data ? "POST" : "GET",
          data: options.data,
          success: function success(data, textStatus, jqXHR) {
            if (jqXHR.responseJSON.message) alertify.success(jqXHR.responseJSON.message);
            app.sending = false;
            app.sent = true;
            if (options.success) options.success(jqXHR.responseJSON);
          },
          error: function error(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON.message) alertify.error(jqXHR.responseJSON.message);
            if (jqXHR.responseJSON.errors) app.fieldErrors = jqXHR.responseJSON.errors;
            app.sending = false;
            if (options.error) options.error(jqXHR.responseJSON);
          }
        });
      }
    },
    submit: function submit(event) {
      this.submitForm(event.target);
    },
    submitForm: function submitForm(target) {
      var postData = $(event.target).serializeArray();
      var formUrl = $(event.target).attr("action");
      this.call(formUrl, {
        data: postData
      });
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
/* harmony import */ var _statesVue_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./statesVue.js */ "./resources/js/statesVue.js");
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./store.js */ "./resources/js/store.js");





window.app = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
  mixins: [_statesVue_js__WEBPACK_IMPORTED_MODULE_2__["default"]],
  components: {
    DearSantaFetcher: {
      mixins: [_ajaxVue_js__WEBPACK_IMPORTED_MODULE_1__["default"]],
      props: ['formurl'],
      computed: Object(vuex__WEBPACK_IMPORTED_MODULE_3__["mapState"])(['csrf', 'key']),
      mounted: function mounted() {
        this.$nextTick(function () {
          var _this = this;

          this.call(this.formurl, {
            data: {
              _token: this.csrf,
              key: this.key
            },
            success: function success(json) {
              _this.$emit('success', json);
            }
          });
        });
      }
    },
    DearSantaForm: {
      template: '#form-template',
      mixins: [_ajaxVue_js__WEBPACK_IMPORTED_MODULE_1__["default"]],
      props: ['formurl'],
      computed: Object(vuex__WEBPACK_IMPORTED_MODULE_3__["mapState"])(['csrf', 'key', 'data'])
    }
  },
  el: '#form',
  store: _store_js__WEBPACK_IMPORTED_MODULE_4__["default"],
  data: {
    state: 'DearSantaFetcher',
    states: Object.freeze({
      'DearSantaFetcher': {
        'success': 'DearSantaForm',
        'failure': 'FetcherFailure'
      }
    })
  }
});

/***/ }),

/***/ "./resources/js/statesVue.js":
/*!***********************************!*\
  !*** ./resources/js/statesVue.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      state: null,
      states: {}
    };
  },
  methods: {
    stateSuccess: function stateSuccess(data) {
      this.$store.state.data = data;
      this.state = this.states[this.state].success || this.state;
    },
    stateFailure: function stateFailure(data) {
      this.$store.state.data = data;
      this.state = this.states[this.state].failure || this.state;
    }
  }
});

/***/ }),

/***/ "./resources/js/store.js":
/*!*******************************!*\
  !*** ./resources/js/store.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");


vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(vuex__WEBPACK_IMPORTED_MODULE_1__["default"]);
/* harmony default export */ __webpack_exports__["default"] = (new vuex__WEBPACK_IMPORTED_MODULE_1__["default"].Store({
  state: {
    csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    key: window.location.hash.substr(1),
    data: null
  }
}));

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