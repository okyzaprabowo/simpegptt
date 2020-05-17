!function(e,r){var n=function(e){var r={};function n(i){if(r[i])return r[i].exports;var t=r[i]={i:i,l:!1,exports:{}};return e[i].call(t.exports,t,t.exports,n),t.l=!0,t.exports}return n.m=e,n.c=r,n.d=function(e,r,i){n.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,r){if(1&r&&(e=n(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var t in e)n.d(i,t,function(r){return e[r]}.bind(null,t));return i},n.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(r,"a",r),r},n.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},n.p="",n(n.s=523)}({523:function(e,r,n){"use strict";n.r(r);var i=n(63);n.n(i),n.d(r,"numeral",function(){return i}),n(524)},524:function(e,r,n){var i,t,l;t=[n(63)],void 0===(l="function"==typeof(i=function(e){var r;e.register("locale","bg",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"хил",million:"млн",billion:"млрд",trillion:"трлн"},ordinal:function(e){return""},currency:{symbol:"лв"}}),e.register("locale","chs",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"千",million:"百万",billion:"十亿",trillion:"兆"},ordinal:function(e){return"."},currency:{symbol:"¥"}}),e.register("locale","cs",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"tis.",million:"mil.",billion:"b",trillion:"t"},ordinal:function(){return"."},currency:{symbol:"Kč"}}),e.register("locale","da-dk",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"k",million:"mio",billion:"mia",trillion:"b"},ordinal:function(e){return"."},currency:{symbol:"DKK"}}),e.register("locale","de-ch",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return"."},currency:{symbol:"CHF"}}),e.register("locale","de",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return"."},currency:{symbol:"€"}}),e.register("locale","en-au",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1==~~(e%100/10)?"th":1===r?"st":2===r?"nd":3===r?"rd":"th"},currency:{symbol:"$"}}),e.register("locale","en-gb",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1==~~(e%100/10)?"th":1===r?"st":2===r?"nd":3===r?"rd":"th"},currency:{symbol:"£"}}),e.register("locale","en-za",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1==~~(e%100/10)?"th":1===r?"st":2===r?"nd":3===r?"rd":"th"},currency:{symbol:"R"}}),e.register("locale","es-es",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"k",million:"mm",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1===r||3===r?"er":2===r?"do":7===r||0===r?"mo":8===r?"vo":9===r?"no":"to"},currency:{symbol:"€"}}),e.register("locale","es",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"k",million:"mm",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1===r||3===r?"er":2===r?"do":7===r||0===r?"mo":8===r?"vo":9===r?"no":"to"},currency:{symbol:"$"}}),e.register("locale","et",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:" tuh",million:" mln",billion:" mld",trillion:" trl"},ordinal:function(e){return"."},currency:{symbol:"€"}}),e.register("locale","fi",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"M",billion:"G",trillion:"T"},ordinal:function(e){return"."},currency:{symbol:"€"}}),e.register("locale","fr-ca",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"M",billion:"G",trillion:"T"},ordinal:function(e){return 1===e?"er":"e"},currency:{symbol:"$"}}),e.register("locale","fr-ch",{delimiters:{thousands:"'",decimal:"."},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return 1===e?"er":"e"},currency:{symbol:"CHF"}}),e.register("locale","fr",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return 1===e?"er":"e"},currency:{symbol:"€"}}),e.register("locale","hu",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"E",million:"M",billion:"Mrd",trillion:"T"},ordinal:function(e){return"."},currency:{symbol:" Ft"}}),e.register("locale","it",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"mila",million:"mil",billion:"b",trillion:"t"},ordinal:function(e){return"º"},currency:{symbol:"€"}}),e.register("locale","ja",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"千",million:"百万",billion:"十億",trillion:"兆"},ordinal:function(e){return"."},currency:{symbol:"¥"}}),e.register("locale","lv",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:" tūkst.",million:" milj.",billion:" mljrd.",trillion:" trilj."},ordinal:function(e){return"."},currency:{symbol:"€"}}),e.register("locale","nl-be",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:" mln",billion:" mld",trillion:" bln"},ordinal:function(e){var r=e%100;return 0!==e&&r<=1||8===r||r>=20?"ste":"de"},currency:{symbol:"€ "}}),e.register("locale","nl-nl",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"k",million:"mln",billion:"mrd",trillion:"bln"},ordinal:function(e){var r=e%100;return 0!==e&&r<=1||8===r||r>=20?"ste":"de"},currency:{symbol:"€ "}}),e.register("locale","no",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return"."},currency:{symbol:"kr"}}),e.register("locale","pl",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"tys.",million:"mln",billion:"mld",trillion:"bln"},ordinal:function(e){return"."},currency:{symbol:"PLN"}}),e.register("locale","pt-br",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"mil",million:"milhões",billion:"b",trillion:"t"},ordinal:function(e){return"º"},currency:{symbol:"R$"}}),e.register("locale","pt-pt",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){return"º"},currency:{symbol:"€"}}),e.register("locale","ru-ua",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"тыс.",million:"млн",billion:"b",trillion:"t"},ordinal:function(){return"."},currency:{symbol:"₴"}}),e.register("locale","ru",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"тыс.",million:"млн.",billion:"млрд.",trillion:"трлн."},ordinal:function(){return"."},currency:{symbol:"руб."}}),e.register("locale","sk",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"tis.",million:"mil.",billion:"b",trillion:"t"},ordinal:function(){return"."},currency:{symbol:"€"}}),e.register("locale","sl",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"k",million:"mio",billion:"mrd",trillion:"trilijon"},ordinal:function(){return"."},currency:{symbol:"€"}}),e.register("locale","th",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"พัน",million:"ล้าน",billion:"พันล้าน",trillion:"ล้านล้าน"},ordinal:function(e){return"."},currency:{symbol:"฿"}}),r={1:"'inci",5:"'inci",8:"'inci",70:"'inci",80:"'inci",2:"'nci",7:"'nci",20:"'nci",50:"'nci",3:"'üncü",4:"'üncü",100:"'üncü",6:"'ncı",9:"'uncu",10:"'uncu",30:"'uncu",60:"'ıncı",90:"'ıncı"},e.register("locale","tr",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:"bin",million:"milyon",billion:"milyar",trillion:"trilyon"},ordinal:function(e){if(0===e)return"'ıncı";var n=e%10;return r[n]||r[e%100-n]||r[e>=100?100:null]},currency:{symbol:"₺"}}),e.register("locale","uk-ua",{delimiters:{thousands:" ",decimal:","},abbreviations:{thousand:"тис.",million:"млн",billion:"млрд",trillion:"блн"},ordinal:function(){return""},currency:{symbol:"₴"}}),e.register("locale","vi",{delimiters:{thousands:".",decimal:","},abbreviations:{thousand:" nghìn",million:" triệu",billion:" tỷ",trillion:" nghìn tỷ"},ordinal:function(){return"."},currency:{symbol:"₫"}})})?i.apply(r,t):i)||(e.exports=l)},63:function(e,r,n){var i,t;void 0===(t="function"==typeof(i=function(){var e,r,n,i,t,l={},o={},a={currentLocale:"en",zeroFormat:null,nullFormat:null,defaultFormat:"0,0",scalePercentBy100:!0},u={currentLocale:a.currentLocale,zeroFormat:a.zeroFormat,nullFormat:a.nullFormat,defaultFormat:a.defaultFormat,scalePercentBy100:a.scalePercentBy100};function s(e,r){this._input=e,this._value=r}return(e=function(n){var i,t,o,a;if(e.isNumeral(n))i=n.value();else if(0===n||void 0===n)i=0;else if(null===n||r.isNaN(n))i=null;else if("string"==typeof n)if(u.zeroFormat&&n===u.zeroFormat)i=0;else if(u.nullFormat&&n===u.nullFormat||!n.replace(/[^0-9]+/g,"").length)i=null;else{for(t in l)if((a="function"==typeof l[t].regexps.unformat?l[t].regexps.unformat():l[t].regexps.unformat)&&n.match(a)){o=l[t].unformat;break}i=(o=o||e._.stringToNumber)(n)}else i=Number(n)||null;return new s(n,i)}).version="2.0.6",e.isNumeral=function(e){return e instanceof s},e._=r={numberToFormat:function(r,n,i){var t,l,a,u,s,c,m,d,b=o[e.options.currentLocale],f=!1,h=!1,y="",p="",g=!1;if(r=r||0,a=Math.abs(r),e._.includes(n,"(")?(f=!0,n=n.replace(/[\(|\)]/g,"")):(e._.includes(n,"+")||e._.includes(n,"-"))&&(c=e._.includes(n,"+")?n.indexOf("+"):r<0?n.indexOf("-"):-1,n=n.replace(/[\+|\-]/g,"")),e._.includes(n,"a")&&(l=!!(l=n.match(/a(k|m|b|t)?/))&&l[1],e._.includes(n," a")&&(y=" "),n=n.replace(new RegExp(y+"a[kmbt]?"),""),a>=1e12&&!l||"t"===l?(y+=b.abbreviations.trillion,r/=1e12):a<1e12&&a>=1e9&&!l||"b"===l?(y+=b.abbreviations.billion,r/=1e9):a<1e9&&a>=1e6&&!l||"m"===l?(y+=b.abbreviations.million,r/=1e6):(a<1e6&&a>=1e3&&!l||"k"===l)&&(y+=b.abbreviations.thousand,r/=1e3)),e._.includes(n,"[.]")&&(h=!0,n=n.replace("[.]",".")),u=r.toString().split(".")[0],s=n.split(".")[1],m=n.indexOf(","),t=(n.split(".")[0].split(",")[0].match(/0/g)||[]).length,s?(e._.includes(s,"[")?(s=(s=s.replace("]","")).split("["),p=e._.toFixed(r,s[0].length+s[1].length,i,s[1].length)):p=e._.toFixed(r,s.length,i),u=p.split(".")[0],p=e._.includes(p,".")?b.delimiters.decimal+p.split(".")[1]:"",h&&0===Number(p.slice(1))&&(p="")):u=e._.toFixed(r,0,i),y&&!l&&Number(u)>=1e3&&y!==b.abbreviations.trillion)switch(u=String(Number(u)/1e3),y){case b.abbreviations.thousand:y=b.abbreviations.million;break;case b.abbreviations.million:y=b.abbreviations.billion;break;case b.abbreviations.billion:y=b.abbreviations.trillion}if(e._.includes(u,"-")&&(u=u.slice(1),g=!0),u.length<t)for(var v=t-u.length;v>0;v--)u="0"+u;return m>-1&&(u=u.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g,"$1"+b.delimiters.thousands)),0===n.indexOf(".")&&(u=""),d=u+p+(y||""),f?d=(f&&g?"(":"")+d+(f&&g?")":""):c>=0?d=0===c?(g?"-":"+")+d:d+(g?"-":"+"):g&&(d="-"+d),d},stringToNumber:function(e){var r,n,i,t=o[u.currentLocale],l=e,a={thousand:3,million:6,billion:9,trillion:12};if(u.zeroFormat&&e===u.zeroFormat)n=0;else if(u.nullFormat&&e===u.nullFormat||!e.replace(/[^0-9]+/g,"").length)n=null;else{for(r in n=1,"."!==t.delimiters.decimal&&(e=e.replace(/\./g,"").replace(t.delimiters.decimal,".")),a)if(i=new RegExp("[^a-zA-Z]"+t.abbreviations[r]+"(?:\\)|(\\"+t.currency.symbol+")?(?:\\))?)?$"),l.match(i)){n*=Math.pow(10,a[r]);break}n*=(e.split("-").length+Math.min(e.split("(").length-1,e.split(")").length-1))%2?1:-1,e=e.replace(/[^0-9\.]+/g,""),n*=Number(e)}return n},isNaN:function(e){return"number"==typeof e&&isNaN(e)},includes:function(e,r){return-1!==e.indexOf(r)},insert:function(e,r,n){return e.slice(0,n)+r+e.slice(n)},reduce:function(e,r){if(null===this)throw new TypeError("Array.prototype.reduce called on null or undefined");if("function"!=typeof r)throw new TypeError(r+" is not a function");var n,i=Object(e),t=i.length>>>0,l=0;if(3===arguments.length)n=arguments[2];else{for(;l<t&&!(l in i);)l++;if(l>=t)throw new TypeError("Reduce of empty array with no initial value");n=i[l++]}for(;l<t;l++)l in i&&(n=r(n,i[l],l,i));return n},multiplier:function(e){var r=e.toString().split(".");return r.length<2?1:Math.pow(10,r[1].length)},correctionFactor:function(){return Array.prototype.slice.call(arguments).reduce(function(e,n){var i=r.multiplier(n);return e>i?e:i},1)},toFixed:function(e,r,n,i){var t,l,o,a,u=e.toString().split("."),s=r-(i||0);return t=2===u.length?Math.min(Math.max(u[1].length,s),r):s,o=Math.pow(10,t),a=(n(e+"e+"+t)/o).toFixed(t),i>r-t&&(l=new RegExp("\\.?0{1,"+(i-(r-t))+"}$"),a=a.replace(l,"")),a}},e.options=u,e.formats=l,e.locales=o,e.locale=function(e){return e&&(u.currentLocale=e.toLowerCase()),u.currentLocale},e.localeData=function(e){if(!e)return o[u.currentLocale];if(e=e.toLowerCase(),!o[e])throw new Error("Unknown locale : "+e);return o[e]},e.reset=function(){for(var e in a)u[e]=a[e]},e.zeroFormat=function(e){u.zeroFormat="string"==typeof e?e:null},e.nullFormat=function(e){u.nullFormat="string"==typeof e?e:null},e.defaultFormat=function(e){u.defaultFormat="string"==typeof e?e:"0.0"},e.register=function(e,r,n){if(r=r.toLowerCase(),this[e+"s"][r])throw new TypeError(r+" "+e+" already registered.");return this[e+"s"][r]=n,n},e.validate=function(r,n){var i,t,l,o,a,u,s,c;if("string"!=typeof r&&(r+="",console.warn&&console.warn("Numeral.js: Value is not string. It has been co-erced to: ",r)),(r=r.trim()).match(/^\d+$/))return!0;if(""===r)return!1;try{s=e.localeData(n)}catch(r){s=e.localeData(e.locale())}return l=s.currency.symbol,a=s.abbreviations,i=s.delimiters.decimal,t="."===s.delimiters.thousands?"\\.":s.delimiters.thousands,!(null!==(c=r.match(/^[^\d]+/))&&(r=r.substr(1),c[0]!==l)||null!==(c=r.match(/[^\d]+$/))&&(r=r.slice(0,-1),c[0]!==a.thousand&&c[0]!==a.million&&c[0]!==a.billion&&c[0]!==a.trillion)||(u=new RegExp(t+"{2}"),r.match(/[^\d.,]/g)||(o=r.split(i)).length>2||(o.length<2?!o[0].match(/^\d+.*\d$/)||o[0].match(u):1===o[0].length?!o[0].match(/^\d+$/)||o[0].match(u)||!o[1].match(/^\d+$/):!o[0].match(/^\d+.*\d$/)||o[0].match(u)||!o[1].match(/^\d+$/))))},e.fn=s.prototype={clone:function(){return e(this)},format:function(r,n){var i,t,o,a=this._value,s=r||u.defaultFormat;if(n=n||Math.round,0===a&&null!==u.zeroFormat)t=u.zeroFormat;else if(null===a&&null!==u.nullFormat)t=u.nullFormat;else{for(i in l)if(s.match(l[i].regexps.format)){o=l[i].format;break}t=(o=o||e._.numberToFormat)(a,s,n)}return t},value:function(){return this._value},input:function(){return this._input},set:function(e){return this._value=Number(e),this},add:function(e){var n=r.correctionFactor.call(null,this._value,e);return this._value=r.reduce([this._value,e],function(e,r,i,t){return e+Math.round(n*r)},0)/n,this},subtract:function(e){var n=r.correctionFactor.call(null,this._value,e);return this._value=r.reduce([e],function(e,r,i,t){return e-Math.round(n*r)},Math.round(this._value*n))/n,this},multiply:function(e){return this._value=r.reduce([this._value,e],function(e,n,i,t){var l=r.correctionFactor(e,n);return Math.round(e*l)*Math.round(n*l)/Math.round(l*l)},1),this},divide:function(e){return this._value=r.reduce([this._value,e],function(e,n,i,t){var l=r.correctionFactor(e,n);return Math.round(e*l)/Math.round(n*l)}),this},difference:function(r){return Math.abs(e(this._value).subtract(r).value())}},e.register("locale","en",{delimiters:{thousands:",",decimal:"."},abbreviations:{thousand:"k",million:"m",billion:"b",trillion:"t"},ordinal:function(e){var r=e%10;return 1==~~(e%100/10)?"th":1===r?"st":2===r?"nd":3===r?"rd":"th"},currency:{symbol:"$"}}),e.register("format","bps",{regexps:{format:/(BPS)/,unformat:/(BPS)/},format:function(r,n,i){var t,l=e._.includes(n," BPS")?" ":"";return r*=1e4,n=n.replace(/\s?BPS/,""),t=e._.numberToFormat(r,n,i),e._.includes(t,")")?((t=t.split("")).splice(-1,0,l+"BPS"),t=t.join("")):t=t+l+"BPS",t},unformat:function(r){return+(1e-4*e._.stringToNumber(r)).toFixed(15)}}),i={base:1024,suffixes:["B","KiB","MiB","GiB","TiB","PiB","EiB","ZiB","YiB"]},t="("+(t=(n={base:1e3,suffixes:["B","KB","MB","GB","TB","PB","EB","ZB","YB"]}).suffixes.concat(i.suffixes.filter(function(e){return n.suffixes.indexOf(e)<0})).join("|")).replace("B","B(?!PS)")+")",e.register("format","bytes",{regexps:{format:/([0\s]i?b)/,unformat:new RegExp(t)},format:function(r,t,l){var o,a,u,s=e._.includes(t,"ib")?i:n,c=e._.includes(t," b")||e._.includes(t," ib")?" ":"";for(t=t.replace(/\s?i?b/,""),o=0;o<=s.suffixes.length;o++)if(a=Math.pow(s.base,o),u=Math.pow(s.base,o+1),null===r||0===r||r>=a&&r<u){c+=s.suffixes[o],a>0&&(r/=a);break}return e._.numberToFormat(r,t,l)+c},unformat:function(r){var t,l,o=e._.stringToNumber(r);if(o){for(t=n.suffixes.length-1;t>=0;t--){if(e._.includes(r,n.suffixes[t])){l=Math.pow(n.base,t);break}if(e._.includes(r,i.suffixes[t])){l=Math.pow(i.base,t);break}}o*=l||1}return o}}),e.register("format","currency",{regexps:{format:/(\$)/},format:function(r,n,i){var t,l,o=e.locales[e.options.currentLocale],a={before:n.match(/^([\+|\-|\(|\s|\$]*)/)[0],after:n.match(/([\+|\-|\)|\s|\$]*)$/)[0]};for(n=n.replace(/\s?\$\s?/,""),t=e._.numberToFormat(r,n,i),r>=0?(a.before=a.before.replace(/[\-\(]/,""),a.after=a.after.replace(/[\-\)]/,"")):r<0&&!e._.includes(a.before,"-")&&!e._.includes(a.before,"(")&&(a.before="-"+a.before),l=0;l<a.before.length;l++)switch(a.before[l]){case"$":t=e._.insert(t,o.currency.symbol,l);break;case" ":t=e._.insert(t," ",l+o.currency.symbol.length-1)}for(l=a.after.length-1;l>=0;l--)switch(a.after[l]){case"$":t=l===a.after.length-1?t+o.currency.symbol:e._.insert(t,o.currency.symbol,-(a.after.length-(1+l)));break;case" ":t=l===a.after.length-1?t+" ":e._.insert(t," ",-(a.after.length-(1+l)+o.currency.symbol.length-1))}return t}}),e.register("format","exponential",{regexps:{format:/(e\+|e-)/,unformat:/(e\+|e-)/},format:function(r,n,i){var t=("number"!=typeof r||e._.isNaN(r)?"0e+0":r.toExponential()).split("e");return n=n.replace(/e[\+|\-]{1}0/,""),e._.numberToFormat(Number(t[0]),n,i)+"e"+t[1]},unformat:function(r){var n=e._.includes(r,"e+")?r.split("e+"):r.split("e-"),i=Number(n[0]),t=Number(n[1]);return t=e._.includes(r,"e-")?t*=-1:t,e._.reduce([i,Math.pow(10,t)],function(r,n,i,t){var l=e._.correctionFactor(r,n);return r*l*(n*l)/(l*l)},1)}}),e.register("format","ordinal",{regexps:{format:/(o)/},format:function(r,n,i){var t=e.locales[e.options.currentLocale],l=e._.includes(n," o")?" ":"";return n=n.replace(/\s?o/,""),l+=t.ordinal(r),e._.numberToFormat(r,n,i)+l}}),e.register("format","percentage",{regexps:{format:/(%)/,unformat:/(%)/},format:function(r,n,i){var t,l=e._.includes(n," %")?" ":"";return e.options.scalePercentBy100&&(r*=100),n=n.replace(/\s?\%/,""),t=e._.numberToFormat(r,n,i),e._.includes(t,")")?((t=t.split("")).splice(-1,0,l+"%"),t=t.join("")):t=t+l+"%",t},unformat:function(r){var n=e._.stringToNumber(r);return e.options.scalePercentBy100?.01*n:n}}),e.register("format","time",{regexps:{format:/(:)/,unformat:/(:)/},format:function(e,r,n){var i=Math.floor(e/60/60),t=Math.floor((e-60*i*60)/60),l=Math.round(e-60*i*60-60*t);return i+":"+(t<10?"0"+t:t)+":"+(l<10?"0"+l:l)},unformat:function(e){var r=e.split(":"),n=0;return 3===r.length?(n+=60*Number(r[0])*60,n+=60*Number(r[1]),n+=Number(r[2])):2===r.length&&(n+=60*Number(r[0]),n+=Number(r[1])),Number(n)}}),e})?i.call(r,n,r,e):i)||(e.exports=t)}});if("object"==typeof n){var i=["object"==typeof module&&"object"==typeof module.exports?module.exports:null,"undefined"!=typeof window?window:null,e&&e!==window?e:null];for(var t in n)i[0]&&(i[0][t]=n[t]),i[1]&&"__esModule"!==t&&(i[1][t]=n[t]),i[2]&&(i[2][t]=n[t])}}(this);
