import{u as k,d as S}from"./utils-BqC4Z-GC.js";var T=/(\[[^[]*\])|([-_:/.,()\s]+)|(A|a|YYYY|YY?|MM?M?M?|Do|DD?|hh?|HH?|mm?|ss?|S{1,3}|z|ZZ?)/g,I=/\d/,E=/\d\d/,F=/\d{3}/,Z=/\d{4}/,g=/\d\d?/,O=/[+-]?\d+/,z=/[+-]\d\d:?(\d\d)?|Z/,w=/\d*[^-_:/,()\s\d]+/,D={},P=function(a){return a=+a,a+(a>68?1900:2e3)};function A(t){if(!t||t==="Z")return 0;var a=t.match(/([+-]|\d\d)/g),e=+(a[1]*60)+(+a[2]||0);return e===0?0:a[0]==="+"?-e:e}var h=function(a){return function(e){this[a]=+e}},L=[z,function(t){var a=this.zone||(this.zone={});a.offset=A(t)}],H=function(a){var e=D[a];return e&&(e.indexOf?e:e.s.concat(e.f))},$=function(a,e){var r,o=D,n=o.meridiem;if(!n)r=a===(e?"pm":"PM");else for(var l=1;l<=24;l+=1)if(a.indexOf(n(l,0,e))>-1){r=l>12;break}return r},X={A:[w,function(t){this.afternoon=$(t,!1)}],a:[w,function(t){this.afternoon=$(t,!0)}],S:[I,function(t){this.milliseconds=+t*100}],SS:[E,function(t){this.milliseconds=+t*10}],SSS:[F,function(t){this.milliseconds=+t}],s:[g,h("seconds")],ss:[g,h("seconds")],m:[g,h("minutes")],mm:[g,h("minutes")],H:[g,h("hours")],h:[g,h("hours")],HH:[g,h("hours")],hh:[g,h("hours")],D:[g,h("day")],DD:[E,h("day")],Do:[w,function(t){var a=D,e=a.ordinal,r=t.match(/\d+/);if(this.day=r[0],!!e)for(var o=1;o<=31;o+=1)e(o).replace(/\[|\]/g,"")===t&&(this.day=o)}],M:[g,h("month")],MM:[E,h("month")],MMM:[w,function(t){var a=H("months"),e=H("monthsShort"),r=(e||a.map(function(o){return o.slice(0,3)})).indexOf(t)+1;if(r<1)throw new Error;this.month=r%12||r}],MMMM:[w,function(t){var a=H("months"),e=a.indexOf(t)+1;if(e<1)throw new Error;this.month=e%12||e}],Y:[O,h("year")],YY:[E,function(t){this.year=P(t)}],YYYY:[Z,h("year")],Z:L,ZZ:L};function N(t){var a=t.afternoon;if(a!==void 0){var e=t.hours;a?e<12&&(t.hours+=12):e===12&&(t.hours=0),delete t.afternoon}}function U(t){t=k(t,D&&D.formats);for(var a=t.match(T),e=a.length,r=0;r<e;r+=1){var o=a[r],n=X[o],l=n&&n[0],f=n&&n[1];f?a[r]={regex:l,parser:f}:a[r]=o.replace(/^\[|\]$/g,"")}return function(s){for(var i={},m=0,v=0;m<e;m+=1){var p=a[m];if(typeof p=="string")v+=p.length;else{var d=p.regex,x=p.parser,u=s.slice(v),c=d.exec(u),y=c[0];x.call(i,y),s=s.replace(y,"")}}return N(i),i}}var V=function(a,e,r){try{if(["x","X"].indexOf(e)>-1)return new Date((e==="X"?1e3:1)*a);var o=U(e),n=o(a),l=n.year,f=n.month,s=n.day,i=n.hours,m=n.minutes,v=n.seconds,p=n.milliseconds,d=n.zone,x=new Date,u=s||(!l&&!f?x.getDate():1),c=l||x.getFullYear(),y=0;l&&!f||(y=f>0?f-1:x.getMonth());var Y=i||0,b=m||0,M=v||0,C=p||0;return d?new Date(Date.UTC(c,y,u,Y,b,M,C+d.offset*60*1e3)):r?new Date(Date.UTC(c,y,u,Y,b,M,C)):new Date(c,y,u,Y,b,M,C)}catch{return new Date("")}};const W=function(t,a,e){e.p.customParseFormat=!0,t&&t.parseTwoDigitYear&&(P=t.parseTwoDigitYear);var r=a.prototype,o=r.parse;r.parse=function(n){var l=n.date,f=n.utc,s=n.args;this.$u=f;var i=s[1];if(typeof i=="string"){var m=s[2]===!0,v=s[3]===!0,p=m||v,d=s[2];v&&(d=s[2]),D=this.$locale(),!m&&d&&(D=e.Ls[d]),this.$d=V(l,i,f),this.init(),d&&d!==!0&&(this.$L=this.locale(d).$L),p&&l!=this.format(i)&&(this.$d=new Date("")),D={}}else if(i instanceof Array)for(var x=i.length,u=1;u<=x;u+=1){s[1]=i[u-1];var c=e.apply(this,s);if(c.isValid()){this.$d=c.$d,this.$L=c.$L,this.init();break}u===x&&(this.$d=new Date(""))}else o.call(this,n)}};S.extend(W);window.chartTooltip=t=>{const{chart:a,tooltip:e}=t;let r=a.canvas.parentNode.querySelector("div");const o=c=>new Intl.NumberFormat("en-US",{style:"currency",currency:a.options.currency}).format(c);if(!r){r=document.createElement("div"),r.classList.add("chart-custom-tooltip","bg-theme-secondary-900","dark:bg-theme-dark-800","rounded","absolute","text-white","leading-3.75","text-left","p-2"),r.style.opacity=1,r.style.pointerEvents="none",r.style.position="absolute",r.style.transform="translate(-50%, 0)",r.style.transition="all .1s ease";const c=document.createElement("table");c.style.margin="0px",r.appendChild(c),a.canvas.parentNode.appendChild(r)}if(e.opacity===0){r.style.opacity=0;return}const n=e.title||[],l=e.dataPoints[0].dataset.data[e.dataPoints[0].dataIndex],f=document.createElement("thead"),s=document.createElement("span");s.innerHTML="Price:",s.classList.add("mr-1","font-semibold","text-theme-secondary-500","dark:text-theme-dark-200","text-xs");const i=document.createElement("tr");i.style.backgroundColor="inherit",i.style.borderWidth=0;const m=document.createElement("th");m.style.borderWidth=0;const v=document.createElement("span");v.innerHTML=o(l),v.classList.add("font-semibold","text-xs","dark:text-theme-dark-50"),m.appendChild(s),m.appendChild(v),i.appendChild(m),f.appendChild(i);const p=document.createElement("tbody");n.forEach(c=>{const y=document.createElement("tr");y.style.borderWidth=0;const Y=document.createElement("td");Y.style.borderWidth=0,Y.classList.add("pt-1.5");const b=S(c.replace("p.m.","pm"),"MMM D, YYYY, H:mm:ss a"),M=document.createElement("span");M.innerHTML=b.format("D MMM YYYY HH:mm:ss"),M.classList.add("font-semibold","text-theme-secondary-500","dark:text-theme-dark-200","text-xs","whitespace-nowrap"),Y.appendChild(M),y.appendChild(Y),p.appendChild(y)});const d=r.querySelector("table");for(;d.firstChild;)d.firstChild.remove();d.appendChild(f),d.appendChild(p);const{offsetLeft:x,offsetTop:u}=a.canvas;r.style.opacity=1,r.style.left=x+e.caretX+"px",r.style.top=u+e.caretY-r.clientHeight-16+"px"};