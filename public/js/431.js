"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[431],{7726:function(e,t,r){r.r(t);r(7941),r(2479);t.default={props:{questions:{type:Object,required:!0}},data:function(){return{selectedCategory:Object.keys(this.questions)[0],categories:Object.keys(this.questions),qnas:this.questions,showed:{}}},computed:{selectedQuestions:function(){return Object.keys(this.qnas[this.selectedCategory])},selectedAnswers:function(){return Object.values(this.qnas[this.selectedCategory])}},watch:{selectedCategory:function(){this.showed={}}}}},4508:function(e,t,r){r.r(t);var n=r(1519),a=r.n(n)()((function(e){return e[1]}));a.push([e.id,'#content{padding-bottom:60px;padding-top:140px}.fade-enter-active,.fade-leave-active{transition:all .5s}.fade-enter,.fade-leave-to{opacity:0}.card{margin:10px 60px}.card .card-header{cursor:pointer}.card .card-header:after{border-color:#000;border-style:solid;border-width:.2rem 0 0 .2rem;content:"";height:1.5rem;position:absolute;right:1.25rem;top:1rem;transform:rotate(45deg);transform-origin:.5rem .5rem;transition:all 1s;width:1.5rem}.card .card-header[aria-expanded=true]:after{transform:rotate(45deg) scaleX(-1) scaleY(-1)}',""]),t.default=a},6318:function(e,t,r){r.r(t);var n=r(3379),a=r.n(n),s=r(4508),i={insert:"head",singleton:!1};a()(s.default,i);t.default=s.default.locals||{}},6431:function(e,t,r){r.r(t);var n=r(280),a=r(7123),s={};for(var i in a)"default"!==i&&(s[i]=function(e){return a[e]}.bind(0,i));r.d(t,s);r(7658);var o=(0,r(1900).default)(a.default,n.render,n.staticRenderFns,!1,null,null,null);t.default=o.exports},7123:function(e,t,r){r.r(t);var n=r(7726),a={};for(var s in n)"default"!==s&&(a[s]=function(e){return n[e]}.bind(0,s));r.d(t,a),t.default=n.default},7658:function(e,t,r){r.r(t);var n=r(6318),a={};for(var s in n)"default"!==s&&(a[s]=function(e){return n[e]}.bind(0,s));r.d(t,a)},280:function(e,t,r){r.r(t);var n=r(9352),a={};for(var s in n)"default"!==s&&(a[s]=function(e){return n[e]}.bind(0,s));r.d(t,a)},9352:function(e,t,r){r.r(t),r.d(t,{render:function(){return n},staticRenderFns:function(){return a}});var n=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",[r("ul",{staticClass:"nav nav-tabs"},e._l(e.categories,(function(t,n){return r("li",{key:n,staticClass:"nav-item"},[r("a",{class:{"nav-link":!0,active:e.selectedCategory===t},attrs:{href:"#"},on:{click:function(r){e.selectedCategory=t}}},[e._v(e._s(e.$t("faq.categories."+t)))])])})),0),e._v(" "),e._l(e.selectedQuestions,(function(t,n){return r("div",{key:e.selectedCategory+"_"+n,staticClass:"card"},[r("p",{staticClass:"card-header",attrs:{id:"question"+n,"aria-expanded":e.showed[n],"aria-controls":"answer"+n},on:{click:function(t){return e.$set(e.showed,n,!e.showed[n])}}},[e._v(e._s(t))]),e._v(" "),r("transition",{attrs:{name:"fade"}},[r("div",{directives:[{name:"show",rawName:"v-show",value:e.showed[n],expression:"showed[i]"}],staticClass:"card-body",attrs:{id:"answer"+n}},[r("p",[e._v(e._s(e.selectedAnswers[n]))])])])],1)}))],2)},a=[]}}]);