(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/dearSanta"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function($) {/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");
/* harmony import */ var _form_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./form.vue */ "./resources/js/components/form.vue");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }



/* harmony default export */ __webpack_exports__["default"] = ({
  "extends": _form_vue__WEBPACK_IMPORTED_MODULE_1__["default"],
  computed: _objectSpread({
    emails: function emails() {
      return Object.values(this.data.emails).sort(function (email1, email2) {
        return new Date(email1.created_at) > new Date(email2.created_at) ? -1 : 1;
      }).map(function (email) {
        email.created_at = new Date(email.created_at).toLocaleString('fr-FR');
        return email;
      });
    },
    checkUpdates: function checkUpdates() {
      return !!Object.values(this.data.emails).find(function (email) {
        return email.delivery_status === 'created';
      });
    }
  }, Object(vuex__WEBPACK_IMPORTED_MODULE_0__["mapState"])(['lang'])),
  created: function created() {
    var _this = this;

    setInterval(function () {
      if (_this.checkUpdates) _this.fetchState();
    }, 5000);
  },
  methods: {
    success: function success(data) {
      this.$set(this.data.emails, data.email.id, data.email);
    },
    fetchState: function fetchState() {
      var app = this;
      return $.ajax({
        url: "/dearsanta/".concat(this.data.santa.id, "/fetchState"),
        type: 'POST',
        data: {
          _token: this.csrf,
          key: this.key
        },
        success: function success(data) {
          if (data.emails) {
            Object.values(data.emails).forEach(function (email) {
              var new_update = new Date(email.mail.updated_at);
              var old_update = new Date(app.data.emails[email.id].mail.updated_at);
              app.data.emails[email.id].mail.delivery_status = new_update > old_update ? email.mail.delivery_status : app.data.emails[email.id].mail.delivery_status;
            });
          }
        }
      });
    }
  }
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4& ***!
  \****************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "ajax-form",
        {
          attrs: { action: "/dearsanta/" + _vm.data.santa.id + "/send" },
          on: { success: _vm.success }
        },
        [
          _c("fieldset", [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { attrs: { for: "mailContent" } }, [
                _vm._v("Contenu du mail")
              ]),
              _vm._v(" "),
              _c("textarea", {
                staticClass: "form-control",
                attrs: {
                  id: "mailContent",
                  name: "content",
                  required: "",
                  placeholder: "Cher Papa Noël..."
                }
              })
            ])
          ])
        ]
      ),
      _vm._v(" "),
      _c("table", { staticClass: "table table-hover" }, [
        _c("thead", [
          _c("tr", { staticClass: "table-active" }, [
            _c("th", { attrs: { scope: "col" } }, [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.lang.get("dearsanta.list.date")) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("th", { attrs: { scope: "col" } }, [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.lang.get("dearsanta.list.body")) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("th", { attrs: { scope: "col" } }, [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.lang.get("dearsanta.list.status")) +
                  "\n                "
              )
            ])
          ])
        ]),
        _vm._v(" "),
        _c(
          "tbody",
          [
            _vm._l(_vm.emails, function(email) {
              return _c("tr", { key: email.id, staticClass: "email" }, [
                _c("td", [_vm._v(_vm._s(email.mail.created_at))]),
                _vm._v(" "),
                _c("td", [_vm._v(_vm._s(email.mail_body))]),
                _vm._v(" "),
                _c("td", [_vm._v(_vm._s(email.mail.delivery_status))])
              ])
            }),
            _vm._v(" "),
            _vm.emails.length === 0
              ? _c("tr", { staticClass: "no-email" }, [
                  _c("td", { attrs: { colspan: "3" } }, [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.lang.get("dearsanta.list.empty")) +
                        "\n                "
                    )
                  ])
                ])
              : _vm._e()
          ],
          2
        )
      ])
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/dearSantaForm.vue":
/*!***************************************************!*\
  !*** ./resources/js/components/dearSantaForm.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dearSantaForm.vue?vue&type=template&id=600f7ca4& */ "./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4&");
/* harmony import */ var _dearSantaForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./dearSantaForm.vue?vue&type=script&lang=js& */ "./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _dearSantaForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__["render"],
  _dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/dearSantaForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_dearSantaForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./dearSantaForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_dearSantaForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4&":
/*!**********************************************************************************!*\
  !*** ./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./dearSantaForm.vue?vue&type=template&id=600f7ca4& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/dearSantaForm.vue?vue&type=template&id=600f7ca4&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dearSantaForm_vue_vue_type_template_id_600f7ca4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



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
/* harmony import */ var _components_dearSantaForm_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/dearSantaForm.vue */ "./resources/js/components/dearSantaForm.vue");
/* harmony import */ var _mixins_vueFetcher_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mixins/vueFetcher.js */ "./resources/js/mixins/vueFetcher.js");



window.app = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
  mixins: [Object(_mixins_vueFetcher_js__WEBPACK_IMPORTED_MODULE_2__["VueFetcher"])(_components_dearSantaForm_vue__WEBPACK_IMPORTED_MODULE_1__["default"])]
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