(()=>{"use strict";var e,t={57:(e,t,r)=>{const o=window.wp.element,n=window.wp.i18n,a=window.wp.components,c=window.wp.blockEditor,l=window.wp.blocks,s=window.wp.serverSideRender;var i=r.n(s);const d=JSON.parse('{"name":"occ/alternate-product-categories"}'),{name:p}=d,w={icon:{src:(0,o.createElement)(SVG,{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},(0,o.createElement)(Path,{d:"M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z"})),foreground:"#ff8a00"},edit:e=>{const t=e.attributes;return(0,o.createElement)(o.Fragment,null,(0,o.createElement)(c.InspectorControls,null,(0,o.createElement)(a.PanelBody,{title:(0,n.__)("Settings","rather-simple-woocommerce-alternate-product-categories")},(0,o.createElement)(a.ToggleControl,{label:(0,n.__)("Show as dropdown","rather-simple-woocommerce-alternate-product-categories"),checked:!!t.dropdown,onChange:()=>{e.setAttributes({dropdown:!e.attributes.dropdown})}}),(0,o.createElement)(a.ToggleControl,{label:(0,n.__)("Show product counts","rather-simple-woocommerce-alternate-product-categories"),checked:!!t.count,onChange:()=>{e.setAttributes({count:!e.attributes.count})}}))),(0,o.createElement)(a.Disabled,null,(0,o.createElement)(i(),{block:"occ/alternate-product-categories",attributes:t})))},transforms:{from:[{type:"block",blocks:["core/legacy-widget"],isMatch:({idBase:e,instance:t})=>!!t?.raw&&"rswapc"===e,transform:({instance:e})=>{const t=(0,l.createBlock)("occ/alternate-product-categories",{dropdown:e.raw.dropdown,count:e.raw.count});return e.raw?.title?[(0,l.createBlock)("core/heading",{content:e.raw.title}),t]:t}}]}};(0,l.registerBlockType)(p,w)}},r={};function o(e){var n=r[e];if(void 0!==n)return n.exports;var a=r[e]={exports:{}};return t[e](a,a.exports,o),a.exports}o.m=t,e=[],o.O=(t,r,n,a)=>{if(!r){var c=1/0;for(d=0;d<e.length;d++){r=e[d][0],n=e[d][1],a=e[d][2];for(var l=!0,s=0;s<r.length;s++)(!1&a||c>=a)&&Object.keys(o.O).every((e=>o.O[e](r[s])))?r.splice(s--,1):(l=!1,a<c&&(c=a));if(l){e.splice(d--,1);var i=n();void 0!==i&&(t=i)}}return t}a=a||0;for(var d=e.length;d>0&&e[d-1][2]>a;d--)e[d]=e[d-1];e[d]=[r,n,a]},o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={823:0,726:0};o.O.j=t=>0===e[t];var t=(t,r)=>{var n,a,c=r[0],l=r[1],s=r[2],i=0;if(c.some((t=>0!==e[t]))){for(n in l)o.o(l,n)&&(o.m[n]=l[n]);if(s)var d=s(o)}for(t&&t(r);i<c.length;i++)a=c[i],o.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return o.O(d)},r=self.webpackChunkrather_simple_woocommerce_alternate_product_categories=self.webpackChunkrather_simple_woocommerce_alternate_product_categories||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=o.O(void 0,[726],(()=>o(57)));n=o.O(n)})();