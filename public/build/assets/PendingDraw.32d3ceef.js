import y from"./FetcherLayout.b4b9b0e2.js";import{N as b}from"./NavPageLink.4d27d314.js";import{f as k}from"./fetch.01f53e84.js";import{E as w}from"./echo.4216f5db.js";import{_}from"./Layout.7e513d1b.js";import{o as n,d as o,b as u,t as F,e as m,j as L,F as N,r as a,c as V,w as i,a as r}from"./app.6256a668.js";const j={props:{routes:{type:Object},pending:{type:Object}},created(){w.channel("pending_draw."+this.pending.id).listen(".status.update",t=>{console.debug("echo",t),this.pending={...this.pending,...t}})},methods:{process(){return this.pending.status="drawing",k(this.routes.process).then(t=>{this.pending.status="drawn"}).catch(t=>{this.pending.status="waiting"})}}},P={key:0},S=u(" Votre \xE9v\xE8nement SecretSanta est pr\xEAt \xE0 d\xE9marrer. "),x={key:1},B=u(" Le tirage au sort est en cours "),D=m("button",{disabled:""},"Veuillez patienter",-1),C=[B,D],O={key:2},E={key:3};function z(t,d,e,g,h,c){return n(),o(N,null,[u(" Bonjour "+F(e.pending.organizer_name)+", ",1),e.pending.status==="created"?(n(),o("div",P,[S,m("button",{onClick:d[0]||(d[0]=(...s)=>c.process&&c.process(...s))},"Lancer le tirage au sort")])):e.pending.status==="drawing"?(n(),o("div",x,C)):e.pending.status==="started"?(n(),o("div",O," Votre \xE9v\xE8nement SecretSanta est d\xE9j\xE0 en cours ")):e.pending.status==="drawn"?(n(),o("div",E," Votre \xE9v\xE8nement SecretSanta a bien \xE9t\xE9 d\xE9marr\xE9 ")):L("",!0)],64)}var T=_(j,[["render",z]]);const q={components:{FetcherLayout:y,NavPageLink:b,PendingDrawForm:T},props:{routes:{type:Object},pending:{type:Object}}};function A(t,d,e,g,h,c){const s=a("i18n-t"),p=a("NavPageLink"),f=a("PendingDrawForm"),l=a("FetcherLayout");return n(),V(l,{route:e.routes.fetch},{"navbar-right":i(()=>[r(p,{href:t.route("form.index")},{default:i(()=>[r(s,{keypath:"common.nav.go"})]),_:1},8,["href"]),r(p,{href:t.route("dashboard")},{default:i(()=>[r(s,{keypath:"common.nav.dashboard"})]),_:1},8,["href"])]),default:i(v=>[r(f,{routes:e.routes,pending:{...e.pending,...v.pending}},null,8,["routes","pending"])]),_:1},8,["route"])}var Q=_(q,[["render",A]]);export{Q as default};
