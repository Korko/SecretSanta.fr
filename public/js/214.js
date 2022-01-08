"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[214],{2554:function(a,t,e){e.r(t);var n=e(9755),r=e.n(n),i=e(9088),d=e(2333);t.default={props:{action:{type:String,default:""},button:{type:Boolean,default:!0},buttonSend:{type:String,default:""},buttonSending:{type:String,default:""},buttonSent:{type:String,default:""},buttonReset:{type:String,default:""},v$:{type:Object,default:null},sendIcon:{type:String,default:"paper-plane"},autoReset:{type:Boolean,default:!1}},data:function(){return{fieldErrors:[],sending:!1,sent:!1}},watch:{sending:function(){this.$emit("change",this.sending)}},methods:{fieldError:function(a){return this.fieldErrors[a]?this.fieldErrors[a][0]:null},call:function(a,t){var e=this;if(!this.sending&&!this.sent)return this.v$&&this.v$.$touch(),(!this.v$||!this.v$.$invalid)&&(this.sending=!0,(0,i.default)(a,"POST",t.data).then((function(a){e.fieldErrors=[],e.sending=!1,e.autoReset||(e.sent=!0),(t.success||t.then||function(){})(a),(t.complete||t.finally||function(){})(),e.$emit("success",a),e.autoReset&&e.onReset()})).catch((function(a){var n,r;a&&a.errors&&(e.fieldErrors=a.errors),e.sending=!1,(n=t.error||t.catch)&&n(a),(r=t.complete||t.finally)&&r(),!n&&!r&&e.fieldErrors.length>0&&d.default.errorAlert(e.$t("form.internalError")),e.$emit("error")})))},onSubmit:function(){this.submit()},onReset:function(){this.$emit("reset"),this.fieldErrors=[],this.v$.$reset(),this.sending=!1,this.sent=!1},submit:function(a,t){this.$emit("beforeSubmit"),a=a||r()(this.$el).serialize();var e=this.call(this.action,Object.assign({data:a},t));return this.$emit("afterSubmit"),e}}}},4509:function(a,t,e){e.r(t);e(8253);var n=e(4028),r=e(2587),i=e(9088),d=e(7936),c=e(5565),o=e(1573),s=e(4667),l=e(388);function f(a,t,e){return t in a?Object.defineProperty(a,t,{value:e,enumerable:!0,configurable:!0,writable:!0}):a[t]=e,a}t.default={components:{EmailStatus:o.default,Tooltip:s.default},extends:l.default,setup:function(){return{v$:(0,n.useVuelidate)()}},validations:function(){return{content:{required:r.required}}},props:{draw:{type:Object,required:!0},participant:{type:Object,required:!0},emails:{type:Object,required:!0},routes:{type:Object,required:!0},resendEmailUrls:{type:Object,required:!0},resendTargetEmailsUrl:{type:Object,required:!0},targetDearSantaLastUpdate:{type:Date,required:!0}},data:function(){return{content:""}},computed:{emailsByDate:function(){return Object.values(this.emails).map((function(a){return Object.assign(a,a.mail)})).sort((function(a,t){return new Date(a.created_at)>new Date(t.created_at)?-1:1})).map((function(a){return a.created_at=new Date(a.created_at).toLocaleString("fr-FR"),a}))||[]},checkUpdates:function(){return!!Object.values(this.emails).find((function(a){return"error"!==a.mail.delivery_status}))},recentTargetDearSanta:function(){return(new Date).getTime()-new Date(this.targetDearSantaLastUpdate).getTime()<3e5}},created:function(){var a=this;d.default.channel("draw."+this.draw.hash).listen(".mail.update",(function(t){a.emails[t.id]&&(a.emails[t.id].mail.delivery_status=t.delivery_status,a.emails[t.id].mail.updated_at=t.updated_at)})),window.localStorage.setItem("secretsanta",JSON.stringify((0,c.deepMerge)(JSON.parse(window.localStorage.getItem("secretsanta"))||{},f({},this.draw.hash,{title:this.draw.mail_title,creation:this.draw.created_at,expiration:this.draw.expires_at,organizerName:this.draw.organizer_name,links:f({},this.participant.hash,{name:this.participant.name,link:window.location.href})}))))},methods:{success:function(a){a.email.updated_at||(a.email.updated_at=new Date),this.$set(this.emails,a.email.id,a.email)},reset:function(){this.content=""},resend:function(a){return this.emails[a].mail.delivery_status="created",this.emails[a].mail.updated_at=(new Date).getTime(),(0,i.default)(this.resendEmailUrls[a])},resend_target:function(){return this.targetDearSantaLastUpdate=(new Date).getTime(),(0,i.default)(this.resendTargetEmailsUrl)},nl2br:function(a){return a.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br />$2")},e:function(a){var t=document.createElement("p");return t.appendChild(document.createTextNode(a)),t.innerHTML}}}},6623:function(a,t,e){e.r(t);var n=e(4667);t.default={components:{Tooltip:n.default},props:{can_redo:{type:Boolean,default:!0},delivery_status:{type:String,required:!0},last_update:{type:[String,Number],default:null},disabled:{type:Boolean,default:!1},rateLimit:{type:Number,default:3e5}},data:function(){return{recent:!0,recentUpdateTimeout:null}},computed:{icon:function(){return{created:"fas fa-spinner",sending:"fas fa-spinner",sent:"fas fa-check",received:"fas fa-check",error:"fas fa-exclamation-triangle"}[this.delivery_status]}},watch:{last_update:{immediate:!0,handler:function(){var a=this;this.recent=this.isRecent(),this.recent&&(this.recentUpdateTimeout&&clearTimeout(this.recentUpdateTimeout),this.recentUpdateTimeout=setTimeout((function(){a.recent=a.isRecent()}),this.rateLimit-this.getDelay()))}}},methods:{getDelay:function(){return(new Date).getTime()-new Date(this.last_update).getTime()},isRecent:function(){return this.getDelay()<this.rateLimit}}}},4077:function(a,t,e){e.r(t);var n=e(8203);t.default={components:{AjaxForm:n.default}}},1164:function(a,t,e){e.r(t),t.default={props:{direction:{type:String,validator:function(a){return-1!==["top","left","bottom","right"].indexOf(a)},default:"right"}}}},9959:function(a,t,e){e.r(t),e.d(t,{render:function(){return m}});var n=e(8253),r=["action"],i=["disabled"],d={key:0},c=["disabled"],o={key:0},s=(0,n.createElementVNode)("span",{class:"fas fa-check-circle"},null,-1),l={key:1},f=(0,n.createElementVNode)("span",{class:"fas fa-spinner"},null,-1),u={key:2},p={key:0,type:"reset",class:"btn btn-primary btn-lg"},v=(0,n.createElementVNode)("span",{class:"fas fa-backward"},null,-1);function m(a,t,e,m,h,g){return(0,n.openBlock)(),(0,n.createElementBlock)("form",{action:e.action,method:"post",autocomplete:"off",onSubmit:t[0]||(t[0]=(0,n.withModifiers)((function(){return g.onSubmit&&g.onSubmit.apply(g,arguments)}),["prevent"])),onReset:t[1]||(t[1]=(0,n.withModifiers)((function(){return g.onReset&&g.onReset.apply(g,arguments)}),["prevent"]))},[(0,n.createElementVNode)("fieldset",{disabled:a.sending||a.sent},[(0,n.renderSlot)(a.$slots,"default",(0,n.normalizeProps)((0,n.guardReactiveProps)({sending:a.sending,sent:a.sent,submit:g.submit,onSubmit:g.onSubmit,onReset:g.onReset,fieldError:g.fieldError})))],8,i),e.button?((0,n.openBlock)(),(0,n.createElementBlock)("fieldset",d,[(0,n.createElementVNode)("button",{type:"submit",class:"btn btn-primary btn-lg",disabled:a.sent||a.sending},[a.sent?((0,n.openBlock)(),(0,n.createElementBlock)("span",o,[s,(0,n.createTextVNode)(" "+(0,n.toDisplayString)(e.buttonSent||a.$t("common.form.sent")),1)])):a.sending?((0,n.openBlock)(),(0,n.createElementBlock)("span",l,[f,(0,n.createTextVNode)(" "+(0,n.toDisplayString)(e.buttonSending||a.$t("common.form.sending")),1)])):((0,n.openBlock)(),(0,n.createElementBlock)("span",u,[(0,n.createElementVNode)("span",{class:(0,n.normalizeClass)("fas fa-"+e.sendIcon)},null,2),(0,n.createTextVNode)(" "+(0,n.toDisplayString)(e.buttonSend||a.$t("common.form.send")),1)]))],8,c),a.sent?((0,n.openBlock)(),(0,n.createElementBlock)("button",p,[(0,n.createElementVNode)("span",null,[v,(0,n.createTextVNode)(" "+(0,n.toDisplayString)(e.buttonReset||a.$t("common.form.reset")),1)])])):(0,n.createCommentVNode)("",!0)])):(0,n.createCommentVNode)("",!0)],40,r)}},8787:function(a,t,e){e.r(t),e.d(t,{render:function(){return i}});var n=e(8253),r=[(0,n.createStaticVNode)('<div id="container" data-v-c35daafc><aside data-v-c35daafc><ul data-v-c35daafc><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_01.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status orange" data-v-c35daafc></span> offline </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_02.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_03.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status orange" data-v-c35daafc></span> offline </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_04.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_05.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status orange" data-v-c35daafc></span> offline </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_06.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_07.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_08.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_09.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status green" data-v-c35daafc></span> online </h3></div></li><li data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_10.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Prénom Nom</h2><h3 data-v-c35daafc><span class="status orange" data-v-c35daafc></span> offline </h3></div></li></ul></aside><main data-v-c35daafc><header data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_01.jpg" alt="" data-v-c35daafc><div data-v-c35daafc><h2 data-v-c35daafc>Chat with Vincent Porter</h2><h3 data-v-c35daafc>already 1902 messages</h3></div><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_star.png" alt="" data-v-c35daafc></header><ul id="chat" data-v-c35daafc><li class="you" data-v-c35daafc><div class="entete" data-v-c35daafc><span class="status green" data-v-c35daafc></span><h2 data-v-c35daafc>Vincent</h2><h3 data-v-c35daafc>10:12AM, Today</h3></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. </div></li><li class="me" data-v-c35daafc><div class="entete" data-v-c35daafc><h3 data-v-c35daafc>10:12AM, Today</h3><h2 data-v-c35daafc>Vincent</h2><span class="status blue" data-v-c35daafc></span></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. </div></li><li class="me" data-v-c35daafc><div class="entete" data-v-c35daafc><h3 data-v-c35daafc>10:12AM, Today</h3><h2 data-v-c35daafc>Vincent</h2><span class="status blue" data-v-c35daafc></span></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> OK </div></li><li class="you" data-v-c35daafc><div class="entete" data-v-c35daafc><span class="status green" data-v-c35daafc></span><h2 data-v-c35daafc>Vincent</h2><h3 data-v-c35daafc>10:12AM, Today</h3></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. </div></li><li class="me" data-v-c35daafc><div class="entete" data-v-c35daafc><h3 data-v-c35daafc>10:12AM, Today</h3><h2 data-v-c35daafc>Vincent</h2><span class="status blue" data-v-c35daafc></span></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. </div></li><li class="me" data-v-c35daafc><div class="entete" data-v-c35daafc><h3 data-v-c35daafc>10:12AM, Today</h3><h2 data-v-c35daafc>Vincent</h2><span class="status blue" data-v-c35daafc></span></div><div class="triangle" data-v-c35daafc></div><div class="message" data-v-c35daafc> OK </div></li></ul><footer data-v-c35daafc><textarea placeholder="Type your message" data-v-c35daafc></textarea><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_picture.png" alt="" data-v-c35daafc><img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_file.png" alt="" data-v-c35daafc><a href="#" data-v-c35daafc>Send</a></footer></main></div>',1)];function i(a,t,e,i,d,c){return(0,n.openBlock)(),(0,n.createElementBlock)("div",null,r)}},6335:function(a,t,e){e.r(t),e.d(t,{render:function(){return l}});var n=e(8253),r=function(a){return(0,n.pushScopeId)("data-v-5f877bab"),a=a(),(0,n.popScopeId)(),a},i={class:"text-content"},d={disabled:!0,type:"button",class:"btn btn-outline-secondary"},c=r((function(){return(0,n.createElementVNode)("i",{class:"fas fa-redo"},null,-1)})),o=["disabled"],s=r((function(){return(0,n.createElementVNode)("i",{class:"fas fa-redo"},null,-1)}));function l(a,t,e,r,l,f){var u=(0,n.resolveComponent)("tooltip");return(0,n.openBlock)(),(0,n.createElementBlock)("div",null,[(0,n.createElementVNode)("span",null,[(0,n.createTextVNode)((0,n.toDisplayString)(a.$t("common.email.status.".concat(e.delivery_status)))+" ",1),(0,n.createElementVNode)("i",{class:(0,n.normalizeClass)([f.icon,e.delivery_status])},null,2)]),e.can_redo||"error"===e.delivery_status?((0,n.openBlock)(),(0,n.createElementBlock)(n.Fragment,{key:0},[a.recent?((0,n.openBlock)(),(0,n.createBlock)(u,{key:0,direction:"left"},{tooltip:(0,n.withCtx)((function(){return[(0,n.createElementVNode)("div",i,(0,n.toDisplayString)(a.$t("common.email.recent")),1)]})),default:(0,n.withCtx)((function(){return[(0,n.createElementVNode)("button",d,[c,(0,n.createTextVNode)(" "+(0,n.toDisplayString)(a.$t("common.email.redo")),1)])]})),_:1})):((0,n.openBlock)(),(0,n.createElementBlock)("button",{key:1,disabled:e.disabled,type:"button",class:"btn btn-outline-secondary",onClick:t[0]||(t[0]=function(t){return a.$emit("redo")})},[s,(0,n.createTextVNode)(" "+(0,n.toDisplayString)(a.$t("common.email.redo")),1)],8,o))],64)):(0,n.createCommentVNode)("",!0)])}},3276:function(a,t,e){e.r(t),e.d(t,{render:function(){return r}});var n=e(8253);function r(a,t,e,r,i,d){var c=(0,n.resolveComponent)("ajax-form");return(0,n.openBlock)(),(0,n.createBlock)(c)}},60:function(a,t,e){e.r(t),e.d(t,{render:function(){return c}});var n=e(8253),r={class:"tip-wrapper"},i={class:"tip-handler"},d=function(a){return(0,n.pushScopeId)("data-v-c3bddb24"),a=a(),(0,n.popScopeId)(),a}((function(){return(0,n.createElementVNode)("i",null,null,-1)}));function c(a,t,e,c,o,s){return(0,n.openBlock)(),(0,n.createElementBlock)("div",r,[(0,n.createElementVNode)("span",i,[(0,n.renderSlot)(a.$slots,"default",{},void 0,!0)]),(0,n.createElementVNode)("div",{class:(0,n.normalizeClass)(["tip-content",e.direction])},[(0,n.renderSlot)(a.$slots,"tooltip",{},void 0,!0),d],2)])}},2333:function(a,t,e){e.r(t);var n=e(159),r=e(8307);r.defaults.transition="slide",r.defaults.theme.ok="btn btn-primary",r.defaults.theme.cancel="btn btn-danger",r.defaults.theme.input="form-control",r.defaults.notifier.position="top-right";var i=document.documentElement.lang.substr(0,2);r.errorAlert||r.dialog("errorAlert",(function(){return{setup:function(){return{options:{frameless:!1,movable:!1,closableByDimmer:!1,maximizable:!1,resizable:!1},buttons:[{text:r.defaults.glossary.ok,key:27,invokeOnClose:!0,className:r.defaults.theme.ok}],focus:{element:0,select:!1}}},build:function(){var a='<span class="fa fa-times-circle fa-2x" style="vertical-align:middle;color:#e10000;"></span> '+n.default[i].common.internal;this.setHeader(a)}}}),!0,"alert"),r.confirmAlert||r.dialog("confirmAlert",(function(){return{setup:function(){return{options:{frameless:!1,movable:!1,closableByDimmer:!1,maximizable:!1,resizable:!1},buttons:[{text:r.defaults.glossary.ok,key:27,invokeOnClose:!0,className:r.defaults.theme.ok}],focus:{element:0,select:!1}}},build:function(){var a='<span class="fa fa-check fa-2x" style="vertical-align:middle;color:#00e100;"></span> '+n.default[i].common.success;this.setHeader(a)}}}),!0,"alert"),t.default=r},7936:function(a,t,e){e.r(t);var n=e(4554);window.Pusher=e(6606),t.default=new n.default({broadcaster:"pusher",key:window.global.pusher.key,wsHost:window.global.pusher.host,wsPort:window.global.pusher.port||443,useTLS:!(80===window.global.pusher.port),disableStats:!0,enabledTransports:80===window.global.pusher.port?["ws"]:["wss"]})},9088:function(a,t,e){e.r(t),e.d(t,{default:function(){return s}});var n=e(5565);function r(a,t){var e=Object.keys(a);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(a);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(a,t).enumerable}))),e.push.apply(e,n)}return e}function i(a){for(var t=1;t<arguments.length;t++){var e=null!=arguments[t]?arguments[t]:{};t%2?r(Object(e),!0).forEach((function(t){d(a,t,e[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(a,Object.getOwnPropertyDescriptors(e)):r(Object(e)).forEach((function(t){Object.defineProperty(a,t,Object.getOwnPropertyDescriptor(e,t))}))}return a}function d(a,t,e){return t in a?Object.defineProperty(a,t,{value:e,enumerable:!0,configurable:!0,writable:!0}):a[t]=e,a}function c(a,t){return function(a){if(Array.isArray(a))return a}(a)||function(a,t){var e=null==a?null:"undefined"!=typeof Symbol&&a[Symbol.iterator]||a["@@iterator"];if(null==e)return;var n,r,i=[],d=!0,c=!1;try{for(e=e.call(a);!(d=(n=e.next()).done)&&(i.push(n.value),!t||i.length!==t);d=!0);}catch(a){c=!0,r=a}finally{try{d||null==e.return||e.return()}finally{if(c)throw r}}return i}(a,t)||function(a,t){if(!a)return;if("string"==typeof a)return o(a,t);var e=Object.prototype.toString.call(a).slice(8,-1);"Object"===e&&a.constructor&&(e=a.constructor.name);if("Map"===e||"Set"===e)return Array.from(a);if("Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e))return o(a,t)}(a,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function o(a,t){(null==t||t>a.length)&&(t=a.length);for(var e=0,n=new Array(t);e<t;e++)n[e]=a[e];return n}function s(a,t,e,r){var d=void 0;return(0,n.isObject)(e)||(0,n.isArray)(e)?(d=new FormData,Object.entries(e).forEach((function(a){var t=c(a,2),e=t[0],n=t[1];return d.append(e,n)}))):e&&(d=new URLSearchParams(e)),window.fetch(new Request(a,{method:t||"GET",headers:i({"X-HASH-IV":window.location.hash.substr(1),"X-Requested-With":"XMLHttpRequest"},r||{}),body:d})).then((function(a){var t;return t=(r||{}).responseType?a.text():a.json(),a.ok?t:t.then((function(a){return Promise.reject(a)}))}))}},5565:function(a,t,e){e.r(t),e.d(t,{download:function(){return i},isString:function(){return d},isObject:function(){return c},isArray:function(){return o},isBoolean:function(){return s},has:function(){return l},get:function(){return f},px:function(){return u},translate:function(){return p},deepMerge:function(){return v}});var n=e(6486),r=e.n(n),i=function(a,t,e){var n=new Blob([a]);n=n.slice(0,n.size,e);var r=window.URL.createObjectURL(n),i=document.createElement("a");i.href=r,i.setAttribute("download",t),document.body.appendChild(i),i.click()},d=function(a){return"string"==typeof a||a instanceof String},c=function(a){return"[object Object]"===Object.prototype.toString.call(a)},o=function(a){return"[object Array]"===Object.prototype.toString.call(a)},s=function(a){return"boolean"==typeof a},l=function(a,t){return c(a)&&a.hasOwnProperty(t)},f=function(a,t,e){return l(a,t)?a[t]:e},u=function(a){return"".concat(a,"px")},p=function(a,t){return"translate(".concat(a,", ").concat(t,")")},v=function(a){for(var t=arguments.length,e=new Array(t>1?t-1:0),n=1;n<t;n++)e[n-1]=arguments[n];return r().mergeWith.apply(r(),[a].concat(e,[function(a,t){if(r().isArray(a))return a.concat(t)}]))}},9039:function(a,t,e){e.r(t);var n=e(1519),r=e.n(n)()((function(a){return a[1]}));r.push([a.id,"#container[data-v-c35daafc]{background:#eff3f7;font-size:0;height:800px;margin:-76px auto 0;overflow:hidden}aside[data-v-c35daafc]{background-color:#4d5760;height:800px;position:absolute;width:260px}aside[data-v-c35daafc],main[data-v-c35daafc]{display:inline-block;font-size:15px;vertical-align:top}main[data-v-c35daafc]{height:100%;height:800px;padding-left:260px;width:100%}aside ul[data-v-c35daafc]{height:690px;list-style-type:none;margin:0;overflow-y:scroll;padding-left:0}aside li[data-v-c35daafc]{padding:10px 0}aside li[data-v-c35daafc]:hover{background-color:#5e616a}h2[data-v-c35daafc],h3[data-v-c35daafc]{margin:0}aside li img[data-v-c35daafc]{border-radius:50%;margin-left:20px;margin-right:8px}aside li div[data-v-c35daafc]{display:inline-block;margin-top:12px;vertical-align:top}aside li h2[data-v-c35daafc]{color:#fff;font-size:14px;font-weight:400;margin-bottom:5px}aside li h3[data-v-c35daafc]{color:#7e818a;font-size:12px;font-weight:400}.status[data-v-c35daafc]{border-radius:50%;display:inline-block;height:8px;margin-right:7px;width:8px}.green[data-v-c35daafc]{background-color:#58b666}.orange[data-v-c35daafc]{background-color:#ff725d}.blue[data-v-c35daafc]{background-color:#6fbced;margin-left:7px;margin-right:0}main header[data-v-c35daafc]{height:110px;padding:30px 20px 30px 40px}main header>*[data-v-c35daafc]{display:inline-block;vertical-align:top}main header img[data-v-c35daafc]:first-child{border-radius:50%}main header img[data-v-c35daafc]:last-child{margin-top:8px;width:24px}main header div[data-v-c35daafc]{margin-left:10px;margin-right:145px}main header h2[data-v-c35daafc]{font-size:16px;margin-bottom:5px}main header h3[data-v-c35daafc]{color:#7e818a;font-size:14px;font-weight:400}#chat[data-v-c35daafc]{border-bottom:2px solid #fff;border-top:2px solid #fff;height:535px;list-style-type:none;margin:0;overflow-y:scroll;padding-left:0}#chat li[data-v-c35daafc]{padding:10px 30px}#chat h2[data-v-c35daafc],#chat h3[data-v-c35daafc]{display:inline-block;font-size:13px;font-weight:400}#chat h3[data-v-c35daafc]{color:#bbb}#chat .entete[data-v-c35daafc]{margin-bottom:5px}#chat .message[data-v-c35daafc]{border-radius:5px;color:#fff;display:inline-block;line-height:25px;max-width:90%;padding:20px;text-align:left}#chat .me[data-v-c35daafc]{text-align:right}#chat .you .message[data-v-c35daafc]{background-color:#58b666}#chat .me .message[data-v-c35daafc]{background-color:#6fbced}#chat .triangle[data-v-c35daafc]{border-style:solid;border-width:0 10px 10px;height:0;width:0}#chat .you .triangle[data-v-c35daafc]{border-color:transparent transparent #58b666;margin-left:15px}#chat .me .triangle[data-v-c35daafc]{border-color:transparent transparent #6fbced;margin-left:375px}main footer[data-v-c35daafc]{height:155px;padding:20px 30px 10px 20px}main footer textarea[data-v-c35daafc]{border:none;border-radius:3px;display:block;font-size:13px;height:80px;margin-bottom:13px;padding:20px;resize:none;width:100%}main footer textarea[data-v-c35daafc]::-moz-placeholder{color:#ddd}main footer textarea[data-v-c35daafc]:-ms-input-placeholder{color:#ddd}main footer textarea[data-v-c35daafc]::placeholder{color:#ddd}main footer img[data-v-c35daafc]{cursor:pointer;height:30px}main footer a[data-v-c35daafc]{color:#6fbced;display:inline-block;font-weight:700;margin-left:333px;margin-top:5px;text-decoration:none;text-transform:uppercase;vertical-align:top}#form form[data-v-c35daafc]{margin-bottom:20px}.email td p[data-v-c35daafc]{--lines:15;-webkit-line-clamp:var(--lines);-webkit-box-orient:vertical;background:linear-gradient(#fff 30%,hsla(0,0%,100%,0)),linear-gradient(hsla(0,0%,100%,0),#fff 70%) 0 100%,radial-gradient(50% 0,farthest-side,rgba(0,0,0,.2),transparent),radial-gradient(50% 100%,farthest-side,rgba(0,0,0,.2),transparent) 0 100%;background:linear-gradient(#fff 30%,hsla(0,0%,100%,0)),linear-gradient(hsla(0,0%,100%,0),#fff 70%) 0 100%,radial-gradient(farthest-side at 50% 0,rgba(0,0,0,.2),transparent),radial-gradient(farthest-side at 50% 100%,rgba(0,0,0,.2),transparent) 0 100%;background-attachment:local,local,scroll,scroll;background-repeat:no-repeat;background-size:100% 40px,100% 40px,100% 14px,100% 14px;display:-webkit-box;max-height:calc(var(--lines)*1.5em);overflow:auto}.email:hover td p[data-v-c35daafc]{background:rgba(0,0,0,.075),rgba(0,0,0,.075),radial-gradient(50% 0,farthest-side,rgba(0,0,0,.2),transparent),radial-gradient(50% 100%,farthest-side,rgba(0,0,0,.2),transparent) 0 100%;background:rgba(0,0,0,.075),rgba(0,0,0,.075),radial-gradient(farthest-side at 50% 0,rgba(0,0,0,.2),transparent),radial-gradient(farthest-side at 50% 100%,rgba(0,0,0,.2),transparent) 0 100%}.no-email[data-v-c35daafc]{text-align:center}table[data-v-c35daafc]{table-layout:fixed}table th[data-v-c35daafc]:first-child,table th[data-v-c35daafc]:last-child{width:12em}table caption[data-v-c35daafc]{display:none}",""]),t.default=r},1322:function(a,t,e){e.r(t);var n=e(1519),r=e.n(n)()((function(a){return a[1]}));r.push([a.id,".input-group-append[data-v-5f877bab]{display:inline;margin-left:10px}.error[data-v-5f877bab]{color:red}.sent[data-v-5f877bab]{color:orange}.received[data-v-5f877bab]{color:green}",""]),t.default=r},7800:function(a,t,e){e.r(t);var n=e(1519),r=e.n(n)()((function(a){return a[1]}));r.push([a.id,'.tip-wrapper[data-v-c3bddb24]{display:inline-block;position:relative;text-align:left}.tip-wrapper .tip-handler[data-v-c3bddb24] *{-webkit-text-decoration:underline dotted;text-decoration:underline dotted}.tip-content h3[data-v-c3bddb24]{margin:12px 0}.tip-content[data-v-c3bddb24]{background-color:#444;border-radius:8px;box-shadow:0 1px 8px rgba(0,0,0,.5);box-sizing:border-box;color:#eee;font-size:13px;font-weight:400;max-width:400px;min-width:300px;opacity:0;padding:0;position:absolute;transition:opacity .8s;visibility:hidden;z-index:99999999}.tip-content.right[data-v-c3bddb24]{left:100%;margin-left:20px;top:50%;transform:translateY(-50%)}.tip-content.left[data-v-c3bddb24]{margin-right:20px;right:100%;top:50%;transform:translateY(-50%)}.tip-content.top[data-v-c3bddb24]{left:50%;top:-20px;transform:translate(-30%,-100%)}.tip-content.bottom[data-v-c3bddb24]{left:50%;top:40px;transform:translate(-50%)}.tip-wrapper:hover .tip-content[data-v-c3bddb24]{opacity:1;visibility:visible}.tip-content[data-v-c3bddb24] .text-content{padding:10px 20px}.tip-content img[data-v-c3bddb24]{border-radius:8px 8px 0 0;width:400px}.tip-content.right i[data-v-c3bddb24]{height:24px;margin-top:-12px;overflow:hidden;position:absolute;right:100%;top:50%;width:12px}.tip-content.right i[data-v-c3bddb24]:after{background-color:#444;box-shadow:0 1px 8px rgba(0,0,0,.5);content:"";height:12px;left:0;position:absolute;top:50%;transform:translate(50%,-50%) rotate(-45deg);width:12px}.tip-content.left i[data-v-c3bddb24]{height:24px;left:100%;margin-top:-12px;overflow:hidden;position:absolute;top:50%;width:12px}.tip-content.left i[data-v-c3bddb24]:after{background-color:#444;box-shadow:0 1px 8px rgba(0,0,0,.5);content:"";height:12px;left:0;position:absolute;top:50%;transform:translate(-50%,-50%) rotate(-45deg);width:12px}.tip-content.top i[data-v-c3bddb24]{height:12px;left:30%;margin-left:-12px;overflow:hidden;position:absolute;top:100%;width:24px}.tip-content.top i[data-v-c3bddb24]:after{background-color:#444;box-shadow:0 1px 8px rgba(0,0,0,.5);content:"";height:15px;left:50%;position:absolute;transform:translate(-50%,-50%) rotate(45deg);width:15px}.tip-content.bottom i[data-v-c3bddb24]{bottom:100%;height:12px;left:50%;margin-left:-12px;overflow:hidden;position:absolute;width:24px}.tip-content.bottom i[data-v-c3bddb24]:after{background-color:#444;box-shadow:0 1px 8px rgba(0,0,0,.5);content:"";height:12px;left:50%;position:absolute;transform:translate(-50%,50%) rotate(45deg);width:12px}',""]),t.default=r},2411:function(a,t,e){e.r(t);var n=e(3379),r=e.n(n),i=e(9039),d={insert:"head",singleton:!1};r()(i.default,d);t.default=i.default.locals||{}},7666:function(a,t,e){e.r(t);var n=e(3379),r=e.n(n),i=e(1322),d={insert:"head",singleton:!1};r()(i.default,d);t.default=i.default.locals||{}},5604:function(a,t,e){e.r(t);var n=e(3379),r=e.n(n),i=e(7800),d={insert:"head",singleton:!1};r()(i.default,d);t.default=i.default.locals||{}},8203:function(a,t,e){e.r(t);var n=e(3014),r=e(2365),i={};for(var d in r)"default"!==d&&(i[d]=function(a){return r[a]}.bind(0,d));e.d(t,i);const c=(0,e(3744).default)(r.default,[["render",n.render]]);t.default=c},1214:function(a,t,e){e.r(t);var n=e(4661),r=e(7860),i={};for(var d in r)"default"!==d&&(i[d]=function(a){return r[a]}.bind(0,d));e.d(t,i);e(5511);const c=(0,e(3744).default)(r.default,[["render",n.render],["__scopeId","data-v-c35daafc"]]);t.default=c},1573:function(a,t,e){e.r(t);var n=e(9552),r=e(4770),i={};for(var d in r)"default"!==d&&(i[d]=function(a){return r[a]}.bind(0,d));e.d(t,i);e(4623);const c=(0,e(3744).default)(r.default,[["render",n.render],["__scopeId","data-v-5f877bab"]]);t.default=c},388:function(a,t,e){e.r(t);var n=e(8637),r=e(4734),i={};for(var d in r)"default"!==d&&(i[d]=function(a){return r[a]}.bind(0,d));e.d(t,i);const c=(0,e(3744).default)(r.default,[["render",n.render]]);t.default=c},4667:function(a,t,e){e.r(t);var n=e(7361),r=e(2564),i={};for(var d in r)"default"!==d&&(i[d]=function(a){return r[a]}.bind(0,d));e.d(t,i);e(2369);const c=(0,e(3744).default)(r.default,[["render",n.render],["__scopeId","data-v-c3bddb24"]]);t.default=c},2365:function(a,t,e){e.r(t),e.d(t,{default:function(){return n.default}});var n=e(2554),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},7860:function(a,t,e){e.r(t),e.d(t,{default:function(){return n.default}});var n=e(4509),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},4770:function(a,t,e){e.r(t),e.d(t,{default:function(){return n.default}});var n=e(6623),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},4734:function(a,t,e){e.r(t),e.d(t,{default:function(){return n.default}});var n=e(4077),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},2564:function(a,t,e){e.r(t),e.d(t,{default:function(){return n.default}});var n=e(1164),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},3014:function(a,t,e){e.r(t);var n=e(9959),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},4661:function(a,t,e){e.r(t);var n=e(8787),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},9552:function(a,t,e){e.r(t);var n=e(6335),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},8637:function(a,t,e){e.r(t);var n=e(3276),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},7361:function(a,t,e){e.r(t);var n=e(60),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},5511:function(a,t,e){e.r(t);var n=e(2411),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},4623:function(a,t,e){e.r(t);var n=e(7666),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)},2369:function(a,t,e){e.r(t);var n=e(5604),r={};for(var i in n)"default"!==i&&(r[i]=function(a){return n[a]}.bind(0,i));e.d(t,r)}}]);