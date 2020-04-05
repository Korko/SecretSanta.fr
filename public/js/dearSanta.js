(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/dearSanta"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/dearSantaForm.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_for_each__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.for-each */ "./node_modules/core-js/modules/es.array.for-each.js");
/* harmony import */ var core_js_modules_es_array_for_each__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_for_each__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_array_map__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.map */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_sort__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.sort */ "./node_modules/core-js/modules/es.array.sort.js");
/* harmony import */ var core_js_modules_es_array_sort__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_sort__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.date.to-string */ "./node_modules/core-js/modules/es.date.to-string.js");
/* harmony import */ var core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_object_assign__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.object.assign */ "./node_modules/core-js/modules/es.object.assign.js");
/* harmony import */ var core_js_modules_es_object_assign__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_assign__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_object_values__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.object.values */ "./node_modules/core-js/modules/es.object.values.js");
/* harmony import */ var core_js_modules_es_object_values__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_values__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! vuelidate */ "./node_modules/vuelidate/lib/index.js");
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(vuelidate__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! vuelidate/lib/validators */ "./node_modules/vuelidate/lib/validators/index.js");
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _form_vue__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./form.vue */ "./resources/js/components/form.vue");











vue__WEBPACK_IMPORTED_MODULE_8___default.a.use(vuelidate__WEBPACK_IMPORTED_MODULE_9___default.a);

/* harmony default export */ __webpack_exports__["default"] = ({
  extends: _form_vue__WEBPACK_IMPORTED_MODULE_11__["default"],
  props: {
    data: {
      type: Object,
      default: {}
    }
  },
  data: function data() {
    return {
      content: ''
    };
  },
  computed: {
    emails: function emails() {
      return Object.values(this.data.emails).map(function (email) {
        return Object.assign(email, email.mail);
      }).sort(function (email1, email2) {
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
  },
  created: function created() {
    var _this = this;

    setInterval(function () {
      if (_this.checkUpdates) _this.fetchState();
    }, 5000);
  },
  validations: {
    content: {
      required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_10__["required"]
    }
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
          attrs: {
            action: "/dearsanta/" + _vm.data.santa.id + "/send",
            $v: _vm.$v
          },
          on: { success: _vm.success }
        },
        [
          _c("fieldset", [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { attrs: { for: "mailContent" } }, [
                _vm._v(_vm._s(_vm.$t("dearsanta.content.label")))
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "input-group" }, [
                _c("textarea", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.content,
                      expression: "content"
                    }
                  ],
                  class: {
                    "form-control": true,
                    "is-invalid": _vm.$v.content.$error
                  },
                  attrs: {
                    id: "mailContent",
                    name: "content",
                    placeholder: _vm.$t("dearsanta.content.placeholder"),
                    "aria-invalid": _vm.$v.content.$error
                  },
                  domProps: { value: _vm.content },
                  on: {
                    blur: function($event) {
                      return _vm.$v.content.$touch()
                    },
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.content = $event.target.value
                    }
                  }
                }),
                _vm._v(" "),
                !_vm.$v.content.required
                  ? _c("div", { staticClass: "invalid-tooltip" }, [
                      _vm._v(
                        _vm._s(
                          _vm.$t("validation.custom.dearsanta.content.required")
                        )
                      )
                    ])
                  : _vm._e()
              ])
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
                  _vm._s(_vm.$t("dearsanta.list.date")) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("th", { attrs: { scope: "col" } }, [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.$t("dearsanta.list.body")) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("th", { attrs: { scope: "col" } }, [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.$t("dearsanta.list.status")) +
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
                _c("td", [_vm._v(_vm._s(email.created_at))]),
                _vm._v(" "),
                _c("td", [_vm._v(_vm._s(email.mail_body))]),
                _vm._v(" "),
                _c("td", [
                  _vm._v(
                    _vm._s(
                      _vm.$t("common.email.status." + email.delivery_status)
                    )
                  )
                ])
              ])
            }),
            _vm._v(" "),
            _vm.emails.length === 0
              ? _c("tr", { staticClass: "no-email" }, [
                  _c("td", { attrs: { colspan: "3" } }, [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.$t("dearsanta.list.empty")) +
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
/* harmony import */ var vue_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-i18n */ "./node_modules/vue-i18n/dist/vue-i18n.esm.js");
/* harmony import */ var _vue_i18n_locales_generated_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./vue-i18n-locales.generated.js */ "./resources/js/vue-i18n-locales.generated.js");
/* harmony import */ var _components_dearSantaForm_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/dearSantaForm.vue */ "./resources/js/components/dearSantaForm.vue");
/* harmony import */ var _mixins_vueFetcher_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mixins/vueFetcher.js */ "./resources/js/mixins/vueFetcher.js");


vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(vue_i18n__WEBPACK_IMPORTED_MODULE_1__["default"]);
var lang = document.documentElement.lang.substr(0, 2);

var i18n = new vue_i18n__WEBPACK_IMPORTED_MODULE_1__["default"]({
  locale: lang,
  messages: _vue_i18n_locales_generated_js__WEBPACK_IMPORTED_MODULE_2__["default"]
});


window.app = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
  mixins: [Object(_mixins_vueFetcher_js__WEBPACK_IMPORTED_MODULE_4__["VueFetcher"])(_components_dearSantaForm_vue__WEBPACK_IMPORTED_MODULE_3__["default"])],
  i18n: i18n
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