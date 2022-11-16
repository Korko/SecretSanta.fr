import{p as m,a as b}from"./fetch.ffe8984e.js";import{o as a,d as o,e as r,f as g,n as S,h as y,b as d,t as f,i as E,j as h,u as c}from"./app.bdaf2afe.js";import{_ as R}from"./plugin-vue_export-helper.f2aa9cb4.js";const _={props:{action:{type:String,default:""},button:{type:Boolean,default:!0},buttonSend:{type:String,default:""},buttonSending:{type:String,default:""},buttonSent:{type:String,default:""},buttonReset:{type:String,default:""},sendIcon:{type:String,default:"paper-plane"},autoReset:{type:Boolean,default:!1}},data:()=>({fieldErrors:[],sending:!1,sent:!1}),watch:{sending(){this.$emit("change",this.sending)}},methods:{fieldError(e){return this.fieldErrors[e]?this.fieldErrors[e][0]:null},precog(e,s){return m(e,s).then(()=>{this.fieldErrors=Object.keys(this.fieldErrors).filter(t=>s[t]).reduce((t,i)=>Object.assign(t,{[i]:this.fieldErrors[i]}),{})}).catch(t=>{t&&t.errors&&(this.fieldErrors=Object.assign(this.fieldErrors,t.errors))})},call(e,s){if(!this.sending&&!this.sent)return this.sending=!0,b(e,s.data).then(t=>{this.fieldErrors={},this.sending=!1,this.autoReset||(this.sent=!0),(s.success||s.then||function(){})(t),(s.complete||s.finally||function(){})(),this.$emit("success",t),this.autoReset&&this.onReset()}).catch(t=>{t&&t.errors&&(this.fieldErrors=t.errors),this.sending=!1;var i;(i=s.error||s.catch)&&i(t);var l;(l=s.complete||s.finally)&&l(),!i&&!l&&this.fieldErrors.length>0&&this.$dialog.alert(this.$t("form.internalError")),this.$emit("error")})},onSubmit(){this.submit()},onReset(){this.$emit("reset"),this.fieldErrors={},this.sending=!1,this.sent=!1},validate(e,s){return this.precog(this.action,{[e]:s})},submit(e,s){this.$emit("beforeSubmit"),e=e||serialize(this.$el);var t=this.call(this.action,Object.assign({data:e},s));return this.$emit("afterSubmit"),t}}},p=["action"],k=["disabled"],v={key:0},j=["disabled"],B={key:0},O=r("span",{class:"fas fa-check-circle"},null,-1),w={key:1},z=r("span",{class:"fas fa-spinner"},null,-1),N={key:2},V={key:0,type:"reset",class:"btn btn-primary btn-lg"},$=r("span",{class:"fas fa-backward"},null,-1);function A(e,s,t,i,l,n){return a(),o("form",{action:t.action,method:"post",autocomplete:"off",onSubmit:s[0]||(s[0]=c((...u)=>n.onSubmit&&n.onSubmit(...u),["prevent"])),onReset:s[1]||(s[1]=c((...u)=>n.onReset&&n.onReset(...u),["prevent"]))},[r("fieldset",{disabled:e.sending||e.sent},[g(e.$slots,"default",S(y({sending:e.sending,sent:e.sent,submit:n.submit,onSubmit:n.onSubmit,onReset:n.onReset,fieldError:n.fieldError})))],8,k),t.button?(a(),o("fieldset",v,[r("button",{type:"submit",class:"btn btn-primary btn-lg",disabled:e.sent||e.sending},[e.sent?(a(),o("span",B,[O,d(" "+f(t.buttonSent||e.$t("common.form.sent")),1)])):e.sending?(a(),o("span",w,[z,d(" "+f(t.buttonSending||e.$t("common.form.sending")),1)])):(a(),o("span",N,[r("span",{class:E("fas fa-"+t.sendIcon)},null,2),d(" "+f(t.buttonSend||e.$t("common.form.send")),1)]))],8,j),e.sent?(a(),o("button",V,[r("span",null,[$,d(" "+f(t.buttonReset||e.$t("common.form.reset")),1)])])):h("",!0)])):h("",!0)],40,p)}var F=R(_,[["render",A]]);export{F as A};
