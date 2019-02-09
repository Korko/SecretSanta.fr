(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/randomForm"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/select2.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
var jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

__webpack_require__(/*! multiple-select */ "./node_modules/multiple-select/multiple-select.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  props: ['name', 'options', 'value', 'placeholder'],
  mounted: function mounted() {
    jQuery(this.$el).multipleSelect({
      selectAll: false,
      filter: true,
      placeholder: this.placeholder,
      ellipsis: true
    });
  },
  updated: function updated() {
    jQuery(this.$el).multipleSelect('refresh');
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--9-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--9-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\nselect {\n  min-width: 300px;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/moment/locale sync recursive ^\\.\\/.*$":
/*!**************************************************!*\
  !*** ./node_modules/moment/locale sync ^\.\/.*$ ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var map = {
	"./af": "./node_modules/moment/locale/af.js",
	"./af.js": "./node_modules/moment/locale/af.js",
	"./ar": "./node_modules/moment/locale/ar.js",
	"./ar-dz": "./node_modules/moment/locale/ar-dz.js",
	"./ar-dz.js": "./node_modules/moment/locale/ar-dz.js",
	"./ar-kw": "./node_modules/moment/locale/ar-kw.js",
	"./ar-kw.js": "./node_modules/moment/locale/ar-kw.js",
	"./ar-ly": "./node_modules/moment/locale/ar-ly.js",
	"./ar-ly.js": "./node_modules/moment/locale/ar-ly.js",
	"./ar-ma": "./node_modules/moment/locale/ar-ma.js",
	"./ar-ma.js": "./node_modules/moment/locale/ar-ma.js",
	"./ar-sa": "./node_modules/moment/locale/ar-sa.js",
	"./ar-sa.js": "./node_modules/moment/locale/ar-sa.js",
	"./ar-tn": "./node_modules/moment/locale/ar-tn.js",
	"./ar-tn.js": "./node_modules/moment/locale/ar-tn.js",
	"./ar.js": "./node_modules/moment/locale/ar.js",
	"./az": "./node_modules/moment/locale/az.js",
	"./az.js": "./node_modules/moment/locale/az.js",
	"./be": "./node_modules/moment/locale/be.js",
	"./be.js": "./node_modules/moment/locale/be.js",
	"./bg": "./node_modules/moment/locale/bg.js",
	"./bg.js": "./node_modules/moment/locale/bg.js",
	"./bm": "./node_modules/moment/locale/bm.js",
	"./bm.js": "./node_modules/moment/locale/bm.js",
	"./bn": "./node_modules/moment/locale/bn.js",
	"./bn.js": "./node_modules/moment/locale/bn.js",
	"./bo": "./node_modules/moment/locale/bo.js",
	"./bo.js": "./node_modules/moment/locale/bo.js",
	"./br": "./node_modules/moment/locale/br.js",
	"./br.js": "./node_modules/moment/locale/br.js",
	"./bs": "./node_modules/moment/locale/bs.js",
	"./bs.js": "./node_modules/moment/locale/bs.js",
	"./ca": "./node_modules/moment/locale/ca.js",
	"./ca.js": "./node_modules/moment/locale/ca.js",
	"./cs": "./node_modules/moment/locale/cs.js",
	"./cs.js": "./node_modules/moment/locale/cs.js",
	"./cv": "./node_modules/moment/locale/cv.js",
	"./cv.js": "./node_modules/moment/locale/cv.js",
	"./cy": "./node_modules/moment/locale/cy.js",
	"./cy.js": "./node_modules/moment/locale/cy.js",
	"./da": "./node_modules/moment/locale/da.js",
	"./da.js": "./node_modules/moment/locale/da.js",
	"./de": "./node_modules/moment/locale/de.js",
	"./de-at": "./node_modules/moment/locale/de-at.js",
	"./de-at.js": "./node_modules/moment/locale/de-at.js",
	"./de-ch": "./node_modules/moment/locale/de-ch.js",
	"./de-ch.js": "./node_modules/moment/locale/de-ch.js",
	"./de.js": "./node_modules/moment/locale/de.js",
	"./dv": "./node_modules/moment/locale/dv.js",
	"./dv.js": "./node_modules/moment/locale/dv.js",
	"./el": "./node_modules/moment/locale/el.js",
	"./el.js": "./node_modules/moment/locale/el.js",
	"./en-au": "./node_modules/moment/locale/en-au.js",
	"./en-au.js": "./node_modules/moment/locale/en-au.js",
	"./en-ca": "./node_modules/moment/locale/en-ca.js",
	"./en-ca.js": "./node_modules/moment/locale/en-ca.js",
	"./en-gb": "./node_modules/moment/locale/en-gb.js",
	"./en-gb.js": "./node_modules/moment/locale/en-gb.js",
	"./en-ie": "./node_modules/moment/locale/en-ie.js",
	"./en-ie.js": "./node_modules/moment/locale/en-ie.js",
	"./en-il": "./node_modules/moment/locale/en-il.js",
	"./en-il.js": "./node_modules/moment/locale/en-il.js",
	"./en-nz": "./node_modules/moment/locale/en-nz.js",
	"./en-nz.js": "./node_modules/moment/locale/en-nz.js",
	"./eo": "./node_modules/moment/locale/eo.js",
	"./eo.js": "./node_modules/moment/locale/eo.js",
	"./es": "./node_modules/moment/locale/es.js",
	"./es-do": "./node_modules/moment/locale/es-do.js",
	"./es-do.js": "./node_modules/moment/locale/es-do.js",
	"./es-us": "./node_modules/moment/locale/es-us.js",
	"./es-us.js": "./node_modules/moment/locale/es-us.js",
	"./es.js": "./node_modules/moment/locale/es.js",
	"./et": "./node_modules/moment/locale/et.js",
	"./et.js": "./node_modules/moment/locale/et.js",
	"./eu": "./node_modules/moment/locale/eu.js",
	"./eu.js": "./node_modules/moment/locale/eu.js",
	"./fa": "./node_modules/moment/locale/fa.js",
	"./fa.js": "./node_modules/moment/locale/fa.js",
	"./fi": "./node_modules/moment/locale/fi.js",
	"./fi.js": "./node_modules/moment/locale/fi.js",
	"./fo": "./node_modules/moment/locale/fo.js",
	"./fo.js": "./node_modules/moment/locale/fo.js",
	"./fr": "./node_modules/moment/locale/fr.js",
	"./fr-ca": "./node_modules/moment/locale/fr-ca.js",
	"./fr-ca.js": "./node_modules/moment/locale/fr-ca.js",
	"./fr-ch": "./node_modules/moment/locale/fr-ch.js",
	"./fr-ch.js": "./node_modules/moment/locale/fr-ch.js",
	"./fr.js": "./node_modules/moment/locale/fr.js",
	"./fy": "./node_modules/moment/locale/fy.js",
	"./fy.js": "./node_modules/moment/locale/fy.js",
	"./gd": "./node_modules/moment/locale/gd.js",
	"./gd.js": "./node_modules/moment/locale/gd.js",
	"./gl": "./node_modules/moment/locale/gl.js",
	"./gl.js": "./node_modules/moment/locale/gl.js",
	"./gom-latn": "./node_modules/moment/locale/gom-latn.js",
	"./gom-latn.js": "./node_modules/moment/locale/gom-latn.js",
	"./gu": "./node_modules/moment/locale/gu.js",
	"./gu.js": "./node_modules/moment/locale/gu.js",
	"./he": "./node_modules/moment/locale/he.js",
	"./he.js": "./node_modules/moment/locale/he.js",
	"./hi": "./node_modules/moment/locale/hi.js",
	"./hi.js": "./node_modules/moment/locale/hi.js",
	"./hr": "./node_modules/moment/locale/hr.js",
	"./hr.js": "./node_modules/moment/locale/hr.js",
	"./hu": "./node_modules/moment/locale/hu.js",
	"./hu.js": "./node_modules/moment/locale/hu.js",
	"./hy-am": "./node_modules/moment/locale/hy-am.js",
	"./hy-am.js": "./node_modules/moment/locale/hy-am.js",
	"./id": "./node_modules/moment/locale/id.js",
	"./id.js": "./node_modules/moment/locale/id.js",
	"./is": "./node_modules/moment/locale/is.js",
	"./is.js": "./node_modules/moment/locale/is.js",
	"./it": "./node_modules/moment/locale/it.js",
	"./it.js": "./node_modules/moment/locale/it.js",
	"./ja": "./node_modules/moment/locale/ja.js",
	"./ja.js": "./node_modules/moment/locale/ja.js",
	"./jv": "./node_modules/moment/locale/jv.js",
	"./jv.js": "./node_modules/moment/locale/jv.js",
	"./ka": "./node_modules/moment/locale/ka.js",
	"./ka.js": "./node_modules/moment/locale/ka.js",
	"./kk": "./node_modules/moment/locale/kk.js",
	"./kk.js": "./node_modules/moment/locale/kk.js",
	"./km": "./node_modules/moment/locale/km.js",
	"./km.js": "./node_modules/moment/locale/km.js",
	"./kn": "./node_modules/moment/locale/kn.js",
	"./kn.js": "./node_modules/moment/locale/kn.js",
	"./ko": "./node_modules/moment/locale/ko.js",
	"./ko.js": "./node_modules/moment/locale/ko.js",
	"./ku": "./node_modules/moment/locale/ku.js",
	"./ku.js": "./node_modules/moment/locale/ku.js",
	"./ky": "./node_modules/moment/locale/ky.js",
	"./ky.js": "./node_modules/moment/locale/ky.js",
	"./lb": "./node_modules/moment/locale/lb.js",
	"./lb.js": "./node_modules/moment/locale/lb.js",
	"./lo": "./node_modules/moment/locale/lo.js",
	"./lo.js": "./node_modules/moment/locale/lo.js",
	"./lt": "./node_modules/moment/locale/lt.js",
	"./lt.js": "./node_modules/moment/locale/lt.js",
	"./lv": "./node_modules/moment/locale/lv.js",
	"./lv.js": "./node_modules/moment/locale/lv.js",
	"./me": "./node_modules/moment/locale/me.js",
	"./me.js": "./node_modules/moment/locale/me.js",
	"./mi": "./node_modules/moment/locale/mi.js",
	"./mi.js": "./node_modules/moment/locale/mi.js",
	"./mk": "./node_modules/moment/locale/mk.js",
	"./mk.js": "./node_modules/moment/locale/mk.js",
	"./ml": "./node_modules/moment/locale/ml.js",
	"./ml.js": "./node_modules/moment/locale/ml.js",
	"./mn": "./node_modules/moment/locale/mn.js",
	"./mn.js": "./node_modules/moment/locale/mn.js",
	"./mr": "./node_modules/moment/locale/mr.js",
	"./mr.js": "./node_modules/moment/locale/mr.js",
	"./ms": "./node_modules/moment/locale/ms.js",
	"./ms-my": "./node_modules/moment/locale/ms-my.js",
	"./ms-my.js": "./node_modules/moment/locale/ms-my.js",
	"./ms.js": "./node_modules/moment/locale/ms.js",
	"./mt": "./node_modules/moment/locale/mt.js",
	"./mt.js": "./node_modules/moment/locale/mt.js",
	"./my": "./node_modules/moment/locale/my.js",
	"./my.js": "./node_modules/moment/locale/my.js",
	"./nb": "./node_modules/moment/locale/nb.js",
	"./nb.js": "./node_modules/moment/locale/nb.js",
	"./ne": "./node_modules/moment/locale/ne.js",
	"./ne.js": "./node_modules/moment/locale/ne.js",
	"./nl": "./node_modules/moment/locale/nl.js",
	"./nl-be": "./node_modules/moment/locale/nl-be.js",
	"./nl-be.js": "./node_modules/moment/locale/nl-be.js",
	"./nl.js": "./node_modules/moment/locale/nl.js",
	"./nn": "./node_modules/moment/locale/nn.js",
	"./nn.js": "./node_modules/moment/locale/nn.js",
	"./pa-in": "./node_modules/moment/locale/pa-in.js",
	"./pa-in.js": "./node_modules/moment/locale/pa-in.js",
	"./pl": "./node_modules/moment/locale/pl.js",
	"./pl.js": "./node_modules/moment/locale/pl.js",
	"./pt": "./node_modules/moment/locale/pt.js",
	"./pt-br": "./node_modules/moment/locale/pt-br.js",
	"./pt-br.js": "./node_modules/moment/locale/pt-br.js",
	"./pt.js": "./node_modules/moment/locale/pt.js",
	"./ro": "./node_modules/moment/locale/ro.js",
	"./ro.js": "./node_modules/moment/locale/ro.js",
	"./ru": "./node_modules/moment/locale/ru.js",
	"./ru.js": "./node_modules/moment/locale/ru.js",
	"./sd": "./node_modules/moment/locale/sd.js",
	"./sd.js": "./node_modules/moment/locale/sd.js",
	"./se": "./node_modules/moment/locale/se.js",
	"./se.js": "./node_modules/moment/locale/se.js",
	"./si": "./node_modules/moment/locale/si.js",
	"./si.js": "./node_modules/moment/locale/si.js",
	"./sk": "./node_modules/moment/locale/sk.js",
	"./sk.js": "./node_modules/moment/locale/sk.js",
	"./sl": "./node_modules/moment/locale/sl.js",
	"./sl.js": "./node_modules/moment/locale/sl.js",
	"./sq": "./node_modules/moment/locale/sq.js",
	"./sq.js": "./node_modules/moment/locale/sq.js",
	"./sr": "./node_modules/moment/locale/sr.js",
	"./sr-cyrl": "./node_modules/moment/locale/sr-cyrl.js",
	"./sr-cyrl.js": "./node_modules/moment/locale/sr-cyrl.js",
	"./sr.js": "./node_modules/moment/locale/sr.js",
	"./ss": "./node_modules/moment/locale/ss.js",
	"./ss.js": "./node_modules/moment/locale/ss.js",
	"./sv": "./node_modules/moment/locale/sv.js",
	"./sv.js": "./node_modules/moment/locale/sv.js",
	"./sw": "./node_modules/moment/locale/sw.js",
	"./sw.js": "./node_modules/moment/locale/sw.js",
	"./ta": "./node_modules/moment/locale/ta.js",
	"./ta.js": "./node_modules/moment/locale/ta.js",
	"./te": "./node_modules/moment/locale/te.js",
	"./te.js": "./node_modules/moment/locale/te.js",
	"./tet": "./node_modules/moment/locale/tet.js",
	"./tet.js": "./node_modules/moment/locale/tet.js",
	"./tg": "./node_modules/moment/locale/tg.js",
	"./tg.js": "./node_modules/moment/locale/tg.js",
	"./th": "./node_modules/moment/locale/th.js",
	"./th.js": "./node_modules/moment/locale/th.js",
	"./tl-ph": "./node_modules/moment/locale/tl-ph.js",
	"./tl-ph.js": "./node_modules/moment/locale/tl-ph.js",
	"./tlh": "./node_modules/moment/locale/tlh.js",
	"./tlh.js": "./node_modules/moment/locale/tlh.js",
	"./tr": "./node_modules/moment/locale/tr.js",
	"./tr.js": "./node_modules/moment/locale/tr.js",
	"./tzl": "./node_modules/moment/locale/tzl.js",
	"./tzl.js": "./node_modules/moment/locale/tzl.js",
	"./tzm": "./node_modules/moment/locale/tzm.js",
	"./tzm-latn": "./node_modules/moment/locale/tzm-latn.js",
	"./tzm-latn.js": "./node_modules/moment/locale/tzm-latn.js",
	"./tzm.js": "./node_modules/moment/locale/tzm.js",
	"./ug-cn": "./node_modules/moment/locale/ug-cn.js",
	"./ug-cn.js": "./node_modules/moment/locale/ug-cn.js",
	"./uk": "./node_modules/moment/locale/uk.js",
	"./uk.js": "./node_modules/moment/locale/uk.js",
	"./ur": "./node_modules/moment/locale/ur.js",
	"./ur.js": "./node_modules/moment/locale/ur.js",
	"./uz": "./node_modules/moment/locale/uz.js",
	"./uz-latn": "./node_modules/moment/locale/uz-latn.js",
	"./uz-latn.js": "./node_modules/moment/locale/uz-latn.js",
	"./uz.js": "./node_modules/moment/locale/uz.js",
	"./vi": "./node_modules/moment/locale/vi.js",
	"./vi.js": "./node_modules/moment/locale/vi.js",
	"./x-pseudo": "./node_modules/moment/locale/x-pseudo.js",
	"./x-pseudo.js": "./node_modules/moment/locale/x-pseudo.js",
	"./yo": "./node_modules/moment/locale/yo.js",
	"./yo.js": "./node_modules/moment/locale/yo.js",
	"./zh-cn": "./node_modules/moment/locale/zh-cn.js",
	"./zh-cn.js": "./node_modules/moment/locale/zh-cn.js",
	"./zh-hk": "./node_modules/moment/locale/zh-hk.js",
	"./zh-hk.js": "./node_modules/moment/locale/zh-hk.js",
	"./zh-tw": "./node_modules/moment/locale/zh-tw.js",
	"./zh-tw.js": "./node_modules/moment/locale/zh-tw.js"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	var id = map[req];
	if(!(id + 1)) { // check for number or string
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return id;
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "./node_modules/moment/locale sync recursive ^\\.\\/.*$";

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--9-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--9-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../node_modules/css-loader??ref--9-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--9-2!../../../node_modules/vue-loader/lib??vue-loader-options!./select2.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=template&id=7e62a13b&":
/*!**********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/select2.vue?vue&type=template&id=7e62a13b& ***!
  \**********************************************************************************************************************************************************************************************************/
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
    "select",
    { attrs: { multiple: "multiple", name: _vm.name } },
    _vm._l(_vm.options, function(option) {
      return _c(
        "option",
        { key: option.id || option.value, domProps: { value: option.value } },
        [_vm._v(_vm._s(option.text))]
      )
    }),
    0
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

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

/***/ "./resources/js/components/select2.vue":
/*!*********************************************!*\
  !*** ./resources/js/components/select2.vue ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./select2.vue?vue&type=template&id=7e62a13b& */ "./resources/js/components/select2.vue?vue&type=template&id=7e62a13b&");
/* harmony import */ var _select2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./select2.vue?vue&type=script&lang=js& */ "./resources/js/components/select2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./select2.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _select2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/select2.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/select2.vue?vue&type=script&lang=js&":
/*!**********************************************************************!*\
  !*** ./resources/js/components/select2.vue?vue&type=script&lang=js& ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./select2.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&":
/*!******************************************************************************!*\
  !*** ./resources/js/components/select2.vue?vue&type=style&index=0&lang=css& ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader!../../../node_modules/css-loader??ref--9-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--9-2!../../../node_modules/vue-loader/lib??vue-loader-options!./select2.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_9_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_9_2_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/select2.vue?vue&type=template&id=7e62a13b&":
/*!****************************************************************************!*\
  !*** ./resources/js/components/select2.vue?vue&type=template&id=7e62a13b& ***!
  \****************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./select2.vue?vue&type=template&id=7e62a13b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/select2.vue?vue&type=template&id=7e62a13b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_select2_vue_vue_type_template_id_7e62a13b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/lang.js":
/*!******************************!*\
  !*** ./resources/js/lang.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Lang = __webpack_require__(/*! lang.js */ "./node_modules/lang.js/src/lang.js");

var messages = __webpack_require__(/*! ./messages.js */ "./resources/js/messages.js");

/* harmony default export */ __webpack_exports__["default"] = (new Lang({
  messages: messages
}));

/***/ }),

/***/ "./resources/js/messages.js":
/*!**********************************!*\
  !*** ./resources/js/messages.js ***!
  \**********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  'fr.csv': __webpack_require__(/*! ../lang/fr/csv.php */ "./resources/lang/fr/csv.php")
});

/***/ }),

/***/ "./resources/js/modernizr.js":
/*!***********************************!*\
  !*** ./resources/js/modernizr.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

;

(function (window) {
  var hadGlobal = 'Modernizr' in window;
  var oldGlobal = window.Modernizr;
  /*!
  * modernizr v3.6.0
  * Build https://modernizr.com/download?-filesystem-inputtypes-setclasses-dontmin
  *
  * Copyright (c)
  *  Faruk Ates
  *  Paul Irish
  *  Alex Sexton
  *  Ryan Seddon
  *  Patrick Kettner
  *  Stu Cox
  *  Richard Herrera
  * MIT License
  */

  /*
   * Modernizr tests which native CSS3 and HTML5 features are available in the
   * current UA and makes the results available to you in two ways: as properties on
   * a global `Modernizr` object, and as classes on the `<html>` element. This
   * information allows you to progressively enhance your pages with a granular level
   * of control over the experience.
  */

  ;

  (function (window, document, undefined) {
    var tests = [];
    /**
     *
     * ModernizrProto is the constructor for Modernizr
     *
     * @class
     * @access public
     */

    var ModernizrProto = {
      // The current version, dummy
      _version: '3.6.0',
      // Any settings that don't work as separate modules
      // can go in here as configuration.
      _config: {
        'classPrefix': '',
        'enableClasses': true,
        'enableJSClass': true,
        'usePrefixes': true
      },
      // Queue of tests
      _q: [],
      // Stub these for people who are listening
      on: function on(test, cb) {
        // I don't really think people should do this, but we can
        // safe guard it a bit.
        // -- NOTE:: this gets WAY overridden in src/addTest for actual async tests.
        // This is in case people listen to synchronous tests. I would leave it out,
        // but the code to *disallow* sync tests in the real version of this
        // function is actually larger than this.
        var self = this;
        setTimeout(function () {
          cb(self[test]);
        }, 0);
      },
      addTest: function addTest(name, fn, options) {
        tests.push({
          name: name,
          fn: fn,
          options: options
        });
      },
      addAsyncTest: function addAsyncTest(fn) {
        tests.push({
          name: null,
          fn: fn
        });
      }
    }; // Fake some of Object.create so we can force non test results to be non "own" properties.

    var Modernizr = function Modernizr() {};

    Modernizr.prototype = ModernizrProto; // Leak modernizr globally when you `require` it rather than force it here.
    // Overwrite name so constructor name is nicer :D

    Modernizr = new Modernizr();
    var classes = [];
    /**
     * is returns a boolean if the typeof an obj is exactly type.
     *
     * @access private
     * @function is
     * @param {*} obj - A thing we want to check the type of
     * @param {string} type - A string to compare the typeof against
     * @returns {boolean}
     */

    function is(obj, type) {
      return _typeof(obj) === type;
    }

    ;
    /**
     * Run through all tests and detect their support in the current UA.
     *
     * @access private
     */

    function testRunner() {
      var featureNames;
      var feature;
      var aliasIdx;
      var result;
      var nameIdx;
      var featureName;
      var featureNameSplit;

      for (var featureIdx in tests) {
        if (tests.hasOwnProperty(featureIdx)) {
          featureNames = [];
          feature = tests[featureIdx]; // run the test, throw the return value into the Modernizr,
          // then based on that boolean, define an appropriate className
          // and push it into an array of classes we'll join later.
          //
          // If there is no name, it's an 'async' test that is run,
          // but not directly added to the object. That should
          // be done with a post-run addTest call.

          if (feature.name) {
            featureNames.push(feature.name.toLowerCase());

            if (feature.options && feature.options.aliases && feature.options.aliases.length) {
              // Add all the aliases into the names list
              for (aliasIdx = 0; aliasIdx < feature.options.aliases.length; aliasIdx++) {
                featureNames.push(feature.options.aliases[aliasIdx].toLowerCase());
              }
            }
          } // Run the test, or use the raw value if it's not a function


          result = is(feature.fn, 'function') ? feature.fn() : feature.fn; // Set each of the names on the Modernizr object

          for (nameIdx = 0; nameIdx < featureNames.length; nameIdx++) {
            featureName = featureNames[nameIdx]; // Support dot properties as sub tests. We don't do checking to make sure
            // that the implied parent tests have been added. You must call them in
            // order (either in the test, or make the parent test a dependency).
            //
            // Cap it to TWO to make the logic simple and because who needs that kind of subtesting
            // hashtag famous last words

            featureNameSplit = featureName.split('.');

            if (featureNameSplit.length === 1) {
              Modernizr[featureNameSplit[0]] = result;
            } else {
              // cast to a Boolean, if not one already
              if (Modernizr[featureNameSplit[0]] && !(Modernizr[featureNameSplit[0]] instanceof Boolean)) {
                Modernizr[featureNameSplit[0]] = new Boolean(Modernizr[featureNameSplit[0]]);
              }

              Modernizr[featureNameSplit[0]][featureNameSplit[1]] = result;
            }

            classes.push((result ? '' : 'no-') + featureNameSplit.join('-'));
          }
        }
      }
    }

    ;
    /**
     * docElement is a convenience wrapper to grab the root element of the document
     *
     * @access private
     * @returns {HTMLElement|SVGElement} The root element of the document
     */

    var docElement = document.documentElement;
    /**
     * A convenience helper to check if the document we are running in is an SVG document
     *
     * @access private
     * @returns {boolean}
     */

    var isSVG = docElement.nodeName.toLowerCase() === 'svg';
    /**
     * setClasses takes an array of class names and adds them to the root element
     *
     * @access private
     * @function setClasses
     * @param {string[]} classes - Array of class names
     */
    // Pass in an and array of class names, e.g.:
    //  ['no-webp', 'borderradius', ...]

    function setClasses(classes) {
      var className = docElement.className;
      var classPrefix = Modernizr._config.classPrefix || '';

      if (isSVG) {
        className = className.baseVal;
      } // Change `no-js` to `js` (independently of the `enableClasses` option)
      // Handle classPrefix on this too


      if (Modernizr._config.enableJSClass) {
        var reJS = new RegExp('(^|\\s)' + classPrefix + 'no-js(\\s|$)');
        className = className.replace(reJS, '$1' + classPrefix + 'js$2');
      }

      if (Modernizr._config.enableClasses) {
        // Add the new classes
        className += ' ' + classPrefix + classes.join(' ' + classPrefix);

        if (isSVG) {
          docElement.className.baseVal = className;
        } else {
          docElement.className = className;
        }
      }
    }

    ;
    /**
     * If the browsers follow the spec, then they would expose vendor-specific styles as:
     *   elem.style.WebkitBorderRadius
     * instead of something like the following (which is technically incorrect):
     *   elem.style.webkitBorderRadius
      * WebKit ghosts their properties in lowercase but Opera & Moz do not.
     * Microsoft uses a lowercase `ms` instead of the correct `Ms` in IE8+
     *   erik.eae.net/archives/2008/03/10/21.48.10/
      * More here: github.com/Modernizr/Modernizr/issues/issue/21
     *
     * @access private
     * @returns {string} The string representing the vendor-specific style properties
     */

    var omPrefixes = 'Moz O ms Webkit';
    var cssomPrefixes = ModernizrProto._config.usePrefixes ? omPrefixes.split(' ') : [];
    ModernizrProto._cssomPrefixes = cssomPrefixes;
    /**
     * contains checks to see if a string contains another string
     *
     * @access private
     * @function contains
     * @param {string} str - The string we want to check for substrings
     * @param {string} substr - The substring we want to search the first string for
     * @returns {boolean}
     */

    function contains(str, substr) {
      return !!~('' + str).indexOf(substr);
    }

    ;
    /**
     * createElement is a convenience wrapper around document.createElement. Since we
     * use createElement all over the place, this allows for (slightly) smaller code
     * as well as abstracting away issues with creating elements in contexts other than
     * HTML documents (e.g. SVG documents).
     *
     * @access private
     * @function createElement
     * @returns {HTMLElement|SVGElement} An HTML or SVG element
     */

    function createElement() {
      if (typeof document.createElement !== 'function') {
        // This is the case in IE7, where the type of createElement is "object".
        // For this reason, we cannot call apply() as Object is not a Function.
        return document.createElement(arguments[0]);
      } else if (isSVG) {
        return document.createElementNS.call(document, 'http://www.w3.org/2000/svg', arguments[0]);
      } else {
        return document.createElement.apply(document, arguments);
      }
    }

    ;
    /**
     * Create our "modernizr" element that we do most feature tests on.
     *
     * @access private
     */

    var modElem = {
      elem: createElement('modernizr')
    }; // Clean up this element

    Modernizr._q.push(function () {
      delete modElem.elem;
    });

    var mStyle = {
      style: modElem.elem.style
    }; // kill ref for gc, must happen before mod.elem is removed, so we unshift on to
    // the front of the queue.

    Modernizr._q.unshift(function () {
      delete mStyle.style;
    });
    /**
     * getBody returns the body of a document, or an element that can stand in for
     * the body if a real body does not exist
     *
     * @access private
     * @function getBody
     * @returns {HTMLElement|SVGElement} Returns the real body of a document, or an
     * artificially created element that stands in for the body
     */


    function getBody() {
      // After page load injecting a fake body doesn't work so check if body exists
      var body = document.body;

      if (!body) {
        // Can't use the real body create a fake one.
        body = createElement(isSVG ? 'svg' : 'body');
        body.fake = true;
      }

      return body;
    }

    ;
    /**
     * injectElementWithStyles injects an element with style element and some CSS rules
     *
     * @access private
     * @function injectElementWithStyles
     * @param {string} rule - String representing a css rule
     * @param {function} callback - A function that is used to test the injected element
     * @param {number} [nodes] - An integer representing the number of additional nodes you want injected
     * @param {string[]} [testnames] - An array of strings that are used as ids for the additional nodes
     * @returns {boolean}
     */

    function injectElementWithStyles(rule, callback, nodes, testnames) {
      var mod = 'modernizr';
      var style;
      var ret;
      var node;
      var docOverflow;
      var div = createElement('div');
      var body = getBody();

      if (parseInt(nodes, 10)) {
        // In order not to give false positives we create a node for each test
        // This also allows the method to scale for unspecified uses
        while (nodes--) {
          node = createElement('div');
          node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
          div.appendChild(node);
        }
      }

      style = createElement('style');
      style.type = 'text/css';
      style.id = 's' + mod; // IE6 will false positive on some tests due to the style element inside the test div somehow interfering offsetHeight, so insert it into body or fakebody.
      // Opera will act all quirky when injecting elements in documentElement when page is served as xml, needs fakebody too. #270

      (!body.fake ? div : body).appendChild(style);
      body.appendChild(div);

      if (style.styleSheet) {
        style.styleSheet.cssText = rule;
      } else {
        style.appendChild(document.createTextNode(rule));
      }

      div.id = mod;

      if (body.fake) {
        //avoid crashing IE8, if background image is used
        body.style.background = ''; //Safari 5.13/5.1.4 OSX stops loading if ::-webkit-scrollbar is used and scrollbars are visible

        body.style.overflow = 'hidden';
        docOverflow = docElement.style.overflow;
        docElement.style.overflow = 'hidden';
        docElement.appendChild(body);
      }

      ret = callback(div, rule); // If this is done after page load we don't want to remove the body so check if body exists

      if (body.fake) {
        body.parentNode.removeChild(body);
        docElement.style.overflow = docOverflow; // Trigger layout so kinetic scrolling isn't disabled in iOS6+
        // eslint-disable-next-line

        docElement.offsetHeight;
      } else {
        div.parentNode.removeChild(div);
      }

      return !!ret;
    }

    ;
    /**
     * domToCSS takes a camelCase string and converts it to kebab-case
     * e.g. boxSizing -> box-sizing
     *
     * @access private
     * @function domToCSS
     * @param {string} name - String name of camelCase prop we want to convert
     * @returns {string} The kebab-case version of the supplied name
     */

    function domToCSS(name) {
      return name.replace(/([A-Z])/g, function (str, m1) {
        return '-' + m1.toLowerCase();
      }).replace(/^ms-/, '-ms-');
    }

    ;
    /**
     * wrapper around getComputedStyle, to fix issues with Firefox returning null when
     * called inside of a hidden iframe
     *
     * @access private
     * @function computedStyle
     * @param {HTMLElement|SVGElement} - The element we want to find the computed styles of
     * @param {string|null} [pseudoSelector]- An optional pseudo element selector (e.g. :before), of null if none
     * @returns {CSSStyleDeclaration}
     */

    function computedStyle(elem, pseudo, prop) {
      var result;

      if ('getComputedStyle' in window) {
        result = getComputedStyle.call(window, elem, pseudo);
        var console = window.console;

        if (result !== null) {
          if (prop) {
            result = result.getPropertyValue(prop);
          }
        } else {
          if (console) {
            var method = console.error ? 'error' : 'log';
            console[method].call(console, 'getComputedStyle returning null, its possible modernizr test results are inaccurate');
          }
        }
      } else {
        result = !pseudo && elem.currentStyle && elem.currentStyle[prop];
      }

      return result;
    }

    ;
    /**
     * nativeTestProps allows for us to use native feature detection functionality if available.
     * some prefixed form, or false, in the case of an unsupported rule
     *
     * @access private
     * @function nativeTestProps
     * @param {array} props - An array of property names
     * @param {string} value - A string representing the value we want to check via @supports
     * @returns {boolean|undefined} A boolean when @supports exists, undefined otherwise
     */
    // Accepts a list of property names and a single value
    // Returns `undefined` if native detection not available

    function nativeTestProps(props, value) {
      var i = props.length; // Start with the JS API: http://www.w3.org/TR/css3-conditional/#the-css-interface

      if ('CSS' in window && 'supports' in window.CSS) {
        // Try every prefixed variant of the property
        while (i--) {
          if (window.CSS.supports(domToCSS(props[i]), value)) {
            return true;
          }
        }

        return false;
      } // Otherwise fall back to at-rule (for Opera 12.x)
      else if ('CSSSupportsRule' in window) {
          // Build a condition string for every prefixed variant
          var conditionText = [];

          while (i--) {
            conditionText.push('(' + domToCSS(props[i]) + ':' + value + ')');
          }

          conditionText = conditionText.join(' or ');
          return injectElementWithStyles('@supports (' + conditionText + ') { #modernizr { position: absolute; } }', function (node) {
            return computedStyle(node, null, 'position') == 'absolute';
          });
        }

      return undefined;
    }

    ;
    /**
     * cssToDOM takes a kebab-case string and converts it to camelCase
     * e.g. box-sizing -> boxSizing
     *
     * @access private
     * @function cssToDOM
     * @param {string} name - String name of kebab-case prop we want to convert
     * @returns {string} The camelCase version of the supplied name
     */

    function cssToDOM(name) {
      return name.replace(/([a-z])-([a-z])/g, function (str, m1, m2) {
        return m1 + m2.toUpperCase();
      }).replace(/^-/, '');
    }

    ; // testProps is a generic CSS / DOM property test.
    // In testing support for a given CSS property, it's legit to test:
    //    `elem.style[styleName] !== undefined`
    // If the property is supported it will return an empty string,
    // if unsupported it will return undefined.
    // We'll take advantage of this quick test and skip setting a style
    // on our modernizr element, but instead just testing undefined vs
    // empty string.
    // Property names can be provided in either camelCase or kebab-case.

    function testProps(props, prefixed, value, skipValueTest) {
      skipValueTest = is(skipValueTest, 'undefined') ? false : skipValueTest; // Try native detect first

      if (!is(value, 'undefined')) {
        var result = nativeTestProps(props, value);

        if (!is(result, 'undefined')) {
          return result;
        }
      } // Otherwise do it properly


      var afterInit, i, propsLength, prop, before; // If we don't have a style element, that means we're running async or after
      // the core tests, so we'll need to create our own elements to use
      // inside of an SVG element, in certain browsers, the `style` element is only
      // defined for valid tags. Therefore, if `modernizr` does not have one, we
      // fall back to a less used element and hope for the best.
      // for strict XHTML browsers the hardly used samp element is used

      var elems = ['modernizr', 'tspan', 'samp'];

      while (!mStyle.style && elems.length) {
        afterInit = true;
        mStyle.modElem = createElement(elems.shift());
        mStyle.style = mStyle.modElem.style;
      } // Delete the objects if we created them.


      function cleanElems() {
        if (afterInit) {
          delete mStyle.style;
          delete mStyle.modElem;
        }
      }

      propsLength = props.length;

      for (i = 0; i < propsLength; i++) {
        prop = props[i];
        before = mStyle.style[prop];

        if (contains(prop, '-')) {
          prop = cssToDOM(prop);
        }

        if (mStyle.style[prop] !== undefined) {
          // If value to test has been passed in, do a set-and-check test.
          // 0 (integer) is a valid property value, so check that `value` isn't
          // undefined, rather than just checking it's truthy.
          if (!skipValueTest && !is(value, 'undefined')) {
            // Needs a try catch block because of old IE. This is slow, but will
            // be avoided in most cases because `skipValueTest` will be used.
            try {
              mStyle.style[prop] = value;
            } catch (e) {} // If the property value has changed, we assume the value used is
            // supported. If `value` is empty string, it'll fail here (because
            // it hasn't changed), which matches how browsers have implemented
            // CSS.supports()


            if (mStyle.style[prop] != before) {
              cleanElems();
              return prefixed == 'pfx' ? prop : true;
            }
          } // Otherwise just return true, or the property name if this is a
          // `prefixed()` call
          else {
              cleanElems();
              return prefixed == 'pfx' ? prop : true;
            }
        }
      }

      cleanElems();
      return false;
    }

    ;
    /**
     * List of JavaScript DOM values used for tests
     *
     * @memberof Modernizr
     * @name Modernizr._domPrefixes
     * @optionName Modernizr._domPrefixes
     * @optionProp domPrefixes
     * @access public
     * @example
     *
     * Modernizr._domPrefixes is exactly the same as [_prefixes](#modernizr-_prefixes), but rather
     * than kebab-case properties, all properties are their Capitalized variant
     *
     * ```js
     * Modernizr._domPrefixes === [ "Moz", "O", "ms", "Webkit" ];
     * ```
     */

    var domPrefixes = ModernizrProto._config.usePrefixes ? omPrefixes.toLowerCase().split(' ') : [];
    ModernizrProto._domPrefixes = domPrefixes;
    /**
     * fnBind is a super small [bind](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/bind) polyfill.
     *
     * @access private
     * @function fnBind
     * @param {function} fn - a function you want to change `this` reference to
     * @param {object} that - the `this` you want to call the function with
     * @returns {function} The wrapped version of the supplied function
     */

    function fnBind(fn, that) {
      return function () {
        return fn.apply(that, arguments);
      };
    }

    ;
    /**
     * testDOMProps is a generic DOM property test; if a browser supports
     *   a certain property, it won't return undefined for it.
     *
     * @access private
     * @function testDOMProps
     * @param {array.<string>} props - An array of properties to test for
     * @param {object} obj - An object or Element you want to use to test the parameters again
     * @param {boolean|object} elem - An Element to bind the property lookup again. Use `false` to prevent the check
     * @returns {false|*} returns false if the prop is unsupported, otherwise the value that is supported
     */

    function testDOMProps(props, obj, elem) {
      var item;

      for (var i in props) {
        if (props[i] in obj) {
          // return the property name as a string
          if (elem === false) {
            return props[i];
          }

          item = obj[props[i]]; // let's bind a function

          if (is(item, 'function')) {
            // bind to obj unless overriden
            return fnBind(item, elem || obj);
          } // return the unbound function or obj or value


          return item;
        }
      }

      return false;
    }

    ;
    /**
     * testPropsAll tests a list of DOM properties we want to check against.
     * We specify literally ALL possible (known and/or likely) properties on
     * the element including the non-vendor prefixed one, for forward-
     * compatibility.
     *
     * @access private
     * @function testPropsAll
     * @param {string} prop - A string of the property to test for
     * @param {string|object} [prefixed] - An object to check the prefixed properties on. Use a string to skip
     * @param {HTMLElement|SVGElement} [elem] - An element used to test the property and value against
     * @param {string} [value] - A string of a css value
     * @param {boolean} [skipValueTest] - An boolean representing if you want to test if value sticks when set
     * @returns {false|string} returns the string version of the property, or false if it is unsupported
     */

    function testPropsAll(prop, prefixed, elem, value, skipValueTest) {
      var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
          props = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' '); // did they call .prefixed('boxSizing') or are we just testing a prop?

      if (is(prefixed, 'string') || is(prefixed, 'undefined')) {
        return testProps(props, prefixed, value, skipValueTest); // otherwise, they called .prefixed('requestAnimationFrame', window[, elem])
      } else {
        props = (prop + ' ' + domPrefixes.join(ucProp + ' ') + ucProp).split(' ');
        return testDOMProps(props, prefixed, elem);
      }
    } // Modernizr.testAllProps() investigates whether a given style property,
    // or any of its vendor-prefixed variants, is recognized
    //
    // Note that the property names must be provided in the camelCase variant.
    // Modernizr.testAllProps('boxSizing')


    ModernizrProto.testAllProps = testPropsAll;
    /**
     * atRule returns a given CSS property at-rule (eg @keyframes), possibly in
     * some prefixed form, or false, in the case of an unsupported rule
     *
     * @memberof Modernizr
     * @name Modernizr.atRule
     * @optionName Modernizr.atRule()
     * @optionProp atRule
     * @access public
     * @function atRule
     * @param {string} prop - String name of the @-rule to test for
     * @returns {string|boolean} The string representing the (possibly prefixed)
     * valid version of the @-rule, or `false` when it is unsupported.
     * @example
     * ```js
     *  var keyframes = Modernizr.atRule('@keyframes');
     *
     *  if (keyframes) {
     *    // keyframes are supported
     *    // could be `@-webkit-keyframes` or `@keyframes`
     *  } else {
     *    // keyframes === `false`
     *  }
     * ```
     *
     */

    var atRule = function atRule(prop) {
      var length = prefixes.length;
      var cssrule = window.CSSRule;
      var rule;

      if (typeof cssrule === 'undefined') {
        return undefined;
      }

      if (!prop) {
        return false;
      } // remove literal @ from beginning of provided property


      prop = prop.replace(/^@/, ''); // CSSRules use underscores instead of dashes

      rule = prop.replace(/-/g, '_').toUpperCase() + '_RULE';

      if (rule in cssrule) {
        return '@' + prop;
      }

      for (var i = 0; i < length; i++) {
        // prefixes gives us something like -o-, and we want O_
        var prefix = prefixes[i];
        var thisRule = prefix.toUpperCase() + '_' + rule;

        if (thisRule in cssrule) {
          return '@-' + prefix.toLowerCase() + '-' + prop;
        }
      }

      return false;
    };

    ModernizrProto.atRule = atRule;
    /**
     * prefixed returns the prefixed or nonprefixed property name variant of your input
     *
     * @memberof Modernizr
     * @name Modernizr.prefixed
     * @optionName Modernizr.prefixed()
     * @optionProp prefixed
     * @access public
     * @function prefixed
     * @param {string} prop - String name of the property to test for
     * @param {object} [obj] - An object to test for the prefixed properties on
     * @param {HTMLElement} [elem] - An element used to test specific properties against
     * @returns {string|false} The string representing the (possibly prefixed) valid
     * version of the property, or `false` when it is unsupported.
     * @example
     *
     * Modernizr.prefixed takes a string css value in the DOM style camelCase (as
     * opposed to the css style kebab-case) form and returns the (possibly prefixed)
     * version of that property that the browser actually supports.
     *
     * For example, in older Firefox...
     * ```js
     * prefixed('boxSizing')
     * ```
     * returns 'MozBoxSizing'
     *
     * In newer Firefox, as well as any other browser that support the unprefixed
     * version would simply return `boxSizing`. Any browser that does not support
     * the property at all, it will return `false`.
     *
     * By default, prefixed is checked against a DOM element. If you want to check
     * for a property on another object, just pass it as a second argument
     *
     * ```js
     * var rAF = prefixed('requestAnimationFrame', window);
     *
     * raf(function() {
     *  renderFunction();
     * })
     * ```
     *
     * Note that this will return _the actual function_ - not the name of the function.
     * If you need the actual name of the property, pass in `false` as a third argument
     *
     * ```js
     * var rAFProp = prefixed('requestAnimationFrame', window, false);
     *
     * rafProp === 'WebkitRequestAnimationFrame' // in older webkit
     * ```
     *
     * One common use case for prefixed is if you're trying to determine which transition
     * end event to bind to, you might do something like...
     * ```js
     * var transEndEventNames = {
     *     'WebkitTransition' : 'webkitTransitionEnd', * Saf 6, Android Browser
     *     'MozTransition'    : 'transitionend',       * only for FF < 15
     *     'transition'       : 'transitionend'        * IE10, Opera, Chrome, FF 15+, Saf 7+
     * };
     *
     * var transEndEventName = transEndEventNames[ Modernizr.prefixed('transition') ];
     * ```
     *
     * If you want a similar lookup, but in kebab-case, you can use [prefixedCSS](#modernizr-prefixedcss).
     */

    var prefixed = ModernizrProto.prefixed = function (prop, obj, elem) {
      if (prop.indexOf('@') === 0) {
        return atRule(prop);
      }

      if (prop.indexOf('-') != -1) {
        // Convert kebab-case to camelCase
        prop = cssToDOM(prop);
      }

      if (!obj) {
        return testPropsAll(prop, 'pfx');
      } else {
        // Testing DOM property e.g. Modernizr.prefixed('requestAnimationFrame', window) // 'mozRequestAnimationFrame'
        return testPropsAll(prop, obj, elem);
      }
    };
    /*!
    {
      "name": "Filesystem API",
      "property": "filesystem",
      "caniuse": "filesystem",
      "notes": [{
        "name": "W3 Draft",
        "href": "http://dev.w3.org/2009/dap/file-system/file-dir-sys.html"
      }],
      "authors": ["Eric Bidelman (@ebidel)"],
      "tags": ["file"],
      "builderAliases": ["file_filesystem"],
      "knownBugs": ["The API will be present in Chrome incognito, but will throw an exception. See crbug.com/93417"]
    }
    !*/


    Modernizr.addTest('filesystem', !!prefixed('requestFileSystem', window));
    /**
     * since we have a fairly large number of input tests that don't mutate the input
     * we create a single element that can be shared with all of those tests for a
     * minor perf boost
     *
     * @access private
     * @returns {HTMLInputElement}
     */

    var inputElem = createElement('input');
    /*!
    {
      "name": "Form input types",
      "property": "inputtypes",
      "caniuse": "forms",
      "tags": ["forms"],
      "authors": ["Mike Taylor"],
      "polyfills": [
        "jquerytools",
        "webshims",
        "h5f",
        "webforms2",
        "nwxforms",
        "fdslider",
        "html5slider",
        "galleryhtml5forms",
        "jscolor",
        "html5formshim",
        "selectedoptionsjs",
        "formvalidationjs"
      ]
    }
    !*/

    /* DOC
    Detects support for HTML5 form input types and exposes Boolean subproperties with the results:
    
    ```javascript
    Modernizr.inputtypes.color
    Modernizr.inputtypes.date
    Modernizr.inputtypes.datetime
    Modernizr.inputtypes['datetime-local']
    Modernizr.inputtypes.email
    Modernizr.inputtypes.month
    Modernizr.inputtypes.number
    Modernizr.inputtypes.range
    Modernizr.inputtypes.search
    Modernizr.inputtypes.tel
    Modernizr.inputtypes.time
    Modernizr.inputtypes.url
    Modernizr.inputtypes.week
    ```
    */
    // Run through HTML5's new input types to see if the UA understands any.
    //   This is put behind the tests runloop because it doesn't return a
    //   true/false like all the other tests; instead, it returns an object
    //   containing each input type with its corresponding true/false value
    // Big thanks to @miketaylr for the html5 forms expertise. miketaylr.com/

    var inputtypes = 'search tel url email datetime date month week time datetime-local number range color'.split(' ');
    var inputs = {};

    Modernizr.inputtypes = function (props) {
      var len = props.length;
      var smile = '1)';
      var inputElemType;
      var defaultView;
      var bool;

      for (var i = 0; i < len; i++) {
        inputElem.setAttribute('type', inputElemType = props[i]);
        bool = inputElem.type !== 'text' && 'style' in inputElem; // We first check to see if the type we give it sticks..
        // If the type does, we feed it a textual value, which shouldn't be valid.
        // If the value doesn't stick, we know there's input sanitization which infers a custom UI

        if (bool) {
          inputElem.value = smile;
          inputElem.style.cssText = 'position:absolute;visibility:hidden;';

          if (/^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined) {
            docElement.appendChild(inputElem);
            defaultView = document.defaultView; // Safari 2-4 allows the smiley as a value, despite making a slider

            bool = defaultView.getComputedStyle && defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' && // Mobile android web browser has false positive, so must
            // check the height to see if the widget is actually there.
            inputElem.offsetHeight !== 0;
            docElement.removeChild(inputElem);
          } else if (/^(search|tel)$/.test(inputElemType)) {// Spec doesn't define any special parsing or detectable UI
            //   behaviors so we pass these through as true
            // Interestingly, opera fails the earlier test, so it doesn't
            //  even make it here.
          } else if (/^(url|email)$/.test(inputElemType)) {
            // Real url and email support comes with prebaked validation.
            bool = inputElem.checkValidity && inputElem.checkValidity() === false;
          } else {
            // If the upgraded input compontent rejects the :) text, we got a winner
            bool = inputElem.value != smile;
          }
        }

        inputs[props[i]] = !!bool;
      }

      return inputs;
    }(inputtypes); // Run each test


    testRunner(); // Remove the "no-js" class if it exists

    setClasses(classes);
    delete ModernizrProto.addTest;
    delete ModernizrProto.addAsyncTest; // Run the things that are supposed to run after the tests

    for (var i = 0; i < Modernizr._q.length; i++) {
      Modernizr._q[i]();
    } // Leak Modernizr namespace


    window.Modernizr = Modernizr;
    ;
  })(window, document);

  module.exports = window.Modernizr;

  if (hadGlobal) {
    window.Modernizr = oldGlobal;
  } else {
    delete window.Modernizr;
  }
})(window);

/***/ }),

/***/ "./resources/js/randomForm.js":
/*!************************************!*\
  !*** ./resources/js/randomForm.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global, $) {/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var alertify_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! alertify.js */ "./node_modules/alertify.js/dist/js/alertify.js");
/* harmony import */ var alertify_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(alertify_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _smsTools_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./smsTools.js */ "./resources/js/smsTools.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_autosize__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-autosize */ "./node_modules/vue-autosize/src/index.js");
/* harmony import */ var vue_autosize__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(vue_autosize__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _modernizr_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./modernizr.js */ "./resources/js/modernizr.js");
/* harmony import */ var _modernizr_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_modernizr_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! moment */ "./node_modules/moment/moment.js");
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(moment__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var papaparse__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! papaparse */ "./node_modules/papaparse/papaparse.min.js");
/* harmony import */ var papaparse__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(papaparse__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _lang_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./lang.js */ "./resources/js/lang.js");
/* harmony import */ var _components_select2_vue__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/select2.vue */ "./resources/js/components/select2.vue");
/* harmony import */ var _ajaxVue_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./ajaxVue.js */ "./resources/js/ajaxVue.js");





vue__WEBPACK_IMPORTED_MODULE_3___default.a.use(vue_autosize__WEBPACK_IMPORTED_MODULE_4___default.a);




_lang_js__WEBPACK_IMPORTED_MODULE_8__["default"].setLocale(window.global.lang);


window.app = new vue__WEBPACK_IMPORTED_MODULE_3___default.a({
  mixins: [_ajaxVue_js__WEBPACK_IMPORTED_MODULE_10__["default"]],
  el: '#form',
  data: {
    participants: [],
    smsContent: '',
    maxSms: global.maxSms,
    dearsanta: false,
    date: window.now,
    showModal: false,
    importing: false
  },
  components: {
    csv: {
      template: '#csv-template',
      components: {
        modal: {
          template: '#modal-template'
        }
      },
      methods: {
        emitSubmit: function emitSubmit() {
          this.$emit('import', $('#uploadCsv input[type=file]')[0].files[0]);
          this.$emit('close');
        },
        emitCancel: function emitCancel() {
          this.$emit('close');
        }
      }
    },
    participant: {
      template: '#participant-template',
      props: {
        idx: {
          type: Number,
          required: true
        },
        name: {
          type: String,
          default: ''
        },
        email: {
          type: String,
          default: ''
        },
        phone: {
          type: String,
          default: ''
        },
        participants: {
          type: Array,
          required: true
        },
        dearsanta: {
          type: Boolean,
          default: false
        },
        smsdisabled: {
          type: Boolean,
          default: false
        }
      },
      data: function data() {
        return {
          exclusions: []
        };
      },
      components: {
        select2: _components_select2_vue__WEBPACK_IMPORTED_MODULE_9__["default"]
      },
      computed: {
        participantNames: function participantNames() {
          var names = [];
          this.participants.forEach(function (participant, idx) {
            if (participant.name && idx !== this.idx) {
              names.push({
                id: participant.id,
                value: idx,
                text: participant.name
              });
            }
          }.bind(this));
          return names;
        },
        phoneNumber: function phoneNumber() {
          if (this.phone.length) {
            return this.phone[0] === '0' ? this.phone.match(/[0-9]{1,2}/g).join(' ') : [this.phone[0]].concat(this.phone.slice(1).match(/[0-9]{1,2}/g)).join(' ');
          } else {
            return this.phone;
          }
        }
      },
      watch: {
        name: function name() {
          this.$emit('changename', this.name);
        },
        email: function email() {
          this.$emit('changeemail', this.email);
        },
        phone: function phone() {
          this.$emit('changephone', this.phone);
        }
      }
    }
  },
  computed: {
    emailUsed: function emailUsed() {
      var used = false;

      for (var i in this.participants) {
        used = used || !!this.participants[i].email;
      }

      return used;
    },
    phoneUsed: function phoneUsed() {
      var used = false;

      for (var i in this.participants) {
        used = used || !!this.participants[i].phone;
      }

      return used;
    },
    allMails: function allMails() {
      var allMails = true;
      this.participants.forEach(function (participant) {
        allMails = allMails && (participant.name === '' || participant.email !== '');
      });
      return allMails;
    },
    longuestName: function longuestName() {
      var name = '';
      this.participants.forEach(function (participant) {
        name = participant.name && _smsTools_js__WEBPACK_IMPORTED_MODULE_2__["default"].length(participant.name) > _smsTools_js__WEBPACK_IMPORTED_MODULE_2__["default"].length(name) ? participant.name : name;
      });
      return name;
    },
    maxSmsContent: function maxSmsContent() {
      return this.smsContent.replace('{SANTA}', this.longuestName).replace('{TARGET}', this.longuestName);
    },
    smsCount: function smsCount() {
      var smsCount = _smsTools_js__WEBPACK_IMPORTED_MODULE_2__["default"].chunk(this.maxSmsContent).length;
      return this.maxSms ? Math.min(smsCount, this.maxSms) : smsCount;
    },
    charactersLeft: function charactersLeft() {
      return _smsTools_js__WEBPACK_IMPORTED_MODULE_2__["default"].chunkMaxLength(this.maxSmsContent, this.smsCount, true) - this.maxSmsContent.length;
    },
    maxLength: function maxLength() {
      return _smsTools_js__WEBPACK_IMPORTED_MODULE_2__["default"].chunkMaxLength(this.maxSmsContent, this.maxSms, true);
    }
  },
  created: function created() {
    this.addParticipant();
    this.addParticipant();
    this.addParticipant();
    vue__WEBPACK_IMPORTED_MODULE_3___default.a.nextTick(function () {
      if (!_modernizr_js__WEBPACK_IMPORTED_MODULE_5___default.a.inputtypes.date) {
        $('input[type=date]', this.$el).datepicker({
          // Consistent format with the HTML5 picker
          dateFormat: 'yy-mm-dd',
          minDate: moment__WEBPACK_IMPORTED_MODULE_6___default()(this.now).add(1, 'day').toDate(),
          maxDate: moment__WEBPACK_IMPORTED_MODULE_6___default()(this.now).add(1, 'year').toDate()
        });
      }

      if (!_modernizr_js__WEBPACK_IMPORTED_MODULE_5___default.a.filereader) {
        $('.participants-imports').remove();
      }
    }.bind(this));
  },
  filters: {
    moment: function moment(date, amount, unit) {
      return moment__WEBPACK_IMPORTED_MODULE_6___default()(date).add(amount, unit).format("YYYY-MM-DD");
    }
  },
  watch: {
    sending: function sending(newVal) {
      // If we reset the sending status, reset the captcha
      if (!newVal) {
        grecaptcha.reset();
      }
    },
    sent: function sent(newVal) {
      // If sent is a success, scroll to the message
      if (newVal) {
        $.scrollTo('#form .row', 800, {
          offset: -120
        });
      }
    },
    errors: function errors(newVal) {
      // If there's new errors, scroll to them
      if (newVal.length) {
        $.scrollTo('#form .row', 800, {
          offset: -120
        });
      }
    },
    allMails: function allMails(newVal) {
      this.dearsanta = this.dearsanta && newVal;
    }
  },
  methods: {
    resetParticipants: function resetParticipants() {
      this.participants = [];
    },
    addParticipant: function addParticipant(name, email, phone) {
      this.participants.push({
        name: name,
        email: email,
        phone: phone,
        id: 'id' + this.participants.length + new Date().getTime()
      });
    },
    importParticipants: function importParticipants(file) {
      this.importing = true;
      var test = papaparse__WEBPACK_IMPORTED_MODULE_7___default.a.parse(file, {
        error: function error() {
          this.importing = false;
          alertify_js__WEBPACK_IMPORTED_MODULE_1___default.a.alert(_lang_js__WEBPACK_IMPORTED_MODULE_8__["default"].get('csv.importError'));
        },
        complete: function (file) {
          this.importing = false;
          this.resetParticipants();
          file.data.forEach(function (participant) {
            if (participant[0] !== '') {
              this.addParticipant(participant[0], participant[1], participant[2]);
            }
          }.bind(this));

          if (this.participants.length < 3) {
            for (var i = 0; i < 3 - this.participants.length; i++) {
              this.addParticipant();
            }
          }

          alertify_js__WEBPACK_IMPORTED_MODULE_1___default.a.alert(_lang_js__WEBPACK_IMPORTED_MODULE_8__["default"].get('csv.importSuccess'));
        }.bind(this)
      });
    }
  }
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js"), __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./resources/js/smsTools.js":
/*!**********************************!*\
  !*** ./resources/js/smsTools.js ***!
  \**********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ((function () {
  var lengths = {
    ascii: [160, 146, 153],
    unicode: [70, 62, 66]
  };
  return {
    isUnicode: function isUnicode(text) {
      for (var charPos = 0; charPos < text.length; charPos++) {
        if (text.charCodeAt(charPos) > 127 && text[charPos] != "€") {
          return true;
        }
      }

      return false;
    },
    length: function length(text) {
      var smsLength = 0;

      for (var charPos = 0; charPos < text.length; charPos++) {
        switch (text[charPos]) {
          case "\n":
          case "[":
          case "]":
          case "\\":
          case "^":
          case "{":
          case "}":
          case "|":
          case "€":
            smsLength += 2;
            break;

          default:
            smsLength += 1;
        }
      }

      return smsLength;
    },
    chunkMaxLength: function chunkMaxLength(text, chunkQuantity, adaptLength) {
      var limits = this.isUnicode(text) ? lengths.unicode : lengths.ascii;
      var limit = limits[0];

      for (var i = 1; i < chunkQuantity; i++) {
        limit += limits[Math.min(2, i)];
      }

      if (adaptLength) {
        limit -= this.length(text) - text.length;
      }

      return limit;
    },
    chunkLengthLeft: function chunkLengthLeft(text) {
      var chunks = this.chunk(text);
      var maxLength = this.chunkMaxLength(text, chunks.length);
      return maxLength - this.length(text);
    },
    chunk: function chunk(text) {
      var chunks = [];
      var limits = this.isUnicode(text) ? lengths.unicode : lengths.ascii;
      var limit = limits[0];
      var chunk = '';

      while (text.length) {
        if (this.length(chunk + text[0]) > limit) {
          chunks.push(chunk);
          chunk = '';
          limit = limits[Math.min(2, chunks.length)];
        }

        chunk = chunk + text[0];
        text = text.substr(1);
      }

      if (chunk) chunks.push(chunk);
      return chunks;
    }
  };
})());

/***/ }),

/***/ "./resources/lang/fr/csv.php":
/*!***********************************!*\
  !*** ./resources/lang/fr/csv.php ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = {"importError":"Une erreur est survenue lors de l'import.","importSuccess":"L'import a été effectué avec succès."};

/***/ }),

/***/ 1:
/*!******************************************!*\
  !*** multi ./resources/js/randomForm.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/secretsanta.fr/dev/resources/js/randomForm.js */"./resources/js/randomForm.js");


/***/ })

},[[1,"/js/manifest","/js/vendor"]]]);