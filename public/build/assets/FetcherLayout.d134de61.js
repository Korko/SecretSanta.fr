import f from"./Layout.576359a3.js";import{F as _}from"./Fetcher.b9beb86d.js";import{_ as d}from"./plugin-vue_export-helper.f2aa9cb4.js";import{S as c,r as o,o as a,c as n,w as e,f as r,a as s,n as h,h as v,e as y}from"./app.cffad64e.js";import"./fetch.5d571f9b.js";const $={components:{Layout:f,Suspense:c,Fetcher:_},props:{src:{type:String,required:!0}}};function b(t,g,l,F,k,L){const p=o("Fetcher"),i=o("i18n-t"),m=o("Layout");return a(),n(m,null,{"navbar-left":e(()=>[r(t.$slots,"navbar-left")]),"navbar-right":e(()=>[r(t.$slots,"navbar-right")]),content:e(()=>[(a(),n(c,null,{default:e(()=>[s(p,{src:l.src},{default:e(u=>[r(t.$slots,"default",h(v(u)))]),_:3},8,["src"])]),fallback:e(()=>[y("div",null,[s(i,{keypath:"common.fetcher.loading"})])]),_:3}))]),_:3})}var P=d($,[["render",b]]);export{P as default};