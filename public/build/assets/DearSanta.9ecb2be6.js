import{F as T}from"./Fetcher.61889ec2.js";import{N as L}from"./NavPageLink.a3a2b258.js";import{d as b,f as g}from"./fetch.49ea9645.js";import{T as F,E as j}from"./Tooltip.890bead5.js";import{E as N}from"./EmailStatus.ebb3fe13.js";import{A as O}from"./AjaxForm.e21440e4.js";import{r as c,o,d as i,t as r,j as v,a as d,w as l,e as t,k as C,v as $,i as B,F as R,l as U,c as y,b as S,p as I,m as M,n as q,h as E}from"./app.91289146.js";import{_ as w}from"./plugin-vue_export-helper.f2aa9cb4.js";import"./Fetcher.3f29ad2d.js";const P={components:{EmailStatus:N,Tooltip:F,AjaxForm:O},props:{draw:{type:Object,required:!0},participant:{type:Object,required:!0},dearSantas:{type:Object,required:!0},routes:{type:Object,required:!0},targetDearSantaLastUpdate:{type:String,required:!0}},data(){return{content:""}},computed:{dearSantasByDate(){return Object.values(this.dearSantas).map(e=>Object.assign(e,e.mail)).sort((e,s)=>new Date(e.created_at)>new Date(s.created_at)?-1:1).map(e=>(e.created_at=new Date(e.created_at).toLocaleString("fr-FR"),e))||[]},finished(){return!!this.draw.finished_at},checkUpdates(){return!!Object.values(this.dearSantas).find(e=>e.mail.delivery_status!=="error")},recentTargetDearSanta(){return new Date().getTime()-new Date(this.targetDearSantaLastUpdate).getTime()<5*60*1e3}},created(){j.channel("draw."+this.draw.hash).listen(".mail.update",e=>{this.dearSantas[e.id]&&(this.dearSantas[e.id].mail.delivery_status=e.delivery_status,this.dearSantas[e.id].mail.updated_at=e.updated_at)}),window.localStorage.setItem("secretsanta",JSON.stringify(b(JSON.parse(window.localStorage.getItem("secretsanta"))||{},{[this.draw.hash]:{title:this.draw.mail_title,creation:this.draw.created_at,expiration:this.draw.expires_at,organizerName:this.draw.organizer_name,links:{[this.participant.hash]:{name:this.participant.name,link:window.location.href}}}})))},methods:{success(e){e.email.updated_at||(e.email.updated_at=new Date),this.$set(this.dearSantas,e.email.id,e.email)},reset(){this.content=""},resend(e){return this.dearSantas[e].mail.delivery_status="created",this.dearSantas[e].mail.updated_at=new Date().getTime(),g(this.dearSantas[e].resendUrl)},resend_target(){return this.targetDearSantaLastUpdate=new Date().getTime(),g(this.routes.resendTarget)},nl2br(e){return e.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br />$2")},e(e){var s=document.createElement("p");return s.appendChild(document.createTextNode(e)),s.innerHTML}}},D=e=>(I("data-v-9bd86a38"),e=e(),M(),e),V={key:0,class:"alert alert-warning",role:"alert"},x={class:"form-group"},z={for:"mailContent"},A={class:"input-group"},H=["placeholder","aria-invalid"],J={key:0,class:"invalid-tooltip"},G={class:"table table-hover"},K={class:"table-active"},Q={scope:"col"},W={scope:"col"},X={scope:"col"},Y=["innerHTML"],Z={key:0,class:"no-email"},ee={colspan:"3"},te={class:"text-center"},ae={class:"text-content"},ne={disabled:!0,type:"button",class:"btn btn-outline-secondary"},re=D(()=>t("i",{class:"fas fa-redo"},null,-1)),se=D(()=>t("i",{class:"fas fa-redo"},null,-1));function oe(e,s,u,k,h,n){const _=c("AjaxForm"),p=c("EmailStatus"),f=c("Tooltip");return o(),i("div",null,[n.finished?(o(),i("div",V,r(e.$t("dearsanta.finished",{finished_at:e.endDateLong})),1)):v("",!0),d(_,{action:u.routes.contactUrl,onSuccess:n.success,onReset:n.reset,autoReset:!0},{default:l(({fieldError:a})=>[t("fieldset",null,[t("div",x,[t("label",z,r(e.$t("dearsanta.content.label")),1),t("div",A,[C(t("textarea",{id:"mailContent","onUpdate:modelValue":s[0]||(s[0]=m=>h.content=m),name:"content",placeholder:e.$t("dearsanta.content.placeholder"),class:B({"form-control":!0,"is-invalid":a("content")}),"aria-invalid":a("content")},null,10,H),[[$,h.content]]),a("content")?(o(),i("div",J,r(a("content")),1)):v("",!0)])])])]),_:1},8,["action","onSuccess","onReset"]),t("table",G,[t("caption",null,r(e.$t("dearsanta.list.caption")),1),t("thead",null,[t("tr",K,[t("th",Q,r(e.$t("dearsanta.list.date")),1),t("th",W,r(e.$t("dearsanta.list.body")),1),t("th",X,r(e.$t("dearsanta.list.status")),1)])]),t("tbody",null,[(o(!0),i(R,null,U(n.dearSantasByDate,a=>(o(),i("tr",{key:a.id,class:"email"},[t("td",null,r(a.created_at),1),t("td",null,[t("p",{innerHTML:n.nl2br(n.e(a.mail_body))},null,8,Y)]),t("td",null,[d(p,{delivery_status:a.mail.delivery_status,last_update:a.mail.updated_at,onRedo:m=>n.resend(a.id)},null,8,["delivery_status","last_update","onRedo"])])]))),128)),n.dearSantasByDate.length===0?(o(),i("tr",Z,[t("td",ee,r(e.$t("dearsanta.list.empty")),1)])):v("",!0)])]),t("div",te,[n.recentTargetDearSanta?(o(),y(f,{key:0,direction:"top"},{tooltip:l(()=>[t("div",ae,r(e.$t("common.email.recent")),1)]),default:l(()=>[t("button",ne,[re,S(" "+r(e.$t("dearsanta.resend.button")),1)])]),_:1})):(o(),i("button",{key:1,class:"btn btn-info btn-lg",onClick:s[1]||(s[1]=(...a)=>n.resend_target&&n.resend_target(...a))},[se,S(" "+r(e.$t("dearsanta.resend.button")),1)]))])])}var ie=w(P,[["render",oe],["__scopeId","data-v-9bd86a38"]]);const de={components:{FetcherLayout:T,NavPageLink:L,DearSantaForm:ie},props:{routes:{type:Object}},methods:{deepMerge:b}};function ce(e,s,u,k,h,n){const _=c("i18n-t"),p=c("NavPageLink"),f=c("DearSantaForm"),a=c("FetcherLayout");return o(),y(a,{src:u.routes.fetch},{"navbar-right":l(()=>[d(p,{href:e.route("form.index")},{default:l(()=>[d(_,{keypath:"common.nav.go"})]),_:1},8,["href"]),d(p,{href:e.route("dashboard")},{default:l(()=>[d(_,{keypath:"common.nav.dashboard"})]),_:1},8,["href"])]),default:l(m=>[d(f,q(E(n.deepMerge(m,{routes:u.routes}))),null,16)]),_:1},8,["src"])}var Se=w(de,[["render",ce]]);export{Se as default};