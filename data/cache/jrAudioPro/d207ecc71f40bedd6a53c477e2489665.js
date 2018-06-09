/* mf6.paradigmusic.com */
var core_system_url='http://mf6.paradigmusic.com';
var core_active_skin='jrAudioPro';
var jrImage_url='image';
var jrCore_url='core';
var jrMailer_url='mailer';
var jrUser_url='user';
var jrSiteBuilder_url='sbcore';
var jrGraph_url='graph';
var jrPlaylist_url='playlist';
var jrPhotoAlbum_url='photoalbum';
var jrPayment_url='payment';
var jrDeveloper_url='developer';
var jrFollower_url='follow';
var jrMarket_url='marketplace';
var jrSearch_url='search';
var jrSupport_url='support';
var jrTips_url='tips';
var jrUpimg_url='upimg';
var jrAudio_url='uploaded_audio';
var jrCustomForm_url='form';
var jrOneAll_url='oneall';
var jrFAQ_url='faq';
var jrChat_url='chat';
var jrGroupDiscuss_url='group_discuss';
var jrGallery_url='gallery';
var jrLaunch_url='launch';
var jrSoundCloud_url='soundcloud';
var jrRating_url='rating';
var jrLike_url='like';
var jrEvent_url='event';
var jrFile_url='file';
var jrGroupPage_url='group_page';
var jrPlaylistAds_url='playlistads';
var jrCharts_url='charts';
var jrSeamless_url='seamless';
var jrDocs_url='documentation';
var jrBirthday_url='birthday';
var jrRecommend_url='recommend';
var jrChainedSelect_url='chained_select';
var jrGroupMailer_url='groupmailer';
var jrInvite_url='invite';
var jrCombinedVideo_url='videocombined';
var jrCombinedAudio_url='audiocombined';
var jrVideo_url='uploaded_video';
var jrProduct_url='product';
var jrSubscribe_url='subscribe';
var jrEmbed_url='embed';
var jrProfile_url='profile';
var jrBundle_url='itembundle';
var jrGroup_url='group';
var jrAction_url='timeline';
var jrComment_url='comment';
var jrShareThis_url='sharethis';
var jrProfileTweaks_url='profiletweaks';

/*! jQuery v1.12.4 | (c) jQuery Foundation | jquery.org/license */
!function(a,b){"object"==typeof module&&"object"==typeof module.exports?module.exports=a.document?b(a,!0):function(a){if(!a.document)throw new Error("jQuery requires a window with a document");return b(a)}:b(a)}("undefined"!=typeof window?window:this,function(a,b){var c=[],d=a.document,e=c.slice,f=c.concat,g=c.push,h=c.indexOf,i={},j=i.toString,k=i.hasOwnProperty,l={},m="1.12.4",n=function(a,b){return new n.fn.init(a,b)},o=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,p=/^-ms-/,q=/-([\da-z])/gi,r=function(a,b){return b.toUpperCase()};n.fn=n.prototype={jquery:m,constructor:n,selector:"",length:0,toArray:function(){return e.call(this)},get:function(a){return null!=a?0>a?this[a+this.length]:this[a]:e.call(this)},pushStack:function(a){var b=n.merge(this.constructor(),a);return b.prevObject=this,b.context=this.context,b},each:function(a){return n.each(this,a)},map:function(a){return this.pushStack(n.map(this,function(b,c){return a.call(b,c,b)}))},slice:function(){return this.pushStack(e.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(a){var b=this.length,c=+a+(0>a?b:0);return this.pushStack(c>=0&&b>c?[this[c]]:[])},end:function(){return this.prevObject||this.constructor()},push:g,sort:c.sort,splice:c.splice},n.extend=n.fn.extend=function(){var a,b,c,d,e,f,g=arguments[0]||{},h=1,i=arguments.length,j=!1;for("boolean"==typeof g&&(j=g,g=arguments[h]||{},h++),"object"==typeof g||n.isFunction(g)||(g={}),h===i&&(g=this,h--);i>h;h++)if(null!=(e=arguments[h]))for(d in e)a=g[d],c=e[d],g!==c&&(j&&c&&(n.isPlainObject(c)||(b=n.isArray(c)))?(b?(b=!1,f=a&&n.isArray(a)?a:[]):f=a&&n.isPlainObject(a)?a:{},g[d]=n.extend(j,f,c)):void 0!==c&&(g[d]=c));return g},n.extend({expando:"jQuery"+(m+Math.random()).replace(/\D/g,""),isReady:!0,error:function(a){throw new Error(a)},noop:function(){},isFunction:function(a){return"function"===n.type(a)},isArray:Array.isArray||function(a){return"array"===n.type(a)},isWindow:function(a){return null!=a&&a==a.window},isNumeric:function(a){var b=a&&a.toString();return!n.isArray(a)&&b-parseFloat(b)+1>=0},isEmptyObject:function(a){var b;for(b in a)return!1;return!0},isPlainObject:function(a){var b;if(!a||"object"!==n.type(a)||a.nodeType||n.isWindow(a))return!1;try{if(a.constructor&&!k.call(a,"constructor")&&!k.call(a.constructor.prototype,"isPrototypeOf"))return!1}catch(c){return!1}if(!l.ownFirst)for(b in a)return k.call(a,b);for(b in a);return void 0===b||k.call(a,b)},type:function(a){return null==a?a+"":"object"==typeof a||"function"==typeof a?i[j.call(a)]||"object":typeof a},globalEval:function(b){b&&n.trim(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(p,"ms-").replace(q,r)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toLowerCase()===b.toLowerCase()},each:function(a,b){var c,d=0;if(s(a)){for(c=a.length;c>d;d++)if(b.call(a[d],d,a[d])===!1)break}else for(d in a)if(b.call(a[d],d,a[d])===!1)break;return a},trim:function(a){return null==a?"":(a+"").replace(o,"")},makeArray:function(a,b){var c=b||[];return null!=a&&(s(Object(a))?n.merge(c,"string"==typeof a?[a]:a):g.call(c,a)),c},inArray:function(a,b,c){var d;if(b){if(h)return h.call(b,a,c);for(d=b.length,c=c?0>c?Math.max(0,d+c):c:0;d>c;c++)if(c in b&&b[c]===a)return c}return-1},merge:function(a,b){var c=+b.length,d=0,e=a.length;while(c>d)a[e++]=b[d++];if(c!==c)while(void 0!==b[d])a[e++]=b[d++];return a.length=e,a},grep:function(a,b,c){for(var d,e=[],f=0,g=a.length,h=!c;g>f;f++)d=!b(a[f],f),d!==h&&e.push(a[f]);return e},map:function(a,b,c){var d,e,g=0,h=[];if(s(a))for(d=a.length;d>g;g++)e=b(a[g],g,c),null!=e&&h.push(e);else for(g in a)e=b(a[g],g,c),null!=e&&h.push(e);return f.apply([],h)},guid:1,proxy:function(a,b){var c,d,f;return"string"==typeof b&&(f=a[b],b=a,a=f),n.isFunction(a)?(c=e.call(arguments,2),d=function(){return a.apply(b||this,c.concat(e.call(arguments)))},d.guid=a.guid=a.guid||n.guid++,d):void 0},now:function(){return+new Date},support:l}),"function"==typeof Symbol&&(n.fn[Symbol.iterator]=c[Symbol.iterator]),n.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(a,b){i["[object "+b+"]"]=b.toLowerCase()});function s(a){var b=!!a&&"length"in a&&a.length,c=n.type(a);return"function"===c||n.isWindow(a)?!1:"array"===c||0===b||"number"==typeof b&&b>0&&b-1 in a}var t=function(a){var b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u="sizzle"+1*new Date,v=a.document,w=0,x=0,y=ga(),z=ga(),A=ga(),B=function(a,b){return a===b&&(l=!0),0},C=1<<31,D={}.hasOwnProperty,E=[],F=E.pop,G=E.push,H=E.push,I=E.slice,J=function(a,b){for(var c=0,d=a.length;d>c;c++)if(a[c]===b)return c;return-1},K="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",L="[\\x20\\t\\r\\n\\f]",M="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",N="\\["+L+"*("+M+")(?:"+L+"*([*^$|!~]?=)"+L+"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|("+M+"))|)"+L+"*\\]",O=":("+M+")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|"+N+")*)|.*)\\)|)",P=new RegExp(L+"+","g"),Q=new RegExp("^"+L+"+|((?:^|[^\\\\])(?:\\\\.)*)"+L+"+$","g"),R=new RegExp("^"+L+"*,"+L+"*"),S=new RegExp("^"+L+"*([>+~]|"+L+")"+L+"*"),T=new RegExp("="+L+"*([^\\]'\"]*?)"+L+"*\\]","g"),U=new RegExp(O),V=new RegExp("^"+M+"$"),W={ID:new RegExp("^#("+M+")"),CLASS:new RegExp("^\\.("+M+")"),TAG:new RegExp("^("+M+"|[*])"),ATTR:new RegExp("^"+N),PSEUDO:new RegExp("^"+O),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+L+"*(even|odd|(([+-]|)(\\d*)n|)"+L+"*(?:([+-]|)"+L+"*(\\d+)|))"+L+"*\\)|)","i"),bool:new RegExp("^(?:"+K+")$","i"),needsContext:new RegExp("^"+L+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+L+"*((?:-\\d)?\\d*)"+L+"*\\)|)(?=[^-]|$)","i")},X=/^(?:input|select|textarea|button)$/i,Y=/^h\d$/i,Z=/^[^{]+\{\s*\[native \w/,$=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,_=/[+~]/,aa=/'|\\/g,ba=new RegExp("\\\\([\\da-f]{1,6}"+L+"?|("+L+")|.)","ig"),ca=function(a,b,c){var d="0x"+b-65536;return d!==d||c?b:0>d?String.fromCharCode(d+65536):String.fromCharCode(d>>10|55296,1023&d|56320)},da=function(){m()};try{H.apply(E=I.call(v.childNodes),v.childNodes),E[v.childNodes.length].nodeType}catch(ea){H={apply:E.length?function(a,b){G.apply(a,I.call(b))}:function(a,b){var c=a.length,d=0;while(a[c++]=b[d++]);a.length=c-1}}}function fa(a,b,d,e){var f,h,j,k,l,o,r,s,w=b&&b.ownerDocument,x=b?b.nodeType:9;if(d=d||[],"string"!=typeof a||!a||1!==x&&9!==x&&11!==x)return d;if(!e&&((b?b.ownerDocument||b:v)!==n&&m(b),b=b||n,p)){if(11!==x&&(o=$.exec(a)))if(f=o[1]){if(9===x){if(!(j=b.getElementById(f)))return d;if(j.id===f)return d.push(j),d}else if(w&&(j=w.getElementById(f))&&t(b,j)&&j.id===f)return d.push(j),d}else{if(o[2])return H.apply(d,b.getElementsByTagName(a)),d;if((f=o[3])&&c.getElementsByClassName&&b.getElementsByClassName)return H.apply(d,b.getElementsByClassName(f)),d}if(c.qsa&&!A[a+" "]&&(!q||!q.test(a))){if(1!==x)w=b,s=a;else if("object"!==b.nodeName.toLowerCase()){(k=b.getAttribute("id"))?k=k.replace(aa,"\\$&"):b.setAttribute("id",k=u),r=g(a),h=r.length,l=V.test(k)?"#"+k:"[id='"+k+"']";while(h--)r[h]=l+" "+qa(r[h]);s=r.join(","),w=_.test(a)&&oa(b.parentNode)||b}if(s)try{return H.apply(d,w.querySelectorAll(s)),d}catch(y){}finally{k===u&&b.removeAttribute("id")}}}return i(a.replace(Q,"$1"),b,d,e)}function ga(){var a=[];function b(c,e){return a.push(c+" ")>d.cacheLength&&delete b[a.shift()],b[c+" "]=e}return b}function ha(a){return a[u]=!0,a}function ia(a){var b=n.createElement("div");try{return!!a(b)}catch(c){return!1}finally{b.parentNode&&b.parentNode.removeChild(b),b=null}}function ja(a,b){var c=a.split("|"),e=c.length;while(e--)d.attrHandle[c[e]]=b}function ka(a,b){var c=b&&a,d=c&&1===a.nodeType&&1===b.nodeType&&(~b.sourceIndex||C)-(~a.sourceIndex||C);if(d)return d;if(c)while(c=c.nextSibling)if(c===b)return-1;return a?1:-1}function la(a){return function(b){var c=b.nodeName.toLowerCase();return"input"===c&&b.type===a}}function ma(a){return function(b){var c=b.nodeName.toLowerCase();return("input"===c||"button"===c)&&b.type===a}}function na(a){return ha(function(b){return b=+b,ha(function(c,d){var e,f=a([],c.length,b),g=f.length;while(g--)c[e=f[g]]&&(c[e]=!(d[e]=c[e]))})})}function oa(a){return a&&"undefined"!=typeof a.getElementsByTagName&&a}c=fa.support={},f=fa.isXML=function(a){var b=a&&(a.ownerDocument||a).documentElement;return b?"HTML"!==b.nodeName:!1},m=fa.setDocument=function(a){var b,e,g=a?a.ownerDocument||a:v;return g!==n&&9===g.nodeType&&g.documentElement?(n=g,o=n.documentElement,p=!f(n),(e=n.defaultView)&&e.top!==e&&(e.addEventListener?e.addEventListener("unload",da,!1):e.attachEvent&&e.attachEvent("onunload",da)),c.attributes=ia(function(a){return a.className="i",!a.getAttribute("className")}),c.getElementsByTagName=ia(function(a){return a.appendChild(n.createComment("")),!a.getElementsByTagName("*").length}),c.getElementsByClassName=Z.test(n.getElementsByClassName),c.getById=ia(function(a){return o.appendChild(a).id=u,!n.getElementsByName||!n.getElementsByName(u).length}),c.getById?(d.find.ID=function(a,b){if("undefined"!=typeof b.getElementById&&p){var c=b.getElementById(a);return c?[c]:[]}},d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){return a.getAttribute("id")===b}}):(delete d.find.ID,d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){var c="undefined"!=typeof a.getAttributeNode&&a.getAttributeNode("id");return c&&c.value===b}}),d.find.TAG=c.getElementsByTagName?function(a,b){return"undefined"!=typeof b.getElementsByTagName?b.getElementsByTagName(a):c.qsa?b.querySelectorAll(a):void 0}:function(a,b){var c,d=[],e=0,f=b.getElementsByTagName(a);if("*"===a){while(c=f[e++])1===c.nodeType&&d.push(c);return d}return f},d.find.CLASS=c.getElementsByClassName&&function(a,b){return"undefined"!=typeof b.getElementsByClassName&&p?b.getElementsByClassName(a):void 0},r=[],q=[],(c.qsa=Z.test(n.querySelectorAll))&&(ia(function(a){o.appendChild(a).innerHTML="<a id='"+u+"'></a><select id='"+u+"-\r\\' msallowcapture=''><option selected=''></option></select>",a.querySelectorAll("[msallowcapture^='']").length&&q.push("[*^$]="+L+"*(?:''|\"\")"),a.querySelectorAll("[selected]").length||q.push("\\["+L+"*(?:value|"+K+")"),a.querySelectorAll("[id~="+u+"-]").length||q.push("~="),a.querySelectorAll(":checked").length||q.push(":checked"),a.querySelectorAll("a#"+u+"+*").length||q.push(".#.+[+~]")}),ia(function(a){var b=n.createElement("input");b.setAttribute("type","hidden"),a.appendChild(b).setAttribute("name","D"),a.querySelectorAll("[name=d]").length&&q.push("name"+L+"*[*^$|!~]?="),a.querySelectorAll(":enabled").length||q.push(":enabled",":disabled"),a.querySelectorAll("*,:x"),q.push(",.*:")})),(c.matchesSelector=Z.test(s=o.matches||o.webkitMatchesSelector||o.mozMatchesSelector||o.oMatchesSelector||o.msMatchesSelector))&&ia(function(a){c.disconnectedMatch=s.call(a,"div"),s.call(a,"[s!='']:x"),r.push("!=",O)}),q=q.length&&new RegExp(q.join("|")),r=r.length&&new RegExp(r.join("|")),b=Z.test(o.compareDocumentPosition),t=b||Z.test(o.contains)?function(a,b){var c=9===a.nodeType?a.documentElement:a,d=b&&b.parentNode;return a===d||!(!d||1!==d.nodeType||!(c.contains?c.contains(d):a.compareDocumentPosition&&16&a.compareDocumentPosition(d)))}:function(a,b){if(b)while(b=b.parentNode)if(b===a)return!0;return!1},B=b?function(a,b){if(a===b)return l=!0,0;var d=!a.compareDocumentPosition-!b.compareDocumentPosition;return d?d:(d=(a.ownerDocument||a)===(b.ownerDocument||b)?a.compareDocumentPosition(b):1,1&d||!c.sortDetached&&b.compareDocumentPosition(a)===d?a===n||a.ownerDocument===v&&t(v,a)?-1:b===n||b.ownerDocument===v&&t(v,b)?1:k?J(k,a)-J(k,b):0:4&d?-1:1)}:function(a,b){if(a===b)return l=!0,0;var c,d=0,e=a.parentNode,f=b.parentNode,g=[a],h=[b];if(!e||!f)return a===n?-1:b===n?1:e?-1:f?1:k?J(k,a)-J(k,b):0;if(e===f)return ka(a,b);c=a;while(c=c.parentNode)g.unshift(c);c=b;while(c=c.parentNode)h.unshift(c);while(g[d]===h[d])d++;return d?ka(g[d],h[d]):g[d]===v?-1:h[d]===v?1:0},n):n},fa.matches=function(a,b){return fa(a,null,null,b)},fa.matchesSelector=function(a,b){if((a.ownerDocument||a)!==n&&m(a),b=b.replace(T,"='$1']"),c.matchesSelector&&p&&!A[b+" "]&&(!r||!r.test(b))&&(!q||!q.test(b)))try{var d=s.call(a,b);if(d||c.disconnectedMatch||a.document&&11!==a.document.nodeType)return d}catch(e){}return fa(b,n,null,[a]).length>0},fa.contains=function(a,b){return(a.ownerDocument||a)!==n&&m(a),t(a,b)},fa.attr=function(a,b){(a.ownerDocument||a)!==n&&m(a);var e=d.attrHandle[b.toLowerCase()],f=e&&D.call(d.attrHandle,b.toLowerCase())?e(a,b,!p):void 0;return void 0!==f?f:c.attributes||!p?a.getAttribute(b):(f=a.getAttributeNode(b))&&f.specified?f.value:null},fa.error=function(a){throw new Error("Syntax error, unrecognized expression: "+a)},fa.uniqueSort=function(a){var b,d=[],e=0,f=0;if(l=!c.detectDuplicates,k=!c.sortStable&&a.slice(0),a.sort(B),l){while(b=a[f++])b===a[f]&&(e=d.push(f));while(e--)a.splice(d[e],1)}return k=null,a},e=fa.getText=function(a){var b,c="",d=0,f=a.nodeType;if(f){if(1===f||9===f||11===f){if("string"==typeof a.textContent)return a.textContent;for(a=a.firstChild;a;a=a.nextSibling)c+=e(a)}else if(3===f||4===f)return a.nodeValue}else while(b=a[d++])c+=e(b);return c},d=fa.selectors={cacheLength:50,createPseudo:ha,match:W,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(a){return a[1]=a[1].replace(ba,ca),a[3]=(a[3]||a[4]||a[5]||"").replace(ba,ca),"~="===a[2]&&(a[3]=" "+a[3]+" "),a.slice(0,4)},CHILD:function(a){return a[1]=a[1].toLowerCase(),"nth"===a[1].slice(0,3)?(a[3]||fa.error(a[0]),a[4]=+(a[4]?a[5]+(a[6]||1):2*("even"===a[3]||"odd"===a[3])),a[5]=+(a[7]+a[8]||"odd"===a[3])):a[3]&&fa.error(a[0]),a},PSEUDO:function(a){var b,c=!a[6]&&a[2];return W.CHILD.test(a[0])?null:(a[3]?a[2]=a[4]||a[5]||"":c&&U.test(c)&&(b=g(c,!0))&&(b=c.indexOf(")",c.length-b)-c.length)&&(a[0]=a[0].slice(0,b),a[2]=c.slice(0,b)),a.slice(0,3))}},filter:{TAG:function(a){var b=a.replace(ba,ca).toLowerCase();return"*"===a?function(){return!0}:function(a){return a.nodeName&&a.nodeName.toLowerCase()===b}},CLASS:function(a){var b=y[a+" "];return b||(b=new RegExp("(^|"+L+")"+a+"("+L+"|$)"))&&y(a,function(a){return b.test("string"==typeof a.className&&a.className||"undefined"!=typeof a.getAttribute&&a.getAttribute("class")||"")})},ATTR:function(a,b,c){return function(d){var e=fa.attr(d,a);return null==e?"!="===b:b?(e+="","="===b?e===c:"!="===b?e!==c:"^="===b?c&&0===e.indexOf(c):"*="===b?c&&e.indexOf(c)>-1:"$="===b?c&&e.slice(-c.length)===c:"~="===b?(" "+e.replace(P," ")+" ").indexOf(c)>-1:"|="===b?e===c||e.slice(0,c.length+1)===c+"-":!1):!0}},CHILD:function(a,b,c,d,e){var f="nth"!==a.slice(0,3),g="last"!==a.slice(-4),h="of-type"===b;return 1===d&&0===e?function(a){return!!a.parentNode}:function(b,c,i){var j,k,l,m,n,o,p=f!==g?"nextSibling":"previousSibling",q=b.parentNode,r=h&&b.nodeName.toLowerCase(),s=!i&&!h,t=!1;if(q){if(f){while(p){m=b;while(m=m[p])if(h?m.nodeName.toLowerCase()===r:1===m.nodeType)return!1;o=p="only"===a&&!o&&"nextSibling"}return!0}if(o=[g?q.firstChild:q.lastChild],g&&s){m=q,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n&&j[2],m=n&&q.childNodes[n];while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if(1===m.nodeType&&++t&&m===b){k[a]=[w,n,t];break}}else if(s&&(m=b,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n),t===!1)while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if((h?m.nodeName.toLowerCase()===r:1===m.nodeType)&&++t&&(s&&(l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),k[a]=[w,t]),m===b))break;return t-=e,t===d||t%d===0&&t/d>=0}}},PSEUDO:function(a,b){var c,e=d.pseudos[a]||d.setFilters[a.toLowerCase()]||fa.error("unsupported pseudo: "+a);return e[u]?e(b):e.length>1?(c=[a,a,"",b],d.setFilters.hasOwnProperty(a.toLowerCase())?ha(function(a,c){var d,f=e(a,b),g=f.length;while(g--)d=J(a,f[g]),a[d]=!(c[d]=f[g])}):function(a){return e(a,0,c)}):e}},pseudos:{not:ha(function(a){var b=[],c=[],d=h(a.replace(Q,"$1"));return d[u]?ha(function(a,b,c,e){var f,g=d(a,null,e,[]),h=a.length;while(h--)(f=g[h])&&(a[h]=!(b[h]=f))}):function(a,e,f){return b[0]=a,d(b,null,f,c),b[0]=null,!c.pop()}}),has:ha(function(a){return function(b){return fa(a,b).length>0}}),contains:ha(function(a){return a=a.replace(ba,ca),function(b){return(b.textContent||b.innerText||e(b)).indexOf(a)>-1}}),lang:ha(function(a){return V.test(a||"")||fa.error("unsupported lang: "+a),a=a.replace(ba,ca).toLowerCase(),function(b){var c;do if(c=p?b.lang:b.getAttribute("xml:lang")||b.getAttribute("lang"))return c=c.toLowerCase(),c===a||0===c.indexOf(a+"-");while((b=b.parentNode)&&1===b.nodeType);return!1}}),target:function(b){var c=a.location&&a.location.hash;return c&&c.slice(1)===b.id},root:function(a){return a===o},focus:function(a){return a===n.activeElement&&(!n.hasFocus||n.hasFocus())&&!!(a.type||a.href||~a.tabIndex)},enabled:function(a){return a.disabled===!1},disabled:function(a){return a.disabled===!0},checked:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&!!a.checked||"option"===b&&!!a.selected},selected:function(a){return a.parentNode&&a.parentNode.selectedIndex,a.selected===!0},empty:function(a){for(a=a.firstChild;a;a=a.nextSibling)if(a.nodeType<6)return!1;return!0},parent:function(a){return!d.pseudos.empty(a)},header:function(a){return Y.test(a.nodeName)},input:function(a){return X.test(a.nodeName)},button:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&"button"===a.type||"button"===b},text:function(a){var b;return"input"===a.nodeName.toLowerCase()&&"text"===a.type&&(null==(b=a.getAttribute("type"))||"text"===b.toLowerCase())},first:na(function(){return[0]}),last:na(function(a,b){return[b-1]}),eq:na(function(a,b,c){return[0>c?c+b:c]}),even:na(function(a,b){for(var c=0;b>c;c+=2)a.push(c);return a}),odd:na(function(a,b){for(var c=1;b>c;c+=2)a.push(c);return a}),lt:na(function(a,b,c){for(var d=0>c?c+b:c;--d>=0;)a.push(d);return a}),gt:na(function(a,b,c){for(var d=0>c?c+b:c;++d<b;)a.push(d);return a})}},d.pseudos.nth=d.pseudos.eq;for(b in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})d.pseudos[b]=la(b);for(b in{submit:!0,reset:!0})d.pseudos[b]=ma(b);function pa(){}pa.prototype=d.filters=d.pseudos,d.setFilters=new pa,g=fa.tokenize=function(a,b){var c,e,f,g,h,i,j,k=z[a+" "];if(k)return b?0:k.slice(0);h=a,i=[],j=d.preFilter;while(h){c&&!(e=R.exec(h))||(e&&(h=h.slice(e[0].length)||h),i.push(f=[])),c=!1,(e=S.exec(h))&&(c=e.shift(),f.push({value:c,type:e[0].replace(Q," ")}),h=h.slice(c.length));for(g in d.filter)!(e=W[g].exec(h))||j[g]&&!(e=j[g](e))||(c=e.shift(),f.push({value:c,type:g,matches:e}),h=h.slice(c.length));if(!c)break}return b?h.length:h?fa.error(a):z(a,i).slice(0)};function qa(a){for(var b=0,c=a.length,d="";c>b;b++)d+=a[b].value;return d}function ra(a,b,c){var d=b.dir,e=c&&"parentNode"===d,f=x++;return b.first?function(b,c,f){while(b=b[d])if(1===b.nodeType||e)return a(b,c,f)}:function(b,c,g){var h,i,j,k=[w,f];if(g){while(b=b[d])if((1===b.nodeType||e)&&a(b,c,g))return!0}else while(b=b[d])if(1===b.nodeType||e){if(j=b[u]||(b[u]={}),i=j[b.uniqueID]||(j[b.uniqueID]={}),(h=i[d])&&h[0]===w&&h[1]===f)return k[2]=h[2];if(i[d]=k,k[2]=a(b,c,g))return!0}}}function sa(a){return a.length>1?function(b,c,d){var e=a.length;while(e--)if(!a[e](b,c,d))return!1;return!0}:a[0]}function ta(a,b,c){for(var d=0,e=b.length;e>d;d++)fa(a,b[d],c);return c}function ua(a,b,c,d,e){for(var f,g=[],h=0,i=a.length,j=null!=b;i>h;h++)(f=a[h])&&(c&&!c(f,d,e)||(g.push(f),j&&b.push(h)));return g}function va(a,b,c,d,e,f){return d&&!d[u]&&(d=va(d)),e&&!e[u]&&(e=va(e,f)),ha(function(f,g,h,i){var j,k,l,m=[],n=[],o=g.length,p=f||ta(b||"*",h.nodeType?[h]:h,[]),q=!a||!f&&b?p:ua(p,m,a,h,i),r=c?e||(f?a:o||d)?[]:g:q;if(c&&c(q,r,h,i),d){j=ua(r,n),d(j,[],h,i),k=j.length;while(k--)(l=j[k])&&(r[n[k]]=!(q[n[k]]=l))}if(f){if(e||a){if(e){j=[],k=r.length;while(k--)(l=r[k])&&j.push(q[k]=l);e(null,r=[],j,i)}k=r.length;while(k--)(l=r[k])&&(j=e?J(f,l):m[k])>-1&&(f[j]=!(g[j]=l))}}else r=ua(r===g?r.splice(o,r.length):r),e?e(null,g,r,i):H.apply(g,r)})}function wa(a){for(var b,c,e,f=a.length,g=d.relative[a[0].type],h=g||d.relative[" "],i=g?1:0,k=ra(function(a){return a===b},h,!0),l=ra(function(a){return J(b,a)>-1},h,!0),m=[function(a,c,d){var e=!g&&(d||c!==j)||((b=c).nodeType?k(a,c,d):l(a,c,d));return b=null,e}];f>i;i++)if(c=d.relative[a[i].type])m=[ra(sa(m),c)];else{if(c=d.filter[a[i].type].apply(null,a[i].matches),c[u]){for(e=++i;f>e;e++)if(d.relative[a[e].type])break;return va(i>1&&sa(m),i>1&&qa(a.slice(0,i-1).concat({value:" "===a[i-2].type?"*":""})).replace(Q,"$1"),c,e>i&&wa(a.slice(i,e)),f>e&&wa(a=a.slice(e)),f>e&&qa(a))}m.push(c)}return sa(m)}function xa(a,b){var c=b.length>0,e=a.length>0,f=function(f,g,h,i,k){var l,o,q,r=0,s="0",t=f&&[],u=[],v=j,x=f||e&&d.find.TAG("*",k),y=w+=null==v?1:Math.random()||.1,z=x.length;for(k&&(j=g===n||g||k);s!==z&&null!=(l=x[s]);s++){if(e&&l){o=0,g||l.ownerDocument===n||(m(l),h=!p);while(q=a[o++])if(q(l,g||n,h)){i.push(l);break}k&&(w=y)}c&&((l=!q&&l)&&r--,f&&t.push(l))}if(r+=s,c&&s!==r){o=0;while(q=b[o++])q(t,u,g,h);if(f){if(r>0)while(s--)t[s]||u[s]||(u[s]=F.call(i));u=ua(u)}H.apply(i,u),k&&!f&&u.length>0&&r+b.length>1&&fa.uniqueSort(i)}return k&&(w=y,j=v),t};return c?ha(f):f}return h=fa.compile=function(a,b){var c,d=[],e=[],f=A[a+" "];if(!f){b||(b=g(a)),c=b.length;while(c--)f=wa(b[c]),f[u]?d.push(f):e.push(f);f=A(a,xa(e,d)),f.selector=a}return f},i=fa.select=function(a,b,e,f){var i,j,k,l,m,n="function"==typeof a&&a,o=!f&&g(a=n.selector||a);if(e=e||[],1===o.length){if(j=o[0]=o[0].slice(0),j.length>2&&"ID"===(k=j[0]).type&&c.getById&&9===b.nodeType&&p&&d.relative[j[1].type]){if(b=(d.find.ID(k.matches[0].replace(ba,ca),b)||[])[0],!b)return e;n&&(b=b.parentNode),a=a.slice(j.shift().value.length)}i=W.needsContext.test(a)?0:j.length;while(i--){if(k=j[i],d.relative[l=k.type])break;if((m=d.find[l])&&(f=m(k.matches[0].replace(ba,ca),_.test(j[0].type)&&oa(b.parentNode)||b))){if(j.splice(i,1),a=f.length&&qa(j),!a)return H.apply(e,f),e;break}}}return(n||h(a,o))(f,b,!p,e,!b||_.test(a)&&oa(b.parentNode)||b),e},c.sortStable=u.split("").sort(B).join("")===u,c.detectDuplicates=!!l,m(),c.sortDetached=ia(function(a){return 1&a.compareDocumentPosition(n.createElement("div"))}),ia(function(a){return a.innerHTML="<a href='#'></a>","#"===a.firstChild.getAttribute("href")})||ja("type|href|height|width",function(a,b,c){return c?void 0:a.getAttribute(b,"type"===b.toLowerCase()?1:2)}),c.attributes&&ia(function(a){return a.innerHTML="<input/>",a.firstChild.setAttribute("value",""),""===a.firstChild.getAttribute("value")})||ja("value",function(a,b,c){return c||"input"!==a.nodeName.toLowerCase()?void 0:a.defaultValue}),ia(function(a){return null==a.getAttribute("disabled")})||ja(K,function(a,b,c){var d;return c?void 0:a[b]===!0?b.toLowerCase():(d=a.getAttributeNode(b))&&d.specified?d.value:null}),fa}(a);n.find=t,n.expr=t.selectors,n.expr[":"]=n.expr.pseudos,n.uniqueSort=n.unique=t.uniqueSort,n.text=t.getText,n.isXMLDoc=t.isXML,n.contains=t.contains;var u=function(a,b,c){var d=[],e=void 0!==c;while((a=a[b])&&9!==a.nodeType)if(1===a.nodeType){if(e&&n(a).is(c))break;d.push(a)}return d},v=function(a,b){for(var c=[];a;a=a.nextSibling)1===a.nodeType&&a!==b&&c.push(a);return c},w=n.expr.match.needsContext,x=/^<([\w-]+)\s*\/?>(?:<\/\1>|)$/,y=/^.[^:#\[\.,]*$/;function z(a,b,c){if(n.isFunction(b))return n.grep(a,function(a,d){return!!b.call(a,d,a)!==c});if(b.nodeType)return n.grep(a,function(a){return a===b!==c});if("string"==typeof b){if(y.test(b))return n.filter(b,a,c);b=n.filter(b,a)}return n.grep(a,function(a){return n.inArray(a,b)>-1!==c})}n.filter=function(a,b,c){var d=b[0];return c&&(a=":not("+a+")"),1===b.length&&1===d.nodeType?n.find.matchesSelector(d,a)?[d]:[]:n.find.matches(a,n.grep(b,function(a){return 1===a.nodeType}))},n.fn.extend({find:function(a){var b,c=[],d=this,e=d.length;if("string"!=typeof a)return this.pushStack(n(a).filter(function(){for(b=0;e>b;b++)if(n.contains(d[b],this))return!0}));for(b=0;e>b;b++)n.find(a,d[b],c);return c=this.pushStack(e>1?n.unique(c):c),c.selector=this.selector?this.selector+" "+a:a,c},filter:function(a){return this.pushStack(z(this,a||[],!1))},not:function(a){return this.pushStack(z(this,a||[],!0))},is:function(a){return!!z(this,"string"==typeof a&&w.test(a)?n(a):a||[],!1).length}});var A,B=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=n.fn.init=function(a,b,c){var e,f;if(!a)return this;if(c=c||A,"string"==typeof a){if(e="<"===a.charAt(0)&&">"===a.charAt(a.length-1)&&a.length>=3?[null,a,null]:B.exec(a),!e||!e[1]&&b)return!b||b.jquery?(b||c).find(a):this.constructor(b).find(a);if(e[1]){if(b=b instanceof n?b[0]:b,n.merge(this,n.parseHTML(e[1],b&&b.nodeType?b.ownerDocument||b:d,!0)),x.test(e[1])&&n.isPlainObject(b))for(e in b)n.isFunction(this[e])?this[e](b[e]):this.attr(e,b[e]);return this}if(f=d.getElementById(e[2]),f&&f.parentNode){if(f.id!==e[2])return A.find(a);this.length=1,this[0]=f}return this.context=d,this.selector=a,this}return a.nodeType?(this.context=this[0]=a,this.length=1,this):n.isFunction(a)?"undefined"!=typeof c.ready?c.ready(a):a(n):(void 0!==a.selector&&(this.selector=a.selector,this.context=a.context),n.makeArray(a,this))};C.prototype=n.fn,A=n(d);var D=/^(?:parents|prev(?:Until|All))/,E={children:!0,contents:!0,next:!0,prev:!0};n.fn.extend({has:function(a){var b,c=n(a,this),d=c.length;return this.filter(function(){for(b=0;d>b;b++)if(n.contains(this,c[b]))return!0})},closest:function(a,b){for(var c,d=0,e=this.length,f=[],g=w.test(a)||"string"!=typeof a?n(a,b||this.context):0;e>d;d++)for(c=this[d];c&&c!==b;c=c.parentNode)if(c.nodeType<11&&(g?g.index(c)>-1:1===c.nodeType&&n.find.matchesSelector(c,a))){f.push(c);break}return this.pushStack(f.length>1?n.uniqueSort(f):f)},index:function(a){return a?"string"==typeof a?n.inArray(this[0],n(a)):n.inArray(a.jquery?a[0]:a,this):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(a,b){return this.pushStack(n.uniqueSort(n.merge(this.get(),n(a,b))))},addBack:function(a){return this.add(null==a?this.prevObject:this.prevObject.filter(a))}});function F(a,b){do a=a[b];while(a&&1!==a.nodeType);return a}n.each({parent:function(a){var b=a.parentNode;return b&&11!==b.nodeType?b:null},parents:function(a){return u(a,"parentNode")},parentsUntil:function(a,b,c){return u(a,"parentNode",c)},next:function(a){return F(a,"nextSibling")},prev:function(a){return F(a,"previousSibling")},nextAll:function(a){return u(a,"nextSibling")},prevAll:function(a){return u(a,"previousSibling")},nextUntil:function(a,b,c){return u(a,"nextSibling",c)},prevUntil:function(a,b,c){return u(a,"previousSibling",c)},siblings:function(a){return v((a.parentNode||{}).firstChild,a)},children:function(a){return v(a.firstChild)},contents:function(a){return n.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:n.merge([],a.childNodes)}},function(a,b){n.fn[a]=function(c,d){var e=n.map(this,b,c);return"Until"!==a.slice(-5)&&(d=c),d&&"string"==typeof d&&(e=n.filter(d,e)),this.length>1&&(E[a]||(e=n.uniqueSort(e)),D.test(a)&&(e=e.reverse())),this.pushStack(e)}});var G=/\S+/g;function H(a){var b={};return n.each(a.match(G)||[],function(a,c){b[c]=!0}),b}n.Callbacks=function(a){a="string"==typeof a?H(a):n.extend({},a);var b,c,d,e,f=[],g=[],h=-1,i=function(){for(e=a.once,d=b=!0;g.length;h=-1){c=g.shift();while(++h<f.length)f[h].apply(c[0],c[1])===!1&&a.stopOnFalse&&(h=f.length,c=!1)}a.memory||(c=!1),b=!1,e&&(f=c?[]:"")},j={add:function(){return f&&(c&&!b&&(h=f.length-1,g.push(c)),function d(b){n.each(b,function(b,c){n.isFunction(c)?a.unique&&j.has(c)||f.push(c):c&&c.length&&"string"!==n.type(c)&&d(c)})}(arguments),c&&!b&&i()),this},remove:function(){return n.each(arguments,function(a,b){var c;while((c=n.inArray(b,f,c))>-1)f.splice(c,1),h>=c&&h--}),this},has:function(a){return a?n.inArray(a,f)>-1:f.length>0},empty:function(){return f&&(f=[]),this},disable:function(){return e=g=[],f=c="",this},disabled:function(){return!f},lock:function(){return e=!0,c||j.disable(),this},locked:function(){return!!e},fireWith:function(a,c){return e||(c=c||[],c=[a,c.slice?c.slice():c],g.push(c),b||i()),this},fire:function(){return j.fireWith(this,arguments),this},fired:function(){return!!d}};return j},n.extend({Deferred:function(a){var b=[["resolve","done",n.Callbacks("once memory"),"resolved"],["reject","fail",n.Callbacks("once memory"),"rejected"],["notify","progress",n.Callbacks("memory")]],c="pending",d={state:function(){return c},always:function(){return e.done(arguments).fail(arguments),this},then:function(){var a=arguments;return n.Deferred(function(c){n.each(b,function(b,f){var g=n.isFunction(a[b])&&a[b];e[f[1]](function(){var a=g&&g.apply(this,arguments);a&&n.isFunction(a.promise)?a.promise().progress(c.notify).done(c.resolve).fail(c.reject):c[f[0]+"With"](this===d?c.promise():this,g?[a]:arguments)})}),a=null}).promise()},promise:function(a){return null!=a?n.extend(a,d):d}},e={};return d.pipe=d.then,n.each(b,function(a,f){var g=f[2],h=f[3];d[f[1]]=g.add,h&&g.add(function(){c=h},b[1^a][2].disable,b[2][2].lock),e[f[0]]=function(){return e[f[0]+"With"](this===e?d:this,arguments),this},e[f[0]+"With"]=g.fireWith}),d.promise(e),a&&a.call(e,e),e},when:function(a){var b=0,c=e.call(arguments),d=c.length,f=1!==d||a&&n.isFunction(a.promise)?d:0,g=1===f?a:n.Deferred(),h=function(a,b,c){return function(d){b[a]=this,c[a]=arguments.length>1?e.call(arguments):d,c===i?g.notifyWith(b,c):--f||g.resolveWith(b,c)}},i,j,k;if(d>1)for(i=new Array(d),j=new Array(d),k=new Array(d);d>b;b++)c[b]&&n.isFunction(c[b].promise)?c[b].promise().progress(h(b,j,i)).done(h(b,k,c)).fail(g.reject):--f;return f||g.resolveWith(k,c),g.promise()}});var I;n.fn.ready=function(a){return n.ready.promise().done(a),this},n.extend({isReady:!1,readyWait:1,holdReady:function(a){a?n.readyWait++:n.ready(!0)},ready:function(a){(a===!0?--n.readyWait:n.isReady)||(n.isReady=!0,a!==!0&&--n.readyWait>0||(I.resolveWith(d,[n]),n.fn.triggerHandler&&(n(d).triggerHandler("ready"),n(d).off("ready"))))}});function J(){d.addEventListener?(d.removeEventListener("DOMContentLoaded",K),a.removeEventListener("load",K)):(d.detachEvent("onreadystatechange",K),a.detachEvent("onload",K))}function K(){(d.addEventListener||"load"===a.event.type||"complete"===d.readyState)&&(J(),n.ready())}n.ready.promise=function(b){if(!I)if(I=n.Deferred(),"complete"===d.readyState||"loading"!==d.readyState&&!d.documentElement.doScroll)a.setTimeout(n.ready);else if(d.addEventListener)d.addEventListener("DOMContentLoaded",K),a.addEventListener("load",K);else{d.attachEvent("onreadystatechange",K),a.attachEvent("onload",K);var c=!1;try{c=null==a.frameElement&&d.documentElement}catch(e){}c&&c.doScroll&&!function f(){if(!n.isReady){try{c.doScroll("left")}catch(b){return a.setTimeout(f,50)}J(),n.ready()}}()}return I.promise(b)},n.ready.promise();var L;for(L in n(l))break;l.ownFirst="0"===L,l.inlineBlockNeedsLayout=!1,n(function(){var a,b,c,e;c=d.getElementsByTagName("body")[0],c&&c.style&&(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1",l.inlineBlockNeedsLayout=a=3===b.offsetWidth,a&&(c.style.zoom=1)),c.removeChild(e))}),function(){var a=d.createElement("div");l.deleteExpando=!0;try{delete a.test}catch(b){l.deleteExpando=!1}a=null}();var M=function(a){var b=n.noData[(a.nodeName+" ").toLowerCase()],c=+a.nodeType||1;return 1!==c&&9!==c?!1:!b||b!==!0&&a.getAttribute("classid")===b},N=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,O=/([A-Z])/g;function P(a,b,c){if(void 0===c&&1===a.nodeType){var d="data-"+b.replace(O,"-$1").toLowerCase();if(c=a.getAttribute(d),"string"==typeof c){try{c="true"===c?!0:"false"===c?!1:"null"===c?null:+c+""===c?+c:N.test(c)?n.parseJSON(c):c}catch(e){}n.data(a,b,c)}else c=void 0;
}return c}function Q(a){var b;for(b in a)if(("data"!==b||!n.isEmptyObject(a[b]))&&"toJSON"!==b)return!1;return!0}function R(a,b,d,e){if(M(a)){var f,g,h=n.expando,i=a.nodeType,j=i?n.cache:a,k=i?a[h]:a[h]&&h;if(k&&j[k]&&(e||j[k].data)||void 0!==d||"string"!=typeof b)return k||(k=i?a[h]=c.pop()||n.guid++:h),j[k]||(j[k]=i?{}:{toJSON:n.noop}),"object"!=typeof b&&"function"!=typeof b||(e?j[k]=n.extend(j[k],b):j[k].data=n.extend(j[k].data,b)),g=j[k],e||(g.data||(g.data={}),g=g.data),void 0!==d&&(g[n.camelCase(b)]=d),"string"==typeof b?(f=g[b],null==f&&(f=g[n.camelCase(b)])):f=g,f}}function S(a,b,c){if(M(a)){var d,e,f=a.nodeType,g=f?n.cache:a,h=f?a[n.expando]:n.expando;if(g[h]){if(b&&(d=c?g[h]:g[h].data)){n.isArray(b)?b=b.concat(n.map(b,n.camelCase)):b in d?b=[b]:(b=n.camelCase(b),b=b in d?[b]:b.split(" ")),e=b.length;while(e--)delete d[b[e]];if(c?!Q(d):!n.isEmptyObject(d))return}(c||(delete g[h].data,Q(g[h])))&&(f?n.cleanData([a],!0):l.deleteExpando||g!=g.window?delete g[h]:g[h]=void 0)}}}n.extend({cache:{},noData:{"applet ":!0,"embed ":!0,"object ":"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"},hasData:function(a){return a=a.nodeType?n.cache[a[n.expando]]:a[n.expando],!!a&&!Q(a)},data:function(a,b,c){return R(a,b,c)},removeData:function(a,b){return S(a,b)},_data:function(a,b,c){return R(a,b,c,!0)},_removeData:function(a,b){return S(a,b,!0)}}),n.fn.extend({data:function(a,b){var c,d,e,f=this[0],g=f&&f.attributes;if(void 0===a){if(this.length&&(e=n.data(f),1===f.nodeType&&!n._data(f,"parsedAttrs"))){c=g.length;while(c--)g[c]&&(d=g[c].name,0===d.indexOf("data-")&&(d=n.camelCase(d.slice(5)),P(f,d,e[d])));n._data(f,"parsedAttrs",!0)}return e}return"object"==typeof a?this.each(function(){n.data(this,a)}):arguments.length>1?this.each(function(){n.data(this,a,b)}):f?P(f,a,n.data(f,a)):void 0},removeData:function(a){return this.each(function(){n.removeData(this,a)})}}),n.extend({queue:function(a,b,c){var d;return a?(b=(b||"fx")+"queue",d=n._data(a,b),c&&(!d||n.isArray(c)?d=n._data(a,b,n.makeArray(c)):d.push(c)),d||[]):void 0},dequeue:function(a,b){b=b||"fx";var c=n.queue(a,b),d=c.length,e=c.shift(),f=n._queueHooks(a,b),g=function(){n.dequeue(a,b)};"inprogress"===e&&(e=c.shift(),d--),e&&("fx"===b&&c.unshift("inprogress"),delete f.stop,e.call(a,g,f)),!d&&f&&f.empty.fire()},_queueHooks:function(a,b){var c=b+"queueHooks";return n._data(a,c)||n._data(a,c,{empty:n.Callbacks("once memory").add(function(){n._removeData(a,b+"queue"),n._removeData(a,c)})})}}),n.fn.extend({queue:function(a,b){var c=2;return"string"!=typeof a&&(b=a,a="fx",c--),arguments.length<c?n.queue(this[0],a):void 0===b?this:this.each(function(){var c=n.queue(this,a,b);n._queueHooks(this,a),"fx"===a&&"inprogress"!==c[0]&&n.dequeue(this,a)})},dequeue:function(a){return this.each(function(){n.dequeue(this,a)})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,b){var c,d=1,e=n.Deferred(),f=this,g=this.length,h=function(){--d||e.resolveWith(f,[f])};"string"!=typeof a&&(b=a,a=void 0),a=a||"fx";while(g--)c=n._data(f[g],a+"queueHooks"),c&&c.empty&&(d++,c.empty.add(h));return h(),e.promise(b)}}),function(){var a;l.shrinkWrapBlocks=function(){if(null!=a)return a;a=!1;var b,c,e;return c=d.getElementsByTagName("body")[0],c&&c.style?(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1",b.appendChild(d.createElement("div")).style.width="5px",a=3!==b.offsetWidth),c.removeChild(e),a):void 0}}();var T=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,U=new RegExp("^(?:([+-])=|)("+T+")([a-z%]*)$","i"),V=["Top","Right","Bottom","Left"],W=function(a,b){return a=b||a,"none"===n.css(a,"display")||!n.contains(a.ownerDocument,a)};function X(a,b,c,d){var e,f=1,g=20,h=d?function(){return d.cur()}:function(){return n.css(a,b,"")},i=h(),j=c&&c[3]||(n.cssNumber[b]?"":"px"),k=(n.cssNumber[b]||"px"!==j&&+i)&&U.exec(n.css(a,b));if(k&&k[3]!==j){j=j||k[3],c=c||[],k=+i||1;do f=f||".5",k/=f,n.style(a,b,k+j);while(f!==(f=h()/i)&&1!==f&&--g)}return c&&(k=+k||+i||0,e=c[1]?k+(c[1]+1)*c[2]:+c[2],d&&(d.unit=j,d.start=k,d.end=e)),e}var Y=function(a,b,c,d,e,f,g){var h=0,i=a.length,j=null==c;if("object"===n.type(c)){e=!0;for(h in c)Y(a,b,h,c[h],!0,f,g)}else if(void 0!==d&&(e=!0,n.isFunction(d)||(g=!0),j&&(g?(b.call(a,d),b=null):(j=b,b=function(a,b,c){return j.call(n(a),c)})),b))for(;i>h;h++)b(a[h],c,g?d:d.call(a[h],h,b(a[h],c)));return e?a:j?b.call(a):i?b(a[0],c):f},Z=/^(?:checkbox|radio)$/i,$=/<([\w:-]+)/,_=/^$|\/(?:java|ecma)script/i,aa=/^\s+/,ba="abbr|article|aside|audio|bdi|canvas|data|datalist|details|dialog|figcaption|figure|footer|header|hgroup|main|mark|meter|nav|output|picture|progress|section|summary|template|time|video";function ca(a){var b=ba.split("|"),c=a.createDocumentFragment();if(c.createElement)while(b.length)c.createElement(b.pop());return c}!function(){var a=d.createElement("div"),b=d.createDocumentFragment(),c=d.createElement("input");a.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",l.leadingWhitespace=3===a.firstChild.nodeType,l.tbody=!a.getElementsByTagName("tbody").length,l.htmlSerialize=!!a.getElementsByTagName("link").length,l.html5Clone="<:nav></:nav>"!==d.createElement("nav").cloneNode(!0).outerHTML,c.type="checkbox",c.checked=!0,b.appendChild(c),l.appendChecked=c.checked,a.innerHTML="<textarea>x</textarea>",l.noCloneChecked=!!a.cloneNode(!0).lastChild.defaultValue,b.appendChild(a),c=d.createElement("input"),c.setAttribute("type","radio"),c.setAttribute("checked","checked"),c.setAttribute("name","t"),a.appendChild(c),l.checkClone=a.cloneNode(!0).cloneNode(!0).lastChild.checked,l.noCloneEvent=!!a.addEventListener,a[n.expando]=1,l.attributes=!a.getAttribute(n.expando)}();var da={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],area:[1,"<map>","</map>"],param:[1,"<object>","</object>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:l.htmlSerialize?[0,"",""]:[1,"X<div>","</div>"]};da.optgroup=da.option,da.tbody=da.tfoot=da.colgroup=da.caption=da.thead,da.th=da.td;function ea(a,b){var c,d,e=0,f="undefined"!=typeof a.getElementsByTagName?a.getElementsByTagName(b||"*"):"undefined"!=typeof a.querySelectorAll?a.querySelectorAll(b||"*"):void 0;if(!f)for(f=[],c=a.childNodes||a;null!=(d=c[e]);e++)!b||n.nodeName(d,b)?f.push(d):n.merge(f,ea(d,b));return void 0===b||b&&n.nodeName(a,b)?n.merge([a],f):f}function fa(a,b){for(var c,d=0;null!=(c=a[d]);d++)n._data(c,"globalEval",!b||n._data(b[d],"globalEval"))}var ga=/<|&#?\w+;/,ha=/<tbody/i;function ia(a){Z.test(a.type)&&(a.defaultChecked=a.checked)}function ja(a,b,c,d,e){for(var f,g,h,i,j,k,m,o=a.length,p=ca(b),q=[],r=0;o>r;r++)if(g=a[r],g||0===g)if("object"===n.type(g))n.merge(q,g.nodeType?[g]:g);else if(ga.test(g)){i=i||p.appendChild(b.createElement("div")),j=($.exec(g)||["",""])[1].toLowerCase(),m=da[j]||da._default,i.innerHTML=m[1]+n.htmlPrefilter(g)+m[2],f=m[0];while(f--)i=i.lastChild;if(!l.leadingWhitespace&&aa.test(g)&&q.push(b.createTextNode(aa.exec(g)[0])),!l.tbody){g="table"!==j||ha.test(g)?"<table>"!==m[1]||ha.test(g)?0:i:i.firstChild,f=g&&g.childNodes.length;while(f--)n.nodeName(k=g.childNodes[f],"tbody")&&!k.childNodes.length&&g.removeChild(k)}n.merge(q,i.childNodes),i.textContent="";while(i.firstChild)i.removeChild(i.firstChild);i=p.lastChild}else q.push(b.createTextNode(g));i&&p.removeChild(i),l.appendChecked||n.grep(ea(q,"input"),ia),r=0;while(g=q[r++])if(d&&n.inArray(g,d)>-1)e&&e.push(g);else if(h=n.contains(g.ownerDocument,g),i=ea(p.appendChild(g),"script"),h&&fa(i),c){f=0;while(g=i[f++])_.test(g.type||"")&&c.push(g)}return i=null,p}!function(){var b,c,e=d.createElement("div");for(b in{submit:!0,change:!0,focusin:!0})c="on"+b,(l[b]=c in a)||(e.setAttribute(c,"t"),l[b]=e.attributes[c].expando===!1);e=null}();var ka=/^(?:input|select|textarea)$/i,la=/^key/,ma=/^(?:mouse|pointer|contextmenu|drag|drop)|click/,na=/^(?:focusinfocus|focusoutblur)$/,oa=/^([^.]*)(?:\.(.+)|)/;function pa(){return!0}function qa(){return!1}function ra(){try{return d.activeElement}catch(a){}}function sa(a,b,c,d,e,f){var g,h;if("object"==typeof b){"string"!=typeof c&&(d=d||c,c=void 0);for(h in b)sa(a,h,c,d,b[h],f);return a}if(null==d&&null==e?(e=c,d=c=void 0):null==e&&("string"==typeof c?(e=d,d=void 0):(e=d,d=c,c=void 0)),e===!1)e=qa;else if(!e)return a;return 1===f&&(g=e,e=function(a){return n().off(a),g.apply(this,arguments)},e.guid=g.guid||(g.guid=n.guid++)),a.each(function(){n.event.add(this,b,e,d,c)})}n.event={global:{},add:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n._data(a);if(r){c.handler&&(i=c,c=i.handler,e=i.selector),c.guid||(c.guid=n.guid++),(g=r.events)||(g=r.events={}),(k=r.handle)||(k=r.handle=function(a){return"undefined"==typeof n||a&&n.event.triggered===a.type?void 0:n.event.dispatch.apply(k.elem,arguments)},k.elem=a),b=(b||"").match(G)||[""],h=b.length;while(h--)f=oa.exec(b[h])||[],o=q=f[1],p=(f[2]||"").split(".").sort(),o&&(j=n.event.special[o]||{},o=(e?j.delegateType:j.bindType)||o,j=n.event.special[o]||{},l=n.extend({type:o,origType:q,data:d,handler:c,guid:c.guid,selector:e,needsContext:e&&n.expr.match.needsContext.test(e),namespace:p.join(".")},i),(m=g[o])||(m=g[o]=[],m.delegateCount=0,j.setup&&j.setup.call(a,d,p,k)!==!1||(a.addEventListener?a.addEventListener(o,k,!1):a.attachEvent&&a.attachEvent("on"+o,k))),j.add&&(j.add.call(a,l),l.handler.guid||(l.handler.guid=c.guid)),e?m.splice(m.delegateCount++,0,l):m.push(l),n.event.global[o]=!0);a=null}},remove:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n.hasData(a)&&n._data(a);if(r&&(k=r.events)){b=(b||"").match(G)||[""],j=b.length;while(j--)if(h=oa.exec(b[j])||[],o=q=h[1],p=(h[2]||"").split(".").sort(),o){l=n.event.special[o]||{},o=(d?l.delegateType:l.bindType)||o,m=k[o]||[],h=h[2]&&new RegExp("(^|\\.)"+p.join("\\.(?:.*\\.|)")+"(\\.|$)"),i=f=m.length;while(f--)g=m[f],!e&&q!==g.origType||c&&c.guid!==g.guid||h&&!h.test(g.namespace)||d&&d!==g.selector&&("**"!==d||!g.selector)||(m.splice(f,1),g.selector&&m.delegateCount--,l.remove&&l.remove.call(a,g));i&&!m.length&&(l.teardown&&l.teardown.call(a,p,r.handle)!==!1||n.removeEvent(a,o,r.handle),delete k[o])}else for(o in k)n.event.remove(a,o+b[j],c,d,!0);n.isEmptyObject(k)&&(delete r.handle,n._removeData(a,"events"))}},trigger:function(b,c,e,f){var g,h,i,j,l,m,o,p=[e||d],q=k.call(b,"type")?b.type:b,r=k.call(b,"namespace")?b.namespace.split("."):[];if(i=m=e=e||d,3!==e.nodeType&&8!==e.nodeType&&!na.test(q+n.event.triggered)&&(q.indexOf(".")>-1&&(r=q.split("."),q=r.shift(),r.sort()),h=q.indexOf(":")<0&&"on"+q,b=b[n.expando]?b:new n.Event(q,"object"==typeof b&&b),b.isTrigger=f?2:3,b.namespace=r.join("."),b.rnamespace=b.namespace?new RegExp("(^|\\.)"+r.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,b.result=void 0,b.target||(b.target=e),c=null==c?[b]:n.makeArray(c,[b]),l=n.event.special[q]||{},f||!l.trigger||l.trigger.apply(e,c)!==!1)){if(!f&&!l.noBubble&&!n.isWindow(e)){for(j=l.delegateType||q,na.test(j+q)||(i=i.parentNode);i;i=i.parentNode)p.push(i),m=i;m===(e.ownerDocument||d)&&p.push(m.defaultView||m.parentWindow||a)}o=0;while((i=p[o++])&&!b.isPropagationStopped())b.type=o>1?j:l.bindType||q,g=(n._data(i,"events")||{})[b.type]&&n._data(i,"handle"),g&&g.apply(i,c),g=h&&i[h],g&&g.apply&&M(i)&&(b.result=g.apply(i,c),b.result===!1&&b.preventDefault());if(b.type=q,!f&&!b.isDefaultPrevented()&&(!l._default||l._default.apply(p.pop(),c)===!1)&&M(e)&&h&&e[q]&&!n.isWindow(e)){m=e[h],m&&(e[h]=null),n.event.triggered=q;try{e[q]()}catch(s){}n.event.triggered=void 0,m&&(e[h]=m)}return b.result}},dispatch:function(a){a=n.event.fix(a);var b,c,d,f,g,h=[],i=e.call(arguments),j=(n._data(this,"events")||{})[a.type]||[],k=n.event.special[a.type]||{};if(i[0]=a,a.delegateTarget=this,!k.preDispatch||k.preDispatch.call(this,a)!==!1){h=n.event.handlers.call(this,a,j),b=0;while((f=h[b++])&&!a.isPropagationStopped()){a.currentTarget=f.elem,c=0;while((g=f.handlers[c++])&&!a.isImmediatePropagationStopped())a.rnamespace&&!a.rnamespace.test(g.namespace)||(a.handleObj=g,a.data=g.data,d=((n.event.special[g.origType]||{}).handle||g.handler).apply(f.elem,i),void 0!==d&&(a.result=d)===!1&&(a.preventDefault(),a.stopPropagation()))}return k.postDispatch&&k.postDispatch.call(this,a),a.result}},handlers:function(a,b){var c,d,e,f,g=[],h=b.delegateCount,i=a.target;if(h&&i.nodeType&&("click"!==a.type||isNaN(a.button)||a.button<1))for(;i!=this;i=i.parentNode||this)if(1===i.nodeType&&(i.disabled!==!0||"click"!==a.type)){for(d=[],c=0;h>c;c++)f=b[c],e=f.selector+" ",void 0===d[e]&&(d[e]=f.needsContext?n(e,this).index(i)>-1:n.find(e,this,null,[i]).length),d[e]&&d.push(f);d.length&&g.push({elem:i,handlers:d})}return h<b.length&&g.push({elem:this,handlers:b.slice(h)}),g},fix:function(a){if(a[n.expando])return a;var b,c,e,f=a.type,g=a,h=this.fixHooks[f];h||(this.fixHooks[f]=h=ma.test(f)?this.mouseHooks:la.test(f)?this.keyHooks:{}),e=h.props?this.props.concat(h.props):this.props,a=new n.Event(g),b=e.length;while(b--)c=e[b],a[c]=g[c];return a.target||(a.target=g.srcElement||d),3===a.target.nodeType&&(a.target=a.target.parentNode),a.metaKey=!!a.metaKey,h.filter?h.filter(a,g):a},props:"altKey bubbles cancelable ctrlKey currentTarget detail eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(a,b){return null==a.which&&(a.which=null!=b.charCode?b.charCode:b.keyCode),a}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(a,b){var c,e,f,g=b.button,h=b.fromElement;return null==a.pageX&&null!=b.clientX&&(e=a.target.ownerDocument||d,f=e.documentElement,c=e.body,a.pageX=b.clientX+(f&&f.scrollLeft||c&&c.scrollLeft||0)-(f&&f.clientLeft||c&&c.clientLeft||0),a.pageY=b.clientY+(f&&f.scrollTop||c&&c.scrollTop||0)-(f&&f.clientTop||c&&c.clientTop||0)),!a.relatedTarget&&h&&(a.relatedTarget=h===a.target?b.toElement:h),a.which||void 0===g||(a.which=1&g?1:2&g?3:4&g?2:0),a}},special:{load:{noBubble:!0},focus:{trigger:function(){if(this!==ra()&&this.focus)try{return this.focus(),!1}catch(a){}},delegateType:"focusin"},blur:{trigger:function(){return this===ra()&&this.blur?(this.blur(),!1):void 0},delegateType:"focusout"},click:{trigger:function(){return n.nodeName(this,"input")&&"checkbox"===this.type&&this.click?(this.click(),!1):void 0},_default:function(a){return n.nodeName(a.target,"a")}},beforeunload:{postDispatch:function(a){void 0!==a.result&&a.originalEvent&&(a.originalEvent.returnValue=a.result)}}},simulate:function(a,b,c){var d=n.extend(new n.Event,c,{type:a,isSimulated:!0});n.event.trigger(d,null,b),d.isDefaultPrevented()&&c.preventDefault()}},n.removeEvent=d.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c)}:function(a,b,c){var d="on"+b;a.detachEvent&&("undefined"==typeof a[d]&&(a[d]=null),a.detachEvent(d,c))},n.Event=function(a,b){return this instanceof n.Event?(a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||void 0===a.defaultPrevented&&a.returnValue===!1?pa:qa):this.type=a,b&&n.extend(this,b),this.timeStamp=a&&a.timeStamp||n.now(),void(this[n.expando]=!0)):new n.Event(a,b)},n.Event.prototype={constructor:n.Event,isDefaultPrevented:qa,isPropagationStopped:qa,isImmediatePropagationStopped:qa,preventDefault:function(){var a=this.originalEvent;this.isDefaultPrevented=pa,a&&(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){var a=this.originalEvent;this.isPropagationStopped=pa,a&&!this.isSimulated&&(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){var a=this.originalEvent;this.isImmediatePropagationStopped=pa,a&&a.stopImmediatePropagation&&a.stopImmediatePropagation(),this.stopPropagation()}},n.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(a,b){n.event.special[a]={delegateType:b,bindType:b,handle:function(a){var c,d=this,e=a.relatedTarget,f=a.handleObj;return e&&(e===d||n.contains(d,e))||(a.type=f.origType,c=f.handler.apply(this,arguments),a.type=b),c}}}),l.submit||(n.event.special.submit={setup:function(){return n.nodeName(this,"form")?!1:void n.event.add(this,"click._submit keypress._submit",function(a){var b=a.target,c=n.nodeName(b,"input")||n.nodeName(b,"button")?n.prop(b,"form"):void 0;c&&!n._data(c,"submit")&&(n.event.add(c,"submit._submit",function(a){a._submitBubble=!0}),n._data(c,"submit",!0))})},postDispatch:function(a){a._submitBubble&&(delete a._submitBubble,this.parentNode&&!a.isTrigger&&n.event.simulate("submit",this.parentNode,a))},teardown:function(){return n.nodeName(this,"form")?!1:void n.event.remove(this,"._submit")}}),l.change||(n.event.special.change={setup:function(){return ka.test(this.nodeName)?("checkbox"!==this.type&&"radio"!==this.type||(n.event.add(this,"propertychange._change",function(a){"checked"===a.originalEvent.propertyName&&(this._justChanged=!0)}),n.event.add(this,"click._change",function(a){this._justChanged&&!a.isTrigger&&(this._justChanged=!1),n.event.simulate("change",this,a)})),!1):void n.event.add(this,"beforeactivate._change",function(a){var b=a.target;ka.test(b.nodeName)&&!n._data(b,"change")&&(n.event.add(b,"change._change",function(a){!this.parentNode||a.isSimulated||a.isTrigger||n.event.simulate("change",this.parentNode,a)}),n._data(b,"change",!0))})},handle:function(a){var b=a.target;return this!==b||a.isSimulated||a.isTrigger||"radio"!==b.type&&"checkbox"!==b.type?a.handleObj.handler.apply(this,arguments):void 0},teardown:function(){return n.event.remove(this,"._change"),!ka.test(this.nodeName)}}),l.focusin||n.each({focus:"focusin",blur:"focusout"},function(a,b){var c=function(a){n.event.simulate(b,a.target,n.event.fix(a))};n.event.special[b]={setup:function(){var d=this.ownerDocument||this,e=n._data(d,b);e||d.addEventListener(a,c,!0),n._data(d,b,(e||0)+1)},teardown:function(){var d=this.ownerDocument||this,e=n._data(d,b)-1;e?n._data(d,b,e):(d.removeEventListener(a,c,!0),n._removeData(d,b))}}}),n.fn.extend({on:function(a,b,c,d){return sa(this,a,b,c,d)},one:function(a,b,c,d){return sa(this,a,b,c,d,1)},off:function(a,b,c){var d,e;if(a&&a.preventDefault&&a.handleObj)return d=a.handleObj,n(a.delegateTarget).off(d.namespace?d.origType+"."+d.namespace:d.origType,d.selector,d.handler),this;if("object"==typeof a){for(e in a)this.off(e,b,a[e]);return this}return b!==!1&&"function"!=typeof b||(c=b,b=void 0),c===!1&&(c=qa),this.each(function(){n.event.remove(this,a,c,b)})},trigger:function(a,b){return this.each(function(){n.event.trigger(a,b,this)})},triggerHandler:function(a,b){var c=this[0];return c?n.event.trigger(a,b,c,!0):void 0}});var ta=/ jQuery\d+="(?:null|\d+)"/g,ua=new RegExp("<(?:"+ba+")[\\s/>]","i"),va=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi,wa=/<script|<style|<link/i,xa=/checked\s*(?:[^=]|=\s*.checked.)/i,ya=/^true\/(.*)/,za=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,Aa=ca(d),Ba=Aa.appendChild(d.createElement("div"));function Ca(a,b){return n.nodeName(a,"table")&&n.nodeName(11!==b.nodeType?b:b.firstChild,"tr")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function Da(a){return a.type=(null!==n.find.attr(a,"type"))+"/"+a.type,a}function Ea(a){var b=ya.exec(a.type);return b?a.type=b[1]:a.removeAttribute("type"),a}function Fa(a,b){if(1===b.nodeType&&n.hasData(a)){var c,d,e,f=n._data(a),g=n._data(b,f),h=f.events;if(h){delete g.handle,g.events={};for(c in h)for(d=0,e=h[c].length;e>d;d++)n.event.add(b,c,h[c][d])}g.data&&(g.data=n.extend({},g.data))}}function Ga(a,b){var c,d,e;if(1===b.nodeType){if(c=b.nodeName.toLowerCase(),!l.noCloneEvent&&b[n.expando]){e=n._data(b);for(d in e.events)n.removeEvent(b,d,e.handle);b.removeAttribute(n.expando)}"script"===c&&b.text!==a.text?(Da(b).text=a.text,Ea(b)):"object"===c?(b.parentNode&&(b.outerHTML=a.outerHTML),l.html5Clone&&a.innerHTML&&!n.trim(b.innerHTML)&&(b.innerHTML=a.innerHTML)):"input"===c&&Z.test(a.type)?(b.defaultChecked=b.checked=a.checked,b.value!==a.value&&(b.value=a.value)):"option"===c?b.defaultSelected=b.selected=a.defaultSelected:"input"!==c&&"textarea"!==c||(b.defaultValue=a.defaultValue)}}function Ha(a,b,c,d){b=f.apply([],b);var e,g,h,i,j,k,m=0,o=a.length,p=o-1,q=b[0],r=n.isFunction(q);if(r||o>1&&"string"==typeof q&&!l.checkClone&&xa.test(q))return a.each(function(e){var f=a.eq(e);r&&(b[0]=q.call(this,e,f.html())),Ha(f,b,c,d)});if(o&&(k=ja(b,a[0].ownerDocument,!1,a,d),e=k.firstChild,1===k.childNodes.length&&(k=e),e||d)){for(i=n.map(ea(k,"script"),Da),h=i.length;o>m;m++)g=k,m!==p&&(g=n.clone(g,!0,!0),h&&n.merge(i,ea(g,"script"))),c.call(a[m],g,m);if(h)for(j=i[i.length-1].ownerDocument,n.map(i,Ea),m=0;h>m;m++)g=i[m],_.test(g.type||"")&&!n._data(g,"globalEval")&&n.contains(j,g)&&(g.src?n._evalUrl&&n._evalUrl(g.src):n.globalEval((g.text||g.textContent||g.innerHTML||"").replace(za,"")));k=e=null}return a}function Ia(a,b,c){for(var d,e=b?n.filter(b,a):a,f=0;null!=(d=e[f]);f++)c||1!==d.nodeType||n.cleanData(ea(d)),d.parentNode&&(c&&n.contains(d.ownerDocument,d)&&fa(ea(d,"script")),d.parentNode.removeChild(d));return a}n.extend({htmlPrefilter:function(a){return a.replace(va,"<$1></$2>")},clone:function(a,b,c){var d,e,f,g,h,i=n.contains(a.ownerDocument,a);if(l.html5Clone||n.isXMLDoc(a)||!ua.test("<"+a.nodeName+">")?f=a.cloneNode(!0):(Ba.innerHTML=a.outerHTML,Ba.removeChild(f=Ba.firstChild)),!(l.noCloneEvent&&l.noCloneChecked||1!==a.nodeType&&11!==a.nodeType||n.isXMLDoc(a)))for(d=ea(f),h=ea(a),g=0;null!=(e=h[g]);++g)d[g]&&Ga(e,d[g]);if(b)if(c)for(h=h||ea(a),d=d||ea(f),g=0;null!=(e=h[g]);g++)Fa(e,d[g]);else Fa(a,f);return d=ea(f,"script"),d.length>0&&fa(d,!i&&ea(a,"script")),d=h=e=null,f},cleanData:function(a,b){for(var d,e,f,g,h=0,i=n.expando,j=n.cache,k=l.attributes,m=n.event.special;null!=(d=a[h]);h++)if((b||M(d))&&(f=d[i],g=f&&j[f])){if(g.events)for(e in g.events)m[e]?n.event.remove(d,e):n.removeEvent(d,e,g.handle);j[f]&&(delete j[f],k||"undefined"==typeof d.removeAttribute?d[i]=void 0:d.removeAttribute(i),c.push(f))}}}),n.fn.extend({domManip:Ha,detach:function(a){return Ia(this,a,!0)},remove:function(a){return Ia(this,a)},text:function(a){return Y(this,function(a){return void 0===a?n.text(this):this.empty().append((this[0]&&this[0].ownerDocument||d).createTextNode(a))},null,a,arguments.length)},append:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.appendChild(a)}})},prepend:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.insertBefore(a,b.firstChild)}})},before:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this)})},after:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this.nextSibling)})},empty:function(){for(var a,b=0;null!=(a=this[b]);b++){1===a.nodeType&&n.cleanData(ea(a,!1));while(a.firstChild)a.removeChild(a.firstChild);a.options&&n.nodeName(a,"select")&&(a.options.length=0)}return this},clone:function(a,b){return a=null==a?!1:a,b=null==b?a:b,this.map(function(){return n.clone(this,a,b)})},html:function(a){return Y(this,function(a){var b=this[0]||{},c=0,d=this.length;if(void 0===a)return 1===b.nodeType?b.innerHTML.replace(ta,""):void 0;if("string"==typeof a&&!wa.test(a)&&(l.htmlSerialize||!ua.test(a))&&(l.leadingWhitespace||!aa.test(a))&&!da[($.exec(a)||["",""])[1].toLowerCase()]){a=n.htmlPrefilter(a);try{for(;d>c;c++)b=this[c]||{},1===b.nodeType&&(n.cleanData(ea(b,!1)),b.innerHTML=a);b=0}catch(e){}}b&&this.empty().append(a)},null,a,arguments.length)},replaceWith:function(){var a=[];return Ha(this,arguments,function(b){var c=this.parentNode;n.inArray(this,a)<0&&(n.cleanData(ea(this)),c&&c.replaceChild(b,this))},a)}}),n.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){n.fn[a]=function(a){for(var c,d=0,e=[],f=n(a),h=f.length-1;h>=d;d++)c=d===h?this:this.clone(!0),n(f[d])[b](c),g.apply(e,c.get());return this.pushStack(e)}});var Ja,Ka={HTML:"block",BODY:"block"};function La(a,b){var c=n(b.createElement(a)).appendTo(b.body),d=n.css(c[0],"display");return c.detach(),d}function Ma(a){var b=d,c=Ka[a];return c||(c=La(a,b),"none"!==c&&c||(Ja=(Ja||n("<iframe frameborder='0' width='0' height='0'/>")).appendTo(b.documentElement),b=(Ja[0].contentWindow||Ja[0].contentDocument).document,b.write(),b.close(),c=La(a,b),Ja.detach()),Ka[a]=c),c}var Na=/^margin/,Oa=new RegExp("^("+T+")(?!px)[a-z%]+$","i"),Pa=function(a,b,c,d){var e,f,g={};for(f in b)g[f]=a.style[f],a.style[f]=b[f];e=c.apply(a,d||[]);for(f in b)a.style[f]=g[f];return e},Qa=d.documentElement;!function(){var b,c,e,f,g,h,i=d.createElement("div"),j=d.createElement("div");if(j.style){j.style.cssText="float:left;opacity:.5",l.opacity="0.5"===j.style.opacity,l.cssFloat=!!j.style.cssFloat,j.style.backgroundClip="content-box",j.cloneNode(!0).style.backgroundClip="",l.clearCloneStyle="content-box"===j.style.backgroundClip,i=d.createElement("div"),i.style.cssText="border:0;width:8px;height:0;top:0;left:-9999px;padding:0;margin-top:1px;position:absolute",j.innerHTML="",i.appendChild(j),l.boxSizing=""===j.style.boxSizing||""===j.style.MozBoxSizing||""===j.style.WebkitBoxSizing,n.extend(l,{reliableHiddenOffsets:function(){return null==b&&k(),f},boxSizingReliable:function(){return null==b&&k(),e},pixelMarginRight:function(){return null==b&&k(),c},pixelPosition:function(){return null==b&&k(),b},reliableMarginRight:function(){return null==b&&k(),g},reliableMarginLeft:function(){return null==b&&k(),h}});function k(){var k,l,m=d.documentElement;m.appendChild(i),j.style.cssText="-webkit-box-sizing:border-box;box-sizing:border-box;position:relative;display:block;margin:auto;border:1px;padding:1px;top:1%;width:50%",b=e=h=!1,c=g=!0,a.getComputedStyle&&(l=a.getComputedStyle(j),b="1%"!==(l||{}).top,h="2px"===(l||{}).marginLeft,e="4px"===(l||{width:"4px"}).width,j.style.marginRight="50%",c="4px"===(l||{marginRight:"4px"}).marginRight,k=j.appendChild(d.createElement("div")),k.style.cssText=j.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0",k.style.marginRight=k.style.width="0",j.style.width="1px",g=!parseFloat((a.getComputedStyle(k)||{}).marginRight),j.removeChild(k)),j.style.display="none",f=0===j.getClientRects().length,f&&(j.style.display="",j.innerHTML="<table><tr><td></td><td>t</td></tr></table>",j.childNodes[0].style.borderCollapse="separate",k=j.getElementsByTagName("td"),k[0].style.cssText="margin:0;border:0;padding:0;display:none",f=0===k[0].offsetHeight,f&&(k[0].style.display="",k[1].style.display="none",f=0===k[0].offsetHeight)),m.removeChild(i)}}}();var Ra,Sa,Ta=/^(top|right|bottom|left)$/;a.getComputedStyle?(Ra=function(b){var c=b.ownerDocument.defaultView;return c&&c.opener||(c=a),c.getComputedStyle(b)},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c.getPropertyValue(b)||c[b]:void 0,""!==g&&void 0!==g||n.contains(a.ownerDocument,a)||(g=n.style(a,b)),c&&!l.pixelMarginRight()&&Oa.test(g)&&Na.test(b)&&(d=h.width,e=h.minWidth,f=h.maxWidth,h.minWidth=h.maxWidth=h.width=g,g=c.width,h.width=d,h.minWidth=e,h.maxWidth=f),void 0===g?g:g+""}):Qa.currentStyle&&(Ra=function(a){return a.currentStyle},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c[b]:void 0,null==g&&h&&h[b]&&(g=h[b]),Oa.test(g)&&!Ta.test(b)&&(d=h.left,e=a.runtimeStyle,f=e&&e.left,f&&(e.left=a.currentStyle.left),h.left="fontSize"===b?"1em":g,g=h.pixelLeft+"px",h.left=d,f&&(e.left=f)),void 0===g?g:g+""||"auto"});function Ua(a,b){return{get:function(){return a()?void delete this.get:(this.get=b).apply(this,arguments)}}}var Va=/alpha\([^)]*\)/i,Wa=/opacity\s*=\s*([^)]*)/i,Xa=/^(none|table(?!-c[ea]).+)/,Ya=new RegExp("^("+T+")(.*)$","i"),Za={position:"absolute",visibility:"hidden",display:"block"},$a={letterSpacing:"0",fontWeight:"400"},_a=["Webkit","O","Moz","ms"],ab=d.createElement("div").style;function bb(a){if(a in ab)return a;var b=a.charAt(0).toUpperCase()+a.slice(1),c=_a.length;while(c--)if(a=_a[c]+b,a in ab)return a}function cb(a,b){for(var c,d,e,f=[],g=0,h=a.length;h>g;g++)d=a[g],d.style&&(f[g]=n._data(d,"olddisplay"),c=d.style.display,b?(f[g]||"none"!==c||(d.style.display=""),""===d.style.display&&W(d)&&(f[g]=n._data(d,"olddisplay",Ma(d.nodeName)))):(e=W(d),(c&&"none"!==c||!e)&&n._data(d,"olddisplay",e?c:n.css(d,"display"))));for(g=0;h>g;g++)d=a[g],d.style&&(b&&"none"!==d.style.display&&""!==d.style.display||(d.style.display=b?f[g]||"":"none"));return a}function db(a,b,c){var d=Ya.exec(b);return d?Math.max(0,d[1]-(c||0))+(d[2]||"px"):b}function eb(a,b,c,d,e){for(var f=c===(d?"border":"content")?4:"width"===b?1:0,g=0;4>f;f+=2)"margin"===c&&(g+=n.css(a,c+V[f],!0,e)),d?("content"===c&&(g-=n.css(a,"padding"+V[f],!0,e)),"margin"!==c&&(g-=n.css(a,"border"+V[f]+"Width",!0,e))):(g+=n.css(a,"padding"+V[f],!0,e),"padding"!==c&&(g+=n.css(a,"border"+V[f]+"Width",!0,e)));return g}function fb(a,b,c){var d=!0,e="width"===b?a.offsetWidth:a.offsetHeight,f=Ra(a),g=l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,f);if(0>=e||null==e){if(e=Sa(a,b,f),(0>e||null==e)&&(e=a.style[b]),Oa.test(e))return e;d=g&&(l.boxSizingReliable()||e===a.style[b]),e=parseFloat(e)||0}return e+eb(a,b,c||(g?"border":"content"),d,f)+"px"}n.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=Sa(a,"opacity");return""===c?"1":c}}}},cssNumber:{animationIterationCount:!0,columnCount:!0,fillOpacity:!0,flexGrow:!0,flexShrink:!0,fontWeight:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":l.cssFloat?"cssFloat":"styleFloat"},style:function(a,b,c,d){if(a&&3!==a.nodeType&&8!==a.nodeType&&a.style){var e,f,g,h=n.camelCase(b),i=a.style;if(b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],void 0===c)return g&&"get"in g&&void 0!==(e=g.get(a,!1,d))?e:i[b];if(f=typeof c,"string"===f&&(e=U.exec(c))&&e[1]&&(c=X(a,b,e),f="number"),null!=c&&c===c&&("number"===f&&(c+=e&&e[3]||(n.cssNumber[h]?"":"px")),l.clearCloneStyle||""!==c||0!==b.indexOf("background")||(i[b]="inherit"),!(g&&"set"in g&&void 0===(c=g.set(a,c,d)))))try{i[b]=c}catch(j){}}},css:function(a,b,c,d){var e,f,g,h=n.camelCase(b);return b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],g&&"get"in g&&(f=g.get(a,!0,c)),void 0===f&&(f=Sa(a,b,d)),"normal"===f&&b in $a&&(f=$a[b]),""===c||c?(e=parseFloat(f),c===!0||isFinite(e)?e||0:f):f}}),n.each(["height","width"],function(a,b){n.cssHooks[b]={get:function(a,c,d){return c?Xa.test(n.css(a,"display"))&&0===a.offsetWidth?Pa(a,Za,function(){return fb(a,b,d)}):fb(a,b,d):void 0},set:function(a,c,d){var e=d&&Ra(a);return db(a,c,d?eb(a,b,d,l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,e),e):0)}}}),l.opacity||(n.cssHooks.opacity={get:function(a,b){return Wa.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?.01*parseFloat(RegExp.$1)+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle,e=n.isNumeric(b)?"alpha(opacity="+100*b+")":"",f=d&&d.filter||c.filter||"";c.zoom=1,(b>=1||""===b)&&""===n.trim(f.replace(Va,""))&&c.removeAttribute&&(c.removeAttribute("filter"),""===b||d&&!d.filter)||(c.filter=Va.test(f)?f.replace(Va,e):f+" "+e)}}),n.cssHooks.marginRight=Ua(l.reliableMarginRight,function(a,b){return b?Pa(a,{display:"inline-block"},Sa,[a,"marginRight"]):void 0}),n.cssHooks.marginLeft=Ua(l.reliableMarginLeft,function(a,b){return b?(parseFloat(Sa(a,"marginLeft"))||(n.contains(a.ownerDocument,a)?a.getBoundingClientRect().left-Pa(a,{
marginLeft:0},function(){return a.getBoundingClientRect().left}):0))+"px":void 0}),n.each({margin:"",padding:"",border:"Width"},function(a,b){n.cssHooks[a+b]={expand:function(c){for(var d=0,e={},f="string"==typeof c?c.split(" "):[c];4>d;d++)e[a+V[d]+b]=f[d]||f[d-2]||f[0];return e}},Na.test(a)||(n.cssHooks[a+b].set=db)}),n.fn.extend({css:function(a,b){return Y(this,function(a,b,c){var d,e,f={},g=0;if(n.isArray(b)){for(d=Ra(a),e=b.length;e>g;g++)f[b[g]]=n.css(a,b[g],!1,d);return f}return void 0!==c?n.style(a,b,c):n.css(a,b)},a,b,arguments.length>1)},show:function(){return cb(this,!0)},hide:function(){return cb(this)},toggle:function(a){return"boolean"==typeof a?a?this.show():this.hide():this.each(function(){W(this)?n(this).show():n(this).hide()})}});function gb(a,b,c,d,e){return new gb.prototype.init(a,b,c,d,e)}n.Tween=gb,gb.prototype={constructor:gb,init:function(a,b,c,d,e,f){this.elem=a,this.prop=c,this.easing=e||n.easing._default,this.options=b,this.start=this.now=this.cur(),this.end=d,this.unit=f||(n.cssNumber[c]?"":"px")},cur:function(){var a=gb.propHooks[this.prop];return a&&a.get?a.get(this):gb.propHooks._default.get(this)},run:function(a){var b,c=gb.propHooks[this.prop];return this.options.duration?this.pos=b=n.easing[this.easing](a,this.options.duration*a,0,1,this.options.duration):this.pos=b=a,this.now=(this.end-this.start)*b+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),c&&c.set?c.set(this):gb.propHooks._default.set(this),this}},gb.prototype.init.prototype=gb.prototype,gb.propHooks={_default:{get:function(a){var b;return 1!==a.elem.nodeType||null!=a.elem[a.prop]&&null==a.elem.style[a.prop]?a.elem[a.prop]:(b=n.css(a.elem,a.prop,""),b&&"auto"!==b?b:0)},set:function(a){n.fx.step[a.prop]?n.fx.step[a.prop](a):1!==a.elem.nodeType||null==a.elem.style[n.cssProps[a.prop]]&&!n.cssHooks[a.prop]?a.elem[a.prop]=a.now:n.style(a.elem,a.prop,a.now+a.unit)}}},gb.propHooks.scrollTop=gb.propHooks.scrollLeft={set:function(a){a.elem.nodeType&&a.elem.parentNode&&(a.elem[a.prop]=a.now)}},n.easing={linear:function(a){return a},swing:function(a){return.5-Math.cos(a*Math.PI)/2},_default:"swing"},n.fx=gb.prototype.init,n.fx.step={};var hb,ib,jb=/^(?:toggle|show|hide)$/,kb=/queueHooks$/;function lb(){return a.setTimeout(function(){hb=void 0}),hb=n.now()}function mb(a,b){var c,d={height:a},e=0;for(b=b?1:0;4>e;e+=2-b)c=V[e],d["margin"+c]=d["padding"+c]=a;return b&&(d.opacity=d.width=a),d}function nb(a,b,c){for(var d,e=(qb.tweeners[b]||[]).concat(qb.tweeners["*"]),f=0,g=e.length;g>f;f++)if(d=e[f].call(c,b,a))return d}function ob(a,b,c){var d,e,f,g,h,i,j,k,m=this,o={},p=a.style,q=a.nodeType&&W(a),r=n._data(a,"fxshow");c.queue||(h=n._queueHooks(a,"fx"),null==h.unqueued&&(h.unqueued=0,i=h.empty.fire,h.empty.fire=function(){h.unqueued||i()}),h.unqueued++,m.always(function(){m.always(function(){h.unqueued--,n.queue(a,"fx").length||h.empty.fire()})})),1===a.nodeType&&("height"in b||"width"in b)&&(c.overflow=[p.overflow,p.overflowX,p.overflowY],j=n.css(a,"display"),k="none"===j?n._data(a,"olddisplay")||Ma(a.nodeName):j,"inline"===k&&"none"===n.css(a,"float")&&(l.inlineBlockNeedsLayout&&"inline"!==Ma(a.nodeName)?p.zoom=1:p.display="inline-block")),c.overflow&&(p.overflow="hidden",l.shrinkWrapBlocks()||m.always(function(){p.overflow=c.overflow[0],p.overflowX=c.overflow[1],p.overflowY=c.overflow[2]}));for(d in b)if(e=b[d],jb.exec(e)){if(delete b[d],f=f||"toggle"===e,e===(q?"hide":"show")){if("show"!==e||!r||void 0===r[d])continue;q=!0}o[d]=r&&r[d]||n.style(a,d)}else j=void 0;if(n.isEmptyObject(o))"inline"===("none"===j?Ma(a.nodeName):j)&&(p.display=j);else{r?"hidden"in r&&(q=r.hidden):r=n._data(a,"fxshow",{}),f&&(r.hidden=!q),q?n(a).show():m.done(function(){n(a).hide()}),m.done(function(){var b;n._removeData(a,"fxshow");for(b in o)n.style(a,b,o[b])});for(d in o)g=nb(q?r[d]:0,d,m),d in r||(r[d]=g.start,q&&(g.end=g.start,g.start="width"===d||"height"===d?1:0))}}function pb(a,b){var c,d,e,f,g;for(c in a)if(d=n.camelCase(c),e=b[d],f=a[c],n.isArray(f)&&(e=f[1],f=a[c]=f[0]),c!==d&&(a[d]=f,delete a[c]),g=n.cssHooks[d],g&&"expand"in g){f=g.expand(f),delete a[d];for(c in f)c in a||(a[c]=f[c],b[c]=e)}else b[d]=e}function qb(a,b,c){var d,e,f=0,g=qb.prefilters.length,h=n.Deferred().always(function(){delete i.elem}),i=function(){if(e)return!1;for(var b=hb||lb(),c=Math.max(0,j.startTime+j.duration-b),d=c/j.duration||0,f=1-d,g=0,i=j.tweens.length;i>g;g++)j.tweens[g].run(f);return h.notifyWith(a,[j,f,c]),1>f&&i?c:(h.resolveWith(a,[j]),!1)},j=h.promise({elem:a,props:n.extend({},b),opts:n.extend(!0,{specialEasing:{},easing:n.easing._default},c),originalProperties:b,originalOptions:c,startTime:hb||lb(),duration:c.duration,tweens:[],createTween:function(b,c){var d=n.Tween(a,j.opts,b,c,j.opts.specialEasing[b]||j.opts.easing);return j.tweens.push(d),d},stop:function(b){var c=0,d=b?j.tweens.length:0;if(e)return this;for(e=!0;d>c;c++)j.tweens[c].run(1);return b?(h.notifyWith(a,[j,1,0]),h.resolveWith(a,[j,b])):h.rejectWith(a,[j,b]),this}}),k=j.props;for(pb(k,j.opts.specialEasing);g>f;f++)if(d=qb.prefilters[f].call(j,a,k,j.opts))return n.isFunction(d.stop)&&(n._queueHooks(j.elem,j.opts.queue).stop=n.proxy(d.stop,d)),d;return n.map(k,nb,j),n.isFunction(j.opts.start)&&j.opts.start.call(a,j),n.fx.timer(n.extend(i,{elem:a,anim:j,queue:j.opts.queue})),j.progress(j.opts.progress).done(j.opts.done,j.opts.complete).fail(j.opts.fail).always(j.opts.always)}n.Animation=n.extend(qb,{tweeners:{"*":[function(a,b){var c=this.createTween(a,b);return X(c.elem,a,U.exec(b),c),c}]},tweener:function(a,b){n.isFunction(a)?(b=a,a=["*"]):a=a.match(G);for(var c,d=0,e=a.length;e>d;d++)c=a[d],qb.tweeners[c]=qb.tweeners[c]||[],qb.tweeners[c].unshift(b)},prefilters:[ob],prefilter:function(a,b){b?qb.prefilters.unshift(a):qb.prefilters.push(a)}}),n.speed=function(a,b,c){var d=a&&"object"==typeof a?n.extend({},a):{complete:c||!c&&b||n.isFunction(a)&&a,duration:a,easing:c&&b||b&&!n.isFunction(b)&&b};return d.duration=n.fx.off?0:"number"==typeof d.duration?d.duration:d.duration in n.fx.speeds?n.fx.speeds[d.duration]:n.fx.speeds._default,null!=d.queue&&d.queue!==!0||(d.queue="fx"),d.old=d.complete,d.complete=function(){n.isFunction(d.old)&&d.old.call(this),d.queue&&n.dequeue(this,d.queue)},d},n.fn.extend({fadeTo:function(a,b,c,d){return this.filter(W).css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){var e=n.isEmptyObject(a),f=n.speed(b,c,d),g=function(){var b=qb(this,n.extend({},a),f);(e||n._data(this,"finish"))&&b.stop(!0)};return g.finish=g,e||f.queue===!1?this.each(g):this.queue(f.queue,g)},stop:function(a,b,c){var d=function(a){var b=a.stop;delete a.stop,b(c)};return"string"!=typeof a&&(c=b,b=a,a=void 0),b&&a!==!1&&this.queue(a||"fx",[]),this.each(function(){var b=!0,e=null!=a&&a+"queueHooks",f=n.timers,g=n._data(this);if(e)g[e]&&g[e].stop&&d(g[e]);else for(e in g)g[e]&&g[e].stop&&kb.test(e)&&d(g[e]);for(e=f.length;e--;)f[e].elem!==this||null!=a&&f[e].queue!==a||(f[e].anim.stop(c),b=!1,f.splice(e,1));!b&&c||n.dequeue(this,a)})},finish:function(a){return a!==!1&&(a=a||"fx"),this.each(function(){var b,c=n._data(this),d=c[a+"queue"],e=c[a+"queueHooks"],f=n.timers,g=d?d.length:0;for(c.finish=!0,n.queue(this,a,[]),e&&e.stop&&e.stop.call(this,!0),b=f.length;b--;)f[b].elem===this&&f[b].queue===a&&(f[b].anim.stop(!0),f.splice(b,1));for(b=0;g>b;b++)d[b]&&d[b].finish&&d[b].finish.call(this);delete c.finish})}}),n.each(["toggle","show","hide"],function(a,b){var c=n.fn[b];n.fn[b]=function(a,d,e){return null==a||"boolean"==typeof a?c.apply(this,arguments):this.animate(mb(b,!0),a,d,e)}}),n.each({slideDown:mb("show"),slideUp:mb("hide"),slideToggle:mb("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){n.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),n.timers=[],n.fx.tick=function(){var a,b=n.timers,c=0;for(hb=n.now();c<b.length;c++)a=b[c],a()||b[c]!==a||b.splice(c--,1);b.length||n.fx.stop(),hb=void 0},n.fx.timer=function(a){n.timers.push(a),a()?n.fx.start():n.timers.pop()},n.fx.interval=13,n.fx.start=function(){ib||(ib=a.setInterval(n.fx.tick,n.fx.interval))},n.fx.stop=function(){a.clearInterval(ib),ib=null},n.fx.speeds={slow:600,fast:200,_default:400},n.fn.delay=function(b,c){return b=n.fx?n.fx.speeds[b]||b:b,c=c||"fx",this.queue(c,function(c,d){var e=a.setTimeout(c,b);d.stop=function(){a.clearTimeout(e)}})},function(){var a,b=d.createElement("input"),c=d.createElement("div"),e=d.createElement("select"),f=e.appendChild(d.createElement("option"));c=d.createElement("div"),c.setAttribute("className","t"),c.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=c.getElementsByTagName("a")[0],b.setAttribute("type","checkbox"),c.appendChild(b),a=c.getElementsByTagName("a")[0],a.style.cssText="top:1px",l.getSetAttribute="t"!==c.className,l.style=/top/.test(a.getAttribute("style")),l.hrefNormalized="/a"===a.getAttribute("href"),l.checkOn=!!b.value,l.optSelected=f.selected,l.enctype=!!d.createElement("form").enctype,e.disabled=!0,l.optDisabled=!f.disabled,b=d.createElement("input"),b.setAttribute("value",""),l.input=""===b.getAttribute("value"),b.value="t",b.setAttribute("type","radio"),l.radioValue="t"===b.value}();var rb=/\r/g,sb=/[\x20\t\r\n\f]+/g;n.fn.extend({val:function(a){var b,c,d,e=this[0];{if(arguments.length)return d=n.isFunction(a),this.each(function(c){var e;1===this.nodeType&&(e=d?a.call(this,c,n(this).val()):a,null==e?e="":"number"==typeof e?e+="":n.isArray(e)&&(e=n.map(e,function(a){return null==a?"":a+""})),b=n.valHooks[this.type]||n.valHooks[this.nodeName.toLowerCase()],b&&"set"in b&&void 0!==b.set(this,e,"value")||(this.value=e))});if(e)return b=n.valHooks[e.type]||n.valHooks[e.nodeName.toLowerCase()],b&&"get"in b&&void 0!==(c=b.get(e,"value"))?c:(c=e.value,"string"==typeof c?c.replace(rb,""):null==c?"":c)}}}),n.extend({valHooks:{option:{get:function(a){var b=n.find.attr(a,"value");return null!=b?b:n.trim(n.text(a)).replace(sb," ")}},select:{get:function(a){for(var b,c,d=a.options,e=a.selectedIndex,f="select-one"===a.type||0>e,g=f?null:[],h=f?e+1:d.length,i=0>e?h:f?e:0;h>i;i++)if(c=d[i],(c.selected||i===e)&&(l.optDisabled?!c.disabled:null===c.getAttribute("disabled"))&&(!c.parentNode.disabled||!n.nodeName(c.parentNode,"optgroup"))){if(b=n(c).val(),f)return b;g.push(b)}return g},set:function(a,b){var c,d,e=a.options,f=n.makeArray(b),g=e.length;while(g--)if(d=e[g],n.inArray(n.valHooks.option.get(d),f)>-1)try{d.selected=c=!0}catch(h){d.scrollHeight}else d.selected=!1;return c||(a.selectedIndex=-1),e}}}}),n.each(["radio","checkbox"],function(){n.valHooks[this]={set:function(a,b){return n.isArray(b)?a.checked=n.inArray(n(a).val(),b)>-1:void 0}},l.checkOn||(n.valHooks[this].get=function(a){return null===a.getAttribute("value")?"on":a.value})});var tb,ub,vb=n.expr.attrHandle,wb=/^(?:checked|selected)$/i,xb=l.getSetAttribute,yb=l.input;n.fn.extend({attr:function(a,b){return Y(this,n.attr,a,b,arguments.length>1)},removeAttr:function(a){return this.each(function(){n.removeAttr(this,a)})}}),n.extend({attr:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return"undefined"==typeof a.getAttribute?n.prop(a,b,c):(1===f&&n.isXMLDoc(a)||(b=b.toLowerCase(),e=n.attrHooks[b]||(n.expr.match.bool.test(b)?ub:tb)),void 0!==c?null===c?void n.removeAttr(a,b):e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:(a.setAttribute(b,c+""),c):e&&"get"in e&&null!==(d=e.get(a,b))?d:(d=n.find.attr(a,b),null==d?void 0:d))},attrHooks:{type:{set:function(a,b){if(!l.radioValue&&"radio"===b&&n.nodeName(a,"input")){var c=a.value;return a.setAttribute("type",b),c&&(a.value=c),b}}}},removeAttr:function(a,b){var c,d,e=0,f=b&&b.match(G);if(f&&1===a.nodeType)while(c=f[e++])d=n.propFix[c]||c,n.expr.match.bool.test(c)?yb&&xb||!wb.test(c)?a[d]=!1:a[n.camelCase("default-"+c)]=a[d]=!1:n.attr(a,c,""),a.removeAttribute(xb?c:d)}}),ub={set:function(a,b,c){return b===!1?n.removeAttr(a,c):yb&&xb||!wb.test(c)?a.setAttribute(!xb&&n.propFix[c]||c,c):a[n.camelCase("default-"+c)]=a[c]=!0,c}},n.each(n.expr.match.bool.source.match(/\w+/g),function(a,b){var c=vb[b]||n.find.attr;yb&&xb||!wb.test(b)?vb[b]=function(a,b,d){var e,f;return d||(f=vb[b],vb[b]=e,e=null!=c(a,b,d)?b.toLowerCase():null,vb[b]=f),e}:vb[b]=function(a,b,c){return c?void 0:a[n.camelCase("default-"+b)]?b.toLowerCase():null}}),yb&&xb||(n.attrHooks.value={set:function(a,b,c){return n.nodeName(a,"input")?void(a.defaultValue=b):tb&&tb.set(a,b,c)}}),xb||(tb={set:function(a,b,c){var d=a.getAttributeNode(c);return d||a.setAttributeNode(d=a.ownerDocument.createAttribute(c)),d.value=b+="","value"===c||b===a.getAttribute(c)?b:void 0}},vb.id=vb.name=vb.coords=function(a,b,c){var d;return c?void 0:(d=a.getAttributeNode(b))&&""!==d.value?d.value:null},n.valHooks.button={get:function(a,b){var c=a.getAttributeNode(b);return c&&c.specified?c.value:void 0},set:tb.set},n.attrHooks.contenteditable={set:function(a,b,c){tb.set(a,""===b?!1:b,c)}},n.each(["width","height"],function(a,b){n.attrHooks[b]={set:function(a,c){return""===c?(a.setAttribute(b,"auto"),c):void 0}}})),l.style||(n.attrHooks.style={get:function(a){return a.style.cssText||void 0},set:function(a,b){return a.style.cssText=b+""}});var zb=/^(?:input|select|textarea|button|object)$/i,Ab=/^(?:a|area)$/i;n.fn.extend({prop:function(a,b){return Y(this,n.prop,a,b,arguments.length>1)},removeProp:function(a){return a=n.propFix[a]||a,this.each(function(){try{this[a]=void 0,delete this[a]}catch(b){}})}}),n.extend({prop:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return 1===f&&n.isXMLDoc(a)||(b=n.propFix[b]||b,e=n.propHooks[b]),void 0!==c?e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:a[b]=c:e&&"get"in e&&null!==(d=e.get(a,b))?d:a[b]},propHooks:{tabIndex:{get:function(a){var b=n.find.attr(a,"tabindex");return b?parseInt(b,10):zb.test(a.nodeName)||Ab.test(a.nodeName)&&a.href?0:-1}}},propFix:{"for":"htmlFor","class":"className"}}),l.hrefNormalized||n.each(["href","src"],function(a,b){n.propHooks[b]={get:function(a){return a.getAttribute(b,4)}}}),l.optSelected||(n.propHooks.selected={get:function(a){var b=a.parentNode;return b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex),null},set:function(a){var b=a.parentNode;b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex)}}),n.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){n.propFix[this.toLowerCase()]=this}),l.enctype||(n.propFix.enctype="encoding");var Bb=/[\t\r\n\f]/g;function Cb(a){return n.attr(a,"class")||""}n.fn.extend({addClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).addClass(a.call(this,b,Cb(this)))});if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])d.indexOf(" "+f+" ")<0&&(d+=f+" ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},removeClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).removeClass(a.call(this,b,Cb(this)))});if(!arguments.length)return this.attr("class","");if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])while(d.indexOf(" "+f+" ")>-1)d=d.replace(" "+f+" "," ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},toggleClass:function(a,b){var c=typeof a;return"boolean"==typeof b&&"string"===c?b?this.addClass(a):this.removeClass(a):n.isFunction(a)?this.each(function(c){n(this).toggleClass(a.call(this,c,Cb(this),b),b)}):this.each(function(){var b,d,e,f;if("string"===c){d=0,e=n(this),f=a.match(G)||[];while(b=f[d++])e.hasClass(b)?e.removeClass(b):e.addClass(b)}else void 0!==a&&"boolean"!==c||(b=Cb(this),b&&n._data(this,"__className__",b),n.attr(this,"class",b||a===!1?"":n._data(this,"__className__")||""))})},hasClass:function(a){var b,c,d=0;b=" "+a+" ";while(c=this[d++])if(1===c.nodeType&&(" "+Cb(c)+" ").replace(Bb," ").indexOf(b)>-1)return!0;return!1}}),n.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(a,b){n.fn[b]=function(a,c){return arguments.length>0?this.on(b,null,a,c):this.trigger(b)}}),n.fn.extend({hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)}});var Db=a.location,Eb=n.now(),Fb=/\?/,Gb=/(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;n.parseJSON=function(b){if(a.JSON&&a.JSON.parse)return a.JSON.parse(b+"");var c,d=null,e=n.trim(b+"");return e&&!n.trim(e.replace(Gb,function(a,b,e,f){return c&&b&&(d=0),0===d?a:(c=e||b,d+=!f-!e,"")}))?Function("return "+e)():n.error("Invalid JSON: "+b)},n.parseXML=function(b){var c,d;if(!b||"string"!=typeof b)return null;try{a.DOMParser?(d=new a.DOMParser,c=d.parseFromString(b,"text/xml")):(c=new a.ActiveXObject("Microsoft.XMLDOM"),c.async="false",c.loadXML(b))}catch(e){c=void 0}return c&&c.documentElement&&!c.getElementsByTagName("parsererror").length||n.error("Invalid XML: "+b),c};var Hb=/#.*$/,Ib=/([?&])_=[^&]*/,Jb=/^(.*?):[ \t]*([^\r\n]*)\r?$/gm,Kb=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Lb=/^(?:GET|HEAD)$/,Mb=/^\/\//,Nb=/^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,Ob={},Pb={},Qb="*/".concat("*"),Rb=Db.href,Sb=Nb.exec(Rb.toLowerCase())||[];function Tb(a){return function(b,c){"string"!=typeof b&&(c=b,b="*");var d,e=0,f=b.toLowerCase().match(G)||[];if(n.isFunction(c))while(d=f[e++])"+"===d.charAt(0)?(d=d.slice(1)||"*",(a[d]=a[d]||[]).unshift(c)):(a[d]=a[d]||[]).push(c)}}function Ub(a,b,c,d){var e={},f=a===Pb;function g(h){var i;return e[h]=!0,n.each(a[h]||[],function(a,h){var j=h(b,c,d);return"string"!=typeof j||f||e[j]?f?!(i=j):void 0:(b.dataTypes.unshift(j),g(j),!1)}),i}return g(b.dataTypes[0])||!e["*"]&&g("*")}function Vb(a,b){var c,d,e=n.ajaxSettings.flatOptions||{};for(d in b)void 0!==b[d]&&((e[d]?a:c||(c={}))[d]=b[d]);return c&&n.extend(!0,a,c),a}function Wb(a,b,c){var d,e,f,g,h=a.contents,i=a.dataTypes;while("*"===i[0])i.shift(),void 0===e&&(e=a.mimeType||b.getResponseHeader("Content-Type"));if(e)for(g in h)if(h[g]&&h[g].test(e)){i.unshift(g);break}if(i[0]in c)f=i[0];else{for(g in c){if(!i[0]||a.converters[g+" "+i[0]]){f=g;break}d||(d=g)}f=f||d}return f?(f!==i[0]&&i.unshift(f),c[f]):void 0}function Xb(a,b,c,d){var e,f,g,h,i,j={},k=a.dataTypes.slice();if(k[1])for(g in a.converters)j[g.toLowerCase()]=a.converters[g];f=k.shift();while(f)if(a.responseFields[f]&&(c[a.responseFields[f]]=b),!i&&d&&a.dataFilter&&(b=a.dataFilter(b,a.dataType)),i=f,f=k.shift())if("*"===f)f=i;else if("*"!==i&&i!==f){if(g=j[i+" "+f]||j["* "+f],!g)for(e in j)if(h=e.split(" "),h[1]===f&&(g=j[i+" "+h[0]]||j["* "+h[0]])){g===!0?g=j[e]:j[e]!==!0&&(f=h[0],k.unshift(h[1]));break}if(g!==!0)if(g&&a["throws"])b=g(b);else try{b=g(b)}catch(l){return{state:"parsererror",error:g?l:"No conversion from "+i+" to "+f}}}return{state:"success",data:b}}n.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Rb,type:"GET",isLocal:Kb.test(Sb[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":Qb,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":n.parseJSON,"text xml":n.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(a,b){return b?Vb(Vb(a,n.ajaxSettings),b):Vb(n.ajaxSettings,a)},ajaxPrefilter:Tb(Ob),ajaxTransport:Tb(Pb),ajax:function(b,c){"object"==typeof b&&(c=b,b=void 0),c=c||{};var d,e,f,g,h,i,j,k,l=n.ajaxSetup({},c),m=l.context||l,o=l.context&&(m.nodeType||m.jquery)?n(m):n.event,p=n.Deferred(),q=n.Callbacks("once memory"),r=l.statusCode||{},s={},t={},u=0,v="canceled",w={readyState:0,getResponseHeader:function(a){var b;if(2===u){if(!k){k={};while(b=Jb.exec(g))k[b[1].toLowerCase()]=b[2]}b=k[a.toLowerCase()]}return null==b?null:b},getAllResponseHeaders:function(){return 2===u?g:null},setRequestHeader:function(a,b){var c=a.toLowerCase();return u||(a=t[c]=t[c]||a,s[a]=b),this},overrideMimeType:function(a){return u||(l.mimeType=a),this},statusCode:function(a){var b;if(a)if(2>u)for(b in a)r[b]=[r[b],a[b]];else w.always(a[w.status]);return this},abort:function(a){var b=a||v;return j&&j.abort(b),y(0,b),this}};if(p.promise(w).complete=q.add,w.success=w.done,w.error=w.fail,l.url=((b||l.url||Rb)+"").replace(Hb,"").replace(Mb,Sb[1]+"//"),l.type=c.method||c.type||l.method||l.type,l.dataTypes=n.trim(l.dataType||"*").toLowerCase().match(G)||[""],null==l.crossDomain&&(d=Nb.exec(l.url.toLowerCase()),l.crossDomain=!(!d||d[1]===Sb[1]&&d[2]===Sb[2]&&(d[3]||("http:"===d[1]?"80":"443"))===(Sb[3]||("http:"===Sb[1]?"80":"443")))),l.data&&l.processData&&"string"!=typeof l.data&&(l.data=n.param(l.data,l.traditional)),Ub(Ob,l,c,w),2===u)return w;i=n.event&&l.global,i&&0===n.active++&&n.event.trigger("ajaxStart"),l.type=l.type.toUpperCase(),l.hasContent=!Lb.test(l.type),f=l.url,l.hasContent||(l.data&&(f=l.url+=(Fb.test(f)?"&":"?")+l.data,delete l.data),l.cache===!1&&(l.url=Ib.test(f)?f.replace(Ib,"$1_="+Eb++):f+(Fb.test(f)?"&":"?")+"_="+Eb++)),l.ifModified&&(n.lastModified[f]&&w.setRequestHeader("If-Modified-Since",n.lastModified[f]),n.etag[f]&&w.setRequestHeader("If-None-Match",n.etag[f])),(l.data&&l.hasContent&&l.contentType!==!1||c.contentType)&&w.setRequestHeader("Content-Type",l.contentType),w.setRequestHeader("Accept",l.dataTypes[0]&&l.accepts[l.dataTypes[0]]?l.accepts[l.dataTypes[0]]+("*"!==l.dataTypes[0]?", "+Qb+"; q=0.01":""):l.accepts["*"]);for(e in l.headers)w.setRequestHeader(e,l.headers[e]);if(l.beforeSend&&(l.beforeSend.call(m,w,l)===!1||2===u))return w.abort();v="abort";for(e in{success:1,error:1,complete:1})w[e](l[e]);if(j=Ub(Pb,l,c,w)){if(w.readyState=1,i&&o.trigger("ajaxSend",[w,l]),2===u)return w;l.async&&l.timeout>0&&(h=a.setTimeout(function(){w.abort("timeout")},l.timeout));try{u=1,j.send(s,y)}catch(x){if(!(2>u))throw x;y(-1,x)}}else y(-1,"No Transport");function y(b,c,d,e){var k,s,t,v,x,y=c;2!==u&&(u=2,h&&a.clearTimeout(h),j=void 0,g=e||"",w.readyState=b>0?4:0,k=b>=200&&300>b||304===b,d&&(v=Wb(l,w,d)),v=Xb(l,v,w,k),k?(l.ifModified&&(x=w.getResponseHeader("Last-Modified"),x&&(n.lastModified[f]=x),x=w.getResponseHeader("etag"),x&&(n.etag[f]=x)),204===b||"HEAD"===l.type?y="nocontent":304===b?y="notmodified":(y=v.state,s=v.data,t=v.error,k=!t)):(t=y,!b&&y||(y="error",0>b&&(b=0))),w.status=b,w.statusText=(c||y)+"",k?p.resolveWith(m,[s,y,w]):p.rejectWith(m,[w,y,t]),w.statusCode(r),r=void 0,i&&o.trigger(k?"ajaxSuccess":"ajaxError",[w,l,k?s:t]),q.fireWith(m,[w,y]),i&&(o.trigger("ajaxComplete",[w,l]),--n.active||n.event.trigger("ajaxStop")))}return w},getJSON:function(a,b,c){return n.get(a,b,c,"json")},getScript:function(a,b){return n.get(a,void 0,b,"script")}}),n.each(["get","post"],function(a,b){n[b]=function(a,c,d,e){return n.isFunction(c)&&(e=e||d,d=c,c=void 0),n.ajax(n.extend({url:a,type:b,dataType:e,data:c,success:d},n.isPlainObject(a)&&a))}}),n._evalUrl=function(a){return n.ajax({url:a,type:"GET",dataType:"script",cache:!0,async:!1,global:!1,"throws":!0})},n.fn.extend({wrapAll:function(a){if(n.isFunction(a))return this.each(function(b){n(this).wrapAll(a.call(this,b))});if(this[0]){var b=n(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&1===a.firstChild.nodeType)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){return n.isFunction(a)?this.each(function(b){n(this).wrapInner(a.call(this,b))}):this.each(function(){var b=n(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){var b=n.isFunction(a);return this.each(function(c){n(this).wrapAll(b?a.call(this,c):a)})},unwrap:function(){return this.parent().each(function(){n.nodeName(this,"body")||n(this).replaceWith(this.childNodes)}).end()}});function Yb(a){return a.style&&a.style.display||n.css(a,"display")}function Zb(a){if(!n.contains(a.ownerDocument||d,a))return!0;while(a&&1===a.nodeType){if("none"===Yb(a)||"hidden"===a.type)return!0;a=a.parentNode}return!1}n.expr.filters.hidden=function(a){return l.reliableHiddenOffsets()?a.offsetWidth<=0&&a.offsetHeight<=0&&!a.getClientRects().length:Zb(a)},n.expr.filters.visible=function(a){return!n.expr.filters.hidden(a)};var $b=/%20/g,_b=/\[\]$/,ac=/\r?\n/g,bc=/^(?:submit|button|image|reset|file)$/i,cc=/^(?:input|select|textarea|keygen)/i;function dc(a,b,c,d){var e;if(n.isArray(b))n.each(b,function(b,e){c||_b.test(a)?d(a,e):dc(a+"["+("object"==typeof e&&null!=e?b:"")+"]",e,c,d)});else if(c||"object"!==n.type(b))d(a,b);else for(e in b)dc(a+"["+e+"]",b[e],c,d)}n.param=function(a,b){var c,d=[],e=function(a,b){b=n.isFunction(b)?b():null==b?"":b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};if(void 0===b&&(b=n.ajaxSettings&&n.ajaxSettings.traditional),n.isArray(a)||a.jquery&&!n.isPlainObject(a))n.each(a,function(){e(this.name,this.value)});else for(c in a)dc(c,a[c],b,e);return d.join("&").replace($b,"+")},n.fn.extend({serialize:function(){return n.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var a=n.prop(this,"elements");return a?n.makeArray(a):this}).filter(function(){var a=this.type;return this.name&&!n(this).is(":disabled")&&cc.test(this.nodeName)&&!bc.test(a)&&(this.checked||!Z.test(a))}).map(function(a,b){var c=n(this).val();return null==c?null:n.isArray(c)?n.map(c,function(a){return{name:b.name,value:a.replace(ac,"\r\n")}}):{name:b.name,value:c.replace(ac,"\r\n")}}).get()}}),n.ajaxSettings.xhr=void 0!==a.ActiveXObject?function(){return this.isLocal?ic():d.documentMode>8?hc():/^(get|post|head|put|delete|options)$/i.test(this.type)&&hc()||ic()}:hc;var ec=0,fc={},gc=n.ajaxSettings.xhr();a.attachEvent&&a.attachEvent("onunload",function(){for(var a in fc)fc[a](void 0,!0)}),l.cors=!!gc&&"withCredentials"in gc,gc=l.ajax=!!gc,gc&&n.ajaxTransport(function(b){if(!b.crossDomain||l.cors){var c;return{send:function(d,e){var f,g=b.xhr(),h=++ec;if(g.open(b.type,b.url,b.async,b.username,b.password),b.xhrFields)for(f in b.xhrFields)g[f]=b.xhrFields[f];b.mimeType&&g.overrideMimeType&&g.overrideMimeType(b.mimeType),b.crossDomain||d["X-Requested-With"]||(d["X-Requested-With"]="XMLHttpRequest");for(f in d)void 0!==d[f]&&g.setRequestHeader(f,d[f]+"");g.send(b.hasContent&&b.data||null),c=function(a,d){var f,i,j;if(c&&(d||4===g.readyState))if(delete fc[h],c=void 0,g.onreadystatechange=n.noop,d)4!==g.readyState&&g.abort();else{j={},f=g.status,"string"==typeof g.responseText&&(j.text=g.responseText);try{i=g.statusText}catch(k){i=""}f||!b.isLocal||b.crossDomain?1223===f&&(f=204):f=j.text?200:404}j&&e(f,i,j,g.getAllResponseHeaders())},b.async?4===g.readyState?a.setTimeout(c):g.onreadystatechange=fc[h]=c:c()},abort:function(){c&&c(void 0,!0)}}}});function hc(){try{return new a.XMLHttpRequest}catch(b){}}function ic(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}n.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function(a){return n.globalEval(a),a}}}),n.ajaxPrefilter("script",function(a){void 0===a.cache&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),n.ajaxTransport("script",function(a){if(a.crossDomain){var b,c=d.head||n("head")[0]||d.documentElement;return{send:function(e,f){b=d.createElement("script"),b.async=!0,a.scriptCharset&&(b.charset=a.scriptCharset),b.src=a.url,b.onload=b.onreadystatechange=function(a,c){(c||!b.readyState||/loaded|complete/.test(b.readyState))&&(b.onload=b.onreadystatechange=null,b.parentNode&&b.parentNode.removeChild(b),b=null,c||f(200,"success"))},c.insertBefore(b,c.firstChild)},abort:function(){b&&b.onload(void 0,!0)}}}});var jc=[],kc=/(=)\?(?=&|$)|\?\?/;n.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var a=jc.pop()||n.expando+"_"+Eb++;return this[a]=!0,a}}),n.ajaxPrefilter("json jsonp",function(b,c,d){var e,f,g,h=b.jsonp!==!1&&(kc.test(b.url)?"url":"string"==typeof b.data&&0===(b.contentType||"").indexOf("application/x-www-form-urlencoded")&&kc.test(b.data)&&"data");return h||"jsonp"===b.dataTypes[0]?(e=b.jsonpCallback=n.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,h?b[h]=b[h].replace(kc,"$1"+e):b.jsonp!==!1&&(b.url+=(Fb.test(b.url)?"&":"?")+b.jsonp+"="+e),b.converters["script json"]=function(){return g||n.error(e+" was not called"),g[0]},b.dataTypes[0]="json",f=a[e],a[e]=function(){g=arguments},d.always(function(){void 0===f?n(a).removeProp(e):a[e]=f,b[e]&&(b.jsonpCallback=c.jsonpCallback,jc.push(e)),g&&n.isFunction(f)&&f(g[0]),g=f=void 0}),"script"):void 0}),n.parseHTML=function(a,b,c){if(!a||"string"!=typeof a)return null;"boolean"==typeof b&&(c=b,b=!1),b=b||d;var e=x.exec(a),f=!c&&[];return e?[b.createElement(e[1])]:(e=ja([a],b,f),f&&f.length&&n(f).remove(),n.merge([],e.childNodes))};var lc=n.fn.load;n.fn.load=function(a,b,c){if("string"!=typeof a&&lc)return lc.apply(this,arguments);var d,e,f,g=this,h=a.indexOf(" ");return h>-1&&(d=n.trim(a.slice(h,a.length)),a=a.slice(0,h)),n.isFunction(b)?(c=b,b=void 0):b&&"object"==typeof b&&(e="POST"),g.length>0&&n.ajax({url:a,type:e||"GET",dataType:"html",data:b}).done(function(a){f=arguments,g.html(d?n("<div>").append(n.parseHTML(a)).find(d):a)}).always(c&&function(a,b){g.each(function(){c.apply(this,f||[a.responseText,b,a])})}),this},n.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(a,b){n.fn[b]=function(a){return this.on(b,a)}}),n.expr.filters.animated=function(a){return n.grep(n.timers,function(b){return a===b.elem}).length};function mc(a){return n.isWindow(a)?a:9===a.nodeType?a.defaultView||a.parentWindow:!1}n.offset={setOffset:function(a,b,c){var d,e,f,g,h,i,j,k=n.css(a,"position"),l=n(a),m={};"static"===k&&(a.style.position="relative"),h=l.offset(),f=n.css(a,"top"),i=n.css(a,"left"),j=("absolute"===k||"fixed"===k)&&n.inArray("auto",[f,i])>-1,j?(d=l.position(),g=d.top,e=d.left):(g=parseFloat(f)||0,e=parseFloat(i)||0),n.isFunction(b)&&(b=b.call(a,c,n.extend({},h))),null!=b.top&&(m.top=b.top-h.top+g),null!=b.left&&(m.left=b.left-h.left+e),"using"in b?b.using.call(a,m):l.css(m)}},n.fn.extend({offset:function(a){if(arguments.length)return void 0===a?this:this.each(function(b){n.offset.setOffset(this,a,b)});var b,c,d={top:0,left:0},e=this[0],f=e&&e.ownerDocument;if(f)return b=f.documentElement,n.contains(b,e)?("undefined"!=typeof e.getBoundingClientRect&&(d=e.getBoundingClientRect()),c=mc(f),{top:d.top+(c.pageYOffset||b.scrollTop)-(b.clientTop||0),left:d.left+(c.pageXOffset||b.scrollLeft)-(b.clientLeft||0)}):d},position:function(){if(this[0]){var a,b,c={top:0,left:0},d=this[0];return"fixed"===n.css(d,"position")?b=d.getBoundingClientRect():(a=this.offsetParent(),b=this.offset(),n.nodeName(a[0],"html")||(c=a.offset()),c.top+=n.css(a[0],"borderTopWidth",!0),c.left+=n.css(a[0],"borderLeftWidth",!0)),{top:b.top-c.top-n.css(d,"marginTop",!0),left:b.left-c.left-n.css(d,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var a=this.offsetParent;while(a&&!n.nodeName(a,"html")&&"static"===n.css(a,"position"))a=a.offsetParent;return a||Qa})}}),n.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(a,b){var c=/Y/.test(b);n.fn[a]=function(d){return Y(this,function(a,d,e){var f=mc(a);return void 0===e?f?b in f?f[b]:f.document.documentElement[d]:a[d]:void(f?f.scrollTo(c?n(f).scrollLeft():e,c?e:n(f).scrollTop()):a[d]=e)},a,d,arguments.length,null)}}),n.each(["top","left"],function(a,b){n.cssHooks[b]=Ua(l.pixelPosition,function(a,c){return c?(c=Sa(a,b),Oa.test(c)?n(a).position()[b]+"px":c):void 0})}),n.each({Height:"height",Width:"width"},function(a,b){n.each({
padding:"inner"+a,content:b,"":"outer"+a},function(c,d){n.fn[d]=function(d,e){var f=arguments.length&&(c||"boolean"!=typeof d),g=c||(d===!0||e===!0?"margin":"border");return Y(this,function(b,c,d){var e;return n.isWindow(b)?b.document.documentElement["client"+a]:9===b.nodeType?(e=b.documentElement,Math.max(b.body["scroll"+a],e["scroll"+a],b.body["offset"+a],e["offset"+a],e["client"+a])):void 0===d?n.css(b,c,g):n.style(b,c,d,g)},b,f?d:void 0,f,null)}})}),n.fn.extend({bind:function(a,b,c){return this.on(a,null,b,c)},unbind:function(a,b){return this.off(a,null,b)},delegate:function(a,b,c,d){return this.on(b,a,c,d)},undelegate:function(a,b,c){return 1===arguments.length?this.off(a,"**"):this.off(b,a||"**",c)}}),n.fn.size=function(){return this.length},n.fn.andSelf=n.fn.addBack,"function"==typeof define&&define.amd&&define("jquery",[],function(){return n});var nc=a.jQuery,oc=a.$;return n.noConflict=function(b){return a.$===n&&(a.$=oc),b&&a.jQuery===n&&(a.jQuery=nc),n},b||(a.jQuery=a.$=n),n});

/**
 * Lightbox v2.9.0
 * by Lokesh Dhakar
 *
 * More info:
 * http://lokeshdhakar.com/projects/lightbox2/
 *
 * Copyright 2007, 2015 Lokesh Dhakar
 * Released under the MIT license
 * https://github.com/lokesh/lightbox2/blob/master/LICENSE
 */
if ($("#lightbox").length == 0) {
    !function(a,b){"function"==typeof define&&define.amd?define(["jquery"],b):"object"==typeof exports?module.exports=b(require("jquery")):a.lightbox=b(a.jQuery)}(this,function(a){function b(b){this.album=[],this.currentImageIndex=void 0,this.init(),this.options=a.extend({},this.constructor.defaults),this.option(b)}return b.defaults={albumLabel:"Image %1 of %2",alwaysShowNavOnTouchDevices:!1,fadeDuration:600,fitImagesInViewport:!0,imageFadeDuration:600,positionFromTop:50,resizeDuration:700,showImageNumberLabel:!0,wrapAround:!1,disableScrolling:!1,sanitizeTitle:!1},b.prototype.option=function(b){a.extend(this.options,b)},b.prototype.imageCountLabel=function(a,b){return this.options.albumLabel.replace(/%1/g,a).replace(/%2/g,b)},b.prototype.init=function(){var b=this;a(document).ready(function(){b.enable(),b.build()})},b.prototype.enable=function(){var b=this;a("body").on("click","a[rel^=lightbox], area[rel^=lightbox], a[data-lightbox], area[data-lightbox]",function(c){return b.start(a(c.currentTarget)),!1})},b.prototype.build=function(){var b=this;a('<div id="lightboxOverlay" class="lightboxOverlay"></div><div id="lightbox" class="lightbox"><div class="lb-outerContainer"><div class="lb-container"><img class="lb-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" /><div class="lb-nav"><a class="lb-prev" href="" ></a><a class="lb-next" href="" ></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div>').appendTo(a("body")),this.$lightbox=a("#lightbox"),this.$overlay=a("#lightboxOverlay"),this.$outerContainer=this.$lightbox.find(".lb-outerContainer"),this.$container=this.$lightbox.find(".lb-container"),this.$image=this.$lightbox.find(".lb-image"),this.$nav=this.$lightbox.find(".lb-nav"),this.containerPadding={top:parseInt(this.$container.css("padding-top"),10),right:parseInt(this.$container.css("padding-right"),10),bottom:parseInt(this.$container.css("padding-bottom"),10),left:parseInt(this.$container.css("padding-left"),10)},this.imageBorderWidth={top:parseInt(this.$image.css("border-top-width"),10),right:parseInt(this.$image.css("border-right-width"),10),bottom:parseInt(this.$image.css("border-bottom-width"),10),left:parseInt(this.$image.css("border-left-width"),10)},this.$overlay.hide().on("click",function(){return b.end(),!1}),this.$lightbox.hide().on("click",function(c){return"lightbox"===a(c.target).attr("id")&&b.end(),!1}),this.$outerContainer.on("click",function(c){return"lightbox"===a(c.target).attr("id")&&b.end(),!1}),this.$lightbox.find(".lb-prev").on("click",function(){return 0===b.currentImageIndex?b.changeImage(b.album.length-1):b.changeImage(b.currentImageIndex-1),!1}),this.$lightbox.find(".lb-next").on("click",function(){return b.currentImageIndex===b.album.length-1?b.changeImage(0):b.changeImage(b.currentImageIndex+1),!1}),this.$nav.on("mousedown",function(a){3===a.which&&(b.$nav.css("pointer-events","none"),b.$lightbox.one("contextmenu",function(){setTimeout(function(){this.$nav.css("pointer-events","auto")}.bind(b),0)}))}),this.$lightbox.find(".lb-loader, .lb-close").on("click",function(){return b.end(),!1})},b.prototype.start=function(b){function c(a){d.album.push({link:a.attr("href"),title:a.attr("data-title")||a.attr("title")})}var d=this,e=a(window);e.on("resize",a.proxy(this.sizeOverlay,this)),a("select, object, embed").css({visibility:"hidden"}),this.sizeOverlay(),this.album=[];var f,g=0,h=b.attr("data-lightbox");if(h){f=a(b.prop("tagName")+'[data-lightbox="'+h+'"]');for(var i=0;i<f.length;i=++i)c(a(f[i])),f[i]===b[0]&&(g=i)}else if("lightbox"===b.attr("rel"))c(b);else{f=a(b.prop("tagName")+'[rel="'+b.attr("rel")+'"]');for(var j=0;j<f.length;j=++j)c(a(f[j])),f[j]===b[0]&&(g=j)}var k=e.scrollTop()+this.options.positionFromTop,l=e.scrollLeft();this.$lightbox.css({top:k+"px",left:l+"px"}).fadeIn(this.options.fadeDuration),this.options.disableScrolling&&a("body").addClass("lb-disable-scrolling"),this.changeImage(g)},b.prototype.changeImage=function(b){var c=this;this.disableKeyboardNav();var d=this.$lightbox.find(".lb-image");this.$overlay.fadeIn(this.options.fadeDuration),a(".lb-loader").fadeIn("slow"),this.$lightbox.find(".lb-image, .lb-nav, .lb-prev, .lb-next, .lb-dataContainer, .lb-numbers, .lb-caption").hide(),this.$outerContainer.addClass("animating");var e=new Image;e.onload=function(){var f,g,h,i,j,k,l;d.attr("src",c.album[b].link),f=a(e),d.width(e.width),d.height(e.height),c.options.fitImagesInViewport&&(l=a(window).width(),k=a(window).height(),j=l-c.containerPadding.left-c.containerPadding.right-c.imageBorderWidth.left-c.imageBorderWidth.right-20,i=k-c.containerPadding.top-c.containerPadding.bottom-c.imageBorderWidth.top-c.imageBorderWidth.bottom-120,c.options.maxWidth&&c.options.maxWidth<j&&(j=c.options.maxWidth),c.options.maxHeight&&c.options.maxHeight<j&&(i=c.options.maxHeight),(e.width>j||e.height>i)&&(e.width/j>e.height/i?(h=j,g=parseInt(e.height/(e.width/h),10),d.width(h),d.height(g)):(g=i,h=parseInt(e.width/(e.height/g),10),d.width(h),d.height(g)))),c.sizeContainer(d.width(),d.height())},e.src=this.album[b].link,this.currentImageIndex=b},b.prototype.sizeOverlay=function(){this.$overlay.width(a(document).width()).height(a(document).height())},b.prototype.sizeContainer=function(a,b){function c(){d.$lightbox.find(".lb-dataContainer").width(g),d.$lightbox.find(".lb-prevLink").height(h),d.$lightbox.find(".lb-nextLink").height(h),d.showImage()}var d=this,e=this.$outerContainer.outerWidth(),f=this.$outerContainer.outerHeight(),g=a+this.containerPadding.left+this.containerPadding.right+this.imageBorderWidth.left+this.imageBorderWidth.right,h=b+this.containerPadding.top+this.containerPadding.bottom+this.imageBorderWidth.top+this.imageBorderWidth.bottom;e!==g||f!==h?this.$outerContainer.animate({width:g,height:h},this.options.resizeDuration,"swing",function(){c()}):c()},b.prototype.showImage=function(){this.$lightbox.find(".lb-loader").stop(!0).hide(),this.$lightbox.find(".lb-image").fadeIn(this.options.imageFadeDuration),this.updateNav(),this.updateDetails(),this.preloadNeighboringImages(),this.enableKeyboardNav()},b.prototype.updateNav=function(){var a=!1;try{document.createEvent("TouchEvent"),a=this.options.alwaysShowNavOnTouchDevices?!0:!1}catch(b){}this.$lightbox.find(".lb-nav").show(),this.album.length>1&&(this.options.wrapAround?(a&&this.$lightbox.find(".lb-prev, .lb-next").css("opacity","1"),this.$lightbox.find(".lb-prev, .lb-next").show()):(this.currentImageIndex>0&&(this.$lightbox.find(".lb-prev").show(),a&&this.$lightbox.find(".lb-prev").css("opacity","1")),this.currentImageIndex<this.album.length-1&&(this.$lightbox.find(".lb-next").show(),a&&this.$lightbox.find(".lb-next").css("opacity","1"))))},b.prototype.updateDetails=function(){var b=this;if("undefined"!=typeof this.album[this.currentImageIndex].title&&""!==this.album[this.currentImageIndex].title){var c=this.$lightbox.find(".lb-caption");this.options.sanitizeTitle?c.text(this.album[this.currentImageIndex].title):c.html(this.album[this.currentImageIndex].title),c.fadeIn("fast").find("a").on("click",function(b){void 0!==a(this).attr("target")?window.open(a(this).attr("href"),a(this).attr("target")):location.href=a(this).attr("href")})}if(this.album.length>1&&this.options.showImageNumberLabel){var d=this.imageCountLabel(this.currentImageIndex+1,this.album.length);this.$lightbox.find(".lb-number").text(d).fadeIn("fast")}else this.$lightbox.find(".lb-number").hide();this.$outerContainer.removeClass("animating"),this.$lightbox.find(".lb-dataContainer").fadeIn(this.options.resizeDuration,function(){return b.sizeOverlay()})},b.prototype.preloadNeighboringImages=function(){if(this.album.length>this.currentImageIndex+1){var a=new Image;a.src=this.album[this.currentImageIndex+1].link}if(this.currentImageIndex>0){var b=new Image;b.src=this.album[this.currentImageIndex-1].link}},b.prototype.enableKeyboardNav=function(){a(document).on("keyup.keyboard",a.proxy(this.keyboardAction,this))},b.prototype.disableKeyboardNav=function(){a(document).off(".keyboard")},b.prototype.keyboardAction=function(a){var b=27,c=37,d=39,e=a.keyCode,f=String.fromCharCode(e).toLowerCase();e===b||f.match(/x|o|c/)?this.end():"p"===f||e===c?0!==this.currentImageIndex?this.changeImage(this.currentImageIndex-1):this.options.wrapAround&&this.album.length>1&&this.changeImage(this.album.length-1):("n"===f||e===d)&&(this.currentImageIndex!==this.album.length-1?this.changeImage(this.currentImageIndex+1):this.options.wrapAround&&this.album.length>1&&this.changeImage(0))},b.prototype.end=function(){this.disableKeyboardNav(),a(window).off("resize",this.sizeOverlay),this.$lightbox.fadeOut(this.options.fadeDuration),this.$overlay.fadeOut(this.options.fadeDuration),a("select, object, embed").css({visibility:"visible"}),this.options.disableScrolling&&a("body").removeClass("lb-disable-scrolling")},new b});
}

jQuery.fn.liveSearch=function(conf){var config=jQuery.extend({url:'/search-results.php?q=',id:'jquery-live-search',duration:400,typeDelay:200,loadingClass:'loading',onSlideUp:function(){},uptadePosition:false},conf);var liveSearch=jQuery('#'+config.id);if(!liveSearch.length){liveSearch=jQuery('<div id="'+config.id+'"></div>').appendTo(document.body).hide().slideUp(0);jQuery(document.body).click(function(event){var clicked=jQuery(event.target);if(!(clicked.is('#'+config.id)||clicked.parents('#'+config.id).length||clicked.is('input'))){liveSearch.slideUp(config.duration,function(){config.onSlideUp()})}})}return this.each(function(){var input=jQuery(this).attr('autocomplete','off');var liveSearchPaddingBorderHoriz=parseInt(liveSearch.css('paddingLeft'),10)+parseInt(liveSearch.css('paddingRight'),10)+parseInt(liveSearch.css('borderLeftWidth'),10)+parseInt(liveSearch.css('borderRightWidth'),10);var repositionLiveSearch=function(){var tmpOffset=input.offset();var inputDim={left:tmpOffset.left,top:tmpOffset.top,width:input.outerWidth(),height:input.outerHeight()};inputDim.topPos=inputDim.top+inputDim.height;inputDim.totalWidth=inputDim.width-liveSearchPaddingBorderHoriz;liveSearch.css({position:'absolute',left:inputDim.left+'px',top:inputDim.topPos+'px',width:inputDim.totalWidth+'px'})};var showLiveSearch=function(){repositionLiveSearch();$(window).unbind('resize',repositionLiveSearch);$(window).bind('resize',repositionLiveSearch);liveSearch.slideDown(config.duration)};var hideLiveSearch=function(){liveSearch.slideUp(config.duration,function(){config.onSlideUp()})};input.focus(function(){if(this.value!==''){if(liveSearch.html()==''){this.lastValue='';input.keyup()}else{setTimeout(showLiveSearch,1)}}}).keyup(function(){if(this.value!=this.lastValue){input.addClass(config.loadingClass);var q=this.value;if(this.timer){clearTimeout(this.timer)}this.timer=setTimeout(function(){jQuery.get(config.url+q,function(data){input.removeClass(config.loadingClass);if(data.length&&q.length){liveSearch.html(data);showLiveSearch()}else{hideLiveSearch()}})},config.typeDelay);this.lastValue=this.value}})})};

/*! jPlayer 2.9.2 for jQuery ~ (c) 2009-2014 Happyworm Ltd ~ MIT License */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],b):b("object"==typeof exports?require("jquery"):a.jQuery?a.jQuery:a.Zepto)}(this,function(a,b){a.fn.jPlayer=function(c){var d="jPlayer",e="string"==typeof c,f=Array.prototype.slice.call(arguments,1),g=this;return c=!e&&f.length?a.extend.apply(null,[!0,c].concat(f)):c,e&&"_"===c.charAt(0)?g:(this.each(e?function(){var e=a(this).data(d),h=e&&a.isFunction(e[c])?e[c].apply(e,f):e;return h!==e&&h!==b?(g=h,!1):void 0}:function(){var b=a(this).data(d);b?b.option(c||{}):a(this).data(d,new a.jPlayer(c,this))}),g)},a.jPlayer=function(b,c){if(arguments.length){this.element=a(c),this.options=a.extend(!0,{},this.options,b);var d=this;this.element.bind("remove.jPlayer",function(){d.destroy()}),this._init()}},"function"!=typeof a.fn.stop&&(a.fn.stop=function(){}),a.jPlayer.emulateMethods="load play pause",a.jPlayer.emulateStatus="src readyState networkState currentTime duration paused ended playbackRate",a.jPlayer.emulateOptions="muted volume",a.jPlayer.reservedEvent="ready flashreset resize repeat error warning",a.jPlayer.event={},a.each(["ready","setmedia","flashreset","resize","repeat","click","error","warning","loadstart","progress","suspend","abort","emptied","stalled","play","pause","loadedmetadata","loadeddata","waiting","playing","canplay","canplaythrough","seeking","seeked","timeupdate","ended","ratechange","durationchange","volumechange"],function(){a.jPlayer.event[this]="jPlayer_"+this}),a.jPlayer.htmlEvent=["loadstart","abort","emptied","stalled","loadedmetadata","canplay","canplaythrough"],a.jPlayer.pause=function(){a.jPlayer.prototype.destroyRemoved(),a.each(a.jPlayer.prototype.instances,function(a,b){b.data("jPlayer").status.srcSet&&b.jPlayer("pause")})},a.jPlayer.timeFormat={showHour:!1,showMin:!0,showSec:!0,padHour:!1,padMin:!0,padSec:!0,sepHour:":",sepMin:":",sepSec:""};var c=function(){this.init()};c.prototype={init:function(){this.options={timeFormat:a.jPlayer.timeFormat}},time:function(a){a=a&&"number"==typeof a?a:0;var b=new Date(1e3*a),c=b.getUTCHours(),d=this.options.timeFormat.showHour?b.getUTCMinutes():b.getUTCMinutes()+60*c,e=this.options.timeFormat.showMin?b.getUTCSeconds():b.getUTCSeconds()+60*d,f=this.options.timeFormat.padHour&&10>c?"0"+c:c,g=this.options.timeFormat.padMin&&10>d?"0"+d:d,h=this.options.timeFormat.padSec&&10>e?"0"+e:e,i="";return i+=this.options.timeFormat.showHour?f+this.options.timeFormat.sepHour:"",i+=this.options.timeFormat.showMin?g+this.options.timeFormat.sepMin:"",i+=this.options.timeFormat.showSec?h+this.options.timeFormat.sepSec:""}};var d=new c;a.jPlayer.convertTime=function(a){return d.time(a)},a.jPlayer.uaBrowser=function(a){var b=a.toLowerCase(),c=/(webkit)[ \/]([\w.]+)/,d=/(opera)(?:.*version)?[ \/]([\w.]+)/,e=/(msie) ([\w.]+)/,f=/(mozilla)(?:.*? rv:([\w.]+))?/,g=c.exec(b)||d.exec(b)||e.exec(b)||b.indexOf("compatible")<0&&f.exec(b)||[];return{browser:g[1]||"",version:g[2]||"0"}},a.jPlayer.uaPlatform=function(a){var b=a.toLowerCase(),c=/(ipad|iphone|ipod|android|blackberry|playbook|windows ce|webos)/,d=/(ipad|playbook)/,e=/(android)/,f=/(mobile)/,g=c.exec(b)||[],h=d.exec(b)||!f.exec(b)&&e.exec(b)||[];return g[1]&&(g[1]=g[1].replace(/\s/g,"_")),{platform:g[1]||"",tablet:h[1]||""}},a.jPlayer.browser={},a.jPlayer.platform={};var e=a.jPlayer.uaBrowser(navigator.userAgent);e.browser&&(a.jPlayer.browser[e.browser]=!0,a.jPlayer.browser.version=e.version);var f=a.jPlayer.uaPlatform(navigator.userAgent);f.platform&&(a.jPlayer.platform[f.platform]=!0,a.jPlayer.platform.mobile=!f.tablet,a.jPlayer.platform.tablet=!!f.tablet),a.jPlayer.getDocMode=function(){var b;return a.jPlayer.browser.msie&&(document.documentMode?b=document.documentMode:(b=5,document.compatMode&&"CSS1Compat"===document.compatMode&&(b=7))),b},a.jPlayer.browser.documentMode=a.jPlayer.getDocMode(),a.jPlayer.nativeFeatures={init:function(){var a,b,c,d=document,e=d.createElement("video"),f={w3c:["fullscreenEnabled","fullscreenElement","requestFullscreen","exitFullscreen","fullscreenchange","fullscreenerror"],moz:["mozFullScreenEnabled","mozFullScreenElement","mozRequestFullScreen","mozCancelFullScreen","mozfullscreenchange","mozfullscreenerror"],webkit:["","webkitCurrentFullScreenElement","webkitRequestFullScreen","webkitCancelFullScreen","webkitfullscreenchange",""],webkitVideo:["webkitSupportsFullscreen","webkitDisplayingFullscreen","webkitEnterFullscreen","webkitExitFullscreen","",""],ms:["","msFullscreenElement","msRequestFullscreen","msExitFullscreen","MSFullscreenChange","MSFullscreenError"]},g=["w3c","moz","webkit","webkitVideo","ms"];for(this.fullscreen=a={support:{w3c:!!d[f.w3c[0]],moz:!!d[f.moz[0]],webkit:"function"==typeof d[f.webkit[3]],webkitVideo:"function"==typeof e[f.webkitVideo[2]],ms:"function"==typeof e[f.ms[2]]},used:{}},b=0,c=g.length;c>b;b++){var h=g[b];if(a.support[h]){a.spec=h,a.used[h]=!0;break}}if(a.spec){var i=f[a.spec];a.api={fullscreenEnabled:!0,fullscreenElement:function(a){return a=a?a:d,a[i[1]]},requestFullscreen:function(a){return a[i[2]]()},exitFullscreen:function(a){return a=a?a:d,a[i[3]]()}},a.event={fullscreenchange:i[4],fullscreenerror:i[5]}}else a.api={fullscreenEnabled:!1,fullscreenElement:function(){return null},requestFullscreen:function(){},exitFullscreen:function(){}},a.event={}}},a.jPlayer.nativeFeatures.init(),a.jPlayer.focus=null,a.jPlayer.keyIgnoreElementNames="A INPUT TEXTAREA SELECT BUTTON";var g=function(b){var c,d=a.jPlayer.focus;d&&(a.each(a.jPlayer.keyIgnoreElementNames.split(/\s+/g),function(a,d){return b.target.nodeName.toUpperCase()===d.toUpperCase()?(c=!0,!1):void 0}),c||a.each(d.options.keyBindings,function(c,e){return e&&a.isFunction(e.fn)&&("number"==typeof e.key&&b.which===e.key||"string"==typeof e.key&&b.key===e.key)?(b.preventDefault(),e.fn(d),!1):void 0}))};a.jPlayer.keys=function(b){var c="keydown.jPlayer";a(document.documentElement).unbind(c),b&&a(document.documentElement).bind(c,g)},a.jPlayer.keys(!0),a.jPlayer.prototype={count:0,version:{script:"2.9.2",needFlash:"2.9.0",flash:"unknown"},options:{swfPath:"js",solution:"html, flash",supplied:"mp3",auroraFormats:"wav",preload:"metadata",volume:.8,muted:!1,remainingDuration:!1,toggleDuration:!1,captureDuration:!0,playbackRate:1,defaultPlaybackRate:1,minPlaybackRate:.5,maxPlaybackRate:4,wmode:"opaque",backgroundColor:"#000000",cssSelectorAncestor:"#jp_container_1",cssSelector:{videoPlay:".jp-video-play",play:".jp-play",pause:".jp-pause",stop:".jp-stop",seekBar:".jp-seek-bar",playBar:".jp-play-bar",mute:".jp-mute",unmute:".jp-unmute",volumeBar:".jp-volume-bar",volumeBarValue:".jp-volume-bar-value",volumeMax:".jp-volume-max",playbackRateBar:".jp-playback-rate-bar",playbackRateBarValue:".jp-playback-rate-bar-value",currentTime:".jp-current-time",duration:".jp-duration",title:".jp-title",fullScreen:".jp-full-screen",restoreScreen:".jp-restore-screen",repeat:".jp-repeat",repeatOff:".jp-repeat-off",gui:".jp-gui",noSolution:".jp-no-solution"},stateClass:{playing:"jp-state-playing",seeking:"jp-state-seeking",muted:"jp-state-muted",looped:"jp-state-looped",fullScreen:"jp-state-full-screen",noVolume:"jp-state-no-volume"},useStateClassSkin:!1,autoBlur:!0,smoothPlayBar:!1,fullScreen:!1,fullWindow:!1,autohide:{restored:!1,full:!0,fadeIn:200,fadeOut:600,hold:1e3},loop:!1,repeat:function(b){b.jPlayer.options.loop?a(this).unbind(".jPlayerRepeat").bind(a.jPlayer.event.ended+".jPlayer.jPlayerRepeat",function(){a(this).jPlayer("play")}):a(this).unbind(".jPlayerRepeat")},nativeVideoControls:{},noFullWindow:{msie:/msie [0-6]\./,ipad:/ipad.*?os [0-4]\./,iphone:/iphone/,ipod:/ipod/,android_pad:/android [0-3]\.(?!.*?mobile)/,android_phone:/(?=.*android)(?!.*chrome)(?=.*mobile)/,blackberry:/blackberry/,windows_ce:/windows ce/,iemobile:/iemobile/,webos:/webos/},noVolume:{ipad:/ipad/,iphone:/iphone/,ipod:/ipod/,android_pad:/android(?!.*?mobile)/,android_phone:/android.*?mobile/,blackberry:/blackberry/,windows_ce:/windows ce/,iemobile:/iemobile/,webos:/webos/,playbook:/playbook/},timeFormat:{},keyEnabled:!1,audioFullScreen:!1,keyBindings:{play:{key:80,fn:function(a){a.status.paused?a.play():a.pause()}},fullScreen:{key:70,fn:function(a){(a.status.video||a.options.audioFullScreen)&&a._setOption("fullScreen",!a.options.fullScreen)}},muted:{key:77,fn:function(a){a._muted(!a.options.muted)}},volumeUp:{key:190,fn:function(a){a.volume(a.options.volume+.1)}},volumeDown:{key:188,fn:function(a){a.volume(a.options.volume-.1)}},loop:{key:76,fn:function(a){a._loop(!a.options.loop)}}},verticalVolume:!1,verticalPlaybackRate:!1,globalVolume:!1,idPrefix:"jp",noConflict:"jQuery",emulateHtml:!1,consoleAlerts:!0,errorAlerts:!1,warningAlerts:!1},optionsAudio:{size:{width:"0px",height:"0px",cssClass:""},sizeFull:{width:"0px",height:"0px",cssClass:""}},optionsVideo:{size:{width:"480px",height:"270px",cssClass:"jp-video-270p"},sizeFull:{width:"100%",height:"100%",cssClass:"jp-video-full"}},instances:{},status:{src:"",media:{},paused:!0,format:{},formatType:"",waitForPlay:!0,waitForLoad:!0,srcSet:!1,video:!1,seekPercent:0,currentPercentRelative:0,currentPercentAbsolute:0,currentTime:0,duration:0,remaining:0,videoWidth:0,videoHeight:0,readyState:0,networkState:0,playbackRate:1,ended:0},internal:{ready:!1},solution:{html:!0,aurora:!0,flash:!0},format:{mp3:{codec:"audio/mpeg",flashCanPlay:!0,media:"audio"},m4a:{codec:'audio/mp4; codecs="mp4a.40.2"',flashCanPlay:!0,media:"audio"},m3u8a:{codec:'application/vnd.apple.mpegurl; codecs="mp4a.40.2"',flashCanPlay:!1,media:"audio"},m3ua:{codec:"audio/mpegurl",flashCanPlay:!1,media:"audio"},oga:{codec:'audio/ogg; codecs="vorbis, opus"',flashCanPlay:!1,media:"audio"},flac:{codec:"audio/x-flac",flashCanPlay:!1,media:"audio"},wav:{codec:'audio/wav; codecs="1"',flashCanPlay:!1,media:"audio"},webma:{codec:'audio/webm; codecs="vorbis"',flashCanPlay:!1,media:"audio"},fla:{codec:"audio/x-flv",flashCanPlay:!0,media:"audio"},rtmpa:{codec:'audio/rtmp; codecs="rtmp"',flashCanPlay:!0,media:"audio"},m4v:{codec:'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',flashCanPlay:!0,media:"video"},m3u8v:{codec:'application/vnd.apple.mpegurl; codecs="avc1.42E01E, mp4a.40.2"',flashCanPlay:!1,media:"video"},m3uv:{codec:"audio/mpegurl",flashCanPlay:!1,media:"video"},ogv:{codec:'video/ogg; codecs="theora, vorbis"',flashCanPlay:!1,media:"video"},webmv:{codec:'video/webm; codecs="vorbis, vp8"',flashCanPlay:!1,media:"video"},flv:{codec:"video/x-flv",flashCanPlay:!0,media:"video"},rtmpv:{codec:'video/rtmp; codecs="rtmp"',flashCanPlay:!0,media:"video"}},_init:function(){var c=this;if(this.element.empty(),this.status=a.extend({},this.status),this.internal=a.extend({},this.internal),this.options.timeFormat=a.extend({},a.jPlayer.timeFormat,this.options.timeFormat),this.internal.cmdsIgnored=a.jPlayer.platform.ipad||a.jPlayer.platform.iphone||a.jPlayer.platform.ipod,this.internal.domNode=this.element.get(0),this.options.keyEnabled&&!a.jPlayer.focus&&(a.jPlayer.focus=this),this.androidFix={setMedia:!1,play:!1,pause:!1,time:0/0},a.jPlayer.platform.android&&(this.options.preload="auto"!==this.options.preload?"metadata":"auto"),this.formats=[],this.solutions=[],this.require={},this.htmlElement={},this.html={},this.html.audio={},this.html.video={},this.aurora={},this.aurora.formats=[],this.aurora.properties=[],this.flash={},this.css={},this.css.cs={},this.css.jq={},this.ancestorJq=[],this.options.volume=this._limitValue(this.options.volume,0,1),a.each(this.options.supplied.toLowerCase().split(","),function(b,d){var e=d.replace(/^\s+|\s+$/g,"");if(c.format[e]){var f=!1;a.each(c.formats,function(a,b){return e===b?(f=!0,!1):void 0}),f||c.formats.push(e)}}),a.each(this.options.solution.toLowerCase().split(","),function(b,d){var e=d.replace(/^\s+|\s+$/g,"");if(c.solution[e]){var f=!1;a.each(c.solutions,function(a,b){return e===b?(f=!0,!1):void 0}),f||c.solutions.push(e)}}),a.each(this.options.auroraFormats.toLowerCase().split(","),function(b,d){var e=d.replace(/^\s+|\s+$/g,"");if(c.format[e]){var f=!1;a.each(c.aurora.formats,function(a,b){return e===b?(f=!0,!1):void 0}),f||c.aurora.formats.push(e)}}),this.internal.instance="jp_"+this.count,this.instances[this.internal.instance]=this.element,this.element.attr("id")||this.element.attr("id",this.options.idPrefix+"_jplayer_"+this.count),this.internal.self=a.extend({},{id:this.element.attr("id"),jq:this.element}),this.internal.audio=a.extend({},{id:this.options.idPrefix+"_audio_"+this.count,jq:b}),this.internal.video=a.extend({},{id:this.options.idPrefix+"_video_"+this.count,jq:b}),this.internal.flash=a.extend({},{id:this.options.idPrefix+"_flash_"+this.count,jq:b,swf:this.options.swfPath+(".swf"!==this.options.swfPath.toLowerCase().slice(-4)?(this.options.swfPath&&"/"!==this.options.swfPath.slice(-1)?"/":"")+"jquery.jplayer.swf":"")}),this.internal.poster=a.extend({},{id:this.options.idPrefix+"_poster_"+this.count,jq:b}),a.each(a.jPlayer.event,function(a,d){c.options[a]!==b&&(c.element.bind(d+".jPlayer",c.options[a]),c.options[a]=b)}),this.require.audio=!1,this.require.video=!1,a.each(this.formats,function(a,b){c.require[c.format[b].media]=!0}),this.options=this.require.video?a.extend(!0,{},this.optionsVideo,this.options):a.extend(!0,{},this.optionsAudio,this.options),this._setSize(),this.status.nativeVideoControls=this._uaBlocklist(this.options.nativeVideoControls),this.status.noFullWindow=this._uaBlocklist(this.options.noFullWindow),this.status.noVolume=this._uaBlocklist(this.options.noVolume),a.jPlayer.nativeFeatures.fullscreen.api.fullscreenEnabled&&this._fullscreenAddEventListeners(),this._restrictNativeVideoControls(),this.htmlElement.poster=document.createElement("img"),this.htmlElement.poster.id=this.internal.poster.id,this.htmlElement.poster.onload=function(){(!c.status.video||c.status.waitForPlay)&&c.internal.poster.jq.show()},this.element.append(this.htmlElement.poster),this.internal.poster.jq=a("#"+this.internal.poster.id),this.internal.poster.jq.css({width:this.status.width,height:this.status.height}),this.internal.poster.jq.hide(),this.internal.poster.jq.bind("click.jPlayer",function(){c._trigger(a.jPlayer.event.click)}),this.html.audio.available=!1,this.require.audio&&(this.htmlElement.audio=document.createElement("audio"),this.htmlElement.audio.id=this.internal.audio.id,this.html.audio.available=!!this.htmlElement.audio.canPlayType&&this._testCanPlayType(this.htmlElement.audio)),this.html.video.available=!1,this.require.video&&(this.htmlElement.video=document.createElement("video"),this.htmlElement.video.id=this.internal.video.id,this.html.video.available=!!this.htmlElement.video.canPlayType&&this._testCanPlayType(this.htmlElement.video)),this.flash.available=this._checkForFlash(10.1),this.html.canPlay={},this.aurora.canPlay={},this.flash.canPlay={},a.each(this.formats,function(b,d){c.html.canPlay[d]=c.html[c.format[d].media].available&&""!==c.htmlElement[c.format[d].media].canPlayType(c.format[d].codec),c.aurora.canPlay[d]=a.inArray(d,c.aurora.formats)>-1,c.flash.canPlay[d]=c.format[d].flashCanPlay&&c.flash.available}),this.html.desired=!1,this.aurora.desired=!1,this.flash.desired=!1,a.each(this.solutions,function(b,d){if(0===b)c[d].desired=!0;else{var e=!1,f=!1;a.each(c.formats,function(a,b){c[c.solutions[0]].canPlay[b]&&("video"===c.format[b].media?f=!0:e=!0)}),c[d].desired=c.require.audio&&!e||c.require.video&&!f}}),this.html.support={},this.aurora.support={},this.flash.support={},a.each(this.formats,function(a,b){c.html.support[b]=c.html.canPlay[b]&&c.html.desired,c.aurora.support[b]=c.aurora.canPlay[b]&&c.aurora.desired,c.flash.support[b]=c.flash.canPlay[b]&&c.flash.desired}),this.html.used=!1,this.aurora.used=!1,this.flash.used=!1,a.each(this.solutions,function(b,d){a.each(c.formats,function(a,b){return c[d].support[b]?(c[d].used=!0,!1):void 0})}),this._resetActive(),this._resetGate(),this._cssSelectorAncestor(this.options.cssSelectorAncestor),this.html.used||this.aurora.used||this.flash.used?this.css.jq.noSolution.length&&this.css.jq.noSolution.hide():(this._error({type:a.jPlayer.error.NO_SOLUTION,context:"{solution:'"+this.options.solution+"', supplied:'"+this.options.supplied+"'}",message:a.jPlayer.errorMsg.NO_SOLUTION,hint:a.jPlayer.errorHint.NO_SOLUTION}),this.css.jq.noSolution.length&&this.css.jq.noSolution.show()),this.flash.used){var d,e="jQuery="+encodeURI(this.options.noConflict)+"&id="+encodeURI(this.internal.self.id)+"&vol="+this.options.volume+"&muted="+this.options.muted;if(a.jPlayer.browser.msie&&(Number(a.jPlayer.browser.version)<9||a.jPlayer.browser.documentMode<9)){var f='<object id="'+this.internal.flash.id+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="0" height="0" tabindex="-1"></object>',g=['<param name="movie" value="'+this.internal.flash.swf+'" />','<param name="FlashVars" value="'+e+'" />','<param name="allowScriptAccess" value="always" />','<param name="bgcolor" value="'+this.options.backgroundColor+'" />','<param name="wmode" value="'+this.options.wmode+'" />'];d=document.createElement(f);for(var h=0;h<g.length;h++)d.appendChild(document.createElement(g[h]))}else{var i=function(a,b,c){var d=document.createElement("param");d.setAttribute("name",b),d.setAttribute("value",c),a.appendChild(d)};d=document.createElement("object"),d.setAttribute("id",this.internal.flash.id),d.setAttribute("name",this.internal.flash.id),d.setAttribute("data",this.internal.flash.swf),d.setAttribute("type","application/x-shockwave-flash"),d.setAttribute("width","1"),d.setAttribute("height","1"),d.setAttribute("tabindex","-1"),i(d,"flashvars",e),i(d,"allowscriptaccess","always"),i(d,"bgcolor",this.options.backgroundColor),i(d,"wmode",this.options.wmode)}this.element.append(d),this.internal.flash.jq=a(d)}this.status.playbackRateEnabled=this.html.used&&!this.flash.used?this._testPlaybackRate("audio"):!1,this._updatePlaybackRate(),this.html.used&&(this.html.audio.available&&(this._addHtmlEventListeners(this.htmlElement.audio,this.html.audio),this.element.append(this.htmlElement.audio),this.internal.audio.jq=a("#"+this.internal.audio.id)),this.html.video.available&&(this._addHtmlEventListeners(this.htmlElement.video,this.html.video),this.element.append(this.htmlElement.video),this.internal.video.jq=a("#"+this.internal.video.id),this.internal.video.jq.css(this.status.nativeVideoControls?{width:this.status.width,height:this.status.height}:{width:"0px",height:"0px"}),this.internal.video.jq.bind("click.jPlayer",function(){c._trigger(a.jPlayer.event.click)}))),this.aurora.used,this.options.emulateHtml&&this._emulateHtmlBridge(),!this.html.used&&!this.aurora.used||this.flash.used||setTimeout(function(){c.internal.ready=!0,c.version.flash="n/a",c._trigger(a.jPlayer.event.repeat),c._trigger(a.jPlayer.event.ready)},100),this._updateNativeVideoControls(),this.css.jq.videoPlay.length&&this.css.jq.videoPlay.hide(),a.jPlayer.prototype.count++},destroy:function(){this.clearMedia(),this._removeUiClass(),this.css.jq.currentTime.length&&this.css.jq.currentTime.text(""),this.css.jq.duration.length&&this.css.jq.duration.text(""),a.each(this.css.jq,function(a,b){b.length&&b.unbind(".jPlayer")}),this.internal.poster.jq.unbind(".jPlayer"),this.internal.video.jq&&this.internal.video.jq.unbind(".jPlayer"),this._fullscreenRemoveEventListeners(),this===a.jPlayer.focus&&(a.jPlayer.focus=null),this.options.emulateHtml&&this._destroyHtmlBridge(),this.element.removeData("jPlayer"),this.element.unbind(".jPlayer"),this.element.empty(),delete this.instances[this.internal.instance]},destroyRemoved:function(){var b=this;a.each(this.instances,function(a,c){b.element!==c&&(c.data("jPlayer")||(c.jPlayer("destroy"),delete b.instances[a]))})},enable:function(){},disable:function(){},_testCanPlayType:function(a){try{return a.canPlayType(this.format.mp3.codec),!0}catch(b){return!1}},_testPlaybackRate:function(a){var b,c=.5;a="string"==typeof a?a:"audio",b=document.createElement(a);try{return"playbackRate"in b?(b.playbackRate=c,b.playbackRate===c):!1}catch(d){return!1}},_uaBlocklist:function(b){var c=navigator.userAgent.toLowerCase(),d=!1;return a.each(b,function(a,b){return b&&b.test(c)?(d=!0,!1):void 0}),d},_restrictNativeVideoControls:function(){this.require.audio&&this.status.nativeVideoControls&&(this.status.nativeVideoControls=!1,this.status.noFullWindow=!0)},_updateNativeVideoControls:function(){this.html.video.available&&this.html.used&&(this.htmlElement.video.controls=this.status.nativeVideoControls,this._updateAutohide(),this.status.nativeVideoControls&&this.require.video?(this.internal.poster.jq.hide(),this.internal.video.jq.css({width:this.status.width,height:this.status.height})):this.status.waitForPlay&&this.status.video&&(this.internal.poster.jq.show(),this.internal.video.jq.css({width:"0px",height:"0px"})))},_addHtmlEventListeners:function(b,c){var d=this;b.preload=this.options.preload,b.muted=this.options.muted,b.volume=this.options.volume,this.status.playbackRateEnabled&&(b.defaultPlaybackRate=this.options.defaultPlaybackRate,b.playbackRate=this.options.playbackRate),b.addEventListener("progress",function(){c.gate&&(d.internal.cmdsIgnored&&this.readyState>0&&(d.internal.cmdsIgnored=!1),d._getHtmlStatus(b),d._updateInterface(),d._trigger(a.jPlayer.event.progress))},!1),b.addEventListener("loadeddata",function(){c.gate&&(d.androidFix.setMedia=!1,d.androidFix.play&&(d.androidFix.play=!1,d.play(d.androidFix.time)),d.androidFix.pause&&(d.androidFix.pause=!1,d.pause(d.androidFix.time)),d._trigger(a.jPlayer.event.loadeddata))},!1),b.addEventListener("timeupdate",function(){c.gate&&(d._getHtmlStatus(b),d._updateInterface(),d._trigger(a.jPlayer.event.timeupdate))},!1),b.addEventListener("durationchange",function(){c.gate&&(d._getHtmlStatus(b),d._updateInterface(),d._trigger(a.jPlayer.event.durationchange))},!1),b.addEventListener("play",function(){c.gate&&(d._updateButtons(!0),d._html_checkWaitForPlay(),d._trigger(a.jPlayer.event.play))},!1),b.addEventListener("playing",function(){c.gate&&(d._updateButtons(!0),d._seeked(),d._trigger(a.jPlayer.event.playing))},!1),b.addEventListener("pause",function(){c.gate&&(d._updateButtons(!1),d._trigger(a.jPlayer.event.pause))},!1),b.addEventListener("waiting",function(){c.gate&&(d._seeking(),d._trigger(a.jPlayer.event.waiting))},!1),b.addEventListener("seeking",function(){c.gate&&(d._seeking(),d._trigger(a.jPlayer.event.seeking))},!1),b.addEventListener("seeked",function(){c.gate&&(d._seeked(),d._trigger(a.jPlayer.event.seeked))},!1),b.addEventListener("volumechange",function(){c.gate&&(d.options.volume=b.volume,d.options.muted=b.muted,d._updateMute(),d._updateVolume(),d._trigger(a.jPlayer.event.volumechange))},!1),b.addEventListener("ratechange",function(){c.gate&&(d.options.defaultPlaybackRate=b.defaultPlaybackRate,d.options.playbackRate=b.playbackRate,d._updatePlaybackRate(),d._trigger(a.jPlayer.event.ratechange))},!1),b.addEventListener("suspend",function(){c.gate&&(d._seeked(),d._trigger(a.jPlayer.event.suspend))},!1),b.addEventListener("ended",function(){c.gate&&(a.jPlayer.browser.webkit||(d.htmlElement.media.currentTime=0),d.htmlElement.media.pause(),d._updateButtons(!1),d._getHtmlStatus(b,!0),d._updateInterface(),d._trigger(a.jPlayer.event.ended))},!1),b.addEventListener("error",function(){c.gate&&(d._updateButtons(!1),d._seeked(),d.status.srcSet&&(clearTimeout(d.internal.htmlDlyCmdId),d.status.waitForLoad=!0,d.status.waitForPlay=!0,d.status.video&&!d.status.nativeVideoControls&&d.internal.video.jq.css({width:"0px",height:"0px"}),d._validString(d.status.media.poster)&&!d.status.nativeVideoControls&&d.internal.poster.jq.show(),d.css.jq.videoPlay.length&&d.css.jq.videoPlay.show(),d._error({type:a.jPlayer.error.URL,context:d.status.src,message:a.jPlayer.errorMsg.URL,hint:a.jPlayer.errorHint.URL})))},!1),a.each(a.jPlayer.htmlEvent,function(e,f){b.addEventListener(this,function(){c.gate&&d._trigger(a.jPlayer.event[f])},!1)})},_addAuroraEventListeners:function(b,c){var d=this;b.volume=100*this.options.volume,b.on("progress",function(){c.gate&&(d.internal.cmdsIgnored&&this.readyState>0&&(d.internal.cmdsIgnored=!1),d._getAuroraStatus(b),d._updateInterface(),d._trigger(a.jPlayer.event.progress),b.duration>0&&d._trigger(a.jPlayer.event.timeupdate))},!1),b.on("ready",function(){c.gate&&d._trigger(a.jPlayer.event.loadeddata)},!1),b.on("duration",function(){c.gate&&(d._getAuroraStatus(b),d._updateInterface(),d._trigger(a.jPlayer.event.durationchange))},!1),b.on("end",function(){c.gate&&(d._updateButtons(!1),d._getAuroraStatus(b,!0),d._updateInterface(),d._trigger(a.jPlayer.event.ended))},!1),b.on("error",function(){c.gate&&(d._updateButtons(!1),d._seeked(),d.status.srcSet&&(d.status.waitForLoad=!0,d.status.waitForPlay=!0,d.status.video&&!d.status.nativeVideoControls&&d.internal.video.jq.css({width:"0px",height:"0px"}),d._validString(d.status.media.poster)&&!d.status.nativeVideoControls&&d.internal.poster.jq.show(),d.css.jq.videoPlay.length&&d.css.jq.videoPlay.show(),d._error({type:a.jPlayer.error.URL,context:d.status.src,message:a.jPlayer.errorMsg.URL,hint:a.jPlayer.errorHint.URL})))},!1)},_getHtmlStatus:function(a,b){var c=0,d=0,e=0,f=0;isFinite(a.duration)&&(this.status.duration=a.duration),c=a.currentTime,d=this.status.duration>0?100*c/this.status.duration:0,"object"==typeof a.seekable&&a.seekable.length>0?(e=this.status.duration>0?100*a.seekable.end(a.seekable.length-1)/this.status.duration:100,f=this.status.duration>0?100*a.currentTime/a.seekable.end(a.seekable.length-1):0):(e=100,f=d),b&&(c=0,f=0,d=0),this.status.seekPercent=e,this.status.currentPercentRelative=f,this.status.currentPercentAbsolute=d,this.status.currentTime=c,this.status.remaining=this.status.duration-this.status.currentTime,this.status.videoWidth=a.videoWidth,this.status.videoHeight=a.videoHeight,this.status.readyState=a.readyState,this.status.networkState=a.networkState,this.status.playbackRate=a.playbackRate,this.status.ended=a.ended},_getAuroraStatus:function(a,b){var c=0,d=0,e=0,f=0;this.status.duration=a.duration/1e3,c=a.currentTime/1e3,d=this.status.duration>0?100*c/this.status.duration:0,a.buffered>0?(e=this.status.duration>0?a.buffered*this.status.duration/this.status.duration:100,f=this.status.duration>0?c/(a.buffered*this.status.duration):0):(e=100,f=d),b&&(c=0,f=0,d=0),this.status.seekPercent=e,this.status.currentPercentRelative=f,this.status.currentPercentAbsolute=d,this.status.currentTime=c,this.status.remaining=this.status.duration-this.status.currentTime,this.status.readyState=4,this.status.networkState=0,this.status.playbackRate=1,this.status.ended=!1},_resetStatus:function(){this.status=a.extend({},this.status,a.jPlayer.prototype.status)},_trigger:function(b,c,d){var e=a.Event(b);e.jPlayer={},e.jPlayer.version=a.extend({},this.version),e.jPlayer.options=a.extend(!0,{},this.options),e.jPlayer.status=a.extend(!0,{},this.status),e.jPlayer.html=a.extend(!0,{},this.html),e.jPlayer.aurora=a.extend(!0,{},this.aurora),e.jPlayer.flash=a.extend(!0,{},this.flash),c&&(e.jPlayer.error=a.extend({},c)),d&&(e.jPlayer.warning=a.extend({},d)),this.element.trigger(e)},jPlayerFlashEvent:function(b,c){if(b===a.jPlayer.event.ready)if(this.internal.ready){if(this.flash.gate){if(this.status.srcSet){var d=this.status.currentTime,e=this.status.paused;this.setMedia(this.status.media),this.volumeWorker(this.options.volume),d>0&&(e?this.pause(d):this.play(d))}this._trigger(a.jPlayer.event.flashreset)}}else this.internal.ready=!0,this.internal.flash.jq.css({width:"0px",height:"0px"}),this.version.flash=c.version,this.version.needFlash!==this.version.flash&&this._error({type:a.jPlayer.error.VERSION,context:this.version.flash,message:a.jPlayer.errorMsg.VERSION+this.version.flash,hint:a.jPlayer.errorHint.VERSION}),this._trigger(a.jPlayer.event.repeat),this._trigger(b);if(this.flash.gate)switch(b){case a.jPlayer.event.progress:this._getFlashStatus(c),this._updateInterface(),this._trigger(b);break;case a.jPlayer.event.timeupdate:this._getFlashStatus(c),this._updateInterface(),this._trigger(b);break;case a.jPlayer.event.play:this._seeked(),this._updateButtons(!0),this._trigger(b);break;case a.jPlayer.event.pause:this._updateButtons(!1),this._trigger(b);break;case a.jPlayer.event.ended:this._updateButtons(!1),this._trigger(b);break;case a.jPlayer.event.click:this._trigger(b);break;case a.jPlayer.event.error:this.status.waitForLoad=!0,this.status.waitForPlay=!0,this.status.video&&this.internal.flash.jq.css({width:"0px",height:"0px"}),this._validString(this.status.media.poster)&&this.internal.poster.jq.show(),this.css.jq.videoPlay.length&&this.status.video&&this.css.jq.videoPlay.show(),this.status.video?this._flash_setVideo(this.status.media):this._flash_setAudio(this.status.media),this._updateButtons(!1),this._error({type:a.jPlayer.error.URL,context:c.src,message:a.jPlayer.errorMsg.URL,hint:a.jPlayer.errorHint.URL});break;case a.jPlayer.event.seeking:this._seeking(),this._trigger(b);break;case a.jPlayer.event.seeked:this._seeked(),this._trigger(b);break;case a.jPlayer.event.ready:break;default:this._trigger(b)}return!1},_getFlashStatus:function(a){this.status.seekPercent=a.seekPercent,this.status.currentPercentRelative=a.currentPercentRelative,this.status.currentPercentAbsolute=a.currentPercentAbsolute,this.status.currentTime=a.currentTime,this.status.duration=a.duration,this.status.remaining=a.duration-a.currentTime,this.status.videoWidth=a.videoWidth,this.status.videoHeight=a.videoHeight,this.status.readyState=4,this.status.networkState=0,this.status.playbackRate=1,this.status.ended=!1},_updateButtons:function(a){a===b?a=!this.status.paused:this.status.paused=!a,a?this.addStateClass("playing"):this.removeStateClass("playing"),!this.status.noFullWindow&&this.options.fullWindow?this.addStateClass("fullScreen"):this.removeStateClass("fullScreen"),this.options.loop?this.addStateClass("looped"):this.removeStateClass("looped"),this.css.jq.play.length&&this.css.jq.pause.length&&(a?(this.css.jq.play.hide(),this.css.jq.pause.show()):(this.css.jq.play.show(),this.css.jq.pause.hide())),this.css.jq.restoreScreen.length&&this.css.jq.fullScreen.length&&(this.status.noFullWindow?(this.css.jq.fullScreen.hide(),this.css.jq.restoreScreen.hide()):this.options.fullWindow?(this.css.jq.fullScreen.hide(),this.css.jq.restoreScreen.show()):(this.css.jq.fullScreen.show(),this.css.jq.restoreScreen.hide())),this.css.jq.repeat.length&&this.css.jq.repeatOff.length&&(this.options.loop?(this.css.jq.repeat.hide(),this.css.jq.repeatOff.show()):(this.css.jq.repeat.show(),this.css.jq.repeatOff.hide()))},_updateInterface:function(){this.css.jq.seekBar.length&&this.css.jq.seekBar.width(this.status.seekPercent+"%"),this.css.jq.playBar.length&&(this.options.smoothPlayBar?this.css.jq.playBar.stop().animate({width:this.status.currentPercentAbsolute+"%"},250,"linear"):this.css.jq.playBar.width(this.status.currentPercentRelative+"%"));var a="";this.css.jq.currentTime.length&&(a=this._convertTime(this.status.currentTime),a!==this.css.jq.currentTime.text()&&this.css.jq.currentTime.text(this._convertTime(this.status.currentTime)));var b="",c=this.status.duration,d=this.status.remaining;this.css.jq.duration.length&&("string"==typeof this.status.media.duration?b=this.status.media.duration:("number"==typeof this.status.media.duration&&(c=this.status.media.duration,d=c-this.status.currentTime),b=this.options.remainingDuration?(d>0?"-":"")+this._convertTime(d):this._convertTime(c)),b!==this.css.jq.duration.text()&&this.css.jq.duration.text(b))},_convertTime:c.prototype.time,_seeking:function(){this.css.jq.seekBar.length&&this.css.jq.seekBar.addClass("jp-seeking-bg"),this.addStateClass("seeking")},_seeked:function(){this.css.jq.seekBar.length&&this.css.jq.seekBar.removeClass("jp-seeking-bg"),this.removeStateClass("seeking")},_resetGate:function(){this.html.audio.gate=!1,this.html.video.gate=!1,this.aurora.gate=!1,this.flash.gate=!1},_resetActive:function(){this.html.active=!1,this.aurora.active=!1,this.flash.active=!1},_escapeHtml:function(a){return a.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;").split('"').join("&quot;")},_qualifyURL:function(a){var b=document.createElement("div");
return b.innerHTML='<a href="'+this._escapeHtml(a)+'">x</a>',b.firstChild.href},_absoluteMediaUrls:function(b){var c=this;return a.each(b,function(a,d){d&&c.format[a]&&"data:"!==d.substr(0,5)&&(b[a]=c._qualifyURL(d))}),b},addStateClass:function(a){this.ancestorJq.length&&this.ancestorJq.addClass(this.options.stateClass[a])},removeStateClass:function(a){this.ancestorJq.length&&this.ancestorJq.removeClass(this.options.stateClass[a])},setMedia:function(b){var c=this,d=!1,e=this.status.media.poster!==b.poster;this._resetMedia(),this._resetGate(),this._resetActive(),this.androidFix.setMedia=!1,this.androidFix.play=!1,this.androidFix.pause=!1,b=this._absoluteMediaUrls(b),a.each(this.formats,function(e,f){var g="video"===c.format[f].media;return a.each(c.solutions,function(e,h){if(c[h].support[f]&&c._validString(b[f])){var i="html"===h,j="aurora"===h;return g?(i?(c.html.video.gate=!0,c._html_setVideo(b),c.html.active=!0):(c.flash.gate=!0,c._flash_setVideo(b),c.flash.active=!0),c.css.jq.videoPlay.length&&c.css.jq.videoPlay.show(),c.status.video=!0):(i?(c.html.audio.gate=!0,c._html_setAudio(b),c.html.active=!0,a.jPlayer.platform.android&&(c.androidFix.setMedia=!0)):j?(c.aurora.gate=!0,c._aurora_setAudio(b),c.aurora.active=!0):(c.flash.gate=!0,c._flash_setAudio(b),c.flash.active=!0),c.css.jq.videoPlay.length&&c.css.jq.videoPlay.hide(),c.status.video=!1),d=!0,!1}}),d?!1:void 0}),d?(this.status.nativeVideoControls&&this.html.video.gate||this._validString(b.poster)&&(e?this.htmlElement.poster.src=b.poster:this.internal.poster.jq.show()),"string"==typeof b.title&&(this.css.jq.title.length&&this.css.jq.title.html(b.title),this.htmlElement.audio&&this.htmlElement.audio.setAttribute("title",b.title),this.htmlElement.video&&this.htmlElement.video.setAttribute("title",b.title)),this.status.srcSet=!0,this.status.media=a.extend({},b),this._updateButtons(!1),this._updateInterface(),this._trigger(a.jPlayer.event.setmedia)):this._error({type:a.jPlayer.error.NO_SUPPORT,context:"{supplied:'"+this.options.supplied+"'}",message:a.jPlayer.errorMsg.NO_SUPPORT,hint:a.jPlayer.errorHint.NO_SUPPORT})},_resetMedia:function(){this._resetStatus(),this._updateButtons(!1),this._updateInterface(),this._seeked(),this.internal.poster.jq.hide(),clearTimeout(this.internal.htmlDlyCmdId),this.html.active?this._html_resetMedia():this.aurora.active?this._aurora_resetMedia():this.flash.active&&this._flash_resetMedia()},clearMedia:function(){this._resetMedia(),this.html.active?this._html_clearMedia():this.aurora.active?this._aurora_clearMedia():this.flash.active&&this._flash_clearMedia(),this._resetGate(),this._resetActive()},load:function(){this.status.srcSet?this.html.active?this._html_load():this.aurora.active?this._aurora_load():this.flash.active&&this._flash_load():this._urlNotSetError("load")},focus:function(){this.options.keyEnabled&&(a.jPlayer.focus=this)},play:function(a){var b="object"==typeof a;b&&this.options.useStateClassSkin&&!this.status.paused?this.pause(a):(a="number"==typeof a?a:0/0,this.status.srcSet?(this.focus(),this.html.active?this._html_play(a):this.aurora.active?this._aurora_play(a):this.flash.active&&this._flash_play(a)):this._urlNotSetError("play"))},videoPlay:function(){this.play()},pause:function(a){a="number"==typeof a?a:0/0,this.status.srcSet?this.html.active?this._html_pause(a):this.aurora.active?this._aurora_pause(a):this.flash.active&&this._flash_pause(a):this._urlNotSetError("pause")},tellOthers:function(b,c){var d=this,e="function"==typeof c,f=Array.prototype.slice.call(arguments);"string"==typeof b&&(e&&f.splice(1,1),a.jPlayer.prototype.destroyRemoved(),a.each(this.instances,function(){d.element!==this&&(!e||c.call(this.data("jPlayer"),d))&&this.jPlayer.apply(this,f)}))},pauseOthers:function(a){this.tellOthers("pause",function(){return this.status.srcSet},a)},stop:function(){this.status.srcSet?this.html.active?this._html_pause(0):this.aurora.active?this._aurora_pause(0):this.flash.active&&this._flash_pause(0):this._urlNotSetError("stop")},playHead:function(a){a=this._limitValue(a,0,100),this.status.srcSet?this.html.active?this._html_playHead(a):this.aurora.active?this._aurora_playHead(a):this.flash.active&&this._flash_playHead(a):this._urlNotSetError("playHead")},_muted:function(a){this.mutedWorker(a),this.options.globalVolume&&this.tellOthers("mutedWorker",function(){return this.options.globalVolume},a)},mutedWorker:function(b){this.options.muted=b,this.html.used&&this._html_setProperty("muted",b),this.aurora.used&&this._aurora_mute(b),this.flash.used&&this._flash_mute(b),this.html.video.gate||this.html.audio.gate||(this._updateMute(b),this._updateVolume(this.options.volume),this._trigger(a.jPlayer.event.volumechange))},mute:function(a){var c="object"==typeof a;c&&this.options.useStateClassSkin&&this.options.muted?this._muted(!1):(a=a===b?!0:!!a,this._muted(a))},unmute:function(a){a=a===b?!0:!!a,this._muted(!a)},_updateMute:function(a){a===b&&(a=this.options.muted),a?this.addStateClass("muted"):this.removeStateClass("muted"),this.css.jq.mute.length&&this.css.jq.unmute.length&&(this.status.noVolume?(this.css.jq.mute.hide(),this.css.jq.unmute.hide()):a?(this.css.jq.mute.hide(),this.css.jq.unmute.show()):(this.css.jq.mute.show(),this.css.jq.unmute.hide()))},volume:function(a){this.volumeWorker(a),this.options.globalVolume&&this.tellOthers("volumeWorker",function(){return this.options.globalVolume},a)},volumeWorker:function(b){b=this._limitValue(b,0,1),this.options.volume=b,this.html.used&&this._html_setProperty("volume",b),this.aurora.used&&this._aurora_volume(b),this.flash.used&&this._flash_volume(b),this.html.video.gate||this.html.audio.gate||(this._updateVolume(b),this._trigger(a.jPlayer.event.volumechange))},volumeBar:function(b){if(this.css.jq.volumeBar.length){var c=a(b.currentTarget),d=c.offset(),e=b.pageX-d.left,f=c.width(),g=c.height()-b.pageY+d.top,h=c.height();this.volume(this.options.verticalVolume?g/h:e/f)}this.options.muted&&this._muted(!1)},_updateVolume:function(a){a===b&&(a=this.options.volume),a=this.options.muted?0:a,this.status.noVolume?(this.addStateClass("noVolume"),this.css.jq.volumeBar.length&&this.css.jq.volumeBar.hide(),this.css.jq.volumeBarValue.length&&this.css.jq.volumeBarValue.hide(),this.css.jq.volumeMax.length&&this.css.jq.volumeMax.hide()):(this.removeStateClass("noVolume"),this.css.jq.volumeBar.length&&this.css.jq.volumeBar.show(),this.css.jq.volumeBarValue.length&&(this.css.jq.volumeBarValue.show(),this.css.jq.volumeBarValue[this.options.verticalVolume?"height":"width"](100*a+"%")),this.css.jq.volumeMax.length&&this.css.jq.volumeMax.show())},volumeMax:function(){this.volume(1),this.options.muted&&this._muted(!1)},_cssSelectorAncestor:function(b){var c=this;this.options.cssSelectorAncestor=b,this._removeUiClass(),this.ancestorJq=b?a(b):[],b&&1!==this.ancestorJq.length&&this._warning({type:a.jPlayer.warning.CSS_SELECTOR_COUNT,context:b,message:a.jPlayer.warningMsg.CSS_SELECTOR_COUNT+this.ancestorJq.length+" found for cssSelectorAncestor.",hint:a.jPlayer.warningHint.CSS_SELECTOR_COUNT}),this._addUiClass(),a.each(this.options.cssSelector,function(a,b){c._cssSelector(a,b)}),this._updateInterface(),this._updateButtons(),this._updateAutohide(),this._updateVolume(),this._updateMute()},_cssSelector:function(b,c){var d=this;if("string"==typeof c)if(a.jPlayer.prototype.options.cssSelector[b]){if(this.css.jq[b]&&this.css.jq[b].length&&this.css.jq[b].unbind(".jPlayer"),this.options.cssSelector[b]=c,this.css.cs[b]=this.options.cssSelectorAncestor+" "+c,this.css.jq[b]=c?a(this.css.cs[b]):[],this.css.jq[b].length&&this[b]){var e=function(c){c.preventDefault(),d[b](c),d.options.autoBlur?a(this).blur():a(this).focus()};this.css.jq[b].bind("click.jPlayer",e)}c&&1!==this.css.jq[b].length&&this._warning({type:a.jPlayer.warning.CSS_SELECTOR_COUNT,context:this.css.cs[b],message:a.jPlayer.warningMsg.CSS_SELECTOR_COUNT+this.css.jq[b].length+" found for "+b+" method.",hint:a.jPlayer.warningHint.CSS_SELECTOR_COUNT})}else this._warning({type:a.jPlayer.warning.CSS_SELECTOR_METHOD,context:b,message:a.jPlayer.warningMsg.CSS_SELECTOR_METHOD,hint:a.jPlayer.warningHint.CSS_SELECTOR_METHOD});else this._warning({type:a.jPlayer.warning.CSS_SELECTOR_STRING,context:c,message:a.jPlayer.warningMsg.CSS_SELECTOR_STRING,hint:a.jPlayer.warningHint.CSS_SELECTOR_STRING})},duration:function(a){this.options.toggleDuration&&(this.options.captureDuration&&a.stopPropagation(),this._setOption("remainingDuration",!this.options.remainingDuration))},seekBar:function(b){if(this.css.jq.seekBar.length){var c=a(b.currentTarget),d=c.offset(),e=b.pageX-d.left,f=c.width(),g=100*e/f;this.playHead(g)}},playbackRate:function(a){this._setOption("playbackRate",a)},playbackRateBar:function(b){if(this.css.jq.playbackRateBar.length){var c,d,e=a(b.currentTarget),f=e.offset(),g=b.pageX-f.left,h=e.width(),i=e.height()-b.pageY+f.top,j=e.height();c=this.options.verticalPlaybackRate?i/j:g/h,d=c*(this.options.maxPlaybackRate-this.options.minPlaybackRate)+this.options.minPlaybackRate,this.playbackRate(d)}},_updatePlaybackRate:function(){var a=this.options.playbackRate,b=(a-this.options.minPlaybackRate)/(this.options.maxPlaybackRate-this.options.minPlaybackRate);this.status.playbackRateEnabled?(this.css.jq.playbackRateBar.length&&this.css.jq.playbackRateBar.show(),this.css.jq.playbackRateBarValue.length&&(this.css.jq.playbackRateBarValue.show(),this.css.jq.playbackRateBarValue[this.options.verticalPlaybackRate?"height":"width"](100*b+"%"))):(this.css.jq.playbackRateBar.length&&this.css.jq.playbackRateBar.hide(),this.css.jq.playbackRateBarValue.length&&this.css.jq.playbackRateBarValue.hide())},repeat:function(a){var b="object"==typeof a;this._loop(b&&this.options.useStateClassSkin&&this.options.loop?!1:!0)},repeatOff:function(){this._loop(!1)},_loop:function(b){this.options.loop!==b&&(this.options.loop=b,this._updateButtons(),this._trigger(a.jPlayer.event.repeat))},option:function(c,d){var e=c;if(0===arguments.length)return a.extend(!0,{},this.options);if("string"==typeof c){var f=c.split(".");if(d===b){for(var g=a.extend(!0,{},this.options),h=0;h<f.length;h++){if(g[f[h]]===b)return this._warning({type:a.jPlayer.warning.OPTION_KEY,context:c,message:a.jPlayer.warningMsg.OPTION_KEY,hint:a.jPlayer.warningHint.OPTION_KEY}),b;g=g[f[h]]}return g}e={};for(var i=e,j=0;j<f.length;j++)j<f.length-1?(i[f[j]]={},i=i[f[j]]):i[f[j]]=d}return this._setOptions(e),this},_setOptions:function(b){var c=this;return a.each(b,function(a,b){c._setOption(a,b)}),this},_setOption:function(b,c){var d=this;switch(b){case"volume":this.volume(c);break;case"muted":this._muted(c);break;case"globalVolume":this.options[b]=c;break;case"cssSelectorAncestor":this._cssSelectorAncestor(c);break;case"cssSelector":a.each(c,function(a,b){d._cssSelector(a,b)});break;case"playbackRate":this.options[b]=c=this._limitValue(c,this.options.minPlaybackRate,this.options.maxPlaybackRate),this.html.used&&this._html_setProperty("playbackRate",c),this._updatePlaybackRate();break;case"defaultPlaybackRate":this.options[b]=c=this._limitValue(c,this.options.minPlaybackRate,this.options.maxPlaybackRate),this.html.used&&this._html_setProperty("defaultPlaybackRate",c),this._updatePlaybackRate();break;case"minPlaybackRate":this.options[b]=c=this._limitValue(c,.1,this.options.maxPlaybackRate-.1),this._updatePlaybackRate();break;case"maxPlaybackRate":this.options[b]=c=this._limitValue(c,this.options.minPlaybackRate+.1,16),this._updatePlaybackRate();break;case"fullScreen":if(this.options[b]!==c){var e=a.jPlayer.nativeFeatures.fullscreen.used.webkitVideo;(!e||e&&!this.status.waitForPlay)&&(e||(this.options[b]=c),c?this._requestFullscreen():this._exitFullscreen(),e||this._setOption("fullWindow",c))}break;case"fullWindow":this.options[b]!==c&&(this._removeUiClass(),this.options[b]=c,this._refreshSize());break;case"size":this.options.fullWindow||this.options[b].cssClass===c.cssClass||this._removeUiClass(),this.options[b]=a.extend({},this.options[b],c),this._refreshSize();break;case"sizeFull":this.options.fullWindow&&this.options[b].cssClass!==c.cssClass&&this._removeUiClass(),this.options[b]=a.extend({},this.options[b],c),this._refreshSize();break;case"autohide":this.options[b]=a.extend({},this.options[b],c),this._updateAutohide();break;case"loop":this._loop(c);break;case"remainingDuration":this.options[b]=c,this._updateInterface();break;case"toggleDuration":this.options[b]=c;break;case"nativeVideoControls":this.options[b]=a.extend({},this.options[b],c),this.status.nativeVideoControls=this._uaBlocklist(this.options.nativeVideoControls),this._restrictNativeVideoControls(),this._updateNativeVideoControls();break;case"noFullWindow":this.options[b]=a.extend({},this.options[b],c),this.status.nativeVideoControls=this._uaBlocklist(this.options.nativeVideoControls),this.status.noFullWindow=this._uaBlocklist(this.options.noFullWindow),this._restrictNativeVideoControls(),this._updateButtons();break;case"noVolume":this.options[b]=a.extend({},this.options[b],c),this.status.noVolume=this._uaBlocklist(this.options.noVolume),this._updateVolume(),this._updateMute();break;case"emulateHtml":this.options[b]!==c&&(this.options[b]=c,c?this._emulateHtmlBridge():this._destroyHtmlBridge());break;case"timeFormat":this.options[b]=a.extend({},this.options[b],c);break;case"keyEnabled":this.options[b]=c,c||this!==a.jPlayer.focus||(a.jPlayer.focus=null);break;case"keyBindings":this.options[b]=a.extend(!0,{},this.options[b],c);break;case"audioFullScreen":this.options[b]=c;break;case"autoBlur":this.options[b]=c}return this},_refreshSize:function(){this._setSize(),this._addUiClass(),this._updateSize(),this._updateButtons(),this._updateAutohide(),this._trigger(a.jPlayer.event.resize)},_setSize:function(){this.options.fullWindow?(this.status.width=this.options.sizeFull.width,this.status.height=this.options.sizeFull.height,this.status.cssClass=this.options.sizeFull.cssClass):(this.status.width=this.options.size.width,this.status.height=this.options.size.height,this.status.cssClass=this.options.size.cssClass),this.element.css({width:this.status.width,height:this.status.height})},_addUiClass:function(){this.ancestorJq.length&&this.ancestorJq.addClass(this.status.cssClass)},_removeUiClass:function(){this.ancestorJq.length&&this.ancestorJq.removeClass(this.status.cssClass)},_updateSize:function(){this.internal.poster.jq.css({width:this.status.width,height:this.status.height}),!this.status.waitForPlay&&this.html.active&&this.status.video||this.html.video.available&&this.html.used&&this.status.nativeVideoControls?this.internal.video.jq.css({width:this.status.width,height:this.status.height}):!this.status.waitForPlay&&this.flash.active&&this.status.video&&this.internal.flash.jq.css({width:this.status.width,height:this.status.height})},_updateAutohide:function(){var a=this,b="mousemove.jPlayer",c=".jPlayerAutohide",d=b+c,e=function(b){var c,d,e=!1;"undefined"!=typeof a.internal.mouse?(c=a.internal.mouse.x-b.pageX,d=a.internal.mouse.y-b.pageY,e=Math.floor(c)>0||Math.floor(d)>0):e=!0,a.internal.mouse={x:b.pageX,y:b.pageY},e&&a.css.jq.gui.fadeIn(a.options.autohide.fadeIn,function(){clearTimeout(a.internal.autohideId),a.internal.autohideId=setTimeout(function(){a.css.jq.gui.fadeOut(a.options.autohide.fadeOut)},a.options.autohide.hold)})};this.css.jq.gui.length&&(this.css.jq.gui.stop(!0,!0),clearTimeout(this.internal.autohideId),delete this.internal.mouse,this.element.unbind(c),this.css.jq.gui.unbind(c),this.status.nativeVideoControls?this.css.jq.gui.hide():this.options.fullWindow&&this.options.autohide.full||!this.options.fullWindow&&this.options.autohide.restored?(this.element.bind(d,e),this.css.jq.gui.bind(d,e),this.css.jq.gui.hide()):this.css.jq.gui.show())},fullScreen:function(a){var b="object"==typeof a;b&&this.options.useStateClassSkin&&this.options.fullScreen?this._setOption("fullScreen",!1):this._setOption("fullScreen",!0)},restoreScreen:function(){this._setOption("fullScreen",!1)},_fullscreenAddEventListeners:function(){var b=this,c=a.jPlayer.nativeFeatures.fullscreen;c.api.fullscreenEnabled&&c.event.fullscreenchange&&("function"!=typeof this.internal.fullscreenchangeHandler&&(this.internal.fullscreenchangeHandler=function(){b._fullscreenchange()}),document.addEventListener(c.event.fullscreenchange,this.internal.fullscreenchangeHandler,!1))},_fullscreenRemoveEventListeners:function(){var b=a.jPlayer.nativeFeatures.fullscreen;this.internal.fullscreenchangeHandler&&document.removeEventListener(b.event.fullscreenchange,this.internal.fullscreenchangeHandler,!1)},_fullscreenchange:function(){this.options.fullScreen&&!a.jPlayer.nativeFeatures.fullscreen.api.fullscreenElement()&&this._setOption("fullScreen",!1)},_requestFullscreen:function(){var b=this.ancestorJq.length?this.ancestorJq[0]:this.element[0],c=a.jPlayer.nativeFeatures.fullscreen;c.used.webkitVideo&&(b=this.htmlElement.video),c.api.fullscreenEnabled&&c.api.requestFullscreen(b)},_exitFullscreen:function(){var b,c=a.jPlayer.nativeFeatures.fullscreen;c.used.webkitVideo&&(b=this.htmlElement.video),c.api.fullscreenEnabled&&c.api.exitFullscreen(b)},_html_initMedia:function(b){var c=a(this.htmlElement.media).empty();a.each(b.track||[],function(a,b){var d=document.createElement("track");d.setAttribute("kind",b.kind?b.kind:""),d.setAttribute("src",b.src?b.src:""),d.setAttribute("srclang",b.srclang?b.srclang:""),d.setAttribute("label",b.label?b.label:""),b.def&&d.setAttribute("default",b.def),c.append(d)}),this.htmlElement.media.src=this.status.src,"none"!==this.options.preload&&this._html_load(),this._trigger(a.jPlayer.event.timeupdate)},_html_setFormat:function(b){var c=this;a.each(this.formats,function(a,d){return c.html.support[d]&&b[d]?(c.status.src=b[d],c.status.format[d]=!0,c.status.formatType=d,!1):void 0})},_html_setAudio:function(a){this._html_setFormat(a),this.htmlElement.media=this.htmlElement.audio,this._html_initMedia(a)},_html_setVideo:function(a){this._html_setFormat(a),this.status.nativeVideoControls&&(this.htmlElement.video.poster=this._validString(a.poster)?a.poster:""),this.htmlElement.media=this.htmlElement.video,this._html_initMedia(a)},_html_resetMedia:function(){this.htmlElement.media&&(this.htmlElement.media.id!==this.internal.video.id||this.status.nativeVideoControls||this.internal.video.jq.css({width:"0px",height:"0px"}),this.htmlElement.media.pause())},_html_clearMedia:function(){this.htmlElement.media&&(this.htmlElement.media.src="about:blank",this.htmlElement.media.load())},_html_load:function(){this.status.waitForLoad&&(this.status.waitForLoad=!1,this.htmlElement.media.load()),clearTimeout(this.internal.htmlDlyCmdId)},_html_play:function(a){var b=this,c=this.htmlElement.media;if(this.androidFix.pause=!1,this._html_load(),this.androidFix.setMedia)this.androidFix.play=!0,this.androidFix.time=a;else if(isNaN(a))c.play();else{this.internal.cmdsIgnored&&c.play();try{if(c.seekable&&!("object"==typeof c.seekable&&c.seekable.length>0))throw 1;c.currentTime=a,c.play()}catch(d){return void(this.internal.htmlDlyCmdId=setTimeout(function(){b.play(a)},250))}}this._html_checkWaitForPlay()},_html_pause:function(a){var b=this,c=this.htmlElement.media;if(this.androidFix.play=!1,a>0?this._html_load():clearTimeout(this.internal.htmlDlyCmdId),c.pause(),this.androidFix.setMedia)this.androidFix.pause=!0,this.androidFix.time=a;else if(!isNaN(a))try{if(c.seekable&&!("object"==typeof c.seekable&&c.seekable.length>0))throw 1;c.currentTime=a}catch(d){return void(this.internal.htmlDlyCmdId=setTimeout(function(){b.pause(a)},250))}a>0&&this._html_checkWaitForPlay()},_html_playHead:function(a){var b=this,c=this.htmlElement.media;this._html_load();try{if("object"==typeof c.seekable&&c.seekable.length>0)c.currentTime=a*c.seekable.end(c.seekable.length-1)/100;else{if(!(c.duration>0)||isNaN(c.duration))throw"e";c.currentTime=a*c.duration/100}}catch(d){return void(this.internal.htmlDlyCmdId=setTimeout(function(){b.playHead(a)},250))}this.status.waitForLoad||this._html_checkWaitForPlay()},_html_checkWaitForPlay:function(){this.status.waitForPlay&&(this.status.waitForPlay=!1,this.css.jq.videoPlay.length&&this.css.jq.videoPlay.hide(),this.status.video&&(this.internal.poster.jq.hide(),this.internal.video.jq.css({width:this.status.width,height:this.status.height})))},_html_setProperty:function(a,b){this.html.audio.available&&(this.htmlElement.audio[a]=b),this.html.video.available&&(this.htmlElement.video[a]=b)},_aurora_setAudio:function(b){var c=this;a.each(this.formats,function(a,d){return c.aurora.support[d]&&b[d]?(c.status.src=b[d],c.status.format[d]=!0,c.status.formatType=d,!1):void 0}),this.aurora.player=new AV.Player.fromURL(this.status.src),this._addAuroraEventListeners(this.aurora.player,this.aurora),"auto"===this.options.preload&&(this._aurora_load(),this.status.waitForLoad=!1)},_aurora_resetMedia:function(){this.aurora.player&&this.aurora.player.stop()},_aurora_clearMedia:function(){},_aurora_load:function(){this.status.waitForLoad&&(this.status.waitForLoad=!1,this.aurora.player.preload())},_aurora_play:function(b){this.status.waitForLoad||isNaN(b)||this.aurora.player.seek(b),this.aurora.player.playing||this.aurora.player.play(),this.status.waitForLoad=!1,this._aurora_checkWaitForPlay(),this._updateButtons(!0),this._trigger(a.jPlayer.event.play)},_aurora_pause:function(b){isNaN(b)||this.aurora.player.seek(1e3*b),this.aurora.player.pause(),b>0&&this._aurora_checkWaitForPlay(),this._updateButtons(!1),this._trigger(a.jPlayer.event.pause)},_aurora_playHead:function(a){this.aurora.player.duration>0&&this.aurora.player.seek(a*this.aurora.player.duration/100),this.status.waitForLoad||this._aurora_checkWaitForPlay()},_aurora_checkWaitForPlay:function(){this.status.waitForPlay&&(this.status.waitForPlay=!1)},_aurora_volume:function(a){this.aurora.player.volume=100*a},_aurora_mute:function(a){a?(this.aurora.properties.lastvolume=this.aurora.player.volume,this.aurora.player.volume=0):this.aurora.player.volume=this.aurora.properties.lastvolume,this.aurora.properties.muted=a},_flash_setAudio:function(b){var c=this;try{a.each(this.formats,function(a,d){if(c.flash.support[d]&&b[d]){switch(d){case"m4a":case"fla":c._getMovie().fl_setAudio_m4a(b[d]);break;case"mp3":c._getMovie().fl_setAudio_mp3(b[d]);break;case"rtmpa":c._getMovie().fl_setAudio_rtmp(b[d])}return c.status.src=b[d],c.status.format[d]=!0,c.status.formatType=d,!1}}),"auto"===this.options.preload&&(this._flash_load(),this.status.waitForLoad=!1)}catch(d){this._flashError(d)}},_flash_setVideo:function(b){var c=this;try{a.each(this.formats,function(a,d){if(c.flash.support[d]&&b[d]){switch(d){case"m4v":case"flv":c._getMovie().fl_setVideo_m4v(b[d]);break;case"rtmpv":c._getMovie().fl_setVideo_rtmp(b[d])}return c.status.src=b[d],c.status.format[d]=!0,c.status.formatType=d,!1}}),"auto"===this.options.preload&&(this._flash_load(),this.status.waitForLoad=!1)}catch(d){this._flashError(d)}},_flash_resetMedia:function(){this.internal.flash.jq.css({width:"0px",height:"0px"}),this._flash_pause(0/0)},_flash_clearMedia:function(){try{this._getMovie().fl_clearMedia()}catch(a){this._flashError(a)}},_flash_load:function(){try{this._getMovie().fl_load()}catch(a){this._flashError(a)}this.status.waitForLoad=!1},_flash_play:function(a){try{this._getMovie().fl_play(a)}catch(b){this._flashError(b)}this.status.waitForLoad=!1,this._flash_checkWaitForPlay()},_flash_pause:function(a){try{this._getMovie().fl_pause(a)}catch(b){this._flashError(b)}a>0&&(this.status.waitForLoad=!1,this._flash_checkWaitForPlay())},_flash_playHead:function(a){try{this._getMovie().fl_play_head(a)}catch(b){this._flashError(b)}this.status.waitForLoad||this._flash_checkWaitForPlay()},_flash_checkWaitForPlay:function(){this.status.waitForPlay&&(this.status.waitForPlay=!1,this.css.jq.videoPlay.length&&this.css.jq.videoPlay.hide(),this.status.video&&(this.internal.poster.jq.hide(),this.internal.flash.jq.css({width:this.status.width,height:this.status.height})))},_flash_volume:function(a){try{this._getMovie().fl_volume(a)}catch(b){this._flashError(b)}},_flash_mute:function(a){try{this._getMovie().fl_mute(a)}catch(b){this._flashError(b)}},_getMovie:function(){return document[this.internal.flash.id]},_getFlashPluginVersion:function(){var a,b=0;if(window.ActiveXObject)try{if(a=new ActiveXObject("ShockwaveFlash.ShockwaveFlash")){var c=a.GetVariable("$version");c&&(c=c.split(" ")[1].split(","),b=parseInt(c[0],10)+"."+parseInt(c[1],10))}}catch(d){}else navigator.plugins&&navigator.mimeTypes.length>0&&(a=navigator.plugins["Shockwave Flash"],a&&(b=navigator.plugins["Shockwave Flash"].description.replace(/.*\s(\d+\.\d+).*/,"$1")));return 1*b},_checkForFlash:function(a){var b=!1;return this._getFlashPluginVersion()>=a&&(b=!0),b},_validString:function(a){return a&&"string"==typeof a},_limitValue:function(a,b,c){return b>a?b:a>c?c:a},_urlNotSetError:function(b){this._error({type:a.jPlayer.error.URL_NOT_SET,context:b,message:a.jPlayer.errorMsg.URL_NOT_SET,hint:a.jPlayer.errorHint.URL_NOT_SET})},_flashError:function(b){var c;c=this.internal.ready?"FLASH_DISABLED":"FLASH",this._error({type:a.jPlayer.error[c],context:this.internal.flash.swf,message:a.jPlayer.errorMsg[c]+b.message,hint:a.jPlayer.errorHint[c]}),this.internal.flash.jq.css({width:"1px",height:"1px"})},_error:function(b){this._trigger(a.jPlayer.event.error,b),this.options.errorAlerts&&this._alert("Error!"+(b.message?"\n"+b.message:"")+(b.hint?"\n"+b.hint:"")+"\nContext: "+b.context)},_warning:function(c){this._trigger(a.jPlayer.event.warning,b,c),this.options.warningAlerts&&this._alert("Warning!"+(c.message?"\n"+c.message:"")+(c.hint?"\n"+c.hint:"")+"\nContext: "+c.context)},_alert:function(a){var b="jPlayer "+this.version.script+" : id='"+this.internal.self.id+"' : "+a;this.options.consoleAlerts?window.console&&window.console.log&&window.console.log(b):alert(b)},_emulateHtmlBridge:function(){var b=this;a.each(a.jPlayer.emulateMethods.split(/\s+/g),function(a,c){b.internal.domNode[c]=function(a){b[c](a)}}),a.each(a.jPlayer.event,function(c,d){var e=!0;a.each(a.jPlayer.reservedEvent.split(/\s+/g),function(a,b){return b===c?(e=!1,!1):void 0}),e&&b.element.bind(d+".jPlayer.jPlayerHtml",function(){b._emulateHtmlUpdate();var a=document.createEvent("Event");a.initEvent(c,!1,!0),b.internal.domNode.dispatchEvent(a)})})},_emulateHtmlUpdate:function(){var b=this;a.each(a.jPlayer.emulateStatus.split(/\s+/g),function(a,c){b.internal.domNode[c]=b.status[c]}),a.each(a.jPlayer.emulateOptions.split(/\s+/g),function(a,c){b.internal.domNode[c]=b.options[c]})},_destroyHtmlBridge:function(){var b=this;this.element.unbind(".jPlayerHtml");var c=a.jPlayer.emulateMethods+" "+a.jPlayer.emulateStatus+" "+a.jPlayer.emulateOptions;a.each(c.split(/\s+/g),function(a,c){delete b.internal.domNode[c]})}},a.jPlayer.error={FLASH:"e_flash",FLASH_DISABLED:"e_flash_disabled",NO_SOLUTION:"e_no_solution",NO_SUPPORT:"e_no_support",URL:"e_url",URL_NOT_SET:"e_url_not_set",VERSION:"e_version"},a.jPlayer.errorMsg={FLASH:"jPlayer's Flash fallback is not configured correctly, or a command was issued before the jPlayer Ready event. Details: ",FLASH_DISABLED:"jPlayer's Flash fallback has been disabled by the browser due to the CSS rules you have used. Details: ",NO_SOLUTION:"No solution can be found by jPlayer in this browser. Neither HTML nor Flash can be used.",NO_SUPPORT:"It is not possible to play any media format provided in setMedia() on this browser using your current options.",URL:"Media URL could not be loaded.",URL_NOT_SET:"Attempt to issue media playback commands, while no media url is set.",VERSION:"jPlayer "+a.jPlayer.prototype.version.script+" needs Jplayer.swf version "+a.jPlayer.prototype.version.needFlash+" but found "},a.jPlayer.errorHint={FLASH:"Check your swfPath option and that Jplayer.swf is there.",FLASH_DISABLED:"Check that you have not display:none; the jPlayer entity or any ancestor.",NO_SOLUTION:"Review the jPlayer options: support and supplied.",NO_SUPPORT:"Video or audio formats defined in the supplied option are missing.",URL:"Check media URL is valid.",URL_NOT_SET:"Use setMedia() to set the media URL.",VERSION:"Update jPlayer files."},a.jPlayer.warning={CSS_SELECTOR_COUNT:"e_css_selector_count",CSS_SELECTOR_METHOD:"e_css_selector_method",CSS_SELECTOR_STRING:"e_css_selector_string",OPTION_KEY:"e_option_key"},a.jPlayer.warningMsg={CSS_SELECTOR_COUNT:"The number of css selectors found did not equal one: ",CSS_SELECTOR_METHOD:"The methodName given in jPlayer('cssSelector') is not a valid jPlayer method.",CSS_SELECTOR_STRING:"The methodCssSelector given in jPlayer('cssSelector') is not a String or is empty.",OPTION_KEY:"The option requested in jPlayer('option') is undefined."},a.jPlayer.warningHint={CSS_SELECTOR_COUNT:"Check your css selector and the ancestor.",CSS_SELECTOR_METHOD:"Check your method name.",CSS_SELECTOR_STRING:"Check your css selector is a string.",OPTION_KEY:"Check your option name."}});
(function(b,f){jPlayerPlaylist=function(a,c,d){var e=this;this.current=0;this.removing=this.shuffled=this.loop=!1;this.cssSelector=b.extend({},this._cssSelector,a);this.options=b.extend(!0,{keyBindings:{next:{key:39,fn:function(){e.next()}},previous:{key:37,fn:function(){e.previous()}}}},this._options,d);this.playlist=[];this.original=[];this._initPlaylist(c);this.cssSelector.title=this.cssSelector.cssSelectorAncestor+" .jp-title";this.cssSelector.playlist=this.cssSelector.cssSelectorAncestor+" .jp-playlist";
this.cssSelector.next=this.cssSelector.cssSelectorAncestor+" .jp-next";this.cssSelector.previous=this.cssSelector.cssSelectorAncestor+" .jp-previous";this.cssSelector.shuffle=this.cssSelector.cssSelectorAncestor+" .jp-shuffle";this.cssSelector.shuffleOff=this.cssSelector.cssSelectorAncestor+" .jp-shuffle-off";this.options.cssSelectorAncestor=this.cssSelector.cssSelectorAncestor;this.options.repeat=function(a){e.loop=a.jPlayer.options.loop};b(this.cssSelector.jPlayer).bind(b.jPlayer.event.ready,function(){e._init()});
b(this.cssSelector.jPlayer).bind(b.jPlayer.event.ended,function(){e.next()});b(this.cssSelector.jPlayer).bind(b.jPlayer.event.play,function(){b(this).jPlayer("pauseOthers")});b(this.cssSelector.jPlayer).bind(b.jPlayer.event.resize,function(a){a.jPlayer.options.fullScreen?b(e.cssSelector.title).show():b(e.cssSelector.title).hide()});b(this.cssSelector.previous).click(function(){e.previous();b(this).blur();return!1});b(this.cssSelector.next).click(function(){e.next();b(this).blur();return!1});b(this.cssSelector.shuffle).click(function(){e.shuffle(!0);
return!1});b(this.cssSelector.shuffleOff).click(function(){e.shuffle(!1);return!1}).hide();this.options.fullScreen||b(this.cssSelector.title).hide();b(this.cssSelector.playlist+" ul").empty();this._createItemHandlers();b(this.cssSelector.jPlayer).jPlayer(this.options)};jPlayerPlaylist.prototype={_cssSelector:{jPlayer:"#jquery_jplayer_1",cssSelectorAncestor:"#jp_container_1"},_options:{playlistOptions:{autoPlay:!1,loopOnPrevious:!1,shuffleOnLoop:!0,enableRemoveControls:!1,displayTime:"slow",addTime:"fast",
removeTime:"fast",shuffleTime:"slow",itemClass:"jp-playlist-item",freeGroupClass:"jp-free-media",freeItemClass:"jp-playlist-item-free",removeItemClass:"jp-playlist-item-remove"}},option:function(a,b){if(b===f)return this.options.playlistOptions[a];this.options.playlistOptions[a]=b;switch(a){case "enableRemoveControls":this._updateControls();break;case "itemClass":case "freeGroupClass":case "freeItemClass":case "removeItemClass":this._refresh(!0),this._createItemHandlers()}return this},_init:function(){var a=
this;this._refresh(function(){a.options.playlistOptions.autoPlay?a.play(a.current):a.select(a.current)})},_initPlaylist:function(a){this.current=0;this.removing=this.shuffled=!1;this.original=b.extend(!0,[],a);this._originalPlaylist()},_originalPlaylist:function(){var a=this;this.playlist=[];b.each(this.original,function(b){a.playlist[b]=a.original[b]})},_refresh:function(a){var c=this;if(a&&!b.isFunction(a))b(this.cssSelector.playlist+" ul").empty(),b.each(this.playlist,function(a){b(c.cssSelector.playlist+
" ul").append(c._createListItem(c.playlist[a]))}),this._updateControls();else{var d=b(this.cssSelector.playlist+" ul").children().length?this.options.playlistOptions.displayTime:0;b(this.cssSelector.playlist+" ul").slideUp(d,function(){var d=b(this);b(this).empty();b.each(c.playlist,function(a){d.append(c._createListItem(c.playlist[a]))});c._updateControls();b.isFunction(a)&&a();c.playlist.length?b(this).slideDown(c.options.playlistOptions.displayTime):b(this).show()})}},_createListItem:function(a){var c=
this,d="<li><div>",d=d+("<a href='javascript:;' class='"+this.options.playlistOptions.removeItemClass+"'>&times;</a>");if(a.free){var e=!0,d=d+("<span class='"+this.options.playlistOptions.freeGroupClass+"'>(");b.each(a,function(a,f){b.jPlayer.prototype.format[a]&&(e?e=!1:d+=" | ",d+="<a class='"+c.options.playlistOptions.freeItemClass+"' href='"+f+"' tabindex='1'>"+a+"</a>")});d+=")</span>"}d+="<a href='javascript:;' class='"+this.options.playlistOptions.itemClass+"' tabindex='1'>"+a.title+(a.artist?
" <span class='jp-artist'>by "+a.artist+"</span>":"")+"</a>";return d+="</div></li>"},_createItemHandlers:function(){var a=this;b(this.cssSelector.playlist).off("click","a."+this.options.playlistOptions.itemClass).on("click","a."+this.options.playlistOptions.itemClass,function(){var c=b(this).parent().parent().index();a.current!==c?a.play(c):b(a.cssSelector.jPlayer).jPlayer("play");b(this).blur();return!1});b(this.cssSelector.playlist).off("click","a."+this.options.playlistOptions.freeItemClass).on("click",
"a."+this.options.playlistOptions.freeItemClass,function(){b(this).parent().parent().find("."+a.options.playlistOptions.itemClass).click();b(this).blur();return!1});b(this.cssSelector.playlist).off("click","a."+this.options.playlistOptions.removeItemClass).on("click","a."+this.options.playlistOptions.removeItemClass,function(){var c=b(this).parent().parent().index();a.remove(c);b(this).blur();return!1})},_updateControls:function(){this.options.playlistOptions.enableRemoveControls?b(this.cssSelector.playlist+
" ."+this.options.playlistOptions.removeItemClass).show():b(this.cssSelector.playlist+" ."+this.options.playlistOptions.removeItemClass).hide();this.shuffled?(b(this.cssSelector.shuffleOff).show(),b(this.cssSelector.shuffle).hide()):(b(this.cssSelector.shuffleOff).hide(),b(this.cssSelector.shuffle).show())},_highlight:function(a){this.playlist.length&&a!==f&&(b(this.cssSelector.playlist+" .jp-playlist-current").removeClass("jp-playlist-current"),b(this.cssSelector.playlist+" li:nth-child("+(a+1)+
")").addClass("jp-playlist-current").find(".jp-playlist-item").addClass("jp-playlist-current"),b(this.cssSelector.title+" li").html(this.playlist[a].title+(this.playlist[a].artist?" <span class='jp-artist'>by "+this.playlist[a].artist+"</span>":"")))},setPlaylist:function(a){this._initPlaylist(a);this._init()},add:function(a,c){b(this.cssSelector.playlist+" ul").append(this._createListItem(a)).find("li:last-child").hide().slideDown(this.options.playlistOptions.addTime);this._updateControls();this.original.push(a);
this.playlist.push(a);c?this.play(this.playlist.length-1):1===this.original.length&&this.select(0)},remove:function(a){var c=this;if(a===f)return this._initPlaylist([]),this._refresh(function(){b(c.cssSelector.jPlayer).jPlayer("clearMedia")}),!0;if(this.removing)return!1;a=0>a?c.original.length+a:a;0<=a&&a<this.playlist.length&&(this.removing=!0,b(this.cssSelector.playlist+" li:nth-child("+(a+1)+")").slideUp(this.options.playlistOptions.removeTime,function(){b(this).remove();if(c.shuffled){var d=
c.playlist[a];b.each(c.original,function(a){if(c.original[a]===d)return c.original.splice(a,1),!1})}else c.original.splice(a,1);c.playlist.splice(a,1);c.original.length?a===c.current?(c.current=a<c.original.length?c.current:c.original.length-1,c.select(c.current)):a<c.current&&c.current--:(b(c.cssSelector.jPlayer).jPlayer("clearMedia"),c.current=0,c.shuffled=!1,c._updateControls());c.removing=!1}));return!0},select:function(a){a=0>a?this.original.length+a:a;0<=a&&a<this.playlist.length?(this.current=
a,this._highlight(a),b(this.cssSelector.jPlayer).jPlayer("setMedia",this.playlist[this.current])):this.current=0},play:function(a){a=0>a?this.original.length+a:a;0<=a&&a<this.playlist.length?this.playlist.length&&(this.select(a),b(this.cssSelector.jPlayer).jPlayer("play")):a===f&&b(this.cssSelector.jPlayer).jPlayer("play")},pause:function(){b(this.cssSelector.jPlayer).jPlayer("pause")},next:function(){var a=this.current+1<this.playlist.length?this.current+1:0;this.loop?0===a&&this.shuffled&&this.options.playlistOptions.shuffleOnLoop&&
1<this.playlist.length?this.shuffle(!0,!0):this.play(a):0<a&&this.play(a)},previous:function(){var a=0<=this.current-1?this.current-1:this.playlist.length-1;(this.loop&&this.options.playlistOptions.loopOnPrevious||a<this.playlist.length-1)&&this.play(a)},shuffle:function(a,c){var d=this;a===f&&(a=!this.shuffled);(a||a!==this.shuffled)&&b(this.cssSelector.playlist+" ul").slideUp(this.options.playlistOptions.shuffleTime,function(){(d.shuffled=a)?d.playlist.sort(function(){return 0.5-Math.random()}):
d._originalPlaylist();d._refresh(!0);c||!b(d.cssSelector.jPlayer).data("jPlayer").status.paused?d.play(0):d.select(0);b(this).slideDown(d.options.playlistOptions.shuffleTime)})}}})(jQuery);
!function(e,t,n){"use strict";!function o(e,t,n){function a(s,l){if(!t[s]){if(!e[s]){var i="function"==typeof require&&require;if(!l&&i)return i(s,!0);if(r)return r(s,!0);var u=new Error("Cannot find module '"+s+"'");throw u.code="MODULE_NOT_FOUND",u}var c=t[s]={exports:{}};e[s][0].call(c.exports,function(t){var n=e[s][1][t];return a(n?n:t)},c,c.exports,o,e,t,n)}return t[s].exports}for(var r="function"==typeof require&&require,s=0;s<n.length;s++)a(n[s]);return a}({1:[function(o,a,r){function s(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(r,"__esModule",{value:!0});var l,i,u,c,d=o("./modules/handle-dom"),f=o("./modules/utils"),p=o("./modules/handle-swal-dom"),m=o("./modules/handle-click"),v=o("./modules/handle-key"),y=s(v),b=o("./modules/default-params"),h=s(b),g=o("./modules/set-params"),w=s(g);r["default"]=u=c=function(){function o(e){var t=a;return t[e]===n?h["default"][e]:t[e]}var a=arguments[0];if((0,d.addClass)(t.body,"stop-scrolling"),(0,p.resetInput)(),a===n)return(0,f.logStr)("SweetAlert expects at least 1 attribute!"),!1;var r=(0,f.extend)({},h["default"]);switch(typeof a){case"string":r.title=a,r.text=arguments[1]||"",r.type=arguments[2]||"";break;case"object":if(a.title===n)return(0,f.logStr)('Missing "title" argument!'),!1;r.title=a.title;for(var s in h["default"])r[s]=o(s);r.confirmButtonText=r.showCancelButton?"Confirm":h["default"].confirmButtonText,r.confirmButtonText=o("confirmButtonText"),r.doneFunction=arguments[1]||null;break;default:return(0,f.logStr)('Unexpected type of argument! Expected "string" or "object", got '+typeof a),!1}(0,w["default"])(r),(0,p.fixVerticalPosition)(),(0,p.openModal)(arguments[1]);for(var u=(0,p.getModal)(),v=u.querySelectorAll("button"),b=["onclick","onmouseover","onmouseout","onmousedown","onmouseup","onfocus"],g=function(e){return(0,m.handleButton)(e,r,u)},C=0;C<v.length;C++)for(var S=0;S<b.length;S++){var x=b[S];v[C][x]=g}(0,p.getOverlay)().onclick=g,l=e.onkeydown;var k=function(e){return(0,y["default"])(e,r,u)};e.onkeydown=k,e.onfocus=function(){setTimeout(function(){i!==n&&(i.focus(),i=n)},0)},c.enableButtons()},u.setDefaults=c.setDefaults=function(e){if(!e)throw new Error("userParams is required");if("object"!=typeof e)throw new Error("userParams has to be a object");(0,f.extend)(h["default"],e)},u.close=c.close=function(){var o=(0,p.getModal)();(0,d.fadeOut)((0,p.getOverlay)(),5),(0,d.fadeOut)(o,5),(0,d.removeClass)(o,"showSweetAlert"),(0,d.addClass)(o,"hideSweetAlert"),(0,d.removeClass)(o,"visible");var a=o.querySelector(".sa-icon.sa-success");(0,d.removeClass)(a,"animate"),(0,d.removeClass)(a.querySelector(".sa-tip"),"animateSuccessTip"),(0,d.removeClass)(a.querySelector(".sa-long"),"animateSuccessLong");var r=o.querySelector(".sa-icon.sa-error");(0,d.removeClass)(r,"animateErrorIcon"),(0,d.removeClass)(r.querySelector(".sa-x-mark"),"animateXMark");var s=o.querySelector(".sa-icon.sa-warning");return(0,d.removeClass)(s,"pulseWarning"),(0,d.removeClass)(s.querySelector(".sa-body"),"pulseWarningIns"),(0,d.removeClass)(s.querySelector(".sa-dot"),"pulseWarningIns"),setTimeout(function(){var e=o.getAttribute("data-custom-class");(0,d.removeClass)(o,e)},300),(0,d.removeClass)(t.body,"stop-scrolling"),e.onkeydown=l,e.previousActiveElement&&e.previousActiveElement.focus(),i=n,clearTimeout(o.timeout),!0},u.showInputError=c.showInputError=function(e){var t=(0,p.getModal)(),n=t.querySelector(".sa-input-error");(0,d.addClass)(n,"show");var o=t.querySelector(".sa-error-container");(0,d.addClass)(o,"show"),o.querySelector("p").innerHTML=e,setTimeout(function(){u.enableButtons()},1),t.querySelector("input").focus()},u.resetInputError=c.resetInputError=function(e){if(e&&13===e.keyCode)return!1;var t=(0,p.getModal)(),n=t.querySelector(".sa-input-error");(0,d.removeClass)(n,"show");var o=t.querySelector(".sa-error-container");(0,d.removeClass)(o,"show")},u.disableButtons=c.disableButtons=function(e){var t=(0,p.getModal)(),n=t.querySelector("button.confirm"),o=t.querySelector("button.cancel");n.disabled=!0,o.disabled=!0},u.enableButtons=c.enableButtons=function(e){var t=(0,p.getModal)(),n=t.querySelector("button.confirm"),o=t.querySelector("button.cancel");n.disabled=!1,o.disabled=!1},"undefined"!=typeof e?e.sweetAlert=e.swal=u:(0,f.logStr)("SweetAlert is a frontend module!"),a.exports=r["default"]},{"./modules/default-params":2,"./modules/handle-click":3,"./modules/handle-dom":4,"./modules/handle-key":5,"./modules/handle-swal-dom":6,"./modules/set-params":8,"./modules/utils":9}],2:[function(e,t,n){Object.defineProperty(n,"__esModule",{value:!0});var o={title:"",text:"",type:null,allowOutsideClick:!1,showConfirmButton:!0,showCancelButton:!1,closeOnConfirm:!0,closeOnCancel:!0,confirmButtonText:"OK",confirmButtonColor:"",cancelButtonText:"Cancel",imageUrl:null,imageSize:null,timer:null,customClass:"",html:!1,animation:!0,allowEscapeKey:!0,inputType:"text",inputPlaceholder:"",inputValue:"",showLoaderOnConfirm:!1};n["default"]=o,t.exports=n["default"]},{}],3:[function(t,n,o){Object.defineProperty(o,"__esModule",{value:!0});var a=t("./utils"),r=(t("./handle-swal-dom"),t("./handle-dom")),s=function(t,n,o){function s(e){m&&n.confirmButtonColor&&(p.style.backgroundColor=e)}var u,c,d,f=t||e.event,p=f.target||f.srcElement,m=-1!==p.className.indexOf("confirm"),v=-1!==p.className.indexOf("sweet-overlay"),y=(0,r.hasClass)(o,"visible"),b=n.doneFunction&&"true"===o.getAttribute("data-has-done-function");switch(m&&n.confirmButtonColor&&(u=n.confirmButtonColor,c=(0,a.colorLuminance)(u,-.04),d=(0,a.colorLuminance)(u,-.14)),f.type){case"mouseover":s(c);break;case"mouseout":s(u);break;case"mousedown":s(d);break;case"mouseup":s(c);break;case"focus":var h=o.querySelector("button.confirm"),g=o.querySelector("button.cancel");m?g.style.boxShadow="none":h.style.boxShadow="none";break;case"click":var w=o===p,C=(0,r.isDescendant)(o,p);if(!w&&!C&&y&&!n.allowOutsideClick)break;m&&b&&y?l(o,n):b&&y||v?i(o,n):(0,r.isDescendant)(o,p)&&"BUTTON"===p.tagName&&sweetAlert.close()}},l=function(e,t){var n=!0;(0,r.hasClass)(e,"show-input")&&(n=e.querySelector("input").value,n||(n="")),t.doneFunction(n),t.closeOnConfirm&&sweetAlert.close(),t.showLoaderOnConfirm&&sweetAlert.disableButtons()},i=function(e,t){var n=String(t.doneFunction).replace(/\s/g,""),o="function("===n.substring(0,9)&&")"!==n.substring(9,10);o&&t.doneFunction(!1),t.closeOnCancel&&sweetAlert.close()};o["default"]={handleButton:s,handleConfirm:l,handleCancel:i},n.exports=o["default"]},{"./handle-dom":4,"./handle-swal-dom":6,"./utils":9}],4:[function(n,o,a){Object.defineProperty(a,"__esModule",{value:!0});var r=function(e,t){return new RegExp(" "+t+" ").test(" "+e.className+" ")},s=function(e,t){r(e,t)||(e.className+=" "+t)},l=function(e,t){var n=" "+e.className.replace(/[\t\r\n]/g," ")+" ";if(r(e,t)){for(;n.indexOf(" "+t+" ")>=0;)n=n.replace(" "+t+" "," ");e.className=n.replace(/^\s+|\s+$/g,"")}},i=function(e){var n=t.createElement("div");return n.appendChild(t.createTextNode(e)),n.innerHTML},u=function(e){e.style.opacity="",e.style.display="block"},c=function(e){if(e&&!e.length)return u(e);for(var t=0;t<e.length;++t)u(e[t])},d=function(e){e.style.opacity="",e.style.display="none"},f=function(e){if(e&&!e.length)return d(e);for(var t=0;t<e.length;++t)d(e[t])},p=function(e,t){for(var n=t.parentNode;null!==n;){if(n===e)return!0;n=n.parentNode}return!1},m=function(e){e.style.left="-9999px",e.style.display="block";var t,n=e.clientHeight;var w=($('body').width()/2);return t="undefined"!=typeof getComputedStyle?parseInt(getComputedStyle(e).getPropertyValue("padding-top"),10):parseInt(e.currentStyle.padding),e.style.left=w+"px",e.style.display="none","-"+parseInt((n+t)/2)+"px"},v=function(e,t){if(+e.style.opacity<1){t=t||16,e.style.opacity=0,e.style.display="block";var n=+new Date,o=function a(){e.style.opacity=+e.style.opacity+(new Date-n)/10,n=+new Date,+e.style.opacity<1&&setTimeout(a,t)};o()}e.style.display="block"},y=function(e,t){t=t||16,e.style.opacity=1;var n=+new Date,o=function a(){e.style.opacity=+e.style.opacity-(new Date-n)/10,n=+new Date,+e.style.opacity>0?setTimeout(a,t):e.style.display="none"};o()},b=function(n){if("function"==typeof MouseEvent){var o=new MouseEvent("click",{view:e,bubbles:!1,cancelable:!0});n.dispatchEvent(o)}else if(t.createEvent){var a=t.createEvent("MouseEvents");a.initEvent("click",!1,!1),n.dispatchEvent(a)}else t.createEventObject?n.fireEvent("onclick"):"function"==typeof n.onclick&&n.onclick()},h=function(t){"function"==typeof t.stopPropagation?(t.stopPropagation(),t.preventDefault()):e.event&&e.event.hasOwnProperty("cancelBubble")&&(e.event.cancelBubble=!0)};a.hasClass=r,a.addClass=s,a.removeClass=l,a.escapeHtml=i,a._show=u,a.show=c,a._hide=d,a.hide=f,a.isDescendant=p,a.getTopMargin=m,a.fadeIn=v,a.fadeOut=y,a.fireClick=b,a.stopEventPropagation=h},{}],5:[function(t,o,a){Object.defineProperty(a,"__esModule",{value:!0});var r=t("./handle-dom"),s=t("./handle-swal-dom"),l=function(t,o,a){var l=t||e.event,i=l.keyCode||l.which,u=a.querySelector("button.confirm"),c=a.querySelector("button.cancel"),d=a.querySelectorAll("button[tabindex]");if(-1!==[9,13,32,27].indexOf(i)){for(var f=l.target||l.srcElement,p=-1,m=0;m<d.length;m++)if(f===d[m]){p=m;break}9===i?(f=-1===p?u:p===d.length-1?d[0]:d[p+1],(0,r.stopEventPropagation)(l),f.focus(),o.confirmButtonColor&&(0,s.setFocusStyle)(f,o.confirmButtonColor)):13===i?("INPUT"===f.tagName&&(f=u,u.focus()),f=-1===p?u:n):27===i&&o.allowEscapeKey===!0?(f=c,(0,r.fireClick)(f,l)):f=n}};a["default"]=l,o.exports=a["default"]},{"./handle-dom":4,"./handle-swal-dom":6}],6:[function(n,o,a){function r(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(a,"__esModule",{value:!0});var s=n("./utils"),l=n("./handle-dom"),i=n("./default-params"),u=r(i),c=n("./injected-html"),d=r(c),f=".sweet-alert",p=".sweet-overlay",m=function(){var e=t.createElement("div");for(e.innerHTML=d["default"];e.firstChild;)t.body.appendChild(e.firstChild)},v=function x(){var e=t.querySelector(f);return e||(m(),e=x()),e},y=function(){var e=v();return e?e.querySelector("input"):void 0},b=function(){return t.querySelector(p)},h=function(e,t){var n=(0,s.hexToRgb)(t);e.style.boxShadow="0 0 2px rgba("+n+", 0.8), inset 0 0 0 1px rgba(0, 0, 0, 0.05)"},g=function(n){var o=v();(0,l.fadeIn)(b(),10),(0,l.show)(o),(0,l.addClass)(o,"showSweetAlert"),(0,l.removeClass)(o,"hideSweetAlert"),e.previousActiveElement=t.activeElement;var a=o.querySelector("button.confirm");a.focus(),setTimeout(function(){(0,l.addClass)(o,"visible")},500);var r=o.getAttribute("data-timer");if("null"!==r&&""!==r){var s=n;o.timeout=setTimeout(function(){var e=(s||null)&&"true"===o.getAttribute("data-has-done-function");e?s(null):sweetAlert.close()},r)}},w=function(){var e=v(),t=y();(0,l.removeClass)(e,"show-input"),t.value=u["default"].inputValue,t.setAttribute("type",u["default"].inputType),t.setAttribute("placeholder",u["default"].inputPlaceholder),C()},C=function(e){if(e&&13===e.keyCode)return!1;var t=v(),n=t.querySelector(".sa-input-error");(0,l.removeClass)(n,"show");var o=t.querySelector(".sa-error-container");(0,l.removeClass)(o,"show")},S=function(){var e=v();e.style.marginTop=(0,l.getTopMargin)(v())};a.sweetAlertInitialize=m,a.getModal=v,a.getOverlay=b,a.getInput=y,a.setFocusStyle=h,a.openModal=g,a.resetInput=w,a.resetInputError=C,a.fixVerticalPosition=S},{"./default-params":2,"./handle-dom":4,"./injected-html":7,"./utils":9}],7:[function(e,t,n){Object.defineProperty(n,"__esModule",{value:!0});var o='<div class="sweet-overlay" tabIndex="-1"></div><div class="sweet-alert"><div class="sa-icon sa-error">\n      <span class="sa-x-mark">\n        <span class="sa-line sa-left"></span>\n        <span class="sa-line sa-right"></span>\n      </span>\n    </div><div class="sa-icon sa-warning">\n      <span class="sa-body"></span>\n      <span class="sa-dot"></span>\n    </div><div class="sa-icon sa-info"></div><div class="sa-icon sa-success">\n      <span class="sa-line sa-tip"></span>\n      <span class="sa-line sa-long"></span>\n\n      <div class="sa-placeholder"></div>\n      <div class="sa-fix"></div>\n    </div><div class="sa-icon sa-custom"></div><h2>Title</h2>\n    <p>Text</p>\n    <fieldset>\n      <input type="text" tabIndex="3" />\n      <div class="sa-input-error"></div>\n    </fieldset><div class="sa-error-container">\n      <div class="icon">!</div>\n      <p>Not valid!</p>\n    </div><div class="sa-button-container">\n      <button class="cancel form_button" tabIndex="2">Cancel</button>\n      <div class="sa-confirm-button-container">\n        <button class="confirm form_button" tabIndex="1">OK</button><div class="la-ball-fall">\n          <div></div>\n          <div></div>\n          <div></div>\n        </div>\n      </div>\n    </div></div>';n["default"]=o,t.exports=n["default"]},{}],8:[function(e,t,o){Object.defineProperty(o,"__esModule",{value:!0});var a=e("./utils"),r=e("./handle-swal-dom"),s=e("./handle-dom"),l=["error","warning","info","success","input","prompt"],i=function(e){var t=(0,r.getModal)(),o=t.querySelector("h2"),i=t.querySelector("p"),u=t.querySelector("button.cancel"),c=t.querySelector("button.confirm");if(o.innerHTML=e.title,i.innerHTML=e.html?e.text:(0,s.escapeHtml)(e.text||"").split("\n").join("<br>"),e.text&&(0,s.show)(i),e.customClass)(0,s.addClass)(t,e.customClass),t.setAttribute("data-custom-class",e.customClass);else{var d=t.getAttribute("data-custom-class");(0,s.removeClass)(t,d),t.setAttribute("data-custom-class","")}if((0,s.hide)(t.querySelectorAll(".sa-icon")),e.type&&!(0,a.isIE8)()){var f=function(){for(var o=!1,a=0;a<l.length;a++)if(e.type===l[a]){o=!0;break}if(!o)return logStr("Unknown alert type: "+e.type),{v:!1};var i=["success","error","warning","info"],u=n;-1!==i.indexOf(e.type)&&(u=t.querySelector(".sa-icon.sa-"+e.type),(0,s.show)(u));var c=(0,r.getInput)();switch(e.type){case"success":(0,s.addClass)(u,"animate"),(0,s.addClass)(u.querySelector(".sa-tip"),"animateSuccessTip"),(0,s.addClass)(u.querySelector(".sa-long"),"animateSuccessLong");break;case"error":(0,s.addClass)(u,"animateErrorIcon"),(0,s.addClass)(u.querySelector(".sa-x-mark"),"animateXMark");break;case"input":case"prompt":c.setAttribute("type",e.inputType),c.value=e.inputValue,c.setAttribute("placeholder",e.inputPlaceholder),(0,s.addClass)(t,"show-input"),setTimeout(function(){c.focus(),c.addEventListener("keyup",swal.resetInputError)},400)}}();if("object"==typeof f)return f.v}if(e.imageUrl){var p=t.querySelector(".sa-icon.sa-custom");p.style.backgroundImage="url("+e.imageUrl+")",(0,s.show)(p);var m=80,v=80;if(e.imageSize){var y=e.imageSize.toString().split("x"),b=y[0],h=y[1];b&&h?(m=b,v=h):logStr("Parameter imageSize expects value with format WIDTHxHEIGHT, got "+e.imageSize)}p.setAttribute("style",p.getAttribute("style")+"width:"+m+"px; height:"+v+"px")}t.setAttribute("data-has-cancel-button",e.showCancelButton),e.showCancelButton?u.style.display="inline-block":(0,s.hide)(u),t.setAttribute("data-has-confirm-button",e.showConfirmButton),e.showConfirmButton?c.style.display="inline-block":(0,s.hide)(c),e.cancelButtonText&&(u.innerHTML=(0,s.escapeHtml)(e.cancelButtonText)),e.confirmButtonText&&(c.innerHTML=(0,s.escapeHtml)(e.confirmButtonText)),e.confirmButtonColor&&(c.style.backgroundColor=e.confirmButtonColor,c.style.borderLeftColor=e.confirmLoadingButtonColor,c.style.borderRightColor=e.confirmLoadingButtonColor,(0,r.setFocusStyle)(c,e.confirmButtonColor)),t.setAttribute("data-allow-outside-click",e.allowOutsideClick);var g=!!e.doneFunction;t.setAttribute("data-has-done-function",g),e.animation?"string"==typeof e.animation?t.setAttribute("data-animation",e.animation):t.setAttribute("data-animation","pop"):t.setAttribute("data-animation","none"),t.setAttribute("data-timer",e.timer)};o["default"]=i,t.exports=o["default"]},{"./handle-dom":4,"./handle-swal-dom":6,"./utils":9}],9:[function(t,n,o){Object.defineProperty(o,"__esModule",{value:!0});var a=function(e,t){for(var n in t)t.hasOwnProperty(n)&&(e[n]=t[n]);return e},r=function(e){var t=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e);return t?parseInt(t[1],16)+", "+parseInt(t[2],16)+", "+parseInt(t[3],16):null},s=function(){return e.attachEvent&&!e.addEventListener},l=function(t){"undefined"!=typeof e&&e.console&&e.console.log("SweetAlert: "+t)},i=function(e,t){e=String(e).replace(/[^0-9a-f]/gi,""),e.length<6&&(e=e[0]+e[0]+e[1]+e[1]+e[2]+e[2]),t=t||0;var n,o,a="#";for(o=0;3>o;o++)n=parseInt(e.substr(2*o,2),16),n=Math.round(Math.min(Math.max(0,n+n*t),255)).toString(16),a+=("00"+n).substr(n.length);return a};o.extend=a,o.hexToRgb=r,o.isIE8=s,o.logStr=l,o.colorLuminance=i},{}]},{},[1]),"function"==typeof define&&define.amd?define(function(){return sweetAlert}):"undefined"!=typeof module&&module.exports&&(module.exports=sweetAlert)}(window,document);
(function(a){var b,c=a();a.fn.sortable=function(d){var e=String(d);return d=a.extend({connectWith:!1},d),this.each(function(){if(e=="reload"){a(this).children(d.items).off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');}if(/^enable|disable|destroy$/.test(e)){var f=a(this).children(a(this).data("items")).attr("draggable",e=="enable");e=="destroy"&&f.add(this).removeData("connectWith items").off("dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s");return}var g,h,f=a(this).children(d.items),i=a("<"+(/^ul|ol$/i.test(this.tagName)?"li":"div")+' class="sortable-placeholder">');f.find(d.handle).mousedown(function(){g=!0}).mouseup(function(){g=!1}),a(this).data("items",d.items),c=c.add(i),d.connectWith&&a(d.connectWith).add(this).data("connectWith",d.connectWith),f.attr("draggable","true").on("dragstart.h5s",function(c){if(d.handle&&!g)return!1;g=!1;var e=c.originalEvent.dataTransfer;e.effectAllowed="move",e.setData("Text","dummy"),h=(b=a(this)).addClass("sortable-dragging").trigger("sortstart", {item: b});}).on("dragend.h5s",function(){b.removeClass("sortable-dragging").show(),c.detach(),h!=b.index()&&f.parent().trigger("sortupdate",{item:b}),b=null}).not("a[href], img").on("selectstart.h5s",function(){return this.dragDrop&&this.dragDrop(),!1}).end().add([this,i]).on("dragover.h5s dragenter.h5s drop.h5s",function(e){return!f.is(b)&&d.connectWith!==a(b).parent().data("connectWith")?!0:e.type=="drop"?(e.stopPropagation(),c.filter(":visible").after(b),!1):(e.preventDefault(),e.originalEvent.dataTransfer.dropEffect="move",f.is(this)?(d.forcePlaceholderSize&&i.height(b.outerHeight()),b.hide(),a(this)[i.index()<a(this).index()?"after":"before"](i),c.not(i).detach()):!c.is(this)&&!a(this).children(d.items).length&&(c.detach(),a(this).append(i)),!1)})})}})(jQuery);

(function(){function i(c,a,b){return g(c,a,b)}function g(c,a,b,j){j=j||{};a&&!n(a)&&(b=a,a=void 0);a=a||new Date;b=b||o;b.formats=b.formats||{};var i=a.getTime(),h=j.timezone,e=typeof h;if(j.utc||e=="number"||e=="string")a=p(a);if(h){if(e=="string")var k=h[0]=="-"?-1:1,q=parseInt(h.slice(1,3),10),r=parseInt(h.slice(3,5),10),h=k*60*q+r;e&&(a=new Date(a.getTime()+h*6E4))}return c.replace(/%([-_0]?.)/g,function(c,e){var d;if(e.length==2){d=e[0];if(d=="-")d="";else if(d=="_")d=" ";else if(d=="0")d="0";
else return c;e=e[1]}switch(e){case "A":return b.days[a.getDay()];case "a":return b.shortDays[a.getDay()];case "B":return b.months[a.getMonth()];case "b":return b.shortMonths[a.getMonth()];case "C":return f(Math.floor(a.getFullYear()/100),d);case "D":return g(b.formats.D||"%m/%d/%y",a,b);case "d":return f(a.getDate(),d);case "e":return a.getDate();case "F":return g(b.formats.F||"%Y-%m-%d",a,b);case "H":return f(a.getHours(),d);case "h":return b.shortMonths[a.getMonth()];case "I":return f(l(a),d);
case "j":return d=new Date(a.getFullYear(),0,1),d=Math.ceil((a.getTime()-d.getTime())/864E5),f(d,3);case "k":return f(a.getHours(),d==null?" ":d);case "L":return f(Math.floor(i%1E3),3);case "l":return f(l(a),d==null?" ":d);case "M":return f(a.getMinutes(),d);case "m":return f(a.getMonth()+1,d);case "n":return"\n";case "o":return String(a.getDate())+s(a.getDate());case "P":return a.getHours()<12?b.am:b.pm;case "p":return a.getHours()<12?b.AM:b.PM;case "R":return g(b.formats.R||"%H:%M",a,b);case "r":return g(b.formats.r||
"%I:%M:%S %p",a,b);case "S":return f(a.getSeconds(),d);case "s":return Math.floor(i/1E3);case "T":return g(b.formats.T||"%H:%M:%S",a,b);case "t":return"\t";case "U":return f(m(a,"sunday"),d);case "u":return d=a.getDay(),d==0?7:d;case "v":return g(b.formats.v||"%e-%b-%Y",a,b);case "W":return f(m(a,"monday"),d);case "w":return a.getDay();case "Y":return a.getFullYear();case "y":return d=String(a.getFullYear()),d.slice(d.length-2);case "Z":return j.utc?"GMT":(d=a.toString().match(/\((\w+)\)/))&&d[1]||
"";case "z":return j.utc?"+0000":(d=typeof h=="number"?h:-a.getTimezoneOffset(),(d<0?"-":"+")+f(Math.abs(d/60))+f(d%60));default:return e}})}function p(c){var a=(c.getTimezoneOffset()||0)*6E4;return new Date(c.getTime()+a)}function n(c){for(var a=0,b=k.length,a=0;a<b;++a)if(typeof c[k[a]]!="function")return!1;return!0}function f(c,a,b){typeof a==="number"&&(b=a,a="0");a==null&&(a="0");b=b||2;c=String(c);if(a)for(;c.length<b;)c=a+c;return c}function l(c){c=c.getHours();c==0?c=12:c>12&&(c-=12);return c}
function s(c){var a=c%10;c%=100;if(c>=11&&c<=13||a===0||a>=4)return"th";switch(a){case 1:return"st";case 2:return"nd";case 3:return"rd"}}function m(c,a){var a=a||"sunday",b=c.getDay();a=="monday"&&(b==0?b=6:b--);var e=new Date(c.getFullYear(),0,1);return Math.floor(((c-e)/864E5+7-b)/7)}var e;e=typeof module!=="undefined"?module.exports=i:function(){return this||(0,eval)("this")}();var o={days:"Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" "),shortDays:"Sun Mon Tue Wed Thu Fri Sat".split(" "),
months:"January February March April May June July August September October November December".split(" "),shortMonths:"Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(" "),AM:"AM",PM:"PM",am:"am",pm:"pm"};e.strftime=i;e.strftimeTZ=i.strftimeTZ=function(c,a,b,e){if((typeof b=="number"||typeof b=="string")&&e==null)e=b,b=void 0;return g(c,a,b,{timezone:e})};e.strftimeUTC=i.strftimeUTC=function(c,a,b){return g(c,a,b,{utc:!0})};e.localizedStrftime=i.localizedStrftime=function(c){return function(a,
b){return g(a,b,c)}};var k=["getTime","getTimezoneOffset","getDay","getDate","getMonth","getFullYear","getYear","getHours","getMinutes","getSeconds"]})();

/* Javascript plotting library for jQuery, version 0.8.3.

Copyright (c) 2007-2014 IOLA and Ole Laursen.
Licensed under the MIT license.

*/
(function($){$.color={};$.color.make=function(r,g,b,a){var o={};o.r=r||0;o.g=g||0;o.b=b||0;o.a=a!=null?a:1;o.add=function(c,d){for(var i=0;i<c.length;++i)o[c.charAt(i)]+=d;return o.normalize()};o.scale=function(c,f){for(var i=0;i<c.length;++i)o[c.charAt(i)]*=f;return o.normalize()};o.toString=function(){if(o.a>=1){return"rgb("+[o.r,o.g,o.b].join(",")+")"}else{return"rgba("+[o.r,o.g,o.b,o.a].join(",")+")"}};o.normalize=function(){function clamp(min,value,max){return value<min?min:value>max?max:value}o.r=clamp(0,parseInt(o.r),255);o.g=clamp(0,parseInt(o.g),255);o.b=clamp(0,parseInt(o.b),255);o.a=clamp(0,o.a,1);return o};o.clone=function(){return $.color.make(o.r,o.b,o.g,o.a)};return o.normalize()};$.color.extract=function(elem,css){var c;do{c=elem.css(css).toLowerCase();if(c!=""&&c!="transparent")break;elem=elem.parent()}while(elem.length&&!$.nodeName(elem.get(0),"body"));if(c=="rgba(0, 0, 0, 0)")c="transparent";return $.color.parse(c)};$.color.parse=function(str){var res,m=$.color.make;if(res=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(str))return m(parseInt(res[1],10),parseInt(res[2],10),parseInt(res[3],10));if(res=/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(str))return m(parseInt(res[1],10),parseInt(res[2],10),parseInt(res[3],10),parseFloat(res[4]));if(res=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(str))return m(parseFloat(res[1])*2.55,parseFloat(res[2])*2.55,parseFloat(res[3])*2.55);if(res=/rgba\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(str))return m(parseFloat(res[1])*2.55,parseFloat(res[2])*2.55,parseFloat(res[3])*2.55,parseFloat(res[4]));if(res=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(str))return m(parseInt(res[1],16),parseInt(res[2],16),parseInt(res[3],16));if(res=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(str))return m(parseInt(res[1]+res[1],16),parseInt(res[2]+res[2],16),parseInt(res[3]+res[3],16));var name=$.trim(str).toLowerCase();if(name=="transparent")return m(255,255,255,0);else{res=lookupColors[name]||[0,0,0];return m(res[0],res[1],res[2])}};var lookupColors={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]}})(jQuery);(function($){var hasOwnProperty=Object.prototype.hasOwnProperty;if(!$.fn.detach){$.fn.detach=function(){return this.each(function(){if(this.parentNode){this.parentNode.removeChild(this)}})}}function Canvas(cls,container){var element=container.children("."+cls)[0];if(element==null){element=document.createElement("canvas");element.className=cls;$(element).css({direction:"ltr",position:"absolute",left:0,top:0}).appendTo(container);if(!element.getContext){if(window.G_vmlCanvasManager){element=window.G_vmlCanvasManager.initElement(element)}else{throw new Error("Canvas is not available. If you're using IE with a fall-back such as Excanvas, then there's either a mistake in your conditional include, or the page has no DOCTYPE and is rendering in Quirks Mode.")}}}this.element=element;var context=this.context=element.getContext("2d");var devicePixelRatio=window.devicePixelRatio||1,backingStoreRatio=context.webkitBackingStorePixelRatio||context.mozBackingStorePixelRatio||context.msBackingStorePixelRatio||context.oBackingStorePixelRatio||context.backingStorePixelRatio||1;this.pixelRatio=devicePixelRatio/backingStoreRatio;this.resize(container.width(),container.height());this.textContainer=null;this.text={};this._textCache={}}Canvas.prototype.resize=function(width,height){if(width<=0||height<=0){throw new Error("Invalid dimensions for plot, width = "+width+", height = "+height)}var element=this.element,context=this.context,pixelRatio=this.pixelRatio;if(this.width!=width){element.width=width*pixelRatio;element.style.width=width+"px";this.width=width}if(this.height!=height){element.height=height*pixelRatio;element.style.height=height+"px";this.height=height}context.restore();context.save();context.scale(pixelRatio,pixelRatio)};Canvas.prototype.clear=function(){this.context.clearRect(0,0,this.width,this.height)};Canvas.prototype.render=function(){var cache=this._textCache;for(var layerKey in cache){if(hasOwnProperty.call(cache,layerKey)){var layer=this.getTextLayer(layerKey),layerCache=cache[layerKey];layer.hide();for(var styleKey in layerCache){if(hasOwnProperty.call(layerCache,styleKey)){var styleCache=layerCache[styleKey];for(var key in styleCache){if(hasOwnProperty.call(styleCache,key)){var positions=styleCache[key].positions;for(var i=0,position;position=positions[i];i++){if(position.active){if(!position.rendered){layer.append(position.element);position.rendered=true}}else{positions.splice(i--,1);if(position.rendered){position.element.detach()}}}if(positions.length==0){delete styleCache[key]}}}}}layer.show()}}};Canvas.prototype.getTextLayer=function(classes){var layer=this.text[classes];if(layer==null){if(this.textContainer==null){this.textContainer=$("<div class='flot-text'></div>").css({position:"absolute",top:0,left:0,bottom:0,right:0,"font-size":"smaller",color:"#545454"}).insertAfter(this.element)}layer=this.text[classes]=$("<div></div>").addClass(classes).css({position:"absolute",top:0,left:0,bottom:0,right:0}).appendTo(this.textContainer)}return layer};Canvas.prototype.getTextInfo=function(layer,text,font,angle,width){var textStyle,layerCache,styleCache,info;text=""+text;if(typeof font==="object"){textStyle=font.style+" "+font.variant+" "+font.weight+" "+font.size+"px/"+font.lineHeight+"px "+font.family}else{textStyle=font}layerCache=this._textCache[layer];if(layerCache==null){layerCache=this._textCache[layer]={}}styleCache=layerCache[textStyle];if(styleCache==null){styleCache=layerCache[textStyle]={}}info=styleCache[text];if(info==null){var element=$("<div></div>").html(text).css({position:"absolute","max-width":width,top:-9999}).appendTo(this.getTextLayer(layer));if(typeof font==="object"){element.css({font:textStyle,color:font.color})}else if(typeof font==="string"){element.addClass(font)}info=styleCache[text]={width:element.outerWidth(true),height:element.outerHeight(true),element:element,positions:[]};element.detach()}return info};Canvas.prototype.addText=function(layer,x,y,text,font,angle,width,halign,valign){var info=this.getTextInfo(layer,text,font,angle,width),positions=info.positions;if(halign=="center"){x-=info.width/2}else if(halign=="right"){x-=info.width}if(valign=="middle"){y-=info.height/2}else if(valign=="bottom"){y-=info.height}for(var i=0,position;position=positions[i];i++){if(position.x==x&&position.y==y){position.active=true;return}}position={active:true,rendered:false,element:positions.length?info.element.clone():info.element,x:x,y:y};positions.push(position);position.element.css({top:Math.round(y),left:Math.round(x),"text-align":halign})};Canvas.prototype.removeText=function(layer,x,y,text,font,angle){if(text==null){var layerCache=this._textCache[layer];if(layerCache!=null){for(var styleKey in layerCache){if(hasOwnProperty.call(layerCache,styleKey)){var styleCache=layerCache[styleKey];for(var key in styleCache){if(hasOwnProperty.call(styleCache,key)){var positions=styleCache[key].positions;for(var i=0,position;position=positions[i];i++){position.active=false}}}}}}}else{var positions=this.getTextInfo(layer,text,font,angle).positions;for(var i=0,position;position=positions[i];i++){if(position.x==x&&position.y==y){position.active=false}}}};function Plot(placeholder,data_,options_,plugins){var series=[],options={colors:["#edc240","#afd8f8","#cb4b4b","#4da74d","#9440ed"],legend:{show:true,noColumns:1,labelFormatter:null,labelBoxBorderColor:"#ccc",container:null,position:"ne",margin:5,backgroundColor:null,backgroundOpacity:.85,sorted:null},xaxis:{show:null,position:"bottom",mode:null,font:null,color:null,tickColor:null,transform:null,inverseTransform:null,min:null,max:null,autoscaleMargin:null,ticks:null,tickFormatter:null,labelWidth:null,labelHeight:null,reserveSpace:null,tickLength:null,alignTicksWithAxis:null,tickDecimals:null,tickSize:null,minTickSize:null},yaxis:{autoscaleMargin:.02,position:"left"},xaxes:[],yaxes:[],series:{points:{show:false,radius:3,lineWidth:2,fill:true,fillColor:"#ffffff",symbol:"circle"},lines:{lineWidth:2,fill:false,fillColor:null,steps:false},bars:{show:false,lineWidth:2,barWidth:1,fill:true,fillColor:null,align:"left",horizontal:false,zero:true},shadowSize:3,highlightColor:null},grid:{show:true,aboveData:false,color:"#545454",backgroundColor:null,borderColor:null,tickColor:null,margin:0,labelMargin:5,axisMargin:8,borderWidth:2,minBorderMargin:null,markings:null,markingsColor:"#f4f4f4",markingsLineWidth:2,clickable:false,hoverable:false,autoHighlight:true,mouseActiveRadius:10},interaction:{redrawOverlayInterval:1e3/60},hooks:{}},surface=null,overlay=null,eventHolder=null,ctx=null,octx=null,xaxes=[],yaxes=[],plotOffset={left:0,right:0,top:0,bottom:0},plotWidth=0,plotHeight=0,hooks={processOptions:[],processRawData:[],processDatapoints:[],processOffset:[],drawBackground:[],drawSeries:[],draw:[],bindEvents:[],drawOverlay:[],shutdown:[]},plot=this;plot.setData=setData;plot.setupGrid=setupGrid;plot.draw=draw;plot.getPlaceholder=function(){return placeholder};plot.getCanvas=function(){return surface.element};plot.getPlotOffset=function(){return plotOffset};plot.width=function(){return plotWidth};plot.height=function(){return plotHeight};plot.offset=function(){var o=eventHolder.offset();o.left+=plotOffset.left;o.top+=plotOffset.top;return o};plot.getData=function(){return series};plot.getAxes=function(){var res={},i;$.each(xaxes.concat(yaxes),function(_,axis){if(axis)res[axis.direction+(axis.n!=1?axis.n:"")+"axis"]=axis});return res};plot.getXAxes=function(){return xaxes};plot.getYAxes=function(){return yaxes};plot.c2p=canvasToAxisCoords;plot.p2c=axisToCanvasCoords;plot.getOptions=function(){return options};plot.highlight=highlight;plot.unhighlight=unhighlight;plot.triggerRedrawOverlay=triggerRedrawOverlay;plot.pointOffset=function(point){return{left:parseInt(xaxes[axisNumber(point,"x")-1].p2c(+point.x)+plotOffset.left,10),top:parseInt(yaxes[axisNumber(point,"y")-1].p2c(+point.y)+plotOffset.top,10)}};plot.shutdown=shutdown;plot.destroy=function(){shutdown();placeholder.removeData("plot").empty();series=[];options=null;surface=null;overlay=null;eventHolder=null;ctx=null;octx=null;xaxes=[];yaxes=[];hooks=null;highlights=[];plot=null};plot.resize=function(){var width=placeholder.width(),height=placeholder.height();surface.resize(width,height);overlay.resize(width,height)};plot.hooks=hooks;initPlugins(plot);parseOptions(options_);setupCanvases();setData(data_);setupGrid();draw();bindEvents();function executeHooks(hook,args){args=[plot].concat(args);for(var i=0;i<hook.length;++i)hook[i].apply(this,args)}function initPlugins(){var classes={Canvas:Canvas};for(var i=0;i<plugins.length;++i){var p=plugins[i];p.init(plot,classes);if(p.options)$.extend(true,options,p.options)}}function parseOptions(opts){$.extend(true,options,opts);if(opts&&opts.colors){options.colors=opts.colors}if(options.xaxis.color==null)options.xaxis.color=$.color.parse(options.grid.color).scale("a",.22).toString();if(options.yaxis.color==null)options.yaxis.color=$.color.parse(options.grid.color).scale("a",.22).toString();if(options.xaxis.tickColor==null)options.xaxis.tickColor=options.grid.tickColor||options.xaxis.color;if(options.yaxis.tickColor==null)options.yaxis.tickColor=options.grid.tickColor||options.yaxis.color;if(options.grid.borderColor==null)options.grid.borderColor=options.grid.color;if(options.grid.tickColor==null)options.grid.tickColor=$.color.parse(options.grid.color).scale("a",.22).toString();var i,axisOptions,axisCount,fontSize=placeholder.css("font-size"),fontSizeDefault=fontSize?+fontSize.replace("px",""):13,fontDefaults={style:placeholder.css("font-style"),size:Math.round(.8*fontSizeDefault),variant:placeholder.css("font-variant"),weight:placeholder.css("font-weight"),family:placeholder.css("font-family")};axisCount=options.xaxes.length||1;for(i=0;i<axisCount;++i){axisOptions=options.xaxes[i];if(axisOptions&&!axisOptions.tickColor){axisOptions.tickColor=axisOptions.color}axisOptions=$.extend(true,{},options.xaxis,axisOptions);options.xaxes[i]=axisOptions;if(axisOptions.font){axisOptions.font=$.extend({},fontDefaults,axisOptions.font);if(!axisOptions.font.color){axisOptions.font.color=axisOptions.color}if(!axisOptions.font.lineHeight){axisOptions.font.lineHeight=Math.round(axisOptions.font.size*1.15)}}}axisCount=options.yaxes.length||1;for(i=0;i<axisCount;++i){axisOptions=options.yaxes[i];if(axisOptions&&!axisOptions.tickColor){axisOptions.tickColor=axisOptions.color}axisOptions=$.extend(true,{},options.yaxis,axisOptions);options.yaxes[i]=axisOptions;if(axisOptions.font){axisOptions.font=$.extend({},fontDefaults,axisOptions.font);if(!axisOptions.font.color){axisOptions.font.color=axisOptions.color}if(!axisOptions.font.lineHeight){axisOptions.font.lineHeight=Math.round(axisOptions.font.size*1.15)}}}if(options.xaxis.noTicks&&options.xaxis.ticks==null)options.xaxis.ticks=options.xaxis.noTicks;if(options.yaxis.noTicks&&options.yaxis.ticks==null)options.yaxis.ticks=options.yaxis.noTicks;if(options.x2axis){options.xaxes[1]=$.extend(true,{},options.xaxis,options.x2axis);options.xaxes[1].position="top";if(options.x2axis.min==null){options.xaxes[1].min=null}if(options.x2axis.max==null){options.xaxes[1].max=null}}if(options.y2axis){options.yaxes[1]=$.extend(true,{},options.yaxis,options.y2axis);options.yaxes[1].position="right";if(options.y2axis.min==null){options.yaxes[1].min=null}if(options.y2axis.max==null){options.yaxes[1].max=null}}if(options.grid.coloredAreas)options.grid.markings=options.grid.coloredAreas;if(options.grid.coloredAreasColor)options.grid.markingsColor=options.grid.coloredAreasColor;if(options.lines)$.extend(true,options.series.lines,options.lines);if(options.points)$.extend(true,options.series.points,options.points);if(options.bars)$.extend(true,options.series.bars,options.bars);if(options.shadowSize!=null)options.series.shadowSize=options.shadowSize;if(options.highlightColor!=null)options.series.highlightColor=options.highlightColor;for(i=0;i<options.xaxes.length;++i)getOrCreateAxis(xaxes,i+1).options=options.xaxes[i];for(i=0;i<options.yaxes.length;++i)getOrCreateAxis(yaxes,i+1).options=options.yaxes[i];for(var n in hooks)if(options.hooks[n]&&options.hooks[n].length)hooks[n]=hooks[n].concat(options.hooks[n]);executeHooks(hooks.processOptions,[options])}function setData(d){series=parseData(d);fillInSeriesOptions();processData()}function parseData(d){var res=[];for(var i=0;i<d.length;++i){var s=$.extend(true,{},options.series);if(d[i].data!=null){s.data=d[i].data;delete d[i].data;$.extend(true,s,d[i]);d[i].data=s.data}else s.data=d[i];res.push(s)}return res}function axisNumber(obj,coord){var a=obj[coord+"axis"];if(typeof a=="object")a=a.n;if(typeof a!="number")a=1;return a}function allAxes(){return $.grep(xaxes.concat(yaxes),function(a){return a})}function canvasToAxisCoords(pos){var res={},i,axis;for(i=0;i<xaxes.length;++i){axis=xaxes[i];if(axis&&axis.used)res["x"+axis.n]=axis.c2p(pos.left)}for(i=0;i<yaxes.length;++i){axis=yaxes[i];if(axis&&axis.used)res["y"+axis.n]=axis.c2p(pos.top)}if(res.x1!==undefined)res.x=res.x1;if(res.y1!==undefined)res.y=res.y1;return res}function axisToCanvasCoords(pos){var res={},i,axis,key;for(i=0;i<xaxes.length;++i){axis=xaxes[i];if(axis&&axis.used){key="x"+axis.n;if(pos[key]==null&&axis.n==1)key="x";if(pos[key]!=null){res.left=axis.p2c(pos[key]);break}}}for(i=0;i<yaxes.length;++i){axis=yaxes[i];if(axis&&axis.used){key="y"+axis.n;if(pos[key]==null&&axis.n==1)key="y";if(pos[key]!=null){res.top=axis.p2c(pos[key]);break}}}return res}function getOrCreateAxis(axes,number){if(!axes[number-1])axes[number-1]={n:number,direction:axes==xaxes?"x":"y",options:$.extend(true,{},axes==xaxes?options.xaxis:options.yaxis)};return axes[number-1]}function fillInSeriesOptions(){var neededColors=series.length,maxIndex=-1,i;for(i=0;i<series.length;++i){var sc=series[i].color;if(sc!=null){neededColors--;if(typeof sc=="number"&&sc>maxIndex){maxIndex=sc}}}if(neededColors<=maxIndex){neededColors=maxIndex+1}var c,colors=[],colorPool=options.colors,colorPoolSize=colorPool.length,variation=0;for(i=0;i<neededColors;i++){c=$.color.parse(colorPool[i%colorPoolSize]||"#666");if(i%colorPoolSize==0&&i){if(variation>=0){if(variation<.5){variation=-variation-.2}else variation=0}else variation=-variation}colors[i]=c.scale("rgb",1+variation)}var colori=0,s;for(i=0;i<series.length;++i){s=series[i];if(s.color==null){s.color=colors[colori].toString();++colori}else if(typeof s.color=="number")s.color=colors[s.color].toString();if(s.lines.show==null){var v,show=true;for(v in s)if(s[v]&&s[v].show){show=false;break}if(show)s.lines.show=true}if(s.lines.zero==null){s.lines.zero=!!s.lines.fill}s.xaxis=getOrCreateAxis(xaxes,axisNumber(s,"x"));s.yaxis=getOrCreateAxis(yaxes,axisNumber(s,"y"))}}function processData(){var topSentry=Number.POSITIVE_INFINITY,bottomSentry=Number.NEGATIVE_INFINITY,fakeInfinity=Number.MAX_VALUE,i,j,k,m,length,s,points,ps,x,y,axis,val,f,p,data,format;function updateAxis(axis,min,max){if(min<axis.datamin&&min!=-fakeInfinity)axis.datamin=min;if(max>axis.datamax&&max!=fakeInfinity)axis.datamax=max}$.each(allAxes(),function(_,axis){axis.datamin=topSentry;axis.datamax=bottomSentry;axis.used=false});for(i=0;i<series.length;++i){s=series[i];s.datapoints={points:[]};executeHooks(hooks.processRawData,[s,s.data,s.datapoints])}for(i=0;i<series.length;++i){s=series[i];data=s.data;format=s.datapoints.format;if(!format){format=[];format.push({x:true,number:true,required:true});format.push({y:true,number:true,required:true});if(s.bars.show||s.lines.show&&s.lines.fill){var autoscale=!!(s.bars.show&&s.bars.zero||s.lines.show&&s.lines.zero);format.push({y:true,number:true,required:false,defaultValue:0,autoscale:autoscale});if(s.bars.horizontal){delete format[format.length-1].y;format[format.length-1].x=true}}s.datapoints.format=format}if(s.datapoints.pointsize!=null)continue;s.datapoints.pointsize=format.length;ps=s.datapoints.pointsize;points=s.datapoints.points;var insertSteps=s.lines.show&&s.lines.steps;s.xaxis.used=s.yaxis.used=true;for(j=k=0;j<data.length;++j,k+=ps){p=data[j];var nullify=p==null;if(!nullify){for(m=0;m<ps;++m){val=p[m];f=format[m];if(f){if(f.number&&val!=null){val=+val;if(isNaN(val))val=null;else if(val==Infinity)val=fakeInfinity;else if(val==-Infinity)val=-fakeInfinity}if(val==null){if(f.required)nullify=true;if(f.defaultValue!=null)val=f.defaultValue}}points[k+m]=val}}if(nullify){for(m=0;m<ps;++m){val=points[k+m];if(val!=null){f=format[m];if(f.autoscale!==false){if(f.x){updateAxis(s.xaxis,val,val)}if(f.y){updateAxis(s.yaxis,val,val)}}}points[k+m]=null}}else{if(insertSteps&&k>0&&points[k-ps]!=null&&points[k-ps]!=points[k]&&points[k-ps+1]!=points[k+1]){for(m=0;m<ps;++m)points[k+ps+m]=points[k+m];points[k+1]=points[k-ps+1];k+=ps}}}}for(i=0;i<series.length;++i){s=series[i];executeHooks(hooks.processDatapoints,[s,s.datapoints])}for(i=0;i<series.length;++i){s=series[i];points=s.datapoints.points;ps=s.datapoints.pointsize;format=s.datapoints.format;var xmin=topSentry,ymin=topSentry,xmax=bottomSentry,ymax=bottomSentry;for(j=0;j<points.length;j+=ps){if(points[j]==null)continue;for(m=0;m<ps;++m){val=points[j+m];f=format[m];if(!f||f.autoscale===false||val==fakeInfinity||val==-fakeInfinity)continue;if(f.x){if(val<xmin)xmin=val;if(val>xmax)xmax=val}if(f.y){if(val<ymin)ymin=val;if(val>ymax)ymax=val}}}if(s.bars.show){var delta;switch(s.bars.align){case"left":delta=0;break;case"right":delta=-s.bars.barWidth;break;default:delta=-s.bars.barWidth/2}if(s.bars.horizontal){ymin+=delta;ymax+=delta+s.bars.barWidth}else{xmin+=delta;xmax+=delta+s.bars.barWidth}}updateAxis(s.xaxis,xmin,xmax);updateAxis(s.yaxis,ymin,ymax)}$.each(allAxes(),function(_,axis){if(axis.datamin==topSentry)axis.datamin=null;if(axis.datamax==bottomSentry)axis.datamax=null})}function setupCanvases(){placeholder.css("padding",0).children().filter(function(){return!$(this).hasClass("flot-overlay")&&!$(this).hasClass("flot-base")}).remove();if(placeholder.css("position")=="static")placeholder.css("position","relative");surface=new Canvas("flot-base",placeholder);overlay=new Canvas("flot-overlay",placeholder);ctx=surface.context;octx=overlay.context;eventHolder=$(overlay.element).unbind();var existing=placeholder.data("plot");if(existing){existing.shutdown();overlay.clear()}placeholder.data("plot",plot)}function bindEvents(){if(options.grid.hoverable){eventHolder.mousemove(onMouseMove);eventHolder.bind("mouseleave",onMouseLeave)}if(options.grid.clickable)eventHolder.click(onClick);executeHooks(hooks.bindEvents,[eventHolder])}function shutdown(){if(redrawTimeout)clearTimeout(redrawTimeout);eventHolder.unbind("mousemove",onMouseMove);eventHolder.unbind("mouseleave",onMouseLeave);eventHolder.unbind("click",onClick);executeHooks(hooks.shutdown,[eventHolder])}function setTransformationHelpers(axis){function identity(x){return x}var s,m,t=axis.options.transform||identity,it=axis.options.inverseTransform;if(axis.direction=="x"){s=axis.scale=plotWidth/Math.abs(t(axis.max)-t(axis.min));m=Math.min(t(axis.max),t(axis.min))}else{s=axis.scale=plotHeight/Math.abs(t(axis.max)-t(axis.min));s=-s;m=Math.max(t(axis.max),t(axis.min))}if(t==identity)axis.p2c=function(p){return(p-m)*s};else axis.p2c=function(p){return(t(p)-m)*s};if(!it)axis.c2p=function(c){return m+c/s};else axis.c2p=function(c){return it(m+c/s)}}function measureTickLabels(axis){var opts=axis.options,ticks=axis.ticks||[],labelWidth=opts.labelWidth||0,labelHeight=opts.labelHeight||0,maxWidth=labelWidth||(axis.direction=="x"?Math.floor(surface.width/(ticks.length||1)):null),legacyStyles=axis.direction+"Axis "+axis.direction+axis.n+"Axis",layer="flot-"+axis.direction+"-axis flot-"+axis.direction+axis.n+"-axis "+legacyStyles,font=opts.font||"flot-tick-label tickLabel";for(var i=0;i<ticks.length;++i){var t=ticks[i];if(!t.label)continue;var info=surface.getTextInfo(layer,t.label,font,null,maxWidth);labelWidth=Math.max(labelWidth,info.width);labelHeight=Math.max(labelHeight,info.height)}axis.labelWidth=opts.labelWidth||labelWidth;axis.labelHeight=opts.labelHeight||labelHeight}function allocateAxisBoxFirstPhase(axis){var lw=axis.labelWidth,lh=axis.labelHeight,pos=axis.options.position,isXAxis=axis.direction==="x",tickLength=axis.options.tickLength,axisMargin=options.grid.axisMargin,padding=options.grid.labelMargin,innermost=true,outermost=true,first=true,found=false;$.each(isXAxis?xaxes:yaxes,function(i,a){if(a&&(a.show||a.reserveSpace)){if(a===axis){found=true}else if(a.options.position===pos){if(found){outermost=false}else{innermost=false}}if(!found){first=false}}});if(outermost){axisMargin=0}if(tickLength==null){tickLength=first?"full":5}if(!isNaN(+tickLength))padding+=+tickLength;if(isXAxis){lh+=padding;if(pos=="bottom"){plotOffset.bottom+=lh+axisMargin;axis.box={top:surface.height-plotOffset.bottom,height:lh}}else{axis.box={top:plotOffset.top+axisMargin,height:lh};plotOffset.top+=lh+axisMargin}}else{lw+=padding;if(pos=="left"){axis.box={left:plotOffset.left+axisMargin,width:lw};plotOffset.left+=lw+axisMargin}else{plotOffset.right+=lw+axisMargin;axis.box={left:surface.width-plotOffset.right,width:lw}}}axis.position=pos;axis.tickLength=tickLength;axis.box.padding=padding;axis.innermost=innermost}function allocateAxisBoxSecondPhase(axis){if(axis.direction=="x"){axis.box.left=plotOffset.left-axis.labelWidth/2;axis.box.width=surface.width-plotOffset.left-plotOffset.right+axis.labelWidth}else{axis.box.top=plotOffset.top-axis.labelHeight/2;axis.box.height=surface.height-plotOffset.bottom-plotOffset.top+axis.labelHeight}}function adjustLayoutForThingsStickingOut(){var minMargin=options.grid.minBorderMargin,axis,i;if(minMargin==null){minMargin=0;for(i=0;i<series.length;++i)minMargin=Math.max(minMargin,2*(series[i].points.radius+series[i].points.lineWidth/2))}var margins={left:minMargin,right:minMargin,top:minMargin,bottom:minMargin};$.each(allAxes(),function(_,axis){if(axis.reserveSpace&&axis.ticks&&axis.ticks.length){if(axis.direction==="x"){margins.left=Math.max(margins.left,axis.labelWidth/2);margins.right=Math.max(margins.right,axis.labelWidth/2)}else{margins.bottom=Math.max(margins.bottom,axis.labelHeight/2);margins.top=Math.max(margins.top,axis.labelHeight/2)}}});plotOffset.left=Math.ceil(Math.max(margins.left,plotOffset.left));plotOffset.right=Math.ceil(Math.max(margins.right,plotOffset.right));plotOffset.top=Math.ceil(Math.max(margins.top,plotOffset.top));plotOffset.bottom=Math.ceil(Math.max(margins.bottom,plotOffset.bottom))}function setupGrid(){var i,axes=allAxes(),showGrid=options.grid.show;for(var a in plotOffset){var margin=options.grid.margin||0;plotOffset[a]=typeof margin=="number"?margin:margin[a]||0}executeHooks(hooks.processOffset,[plotOffset]);for(var a in plotOffset){if(typeof options.grid.borderWidth=="object"){plotOffset[a]+=showGrid?options.grid.borderWidth[a]:0}else{plotOffset[a]+=showGrid?options.grid.borderWidth:0}}$.each(axes,function(_,axis){var axisOpts=axis.options;axis.show=axisOpts.show==null?axis.used:axisOpts.show;axis.reserveSpace=axisOpts.reserveSpace==null?axis.show:axisOpts.reserveSpace;setRange(axis)});if(showGrid){var allocatedAxes=$.grep(axes,function(axis){return axis.show||axis.reserveSpace});$.each(allocatedAxes,function(_,axis){setupTickGeneration(axis);setTicks(axis);snapRangeToTicks(axis,axis.ticks);measureTickLabels(axis)});for(i=allocatedAxes.length-1;i>=0;--i)allocateAxisBoxFirstPhase(allocatedAxes[i]);adjustLayoutForThingsStickingOut();$.each(allocatedAxes,function(_,axis){allocateAxisBoxSecondPhase(axis)})}plotWidth=surface.width-plotOffset.left-plotOffset.right;plotHeight=surface.height-plotOffset.bottom-plotOffset.top;$.each(axes,function(_,axis){setTransformationHelpers(axis)});if(showGrid){drawAxisLabels()}insertLegend()}function setRange(axis){var opts=axis.options,min=+(opts.min!=null?opts.min:axis.datamin),max=+(opts.max!=null?opts.max:axis.datamax),delta=max-min;if(delta==0){var widen=max==0?1:.01;if(opts.min==null)min-=widen;if(opts.max==null||opts.min!=null)max+=widen}else{var margin=opts.autoscaleMargin;if(margin!=null){if(opts.min==null){min-=delta*margin;if(min<0&&axis.datamin!=null&&axis.datamin>=0)min=0}if(opts.max==null){max+=delta*margin;if(max>0&&axis.datamax!=null&&axis.datamax<=0)max=0}}}axis.min=min;axis.max=max}function setupTickGeneration(axis){var opts=axis.options;var noTicks;if(typeof opts.ticks=="number"&&opts.ticks>0)noTicks=opts.ticks;else noTicks=.3*Math.sqrt(axis.direction=="x"?surface.width:surface.height);var delta=(axis.max-axis.min)/noTicks,dec=-Math.floor(Math.log(delta)/Math.LN10),maxDec=opts.tickDecimals;if(maxDec!=null&&dec>maxDec){dec=maxDec}var magn=Math.pow(10,-dec),norm=delta/magn,size;if(norm<1.5){size=1}else if(norm<3){size=2;if(norm>2.25&&(maxDec==null||dec+1<=maxDec)){size=2.5;++dec}}else if(norm<7.5){size=5}else{size=10}size*=magn;if(opts.minTickSize!=null&&size<opts.minTickSize){size=opts.minTickSize}axis.delta=delta;axis.tickDecimals=Math.max(0,maxDec!=null?maxDec:dec);axis.tickSize=opts.tickSize||size;if(opts.mode=="time"&&!axis.tickGenerator){throw new Error("Time mode requires the flot.time plugin.")}if(!axis.tickGenerator){axis.tickGenerator=function(axis){var ticks=[],start=floorInBase(axis.min,axis.tickSize),i=0,v=Number.NaN,prev;do{prev=v;v=start+i*axis.tickSize;ticks.push(v);++i}while(v<axis.max&&v!=prev);return ticks};axis.tickFormatter=function(value,axis){var factor=axis.tickDecimals?Math.pow(10,axis.tickDecimals):1;var formatted=""+Math.round(value*factor)/factor;if(axis.tickDecimals!=null){var decimal=formatted.indexOf(".");var precision=decimal==-1?0:formatted.length-decimal-1;if(precision<axis.tickDecimals){return(precision?formatted:formatted+".")+(""+factor).substr(1,axis.tickDecimals-precision)}}return formatted}}if($.isFunction(opts.tickFormatter))axis.tickFormatter=function(v,axis){return""+opts.tickFormatter(v,axis)};if(opts.alignTicksWithAxis!=null){var otherAxis=(axis.direction=="x"?xaxes:yaxes)[opts.alignTicksWithAxis-1];if(otherAxis&&otherAxis.used&&otherAxis!=axis){var niceTicks=axis.tickGenerator(axis);if(niceTicks.length>0){if(opts.min==null)axis.min=Math.min(axis.min,niceTicks[0]);if(opts.max==null&&niceTicks.length>1)axis.max=Math.max(axis.max,niceTicks[niceTicks.length-1])}axis.tickGenerator=function(axis){var ticks=[],v,i;for(i=0;i<otherAxis.ticks.length;++i){v=(otherAxis.ticks[i].v-otherAxis.min)/(otherAxis.max-otherAxis.min);v=axis.min+v*(axis.max-axis.min);ticks.push(v)}return ticks};if(!axis.mode&&opts.tickDecimals==null){var extraDec=Math.max(0,-Math.floor(Math.log(axis.delta)/Math.LN10)+1),ts=axis.tickGenerator(axis);if(!(ts.length>1&&/\..*0$/.test((ts[1]-ts[0]).toFixed(extraDec))))axis.tickDecimals=extraDec}}}}function setTicks(axis){var oticks=axis.options.ticks,ticks=[];if(oticks==null||typeof oticks=="number"&&oticks>0)ticks=axis.tickGenerator(axis);else if(oticks){if($.isFunction(oticks))ticks=oticks(axis);else ticks=oticks}var i,v;axis.ticks=[];for(i=0;i<ticks.length;++i){var label=null;var t=ticks[i];if(typeof t=="object"){v=+t[0];if(t.length>1)label=t[1]}else v=+t;if(label==null)label=axis.tickFormatter(v,axis);if(!isNaN(v))axis.ticks.push({v:v,label:label})}}function snapRangeToTicks(axis,ticks){if(axis.options.autoscaleMargin&&ticks.length>0){if(axis.options.min==null)axis.min=Math.min(axis.min,ticks[0].v);if(axis.options.max==null&&ticks.length>1)axis.max=Math.max(axis.max,ticks[ticks.length-1].v)}}function draw(){surface.clear();executeHooks(hooks.drawBackground,[ctx]);var grid=options.grid;if(grid.show&&grid.backgroundColor)drawBackground();if(grid.show&&!grid.aboveData){drawGrid()}for(var i=0;i<series.length;++i){executeHooks(hooks.drawSeries,[ctx,series[i]]);drawSeries(series[i])}executeHooks(hooks.draw,[ctx]);if(grid.show&&grid.aboveData){drawGrid()}surface.render();triggerRedrawOverlay()}function extractRange(ranges,coord){var axis,from,to,key,axes=allAxes();for(var i=0;i<axes.length;++i){axis=axes[i];if(axis.direction==coord){key=coord+axis.n+"axis";if(!ranges[key]&&axis.n==1)key=coord+"axis";if(ranges[key]){from=ranges[key].from;to=ranges[key].to;break}}}if(!ranges[key]){axis=coord=="x"?xaxes[0]:yaxes[0];from=ranges[coord+"1"];to=ranges[coord+"2"]}if(from!=null&&to!=null&&from>to){var tmp=from;from=to;to=tmp}return{from:from,to:to,axis:axis}}function drawBackground(){ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.fillStyle=getColorOrGradient(options.grid.backgroundColor,plotHeight,0,"rgba(255, 255, 255, 0)");ctx.fillRect(0,0,plotWidth,plotHeight);ctx.restore()}function drawGrid(){var i,axes,bw,bc;ctx.save();ctx.translate(plotOffset.left,plotOffset.top);var markings=options.grid.markings;if(markings){if($.isFunction(markings)){axes=plot.getAxes();axes.xmin=axes.xaxis.min;axes.xmax=axes.xaxis.max;axes.ymin=axes.yaxis.min;axes.ymax=axes.yaxis.max;markings=markings(axes)}for(i=0;i<markings.length;++i){var m=markings[i],xrange=extractRange(m,"x"),yrange=extractRange(m,"y");if(xrange.from==null)xrange.from=xrange.axis.min;if(xrange.to==null)xrange.to=xrange.axis.max;
if(yrange.from==null)yrange.from=yrange.axis.min;if(yrange.to==null)yrange.to=yrange.axis.max;if(xrange.to<xrange.axis.min||xrange.from>xrange.axis.max||yrange.to<yrange.axis.min||yrange.from>yrange.axis.max)continue;xrange.from=Math.max(xrange.from,xrange.axis.min);xrange.to=Math.min(xrange.to,xrange.axis.max);yrange.from=Math.max(yrange.from,yrange.axis.min);yrange.to=Math.min(yrange.to,yrange.axis.max);var xequal=xrange.from===xrange.to,yequal=yrange.from===yrange.to;if(xequal&&yequal){continue}xrange.from=Math.floor(xrange.axis.p2c(xrange.from));xrange.to=Math.floor(xrange.axis.p2c(xrange.to));yrange.from=Math.floor(yrange.axis.p2c(yrange.from));yrange.to=Math.floor(yrange.axis.p2c(yrange.to));if(xequal||yequal){var lineWidth=m.lineWidth||options.grid.markingsLineWidth,subPixel=lineWidth%2?.5:0;ctx.beginPath();ctx.strokeStyle=m.color||options.grid.markingsColor;ctx.lineWidth=lineWidth;if(xequal){ctx.moveTo(xrange.to+subPixel,yrange.from);ctx.lineTo(xrange.to+subPixel,yrange.to)}else{ctx.moveTo(xrange.from,yrange.to+subPixel);ctx.lineTo(xrange.to,yrange.to+subPixel)}ctx.stroke()}else{ctx.fillStyle=m.color||options.grid.markingsColor;ctx.fillRect(xrange.from,yrange.to,xrange.to-xrange.from,yrange.from-yrange.to)}}}axes=allAxes();bw=options.grid.borderWidth;for(var j=0;j<axes.length;++j){var axis=axes[j],box=axis.box,t=axis.tickLength,x,y,xoff,yoff;if(!axis.show||axis.ticks.length==0)continue;ctx.lineWidth=1;if(axis.direction=="x"){x=0;if(t=="full")y=axis.position=="top"?0:plotHeight;else y=box.top-plotOffset.top+(axis.position=="top"?box.height:0)}else{y=0;if(t=="full")x=axis.position=="left"?0:plotWidth;else x=box.left-plotOffset.left+(axis.position=="left"?box.width:0)}if(!axis.innermost){ctx.strokeStyle=axis.options.color;ctx.beginPath();xoff=yoff=0;if(axis.direction=="x")xoff=plotWidth+1;else yoff=plotHeight+1;if(ctx.lineWidth==1){if(axis.direction=="x"){y=Math.floor(y)+.5}else{x=Math.floor(x)+.5}}ctx.moveTo(x,y);ctx.lineTo(x+xoff,y+yoff);ctx.stroke()}ctx.strokeStyle=axis.options.tickColor;ctx.beginPath();for(i=0;i<axis.ticks.length;++i){var v=axis.ticks[i].v;xoff=yoff=0;if(isNaN(v)||v<axis.min||v>axis.max||t=="full"&&(typeof bw=="object"&&bw[axis.position]>0||bw>0)&&(v==axis.min||v==axis.max))continue;if(axis.direction=="x"){x=axis.p2c(v);yoff=t=="full"?-plotHeight:t;if(axis.position=="top")yoff=-yoff}else{y=axis.p2c(v);xoff=t=="full"?-plotWidth:t;if(axis.position=="left")xoff=-xoff}if(ctx.lineWidth==1){if(axis.direction=="x")x=Math.floor(x)+.5;else y=Math.floor(y)+.5}ctx.moveTo(x,y);ctx.lineTo(x+xoff,y+yoff)}ctx.stroke()}if(bw){bc=options.grid.borderColor;if(typeof bw=="object"||typeof bc=="object"){if(typeof bw!=="object"){bw={top:bw,right:bw,bottom:bw,left:bw}}if(typeof bc!=="object"){bc={top:bc,right:bc,bottom:bc,left:bc}}if(bw.top>0){ctx.strokeStyle=bc.top;ctx.lineWidth=bw.top;ctx.beginPath();ctx.moveTo(0-bw.left,0-bw.top/2);ctx.lineTo(plotWidth,0-bw.top/2);ctx.stroke()}if(bw.right>0){ctx.strokeStyle=bc.right;ctx.lineWidth=bw.right;ctx.beginPath();ctx.moveTo(plotWidth+bw.right/2,0-bw.top);ctx.lineTo(plotWidth+bw.right/2,plotHeight);ctx.stroke()}if(bw.bottom>0){ctx.strokeStyle=bc.bottom;ctx.lineWidth=bw.bottom;ctx.beginPath();ctx.moveTo(plotWidth+bw.right,plotHeight+bw.bottom/2);ctx.lineTo(0,plotHeight+bw.bottom/2);ctx.stroke()}if(bw.left>0){ctx.strokeStyle=bc.left;ctx.lineWidth=bw.left;ctx.beginPath();ctx.moveTo(0-bw.left/2,plotHeight+bw.bottom);ctx.lineTo(0-bw.left/2,0);ctx.stroke()}}else{ctx.lineWidth=bw;ctx.strokeStyle=options.grid.borderColor;ctx.strokeRect(-bw/2,-bw/2,plotWidth+bw,plotHeight+bw)}}ctx.restore()}function drawAxisLabels(){$.each(allAxes(),function(_,axis){var box=axis.box,legacyStyles=axis.direction+"Axis "+axis.direction+axis.n+"Axis",layer="flot-"+axis.direction+"-axis flot-"+axis.direction+axis.n+"-axis "+legacyStyles,font=axis.options.font||"flot-tick-label tickLabel",tick,x,y,halign,valign;surface.removeText(layer);if(!axis.show||axis.ticks.length==0)return;for(var i=0;i<axis.ticks.length;++i){tick=axis.ticks[i];if(!tick.label||tick.v<axis.min||tick.v>axis.max)continue;if(axis.direction=="x"){halign="center";x=plotOffset.left+axis.p2c(tick.v);if(axis.position=="bottom"){y=box.top+box.padding}else{y=box.top+box.height-box.padding;valign="bottom"}}else{valign="middle";y=plotOffset.top+axis.p2c(tick.v);if(axis.position=="left"){x=box.left+box.width-box.padding;halign="right"}else{x=box.left+box.padding}}surface.addText(layer,x,y,tick.label,font,null,null,halign,valign)}})}function drawSeries(series){if(series.lines.show)drawSeriesLines(series);if(series.bars.show)drawSeriesBars(series);if(series.points.show)drawSeriesPoints(series)}function drawSeriesLines(series){function plotLine(datapoints,xoffset,yoffset,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize,prevx=null,prevy=null;ctx.beginPath();for(var i=ps;i<points.length;i+=ps){var x1=points[i-ps],y1=points[i-ps+1],x2=points[i],y2=points[i+1];if(x1==null||x2==null)continue;if(y1<=y2&&y1<axisy.min){if(y2<axisy.min)continue;x1=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.min}else if(y2<=y1&&y2<axisy.min){if(y1<axisy.min)continue;x2=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.min}if(y1>=y2&&y1>axisy.max){if(y2>axisy.max)continue;x1=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.max}else if(y2>=y1&&y2>axisy.max){if(y1>axisy.max)continue;x2=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.max}if(x1<=x2&&x1<axisx.min){if(x2<axisx.min)continue;y1=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.min}else if(x2<=x1&&x2<axisx.min){if(x1<axisx.min)continue;y2=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.min}if(x1>=x2&&x1>axisx.max){if(x2>axisx.max)continue;y1=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.max}else if(x2>=x1&&x2>axisx.max){if(x1>axisx.max)continue;y2=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.max}if(x1!=prevx||y1!=prevy)ctx.moveTo(axisx.p2c(x1)+xoffset,axisy.p2c(y1)+yoffset);prevx=x2;prevy=y2;ctx.lineTo(axisx.p2c(x2)+xoffset,axisy.p2c(y2)+yoffset)}ctx.stroke()}function plotLineArea(datapoints,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize,bottom=Math.min(Math.max(0,axisy.min),axisy.max),i=0,top,areaOpen=false,ypos=1,segmentStart=0,segmentEnd=0;while(true){if(ps>0&&i>points.length+ps)break;i+=ps;var x1=points[i-ps],y1=points[i-ps+ypos],x2=points[i],y2=points[i+ypos];if(areaOpen){if(ps>0&&x1!=null&&x2==null){segmentEnd=i;ps=-ps;ypos=2;continue}if(ps<0&&i==segmentStart+ps){ctx.fill();areaOpen=false;ps=-ps;ypos=1;i=segmentStart=segmentEnd+ps;continue}}if(x1==null||x2==null)continue;if(x1<=x2&&x1<axisx.min){if(x2<axisx.min)continue;y1=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.min}else if(x2<=x1&&x2<axisx.min){if(x1<axisx.min)continue;y2=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.min}if(x1>=x2&&x1>axisx.max){if(x2>axisx.max)continue;y1=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.max}else if(x2>=x1&&x2>axisx.max){if(x1>axisx.max)continue;y2=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.max}if(!areaOpen){ctx.beginPath();ctx.moveTo(axisx.p2c(x1),axisy.p2c(bottom));areaOpen=true}if(y1>=axisy.max&&y2>=axisy.max){ctx.lineTo(axisx.p2c(x1),axisy.p2c(axisy.max));ctx.lineTo(axisx.p2c(x2),axisy.p2c(axisy.max));continue}else if(y1<=axisy.min&&y2<=axisy.min){ctx.lineTo(axisx.p2c(x1),axisy.p2c(axisy.min));ctx.lineTo(axisx.p2c(x2),axisy.p2c(axisy.min));continue}var x1old=x1,x2old=x2;if(y1<=y2&&y1<axisy.min&&y2>=axisy.min){x1=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.min}else if(y2<=y1&&y2<axisy.min&&y1>=axisy.min){x2=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.min}if(y1>=y2&&y1>axisy.max&&y2<=axisy.max){x1=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.max}else if(y2>=y1&&y2>axisy.max&&y1<=axisy.max){x2=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.max}if(x1!=x1old){ctx.lineTo(axisx.p2c(x1old),axisy.p2c(y1))}ctx.lineTo(axisx.p2c(x1),axisy.p2c(y1));ctx.lineTo(axisx.p2c(x2),axisy.p2c(y2));if(x2!=x2old){ctx.lineTo(axisx.p2c(x2),axisy.p2c(y2));ctx.lineTo(axisx.p2c(x2old),axisy.p2c(y2))}}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.lineJoin="round";var lw=series.lines.lineWidth,sw=series.shadowSize;if(lw>0&&sw>0){ctx.lineWidth=sw;ctx.strokeStyle="rgba(0,0,0,0.1)";var angle=Math.PI/18;plotLine(series.datapoints,Math.sin(angle)*(lw/2+sw/2),Math.cos(angle)*(lw/2+sw/2),series.xaxis,series.yaxis);ctx.lineWidth=sw/2;plotLine(series.datapoints,Math.sin(angle)*(lw/2+sw/4),Math.cos(angle)*(lw/2+sw/4),series.xaxis,series.yaxis)}ctx.lineWidth=lw;ctx.strokeStyle=series.color;var fillStyle=getFillStyle(series.lines,series.color,0,plotHeight);if(fillStyle){ctx.fillStyle=fillStyle;plotLineArea(series.datapoints,series.xaxis,series.yaxis)}if(lw>0)plotLine(series.datapoints,0,0,series.xaxis,series.yaxis);ctx.restore()}function drawSeriesPoints(series){function plotPoints(datapoints,radius,fillStyle,offset,shadow,axisx,axisy,symbol){var points=datapoints.points,ps=datapoints.pointsize;for(var i=0;i<points.length;i+=ps){var x=points[i],y=points[i+1];if(x==null||x<axisx.min||x>axisx.max||y<axisy.min||y>axisy.max)continue;ctx.beginPath();x=axisx.p2c(x);y=axisy.p2c(y)+offset;if(symbol=="circle")ctx.arc(x,y,radius,0,shadow?Math.PI:Math.PI*2,false);else symbol(ctx,x,y,radius,shadow);ctx.closePath();if(fillStyle){ctx.fillStyle=fillStyle;ctx.fill()}ctx.stroke()}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);var lw=series.points.lineWidth,sw=series.shadowSize,radius=series.points.radius,symbol=series.points.symbol;if(lw==0)lw=1e-4;if(lw>0&&sw>0){var w=sw/2;ctx.lineWidth=w;ctx.strokeStyle="rgba(0,0,0,0.1)";plotPoints(series.datapoints,radius,null,w+w/2,true,series.xaxis,series.yaxis,symbol);ctx.strokeStyle="rgba(0,0,0,0.2)";plotPoints(series.datapoints,radius,null,w/2,true,series.xaxis,series.yaxis,symbol)}ctx.lineWidth=lw;ctx.strokeStyle=series.color;plotPoints(series.datapoints,radius,getFillStyle(series.points,series.color),0,false,series.xaxis,series.yaxis,symbol);ctx.restore()}function drawBar(x,y,b,barLeft,barRight,fillStyleCallback,axisx,axisy,c,horizontal,lineWidth){var left,right,bottom,top,drawLeft,drawRight,drawTop,drawBottom,tmp;if(horizontal){drawBottom=drawRight=drawTop=true;drawLeft=false;left=b;right=x;top=y+barLeft;bottom=y+barRight;if(right<left){tmp=right;right=left;left=tmp;drawLeft=true;drawRight=false}}else{drawLeft=drawRight=drawTop=true;drawBottom=false;left=x+barLeft;right=x+barRight;bottom=b;top=y;if(top<bottom){tmp=top;top=bottom;bottom=tmp;drawBottom=true;drawTop=false}}if(right<axisx.min||left>axisx.max||top<axisy.min||bottom>axisy.max)return;if(left<axisx.min){left=axisx.min;drawLeft=false}if(right>axisx.max){right=axisx.max;drawRight=false}if(bottom<axisy.min){bottom=axisy.min;drawBottom=false}if(top>axisy.max){top=axisy.max;drawTop=false}left=axisx.p2c(left);bottom=axisy.p2c(bottom);right=axisx.p2c(right);top=axisy.p2c(top);if(fillStyleCallback){c.fillStyle=fillStyleCallback(bottom,top);c.fillRect(left,top,right-left,bottom-top)}if(lineWidth>0&&(drawLeft||drawRight||drawTop||drawBottom)){c.beginPath();c.moveTo(left,bottom);if(drawLeft)c.lineTo(left,top);else c.moveTo(left,top);if(drawTop)c.lineTo(right,top);else c.moveTo(right,top);if(drawRight)c.lineTo(right,bottom);else c.moveTo(right,bottom);if(drawBottom)c.lineTo(left,bottom);else c.moveTo(left,bottom);c.stroke()}}function drawSeriesBars(series){function plotBars(datapoints,barLeft,barRight,fillStyleCallback,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize;for(var i=0;i<points.length;i+=ps){if(points[i]==null)continue;drawBar(points[i],points[i+1],points[i+2],barLeft,barRight,fillStyleCallback,axisx,axisy,ctx,series.bars.horizontal,series.bars.lineWidth)}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.lineWidth=series.bars.lineWidth;ctx.strokeStyle=series.color;var barLeft;switch(series.bars.align){case"left":barLeft=0;break;case"right":barLeft=-series.bars.barWidth;break;default:barLeft=-series.bars.barWidth/2}var fillStyleCallback=series.bars.fill?function(bottom,top){return getFillStyle(series.bars,series.color,bottom,top)}:null;plotBars(series.datapoints,barLeft,barLeft+series.bars.barWidth,fillStyleCallback,series.xaxis,series.yaxis);ctx.restore()}function getFillStyle(filloptions,seriesColor,bottom,top){var fill=filloptions.fill;if(!fill)return null;if(filloptions.fillColor)return getColorOrGradient(filloptions.fillColor,bottom,top,seriesColor);var c=$.color.parse(seriesColor);c.a=typeof fill=="number"?fill:.4;c.normalize();return c.toString()}function insertLegend(){if(options.legend.container!=null){$(options.legend.container).html("")}else{placeholder.find(".legend").remove()}if(!options.legend.show){return}var fragments=[],entries=[],rowStarted=false,lf=options.legend.labelFormatter,s,label;for(var i=0;i<series.length;++i){s=series[i];if(s.label){label=lf?lf(s.label,s):s.label;if(label){entries.push({label:label,color:s.color})}}}if(options.legend.sorted){if($.isFunction(options.legend.sorted)){entries.sort(options.legend.sorted)}else if(options.legend.sorted=="reverse"){entries.reverse()}else{var ascending=options.legend.sorted!="descending";entries.sort(function(a,b){return a.label==b.label?0:a.label<b.label!=ascending?1:-1})}}for(var i=0;i<entries.length;++i){var entry=entries[i];if(i%options.legend.noColumns==0){if(rowStarted)fragments.push("</tr>");fragments.push("<tr>");rowStarted=true}fragments.push('<td class="legendColorBox"><div style="border:1px solid '+options.legend.labelBoxBorderColor+';padding:1px"><div style="width:4px;height:0;border:5px solid '+entry.color+';overflow:hidden"></div></div></td>'+'<td class="legendLabel">'+entry.label+"</td>")}if(rowStarted)fragments.push("</tr>");if(fragments.length==0)return;var table='<table style="font-size:smaller;color:'+options.grid.color+'">'+fragments.join("")+"</table>";if(options.legend.container!=null)$(options.legend.container).html(table);else{var pos="",p=options.legend.position,m=options.legend.margin;if(m[0]==null)m=[m,m];if(p.charAt(0)=="n")pos+="top:"+(m[1]+plotOffset.top)+"px;";else if(p.charAt(0)=="s")pos+="bottom:"+(m[1]+plotOffset.bottom)+"px;";if(p.charAt(1)=="e")pos+="right:"+(m[0]+plotOffset.right)+"px;";else if(p.charAt(1)=="w")pos+="left:"+(m[0]+plotOffset.left)+"px;";var legend=$('<div class="legend">'+table.replace('style="','style="position:absolute;'+pos+";")+"</div>").appendTo(placeholder);if(options.legend.backgroundOpacity!=0){var c=options.legend.backgroundColor;if(c==null){c=options.grid.backgroundColor;if(c&&typeof c=="string")c=$.color.parse(c);else c=$.color.extract(legend,"background-color");c.a=1;c=c.toString()}var div=legend.children();$('<div style="position:absolute;width:'+div.width()+"px;height:"+div.height()+"px;"+pos+"background-color:"+c+';"> </div>').prependTo(legend).css("opacity",options.legend.backgroundOpacity)}}}var highlights=[],redrawTimeout=null;function findNearbyItem(mouseX,mouseY,seriesFilter){var maxDistance=options.grid.mouseActiveRadius,smallestDistance=maxDistance*maxDistance+1,item=null,foundPoint=false,i,j,ps;for(i=series.length-1;i>=0;--i){if(!seriesFilter(series[i]))continue;var s=series[i],axisx=s.xaxis,axisy=s.yaxis,points=s.datapoints.points,mx=axisx.c2p(mouseX),my=axisy.c2p(mouseY),maxx=maxDistance/axisx.scale,maxy=maxDistance/axisy.scale;ps=s.datapoints.pointsize;if(axisx.options.inverseTransform)maxx=Number.MAX_VALUE;if(axisy.options.inverseTransform)maxy=Number.MAX_VALUE;if(s.lines.show||s.points.show){for(j=0;j<points.length;j+=ps){var x=points[j],y=points[j+1];if(x==null)continue;if(x-mx>maxx||x-mx<-maxx||y-my>maxy||y-my<-maxy)continue;var dx=Math.abs(axisx.p2c(x)-mouseX),dy=Math.abs(axisy.p2c(y)-mouseY),dist=dx*dx+dy*dy;if(dist<smallestDistance){smallestDistance=dist;item=[i,j/ps]}}}if(s.bars.show&&!item){var barLeft,barRight;switch(s.bars.align){case"left":barLeft=0;break;case"right":barLeft=-s.bars.barWidth;break;default:barLeft=-s.bars.barWidth/2}barRight=barLeft+s.bars.barWidth;for(j=0;j<points.length;j+=ps){var x=points[j],y=points[j+1],b=points[j+2];if(x==null)continue;if(series[i].bars.horizontal?mx<=Math.max(b,x)&&mx>=Math.min(b,x)&&my>=y+barLeft&&my<=y+barRight:mx>=x+barLeft&&mx<=x+barRight&&my>=Math.min(b,y)&&my<=Math.max(b,y))item=[i,j/ps]}}}if(item){i=item[0];j=item[1];ps=series[i].datapoints.pointsize;return{datapoint:series[i].datapoints.points.slice(j*ps,(j+1)*ps),dataIndex:j,series:series[i],seriesIndex:i}}return null}function onMouseMove(e){if(options.grid.hoverable)triggerClickHoverEvent("plothover",e,function(s){return s["hoverable"]!=false})}function onMouseLeave(e){if(options.grid.hoverable)triggerClickHoverEvent("plothover",e,function(s){return false})}function onClick(e){triggerClickHoverEvent("plotclick",e,function(s){return s["clickable"]!=false})}function triggerClickHoverEvent(eventname,event,seriesFilter){var offset=eventHolder.offset(),canvasX=event.pageX-offset.left-plotOffset.left,canvasY=event.pageY-offset.top-plotOffset.top,pos=canvasToAxisCoords({left:canvasX,top:canvasY});pos.pageX=event.pageX;pos.pageY=event.pageY;var item=findNearbyItem(canvasX,canvasY,seriesFilter);if(item){item.pageX=parseInt(item.series.xaxis.p2c(item.datapoint[0])+offset.left+plotOffset.left,10);item.pageY=parseInt(item.series.yaxis.p2c(item.datapoint[1])+offset.top+plotOffset.top,10)}if(options.grid.autoHighlight){for(var i=0;i<highlights.length;++i){var h=highlights[i];if(h.auto==eventname&&!(item&&h.series==item.series&&h.point[0]==item.datapoint[0]&&h.point[1]==item.datapoint[1]))unhighlight(h.series,h.point)}if(item)highlight(item.series,item.datapoint,eventname)}placeholder.trigger(eventname,[pos,item])}function triggerRedrawOverlay(){var t=options.interaction.redrawOverlayInterval;if(t==-1){drawOverlay();return}if(!redrawTimeout)redrawTimeout=setTimeout(drawOverlay,t)}function drawOverlay(){redrawTimeout=null;octx.save();overlay.clear();octx.translate(plotOffset.left,plotOffset.top);var i,hi;for(i=0;i<highlights.length;++i){hi=highlights[i];if(hi.series.bars.show)drawBarHighlight(hi.series,hi.point);else drawPointHighlight(hi.series,hi.point)}octx.restore();executeHooks(hooks.drawOverlay,[octx])}function highlight(s,point,auto){if(typeof s=="number")s=series[s];if(typeof point=="number"){var ps=s.datapoints.pointsize;point=s.datapoints.points.slice(ps*point,ps*(point+1))}var i=indexOfHighlight(s,point);if(i==-1){highlights.push({series:s,point:point,auto:auto});triggerRedrawOverlay()}else if(!auto)highlights[i].auto=false}function unhighlight(s,point){if(s==null&&point==null){highlights=[];triggerRedrawOverlay();return}if(typeof s=="number")s=series[s];if(typeof point=="number"){var ps=s.datapoints.pointsize;point=s.datapoints.points.slice(ps*point,ps*(point+1))}var i=indexOfHighlight(s,point);if(i!=-1){highlights.splice(i,1);triggerRedrawOverlay()}}function indexOfHighlight(s,p){for(var i=0;i<highlights.length;++i){var h=highlights[i];if(h.series==s&&h.point[0]==p[0]&&h.point[1]==p[1])return i}return-1}function drawPointHighlight(series,point){var x=point[0],y=point[1],axisx=series.xaxis,axisy=series.yaxis,highlightColor=typeof series.highlightColor==="string"?series.highlightColor:$.color.parse(series.color).scale("a",.5).toString();if(x<axisx.min||x>axisx.max||y<axisy.min||y>axisy.max)return;var pointRadius=series.points.radius+series.points.lineWidth/2;octx.lineWidth=pointRadius;octx.strokeStyle=highlightColor;var radius=1.5*pointRadius;x=axisx.p2c(x);y=axisy.p2c(y);octx.beginPath();if(series.points.symbol=="circle")octx.arc(x,y,radius,0,2*Math.PI,false);else series.points.symbol(octx,x,y,radius,false);octx.closePath();octx.stroke()}function drawBarHighlight(series,point){var highlightColor=typeof series.highlightColor==="string"?series.highlightColor:$.color.parse(series.color).scale("a",.5).toString(),fillStyle=highlightColor,barLeft;switch(series.bars.align){case"left":barLeft=0;break;case"right":barLeft=-series.bars.barWidth;break;default:barLeft=-series.bars.barWidth/2}octx.lineWidth=series.bars.lineWidth;octx.strokeStyle=highlightColor;drawBar(point[0],point[1],point[2]||0,barLeft,barLeft+series.bars.barWidth,function(){return fillStyle},series.xaxis,series.yaxis,octx,series.bars.horizontal,series.bars.lineWidth)}function getColorOrGradient(spec,bottom,top,defaultColor){if(typeof spec=="string")return spec;else{var gradient=ctx.createLinearGradient(0,top,0,bottom);for(var i=0,l=spec.colors.length;i<l;++i){var c=spec.colors[i];if(typeof c!="string"){var co=$.color.parse(defaultColor);if(c.brightness!=null)co=co.scale("rgb",c.brightness);if(c.opacity!=null)co.a*=c.opacity;c=co.toString()}gradient.addColorStop(i/(l-1),c)}return gradient}}}$.plot=function(placeholder,data,options){var plot=new Plot($(placeholder),data,options,$.plot.plugins);return plot};$.plot.version="0.8.3";$.plot.plugins=[];$.fn.plot=function(data,options){return this.each(function(){$.plot(this,data,options)})};function floorInBase(n,base){return base*Math.floor(n/base)}})(jQuery);
/* Javascript plotting library for jQuery, version 0.8.3.

Copyright (c) 2007-2014 IOLA and Ole Laursen.
Licensed under the MIT license.

*/
(function($){var options={xaxis:{timezone:null,timeformat:null,twelveHourClock:false,monthNames:null}};function floorInBase(n,base){return base*Math.floor(n/base)}function formatDate(d,fmt,monthNames,dayNames){if(typeof d.strftime=="function"){return d.strftime(fmt)}var leftPad=function(n,pad){n=""+n;pad=""+(pad==null?"0":pad);return n.length==1?pad+n:n};var r=[];var escape=false;var hours=d.getHours();var isAM=hours<12;if(monthNames==null){monthNames=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]}if(dayNames==null){dayNames=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]}var hours12;if(hours>12){hours12=hours-12}else if(hours==0){hours12=12}else{hours12=hours}for(var i=0;i<fmt.length;++i){var c=fmt.charAt(i);if(escape){switch(c){case"a":c=""+dayNames[d.getDay()];break;case"b":c=""+monthNames[d.getMonth()];break;case"d":c=leftPad(d.getDate());break;case"e":c=leftPad(d.getDate()," ");break;case"h":case"H":c=leftPad(hours);break;case"I":c=leftPad(hours12);break;case"l":c=leftPad(hours12," ");break;case"m":c=leftPad(d.getMonth()+1);break;case"M":c=leftPad(d.getMinutes());break;case"q":c=""+(Math.floor(d.getMonth()/3)+1);break;case"S":c=leftPad(d.getSeconds());break;case"y":c=leftPad(d.getFullYear()%100);break;case"Y":c=""+d.getFullYear();break;case"p":c=isAM?""+"am":""+"pm";break;case"P":c=isAM?""+"AM":""+"PM";break;case"w":c=""+d.getDay();break}r.push(c);escape=false}else{if(c=="%"){escape=true}else{r.push(c)}}}return r.join("")}function makeUtcWrapper(d){function addProxyMethod(sourceObj,sourceMethod,targetObj,targetMethod){sourceObj[sourceMethod]=function(){return targetObj[targetMethod].apply(targetObj,arguments)}}var utc={date:d};if(d.strftime!=undefined){addProxyMethod(utc,"strftime",d,"strftime")}addProxyMethod(utc,"getTime",d,"getTime");addProxyMethod(utc,"setTime",d,"setTime");var props=["Date","Day","FullYear","Hours","Milliseconds","Minutes","Month","Seconds"];for(var p=0;p<props.length;p++){addProxyMethod(utc,"get"+props[p],d,"getUTC"+props[p]);addProxyMethod(utc,"set"+props[p],d,"setUTC"+props[p])}return utc}function dateGenerator(ts,opts){if(opts.timezone=="browser"){return new Date(ts)}else if(!opts.timezone||opts.timezone=="utc"){return makeUtcWrapper(new Date(ts))}else if(typeof timezoneJS!="undefined"&&typeof timezoneJS.Date!="undefined"){var d=new timezoneJS.Date;d.setTimezone(opts.timezone);d.setTime(ts);return d}else{return makeUtcWrapper(new Date(ts))}}var timeUnitSize={second:1e3,minute:60*1e3,hour:60*60*1e3,day:24*60*60*1e3,month:30*24*60*60*1e3,quarter:3*30*24*60*60*1e3,year:365.2425*24*60*60*1e3};var baseSpec=[[1,"second"],[2,"second"],[5,"second"],[10,"second"],[30,"second"],[1,"minute"],[2,"minute"],[5,"minute"],[10,"minute"],[30,"minute"],[1,"hour"],[2,"hour"],[4,"hour"],[8,"hour"],[12,"hour"],[1,"day"],[2,"day"],[3,"day"],[.25,"month"],[.5,"month"],[1,"month"],[2,"month"]];var specMonths=baseSpec.concat([[3,"month"],[6,"month"],[1,"year"]]);var specQuarters=baseSpec.concat([[1,"quarter"],[2,"quarter"],[1,"year"]]);function init(plot){plot.hooks.processOptions.push(function(plot,options){$.each(plot.getAxes(),function(axisName,axis){var opts=axis.options;if(opts.mode=="time"){axis.tickGenerator=function(axis){var ticks=[];var d=dateGenerator(axis.min,opts);var minSize=0;var spec=opts.tickSize&&opts.tickSize[1]==="quarter"||opts.minTickSize&&opts.minTickSize[1]==="quarter"?specQuarters:specMonths;if(opts.minTickSize!=null){if(typeof opts.tickSize=="number"){minSize=opts.tickSize}else{minSize=opts.minTickSize[0]*timeUnitSize[opts.minTickSize[1]]}}for(var i=0;i<spec.length-1;++i){if(axis.delta<(spec[i][0]*timeUnitSize[spec[i][1]]+spec[i+1][0]*timeUnitSize[spec[i+1][1]])/2&&spec[i][0]*timeUnitSize[spec[i][1]]>=minSize){break}}var size=spec[i][0];var unit=spec[i][1];if(unit=="year"){if(opts.minTickSize!=null&&opts.minTickSize[1]=="year"){size=Math.floor(opts.minTickSize[0])}else{var magn=Math.pow(10,Math.floor(Math.log(axis.delta/timeUnitSize.year)/Math.LN10));var norm=axis.delta/timeUnitSize.year/magn;if(norm<1.5){size=1}else if(norm<3){size=2}else if(norm<7.5){size=5}else{size=10}size*=magn}if(size<1){size=1}}axis.tickSize=opts.tickSize||[size,unit];var tickSize=axis.tickSize[0];unit=axis.tickSize[1];var step=tickSize*timeUnitSize[unit];if(unit=="second"){d.setSeconds(floorInBase(d.getSeconds(),tickSize))}else if(unit=="minute"){d.setMinutes(floorInBase(d.getMinutes(),tickSize))}else if(unit=="hour"){d.setHours(floorInBase(d.getHours(),tickSize))}else if(unit=="month"){d.setMonth(floorInBase(d.getMonth(),tickSize))}else if(unit=="quarter"){d.setMonth(3*floorInBase(d.getMonth()/3,tickSize))}else if(unit=="year"){d.setFullYear(floorInBase(d.getFullYear(),tickSize))}d.setMilliseconds(0);if(step>=timeUnitSize.minute){d.setSeconds(0)}if(step>=timeUnitSize.hour){d.setMinutes(0)}if(step>=timeUnitSize.day){d.setHours(0)}if(step>=timeUnitSize.day*4){d.setDate(1)}if(step>=timeUnitSize.month*2){d.setMonth(floorInBase(d.getMonth(),3))}if(step>=timeUnitSize.quarter*2){d.setMonth(floorInBase(d.getMonth(),6))}if(step>=timeUnitSize.year){d.setMonth(0)}var carry=0;var v=Number.NaN;var prev;do{prev=v;v=d.getTime();ticks.push(v);if(unit=="month"||unit=="quarter"){if(tickSize<1){d.setDate(1);var start=d.getTime();d.setMonth(d.getMonth()+(unit=="quarter"?3:1));var end=d.getTime();d.setTime(v+carry*timeUnitSize.hour+(end-start)*tickSize);carry=d.getHours();d.setHours(0)}else{d.setMonth(d.getMonth()+tickSize*(unit=="quarter"?3:1))}}else if(unit=="year"){d.setFullYear(d.getFullYear()+tickSize)}else{d.setTime(v+step)}}while(v<axis.max&&v!=prev);return ticks};axis.tickFormatter=function(v,axis){var d=dateGenerator(v,axis.options);if(opts.timeformat!=null){return formatDate(d,opts.timeformat,opts.monthNames,opts.dayNames)}var useQuarters=axis.options.tickSize&&axis.options.tickSize[1]=="quarter"||axis.options.minTickSize&&axis.options.minTickSize[1]=="quarter";var t=axis.tickSize[0]*timeUnitSize[axis.tickSize[1]];var span=axis.max-axis.min;var suffix=opts.twelveHourClock?" %p":"";var hourCode=opts.twelveHourClock?"%I":"%H";var fmt;if(t<timeUnitSize.minute){fmt=hourCode+":%M:%S"+suffix}else if(t<timeUnitSize.day){if(span<2*timeUnitSize.day){fmt=hourCode+":%M"+suffix}else{fmt="%b %d "+hourCode+":%M"+suffix}}else if(t<timeUnitSize.month){fmt="%b %d"}else if(useQuarters&&t<timeUnitSize.quarter||!useQuarters&&t<timeUnitSize.year){if(span<timeUnitSize.year){fmt="%b"}else{fmt="%b %Y"}}else if(useQuarters&&t<timeUnitSize.year){if(span<timeUnitSize.year){fmt="Q%q"}else{fmt="Q%q %Y"}}else{fmt="%Y"}var rt=formatDate(d,fmt,opts.monthNames,opts.dayNames);return rt}}})})}$.plot.plugins.push({init:init,options:options,name:"time",version:"1.0"});$.plot.formatDate=formatDate;$.plot.dateGenerator=dateGenerator})(jQuery);
/*! http://responsiveslides.com v1.32 by @viljamis */
(function(d,E,w){d.fn.responsiveSlides=function(h){var b=d.extend({auto:!0,speed:1E3,timeout:4E3,pager:!1,nav:!1,random:!1,pause:!1,pauseControls:!1,prevText:"Previous",nextText:"Next",maxwidth:"",controls:"",namespace:"rslides",before:function(){},after:function(){}},h);return this.each(function(){w++;var e=d(this),n,q,i,k,l,m=0,f=e.children(),x=f.size(),r=parseFloat(b.speed),y=parseFloat(b.timeout),s=parseFloat(b.maxwidth),c=b.namespace,g=c+w,z=c+"_nav "+g+"_nav",t=c+"_here",j=g+"_on",A=g+"_s",
    p=d("<ul class='"+c+"_tabs "+g+"_tabs' />"),B={"float":"left",position:"relative"},F={"float":"none",position:"absolute"},u=function(a){b.before();f.stop().fadeOut(r,function(){d(this).removeClass(j).css(F)}).eq(a).fadeIn(r,function(){d(this).addClass(j).css(B);b.after();m=a})};b.random&&(f.sort(function(){return Math.round(Math.random())-0.5}),e.empty().append(f));f.each(function(a){this.id=A+a});e.addClass(c+" "+g);h&&h.maxwidth&&e.css("max-width",s);f.hide().eq(0).addClass(j).css(B).show();if(1<
    f.size()){if(y<r+100)return;if(b.pager){var v=[];f.each(function(a){a+=1;v+="<li><a href='#' class='"+A+a+"'>"+a+"</a></li>"});p.append(v);l=p.find("a");h.controls?d(b.controls).append(p):e.after(p);n=function(a){l.closest("li").removeClass(t).eq(a).addClass(t)}}b.auto&&(q=function(){k=setInterval(function(){f.stop(!0,!0);var a=m+1<x?m+1:0;b.pager&&n(a);u(a)},y)},q());i=function(){b.auto&&(clearInterval(k),q())};b.pause&&e.hover(function(){clearInterval(k)},function(){i()});b.pager&&(l.bind("click",
    function(a){a.preventDefault();b.pauseControls||i();a=l.index(this);m===a||d("."+j).queue("fx").length||(n(a),u(a))}).eq(0).closest("li").addClass(t),b.pauseControls&&l.hover(function(){clearInterval(k)},function(){i()}));if(b.nav){c="<a href='#' class='"+z+" prev'>"+b.prevText+"</a><a href='#' class='"+z+" next'>"+b.nextText+"</a>";h.controls?d(b.controls).append(c):e.after(c);var c=d("."+g+"_nav"),C=d("."+g+"_nav.prev");c.bind("click",function(a){a.preventDefault();if(!d("."+j).queue("fx").length){var c=
    f.index(d("."+j)),a=c-1,c=c+1<x?m+1:0;u(d(this)[0]===C[0]?a:c);b.pager&&n(d(this)[0]===C[0]?a:c);b.pauseControls||i()}});b.pauseControls&&c.hover(function(){clearInterval(k)},function(){i()})}}if("undefined"===typeof document.body.style.maxWidth&&h.maxwidth){var D=function(){e.css("width","100%");e.width()>s&&e.css("width",s)};D();d(E).bind("resize",function(){D()})}})}})(jQuery,this,0);
/*! jQuery UI - v1.10.4 - 2014-04-08
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.sortable.js
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(t,e){function n(e,n){var r,s,o,a=e.nodeName.toLowerCase();return"area"===a?(r=e.parentNode,s=r.name,e.href&&s&&"map"===r.nodeName.toLowerCase()?(o=t("img[usemap=#"+s+"]")[0],!!o&&i(o)):!1):(/input|select|textarea|button|object/.test(a)?!e.disabled:"a"===a?e.href||n:n)&&i(e)}function i(e){return t.expr.filters.visible(e)&&!t(e).parents().addBack().filter(function(){return"hidden"===t.css(this,"visibility")}).length}var r=0,s=/^ui-id-\d+$/;t.ui=t.ui||{},t.extend(t.ui,{version:"1.10.4",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),t.fn.extend({focus:function(e){return function(n,i){return"number"==typeof n?this.each(function(){var e=this;setTimeout(function(){t(e).focus(),i&&i.call(e)},n)}):e.apply(this,arguments)}}(t.fn.focus),scrollParent:function(){var e;return e=t.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(t.css(this,"position"))&&/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!e.length?t(document):e},zIndex:function(n){if(n!==e)return this.css("zIndex",n);if(this.length)for(var i,r,s=t(this[0]);s.length&&s[0]!==document;){if(i=s.css("position"),("absolute"===i||"relative"===i||"fixed"===i)&&(r=parseInt(s.css("zIndex"),10),!isNaN(r)&&0!==r))return r;s=s.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ (++r))})},removeUniqueId:function(){return this.each(function(){s.test(this.id)&&t(this).removeAttr("id")})}}),t.extend(t.expr[":"],{data:t.expr.createPseudo?t.expr.createPseudo(function(e){return function(n){return!!t.data(n,e)}}):function(e,n,i){return!!t.data(e,i[3])},focusable:function(e){return n(e,!isNaN(t.attr(e,"tabindex")))},tabbable:function(e){var i=t.attr(e,"tabindex"),r=isNaN(i);return(r||i>=0)&&n(e,!r)}}),t("<a>").outerWidth(1).jquery||t.each(["Width","Height"],function(n,i){function r(e,n,i,r){return t.each(s,function(){n-=parseFloat(t.css(e,"padding"+this))||0,i&&(n-=parseFloat(t.css(e,"border"+this+"Width"))||0),r&&(n-=parseFloat(t.css(e,"margin"+this))||0)}),n}var s="Width"===i?["Left","Right"]:["Top","Bottom"],o=i.toLowerCase(),a={innerWidth:t.fn.innerWidth,innerHeight:t.fn.innerHeight,outerWidth:t.fn.outerWidth,outerHeight:t.fn.outerHeight};t.fn["inner"+i]=function(n){return n===e?a["inner"+i].call(this):this.each(function(){t(this).css(o,r(this,n)+"px")})},t.fn["outer"+i]=function(e,n){return"number"!=typeof e?a["outer"+i].call(this,e):this.each(function(){t(this).css(o,r(this,e,!0,n)+"px")})}}),t.fn.addBack||(t.fn.addBack=function(t){return this.add(null==t?this.prevObject:this.prevObject.filter(t))}),t("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(t.fn.removeData=function(e){return function(n){return arguments.length?e.call(this,t.camelCase(n)):e.call(this)}}(t.fn.removeData)),t.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),t.support.selectstart="onselectstart"in document.createElement("div"),t.fn.extend({disableSelection:function(){return this.bind((t.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(t){t.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),t.extend(t.ui,{plugin:{add:function(e,n,i){var r,s=t.ui[e].prototype;for(r in i)s.plugins[r]=s.plugins[r]||[],s.plugins[r].push([n,i[r]])},call:function(t,e,n){var i,r=t.plugins[e];if(r&&t.element[0].parentNode&&11!==t.element[0].parentNode.nodeType)for(i=0;r.length>i;i++)t.options[r[i][0]]&&r[i][1].apply(t.element,n)}},hasScroll:function(e,n){if("hidden"===t(e).css("overflow"))return!1;var i=n&&"left"===n?"scrollLeft":"scrollTop",r=!1;return e[i]>0?!0:(e[i]=1,r=e[i]>0,e[i]=0,r)}})})(jQuery);(function(t,e){var i=0,n=Array.prototype.slice,s=t.cleanData;t.cleanData=function(e){for(var i,n=0;null!=(i=e[n]);n++)try{t(i).triggerHandler("remove")}catch(o){}s(e)},t.widget=function(i,n,s){var o,r,a,l,u={},h=i.split(".")[0];i=i.split(".")[1],o=h+"-"+i,s||(s=n,n=t.Widget),t.expr[":"][o.toLowerCase()]=function(e){return!!t.data(e,o)},t[h]=t[h]||{},r=t[h][i],a=t[h][i]=function(t,i){return this._createWidget?(arguments.length&&this._createWidget(t,i),e):new a(t,i)},t.extend(a,r,{version:s.version,_proto:t.extend({},s),_childConstructors:[]}),l=new n,l.options=t.widget.extend({},l.options),t.each(s,function(i,s){return t.isFunction(s)?(u[i]=function(){var t=function(){return n.prototype[i].apply(this,arguments)},e=function(t){return n.prototype[i].apply(this,t)};return function(){var i,n=this._super,o=this._superApply;return this._super=t,this._superApply=e,i=s.apply(this,arguments),this._super=n,this._superApply=o,i}}(),e):(u[i]=s,e)}),a.prototype=t.widget.extend(l,{widgetEventPrefix:r?l.widgetEventPrefix||i:i},u,{constructor:a,namespace:h,widgetName:i,widgetFullName:o}),r?(t.each(r._childConstructors,function(e,i){var n=i.prototype;t.widget(n.namespace+"."+n.widgetName,a,i._proto)}),delete r._childConstructors):n._childConstructors.push(a),t.widget.bridge(i,a)},t.widget.extend=function(i){for(var s,o,r=n.call(arguments,1),a=0,l=r.length;l>a;a++)for(s in r[a])o=r[a][s],r[a].hasOwnProperty(s)&&o!==e&&(i[s]=t.isPlainObject(o)?t.isPlainObject(i[s])?t.widget.extend({},i[s],o):t.widget.extend({},o):o);return i},t.widget.bridge=function(i,s){var o=s.prototype.widgetFullName||i;t.fn[i]=function(r){var a="string"==typeof r,l=n.call(arguments,1),u=this;return r=!a&&l.length?t.widget.extend.apply(null,[r].concat(l)):r,a?this.each(function(){var n,s=t.data(this,o);return s?t.isFunction(s[r])&&"_"!==r.charAt(0)?(n=s[r].apply(s,l),n!==s&&n!==e?(u=n&&n.jquery?u.pushStack(n.get()):n,!1):e):t.error("no such method '"+r+"' for "+i+" widget instance"):t.error("cannot call methods on "+i+" prior to initialization; "+"attempted to call method '"+r+"'")}):this.each(function(){var e=t.data(this,o);e?e.option(r||{})._init():t.data(this,o,new s(r,this))}),u}},t.Widget=function(){},t.Widget._childConstructors=[],t.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(e,n){n=t(n||this.defaultElement||this)[0],this.element=t(n),this.uuid=i++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=t.widget.extend({},this.options,this._getCreateOptions(),e),this.bindings=t(),this.hoverable=t(),this.focusable=t(),n!==this&&(t.data(n,this.widgetFullName,this),this._on(!0,this.element,{remove:function(t){t.target===n&&this.destroy()}}),this.document=t(n.style?n.ownerDocument:n.document||n),this.window=t(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:t.noop,_getCreateEventData:t.noop,_create:t.noop,_init:t.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(t.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:t.noop,widget:function(){return this.element},option:function(i,n){var s,o,r,a=i;if(0===arguments.length)return t.widget.extend({},this.options);if("string"==typeof i)if(a={},s=i.split("."),i=s.shift(),s.length){for(o=a[i]=t.widget.extend({},this.options[i]),r=0;s.length-1>r;r++)o[s[r]]=o[s[r]]||{},o=o[s[r]];if(i=s.pop(),1===arguments.length)return o[i]===e?null:o[i];o[i]=n}else{if(1===arguments.length)return this.options[i]===e?null:this.options[i];a[i]=n}return this._setOptions(a),this},_setOptions:function(t){var e;for(e in t)this._setOption(e,t[e]);return this},_setOption:function(t,e){return this.options[t]=e,"disabled"===t&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!e).attr("aria-disabled",e),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(i,n,s){var o,r=this;"boolean"!=typeof i&&(s=n,n=i,i=!1),s?(n=o=t(n),this.bindings=this.bindings.add(n)):(s=n,n=this.element,o=this.widget()),t.each(s,function(s,a){function l(){return i||r.options.disabled!==!0&&!t(this).hasClass("ui-state-disabled")?("string"==typeof a?r[a]:a).apply(r,arguments):e}"string"!=typeof a&&(l.guid=a.guid=a.guid||l.guid||t.guid++);var u=s.match(/^(\w+)\s*(.*)$/),h=u[1]+r.eventNamespace,c=u[2];c?o.delegate(c,h,l):n.bind(h,l)})},_off:function(t,e){e=(e||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,t.unbind(e).undelegate(e)},_delay:function(t,e){function i(){return("string"==typeof t?n[t]:t).apply(n,arguments)}var n=this;return setTimeout(i,e||0)},_hoverable:function(e){this.hoverable=this.hoverable.add(e),this._on(e,{mouseenter:function(e){t(e.currentTarget).addClass("ui-state-hover")},mouseleave:function(e){t(e.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(e){this.focusable=this.focusable.add(e),this._on(e,{focusin:function(e){t(e.currentTarget).addClass("ui-state-focus")},focusout:function(e){t(e.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(e,i,n){var s,o,r=this.options[e];if(n=n||{},i=t.Event(i),i.type=(e===this.widgetEventPrefix?e:this.widgetEventPrefix+e).toLowerCase(),i.target=this.element[0],o=i.originalEvent)for(s in o)s in i||(i[s]=o[s]);return this.element.trigger(i,n),!(t.isFunction(r)&&r.apply(this.element[0],[i].concat(n))===!1||i.isDefaultPrevented())}},t.each({show:"fadeIn",hide:"fadeOut"},function(e,i){t.Widget.prototype["_"+e]=function(n,s,o){"string"==typeof s&&(s={effect:s});var r,a=s?s===!0||"number"==typeof s?i:s.effect||i:e;s=s||{},"number"==typeof s&&(s={duration:s}),r=!t.isEmptyObject(s),s.complete=o,s.delay&&n.delay(s.delay),r&&t.effects&&t.effects.effect[a]?n[e](s):a!==e&&n[a]?n[a](s.duration,s.easing,o):n.queue(function(i){t(this)[e](),o&&o.call(n[0]),i()})}})})(jQuery);(function(t){var e=!1;t(document).mouseup(function(){e=!1}),t.widget("ui.mouse",{version:"1.10.4",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var e=this;this.element.bind("mousedown."+this.widgetName,function(t){return e._mouseDown(t)}).bind("click."+this.widgetName,function(i){return!0===t.data(i.target,e.widgetName+".preventClickEvent")?(t.removeData(i.target,e.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):undefined}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&t(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(i){if(!e){this._mouseStarted&&this._mouseUp(i),this._mouseDownEvent=i;var s=this,n=1===i.which,a="string"==typeof this.options.cancel&&i.target.nodeName?t(i.target).closest(this.options.cancel).length:!1;return n&&!a&&this._mouseCapture(i)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){s.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(i)&&this._mouseDelayMet(i)&&(this._mouseStarted=this._mouseStart(i)!==!1,!this._mouseStarted)?(i.preventDefault(),!0):(!0===t.data(i.target,this.widgetName+".preventClickEvent")&&t.removeData(i.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(t){return s._mouseMove(t)},this._mouseUpDelegate=function(t){return s._mouseUp(t)},t(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),i.preventDefault(),e=!0,!0)):!0}},_mouseMove:function(e){return t.ui.ie&&(!document.documentMode||9>document.documentMode)&&!e.button?this._mouseUp(e):this._mouseStarted?(this._mouseDrag(e),e.preventDefault()):(this._mouseDistanceMet(e)&&this._mouseDelayMet(e)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,e)!==!1,this._mouseStarted?this._mouseDrag(e):this._mouseUp(e)),!this._mouseStarted)},_mouseUp:function(e){return t(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,e.target===this._mouseDownEvent.target&&t.data(e.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(e)),!1},_mouseDistanceMet:function(t){return Math.max(Math.abs(this._mouseDownEvent.pageX-t.pageX),Math.abs(this._mouseDownEvent.pageY-t.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}})})(jQuery);(function(t){function e(t,e,i){return t>e&&e+i>t}function i(t){return/left|right/.test(t.css("float"))||/inline|table-cell/.test(t.css("display"))}t.widget("ui.sortable",t.ui.mouse,{version:"1.10.4",widgetEventPrefix:"sort",ready:!1,options:{appendTo:"parent",axis:!1,connectWith:!1,containment:!1,cursor:"auto",cursorAt:!1,dropOnEmpty:!0,forcePlaceholderSize:!1,forceHelperSize:!1,grid:!1,handle:!1,helper:"original",items:"> *",opacity:!1,placeholder:!1,revert:!1,scroll:!0,scrollSensitivity:20,scrollSpeed:20,scope:"default",tolerance:"intersect",zIndex:1e3,activate:null,beforeStop:null,change:null,deactivate:null,out:null,over:null,receive:null,remove:null,sort:null,start:null,stop:null,update:null},_create:function(){var t=this.options;this.containerCache={},this.element.addClass("ui-sortable"),this.refresh(),this.floating=this.items.length?"x"===t.axis||i(this.items[0].item):!1,this.offset=this.element.offset(),this._mouseInit(),this.ready=!0},_destroy:function(){this.element.removeClass("ui-sortable ui-sortable-disabled"),this._mouseDestroy();for(var t=this.items.length-1;t>=0;t--)this.items[t].item.removeData(this.widgetName+"-item");return this},_setOption:function(e,i){"disabled"===e?(this.options[e]=i,this.widget().toggleClass("ui-sortable-disabled",!!i)):t.Widget.prototype._setOption.apply(this,arguments)},_mouseCapture:function(e,i){var s=null,n=!1,o=this;return this.reverting?!1:this.options.disabled||"static"===this.options.type?!1:(this._refreshItems(e),t(e.target).parents().each(function(){return t.data(this,o.widgetName+"-item")===o?(s=t(this),!1):undefined}),t.data(e.target,o.widgetName+"-item")===o&&(s=t(e.target)),s?!this.options.handle||i||(t(this.options.handle,s).find("*").addBack().each(function(){this===e.target&&(n=!0)}),n)?(this.currentItem=s,this._removeCurrentsFromItems(),!0):!1:!1)},_mouseStart:function(e,i,s){var n,o,a=this.options;if(this.currentContainer=this,this.refreshPositions(),this.helper=this._createHelper(e),this._cacheHelperProportions(),this._cacheMargins(),this.scrollParent=this.helper.scrollParent(),this.offset=this.currentItem.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},t.extend(this.offset,{click:{left:e.pageX-this.offset.left,top:e.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.helper.css("position","absolute"),this.cssPosition=this.helper.css("position"),this.originalPosition=this._generatePosition(e),this.originalPageX=e.pageX,this.originalPageY=e.pageY,a.cursorAt&&this._adjustOffsetFromHelper(a.cursorAt),this.domPosition={prev:this.currentItem.prev()[0],parent:this.currentItem.parent()[0]},this.helper[0]!==this.currentItem[0]&&this.currentItem.hide(),this._createPlaceholder(),a.containment&&this._setContainment(),a.cursor&&"auto"!==a.cursor&&(o=this.document.find("body"),this.storedCursor=o.css("cursor"),o.css("cursor",a.cursor),this.storedStylesheet=t("<style>*{ cursor: "+a.cursor+" !important; }</style>").appendTo(o)),a.opacity&&(this.helper.css("opacity")&&(this._storedOpacity=this.helper.css("opacity")),this.helper.css("opacity",a.opacity)),a.zIndex&&(this.helper.css("zIndex")&&(this._storedZIndex=this.helper.css("zIndex")),this.helper.css("zIndex",a.zIndex)),this.scrollParent[0]!==document&&"HTML"!==this.scrollParent[0].tagName&&(this.overflowOffset=this.scrollParent.offset()),this._trigger("start",e,this._uiHash()),this._preserveHelperProportions||this._cacheHelperProportions(),!s)for(n=this.containers.length-1;n>=0;n--)this.containers[n]._trigger("activate",e,this._uiHash(this));return t.ui.ddmanager&&(t.ui.ddmanager.current=this),t.ui.ddmanager&&!a.dropBehaviour&&t.ui.ddmanager.prepareOffsets(this,e),this.dragging=!0,this.helper.addClass("ui-sortable-helper"),this._mouseDrag(e),!0},_mouseDrag:function(e){var i,s,n,o,a=this.options,r=!1;for(this.position=this._generatePosition(e),this.positionAbs=this._convertPositionTo("absolute"),this.lastPositionAbs||(this.lastPositionAbs=this.positionAbs),this.options.scroll&&(this.scrollParent[0]!==document&&"HTML"!==this.scrollParent[0].tagName?(this.overflowOffset.top+this.scrollParent[0].offsetHeight-e.pageY<a.scrollSensitivity?this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop+a.scrollSpeed:e.pageY-this.overflowOffset.top<a.scrollSensitivity&&(this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop-a.scrollSpeed),this.overflowOffset.left+this.scrollParent[0].offsetWidth-e.pageX<a.scrollSensitivity?this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft+a.scrollSpeed:e.pageX-this.overflowOffset.left<a.scrollSensitivity&&(this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft-a.scrollSpeed)):(e.pageY-t(document).scrollTop()<a.scrollSensitivity?r=t(document).scrollTop(t(document).scrollTop()-a.scrollSpeed):t(window).height()-(e.pageY-t(document).scrollTop())<a.scrollSensitivity&&(r=t(document).scrollTop(t(document).scrollTop()+a.scrollSpeed)),e.pageX-t(document).scrollLeft()<a.scrollSensitivity?r=t(document).scrollLeft(t(document).scrollLeft()-a.scrollSpeed):t(window).width()-(e.pageX-t(document).scrollLeft())<a.scrollSensitivity&&(r=t(document).scrollLeft(t(document).scrollLeft()+a.scrollSpeed))),r!==!1&&t.ui.ddmanager&&!a.dropBehaviour&&t.ui.ddmanager.prepareOffsets(this,e)),this.positionAbs=this._convertPositionTo("absolute"),this.options.axis&&"y"===this.options.axis||(this.helper[0].style.left=this.position.left+"px"),this.options.axis&&"x"===this.options.axis||(this.helper[0].style.top=this.position.top+"px"),i=this.items.length-1;i>=0;i--)if(s=this.items[i],n=s.item[0],o=this._intersectsWithPointer(s),o&&s.instance===this.currentContainer&&n!==this.currentItem[0]&&this.placeholder[1===o?"next":"prev"]()[0]!==n&&!t.contains(this.placeholder[0],n)&&("semi-dynamic"===this.options.type?!t.contains(this.element[0],n):!0)){if(this.direction=1===o?"down":"up","pointer"!==this.options.tolerance&&!this._intersectsWithSides(s))break;this._rearrange(e,s),this._trigger("change",e,this._uiHash());break}return this._contactContainers(e),t.ui.ddmanager&&t.ui.ddmanager.drag(this,e),this._trigger("sort",e,this._uiHash()),this.lastPositionAbs=this.positionAbs,!1},_mouseStop:function(e,i){if(e){if(t.ui.ddmanager&&!this.options.dropBehaviour&&t.ui.ddmanager.drop(this,e),this.options.revert){var s=this,n=this.placeholder.offset(),o=this.options.axis,a={};o&&"x"!==o||(a.left=n.left-this.offset.parent.left-this.margins.left+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollLeft)),o&&"y"!==o||(a.top=n.top-this.offset.parent.top-this.margins.top+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollTop)),this.reverting=!0,t(this.helper).animate(a,parseInt(this.options.revert,10)||500,function(){s._clear(e)})}else this._clear(e,i);return!1}},cancel:function(){if(this.dragging){this._mouseUp({target:null}),"original"===this.options.helper?this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper"):this.currentItem.show();for(var e=this.containers.length-1;e>=0;e--)this.containers[e]._trigger("deactivate",null,this._uiHash(this)),this.containers[e].containerCache.over&&(this.containers[e]._trigger("out",null,this._uiHash(this)),this.containers[e].containerCache.over=0)}return this.placeholder&&(this.placeholder[0].parentNode&&this.placeholder[0].parentNode.removeChild(this.placeholder[0]),"original"!==this.options.helper&&this.helper&&this.helper[0].parentNode&&this.helper.remove(),t.extend(this,{helper:null,dragging:!1,reverting:!1,_noFinalSort:null}),this.domPosition.prev?t(this.domPosition.prev).after(this.currentItem):t(this.domPosition.parent).prepend(this.currentItem)),this},serialize:function(e){var i=this._getItemsAsjQuery(e&&e.connected),s=[];return e=e||{},t(i).each(function(){var i=(t(e.item||this).attr(e.attribute||"id")||"").match(e.expression||/(.+)[\-=_](.+)/);i&&s.push((e.key||i[1]+"[]")+"="+(e.key&&e.expression?i[1]:i[2]))}),!s.length&&e.key&&s.push(e.key+"="),s.join("&")},toArray:function(e){var i=this._getItemsAsjQuery(e&&e.connected),s=[];return e=e||{},i.each(function(){s.push(t(e.item||this).attr(e.attribute||"id")||"")}),s},_intersectsWith:function(t){var e=this.positionAbs.left,i=e+this.helperProportions.width,s=this.positionAbs.top,n=s+this.helperProportions.height,o=t.left,a=o+t.width,r=t.top,h=r+t.height,l=this.offset.click.top,c=this.offset.click.left,u="x"===this.options.axis||s+l>r&&h>s+l,d="y"===this.options.axis||e+c>o&&a>e+c,p=u&&d;return"pointer"===this.options.tolerance||this.options.forcePointerForContainers||"pointer"!==this.options.tolerance&&this.helperProportions[this.floating?"width":"height"]>t[this.floating?"width":"height"]?p:e+this.helperProportions.width/2>o&&a>i-this.helperProportions.width/2&&s+this.helperProportions.height/2>r&&h>n-this.helperProportions.height/2},_intersectsWithPointer:function(t){var i="x"===this.options.axis||e(this.positionAbs.top+this.offset.click.top,t.top,t.height),s="y"===this.options.axis||e(this.positionAbs.left+this.offset.click.left,t.left,t.width),n=i&&s,o=this._getDragVerticalDirection(),a=this._getDragHorizontalDirection();return n?this.floating?a&&"right"===a||"down"===o?2:1:o&&("down"===o?2:1):!1},_intersectsWithSides:function(t){var i=e(this.positionAbs.top+this.offset.click.top,t.top+t.height/2,t.height),s=e(this.positionAbs.left+this.offset.click.left,t.left+t.width/2,t.width),n=this._getDragVerticalDirection(),o=this._getDragHorizontalDirection();return this.floating&&o?"right"===o&&s||"left"===o&&!s:n&&("down"===n&&i||"up"===n&&!i)},_getDragVerticalDirection:function(){var t=this.positionAbs.top-this.lastPositionAbs.top;return 0!==t&&(t>0?"down":"up")},_getDragHorizontalDirection:function(){var t=this.positionAbs.left-this.lastPositionAbs.left;return 0!==t&&(t>0?"right":"left")},refresh:function(t){return this._refreshItems(t),this.refreshPositions(),this},_connectWith:function(){var t=this.options;return t.connectWith.constructor===String?[t.connectWith]:t.connectWith},_getItemsAsjQuery:function(e){function i(){r.push(this)}var s,n,o,a,r=[],h=[],l=this._connectWith();if(l&&e)for(s=l.length-1;s>=0;s--)for(o=t(l[s]),n=o.length-1;n>=0;n--)a=t.data(o[n],this.widgetFullName),a&&a!==this&&!a.options.disabled&&h.push([t.isFunction(a.options.items)?a.options.items.call(a.element):t(a.options.items,a.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),a]);for(h.push([t.isFunction(this.options.items)?this.options.items.call(this.element,null,{options:this.options,item:this.currentItem}):t(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]),s=h.length-1;s>=0;s--)h[s][0].each(i);return t(r)},_removeCurrentsFromItems:function(){var e=this.currentItem.find(":data("+this.widgetName+"-item)");this.items=t.grep(this.items,function(t){for(var i=0;e.length>i;i++)if(e[i]===t.item[0])return!1;return!0})},_refreshItems:function(e){this.items=[],this.containers=[this];var i,s,n,o,a,r,h,l,c=this.items,u=[[t.isFunction(this.options.items)?this.options.items.call(this.element[0],e,{item:this.currentItem}):t(this.options.items,this.element),this]],d=this._connectWith();if(d&&this.ready)for(i=d.length-1;i>=0;i--)for(n=t(d[i]),s=n.length-1;s>=0;s--)o=t.data(n[s],this.widgetFullName),o&&o!==this&&!o.options.disabled&&(u.push([t.isFunction(o.options.items)?o.options.items.call(o.element[0],e,{item:this.currentItem}):t(o.options.items,o.element),o]),this.containers.push(o));for(i=u.length-1;i>=0;i--)for(a=u[i][1],r=u[i][0],s=0,l=r.length;l>s;s++)h=t(r[s]),h.data(this.widgetName+"-item",a),c.push({item:h,instance:a,width:0,height:0,left:0,top:0})},refreshPositions:function(e){this.offsetParent&&this.helper&&(this.offset.parent=this._getParentOffset());var i,s,n,o;for(i=this.items.length-1;i>=0;i--)s=this.items[i],s.instance!==this.currentContainer&&this.currentContainer&&s.item[0]!==this.currentItem[0]||(n=this.options.toleranceElement?t(this.options.toleranceElement,s.item):s.item,e||(s.width=n.outerWidth(),s.height=n.outerHeight()),o=n.offset(),s.left=o.left,s.top=o.top);if(this.options.custom&&this.options.custom.refreshContainers)this.options.custom.refreshContainers.call(this);else for(i=this.containers.length-1;i>=0;i--)o=this.containers[i].element.offset(),this.containers[i].containerCache.left=o.left,this.containers[i].containerCache.top=o.top,this.containers[i].containerCache.width=this.containers[i].element.outerWidth(),this.containers[i].containerCache.height=this.containers[i].element.outerHeight();return this},_createPlaceholder:function(e){e=e||this;var i,s=e.options;s.placeholder&&s.placeholder.constructor!==String||(i=s.placeholder,s.placeholder={element:function(){var s=e.currentItem[0].nodeName.toLowerCase(),n=t("<"+s+">",e.document[0]).addClass(i||e.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper");return"tr"===s?e.currentItem.children().each(function(){t("<td>&#160;</td>",e.document[0]).attr("colspan",t(this).attr("colspan")||1).appendTo(n)}):"img"===s&&n.attr("src",e.currentItem.attr("src")),i||n.css("visibility","hidden"),n},update:function(t,n){(!i||s.forcePlaceholderSize)&&(n.height()||n.height(e.currentItem.innerHeight()-parseInt(e.currentItem.css("paddingTop")||0,10)-parseInt(e.currentItem.css("paddingBottom")||0,10)),n.width()||n.width(e.currentItem.innerWidth()-parseInt(e.currentItem.css("paddingLeft")||0,10)-parseInt(e.currentItem.css("paddingRight")||0,10)))}}),e.placeholder=t(s.placeholder.element.call(e.element,e.currentItem)),e.currentItem.after(e.placeholder),s.placeholder.update(e,e.placeholder)},_contactContainers:function(s){var n,o,a,r,h,l,c,u,d,p,f=null,g=null;for(n=this.containers.length-1;n>=0;n--)if(!t.contains(this.currentItem[0],this.containers[n].element[0]))if(this._intersectsWith(this.containers[n].containerCache)){if(f&&t.contains(this.containers[n].element[0],f.element[0]))continue;f=this.containers[n],g=n}else this.containers[n].containerCache.over&&(this.containers[n]._trigger("out",s,this._uiHash(this)),this.containers[n].containerCache.over=0);if(f)if(1===this.containers.length)this.containers[g].containerCache.over||(this.containers[g]._trigger("over",s,this._uiHash(this)),this.containers[g].containerCache.over=1);else{for(a=1e4,r=null,p=f.floating||i(this.currentItem),h=p?"left":"top",l=p?"width":"height",c=this.positionAbs[h]+this.offset.click[h],o=this.items.length-1;o>=0;o--)t.contains(this.containers[g].element[0],this.items[o].item[0])&&this.items[o].item[0]!==this.currentItem[0]&&(!p||e(this.positionAbs.top+this.offset.click.top,this.items[o].top,this.items[o].height))&&(u=this.items[o].item.offset()[h],d=!1,Math.abs(u-c)>Math.abs(u+this.items[o][l]-c)&&(d=!0,u+=this.items[o][l]),a>Math.abs(u-c)&&(a=Math.abs(u-c),r=this.items[o],this.direction=d?"up":"down"));if(!r&&!this.options.dropOnEmpty)return;if(this.currentContainer===this.containers[g])return;r?this._rearrange(s,r,null,!0):this._rearrange(s,null,this.containers[g].element,!0),this._trigger("change",s,this._uiHash()),this.containers[g]._trigger("change",s,this._uiHash(this)),this.currentContainer=this.containers[g],this.options.placeholder.update(this.currentContainer,this.placeholder),this.containers[g]._trigger("over",s,this._uiHash(this)),this.containers[g].containerCache.over=1}},_createHelper:function(e){var i=this.options,s=t.isFunction(i.helper)?t(i.helper.apply(this.element[0],[e,this.currentItem])):"clone"===i.helper?this.currentItem.clone():this.currentItem;return s.parents("body").length||t("parent"!==i.appendTo?i.appendTo:this.currentItem[0].parentNode)[0].appendChild(s[0]),s[0]===this.currentItem[0]&&(this._storedCSS={width:this.currentItem[0].style.width,height:this.currentItem[0].style.height,position:this.currentItem.css("position"),top:this.currentItem.css("top"),left:this.currentItem.css("left")}),(!s[0].style.width||i.forceHelperSize)&&s.width(this.currentItem.width()),(!s[0].style.height||i.forceHelperSize)&&s.height(this.currentItem.height()),s},_adjustOffsetFromHelper:function(e){"string"==typeof e&&(e=e.split(" ")),t.isArray(e)&&(e={left:+e[0],top:+e[1]||0}),"left"in e&&(this.offset.click.left=e.left+this.margins.left),"right"in e&&(this.offset.click.left=this.helperProportions.width-e.right+this.margins.left),"top"in e&&(this.offset.click.top=e.top+this.margins.top),"bottom"in e&&(this.offset.click.top=this.helperProportions.height-e.bottom+this.margins.top)},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var e=this.offsetParent.offset();return"absolute"===this.cssPosition&&this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])&&(e.left+=this.scrollParent.scrollLeft(),e.top+=this.scrollParent.scrollTop()),(this.offsetParent[0]===document.body||this.offsetParent[0].tagName&&"html"===this.offsetParent[0].tagName.toLowerCase()&&t.ui.ie)&&(e={top:0,left:0}),{top:e.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:e.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"===this.cssPosition){var t=this.currentItem.position();return{top:t.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:t.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.currentItem.css("marginLeft"),10)||0,top:parseInt(this.currentItem.css("marginTop"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var e,i,s,n=this.options;"parent"===n.containment&&(n.containment=this.helper[0].parentNode),("document"===n.containment||"window"===n.containment)&&(this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,t("document"===n.containment?document:window).width()-this.helperProportions.width-this.margins.left,(t("document"===n.containment?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]),/^(document|window|parent)$/.test(n.containment)||(e=t(n.containment)[0],i=t(n.containment).offset(),s="hidden"!==t(e).css("overflow"),this.containment=[i.left+(parseInt(t(e).css("borderLeftWidth"),10)||0)+(parseInt(t(e).css("paddingLeft"),10)||0)-this.margins.left,i.top+(parseInt(t(e).css("borderTopWidth"),10)||0)+(parseInt(t(e).css("paddingTop"),10)||0)-this.margins.top,i.left+(s?Math.max(e.scrollWidth,e.offsetWidth):e.offsetWidth)-(parseInt(t(e).css("borderLeftWidth"),10)||0)-(parseInt(t(e).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,i.top+(s?Math.max(e.scrollHeight,e.offsetHeight):e.offsetHeight)-(parseInt(t(e).css("borderTopWidth"),10)||0)-(parseInt(t(e).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top])},_convertPositionTo:function(e,i){i||(i=this.position);var s="absolute"===e?1:-1,n="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,o=/(html|body)/i.test(n[0].tagName);return{top:i.top+this.offset.relative.top*s+this.offset.parent.top*s-("fixed"===this.cssPosition?-this.scrollParent.scrollTop():o?0:n.scrollTop())*s,left:i.left+this.offset.relative.left*s+this.offset.parent.left*s-("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():o?0:n.scrollLeft())*s}},_generatePosition:function(e){var i,s,n=this.options,o=e.pageX,a=e.pageY,r="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,h=/(html|body)/i.test(r[0].tagName);return"relative"!==this.cssPosition||this.scrollParent[0]!==document&&this.scrollParent[0]!==this.offsetParent[0]||(this.offset.relative=this._getRelativeOffset()),this.originalPosition&&(this.containment&&(e.pageX-this.offset.click.left<this.containment[0]&&(o=this.containment[0]+this.offset.click.left),e.pageY-this.offset.click.top<this.containment[1]&&(a=this.containment[1]+this.offset.click.top),e.pageX-this.offset.click.left>this.containment[2]&&(o=this.containment[2]+this.offset.click.left),e.pageY-this.offset.click.top>this.containment[3]&&(a=this.containment[3]+this.offset.click.top)),n.grid&&(i=this.originalPageY+Math.round((a-this.originalPageY)/n.grid[1])*n.grid[1],a=this.containment?i-this.offset.click.top>=this.containment[1]&&i-this.offset.click.top<=this.containment[3]?i:i-this.offset.click.top>=this.containment[1]?i-n.grid[1]:i+n.grid[1]:i,s=this.originalPageX+Math.round((o-this.originalPageX)/n.grid[0])*n.grid[0],o=this.containment?s-this.offset.click.left>=this.containment[0]&&s-this.offset.click.left<=this.containment[2]?s:s-this.offset.click.left>=this.containment[0]?s-n.grid[0]:s+n.grid[0]:s)),{top:a-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.scrollParent.scrollTop():h?0:r.scrollTop()),left:o-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():h?0:r.scrollLeft())}},_rearrange:function(t,e,i,s){i?i[0].appendChild(this.placeholder[0]):e.item[0].parentNode.insertBefore(this.placeholder[0],"down"===this.direction?e.item[0]:e.item[0].nextSibling),this.counter=this.counter?++this.counter:1;var n=this.counter;this._delay(function(){n===this.counter&&this.refreshPositions(!s)})},_clear:function(t,e){function i(t,e,i){return function(s){i._trigger(t,s,e._uiHash(e))}}this.reverting=!1;var s,n=[];if(!this._noFinalSort&&this.currentItem.parent().length&&this.placeholder.before(this.currentItem),this._noFinalSort=null,this.helper[0]===this.currentItem[0]){for(s in this._storedCSS)("auto"===this._storedCSS[s]||"static"===this._storedCSS[s])&&(this._storedCSS[s]="");this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")}else this.currentItem.show();for(this.fromOutside&&!e&&n.push(function(t){this._trigger("receive",t,this._uiHash(this.fromOutside))}),!this.fromOutside&&this.domPosition.prev===this.currentItem.prev().not(".ui-sortable-helper")[0]&&this.domPosition.parent===this.currentItem.parent()[0]||e||n.push(function(t){this._trigger("update",t,this._uiHash())}),this!==this.currentContainer&&(e||(n.push(function(t){this._trigger("remove",t,this._uiHash())}),n.push(function(t){return function(e){t._trigger("receive",e,this._uiHash(this))}}.call(this,this.currentContainer)),n.push(function(t){return function(e){t._trigger("update",e,this._uiHash(this))}}.call(this,this.currentContainer)))),s=this.containers.length-1;s>=0;s--)e||n.push(i("deactivate",this,this.containers[s])),this.containers[s].containerCache.over&&(n.push(i("out",this,this.containers[s])),this.containers[s].containerCache.over=0);if(this.storedCursor&&(this.document.find("body").css("cursor",this.storedCursor),this.storedStylesheet.remove()),this._storedOpacity&&this.helper.css("opacity",this._storedOpacity),this._storedZIndex&&this.helper.css("zIndex","auto"===this._storedZIndex?"":this._storedZIndex),this.dragging=!1,this.cancelHelperRemoval){if(!e){for(this._trigger("beforeStop",t,this._uiHash()),s=0;n.length>s;s++)n[s].call(this,t);this._trigger("stop",t,this._uiHash())}return this.fromOutside=!1,!1}if(e||this._trigger("beforeStop",t,this._uiHash()),this.placeholder[0].parentNode.removeChild(this.placeholder[0]),this.helper[0]!==this.currentItem[0]&&this.helper.remove(),this.helper=null,!e){for(s=0;n.length>s;s++)n[s].call(this,t);this._trigger("stop",t,this._uiHash())}return this.fromOutside=!1,!0},_trigger:function(){t.Widget.prototype._trigger.apply(this,arguments)===!1&&this.cancel()},_uiHash:function(e){var i=e||this;return{helper:i.helper,placeholder:i.placeholder||t([]),position:i.position,originalPosition:i.originalPosition,offset:i.positionAbs,item:i.currentItem,sender:e?e.element:null}}})})(jQuery);
/*! jQuery Mobile v1.4.5 | Copyright 2010, 2014 jQuery Foundation, Inc. | jquery.org/license */

(function(e,t,n){typeof define=="function"&&define.amd?define(["jquery"],function(r){return n(r,e,t),r.mobile}):n(e.jQuery,e,t)})(this,document,function(e,t,n,r){(function(e,t,r){"$:nomunge";function l(e){return e=e||location.href,"#"+e.replace(/^[^#]*#?(.*)$/,"$1")}var i="hashchange",s=n,o,u=e.event.special,a=s.documentMode,f="on"+i in t&&(a===r||a>7);e.fn[i]=function(e){return e?this.bind(i,e):this.trigger(i)},e.fn[i].delay=50,u[i]=e.extend(u[i],{setup:function(){if(f)return!1;e(o.start)},teardown:function(){if(f)return!1;e(o.stop)}}),o=function(){function p(){var n=l(),r=h(u);n!==u?(c(u=n,r),e(t).trigger(i)):r!==u&&(location.href=location.href.replace(/#.*/,"")+r),o=setTimeout(p,e.fn[i].delay)}var n={},o,u=l(),a=function(e){return e},c=a,h=a;return n.start=function(){o||p()},n.stop=function(){o&&clearTimeout(o),o=r},t.attachEvent&&!t.addEventListener&&!f&&function(){var t,r;n.start=function(){t||(r=e.fn[i].src,r=r&&r+l(),t=e('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){r||c(l()),p()}).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow,s.onpropertychange=function(){try{event.propertyName==="title"&&(t.document.title=s.title)}catch(e){}})},n.stop=a,h=function(){return l(t.location.href)},c=function(n,r){var o=t.document,u=e.fn[i].domain;n!==r&&(o.title=s.title,o.open(),u&&o.write('<script>document.domain="'+u+'"</script>'),o.close(),t.location.hash=n)}}(),n}()})(e,this),function(e){e.mobile={}}(e),function(e,t,n){e.extend(e.mobile,{version:"1.4.5",subPageUrlKey:"ui-page",hideUrlBar:!0,keepNative:":jqmData(role='none'), :jqmData(role='nojs')",activePageClass:"ui-page-active",activeBtnClass:"ui-btn-active",focusClass:"ui-focus",ajaxEnabled:!0,hashListeningEnabled:!0,linkBindingEnabled:!0,defaultPageTransition:"fade",maxTransitionWidth:!1,minScrollBack:0,defaultDialogTransition:"pop",pageLoadErrorMessage:"Error Loading Page",pageLoadErrorMessageTheme:"a",phonegapNavigationEnabled:!1,autoInitializePage:!0,pushStateEnabled:!0,ignoreContentEnabled:!1,buttonMarkup:{hoverDelay:200},dynamicBaseEnabled:!0,pageContainer:e(),allowCrossDomainPages:!1,dialogHashKey:"&ui-state=dialog"})}(e,this),function(e,t,n){var r={},i=e.find,s=/(?:\{[\s\S]*\}|\[[\s\S]*\])$/,o=/:jqmData\(([^)]*)\)/g;e.extend(e.mobile,{ns:"",getAttribute:function(t,n){var r;t=t.jquery?t[0]:t,t&&t.getAttribute&&(r=t.getAttribute("data-"+e.mobile.ns+n));try{r=r==="true"?!0:r==="false"?!1:r==="null"?null:+r+""===r?+r:s.test(r)?JSON.parse(r):r}catch(i){}return r},nsNormalizeDict:r,nsNormalize:function(t){return r[t]||(r[t]=e.camelCase(e.mobile.ns+t))},closestPageData:function(e){return e.closest(":jqmData(role='page'), :jqmData(role='dialog')").data("mobile-page")}}),e.fn.jqmData=function(t,r){var i;return typeof t!="undefined"&&(t&&(t=e.mobile.nsNormalize(t)),arguments.length<2||r===n?i=this.data(t):i=this.data(t,r)),i},e.jqmData=function(t,n,r){var i;return typeof n!="undefined"&&(i=e.data(t,n?e.mobile.nsNormalize(n):n,r)),i},e.fn.jqmRemoveData=function(t){return this.removeData(e.mobile.nsNormalize(t))},e.jqmRemoveData=function(t,n){return e.removeData(t,e.mobile.nsNormalize(n))},e.find=function(t,n,r,s){return t.indexOf(":jqmData")>-1&&(t=t.replace(o,"[data-"+(e.mobile.ns||"")+"$1]")),i.call(this,t,n,r,s)},e.extend(e.find,i)}(e,this),function(e,t){function s(t,n){var r,i,s,u=t.nodeName.toLowerCase();return"area"===u?(r=t.parentNode,i=r.name,!t.href||!i||r.nodeName.toLowerCase()!=="map"?!1:(s=e("img[usemap=#"+i+"]")[0],!!s&&o(s))):(/input|select|textarea|button|object/.test(u)?!t.disabled:"a"===u?t.href||n:n)&&o(t)}function o(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return e.css(this,"visibility")==="hidden"}).length}var r=0,i=/^ui-id-\d+$/;e.ui=e.ui||{},e.extend(e.ui,{version:"c0ab71056b936627e8a7821f03c044aec6280a40",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({focus:function(t){return function(n,r){return typeof n=="number"?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),r&&r.call(t)},n)}):t.apply(this,arguments)}}(e.fn.focus),scrollParent:function(){var t;return e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?t=this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):t=this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(this[0].ownerDocument||n):t},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++r)})},removeUniqueId:function(){return this.each(function(){i.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(n){return!!e.data(n,t)}}):function(t,n,r){return!!e.data(t,r[3])},focusable:function(t){return s(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var n=e.attr(t,"tabindex"),r=isNaN(n);return(r||n>=0)&&s(t,!r)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(n,r){function u(t,n,r,s){return e.each(i,function(){n-=parseFloat(e.css(t,"padding"+this))||0,r&&(n-=parseFloat(e.css(t,"border"+this+"Width"))||0),s&&(n-=parseFloat(e.css(t,"margin"+this))||0)}),n}var i=r==="Width"?["Left","Right"]:["Top","Bottom"],s=r.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+r]=function(n){return n===t?o["inner"+r].call(this):this.each(function(){e(this).css(s,u(this,n)+"px")})},e.fn["outer"+r]=function(t,n){return typeof t!="number"?o["outer"+r].call(this,t):this.each(function(){e(this).css(s,u(this,t,!0,n)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(e==null?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(n){return arguments.length?t.call(this,e.camelCase(n)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.support.selectstart="onselectstart"in n.createElement("div"),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")},zIndex:function(r){if(r!==t)return this.css("zIndex",r);if(this.length){var i=e(this[0]),s,o;while(i.length&&i[0]!==n){s=i.css("position");if(s==="absolute"||s==="relative"||s==="fixed"){o=parseInt(i.css("zIndex"),10);if(!isNaN(o)&&o!==0)return o}i=i.parent()}}return 0}}),e.ui.plugin={add:function(t,n,r){var i,s=e.ui[t].prototype;for(i in r)s.plugins[i]=s.plugins[i]||[],s.plugins[i].push([n,r[i]])},call:function(e,t,n,r){var i,s=e.plugins[t];if(!s)return;if(!r&&(!e.element[0].parentNode||e.element[0].parentNode.nodeType===11))return;for(i=0;i<s.length;i++)e.options[s[i][0]]&&s[i][1].apply(e.element,n)}}}(e),function(e,t,r){var i=function(t,n){var r=t.parent(),i=[],s=function(){var t=e(this),n=e.mobile.toolbar&&t.data("mobile-toolbar")?t.toolbar("option"):{position:t.attr("data-"+e.mobile.ns+"position"),updatePagePadding:t.attr("data-"+e.mobile.ns+"update-page-padding")!==!1};return n.position!=="fixed"||n.updatePagePadding!==!0},o=r.children(":jqmData(role='header')").filter(s),u=t.children(":jqmData(role='header')"),a=r.children(":jqmData(role='footer')").filter(s),f=t.children(":jqmData(role='footer')");return u.length===0&&o.length>0&&(i=i.concat(o.toArray())),f.length===0&&a.length>0&&(i=i.concat(a.toArray())),e.each(i,function(t,r){n-=e(r).outerHeight()}),Math.max(0,n)};e.extend(e.mobile,{window:e(t),document:e(n),keyCode:e.ui.keyCode,behaviors:{},silentScroll:function(n){e.type(n)!=="number"&&(n=e.mobile.defaultHomeScroll),e.event.special.scrollstart.enabled=!1,setTimeout(function(){t.scrollTo(0,n),e.mobile.document.trigger("silentscroll",{x:0,y:n})},20),setTimeout(function(){e.event.special.scrollstart.enabled=!0},150)},getClosestBaseUrl:function(t){var n=e(t).closest(".ui-page").jqmData("url"),r=e.mobile.path.documentBase.hrefNoHash;if(!e.mobile.dynamicBaseEnabled||!n||!e.mobile.path.isPath(n))n=r;return e.mobile.path.makeUrlAbsolute(n,r)},removeActiveLinkClass:function(t){!!e.mobile.activeClickedLink&&(!e.mobile.activeClickedLink.closest("."+e.mobile.activePageClass).length||t)&&e.mobile.activeClickedLink.removeClass(e.mobile.activeBtnClass),e.mobile.activeClickedLink=null},getInheritedTheme:function(e,t){var n=e[0],r="",i=/ui-(bar|body|overlay)-([a-z])\b/,s,o;while(n){s=n.className||"";if(s&&(o=i.exec(s))&&(r=o[2]))break;n=n.parentNode}return r||t||"a"},enhanceable:function(e){return this.haveParents(e,"enhance")},hijackable:function(e){return this.haveParents(e,"ajax")},haveParents:function(t,n){if(!e.mobile.ignoreContentEnabled)return t;var r=t.length,i=e(),s,o,u,a,f;for(a=0;a<r;a++){o=t.eq(a),u=!1,s=t[a];while(s){f=s.getAttribute?s.getAttribute("data-"+e.mobile.ns+n):"";if(f==="false"){u=!0;break}s=s.parentNode}u||(i=i.add(o))}return i},getScreenHeight:function(){return t.innerHeight||e.mobile.window.height()},resetActivePageHeight:function(t){var n=e("."+e.mobile.activePageClass),r=n.height(),s=n.outerHeight(!0);t=i(n,typeof t=="number"?t:e.mobile.getScreenHeight()),n.css("min-height",""),n.height()<t&&n.css("min-height",t-(s-r))},loading:function(){var t=this.loading._widget||e(e.mobile.loader.prototype.defaultHtml).loader(),n=t.loader.apply(t,arguments);return this.loading._widget=t,n}}),e.addDependents=function(t,n){var r=e(t),i=r.jqmData("dependents")||e();r.jqmData("dependents",e(i).add(n))},e.fn.extend({removeWithDependents:function(){e.removeWithDependents(this)},enhanceWithin:function(){var t,n={},r=e.mobile.page.prototype.keepNativeSelector(),i=this;e.mobile.nojs&&e.mobile.nojs(this),e.mobile.links&&e.mobile.links(this),e.mobile.degradeInputsWithin&&e.mobile.degradeInputsWithin(this),e.fn.buttonMarkup&&this.find(e.fn.buttonMarkup.initSelector).not(r).jqmEnhanceable().buttonMarkup(),e.fn.fieldcontain&&this.find(":jqmData(role='fieldcontain')").not(r).jqmEnhanceable().fieldcontain(),e.each(e.mobile.widgets,function(t,s){if(s.initSelector){var o=e.mobile.enhanceable(i.find(s.initSelector));o.length>0&&(o=o.not(r)),o.length>0&&(n[s.prototype.widgetName]=o)}});for(t in n)n[t][t]();return this},addDependents:function(t){e.addDependents(this,t)},getEncodedText:function(){return e("<a>").text(this.text()).html()},jqmEnhanceable:function(){return e.mobile.enhanceable(this)},jqmHijackable:function(){return e.mobile.hijackable(this)}}),e.removeWithDependents=function(t){var n=e(t);(n.jqmData("dependents")||e()).remove(),n.remove()},e.addDependents=function(t,n){var r=e(t),i=r.jqmData("dependents")||e();r.jqmData("dependents",e(i).add(n))},e.find.matches=function(t,n){return e.find(t,null,null,n)},e.find.matchesSelector=function(t,n){return e.find(n,null,null,[t]).length>0}}(e,this),function(e,r){t.matchMedia=t.matchMedia||function(e,t){var n,r=e.documentElement,i=r.firstElementChild||r.firstChild,s=e.createElement("body"),o=e.createElement("div");return o.id="mq-test-1",o.style.cssText="position:absolute;top:-100em",s.style.background="none",s.appendChild(o),function(e){return o.innerHTML='&shy;<style media="'+e+'"> #mq-test-1 { width: 42px; }</style>',r.insertBefore(s,i),n=o.offsetWidth===42,r.removeChild(s),{matches:n,media:e}}}(n),e.mobile.media=function(e){return t.matchMedia(e).matches}}(e),function(e,t){var r={touch:"ontouchend"in n};e.mobile.support=e.mobile.support||{},e.extend(e.support,r),e.extend(e.mobile.support,r)}(e),function(e,n){e.extend(e.support,{orientation:"orientation"in t&&"onorientationchange"in t})}(e),function(e,r){function i(e){var t=e.charAt(0).toUpperCase()+e.substr(1),n=(e+" "+u.join(t+" ")+t).split(" "),i;for(i in n)if(o[n[i]]!==r)return!0}function h(){var n=t,r=!!n.document.createElementNS&&!!n.document.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect&&(!n.opera||navigator.userAgent.indexOf("Chrome")!==-1),i=function(t){(!t||!r)&&e("html").addClass("ui-nosvg")},s=new n.Image;s.onerror=function(){i(!1)},s.onload=function(){i(s.width===1&&s.height===1)},s.src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="}function p(){var i="transform-3d",o=e.mobile.media("(-"+u.join("-"+i+"),(-")+"-"+i+"),("+i+")"),a,f,l;if(o)return!!o;a=n.createElement("div"),f={MozTransform:"-moz-transform",transform:"transform"},s.append(a);for(l in f)a.style[l]!==r&&(a.style[l]="translate3d( 100px, 1px, 1px )",o=t.getComputedStyle(a).getPropertyValue(f[l]));return!!o&&o!=="none"}function d(){var t=location.protocol+"//"+location.host+location.pathname+"ui-dir/",n=e("head base"),r=null,i="",o,u;return n.length?i=n.attr("href"):n=r=e("<base>",{href:t}).appendTo("head"),o=e("<a href='testurl' />").prependTo(s),u=o[0].href,n[0].href=i||location.pathname,r&&r.remove(),u.indexOf(t)===0}function v(){var e=n.createElement("x"),r=n.documentElement,i=t.getComputedStyle,s;return"pointerEvents"in e.style?(e.style.pointerEvents="auto",e.style.pointerEvents="x",r.appendChild(e),s=i&&i(e,"").pointerEvents==="auto",r.removeChild(e),!!s):!1}function m(){var e=n.createElement("div");return typeof e.getBoundingClientRect!="undefined"}function g(){var e=t,n=navigator.userAgent,r=navigator.platform,i=n.match(/AppleWebKit\/([0-9]+)/),s=!!i&&i[1],o=n.match(/Fennec\/([0-9]+)/),u=!!o&&o[1],a=n.match(/Opera Mobi\/([0-9]+)/),f=!!a&&a[1];return(r.indexOf("iPhone")>-1||r.indexOf("iPad")>-1||r.indexOf("iPod")>-1)&&s&&s<534||e.operamini&&{}.toString.call(e.operamini)==="[object OperaMini]"||a&&f<7458||n.indexOf("Android")>-1&&s&&s<533||u&&u<6||"palmGetResource"in t&&s&&s<534||n.indexOf("MeeGo")>-1&&n.indexOf("NokiaBrowser/8.5.0")>-1?!1:!0}var s=e("<body>").prependTo("html"),o=s[0].style,u=["Webkit","Moz","O"],a="palmGetResource"in t,f=t.operamini&&{}.toString.call(t.operamini)==="[object OperaMini]",l=t.blackberry&&!i("-webkit-transform"),c;e.extend(e.mobile,{browser:{}}),e.mobile.browser.oldIE=function(){var e=3,t=n.createElement("div"),r=t.all||[];do t.innerHTML="<!--[if gt IE "+ ++e+"]><br><![endif]-->";while(r[0]);return e>4?e:!e}(),e.extend(e.support,{pushState:"pushState"in history&&"replaceState"in history&&!(t.navigator.userAgent.indexOf("Firefox")>=0&&t.top!==t)&&t.navigator.userAgent.search(/CriOS/)===-1,mediaquery:e.mobile.media("only all"),cssPseudoElement:!!i("content"),touchOverflow:!!i("overflowScrolling"),cssTransform3d:p(),boxShadow:!!i("boxShadow")&&!l,fixedPosition:g(),scrollTop:("pageXOffset"in t||"scrollTop"in n.documentElement||"scrollTop"in s[0])&&!a&&!f,dynamicBaseTag:d(),cssPointerEvents:v(),boundingRect:m(),inlineSVG:h}),s.remove(),c=function(){var e=t.navigator.userAgent;return e.indexOf("Nokia")>-1&&(e.indexOf("Symbian/3")>-1||e.indexOf("Series60/5")>-1)&&e.indexOf("AppleWebKit")>-1&&e.match(/(BrowserNG|NokiaBrowser)\/7\.[0-3]/)}(),e.mobile.gradeA=function(){return(e.support.mediaquery&&e.support.cssPseudoElement||e.mobile.browser.oldIE&&e.mobile.browser.oldIE>=8)&&(e.support.boundingRect||e.fn.jquery.match(/1\.[0-7+]\.[0-9+]?/)!==null)},e.mobile.ajaxBlacklist=t.blackberry&&!t.WebKitPoint||f||c,c&&e(function(){e("head link[rel='stylesheet']").attr("rel","alternate stylesheet").attr("rel","stylesheet")}),e.support.boxShadow||e("html").addClass("ui-noboxshadow")}(e),function(e,t){var n=e.mobile.window,r,i=function(){};e.event.special.beforenavigate={setup:function(){n.on("navigate",i)},teardown:function(){n.off("navigate",i)}},e.event.special.navigate=r={bound:!1,pushStateEnabled:!0,originalEventName:t,isPushStateEnabled:function(){return e.support.pushState&&e.mobile.pushStateEnabled===!0&&this.isHashChangeEnabled()},isHashChangeEnabled:function(){return e.mobile.hashListeningEnabled===!0},popstate:function(t){var r=new e.Event("navigate"),i=new e.Event("beforenavigate"),s=t.originalEvent.state||{};i.originalEvent=t,n.trigger(i);if(i.isDefaultPrevented())return;t.historyState&&e.extend(s,t.historyState),r.originalEvent=t,setTimeout(function(){n.trigger(r,{state:s})},0)},hashchange:function(t){var r=new e.Event("navigate"),i=new e.Event("beforenavigate");i.originalEvent=t,n.trigger(i);if(i.isDefaultPrevented())return;r.originalEvent=t,n.trigger(r,{state:t.hashchangeState||{}})},setup:function(){if(r.bound)return;r.bound=!0,r.isPushStateEnabled()?(r.originalEventName="popstate",n.bind("popstate.navigate",r.popstate)):r.isHashChangeEnabled()&&(r.originalEventName="hashchange",n.bind("hashchange.navigate",r.hashchange))}}}(e),function(e){e.event.special.throttledresize={setup:function(){e(this).bind("resize",n)},teardown:function(){e(this).unbind("resize",n)}};var t=250,n=function(){s=(new Date).getTime(),o=s-r,o>=t?(r=s,e(this).trigger("throttledresize")):(i&&clearTimeout(i),i=setTimeout(n,t-o))},r=0,i,s,o}(e),function(e,t){function p(){var e=s();e!==o&&(o=e,r.trigger(i))}var r=e(t),i="orientationchange",s,o,u,a,f={0:!0,180:!0},l,c,h;if(e.support.orientation){l=t.innerWidth||r.width(),c=t.innerHeight||r.height(),h=50,u=l>c&&l-c>h,a=f[t.orientation];if(u&&a||!u&&!a)f={"-90":!0,90:!0}}e.event.special.orientationchange=e.extend({},e.event.special.orientationchange,{setup:function(){if(e.support.orientation&&!e.event.special.orientationchange.disabled)return!1;o=s(),r.bind("throttledresize",p)},teardown:function(){if(e.support.orientation&&!e.event.special.orientationchange.disabled)return!1;r.unbind("throttledresize",p)},add:function(e){var t=e.handler;e.handler=function(e){return e.orientation=s(),t.apply(this,arguments)}}}),e.event.special.orientationchange.orientation=s=function(){var r=!0,i=n.documentElement;return e.support.orientation?r=f[t.orientation]:r=i&&i.clientWidth/i.clientHeight<1.1,r?"portrait":"landscape"},e.fn[i]=function(e){return e?this.bind(i,e):this.trigger(i)},e.attrFn&&(e.attrFn[i]=!0)}(e,this),function(e,t,n,r){function T(e){while(e&&typeof e.originalEvent!="undefined")e=e.originalEvent;return e}function N(t,n){var i=t.type,s,o,a,l,c,h,p,d,v;t=e.Event(t),t.type=n,s=t.originalEvent,o=e.event.props,i.search(/^(mouse|click)/)>-1&&(o=f);if(s)for(p=o.length,l;p;)l=o[--p],t[l]=s[l];i.search(/mouse(down|up)|click/)>-1&&!t.which&&(t.which=1);if(i.search(/^touch/)!==-1){a=T(s),i=a.touches,c=a.changedTouches,h=i&&i.length?i[0]:c&&c.length?c[0]:r;if(h)for(d=0,v=u.length;d<v;d++)l=u[d],t[l]=h[l]}return t}function C(t){var n={},r,s;while(t){r=e.data(t,i);for(s in r)r[s]&&(n[s]=n.hasVirtualBinding=!0);t=t.parentNode}return n}function k(t,n){var r;while(t){r=e.data(t,i);if(r&&(!n||r[n]))return t;t=t.parentNode}return null}function L(){g=!1}function A(){g=!0}function O(){E=0,v.length=0,m=!1,A()}function M(){L()}function _(){D(),c=setTimeout(function(){c=0,O()},e.vmouse.resetTimerDuration)}function D(){c&&(clearTimeout(c),c=0)}function P(t,n,r){var i;if(r&&r[t]||!r&&k(n.target,t))i=N(n,t),e(n.target).trigger(i);return i}function H(t){var n=e.data(t.target,s),r;!m&&(!E||E!==n)&&(r=P("v"+t.type,t),r&&(r.isDefaultPrevented()&&t.preventDefault(),r.isPropagationStopped()&&t.stopPropagation(),r.isImmediatePropagationStopped()&&t.stopImmediatePropagation()))}function B(t){var n=T(t).touches,r,i,o;n&&n.length===1&&(r=t.target,i=C(r),i.hasVirtualBinding&&(E=w++,e.data(r,s,E),D(),M(),d=!1,o=T(t).touches[0],h=o.pageX,p=o.pageY,P("vmouseover",t,i),P("vmousedown",t,i)))}function j(e){if(g)return;d||P("vmousecancel",e,C(e.target)),d=!0,_()}function F(t){if(g)return;var n=T(t).touches[0],r=d,i=e.vmouse.moveDistanceThreshold,s=C(t.target);d=d||Math.abs(n.pageX-h)>i||Math.abs(n.pageY-p)>i,d&&!r&&P("vmousecancel",t,s),P("vmousemove",t,s),_()}function I(e){if(g)return;A();var t=C(e.target),n,r;P("vmouseup",e,t),d||(n=P("vclick",e,t),n&&n.isDefaultPrevented()&&(r=T(e).changedTouches[0],v.push({touchID:E,x:r.clientX,y:r.clientY}),m=!0)),P("vmouseout",e,t),d=!1,_()}function q(t){var n=e.data(t,i),r;if(n)for(r in n)if(n[r])return!0;return!1}function R(){}function U(t){var n=t.substr(1);return{setup:function(){q(this)||e.data(this,i,{});var r=e.data(this,i);r[t]=!0,l[t]=(l[t]||0)+1,l[t]===1&&b.bind(n,H),e(this).bind(n,R),y&&(l.touchstart=(l.touchstart||0)+1,l.touchstart===1&&b.bind("touchstart",B).bind("touchend",I).bind("touchmove",F).bind("scroll",j))},teardown:function(){--l[t],l[t]||b.unbind(n,H),y&&(--l.touchstart,l.touchstart||b.unbind("touchstart",B).unbind("touchmove",F).unbind("touchend",I).unbind("scroll",j));var r=e(this),s=e.data(this,i);s&&(s[t]=!1),r.unbind(n,R),q(this)||r.removeData(i)}}}var i="virtualMouseBindings",s="virtualTouchID",o="vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),u="clientX clientY pageX pageY screenX screenY".split(" "),a=e.event.mouseHooks?e.event.mouseHooks.props:[],f=e.event.props.concat(a),l={},c=0,h=0,p=0,d=!1,v=[],m=!1,g=!1,y="addEventListener"in n,b=e(n),w=1,E=0,S,x;e.vmouse={moveDistanceThreshold:10,clickDistanceThreshold:10,resetTimerDuration:1500};for(x=0;x<o.length;x++)e.event.special[o[x]]=U(o[x]);y&&n.addEventListener("click",function(t){var n=v.length,r=t.target,i,o,u,a,f,l;if(n){i=t.clientX,o=t.clientY,S=e.vmouse.clickDistanceThreshold,u=r;while(u){for(a=0;a<n;a++){f=v[a],l=0;if(u===r&&Math.abs(f.x-i)<S&&Math.abs(f.y-o)<S||e.data(u,s)===f.touchID){t.preventDefault(),t.stopPropagation();return}}u=u.parentNode}}},!0)}(e,t,n),function(e,t,r){function l(t,n,i,s){var o=i.type;i.type=n,s?e.event.trigger(i,r,t):e.event.dispatch.call(t,i),i.type=o}var i=e(n),s=e.mobile.support.touch,o="touchmove scroll",u=s?"touchstart":"mousedown",a=s?"touchend":"mouseup",f=s?"touchmove":"mousemove";e.each("touchstart touchmove touchend tap taphold swipe swipeleft swiperight scrollstart scrollstop".split(" "),function(t,n){e.fn[n]=function(e){return e?this.bind(n,e):this.trigger(n)},e.attrFn&&(e.attrFn[n]=!0)}),e.event.special.scrollstart={enabled:!0,setup:function(){function s(e,n){r=n,l(t,r?"scrollstart":"scrollstop",e)}var t=this,n=e(t),r,i;n.bind(o,function(t){if(!e.event.special.scrollstart.enabled)return;r||s(t,!0),clearTimeout(i),i=setTimeout(function(){s(t,!1)},50)})},teardown:function(){e(this).unbind(o)}},e.event.special.tap={tapholdThreshold:750,emitTapOnTaphold:!0,setup:function(){var t=this,n=e(t),r=!1;n.bind("vmousedown",function(s){function a(){clearTimeout(u)}function f(){a(),n.unbind("vclick",c).unbind("vmouseup",a),i.unbind("vmousecancel",f)}function c(e){f(),!r&&o===e.target?l(t,"tap",e):r&&e.preventDefault()}r=!1;if(s.which&&s.which!==1)return!1;var o=s.target,u;n.bind("vmouseup",a).bind("vclick",c),i.bind("vmousecancel",f),u=setTimeout(function(){e.event.special.tap.emitTapOnTaphold||(r=!0),l(t,"taphold",e.Event("taphold",{target:o}))},e.event.special.tap.tapholdThreshold)})},teardown:function(){e(this).unbind("vmousedown").unbind("vclick").unbind("vmouseup"),i.unbind("vmousecancel")}},e.event.special.swipe={scrollSupressionThreshold:30,durationThreshold:1e3,horizontalDistanceThreshold:30,verticalDistanceThreshold:30,getLocation:function(e){var n=t.pageXOffset,r=t.pageYOffset,i=e.clientX,s=e.clientY;if(e.pageY===0&&Math.floor(s)>Math.floor(e.pageY)||e.pageX===0&&Math.floor(i)>Math.floor(e.pageX))i-=n,s-=r;else if(s<e.pageY-r||i<e.pageX-n)i=e.pageX-n,s=e.pageY-r;return{x:i,y:s}},start:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y],origin:e(t.target)}},stop:function(t){var n=t.originalEvent.touches?t.originalEvent.touches[0]:t,r=e.event.special.swipe.getLocation(n);return{time:(new Date).getTime(),coords:[r.x,r.y]}},handleSwipe:function(t,n,r,i){if(n.time-t.time<e.event.special.swipe.durationThreshold&&Math.abs(t.coords[0]-n.coords[0])>e.event.special.swipe.horizontalDistanceThreshold&&Math.abs(t.coords[1]-n.coords[1])<e.event.special.swipe.verticalDistanceThreshold){var s=t.coords[0]>n.coords[0]?"swipeleft":"swiperight";return l(r,"swipe",e.Event("swipe",{target:i,swipestart:t,swipestop:n}),!0),l(r,s,e.Event(s,{target:i,swipestart:t,swipestop:n}),!0),!0}return!1},eventInProgress:!1,setup:function(){var t,n=this,r=e(n),s={};t=e.data(this,"mobile-events"),t||(t={length:0},e.data(this,"mobile-events",t)),t.length++,t.swipe=s,s.start=function(t){if(e.event.special.swipe.eventInProgress)return;e.event.special.swipe.eventInProgress=!0;var r,o=e.event.special.swipe.start(t),u=t.target,l=!1;s.move=function(t){if(!o||t.isDefaultPrevented())return;r=e.event.special.swipe.stop(t),l||(l=e.event.special.swipe.handleSwipe(o,r,n,u),l&&(e.event.special.swipe.eventInProgress=!1)),Math.abs(o.coords[0]-r.coords[0])>e.event.special.swipe.scrollSupressionThreshold&&t.preventDefault()},s.stop=function(){l=!0,e.event.special.swipe.eventInProgress=!1,i.off(f,s.move),s.move=null},i.on(f,s.move).one(a,s.stop)},r.on(u,s.start)},teardown:function(){var t,n;t=e.data(this,"mobile-events"),t&&(n=t.swipe,delete t.swipe,t.length--,t.length===0&&e.removeData(this,"mobile-events")),n&&(n.start&&e(this).off(u,n.start),n.move&&i.off(f,n.move),n.stop&&i.off(a,n.stop))}},e.each({scrollstop:"scrollstart",taphold:"tap",swipeleft:"swipe.left",swiperight:"swipe.right"},function(t,n){e.event.special[t]={setup:function(){e(this).bind(n,e.noop)},teardown:function(){e(this).unbind(n)}}})}(e,this),function(e,t){var r={animation:{},transition:{}},i=n.createElement("a"),s=["","webkit-","moz-","o-"];e.each(["animation","transition"],function(n,o){var u=n===0?o+"-"+"name":o;e.each(s,function(n,s){if(i.style[e.camelCase(s+u)]!==t)return r[o].prefix=s,!1}),r[o].duration=e.camelCase(r[o].prefix+o+"-"+"duration"),r[o].event=e.camelCase(r[o].prefix+o+"-"+"end"),r[o].prefix===""&&(r[o].event=r[o].event.toLowerCase())}),e.support.cssTransitions=r.transition.prefix!==t,e.support.cssAnimations=r.animation.prefix!==t,e(i).remove(),e.fn.animationComplete=function(i,s,o){var u,a,f=this,l=function(){clearTimeout(u),i.apply(this,arguments)},c=!s||s==="animation"?"animation":"transition";if(e.support.cssTransitions&&c==="transition"||e.support.cssAnimations&&c==="animation"){if(o===t){e(this).context!==n&&(a=parseFloat(e(this).css(r[c].duration))*3e3);if(a===0||a===t||isNaN(a))a=e.fn.animationComplete.defaultDuration}return u=setTimeout(function(){e(f).off(r[c].event,l),i.apply(f)},a),e(this).one(r[c].event,l)}return setTimeout(e.proxy(i,this),0),e(this)},e.fn.animationComplete.defaultDuration=1e3}(e)});
/**
 * Copyright (c) 2007-2015 Ariel Flesler - aflesler ○ gmail • com | http://flesler.blogspot.com
 * Licensed under MIT
 * @author Ariel Flesler
 * @version 2.1.3
 */
;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});
/*
  SlidesJS 3.0.4 http://slidesjs.com
  (c) 2013 by Nathan Searles http://nathansearles.com
  Updated: June 26th, 2013
  Apache License: http://www.apache.org/licenses/LICENSE-2.0
*/
(function(){(function(e,t,n){var r,i,s;s="slidesjs";i={width:940,height:528,start:1,navigation:{active:!0,effect:"slide"},pagination:{active:!0,effect:"slide"},play:{active:!1,effect:"slide",interval:5e3,auto:!1,swap:!0,pauseOnHover:!1,restartDelay:2500},effect:{slide:{speed:500},fade:{speed:300,crossfade:!0}},callback:{loaded:function(){},start:function(){},complete:function(){}}};r=function(){function t(t,n){this.element=t;this.options=e.extend(!0,{},i,n);this._defaults=i;this._name=s;this.init()}return t}();r.prototype.init=function(){var n,r,i,s,o,u,a=this;n=e(this.element);this.data=e.data(this);e.data(this,"animating",!1);e.data(this,"total",n.children().not(".slidesjs-navigation",n).length);e.data(this,"current",this.options.start-1);e.data(this,"vendorPrefix",this._getVendorPrefix());if(typeof TouchEvent!="undefined"){e.data(this,"touch",!0);this.options.effect.slide.speed=this.options.effect.slide.speed/2}n.css({overflow:"hidden"});n.slidesContainer=n.children().not(".slidesjs-navigation",n).wrapAll("<div class='slidesjs-container'>",n).parent().css({overflow:"hidden",position:"relative"});e(".slidesjs-container",n).wrapInner("<div class='slidesjs-control'>",n).children();e(".slidesjs-control",n).css({position:"relative",left:0});e(".slidesjs-control",n).children().addClass("slidesjs-slide").css({position:"absolute",top:0,left:0,width:"100%",zIndex:0,display:"none",webkitBackfaceVisibility:"hidden"});e.each(e(".slidesjs-control",n).children(),function(t){var n;n=e(this);return n.attr("slidesjs-index",t)});if(this.data.touch){e(".slidesjs-control",n).on("touchstart",function(e){return a._touchstart(e)});e(".slidesjs-control",n).on("touchmove",function(e){return a._touchmove(e)});e(".slidesjs-control",n).on("touchend",function(e){return a._touchend(e)})}n.fadeIn(0);this.update();this.data.touch&&this._setuptouch();e(".slidesjs-control",n).children(":eq("+this.data.current+")").eq(0).fadeIn(0,function(){return e(this).css({zIndex:10})});if(this.options.navigation.active){o=e("<a>",{"class":"slidesjs-previous slidesjs-navigation",href:"#",title:"Previous",text:"Previous"}).appendTo(n);r=e("<a>",{"class":"slidesjs-next slidesjs-navigation",href:"#",title:"Next",text:"Next"}).appendTo(n)}e(".slidesjs-next",n).click(function(e){e.preventDefault();a.stop(!0);return a.next(a.options.navigation.effect)});e(".slidesjs-previous",n).click(function(e){e.preventDefault();a.stop(!0);return a.previous(a.options.navigation.effect)});if(this.options.play.active){s=e("<a>",{"class":"slidesjs-play slidesjs-navigation",href:"#",title:"Play",text:"Play"}).appendTo(n);u=e("<a>",{"class":"slidesjs-stop slidesjs-navigation",href:"#",title:"Stop",text:"Stop"}).appendTo(n);s.click(function(e){e.preventDefault();return a.play(!0)});u.click(function(e){e.preventDefault();return a.stop(!0)});this.options.play.swap&&u.css({display:"none"})}if(this.options.pagination.active){i=e("<ul>",{"class":"slidesjs-pagination"}).appendTo(n);e.each(new Array(this.data.total),function(t){var n,r;n=e("<li>",{"class":"slidesjs-pagination-item"}).appendTo(i);r=e("<a>",{href:"#","data-slidesjs-item":t,html:t+1}).appendTo(n);return r.click(function(t){t.preventDefault();a.stop(!0);return a.goto(e(t.currentTarget).attr("data-slidesjs-item")*1+1)})})}e(t).bind("resize",function(){return a.update()});this._setActive();this.options.play.auto&&this.play();return this.options.callback.loaded(this.options.start)};r.prototype._setActive=function(t){var n,r;n=e(this.element);this.data=e.data(this);r=t>-1?t:this.data.current;e(".active",n).removeClass("active");return e(".slidesjs-pagination li:eq("+r+") a",n).addClass("active")};r.prototype.update=function(){var t,n,r;t=e(this.element);this.data=e.data(this);e(".slidesjs-control",t).children(":not(:eq("+this.data.current+"))").css({display:"none",left:0,zIndex:0});r=t.width();n=this.options.height/this.options.width*r;this.options.width=r;this.options.height=n;return e(".slidesjs-control, .slidesjs-container",t).css({width:r,height:n})};r.prototype.next=function(t){var n;n=e(this.element);this.data=e.data(this);e.data(this,"direction","next");t===void 0&&(t=this.options.navigation.effect);return t==="fade"?this._fade():this._slide()};r.prototype.previous=function(t){var n;n=e(this.element);this.data=e.data(this);e.data(this,"direction","previous");t===void 0&&(t=this.options.navigation.effect);return t==="fade"?this._fade():this._slide()};r.prototype.goto=function(t){var n,r;n=e(this.element);this.data=e.data(this);r===void 0&&(r=this.options.pagination.effect);t>this.data.total?t=this.data.total:t<1&&(t=1);if(typeof t=="number")return r==="fade"?this._fade(t):this._slide(t);if(typeof t=="string"){if(t==="first")return r==="fade"?this._fade(0):this._slide(0);if(t==="last")return r==="fade"?this._fade(this.data.total):this._slide(this.data.total)}};r.prototype._setuptouch=function(){var t,n,r,i;t=e(this.element);this.data=e.data(this);i=e(".slidesjs-control",t);n=this.data.current+1;r=this.data.current-1;r<0&&(r=this.data.total-1);n>this.data.total-1&&(n=0);i.children(":eq("+n+")").css({display:"block",left:this.options.width});return i.children(":eq("+r+")").css({display:"block",left:-this.options.width})};r.prototype._touchstart=function(t){var n,r;n=e(this.element);this.data=e.data(this);r=t.originalEvent.touches[0];this._setuptouch();e.data(this,"touchtimer",Number(new Date));e.data(this,"touchstartx",r.pageX);e.data(this,"touchstarty",r.pageY);return t.stopPropagation()};r.prototype._touchend=function(t){var n,r,i,s,o,u,a,f=this;n=e(this.element);this.data=e.data(this);u=t.originalEvent.touches[0];s=e(".slidesjs-control",n);if(s.position().left>this.options.width*.5||s.position().left>this.options.width*.1&&Number(new Date)-this.data.touchtimer<250){e.data(this,"direction","previous");this._slide()}else if(s.position().left<-(this.options.width*.5)||s.position().left<-(this.options.width*.1)&&Number(new Date)-this.data.touchtimer<250){e.data(this,"direction","next");this._slide()}else{i=this.data.vendorPrefix;a=i+"Transform";r=i+"TransitionDuration";o=i+"TransitionTimingFunction";s[0].style[a]="translateX(0px)";s[0].style[r]=this.options.effect.slide.speed*.85+"ms"}s.on("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd",function(){i=f.data.vendorPrefix;a=i+"Transform";r=i+"TransitionDuration";o=i+"TransitionTimingFunction";s[0].style[a]="";s[0].style[r]="";return s[0].style[o]=""});return t.stopPropagation()};r.prototype._touchmove=function(t){var n,r,i,s,o;n=e(this.element);this.data=e.data(this);s=t.originalEvent.touches[0];r=this.data.vendorPrefix;i=e(".slidesjs-control",n);o=r+"Transform";e.data(this,"scrolling",Math.abs(s.pageX-this.data.touchstartx)<Math.abs(s.pageY-this.data.touchstarty));if(!this.data.animating&&!this.data.scrolling){t.preventDefault();this._setuptouch();i[0].style[o]="translateX("+(s.pageX-this.data.touchstartx)+"px)"}return t.stopPropagation()};r.prototype.play=function(t){var n,r,i,s=this;n=e(this.element);this.data=e.data(this);if(!this.data.playInterval){if(t){r=this.data.current;this.data.direction="next";this.options.play.effect==="fade"?this._fade():this._slide()}e.data(this,"playInterval",setInterval(function(){r=s.data.current;s.data.direction="next";return s.options.play.effect==="fade"?s._fade():s._slide()},this.options.play.interval));i=e(".slidesjs-container",n);if(this.options.play.pauseOnHover){i.unbind();i.bind("mouseenter",function(){return s.stop()});i.bind("mouseleave",function(){return s.options.play.restartDelay?e.data(s,"restartDelay",setTimeout(function(){return s.play(!0)},s.options.play.restartDelay)):s.play()})}e.data(this,"playing",!0);e(".slidesjs-play",n).addClass("slidesjs-playing");if(this.options.play.swap){e(".slidesjs-play",n).hide();return e(".slidesjs-stop",n).show()}}};r.prototype.stop=function(t){var n;n=e(this.element);this.data=e.data(this);clearInterval(this.data.playInterval);this.options.play.pauseOnHover&&t&&e(".slidesjs-container",n).unbind();e.data(this,"playInterval",null);e.data(this,"playing",!1);e(".slidesjs-play",n).removeClass("slidesjs-playing");if(this.options.play.swap){e(".slidesjs-stop",n).hide();return e(".slidesjs-play",n).show()}};r.prototype._slide=function(t){var n,r,i,s,o,u,a,f,l,c,h=this;n=e(this.element);this.data=e.data(this);if(!this.data.animating&&t!==this.data.current+1){e.data(this,"animating",!0);r=this.data.current;if(t>-1){t-=1;c=t>r?1:-1;i=t>r?-this.options.width:this.options.width;o=t}else{c=this.data.direction==="next"?1:-1;i=this.data.direction==="next"?-this.options.width:this.options.width;o=r+c}o===-1&&(o=this.data.total-1);o===this.data.total&&(o=0);this._setActive(o);a=e(".slidesjs-control",n);t>-1&&a.children(":not(:eq("+r+"))").css({display:"none",left:0,zIndex:0});a.children(":eq("+o+")").css({display:"block",left:c*this.options.width,zIndex:10});this.options.callback.start(r+1);if(this.data.vendorPrefix){u=this.data.vendorPrefix;l=u+"Transform";s=u+"TransitionDuration";f=u+"TransitionTimingFunction";a[0].style[l]="translateX("+i+"px)";a[0].style[s]=this.options.effect.slide.speed+"ms";return a.on("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd",function(){a[0].style[l]="";a[0].style[s]="";a.children(":eq("+o+")").css({left:0});a.children(":eq("+r+")").css({display:"none",left:0,zIndex:0});e.data(h,"current",o);e.data(h,"animating",!1);a.unbind("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd");a.children(":not(:eq("+o+"))").css({display:"none",left:0,zIndex:0});h.data.touch&&h._setuptouch();return h.options.callback.complete(o+1)})}return a.stop().animate({left:i},this.options.effect.slide.speed,function(){a.css({left:0});a.children(":eq("+o+")").css({left:0});return a.children(":eq("+r+")").css({display:"none",left:0,zIndex:0},e.data(h,"current",o),e.data(h,"animating",!1),h.options.callback.complete(o+1))})}};r.prototype._fade=function(t){var n,r,i,s,o,u=this;n=e(this.element);this.data=e.data(this);if(!this.data.animating&&t!==this.data.current+1){e.data(this,"animating",!0);r=this.data.current;if(t){t-=1;o=t>r?1:-1;i=t}else{o=this.data.direction==="next"?1:-1;i=r+o}i===-1&&(i=this.data.total-1);i===this.data.total&&(i=0);this._setActive(i);s=e(".slidesjs-control",n);s.children(":eq("+i+")").css({display:"none",left:0,zIndex:10});this.options.callback.start(r+1);if(this.options.effect.fade.crossfade){s.children(":eq("+this.data.current+")").stop().fadeOut(this.options.effect.fade.speed);return s.children(":eq("+i+")").stop().fadeIn(this.options.effect.fade.speed,function(){s.children(":eq("+i+")").css({zIndex:0});e.data(u,"animating",!1);e.data(u,"current",i);return u.options.callback.complete(i+1)})}return s.children(":eq("+r+")").stop().fadeOut(this.options.effect.fade.speed,function(){s.children(":eq("+i+")").stop().fadeIn(u.options.effect.fade.speed,function(){return s.children(":eq("+i+")").css({zIndex:10})});e.data(u,"animating",!1);e.data(u,"current",i);return u.options.callback.complete(i+1)})}};r.prototype._getVendorPrefix=function(){var e,t,r,i,s;e=n.body||n.documentElement;r=e.style;i="transition";s=["Moz","Webkit","Khtml","O","ms"];i=i.charAt(0).toUpperCase()+i.substr(1);t=0;while(t<s.length){if(typeof r[s[t]+i]=="string")return s[t];t++}return!1};return e.fn[s]=function(t){return this.each(function(){if(!e.data(this,"plugin_"+s))return e.data(this,"plugin_"+s,new r(this,t))})}})(jQuery,window,document)}).call(this);

// Slidebars 0.10.3 (http://plugins.adchsm.me/slidebars/) written by Adam Smith (http://www.adchsm.me/) released under MIT License (http://plugins.adchsm.me/slidebars/license.txt)
!function(t){t.slidebars=function(s){function e(){!c.disableOver||"number"==typeof c.disableOver&&c.disableOver>=k?(w=!0,t("html").addClass("sb-init"),c.hideControlClasses&&T.removeClass("sb-hide"),i()):"number"==typeof c.disableOver&&c.disableOver<k&&(w=!1,t("html").removeClass("sb-init"),c.hideControlClasses&&T.addClass("sb-hide"),g.css("minHeight",""),(p||y)&&o())}function i(){g.css("minHeight","");var s=parseInt(g.css("height"),10),e=parseInt(t("html").css("height"),10);e>s&&g.css("minHeight",t("html").css("height")),m&&m.hasClass("sb-width-custom")&&m.css("width",m.attr("data-sb-width")),C&&C.hasClass("sb-width-custom")&&C.css("width",C.attr("data-sb-width")),m&&(m.hasClass("sb-style-push")||m.hasClass("sb-style-overlay"))&&m.css("marginLeft","-"+m.css("width")),C&&(C.hasClass("sb-style-push")||C.hasClass("sb-style-overlay"))&&C.css("marginRight","-"+C.css("width")),c.scrollLock&&t("html").addClass("sb-scroll-lock")}function n(t,s,e){function n(){a.removeAttr("style"),i()}var a;if(a=t.hasClass("sb-style-push")?g.add(t).add(O):t.hasClass("sb-style-overlay")?t:g.add(O),"translate"===x)"0px"===s?n():a.css("transform","translate( "+s+" )");else if("side"===x)"0px"===s?n():("-"===s[0]&&(s=s.substr(1)),a.css(e,"0px"),setTimeout(function(){a.css(e,s)},1));else if("jQuery"===x){"-"===s[0]&&(s=s.substr(1));var o={};o[e]=s,a.stop().animate(o,400)}}function a(s){function e(){w&&"left"===s&&m?(t("html").addClass("sb-active sb-active-left"),m.addClass("sb-active"),n(m,m.css("width"),"left"),setTimeout(function(){p=!0},400)):w&&"right"===s&&C&&(t("html").addClass("sb-active sb-active-right"),C.addClass("sb-active"),n(C,"-"+C.css("width"),"right"),setTimeout(function(){y=!0},400))}"left"===s&&m&&y||"right"===s&&C&&p?(o(),setTimeout(e,400)):e()}function o(s,e){(p||y)&&(p&&(n(m,"0px","left"),p=!1),y&&(n(C,"0px","right"),y=!1),setTimeout(function(){t("html").removeClass("sb-active sb-active-left sb-active-right"),m&&m.removeClass("sb-active"),C&&C.removeClass("sb-active"),"undefined"!=typeof s&&(void 0===typeof e&&(e="_self"),window.open(s,e))},400))}function l(t){"left"===t&&m&&(p?o():a("left")),"right"===t&&C&&(y?o():a("right"))}function r(t,s){t.stopPropagation(),t.preventDefault(),"touchend"===t.type&&s.off("click")}var c=t.extend({siteClose:!0,scrollLock:!1,disableOver:!1,hideControlClasses:!1},s),h=document.createElement("div").style,d=!1,f=!1;(""===h.MozTransition||""===h.WebkitTransition||""===h.OTransition||""===h.transition)&&(d=!0),(""===h.MozTransform||""===h.WebkitTransform||""===h.OTransform||""===h.transform)&&(f=!0);var u=navigator.userAgent,b=!1,v=!1;/Android/.test(u)?b=u.substr(u.indexOf("Android")+8,3):/(iPhone|iPod|iPad)/.test(u)&&(v=u.substr(u.indexOf("OS ")+3,3).replace("_",".")),(b&&3>b||v&&5>v)&&t("html").addClass("sb-static");var g=t("#sb-site, .sb-site-container");if(t(".sb-left").length)var m=t(".sb-left"),p=!1;if(t(".sb-right").length)var C=t(".sb-right"),y=!1;var w=!1,k=t(window).width(),T=t(".sb-toggle-left, .sb-toggle-right, .sb-open-left, .sb-open-right, .sb-close"),O=t(".sb-slide");e(),t(window).resize(function(){var s=t(window).width();k!==s&&(k=s,e(),p&&a("left"),y&&a("right"))});var x;d&&f?(x="translate",b&&4.4>b&&(x="side")):x="jQuery",this.slidebars={open:a,close:o,toggle:l,init:function(){return w},active:function(t){return"left"===t&&m?p:"right"===t&&C?y:void 0},destroy:function(t){"left"===t&&m&&(p&&o(),setTimeout(function(){m.remove(),m=!1},400)),"right"===t&&C&&(y&&o(),setTimeout(function(){C.remove(),C=!1},400))}},t(".sb-toggle-left").on("touchend click",function(s){r(s,t(this)),l("left")}),t(".sb-toggle-right").on("touchend click",function(s){r(s,t(this)),l("right")}),t(".sb-open-left").on("touchend click",function(s){r(s,t(this)),a("left")}),t(".sb-open-right").on("touchend click",function(s){r(s,t(this)),a("right")}),t(".sb-close").on("touchend click",function(s){if(t(this).is("a")||t(this).children().is("a")){if("click"===s.type){s.stopPropagation(),s.preventDefault();var e=t(this).is("a")?t(this):t(this).find("a"),i=e.attr("href"),n=e.attr("target")?e.attr("target"):"_self";o(i,n)}}else r(s,t(this)),o()}),g.on("touchend click",function(s){c.siteClose&&(p||y)&&(r(s,t(this)),o())})}}(jQuery);

(function(b)
{
    "function" === typeof define && define.amd ? define(["jquery"], b) : b(jQuery)
})(function(b)
{
    var j = [], n = b(document), k = navigator.userAgent.toLowerCase(), l = b(window), g = [], o = null, p = /msie/.test(k) && !/opera/.test(k), q = /opera/.test(k), m, r;
    m = p && /msie 6./.test(k) && "object" !== typeof window.XMLHttpRequest;
    r = p && /msie 7.0/.test(k);
    b.modal = function(a, h)
    {
        return b.modal.impl.init(a, h)
    };
    b.modal.close = function()
    {
        b.modal.impl.close()
    };
    b.modal.focus = function(a)
    {
        b.modal.impl.focus(a)
    };
    b.modal.setContainerDimensions =
        function()
        {
            b.modal.impl.setContainerDimensions()
        };
    b.modal.setPosition = function()
    {
        b.modal.impl.setPosition()
    };
    b.modal.update = function(a, h)
    {
        b.modal.impl.update(a, h)
    };
    b.fn.modal = function(a)
    {
        $('html').css('overflow', 'hidden');
        return b.modal.impl.init(this, a)
    };
    b.modal.defaults = {
        appendTo: "body", focus: !0, opacity: 70, overlayId: "simplemodal-overlay", overlayCss: {}, containerId: "simplemodal-container", containerCss: {}, dataId: "simplemodal-data", dataCss: {}, minHeight: null, minWidth: null, maxHeight: null, maxWidth: null, autoResize: !1, autoPosition: !0, zIndex: 1E3,
        close: !0, closeHTML: '<a class="modalCloseImg" title="Close"></a>', closeClass: "simplemodal-close", escClose: !0, overlayClose: !1, fixed: !0, position: null, persist: !1, modal: !0, onOpen: null, onShow: null, onClose: null
    };
    b.modal.impl = {
        d: {}, init: function(a, h)
        {
            if (this.d.data)return !1;
            o = p && !b.support.boxModel;
            this.o = b.extend({}, b.modal.defaults, h);
            this.zIndex = this.o.zIndex;
            this.occb = !1;
            if ("object" === typeof a) {
                if (a = a instanceof b ? a : b(a), this.d.placeholder = !1, 0 < a.parent().parent().size() && (a.before(b("<span></span>").attr("id",
                        "simplemodal-placeholder").css({display: "none"})), this.d.placeholder = !0, this.display = a.css("display"), !this.o.persist))this.d.orig = a.clone(!0)
            }
            else if ("string" === typeof a || "number" === typeof a)a = b("<div></div>").html(a);
            else return alert("SimpleModal Error: Unsupported data type: " + typeof a), this;
            this.create(a);
            this.open();
            b.isFunction(this.o.onShow) && this.o.onShow.apply(this, [this.d]);
            return this
        }, create: function(a)
        {
            this.getDimensions();
            if (this.o.modal && m)this.d.iframe = b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss,
                {display: "none", opacity: 0, position: "fixed", height: g[0], width: g[1], zIndex: this.o.zIndex, top: 0, left: 0})).appendTo(this.o.appendTo);
            this.d.overlay = b("<div></div>").attr("id", this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss, {display: "none", opacity: this.o.opacity / 100, height: '100%', width: '100%', position: "fixed", left: 0, top: 0, right: 0, zIndex: this.o.zIndex + 1})).appendTo(this.o.appendTo);
            this.d.container = b("<div></div>").attr("id", this.o.containerId).addClass("simplemodal-container").css(b.extend({
                position: this.o.fixed ?
                    "fixed" : "absolute"
            }, this.o.containerCss, {display: "none", zIndex: this.o.zIndex + 2})).append(this.o.close && this.o.closeHTML ? b(this.o.closeHTML).addClass(this.o.closeClass) : "").appendTo(this.o.appendTo);
            this.d.wrap = b("<div></div>").attr("tabIndex", -1).addClass("simplemodal-wrap").css({height: "100%", outline: 0, width: "100%"}).appendTo(this.d.container);
            this.d.data = a.attr("id", a.attr("id") || this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss, {display: "none"})).appendTo("body");
            this.setContainerDimensions();
            this.d.data.appendTo(this.d.wrap);
            (m || o) && this.fixIE()
        }, bindEvents: function()
        {
            var a = this;
            b("." + a.o.closeClass).bind("click.simplemodal", function(b)
            {
                b.preventDefault();
                a.close()
            });
            a.o.modal && a.o.close && a.o.overlayClose && a.d.overlay.bind("click.simplemodal", function(b)
            {
                b.preventDefault();
                a.close()
            });
            n.bind("keydown.simplemodal", function(b)
            {
                a.o.modal && 9 === b.keyCode ? a.watchTab(b) : a.o.close && a.o.escClose && 27 === b.keyCode && (b.preventDefault(), a.close())
            });
            l.bind("resize.simplemodal orientationchange.simplemodal",
                function()
                {
                    a.getDimensions();
                    a.o.autoResize ? a.setContainerDimensions() : a.o.autoPosition && a.setPosition();
                    m || o ? a.fixIE() : a.o.modal && (a.d.iframe && a.d.iframe.css({height: g[0], width: g[1]}), a.d.overlay.css({height: '100%', width: '100%'}))
                })
        }, unbindEvents: function()
        {
            b("." + this.o.closeClass).unbind("click.simplemodal");
            n.unbind("keydown.simplemodal");
            l.unbind(".simplemodal");
            this.d.overlay.unbind("click.simplemodal")
        }, fixIE: function()
        {
            var a = this.o.position;
            b.each([this.d.iframe || null, !this.o.modal ? null : this.d.overlay,
                "fixed" === this.d.container.css("position") ? this.d.container : null], function(b, e)
            {
                if (e) {
                    var f = e[0].style;
                    f.position = "absolute";
                    if (2 > b)f.removeExpression("height"), f.removeExpression("width"), f.setExpression("height", 'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"'), f.setExpression("width", 'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"');
                    else {
                        var c, d;
                        a && a.constructor ===
                        Array ? (c = a[0] ? "number" === typeof a[0] ? a[0].toString() : a[0].replace(/px/, "") : e.css("top").replace(/px/, ""), c = -1 === c.indexOf("%") ? c + ' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"' : parseInt(c.replace(/%/, "")) + ' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"', a[1] && (d = "number" === typeof a[1] ?
                            a[1].toString() : a[1].replace(/px/, ""), d = -1 === d.indexOf("%") ? d + ' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"' : parseInt(d.replace(/%/, "")) + ' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"')) : (c = '(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',
                            d = '(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"');
                        f.removeExpression("top");
                        f.removeExpression("left");
                        f.setExpression("top", c);
                        f.setExpression("left", d)
                    }
                }
            })
        }, focus: function(a)
        {
            var h = this, a = a && -1 !== b.inArray(a, ["first", "last"]) ? a : "first", e = b(":input:enabled:visible:" + a, h.d.wrap);
            setTimeout(function()
                {
                    0 < e.length ? e.focus() : h.d.wrap.focus()
                },
                10)
        }, getDimensions: function()
        {
            var a = "undefined" === typeof window.innerHeight ? l.height() : window.innerHeight;
            j = [n.height(), $('body').width()];
            g = [a, $('body').width()]
        }, getVal: function(a, b)
        {
            return a ? "number" === typeof a ? a : "auto" === a ? 0 : 0 < a.indexOf("%") ? parseInt(a.replace(/%/, "")) / 100 * ("h" === b ? g[0] : g[1]) : parseInt(a.replace(/px/, "")) : null
        }, update: function(a, b)
        {
            if (!this.d.data)return !1;
            this.d.origHeight = this.getVal(a, "h");
            this.d.origWidth = this.getVal(b, "w");
            this.d.data.hide();
            a && this.d.container.css("height", a);
            b && this.d.container.css("width",
                b);
            this.setContainerDimensions();
            this.d.data.show();
            this.o.focus && this.focus();
            this.unbindEvents();
            this.bindEvents()
        }, setContainerDimensions: function()
        {
            var a = m || r, b = this.d.origHeight ? this.d.origHeight : q ? this.d.container.height() : this.getVal(a ? this.d.container[0].currentStyle.height : this.d.container.css("height"), "h"), a = this.d.origWidth ? this.d.origWidth : q ? this.d.container.width() : this.getVal(a ? this.d.container[0].currentStyle.width : this.d.container.css("width"), "w"), e = this.d.data.outerHeight(!0), f =
                this.d.data.outerWidth(!0);
            this.d.origHeight = this.d.origHeight || b;
            this.d.origWidth = this.d.origWidth || a;
            var c = this.o.maxHeight ? this.getVal(this.o.maxHeight, "h") : null, d = this.o.maxWidth ? this.getVal(this.o.maxWidth, "w") : null, c = c && c < g[0] ? c : g[0], d = d && d < g[1] ? d : g[1], i = this.o.minHeight ? this.getVal(this.o.minHeight, "h") : "auto", b = b ? this.o.autoResize && b > c ? c : b < i ? i : b : e ? e > c ? c : this.o.minHeight && "auto" !== i && e < i ? i : e : i, c = this.o.minWidth ? this.getVal(this.o.minWidth, "w") : "auto", a = a ? this.o.autoResize && a > d ? d : a < c ? c : a : f ?
                f > d ? d : this.o.minWidth && "auto" !== c && f < c ? c : f : c;
            this.d.container.css({height: b, width: a});
            this.d.wrap.css({overflow: e > b || f > a ? "auto" : "visible"});
            this.o.autoPosition && this.setPosition()
        }, setPosition: function()
        {
            var a, b;
            a = g[0] / 2 - this.d.container.outerHeight(!0) / 2;
            b = g[1] / 2 - this.d.container.outerWidth(!0) / 2;
            var e = "fixed" !== this.d.container.css("position") ? l.scrollTop() : 0;
            this.o.position && "[object Array]" === Object.prototype.toString.call(this.o.position) ? (a = e + (this.o.position[0] || a), b = this.o.position[1] || b) :
                a = e + a;
            this.d.container.css({left: b, top: a})
        }, watchTab: function(a)
        {
            if (0 < b(a.target).parents(".simplemodal-container").length) {
                if (this.inputs = b(":input:enabled:visible:first, :input:enabled:visible:last", this.d.data[0]), !a.shiftKey && a.target === this.inputs[this.inputs.length - 1] || a.shiftKey && a.target === this.inputs[0] || 0 === this.inputs.length)a.preventDefault(), this.focus(a.shiftKey ? "last" : "first")
            }
            else a.preventDefault(), this.focus()
        }, open: function()
        {
            this.d.iframe && this.d.iframe.show();
            b.isFunction(this.o.onOpen) ?
                this.o.onOpen.apply(this, [this.d]) : (this.d.overlay.show(), this.d.container.show(), this.d.data.show());
            this.o.focus && this.focus();
            this.bindEvents()
        }, close: function()
        {
            $('html').css('overflow','');
            if (!this.d.data)return !1;
            this.unbindEvents();
            if (b.isFunction(this.o.onClose) && !this.occb)this.occb = !0, this.o.onClose.apply(this, [this.d]);
            else {
                if (this.d.placeholder) {
                    var a = b("#simplemodal-placeholder");
                    this.o.persist ? a.replaceWith(this.d.data.removeClass("simplemodal-data").css("display", this.display)) : (this.d.data.hide().remove(), a.replaceWith(this.d.orig))
                }
                else this.d.data.hide().remove();
                this.d.container.hide().remove();
                this.d.overlay.hide();
                this.d.iframe && this.d.iframe.hide().remove();
                this.d.overlay.remove();
                this.d = {}
            }
        }
    }
});

/* Licensed under MIT license, GNU GPL 2 or later, GNU LGPL 2 or later, see license.txt. */

//
// Helper functions
//

var qq = qq || {};

/**
 * Adds all missing properties from second obj to first obj
 */
qq.extend = function(first, second)
{
    for (var prop in second) {
        if (second.hasOwnProperty(prop)) {
            first[prop] = second[prop];
        }
    }
};

/**
 * Searches for a given element in the array, returns -1 if it is not present.
 */
qq.indexOf = function(arr, elt, from)
{
    if (arr.indexOf) return arr.indexOf(elt, from);

    from = from || 0;
    var len = arr.length;

    if (from < 0) from += len;

    for (; from < len; from++) {
        if (from in arr && arr[from] === elt) {
            return from;
        }
    }
    return -1;
};

qq.getUniqueId = (function()
{
    var id = 0;
    return function()
    {
        return id++;
    };
})();

//
// Browsers and platforms detection

qq.ie = function()
{
    return navigator.userAgent.indexOf('MSIE') != -1;
};
qq.safari = function()
{
    return navigator.vendor != undefined && navigator.vendor.indexOf("Apple") != -1;
};
qq.chrome = function()
{
    return navigator.vendor != undefined && navigator.vendor.indexOf('Google') != -1;
};
qq.firefox = function()
{
    return (navigator.userAgent.indexOf('Mozilla') != -1 && navigator.vendor != undefined && navigator.vendor == '');
};
qq.windows = function()
{
    return navigator.platform == "Win32";
};

//
// Events

/** Returns the function which detaches attached event */
qq.attach = function(element, type, fn)
{
    if (element.addEventListener) {
        element.addEventListener(type, fn, false);
    }
    else if (element.attachEvent) {
        element.attachEvent('on' + type, fn);
    }
    return function()
    {
        qq.detach(element, type, fn)
    }
};
qq.detach = function(element, type, fn)
{
    if (element.removeEventListener) {
        element.removeEventListener(type, fn, false);
    }
    else if (element.attachEvent) {
        element.detachEvent('on' + type, fn);
    }
};

qq.preventDefault = function(e)
{
    if (e.preventDefault) {
        e.preventDefault();
    }
    else {
        e.returnValue = false;
    }
};

//
// Node manipulations

/**
 * Insert node a before node b.
 */
qq.insertBefore = function(a, b)
{
    b.parentNode.insertBefore(a, b);
};
qq.remove = function(element)
{
    element.parentNode.removeChild(element);
};

qq.contains = function(parent, descendant)
{
    // compareposition returns false in this case
    if (parent == descendant) return true;

    if (parent.contains) {
        return parent.contains(descendant);
    }
    else {
        return !!(descendant.compareDocumentPosition(parent) & 8);
    }
};

/**
 * Creates and returns element from html string
 * Uses innerHTML to create an element
 */
qq.toElement = (function()
{
    var div = document.createElement('div');
    return function(html)
    {
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    };
})();

//
// Node properties and attributes

/**
 * Sets styles for an element.
 * Fixes opacity in IE6-8.
 */
qq.css = function(element, styles)
{
    if (styles.opacity != null) {
        if (typeof element.style.opacity != 'string' && typeof(element.filters) != 'undefined') {
            styles.filter = 'alpha(opacity=' + Math.round(100 * styles.opacity) + ')';
        }
    }
    qq.extend(element.style, styles);
};
qq.hasClass = function(element, name)
{
    var re = new RegExp('(^| )' + name + '( |$)');
    return re.test(element.className);
};
qq.addClass = function(element, name)
{
    if (!qq.hasClass(element, name)) {
        element.className += ' ' + name;
    }
};
qq.removeClass = function(element, name)
{
    var re = new RegExp('(^| )' + name + '( |$)');
    element.className = element.className.replace(re, ' ').replace(/^\s+|\s+$/g, "");
};
qq.setText = function(element, text)
{
    element.innerText = text;
    element.textContent = text;
};

//
// Selecting elements

qq.children = function(element)
{
    var children = [],
        child    = element.firstChild;

    while (child) {
        if (child.nodeType == 1) {
            children.push(child);
        }
        child = child.nextSibling;
    }

    return children;
};

qq.getByClass = function(element, className)
{
    if (element.querySelectorAll) {
        return element.querySelectorAll('.' + className);
    }

    var result = [];
    var candidates = element.getElementsByTagName("*");
    var len = candidates.length;

    for (var i = 0; i < len; i++) {
        if (qq.hasClass(candidates[i], className)) {
            result.push(candidates[i]);
        }
    }
    return result;
};

/**
 * obj2url() takes a json-object as argument and generates
 * a querystring. pretty much like jQuery.param()
 */
qq.obj2url = function(obj, temp, prefixDone)
{
    var uristrings = [],
        prefix     = '&',
        add        = function(nextObj, i)
        {
            var nextTemp = temp
                ? (/\[\]$/.test(temp)) // prevent double-encoding
                ? temp
                : temp + '[' + i + ']'
                : i;
            if ((nextTemp != 'undefined') && (i != 'undefined')) {
                uristrings.push(
                    (typeof nextObj === 'object')
                        ? qq.obj2url(nextObj, nextTemp, true)
                        : (Object.prototype.toString.call(nextObj) === '[object Function]')
                        ? encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj())
                        : encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj)
                );
            }
        };

    if (!prefixDone && temp) {
        prefix = (/\?/.test(temp)) ? (/\?$/.test(temp)) ? '' : '&' : '?';
        uristrings.push(temp);
        uristrings.push(qq.obj2url(obj));
    }
    else if ((Object.prototype.toString.call(obj) === '[object Array]') && (typeof obj != 'undefined')) {
        // we wont use a for-in-loop on an array (performance)
        for (var p = 0, len = obj.length; p < len; ++p) {
            add(obj[p], p);
        }
    }
    else if ((typeof obj != 'undefined') && (obj !== null) && (typeof obj === "object")) {
        // for anything else but a scalar, we will use for-in-loop
        for (var i in obj) {
            if (obj.hasOwnProperty(i)) {
                add(obj[i], i);
            }
        }
    }
    else {
        uristrings.push(encodeURIComponent(temp) + '=' + encodeURIComponent(obj));
    }

    return uristrings.join(prefix)
        .replace(/^&/, '')
        .replace(/%20/g, '+');
};

//
//
// Uploader Classes
//
//

qq = qq || {};

/**
 * Creates upload button, validates upload, but doesn't create file list or dd.
 */
qq.FileUploaderBasic = function(o)
{
    var that = this;
    this._options = {
        // set to true to see the server response
        debug: false,
        action: '/server/upload',
        params: {},
        customHeaders: {},
        button: null,
        multiple: true,
        maxConnections: 3,
        disableCancelForFormUploads: false,
        autoUpload: true,
        // validation
        allowedExtensions: [],
        acceptFiles: null,		// comma separated string of mime-types for browser to display in browse dialog
        sizeLimit: 0,
        minSizeLimit: 0,
        // events
        // return false to cancel submit
        onSubmit: function(id, fileName)
        {
        },
        onComplete: function(id, fileName, responseJSON)
        {
        },
        onCancel: function(id, fileName)
        {
        },
        onUpload: function(id, fileName, xhr)
        {
        },
        onProgress: function(id, fileName, loaded, total)
        {
            if (loaded === total) {
                var item = that._getItemByFileId(id);
                var cancelLink = that._find(item, 'cancel');
                cancelLink.style.display = 'none';
            }
        },
        onError: function(id, fileName, xhr)
        {
        },
        // messages
        messages: {
            typeError: "{file} has an invalid extension. Only {extensions} {isAre} allowed.",
            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
            emptyError: "{file} is empty, please select files again without it.",
            noFilesError: "No files to upload.",
            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
        },
        showMessage: function(message)
        {
            jrCore_alert(message);
        },
        inputName: 'qqfile',
        extraDropzones: []
    };
    qq.extend(this._options, o);
    qq.extend(this, qq.DisposeSupport);

    // number of files being uploaded
    this._filesInProgress = 0;

    this._storedFiles = [];

    this._handler = this._createUploadHandler();

    if (this._options.button) {
        this._button = this._createUploadButton(this._options.button);
    }

    this._preventLeaveInProgress();
};

qq.FileUploaderBasic.prototype = {
    setParams: function(params)
    {
        this._options.params = params;
    },
    getInProgress: function()
    {
        return this._filesInProgress;
    },
    uploadStoredFiles: function()
    {
        for (var i = 0; i < this._storedFiles.length; i++) {
            var item = this._getItemByFileId(this._storedFiles[i]);
            this._find(item, 'spinner').style.display = "";
            this._filesInProgress++;
            this._handler.upload(this._storedFiles[i], this._options.params);
        }
    },
    clearStoredFiles: function()
    {
        this._storedFiles = [];
    },
    _createUploadButton: function(element)
    {
        var self = this;

        var button = new qq.UploadButton({
            element: element,
            multiple: this._options.multiple && qq.UploadHandlerXhr.isSupported(),
            acceptFiles: this._options.acceptFiles,
            onChange: function(input)
            {
                self._onInputChange(input);
            }
        });

        this.addDisposer(function()
        {
            button.dispose();
        });
        return button;
    },
    _createUploadHandler: function()
    {
        var self = this,
            handlerClass;

        if (qq.UploadHandlerXhr.isSupported()) {
            handlerClass = 'UploadHandlerXhr';
        }
        else {
            handlerClass = 'UploadHandlerForm';
        }

        return new qq[handlerClass]({
            debug: this._options.debug,
            action: this._options.action,
            encoding: this._options.encoding,
            maxConnections: this._options.maxConnections,
            customHeaders: this._options.customHeaders,
            inputName: this._options.inputName,
            extraDropzones: this._options.extraDropzones,
            demoMode: this._options.demoMode,
            onProgress: function(id, fileName, loaded, total)
            {
                self._onProgress(id, fileName, loaded, total);
                self._options.onProgress(id, fileName, loaded, total);
            },
            onComplete: function(id, fileName, result)
            {
                self._onComplete(id, fileName, result);
                self._options.onComplete(id, fileName, result);
            },
            onCancel: function(id, fileName)
            {
                self._onCancel(id, fileName);
                self._options.onCancel(id, fileName);
            },
            onError: self._options.onError,
            onUpload: function(id, fileName, xhr)
            {
                self._onUpload(id, fileName, xhr);
                self._options.onUpload(id, fileName, xhr);
            }
        });
    },
    _preventLeaveInProgress: function()
    {
        var self = this;
        this._attach(window, 'beforeunload', function(e)
        {
            if (!self._filesInProgress) {
                return;
            }

            e = e || window.event;
            // for ie, ff
            e.returnValue = self._options.messages.onLeave;
            // for webkit
            return self._options.messages.onLeave;
        });
    },
    _onSubmit: function(id, fileName)
    {
        if (this._options.autoUpload) {
            this._filesInProgress++;
        }
    },
    _onProgress: function(id, fileName, loaded, total)
    {
    },
    _onComplete: function(id, fileName, result)
    {
        this._filesInProgress--;
        if (result.error) {
            this._options.showMessage(result.error);
        }
    },
    _onCancel: function(id, fileName)
    {
        if (this._options.autoUpload) {
            this._filesInProgress--;
        }
    },
    _onUpload: function(id, fileName, xhr)
    {
    },
    _onInputChange: function(input)
    {
        if (this._handler instanceof qq.UploadHandlerXhr) {
            this._uploadFileList(input.files);
        }
        else {
            if (this._validateFile(input)) {
                this._uploadFile(input);
            }
        }
        this._button.reset();
    },
    _uploadFileList: function(files)
    {
        var len = files.length;
        if (len > 0) {
            for (var l = 0; l < len; l++) {
                if (!this._validateFile(files[l])) {
                    return;
                }
            }

            for (var i = 0; i < len; i++) {
                this._uploadFile(files[i]);
            }
        }
        else {
            this._error('noFilesError', "");
        }
    },
    _uploadFile: function(fileContainer)
    {
        var id = this._handler.add(fileContainer);
        var fileName = this._handler.getName(id);

        if (this._options.onSubmit(id, fileName) !== false) {
            this._onSubmit(id, fileName);
            if (this._options.autoUpload) {
                this._handler.upload(id, this._options.params);
            }
            else {
                var item = this._getItemByFileId(id);
                this._find(item, 'spinner').style.display = "none";
                this._storedFiles.push(id);
            }
        }
    },
    _validateFile: function(file)
    {
        var name, size;

        if (file.value) {
            // it is a file input
            // get input value and remove path to normalize
            name = file.value.replace(/.*(\/|\\)/, "");
        }
        else {
            // fix missing properties in Safari 4 and firefox 11.0a2
            name = (file.fileName !== null && file.fileName !== undefined) ? file.fileName : file.name;
            size = (file.fileSize !== null && file.fileSize !== undefined) ? file.fileSize : file.size;
        }

        if (!this._isAllowedExtension(name)) {
            this._error('typeError', name);
            return false;

        }
        else if (size === 0) {
            this._error('emptyError', name);
            return false;

        }
        else if (size && this._options.sizeLimit && size > this._options.sizeLimit) {
            this._error('sizeError', name);
            return false;

        }
        else if (size && size < this._options.minSizeLimit) {
            this._error('minSizeError', name);
            return false;
        }

        return true;
    },
    _error: function(code, fileName)
    {
        var message = this._options.messages[code];

        function r(name, replacement)
        {
            message = message.replace(name, replacement);
        }

        var extensions = this._options.allowedExtensions.join(', ');

        r('{file}', this._formatFileName(fileName));
        r('{extensions}', extensions);
        r('{sizeLimit}', this._formatSize(this._options.sizeLimit));
        r('{minSizeLimit}', this._formatSize(this._options.minSizeLimit));
        r('{isAre}', extensions.indexOf(",") != -1 ? "are" : "is");

        this._options.showMessage(message);
    },
    _formatFileName: function(name)
    {
        if (name.length > 33) {
            name = name.slice(0, 19) + '...' + name.slice(-13);
        }
        return name;
    },
    _isAllowedExtension: function(fileName)
    {
        var ext = (-1 !== fileName.indexOf('.'))
            ? fileName.replace(/.*[.]/, '').toLowerCase()
            : '';
        var allowed = this._options.allowedExtensions;

        if (!allowed.length) {
            return true;
        }

        for (var i = 0; i < allowed.length; i++) {
            if (allowed[i].toLowerCase() == ext) {
                return true;
            }
        }

        return false;
    },
    _formatSize: function(bytes)
    {
        var i = -1;
        do {
            bytes = bytes / 1024;
            i++;
        } while (bytes > 99);

        return Math.max(bytes, 0.1).toFixed(1) + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i];
    }
};


/**
 * Class that creates upload widget with drag-and-drop and file list
 * @inherits qq.FileUploaderBasic
 */
qq.FileUploader = function(o)
{
    // call parent constructor
    qq.FileUploaderBasic.apply(this, arguments);

    // additional options
    qq.extend(this._options, {
        element: null,
        // if set, will be used instead of qq-upload-list in template
        listElement: null,
        dragText: 'Drop files here to upload',
        uploadButtonText: 'Upload a file',
        cancelButtonText: 'Cancel',
        failUploadText: 'Upload failed',

        template: '<div class="qq-uploader">' +
        '<div class="qq-upload-drop-area"><span>{dragText}</span></div>' +
        '<div class="qq-upload-button form_button upload_button">{uploadButtonText}</div>' +
        '<ul class="qq-upload-list"></ul>' +
        '</div>',

        // template for one item in file list
        fileTemplate: '<li>' +
        '<span class="qq-progress-bar"></span>' +
        '<span class="qq-upload-spinner"></span>' +
        '<span class="qq-upload-finished"></span>' +
        '<span class="qq-upload-file"></span>' +
        '<span class="qq-upload-size"></span>' +
        '<a class="qq-upload-cancel" href="#">{cancelButtonText}</a>' +
        '<span class="qq-upload-failed-text">{failUploadtext}</span>' +
        '</li>',

        classes: {
            // used to get elements from templates
            button: 'qq-upload-button',
            drop: 'qq-upload-drop-area',
            dropActive: 'qq-upload-drop-area-active',
            dropDisabled: 'qq-upload-drop-area-disabled',
            list: 'qq-upload-list',
            progressBar: 'qq-progress-bar',
            file: 'qq-upload-file',
            spinner: 'qq-upload-spinner',
            finished: 'qq-upload-finished',
            size: 'qq-upload-size',
            cancel: 'qq-upload-cancel',

            // added to list item <li> when upload completes
            // used in css to hide progress spinner
            success: 'qq-upload-success',
            fail: 'qq-upload-fail',

            successIcon: null,
            failIcon: null
        }
    });
    // overwrite options with user supplied
    qq.extend(this._options, o);

    // overwrite the upload button text if any
    // same for the Cancel button and Fail message text
    this._options.template = this._options.template.replace(/\{dragText\}/g, this._options.dragText);
    this._options.template = this._options.template.replace(/\{uploadButtonText\}/g, this._options.uploadButtonText);
    this._options.fileTemplate = this._options.fileTemplate.replace(/\{cancelButtonText\}/g, this._options.cancelButtonText);
    this._options.fileTemplate = this._options.fileTemplate.replace(/\{failUploadtext\}/g, this._options.failUploadText);

    this._element = this._options.element;
    this._element.innerHTML = this._options.template;
    this._listElement = this._options.listElement || this._find(this._element, 'list');

    this._classes = this._options.classes;

    this._button = this._createUploadButton(this._find(this._element, 'button'));

    this._bindCancelEvent();
    this._setupDragDrop();
};

// inherit from Basic Uploader
qq.extend(qq.FileUploader.prototype, qq.FileUploaderBasic.prototype);

qq.extend(qq.FileUploader.prototype, {
    clearStoredFiles: function()
    {
        qq.FileUploaderBasic.prototype.clearStoredFiles.apply(this, arguments);
        this._listElement.innerHTML = "";
    },
    addExtraDropzone: function(element)
    {
        this._setupExtraDropzone(element);
    },
    removeExtraDropzone: function(element)
    {
        var dzs = this._options.extraDropzones;
        for (var i in dzs) {
            if (dzs.hasOwnProperty(i)) {
                if (dzs[i] === element) {
                    return this._options.extraDropzones.splice(i, 1);
                }
            }
        }
    },
    _leaving_document_out: function(e)
    {
        return ((qq.chrome() || (qq.safari() && qq.windows())) && e.clientX == 0 && e.clientY == 0) || (qq.firefox() && !e.relatedTarget);
    },
    /**
     * Gets one of the elements listed in this._options.classes
     **/
    _find: function(parent, type)
    {
        var element = qq.getByClass(parent, this._options.classes[type])[0];
        if (!element) {
            throw new Error('element not found ' + type);
        }
        return element;
    },
    _setupExtraDropzone: function(element)
    {
        this._options.extraDropzones.push(element);
        this._setupDropzone(element);
    },
    _setupDropzone: function(dropArea)
    {
        var self = this;

        var dz = new qq.UploadDropZone({
            element: dropArea,
            onEnter: function(e)
            {
                qq.addClass(dropArea, self._classes.dropActive);
                e.stopPropagation();
            },
            onLeave: function(e)
            {
                //e.stopPropagation();
            },
            onLeaveNotDescendants: function(e)
            {
                qq.removeClass(dropArea, self._classes.dropActive);
            },
            onDrop: function(e)
            {
                dropArea.style.display = 'none';
                qq.removeClass(dropArea, self._classes.dropActive);
                self._uploadFileList(e.dataTransfer.files);
            }
        });

        this.addDisposer(function()
        {
            dz.dispose();
        });

        dropArea.style.display = 'none';
    },
    _setupDragDrop: function()
    {
        var dropArea = this._find(this._element, 'drop');
        var self = this;
        this._options.extraDropzones.push(dropArea);

        var dropzones = this._options.extraDropzones;
        var i;
        for (i = 0; i < dropzones.length; i++) {
            this._setupDropzone(dropzones[i]);
        }

        // IE <= 9 does not support the File API used for drag+drop uploads
        // Any volunteers to enable & test this for IE10?
        if (!qq.ie()) {
            this._attach(document, 'dragenter', function(e)
            {
                if (qq.hasClass(dropArea, self._classes.dropDisabled)) return;

                dropArea.style.display = 'block';
                for (i = 0; i < dropzones.length; i++) {
                    dropzones[i].style.display = 'block';
                }

            });
        }
        this._attach(document, 'dragleave', function(e)
        {
            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // only fire when leaving document out
            if (qq.FileUploader.prototype._leaving_document_out(e)) {
                for (i = 0; i < dropzones.length; i++) {
                    dropzones[i].style.display = 'none';
                }
            }
        });
        qq.attach(document, 'drop', function(e)
        {
            for (i = 0; i < dropzones.length; i++) {
                dropzones[i].style.display = 'none';
            }
            e.preventDefault();
        });
    },
    _onSubmit: function(id, fileName)
    {
        qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        this._addToList(id, fileName);
    },
    // Update the progress bar & percentage as the file is uploaded
    _onProgress: function(id, fileName, loaded, total)
    {
        qq.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileId(id);
        var size = this._find(item, 'size');
        size.style.display = 'inline';

        var text;
        var percent = Math.round(loaded / total * 100);

        if (loaded != total) {
            // If still uploading, display percentage
            text = percent + '% from ' + this._formatSize(total);
        }
        else {
            // If complete, just display final size
            text = this._formatSize(total);
        }

        // Update progress bar <span> tag
        this._find(item, 'progressBar').style.width = percent + '%';

        qq.setText(size, text);
    },
    _onComplete: function(id, fileName, result)
    {
        qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

        // mark completed
        var item = this._getItemByFileId(id);
        if (!this._options.disableCancelForFormUploads || qq.UploadHandlerXhr.isSupported()) {
            qq.remove(this._find(item, 'cancel'));
        }
        qq.remove(this._find(item, 'spinner'));

        if (result.success) {
            qq.addClass(item, this._classes.success);
            if (this._classes.successIcon) {
                this._find(item, 'finished').style.display = "inline-block";
                qq.addClass(item, this._classes.successIcon)
            }
        }
        else {
            var errorReason = result.reason ? result.reason : "Unknown error";
            qq.addClass(item, this._classes.fail);
            if (this._classes.failIcon) {
                this._find(item, 'finished').style.display = "inline-block";
                qq.addClass(item, this._classes.failIcon)
            }
            item.title = errorReason;
        }
    },
    _addToList: function(id, fileName)
    {
        var item = qq.toElement(this._options.fileTemplate);
        if (this._options.disableCancelForFormUploads && !qq.UploadHandlerXhr.isSupported()) {
            var cancelLink = this._find(item, 'cancel');
            qq.remove(cancelLink);
        }
        item.qqFileId = id;

        var fileElement = this._find(item, 'file');
        qq.setText(fileElement, this._formatFileName(fileName));
        this._find(item, 'size').style.display = 'none';
        if (!this._options.multiple) this._clearList();
        this._listElement.appendChild(item);
    },
    _clearList: function()
    {
        this._listElement.innerHTML = '';
    },
    _getItemByFileId: function(id)
    {
        var item = this._listElement.firstChild;

        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item) {
            if (item.qqFileId == id) return item;
            item = item.nextSibling;
        }
    },
    /**
     * delegate click event for cancel link
     **/
    _bindCancelEvent: function()
    {
        var self = this,
            list = this._listElement;

        this._attach(list, 'click', function(e)
        {
            e = e || window.event;
            var target = e.target || e.srcElement;

            if (qq.hasClass(target, self._classes.cancel)) {
                qq.preventDefault(e);

                var item = target.parentNode;
                self._handler.cancel(item.qqFileId);
                qq.remove(item);
            }
        });
    }
});

qq.UploadDropZone = function(o)
{
    this._options = {
        element: null,
        onEnter: function(e)
        {
        },
        onLeave: function(e)
        {
        },
        // is not fired when leaving element by hovering descendants
        onLeaveNotDescendants: function(e)
        {
        },
        onDrop: function(e)
        {
        }
    };
    qq.extend(this._options, o);
    qq.extend(this, qq.DisposeSupport);

    this._element = this._options.element;

    this._disableDropOutside();
    this._attachEvents();
};

qq.UploadDropZone.prototype = {
    _dragover_should_be_canceled: function()
    {
        return qq.safari() || (qq.firefox() && qq.windows());
    },
    _disableDropOutside: function(e)
    {
        // run only once for all instances
        if (!qq.UploadDropZone.dropOutsideDisabled) {

            // for these cases we need to catch onDrop to reset dropArea
            if (this._dragover_should_be_canceled) {
                qq.attach(document, 'dragover', function(e)
                {
                    e.preventDefault();
                });
            }
            else {
                qq.attach(document, 'dragover', function(e)
                {
                    if (e.dataTransfer) {
                        e.dataTransfer.dropEffect = 'none';
                        e.preventDefault();
                    }
                });
            }

            qq.UploadDropZone.dropOutsideDisabled = true;
        }
    },
    _attachEvents: function()
    {
        var self = this;

        self._attach(self._element, 'dragover', function(e)
        {
            if (!self._isValidFileDrag(e)) return;

            var effect = qq.ie() ? null : e.dataTransfer.effectAllowed;
            if (effect == 'move' || effect == 'linkMove') {
                e.dataTransfer.dropEffect = 'move'; // for FF (only move allowed)
            }
            else {
                e.dataTransfer.dropEffect = 'copy'; // for Chrome
            }

            e.stopPropagation();
            e.preventDefault();
        });

        self._attach(self._element, 'dragenter', function(e)
        {
            if (!self._isValidFileDrag(e)) return;

            self._options.onEnter(e);
        });

        self._attach(self._element, 'dragleave', function(e)
        {
            if (!self._isValidFileDrag(e)) return;

            self._options.onLeave(e);

            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // do not fire when moving a mouse over a descendant
            if (qq.contains(this, relatedTarget)) return;

            self._options.onLeaveNotDescendants(e);
        });

        self._attach(self._element, 'drop', function(e)
        {
            if (!self._isValidFileDrag(e)) return;

            e.preventDefault();
            self._options.onDrop(e);
        });
    },
    _isValidFileDrag: function(e)
    {
        // e.dataTransfer currently causing IE errors
        // IE9 does NOT support file API, so drag-and-drop is not possible
        // IE10 should work, but currently has not been tested - any volunteers?
        if (qq.ie()) return false;

        var dt       = e.dataTransfer,
            // do not check dt.types.contains in webkit, because it crashes safari 4
            isSafari = qq.safari();

        // dt.effectAllowed is none in Safari 5
        // dt.types.contains check is for firefox
        return dt && dt.effectAllowed != 'none' &&
            (dt.files || (!isSafari && dt.types.contains && dt.types.contains('Files')));

    }
};

qq.UploadButton = function(o)
{
    this._options = {
        element: null,
        // if set to true adds multiple attribute to file input
        multiple: false,
        acceptFiles: null,
        // name attribute of file input
        name: 'file',
        onChange: function(input)
        {
        },
        hoverClass: 'qq-upload-button-hover',
        focusClass: 'qq-upload-button-focus'
    };

    qq.extend(this._options, o);
    qq.extend(this, qq.DisposeSupport);

    this._element = this._options.element;

    // make button suitable container for input
    qq.css(this._element, {
        position: 'relative',
        overflow: 'hidden',
        // Make sure browse button is in the right side
        // in Internet Explorer
        direction: 'ltr'
    });

    this._input = this._createInput();
};

qq.UploadButton.prototype = {
    /* returns file input element */
    getInput: function()
    {
        return this._input;
    },
    /* cleans/recreates the file input */
    reset: function()
    {
        if (this._input.parentNode) {
            qq.remove(this._input);
        }

        qq.removeClass(this._element, this._options.focusClass);
        this._input = this._createInput();
    },
    _createInput: function()
    {
        var input = document.createElement("input");

        if (this._options.multiple) {
            input.setAttribute("multiple", "multiple");
        }

        if (this._options.acceptFiles) input.setAttribute("accept", this._options.acceptFiles);

        input.setAttribute("type", "file");
        input.setAttribute("name", this._options.name);

        qq.css(input, {
            position: 'absolute',
            // in Opera only 'browse' button
            // is clickable and it is located at
            // the right side of the input
            right: 0,
            top: 0,
            fontFamily: 'Arial',
            // 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
            fontSize: '118px',
            margin: 0,
            padding: 0,
            cursor: 'pointer',
            opacity: 0
        });

        this._element.appendChild(input);

        var self = this;
        this._attach(input, 'change', function()
        {
            self._options.onChange(input);
        });

        this._attach(input, 'mouseover', function()
        {
            qq.addClass(self._element, self._options.hoverClass);
        });
        this._attach(input, 'mouseout', function()
        {
            qq.removeClass(self._element, self._options.hoverClass);
        });
        this._attach(input, 'focus', function()
        {
            qq.addClass(self._element, self._options.focusClass);
        });
        this._attach(input, 'blur', function()
        {
            qq.removeClass(self._element, self._options.focusClass);
        });

        // IE and Opera, unfortunately have 2 tab stops on file input
        // which is unacceptable in our case, disable keyboard access
        if (window.attachEvent) {
            // it is IE or Opera
            input.setAttribute('tabIndex', "-1");
        }

        return input;
    }
};

/**
 * Class for uploading files, uploading itself is handled by child classes
 */
qq.UploadHandlerAbstract = function(o)
{
    // Default options, can be overridden by the user
    this._options = {
        debug: false,
        action: '/upload.php',
        // maximum number of concurrent uploads
        maxConnections: 999,
        onProgress: function(id, fileName, loaded, total)
        {
        },
        onComplete: function(id, fileName, response)
        {
        },
        onCancel: function(id, fileName)
        {
        },
        onUpload: function(id, fileName, xhr)
        {
        }
    };
    qq.extend(this._options, o);

    this._queue = [];
    // params for files in queue
    this._params = [];
};
qq.UploadHandlerAbstract.prototype = {
    log: function(str)
    {
        if (this._options.debug && window.console) console.log('[uploader] ' + str);
    },
    /**
     * Adds file or file input to the queue
     * @returns id
     **/
    add: function(file)
    {
    },
    /**
     * Sends the file identified by id and additional query params to the server
     */
    upload: function(id, params)
    {
        var len = this._queue.push(id);

        var copy = { orderid: id };
        qq.extend(copy, params);
        this._params[id] = copy;

        // if too many active uploads, wait...
        if (len <= this._options.maxConnections) {
            this._upload(id, this._params[id]);
        }
    },
    /**
     * Cancels file upload by id
     */
    cancel: function(id)
    {
        this._cancel(id);
        this._dequeue(id);
    },
    /**
     * Cancels all uploads
     */
    cancelAll: function()
    {
        for (var i = 0; i < this._queue.length; i++) {
            this._cancel(this._queue[i]);
        }
        this._queue = [];
    },
    /**
     * Returns name of the file identified by id
     */
    getName: function(id)
    {
    },
    /**
     * Returns size of the file identified by id
     */
    getSize: function(id)
    {
    },
    /**
     * Returns id of files being uploaded or
     * waiting for their turn
     */
    getQueue: function()
    {
        return this._queue;
    },
    /**
     * Actual upload method
     */
    _upload: function(id)
    {
    },
    /**
     * Actual cancel method
     */
    _cancel: function(id)
    {
    },
    /**
     * Removes element from queue, starts upload of next
     */
    _dequeue: function(id)
    {
        var i = qq.indexOf(this._queue, id);
        this._queue.splice(i, 1);

        var max = this._options.maxConnections;

        if (this._queue.length >= max && i < max) {
            var nextId = this._queue[max - 1];
            this._upload(nextId, this._params[nextId]);
        }
    }
};

/**
 * Class for uploading files using form and iframe
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerForm = function(o)
{
    qq.UploadHandlerAbstract.apply(this, arguments);

    this._inputs = {};
    this._detach_load_events = {};
};
// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerForm.prototype, qq.UploadHandlerAbstract.prototype);

qq.extend(qq.UploadHandlerForm.prototype, {
    add: function(fileInput)
    {
        fileInput.setAttribute('name', this._options.inputName);
        var id = 'qq-upload-handler-iframe' + qq.getUniqueId();

        this._inputs[id] = fileInput;

        // remove file input from DOM
        if (fileInput.parentNode) {
            qq.remove(fileInput);
        }

        return id;
    },
    getName: function(id)
    {
        // get input value and remove path to normalize
        return this._inputs[id].value.replace(/.*(\/|\\)/, "");
    },
    _cancel: function(id)
    {
        this._options.onCancel(id, this.getName(id));

        delete this._inputs[id];
        delete this._detach_load_events[id];

        var iframe = document.getElementById(id);
        if (iframe) {
            // to cancel request set src to something else
            // we use src="javascript:false;" because it doesn't
            // trigger ie6 prompt on https
            iframe.setAttribute('src', 'javascript:false;');

            qq.remove(iframe);
        }
    },
    _upload: function(id, params)
    {
        this._options.onUpload(id, this.getName(id), false);
        var input = this._inputs[id];

        if (!input) {
            throw new Error('file with passed id was not added, or already uploaded or cancelled');
        }

        var fileName = this.getName(id);

        var iframe = this._createIframe(id);
        var form = this._createForm(iframe, params);
        form.appendChild(input);

        var self = this;
        this._attachLoadEvent(iframe, function()
        {
            self.log('iframe loaded');

            var response = self._getIframeContentJSON(iframe);

            self._options.onComplete(id, fileName, response);
            self._dequeue(id);

            delete self._inputs[id];
            // timeout added to fix busy state in FF3.6
            setTimeout(function()
            {
                self._detach_load_events[id]();
                delete self._detach_load_events[id];
                qq.remove(iframe);
            }, 1);
        });

        form.submit();
        qq.remove(form);

        return id;
    },
    _attachLoadEvent: function(iframe, callback)
    {
        this._detach_load_events[iframe.id] = qq.attach(iframe, 'load', function()
        {
            // when we remove iframe from dom
            // the request stops, but in IE load
            // event fires
            if (!iframe.parentNode) {
                return;
            }

            try {
                // fixing Opera 10.53
                if (iframe.contentDocument &&
                    iframe.contentDocument.body &&
                    iframe.contentDocument.body.innerHTML == "false") {
                    // In Opera event is fired second time
                    // when body.innerHTML changed from false
                    // to server response approx. after 1 sec
                    // when we upload file with iframe
                    return;
                }
            }
            catch (error) {
                //IE may throw an "access is denied" error when attempting to access contentDocument on the iframe in some cases
            }

            callback();
        });
    },
    /**
     * Returns json object received by iframe from server.
     */
    _getIframeContentJSON: function(iframe)
    {
        //IE may throw an "access is denied" error when attempting to access contentDocument on the iframe in some cases
        try {
            // iframe.contentWindow.document - for IE<7
            var doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document,
                response;

            var innerHTML = doc.body.innerHTML;
            this.log("converting iframe's innerHTML to JSON");
            this.log("innerHTML = " + innerHTML);
            //plain text response may be wrapped in <pre> tag
            if (innerHTML.slice(0, 5).toLowerCase() == '<pre>' && innerHTML.slice(-6).toLowerCase() == '</pre>') {
                innerHTML = doc.body.firstChild.firstChild.nodeValue;
            }
            response = eval("(" + innerHTML + ")");
        } catch (err) {
            response = {success: false};
        }

        return response;
    },
    /**
     * Creates iframe with unique name
     */
    _createIframe: function(id)
    {
        var iframe = qq.toElement('<iframe src="javascript:false;" name="' + id + '" />');
        iframe.setAttribute('id', id);
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        return iframe;
    },
    /**
     * Creates form, that will be submitted to iframe
     */
    _createForm: function(iframe, params)
    {
        var protocol = this._options.demoMode ? "GET" : "POST";
        var form = qq.toElement('<form method="' + protocol + '" enctype="multipart/form-data"></form>');

        var queryString = qq.obj2url(params, this._options.action);

        form.setAttribute('action', queryString);
        form.setAttribute('target', iframe.name);
        form.style.display = 'none';
        document.body.appendChild(form);

        return form;
    }
});

/**
 * Class for uploading files using xhr
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerXhr = function(o)
{
    qq.UploadHandlerAbstract.apply(this, arguments);

    this._files = [];
    this._xhrs = [];

    // current loaded size in bytes for each file
    this._loaded = [];
};

// static method
qq.UploadHandlerXhr.isSupported = function()
{
    var input = document.createElement('input');
    input.type = 'file';

    return (
    'multiple' in input &&
    typeof File != "undefined" &&
    typeof FormData != "undefined" &&
    typeof (new XMLHttpRequest()).upload != "undefined" );
};

// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerXhr.prototype, qq.UploadHandlerAbstract.prototype);

qq.extend(qq.UploadHandlerXhr.prototype, {
    /**
     * Adds file to the queue
     * Returns id to use with upload, cancel
     **/
    add: function(file)
    {
        if (!(file instanceof File)) {
            throw new Error('Passed obj in not a File (in qq.UploadHandlerXhr)');
        }

        return this._files.push(file) - 1;
    },
    getName: function(id)
    {
        var file = this._files[id];
        // fix missing name in Safari 4
        return (file.fileName !== null && file.fileName !== undefined) ? file.fileName : file.name;
    },
    getSize: function(id)
    {
        var file = this._files[id];
        return file.fileSize != null ? file.fileSize : file.size;
    },
    /**
     * Returns uploaded bytes for file identified by id
     */
    getLoaded: function(id)
    {
        return this._loaded[id] || 0;
    },
    /**
     * Sends the file identified by id and additional query params to the server
     */
    _upload: function(id, params)
    {
        this._options.onUpload(id, this.getName(id), true);

        var file = this._files[id],
            name = this.getName(id);

        this._loaded[id] = 0;

        var xhr = this._xhrs[id] = new XMLHttpRequest();
        var self = this;

        xhr.upload.onprogress = function(e)
        {
            if (e.lengthComputable) {
                self._loaded[id] = e.loaded;
                self._options.onProgress(id, name, e.loaded, e.total);
            }
        };

        xhr.onreadystatechange = function()
        {
            if (xhr.readyState == 4) {
                self._onComplete(id, xhr);
            }
        };

        // build query string
        params = params || {};
        params[this._options.inputName] = name;
        var queryString = qq.obj2url(params, this._options.action);

        var protocol = this._options.demoMode ? "GET" : "POST";
        xhr.open(protocol, queryString, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(name));
        if (this._options.encoding == 'multipart') {
            var formData = new FormData();
            formData.append(name, file);
            file = formData;
        }
        else {
            xhr.setRequestHeader("Content-Type", "application/octet-stream");
            xhr.setRequestHeader("X-Mime-Type", file.type);
        }
        for (var key in this._options.customHeaders) {
            if (this._options.customHeaders.hasOwnProperty(key)) {
                xhr.setRequestHeader(key, this._options.customHeaders[key]);
            }
        }
        ;
        xhr.send(file);
    },
    _onComplete: function(id, xhr)
    {
        // the request was aborted/cancelled
        if (!this._files[id]) return;

        var name = this.getName(id);
        var size = this.getSize(id);

        this._options.onProgress(id, name, size, size);

        if (xhr.status == 200) {
            this.log("xhr - server response received");
            this.log("responseText = " + xhr.responseText);

            var response;

            try {
                response = eval("(" + xhr.responseText + ")");
            } catch (err) {
                response = {};
            }

            this._options.onComplete(id, name, response);

        }
        else {
            this._options.onError(id, name, xhr);
            this._options.onComplete(id, name, {});
        }

        this._files[id] = null;
        this._xhrs[id] = null;
        this._dequeue(id);
    },
    _cancel: function(id)
    {
        this._options.onCancel(id, this.getName(id));

        this._files[id] = null;

        if (this._xhrs[id]) {
            this._xhrs[id].abort();
            this._xhrs[id] = null;
        }
    }
});

/**
 * A generic module which supports object disposing in dispose() method.
 * */
qq.DisposeSupport = {
    _disposers: [],

    /** Run all registered disposers */
    dispose: function()
    {
        var disposer;
        while (disposer = this._disposers.shift()) {
            disposer();
        }
    },

    /** Add disposer to the collection */
    addDisposer: function(disposeFunction)
    {
        this._disposers.push(disposeFunction);
    },

    /** Attach event handler and register de-attacher as a disposer */
    _attach: function()
    {
        this.addDisposer(qq.attach.apply(this, arguments));
    }
};

// Jamroom Core Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * Set number of rows for pagination
 * @param n Number
 * @param cb {function}
 */
function jrCore_set_pager_rows(n, cb)
{
    jrSetCookie('jrcore_pager_rows', n, 30);
    return cb();
}

/**
 * Set CSRF location cookie
 * Validated on the server site with jrCore_validate_location_url()
 * @param {string} u URL
 */
function jrCore_set_csrf_cookie(u)
{
    return jrSetCookie('jr_location_url', u, 1);
}

/**
 * Set location CSRF cookie and redirect
 * @param {string} u URL
 */
function jrCore_window_location(u)
{
    jrCore_set_csrf_cookie(u);
    window.location = u;
}

/**
 * Creates a checkbox in form to prevent spam bots from submitting forms
 * @param {string} n Name of checkbox element to add
 * @param {number} i Tab Index value for form
 * @return {boolean}
 */
function jrFormSpamBotCheckbox(n, i)
{
    $('#sb_' + n).html('<input type="checkbox" id="' + n + '" name="' + n + '" tabindex="' + i + '">');
    return true;
}

/**
 * Handle Stream URL Errors from the Media Player
 * @param {object} e jPlayer error response object
 * @return {boolean}
 */
function jrCore_stream_url_error(e)
{
    if (e.jPlayer.error.type == 'e_url') {
        // Get module_url from media URL
        var _tm = e.jPlayer.error.context.replace(core_system_url + '/', '').split('/');
        var url = _tm[0];
        $.get(core_system_url + '/' + jrCore_url + '/stream_url_error/' + url + '/__ajax=1', function(r) {
            if (typeof r.error != "undefined" && r.error !== null) {
                jrCore_alert(r.error);
            }
            else if (typeof e.jPlayer.error.message == "undefined" || e.jPlayer.error.message == null) {
                jrCore_alert(e.jPlayer.error.message);
            }
            else {
                jrCore_alert('an unknown error has occured - please try again');
            }
        });
    }
    return true;
}

/**
 * Submits a form handling validation
 * @param {string} fi Form ID to submit
 * @param {string} ck MD5 checksum for validation key
 * @param {string} mt ajax/modal/post - post form as an AJAX form or normal (post) form
 */
function jrFormSubmit(fi, ck, mt)
{
    var rv = false;
    var si = $(fi).find('#form_submit_indicator');
    var sb = $('.form_submit_section input');
    $('.field-hilight').removeClass('field-hilight');
    sb.attr("disabled", "disabled").addClass('form_button_disabled');
    si.show(250, function() {
        var to = setTimeout(function() {
            // get all the inputs into an array.
            $('.form_editor').each(function() {
                $('#' + this.name + '_editor_contents').val(tinyMCE.get('e' + this.name).getContent());
            });
            var values = $(fi).serializeArray();
            // See if we have saved off entries on load
            if (typeof values !== "object" || values.length === 0) {
                si.hide(300, function() {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(fi, "Unable to serialize form elements for submitting!");
                });
                clearTimeout(to);
                return false;
            }
            var action = $(fi).attr("action");
            if (typeof action === "undefined") {
                si.hide(300, function() {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(fi, "Unable to retrieve form action value for submitting");
                });
                clearTimeout(to);
                return false;
            }

            // Handle form validation
            if (typeof ck !== "undefined" && ck !== null) {

                // Submit URL for validation
                $.ajax({
                    type: 'POST',
                    data: values,
                    cache: false,
                    dataType: 'json',
                    url: core_system_url + '/' + jrCore_url + '/form_validate/__ajax=1',
                    success: function(r) {
                        // Handle any messages
                        if (typeof r === "undefined" || r === null) {
                            si.hide(300, function() {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                jrFormSystemError(fi, 'Empty response received from server - please try again');
                            });
                        }
                        else if (typeof r.OK === "undefined" || r.OK != '1') {
                            if (typeof r.redirect != "undefined") {
                                clearTimeout(to);
                                window.location = r.redirect;
                                return true;
                            }
                            else if (typeof r.on_close != "undefined") {
                                clearTimeout(to);
                                return window[r.on_close](r);
                            }
                            jrFormMessages(fi, r);
                        }
                        else {
                            // r is "OK" - looks OK to submit now
                            if (typeof mt == "undefined" || mt == "ajax") {
                                $.ajax({
                                    type: 'POST',
                                    url: action + '/__ajax=1',
                                    data: values,
                                    cache: false,
                                    dataType: 'json',
                                    success: function(_pmsg) {
                                        // Check for URL redirection
                                        if (typeof _pmsg.redirect != "undefined") {
                                            clearTimeout(to);
                                            window.location = _pmsg.redirect;
                                        }
                                        else if (typeof _pmsg.on_close != "undefined") {
                                            clearTimeout(to);
                                            return window[_pmsg.on_close](_pmsg);
                                        }
                                        else {
                                            jrFormMessages(fi, _pmsg);
                                        }
                                        rv = true;
                                    },
                                    error: function(x, t, e) {
                                        si.hide(300, function() {
                                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                                            // See if we got a message back from the core
                                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                                msg = 'JSON response error: ' + x.responseText;
                                            }
                                            jrFormSystemError(fi, msg);
                                        });
                                    }
                                });
                            }

                            // Modal window
                            else if (mt == "modal") {

                                si.hide(600, function() {
                                    var k = $('#jr_html_modal_token').val();
                                    var n = 0;
                                    $('#modal_window').modal();
                                    $('#modal_indicator').show();

                                    // Setup our "listener" which will update our work progress
                                    var sid = setInterval(function() {
                                        sb.removeAttr("disabled").removeClass('form_button_disabled');
                                        $.ajax({
                                            cache: false,
                                            dataType: 'json',
                                            url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                            success: function(t) {
                                                n = 0;
                                                var fnc = 'jrFormModalSubmit_update_process';
                                                window[fnc](t, sid);
                                            },
                                            error: function(r, t, e) {
                                                // Track errors - if we get to 10 we error out
                                                n++;
                                                if (n > 10) {
                                                    clearInterval(sid);
                                                    jrCore_alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                                }
                                            }
                                        })
                                    }, 1000);

                                    // Submit form
                                    $.ajax({
                                        type: 'POST',
                                        url: action + '/__ajax=1',
                                        data: values,
                                        cache: false,
                                        dataType: 'json',
                                        success: function() {
                                            clearTimeout(to);
                                            return true;
                                        }
                                    });
                                });
                            }

                            // normal POST submit
                            else {
                                $(fi).submit();
                                rv = true;
                            }
                        }
                    },
                    error: function(x, t, e) {
                        si.hide(300, function() {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            // See if we got a message back from the core
                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                msg = 'JSON response error: ' + x.responseText;
                            }
                            jrFormSystemError(fi, msg);
                        });
                    }
                });
            }
            // No validation
            else {

                // AJAX or normal submit?
                if (typeof mt == "undefined" || mt == "ajax") {
                    $.ajax({
                        type: 'POST',
                        url: action + '/__ajax=1',
                        data: values,
                        cache: false,
                        dataType: 'json',
                        success: function(r) {
                            // Check for URL redirection
                            if (typeof r.redirect != "undefined") {
                                window.location = r.redirect;
                            }
                            else if (typeof r.on_close != "undefined") {
                                return window[r.on_close](r);
                            }
                            else {
                                jrFormMessages(fi, r);
                            }
                            rv = true;
                        },
                        error: function(x, t, e) {
                            si.hide(300, function() {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                // See if we got a message back from the core
                                var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                    msg = 'JSON response error: ' + x.responseText;
                                }
                                jrFormSystemError(fi, msg);
                            });
                        }
                    });
                }

                // Modal window
                else if (mt == "modal") {

                    si.hide(600, function() {
                        var k = $('#jr_html_modal_token').val();
                        var n = 0;
                        $('#modal_window').modal();
                        $('#modal_indicator').show();
                        // Setup our "listener" which will update our work progress
                        var sid = setInterval(function() {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            $.ajax({
                                cache: false,
                                dataType: 'json',
                                url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                success: function(t) {
                                    n = 0;
                                    var fnc = 'jrFormModalSubmit_update_process';
                                    window[fnc](t, sid);
                                },
                                error: function(r, t, e) {
                                    // Track errors - if we get to 10 we error out
                                    n++;
                                    if (n > 10) {
                                        clearInterval(sid);
                                        jrCore_alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                    }
                                }
                            })
                        }, 1000);

                        // Submit form
                        $.ajax({
                            type: 'POST',
                            url: action + '/__ajax=1',
                            data: values,
                            cache: false,
                            dataType: 'json',
                            success: function() {
                                clearTimeout(to);
                                return true;
                            }
                        });
                    });
                }

                else {
                    $(fi).submit();
                    rv = true;
                }
            }
            clearTimeout(to);
            return rv;
        }, 500);
    });
}

/**
 * Show a system notice
 */
function jrFormSystemError(fi, t)
{
    jrFormMessages(fi + '_msg', {"notices": [{'type': 'error', 'text': t}]});
}

/**
 * jrFormMessages
 */
function jrFormMessages(fi, _msg)
{
    var m = $(fi + '_msg');
    var rv = true;
    $('.page-notice-shown').hide(10);
    // Handle any messages
    if (typeof _msg.notices !== "undefined") {
        for (var n in _msg.notices) {
            if (_msg.notices.hasOwnProperty(n)) {
                m.html(_msg.notices[n].text).removeClass("error success warning notice").addClass(_msg.notices[n].type);
                if (_msg.notices[n].type === 'error') {
                    rv = false;
                }
            }
        }
    }
    // Handle any error fields
    if (typeof _msg.error_fields !== "undefined") {
        for (var e in _msg.error_fields) {
            if (_msg.error_fields.hasOwnProperty(e)) {
                $(_msg.error_fields[e]).addClass('field-hilight');
            }
        }
    }
    else {
        // Remove any previous errors
        $('.field-hilight').removeClass('field-hilight');
    }
    var si = $(fi).find('#form_submit_indicator');
    si.hide(300, function() {
        m.slideDown(150, function() {
            $('.form_submit_section input').removeAttr('disabled').removeClass('form_button_disabled');
            if ($('.simplemodal-container').length === 0 && m.position() && m.position().top < $(window).scrollTop()) {
                $('html,body').animate({scrollTop: (m.position().top - 100)}, 300);
            }
        });
    });
    return rv;
}

/**
 * generic popup window
 */
function popwin(page, name, w, h, scr)
{
    var b = $('body');
    var l = (b.width() / 2) - (w / 2);
    var t = (b.height() / 2) - (h / 2);
    var s = 'height=' + h + ',width=' + w + ',top=' + t + ',left=' + l + ',scrollbars=' + scr + ',resizable';
    var o = window.open(page, name, s);
    if (o.opener === null) {
        o.opener = self;
    }
}

/**
 * The jrSetCookie function will set a Javascript cookie
 */
function jrSetCookie(n, v, d)
{
    var expires = '';
    if (d) {
        var date = new Date();
        date.setTime(date.getTime() + (d * 86400000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = n + "=" + v + expires + "; path=/";
}

/**
 * The jrReadCookie Function will return the value of a previously set cookie
 */
function jrReadCookie(n)
{
    var ne = n + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        {
            if (c.indexOf(ne) === 0) return c.substring(ne.length, c.length);
        }
    }
    return null;
}

/**
 * The jrEraseCookie will remove a cookie set by jrSetCookie()
 */
function jrEraseCookie(n)
{
    jrSetCookie(n, "", -1);
}

/**
 * Check for module updates
 * @param {object} data Message Object
 * @param {number} sid Update Interval Timer name
 * @return {boolean}
 */
function jrFormModalSubmit_update_process(data, sid)
{
    // Check for any error/complete messages
    for (var u in data) {
        if (data.hasOwnProperty(u)) {
            // When our work is complete on the server we will get a "type"
            // message back (complete,update,error)
            var k = $('#jr_html_modal_token');
            if (typeof data[u].t !== "undefined") {
                var e = $('#modal_error');
                var t = $('#modal_updates');
                var s = $('#modal_success');
                switch (data[u].t) {
                    case 'complete':
                        clearInterval(sid);
                        e.hide();
                        s.prepend(data[u].m + '<br><br>').show();
                        jrFormModalCleanup(k.val());
                        break;
                    case 'update':
                        t.prepend(data[u].m + '<br>');
                        break;
                    case 'empty':
                        return true;
                    case 'error':
                        t.prepend(data[u].m + '<br>');
                        s.hide();
                        e.prepend(data[u].m + '<br><br>').show();
                        break;
                    default:
                        clearInterval(sid);
                        jrFormModalCleanup(k.val());
                        break;
                }
            }
            else {
                clearInterval(sid);
                jrFormModalCleanup(k.val());
            }
        }
    }
    return true;
}

/**
 * jrFormModalCleanup
 * @param {string} k Form ID
 * @return {boolean}
 */
function jrFormModalCleanup(k)
{
    $('#modal_indicator').hide();
    $.ajax({
        cache: false,
        url: core_system_url + '/' + jrCore_url + '/form_modal_cleanup/k=' + k + '/__ajax=1'
    });
    return true;
}

/**
 * jrE - encodeURIComponent
 * @param {string} t String to encode
 * @return {string}
 */
function jrE(t)
{
    return encodeURIComponent(t);
}

/**
 * replacement for jQuery $.browser
 * @param ua
 * @returns {{browser: (*|string), version: (*|string)}}
 */
jQuery.uaMatch = function(ua) {
    ua = ua.toLowerCase();
    var match = /(chrome)[ \/]([\w.]+)/.exec(ua) || /(webkit)[ \/]([\w.]+)/.exec(ua) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) || /(msie) ([\w.]+)/.exec(ua) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) || [];
    return {
        browser: match[1] || "",
        version: match[2] || "0"
    };
};

if (!jQuery.browser) {
    matched = jQuery.uaMatch(navigator.userAgent);
    browser = {};
    if (matched.browser) {
        browser[matched.browser] = true;
        browser.version = matched.version;
    }
    // Chrome is Webkit, but Webkit is also Safari.
    if (browser.chrome) {
        browser.webkit = true;
    }
    else if (browser.webkit) {
        browser.safari = true;
    }
    jQuery.browser = browser;
}

/**
 * Load a URL into a DOM element with spinner and fade in/out
 * @param {string} id DOM element
 * @param {string} url URL to load
 * @returns {boolean}
 */
function jrCore_load_into(id, url)
{
    if (typeof url === "undefined") {
        return false;
    }
    var i = $(id);
    i.fadeOut(100, function() {
        i.html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="' + core_system_url + '/' + jrImage_url + '/img/module/jrCore/loading.gif" style="margin:15px"></div>').fadeIn(100, function() {
            i.load(url, function(r) {
                i.html(r);
            });
        })
    });
    return false;
}

/**
 * Delete an Attachment
 * @param {number} id Item ID
 * @param {string} f upload field
 * @param {string} m upload module
 * @param {number} i index
 */
function jrCore_delete_attachment(id, f, m, i)
{
    var action = core_system_url + '/' + jrCore_url + '/attachment_delete';
    jrCore_set_csrf_cookie(action);
    $.ajax({
        type: 'POST',
        url: action + '/__ajax=1',
        data: {id: id, upload_field: f, upload_module: m},
        cache: false,
        dataType: 'json',
        success: function(_pmsg) {
            // Check for URL redirection
            if (typeof _pmsg.success !== "undefined") {
                $('#' + m + '_' + id + '_' + i).fadeOut(300, function() {
                    $(this).remove();
                    var ab = $('#ab' + id + ' .image_delete');
                    if (ab.length === 0) {
                        $('#ab' + id).remove();
                    }
                });
            }
        },
        error: function() {
            jrCore_alert('jamroom: transmission error - please try again');
        }
    });
}

/**
 * Show pending notice for pending item
 * @param {string} n notice
 */
function jrCore_show_pending_notice(n)
{
    jrCore_alert(n);
}

/**
 * Show an alert
 * @param {string} text
 */
function jrCore_alert(text)
{
    swal({
        type: 'warning',
        title: '',
        text: text,
        animation: false,
        confirmButtonText: 'OK',
        closeOnConfirm: true
    });
}

/**
 * Show a confirmation prompt
 * @param {string} title
 * @param {string} text
 * @param {function} conf
 * @return {boolean}
 */
function jrCore_confirm(title, text, conf)
{
    var o = {
        type: 'warning',
        title: title,
        animation: false,
        showCancelButton: true,
        confirmButtonText: 'OK',
        closeOnConfirm: false
    };
    if (typeof text !== "undefined") {
        o.text = text;
    }
    swal(o, function(c) {
        if (c) {
            swal.close();
            return conf();
        }
        else {
            return false;
        }
    });
}



$(document).ready(function (e) {
    if ($(window).width() <= 767) {

        $('.jrform').find("input[type=text], input[type=file], input[type=password], textarea").each(function(ev)
        {
            var text =  $(this).parent().parent().find('.element_left').text().trim();
            if (text.length === 0) {
                text =  $(this).parent().parent().parent().find('.element_left').text().trim();
            }

            var parent = $(this).parent();
            var el = '<span class="form_label">' + text + '</span>';
            parent.prepend(el);
        });

        var cbl = $('.checkbox_left');
        cbl.parent().find('.checkbox_right').append(cbl.text());
    }

});
// Jamroom User Module Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * Show a notification option
 * @param v string ID to show
 */
function jrUser_notification_option(v)
{
    $('.no-act').removeClass('no-act').fadeOut(250, function() {
        $('#' + v).fadeIn(150).addClass('no-act'); 
    });
}

/**
 * jrSiteBuilder PUBLIC Javascript functions
 * @copyright 2015 Talldude Networks, LLC.
 */

/**
 * Load new widget content into a container ( tabbed container )
 * @param p {int} page_id
 * @param l {int} page location
 * @param i {int} widget_id
 */
function jrSiteBuilder_load_tab(p, l, i)
{
    var h = $('#t' + i);
    $('#c' + l + ' li').removeClass('page_tab_active');
    h.addClass('page_tab_active');
    $('#l' + p + '-location-' + l + ' .sb-content-active').removeClass('sb-content-active').fadeOut(100, function()
    {
        $('#w' + i).fadeIn(100).addClass('sb-content-active');
    });
    location.hash = h.data('widget_hash');
}

// Jamroom Graph Module Javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * Display a graph in a modal window
 * @returns {boolean}
 */
function jrGraph_modal_graph(id, module_url, name, modal, element)
{
    // Get any extra posted args on our URL
    var u = core_system_url + '/' + module_url + '/graph/' + name + '/__ajax=1';
    if (typeof element !== "undefined" && element !== null) {
        var i = $(element).attr('href');
        if (typeof i !== "undefined" && i !== null && i.length > 0) {
            u = i;
        }
    }
    $(id).modal();
    $.get(u, function(r) {
        if (r.indexOf('plothover') !== -1) {
            $(id).html(r);
        }
        else {
            $.modal.close();
            alert(r);
        }
    });
    return false;
}
// Jamroom Playlist Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * show the playlist to add this item to.
 */
function jrPlaylist_select(id, mod, pn)
{
    var pid = '#playlist_' + mod + '_' + id;
    var url = '';
    if (typeof pn == "undefined" || pn == null) {
        pn = 1;
    }
    if ($(pid).is(':visible')) {
        jrPlaylist_hide();
        $(pid).fadeOut(100);
    }
    else {
        $('.overlay').hide();
        jrPlaylist_position(mod, id);
        url = core_system_url + '/' + jrPlaylist_url + '/add/' + mod + '/' + Number(id) + '/p=' + Number(pn) + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            'type': 'GET',
            'url': url,
            cache: false,
            success: function(r)
            {
                $(pid).html(r).fadeIn(200);
            }
        });
    }
}

/**
 * position the playlist on the page via javascript so it doesnt get hidden
 * by the overflow hidden on the .row class.
 */
function jrPlaylist_position(module, item_id)
{
    var bid = $('#playlist_button_' + module + '_' + item_id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#playlist_' + module + '_' + item_id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a playlist
 * @param {string} dom_id DOM ID of item to remove on success
 * @param {int} playlist_id Playlist id (will be string for non logged in users)
 * @param {string} playlist_for Module Item belongs to
 * @param {int} item_id Item ID of item being removed
 */
function jrPlaylist_remove(dom_id, playlist_id, playlist_for, item_id)
{
    var url = core_system_url + '/' + jrPlaylist_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            playlist_id: playlist_id,
            playlist_for: playlist_for,
            item_id: item_id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $(dom_id).slideUp(150, function()
                {
                    $(dom_id).remove();
                });
            }
            else {
                jrCore_alert('error received trying to remove item: ' + _msg.error_msg);
            }
        }
    });
}

/**
 * add the selected item to the playlist
 */
function jrPlaylist_inject(playlist_id, item_id, module)
{
    $('.playlist_button').prop('disabled', true).addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPlaylist_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            playlist_id: playlist_id,
            item_id: item_id,
            playlist_for: module
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                jrPlaylist_hide();
            }
            else {
                $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                $('#playlist_message').addClass('playlist_error').text(_msg.success_msg);
            }
        }
    });
}

/**
 * add the selected item to the playlist
 */
function jrPlaylist_new(item_id, module)
{
    $('.playlist_button').prop('disabled', true).addClass('form_button_disabled');
    $('#playlist_indicator').show();
    setTimeout(function()
    {
        var playlist_title = $('#new_playlist_' + item_id).val();
        if (playlist_title.length > 0) {
            var url = core_system_url + '/' + jrPlaylist_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: playlist_title,
                    item_id: item_id,
                    playlist_for: module
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(_msg)
                {
                    if (_msg.success) {
                        $('#playlist_message').addClass('success').text(_msg.success_msg).show();
                        setTimeout(function()
                        {
                            jrPlaylist_hide();
                            $('#playlist_indicator').hide();
                            $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                        }, 1200);
                    }
                    else {
                        $('#playlist_indicator').hide();
                        $('#playlist_message').addClass('error').text(_msg.success_msg).show();
                        $('.playlist_button').prop('disabled', false).removeClass('disabled');
                    }
                }
            });

        }
        else {
            $('#playlist_message').addClass('error').text('please enter a playlist name').show();
        }
    }, 1000);
}

/**
 * the X button to hide the playlist list box.
 */
function jrPlaylist_hide()
{
    $(".playlist_box").fadeOut(100);
}

// Jamroom PhotoAlbum Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * show the photo album to add this item to.
 */
function jrPhotoAlbum_select(id, mod, page)
{
    var pid = $('#photoalbum_' + mod + '_' + id);
    var url = '';
    if (page === null || typeof page === "undefined") {
        if (pid.is(":visible")) {
            jrPhotoAlbum_hide();
            pid.fadeOut(100);
        }
        else {
            $('.overlay').hide();
            jrPhotoAlbum_position(mod, id);
            url = core_system_url + '/' + jrPhotoAlbum_url + '/add/' + mod + '/' + id + '/p=1/__ajax=1';
            jrCore_set_csrf_cookie(url);
            pid.fadeIn(250).load(url, function()
            {
                $('#new_photoalbum_form').find('input.form_text').focus();
            });
        }
    }
    else {
        url = core_system_url + '/' + jrPhotoAlbum_url + '/add/' + mod + '/' + id + '/p=' + Number(page) + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        pid.load(url);
    }
}

/**
 * position the photo album on the page via javascript so it doesnt get hidden
 * by the overflow hidden on the .row class.
 */
function jrPhotoAlbum_position(mod, id)
{
    var bid = $('#photoalbum_button_' + mod + '_' + id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#photoalbum_' + mod + '_' + id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a photo album
 * @param {string} dom_id DOM ID of item to remove on success
 * @param {int} id PhotoAlbum id (will be string for non logged in users)
 * @param {string} mod Module Item belongs to
 * @param {int} item_id Item ID of item being removed
 */
function jrPhotoAlbum_remove(dom_id, id, mod, item_id)
{
    var url = core_system_url + '/' + jrPhotoAlbum_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            photoalbum_id: id,
            photoalbum_for: mod,
            item_id: item_id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('#' + dom_id).slideUp(150, function()
                {
                    $('#' + dom_id).remove();
                });
            }
            else {
                jrCore_alert('error received trying to remove item: ' + _msg.error_msg);
            }
        }
    });
}

/**
 * add the selected item to the photo album
 */
function jrPhotoAlbum_inject(id, item_id, mod)
{
    $('.photoalbum_button').prop('disabled', true).addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPhotoAlbum_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            photoalbum_id: id,
            item_id: item_id,
            photoalbum_for: mod
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                jrPhotoAlbum_hide();
            }
            else {
                $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                $('#photoalbum_message').addClass('photoalbum_error').text(_msg.success_msg);
            }
        }
    });
}

/**
 * Create a new Photo album
 */
function jrPhotoAlbum_new(item_id, mod)
{
    $('.photoalbum_button').prop('disabled', true).addClass('form_button_disabled');
    var i = $('#photoalbum_indicator');
    i.show();
    setTimeout(function()
    {
        var photoalbum_title = $('#new_photoalbum_' + item_id).val();
        if (photoalbum_title.length > 0) {
            var url = core_system_url + '/' + jrPhotoAlbum_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: photoalbum_title,
                    item_id: item_id,
                    photoalbum_for: mod
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(_msg)
                {
                    i.hide();
                    if (_msg.success) {
                        $('#photoalbum_message').removeClass('error').addClass('success').text(_msg.success_msg).show();
                        setTimeout(function()
                        {
                            jrPhotoAlbum_hide();
                            $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                        }, 1500);
                    }
                    else {
                        $('#photoalbum_message').addClass('error').text(_msg.success_msg).show();
                        $('.photoalbum_button').prop('disabled', false).removeClass('disabled');
                    }
                }
            });
        }
        else {
            $('#photoalbum_message').addClass('error').text('please enter a photo album name').show();
        }
    }, 500);
}

/**
 * the X button to hide the photoalbum list box.
 */
function jrPhotoAlbum_hide()
{
    $(".photoalbum_box").fadeOut(100);
}

/**
 * Set number of images wide
 * @param ct int Number of images wide
 */
function jrPhotoAlbum_xup(ct)
{
    var w = Math.floor(99.9 / Number(ct));
    jrSetCookie('jr_photoalbum_xup_width', w, 1);
    $('ul.sortable > li').css('width', w + '%');
    return false;
}

// Jamroom Payments Module Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * View the cart
 */
function jrPayment_view_cart()
{
    var url = core_system_url + '/' + jrPayment_url + '/cart/__ajax=1';
    if ($('#cart-modal').length === 0) {
        $('body').append('<div id="cart-modal"></div>');
    }
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            $('#cart-modal').html(r).modal();
        }
    });
}

/**
 * Close the shopping cart
 */
function jrPayment_close_cart()
{
    $.modal.close();
    $('#cart-modal').hide();
}

/**
 * Add an item to the cart
 * @param m string Module
 * @param i int Item ID
 * @param f string Field
 */
function jrPayment_add_to_cart(m, i, f)
{
    var url = core_system_url + '/' + jrPayment_url + '/add_item_to_cart/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {cart_module: m, cart_item_id: i, cart_field: f},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                $('#payment-view-cart-button').find('span').text(r.item_count).show();
                jrPayment_view_cart();
            }
        }
    });
}

/**
 * Reset the cart
 */
function jrPayment_reset_cart()
{
    var url = core_system_url + '/' + jrPayment_url + '/cart_reset/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error === "undefined") {
                $('#payment-view-cart-button').find('span').hide();
                $.modal.close();
                $('#cart-holder').hide();
            }
            else {
                jrCore_alert(r.error);
            }
        }
    });
}

/**
 * Remove an item from the cart
 */
function jrPayment_remove_item(id)
{
    var url = core_system_url + '/' + jrPayment_url + '/remove_item_from_cart/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: id},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                // If this was the LAST item in the cart, close cart
                if ($('.cart-item-row').length === 1) {
                    jrPayment_close_cart();
                    $('#payment-view-cart-button').find('span').hide();
                }
                else {
                    var s = $('#payment-view-cart-button').find('span');
                    s.text(s.text() - 1);
                    jrPayment_refresh_cart();
                }
            }
        }
    });
    return false;
}

/**
 * Refresh the cart HTML
 */
function jrPayment_refresh_cart()
{
    // Get fresh cart
    var url = core_system_url + '/' + jrPayment_url + '/cart/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            $('#cart-modal').html(r);
        }
    });
}

/**
 * Redirect to login for checkout
 * @returns {boolean}
 */
function jrPayment_checkout_login()
{
    var url = core_system_url + '/' + jrPayment_url + '/checkout_login/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {url: window.location.href},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            jrCore_window_location(r.url);
        }
    });
    return false;

}

/**
 * Update quantity for an item
 * @param {number} t
 * @returns {boolean}
 */
function jrPayment_update_quantity(t)
{
    var q = $('#q' + t);
    var i = q.data('eid');
    var n = q.val();
    $(t).find('input').attr('disabled', 'disabled').addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPayment_url + '/update_quantity_save/_ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: Number(i), qty: Number(n)},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            $(r).find('input').removeAttr('disabled').removeClass('form_button_disabled');
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                if (typeof r.adjusted !== "undefined") {
                    jrCore_confirm(r.title, r.text, function()
                    {
                        jrPayment_refresh_cart();
                    });
                }
                else {
                    jrPayment_refresh_cart();
                }
            }
        }
    });
    return false;

}

// Jamroom jrFollower Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@talldude.net

/**
 * jrFollowProfile
 */
function jrFollowProfile(id, pid)
{
    var bid = '#' + id;
    $(bid).attr('disabled', 'disabled');
    var url = core_system_url + '/' + jrFollower_url + '/follow/' + pid + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(m)
        {
            if (typeof m.error != "undefined") {
                alert('a system level error was encountered submitting the request - please try again');
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
    return false;
}

/**
 * jrUnFollowProfile
 */
function jrUnFollowProfile(id, pid)
{
    var bid = '#' + id;
    $(bid).attr('disabled', 'disabled');
    var url = core_system_url + '/' + jrFollower_url + '/unfollow/' + pid + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(m)
        {
            if (typeof m.error != "undefined") {
                alert('a system level error was encountered submitting the request - please try again');
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
    return false;
}

/**
 * Get profiles followed by a user
 */
function jrFollower_get_followed()
{
    var url = core_system_url + '/' + jrFollower_url + '/get_followed/__ajax=1';
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(r)
        {
            if (typeof r.error != "undefined") {
                alert(r.error);
            }
            else {
                var e = $('.follow_entry');
                if (e.length > 0) {
                    e.each(function()
                    {
                        var i = $(this).attr('data-id');
                        if (typeof r.following[i] !== "undefined") {
                            if (r.following[i] == 1) {
                                $('#a' + i).show();
                            }
                            else {
                                $('#p' + i).show();
                            }
                        }
                        else {
                            $('#f' + i).show();
                        }
                    });
                }
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
}

/**
 * jrSearch Javascript functions
 * @copyright 2012 Talldude Networks, LLC.
 */

/**
 * Search for results within a specific module
 */
function jrSearch_module_index(url, fields)
{
    var ss = $('#search_module').val();
    if (ss.length > 0) {
        $('#form_submit_indicator').show(300, function()
        {
            window.location = core_system_url + '/' + url + '/ss=' + jrE(ss);
        });
    }
    return false;
}

/**
 * Display a modal search form
 */
function jrSearch_modal_form()
{
    $('#searchform').modal({

        onOpen: function(d)
        {
            d.overlay.fadeIn(75, function()
            {
                d.container.slideDown(0, function()
                {
                    d.data.fadeIn(300, function()
                    {
                        $('#searchform .form_text').focus();
                    });
                });
            });
        },
        onClose: function(d)
        {
            d.data.fadeOut('fast', function()
            {
                d.container.hide('fast', function()
                {
                    d.overlay.fadeOut('fast', function()
                    {
                        $.modal.close();
                    });
                });
            });
        },
        overlayClose: true
    });
}

/**
 * Re-run search on a result page
 */
function jrSearch_refine_results()
{
    $('#form_submit_indicator').show(300, function()
    {
        setTimeout(function()
        {
            $('#sr').submit();
        }, 300);
    });
}
// Jamroom 5 Tour Module Javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * Stop showing tours to the user
 */
function jrTips_stop_tour()
{
    var url = core_system_url + '/' + jrTips_url + '/stop_tour/__ajax=1';
    $.post(url, function(data) {
        if (typeof data !== "undefined" && data.success) {
            $('.qtip').remove();
            if (typeof __tt != "undefined") {
                var _tmp = __tt.split(',');
                for (var i = 0; i < _tmp.length; i++) {
                    $(_tmp[i]).qtip('hide').qtip('destroy', true);
                }
            }
            alert(data.success);
            window.location.reload();
        }
        else {
            alert(data.error);
        }
    });
}

/**
 * Close an in progress tour
 * @param module string Module for tips being closed
 * @param modal int set to 1 to close open modal window
 */
function jrTips_close_tour(module, modal)
{
    $('.qtip').remove();
    if (typeof __tt != "undefined") {
        var _map = jrReadCookie('jrTips_hide');
        if (typeof _map != "undefined" && _map != null) {
            _map = jQuery.parseJSON(_map);
        }
        else {
            _map = {};
        }
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            $(_tmp[i]).qtip('hide').qtip('destroy', true);
        }
        _map[module] = 1;
        jrSetCookie('jrTips_hide', JSON.stringify(_map), 28);
        if (modal == 1) {
            $.modal.close();
        }
        return true;
    }
    return false;
}

/**
 * Close a tip but don't turn off tours
 */
function jrTips_close_tip()
{
    $('.qtip').remove();
    if (typeof __tt != "undefined") {
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            $(_tmp[i]).qtip('hide').qtip('destroy', true);
        }
    }
    return true;
}

/**
 * Restart a Tips tour for a module
 * @param module string Module to restart Tips for
 * @param url string URL to load to start Tour
 */
function jrTips_restart_tour(module, url)
{
    var _map = jrReadCookie('jrTips_hide');
    if (typeof _map != "undefined" && _map != null) {
        _map = jQuery.parseJSON(_map);
        if (typeof _map[module] !== "undefined") {
            delete _map[module];
        }
        jrSetCookie('jrTips_hide', JSON.stringify(_map), 28);
    }
    jrCore_window_location(url);
}

/**
 * Show a YouTube Video in a Modal Window
 * @param s string QTip selector
 * @param id string YouTube video ID
 */
function jrTips_play_youtube(s, id)
{
    $(s + ' .qtip').remove();
    if (typeof __tt != "undefined") {
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            if (s == _tmp[i]) {
                $(_tmp[i]).qtip('destroy', true);
            }
        }
    }
    $('#y' + id).modal()
}

// Jamroom Audio Support Module
// @copyright 2003-2015 by Talldude Networks LLC

// This is a STUB
// Jamroom OneAll Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Set Quota ID cookie
 * @returns {boolean}
 */
function jrOneAll_set_quota_id()
{
    var q = $('#quota_id').val();
    if (typeof q !== "undefined") {
        jrSetCookie('signup_quota_id', Number(q), 2);
    }
    return true;
}
/*
 * jQuery UI Nested Sortable
 * v 2.0 / 29 oct 2012
 * http://mjsarfatti.com/sandbox/nestedSortable
 *
 * Depends on:
 *	 jquery.ui.sortable.js 1.10+
 *
 * Copyright (c) 2010-2013 Manuele J Sarfatti
 * Licensed under the MIT License
 * http://www.opensource.org/licenses/mit-license.php
 */

(function($) {

	function isOverAxis( x, reference, size ) {
		return ( x > reference ) && ( x < ( reference + size ) );
	}

	$.widget("mjs.nestedSortable", $.extend({}, $.ui.sortable.prototype, {

		options: {
			doNotClear: false,
			expandOnHover: 700,
			isAllowed: function(placeholder, placeholderParent, originalItem) { return true; },
			isTree: false,
			listType: 'ol',
			maxLevels: 0,
			protectRoot: false,
			rootID: null,
			rtl: false,
			startCollapsed: false,
			tabSize: 20,

			branchClass: 'mjs-nestedSortable-branch',
			collapsedClass: 'mjs-nestedSortable-collapsed',
			disableNestingClass: 'mjs-nestedSortable-no-nesting',
			errorClass: 'mjs-nestedSortable-error',
			expandedClass: 'mjs-nestedSortable-expanded',
			hoveringClass: 'mjs-nestedSortable-hovering',
			leafClass: 'mjs-nestedSortable-leaf'
		},

		_create: function() {
			this.element.data('ui-sortable', this.element.data('mjs-nestedSortable'));

			// mjs - prevent browser from freezing if the HTML is not correct
			if (!this.element.is(this.options.listType))
				throw new Error('nestedSortable: Please check that the listType option is set to your actual list type');

			// mjs - force 'intersect' tolerance method if we have a tree with expanding/collapsing functionality
			if (this.options.isTree && this.options.expandOnHover) {
				this.options.tolerance = 'intersect';
			}

			$.ui.sortable.prototype._create.apply(this, arguments);

			// mjs - prepare the tree by applying the right classes (the CSS is responsible for actual hide/show functionality)
			if (this.options.isTree) {
				var self = this;
				$(this.items).each(function() {
					var $li = this.item;
					if ($li.children(self.options.listType).length) {
						$li.addClass(self.options.branchClass);
						// expand/collapse class only if they have children
						if (self.options.startCollapsed) $li.addClass(self.options.collapsedClass);
						else $li.addClass(self.options.expandedClass);
					} else {
						$li.addClass(self.options.leafClass);
					}
				})
			}
		},

		_destroy: function() {
			this.element
				.removeData("mjs-nestedSortable")
				.removeData("ui-sortable");
			return $.ui.sortable.prototype._destroy.apply(this, arguments);
		},

		_mouseDrag: function(event) {
			var i, item, itemElement, intersection,
				o = this.options,
				scrolled = false;

			//Compute the helpers position
			this.position = this._generatePosition(event);
			this.positionAbs = this._convertPositionTo("absolute");

			if (!this.lastPositionAbs) {
				this.lastPositionAbs = this.positionAbs;
			}

			//Do scrolling
			if(this.options.scroll) {
				if(this.scrollParent[0] != document && this.scrollParent[0].tagName != 'HTML') {

					if((this.overflowOffset.top + this.scrollParent[0].offsetHeight) - event.pageY < o.scrollSensitivity) {
						this.scrollParent[0].scrollTop = scrolled = this.scrollParent[0].scrollTop + o.scrollSpeed;
					} else if(event.pageY - this.overflowOffset.top < o.scrollSensitivity) {
						this.scrollParent[0].scrollTop = scrolled = this.scrollParent[0].scrollTop - o.scrollSpeed;
					}

					if((this.overflowOffset.left + this.scrollParent[0].offsetWidth) - event.pageX < o.scrollSensitivity) {
						this.scrollParent[0].scrollLeft = scrolled = this.scrollParent[0].scrollLeft + o.scrollSpeed;
					} else if(event.pageX - this.overflowOffset.left < o.scrollSensitivity) {
						this.scrollParent[0].scrollLeft = scrolled = this.scrollParent[0].scrollLeft - o.scrollSpeed;
					}

				} else {

					if(event.pageY - $(document).scrollTop() < o.scrollSensitivity) {
						scrolled = $(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
					} else if($(window).height() - (event.pageY - $(document).scrollTop()) < o.scrollSensitivity) {
						scrolled = $(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
					}

					if(event.pageX - $(document).scrollLeft() < o.scrollSensitivity) {
						scrolled = $(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
					} else if($(window).width() - (event.pageX - $(document).scrollLeft()) < o.scrollSensitivity) {
						scrolled = $(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
					}

				}

				if(scrolled !== false && $.ui.ddmanager && !o.dropBehaviour)
					$.ui.ddmanager.prepareOffsets(this, event);
			}

			//Regenerate the absolute position used for position checks
			this.positionAbs = this._convertPositionTo("absolute");

			// mjs - find the top offset before rearrangement,
			var previousTopOffset = this.placeholder.offset().top;

			//Set the helper position
			if(!this.options.axis || this.options.axis !== "y") {
				this.helper[0].style.left = this.position.left+"px";
			}
			if(!this.options.axis || this.options.axis !== "x") {
				this.helper[0].style.top = this.position.top+"px";
			}

			// mjs - check and reset hovering state at each cycle
			this.hovering = this.hovering ? this.hovering : null;
			this.mouseentered = this.mouseentered ? this.mouseentered : false;

			// mjs - let's start caching some variables
			var parentItem = (this.placeholder[0].parentNode.parentNode &&
							 $(this.placeholder[0].parentNode.parentNode).closest('.ui-sortable').length)
				       			? $(this.placeholder[0].parentNode.parentNode)
				       			: null,
			    level = this._getLevel(this.placeholder),
			    childLevels = this._getChildLevels(this.helper);

			var newList = document.createElement(o.listType);

			//Rearrange
			for (i = this.items.length - 1; i >= 0; i--) {

				//Cache variables and intersection, continue if no intersection
				item = this.items[i];
				itemElement = item.item[0];
				intersection = this._intersectsWithPointer(item);
				if (!intersection) {
					continue;
				}

				// Only put the placeholder inside the current Container, skip all
				// items form other containers. This works because when moving
				// an item from one container to another the
				// currentContainer is switched before the placeholder is moved.
				//
				// Without this moving items in "sub-sortables" can cause the placeholder to jitter
				// beetween the outer and inner container.
				if (item.instance !== this.currentContainer) {
					continue;
				}

				// cannot intersect with itself
				// no useless actions that have been done before
				// no action if the item moved is the parent of the item checked
				if (itemElement !== this.currentItem[0] &&
					this.placeholder[intersection === 1 ? "next" : "prev"]()[0] !== itemElement &&
					!$.contains(this.placeholder[0], itemElement) &&
					(this.options.type === "semi-dynamic" ? !$.contains(this.element[0], itemElement) : true)
				) {

					// mjs - we are intersecting an element: trigger the mouseenter event and store this state
					if (!this.mouseentered) {
						$(itemElement).mouseenter();
						this.mouseentered = true;
					}

					// mjs - if the element has children and they are hidden, show them after a delay (CSS responsible)
					if (o.isTree && $(itemElement).hasClass(o.collapsedClass) && o.expandOnHover) {
						if (!this.hovering) {
							$(itemElement).addClass(o.hoveringClass);
							var self = this;
							this.hovering = window.setTimeout(function() {
								$(itemElement).removeClass(o.collapsedClass).addClass(o.expandedClass);
								self.refreshPositions();
								self._trigger("expand", event, self._uiHash());
							}, o.expandOnHover);
						}
					}

					this.direction = intersection == 1 ? "down" : "up";

					// mjs - rearrange the elements and reset timeouts and hovering state
					if (this.options.tolerance == "pointer" || this._intersectsWithSides(item)) {
						$(itemElement).mouseleave();
						this.mouseentered = false;
						$(itemElement).removeClass(o.hoveringClass);
						this.hovering && window.clearTimeout(this.hovering);
						this.hovering = null;

						// mjs - do not switch container if it's a root item and 'protectRoot' is true
						// or if it's not a root item but we are trying to make it root
						if (o.protectRoot
							&& ! (this.currentItem[0].parentNode == this.element[0] // it's a root item
								  && itemElement.parentNode != this.element[0]) // it's intersecting a non-root item
						) {
							if (this.currentItem[0].parentNode != this.element[0]
							   	&& itemElement.parentNode == this.element[0]
							) {

								if ( ! $(itemElement).children(o.listType).length) {
									itemElement.appendChild(newList);
									o.isTree && $(itemElement).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
								}

								var a = this.direction === "down" ? $(itemElement).prev().children(o.listType) : $(itemElement).children(o.listType);
								if (a[0] !== undefined) {
									this._rearrange(event, null, a);
								}

							} else {
								this._rearrange(event, item);
							}
						} else if ( ! o.protectRoot) {
							this._rearrange(event, item);
						}
					} else {
						break;
					}

					// Clear emtpy ul's/ol's
					this._clearEmpty(itemElement);

					this._trigger("change", event, this._uiHash());
					break;
				}
			}

			// mjs - to find the previous sibling in the list, keep backtracking until we hit a valid list item.
			var previousItem = this.placeholder[0].previousSibling ? $(this.placeholder[0].previousSibling) : null;
			if (previousItem != null) {
				while (previousItem[0].nodeName.toLowerCase() != 'li' || previousItem[0] == this.currentItem[0] || previousItem[0] == this.helper[0]) {
					if (previousItem[0].previousSibling) {
						previousItem = $(previousItem[0].previousSibling);
					} else {
						previousItem = null;
						break;
					}
				}
			}

			// mjs - to find the next sibling in the list, keep stepping forward until we hit a valid list item.
			var nextItem = this.placeholder[0].nextSibling ? $(this.placeholder[0].nextSibling) : null;
			if (nextItem != null) {
				while (nextItem[0].nodeName.toLowerCase() != 'li' || nextItem[0] == this.currentItem[0] || nextItem[0] == this.helper[0]) {
					if (nextItem[0].nextSibling) {
						nextItem = $(nextItem[0].nextSibling);
					} else {
						nextItem = null;
						break;
					}
				}
			}

			this.beyondMaxLevels = 0;

			// mjs - if the item is moved to the left, send it one level up but only if it's at the bottom of the list
			if (parentItem != null
				&& nextItem == null
				&& ! (o.protectRoot && parentItem[0].parentNode == this.element[0])
				&&
					(o.rtl && (this.positionAbs.left + this.helper.outerWidth() > parentItem.offset().left + parentItem.outerWidth())
					 || ! o.rtl && (this.positionAbs.left < parentItem.offset().left))
			) {

				parentItem.after(this.placeholder[0]);
				if (o.isTree && parentItem.children(o.listItem).children('li:visible:not(.ui-sortable-helper)').length < 1) {
					parentItem.removeClass(this.options.branchClass + ' ' + this.options.expandedClass)
							  .addClass(this.options.leafClass);
				}
				this._clearEmpty(parentItem[0]);
				this._trigger("change", event, this._uiHash());
			}
			// mjs - if the item is below a sibling and is moved to the right, make it a child of that sibling
			else if (previousItem != null
					 && ! previousItem.hasClass(o.disableNestingClass)
					 &&
						(previousItem.children(o.listType).length && previousItem.children(o.listType).is(':visible')
						 || ! previousItem.children(o.listType).length)
					 && ! (o.protectRoot && this.currentItem[0].parentNode == this.element[0])
					 &&
						(o.rtl && (this.positionAbs.left + this.helper.outerWidth() < previousItem.offset().left + previousItem.outerWidth() - o.tabSize)
						 || ! o.rtl && (this.positionAbs.left > previousItem.offset().left + o.tabSize))
			) {

				this._isAllowed(previousItem, level, level+childLevels+1);

				if (!previousItem.children(o.listType).length) {
					previousItem[0].appendChild(newList);
					o.isTree && previousItem.removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
				}

		        // mjs - if this item is being moved from the top, add it to the top of the list.
		        if (previousTopOffset && (previousTopOffset <= previousItem.offset().top)) {
		        	previousItem.children(o.listType).prepend(this.placeholder);
		        }
		        // mjs - otherwise, add it to the bottom of the list.
		        else {
					previousItem.children(o.listType)[0].appendChild(this.placeholder[0]);
				}

				this._trigger("change", event, this._uiHash());
			}
			else {
				this._isAllowed(parentItem, level, level+childLevels);
			}

			//Post events to containers
			this._contactContainers(event);

			//Interconnect with droppables
			if($.ui.ddmanager) {
				$.ui.ddmanager.drag(this, event);
			}

			//Call callbacks
			this._trigger('sort', event, this._uiHash());

			this.lastPositionAbs = this.positionAbs;
			return false;

		},

		_mouseStop: function(event, noPropagation) {

			// mjs - if the item is in a position not allowed, send it back
			if (this.beyondMaxLevels) {

				this.placeholder.removeClass(this.options.errorClass);

				if (this.domPosition.prev) {
					$(this.domPosition.prev).after(this.placeholder);
				} else {
					$(this.domPosition.parent).prepend(this.placeholder);
				}

				this._trigger("revert", event, this._uiHash());

			}


			// mjs - clear the hovering timeout, just to be sure
			$('.'+this.options.hoveringClass).mouseleave().removeClass(this.options.hoveringClass);
			this.mouseentered = false;
			this.hovering && window.clearTimeout(this.hovering);
			this.hovering = null;

			$.ui.sortable.prototype._mouseStop.apply(this, arguments);

		},

		// mjs - this function is slightly modified to make it easier to hover over a collapsed element and have it expand
		_intersectsWithSides: function(item) {

			var half = this.options.isTree ? .8 : .5;

			var isOverBottomHalf = isOverAxis(this.positionAbs.top + this.offset.click.top, item.top + (item.height*half), item.height),
				isOverTopHalf = isOverAxis(this.positionAbs.top + this.offset.click.top, item.top - (item.height*half), item.height),
				isOverRightHalf = isOverAxis(this.positionAbs.left + this.offset.click.left, item.left + (item.width/2), item.width),
				verticalDirection = this._getDragVerticalDirection(),
				horizontalDirection = this._getDragHorizontalDirection();

			if (this.floating && horizontalDirection) {
				return ((horizontalDirection == "right" && isOverRightHalf) || (horizontalDirection == "left" && !isOverRightHalf));
			} else {
				return verticalDirection && ((verticalDirection == "down" && isOverBottomHalf) || (verticalDirection == "up" && isOverTopHalf));
			}

		},

		_contactContainers: function(event) {

			if (this.options.protectRoot && this.currentItem[0].parentNode == this.element[0] ) {
				return;
			}

			$.ui.sortable.prototype._contactContainers.apply(this, arguments);

		},

		_clear: function(event, noPropagation) {

			$.ui.sortable.prototype._clear.apply(this, arguments);

			// mjs - clean last empty ul/ol
			for (var i = this.items.length - 1; i >= 0; i--) {
				var item = this.items[i].item[0];
				this._clearEmpty(item);
			}

		},

		serialize: function(options) {

			var o = $.extend({}, this.options, options),
				items = this._getItemsAsjQuery(o && o.connected),
			    str = [];

			$(items).each(function() {
				var res = ($(o.item || this).attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/)),
				    pid = ($(o.item || this).parent(o.listType)
						.parent(o.items)
						.attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/));

				if (res) {
					str.push(((o.key || res[1]) + '[' + (o.key && o.expression ? res[1] : res[2]) + ']')
						+ '='
						+ (pid ? (o.key && o.expression ? pid[1] : pid[2]) : o.rootID));
				}
			});

			if(!str.length && o.key) {
				str.push(o.key + '=');
			}

			return str.join('&');

		},

		toHierarchy: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [];

			$(this.element).children(o.items).each(function () {
				var level = _recursiveItems(this);
				ret.push(level);
			});

			return ret;

			function _recursiveItems(item) {
				var id = ($(item).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
				if (id) {
					var currentItem = {"id" : id[2]};
					if ($(item).children(o.listType).children(o.items).length > 0) {
						currentItem.children = [];
						$(item).children(o.listType).children(o.items).each(function() {
							var level = _recursiveItems(this);
							currentItem.children.push(level);
						});
					}
					return currentItem;
				}
			}
		},

		toArray: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [],
			    left = 1;

			if (!o.excludeRoot) {
				ret.push({
					"item_id": o.rootID,
					"parent_id": null,
					"depth": sDepth,
					"left": left,
					"right": ($(o.items, this.element).length + 1) * 2
				});
				left++
			}

			$(this.element).children(o.items).each(function () {
				left = _recursiveArray(this, sDepth + 1, left);
			});

			ret = ret.sort(function(a,b){ return (a.left - b.left); });

			return ret;

			function _recursiveArray(item, depth, left) {

				var right = left + 1,
				    id,
				    pid;

				if ($(item).children(o.listType).children(o.items).length > 0) {
					depth ++;
					$(item).children(o.listType).children(o.items).each(function () {
						right = _recursiveArray($(this), depth, right);
					});
					depth --;
				}

				id = ($(item).attr(o.attribute || 'id')).match(o.expression || (/(.+)[-=_](.+)/));

				if (depth === sDepth + 1) {
					pid = o.rootID;
				} else {
					var parentItem = ($(item).parent(o.listType)
											 .parent(o.items)
											 .attr(o.attribute || 'id'))
											 .match(o.expression || (/(.+)[-=_](.+)/));
					pid = parentItem[2];
				}

				if (id) {
						ret.push({"item_id": id[2], "parent_id": pid, "depth": depth, "left": left, "right": right});
				}

				left = right + 1;
				return left;
			}

		},

		_clearEmpty: function(item) {
			var o = this.options;

			var emptyList = $(item).children(o.listType);

			if (emptyList.length && !emptyList.children().length && !o.doNotClear) {
				o.isTree && $(item).removeClass(o.branchClass + ' ' + o.expandedClass).addClass(o.leafClass);
				emptyList.remove();
			} else if (o.isTree && emptyList.length && emptyList.children().length && emptyList.is(':visible')) {
				$(item).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
			} else if (o.isTree && emptyList.length && emptyList.children().length && !emptyList.is(':visible')) {
				$(item).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.collapsedClass);
			}

		},

		_getLevel: function(item) {

			var level = 1;

			if (this.options.listType) {
				var list = item.closest(this.options.listType);
				while (list && list.length > 0 &&
                    	!list.is('.ui-sortable')) {
					level++;
					list = list.parent().closest(this.options.listType);
				}
			}

			return level;
		},

		_getChildLevels: function(parent, depth) {
			var self = this,
			    o = this.options,
			    result = 0;
			depth = depth || 0;

			$(parent).children(o.listType).children(o.items).each(function (index, child) {
					result = Math.max(self._getChildLevels(child, depth + 1), result);
			});

			return depth ? result + 1 : result;
		},

		_isAllowed: function(parentItem, level, levels) {
			var o = this.options,
				maxLevels = this.placeholder.closest('.ui-sortable').nestedSortable('option', 'maxLevels'); // this takes into account the maxLevels set to the recipient list

			// mjs - is the root protected?
			// mjs - are we nesting too deep?
			if ( ! o.isAllowed(this.placeholder, parentItem, this.currentItem)) {
					this.placeholder.addClass(o.errorClass);
					if (maxLevels < levels && maxLevels != 0) {
						this.beyondMaxLevels = levels - maxLevels;
					} else {
						this.beyondMaxLevels = 1;
					}
			} else {
				if (maxLevels < levels && maxLevels != 0) {
					this.placeholder.addClass(o.errorClass);
					this.beyondMaxLevels = levels - maxLevels;
				} else {
					this.placeholder.removeClass(o.errorClass);
					this.beyondMaxLevels = 0;
				}
			}
		}

	}));

	$.mjs.nestedSortable.prototype.options = $.extend({}, $.ui.sortable.prototype.options, $.mjs.nestedSortable.prototype.options);
})(jQuery);

// Jamroom Chat Module Javascript
// @copyright 2003-2017 by Talldude Networks LLC

var __jrchat_iv;
var __jrchat_cr = '';
var __jrchat_bc = '';
var __jrchat_nr = '';
var __jrchat_ip = false;
var __jrchat_lm = false;
var __jrchat_ls = 5000;
var __jrchat_cc = 30000;

$(window).resize(function()
{
    jrChat_set_chat_height();
});

window.addEventListener("focus", function()
{
    jrChat_set_local_item('focused', 1);
    if (jrChat_is_mobile_view()) {
        var ttl = document.title;
        if (ttl.indexOf('[') === 0) {
            var brk = ttl.indexOf(']');
            document.title = ttl.substr(brk + 2);
        }
    }
});

window.addEventListener("blur", function()
{
    jrChat_set_local_item('focused', 0);
});

$(window).on('beforeunload', function()
{
    if ($('#jrchat-room').length > 0) {
        
        var c = $('#jrchat-messages');
        var h = c.html();
        jrChat_set_local_item('messages', h);

        var t = $('#jrchat-new-message-input').val();
        jrChat_set_local_item('cm_content', t);

        var l = $('#jrchat-active-title').text();
        jrChat_set_local_item('active_room_title', l);

        var b = $('.jrchat-bubble').text();
        jrChat_set_local_item('active_room_users', b);

    }
    __jrchat_ls = 5000;
    __jrchat_lm = false;
    __jrchat_ip = false;
    
});

$(document).ready(function()
{
    if ($('#jrchat-room').length) {
        jrChat_init();
    }
});

/**
 * Initialize Chat
 */
function jrChat_init(cb)
{
    var r = true;

    // Restore what user was typing
    var p = jrChat_get_local_item('cm_content', '');
    if (p.length > 0 && p != "undefined") {
        $('#jrchat-new-message-input').val(p);
    }
    jrChat_set_local_item('cm_content', '');

    var h = jrChat_get_local_item('messages', '');
    if (h !== null && typeof h !== "undefined" && h.length > 10) {

        var b = jrChat_get_local_item('active_room_users', 0);
        $('.jrchat-bubble').text(b);

        // Restore messages from page load
        var chm = $('#jrchat-messages');

        // How many messages are we restoring?
        var num = $(h).filter('.jrchat-msg').length;
        if (num < 200 && num > 0) {
            // Restore - otherwise load
            chm.html(h);
            $('#jrchat-page-limit').remove();
            var lst = $('.jrchat-msg').last().attr('id').replace('m', '');
            if (lst >= jrChat_get_last_id()) {
                r = false;
                jrChat_scroll_to_bottom(0);
                jrChat_init_pager();
                jrChat_init_chat_controls();
            }
        }
    }
    jrChat_set_local_item('messages', '');
    jrChat_set_local_item('loop_timer', __jrchat_ls);
    jrChat_set_local_item('loop_number', 0);
    jrChat_set_chat_height();
    jrChat_store_fixed_element_positions();

    var c = $('#jrchat-room');
    var i = jrChat_get_notification_number();
    var s = jrChat_get_item('state', 'closed');
    if (s == 'open' || c.css('right') == '0px') {
        jrChat_set_local_item('focused', 1);
        jrChat_set_initial_tab_state('open');
        if (c.css('right').indexOf('-') === 0) {
            // We are supposed to be open...
            jrChat_toggle('open');
        }
        var w = jrChat_get_local_item('width', 0);
        if (w == 0) {
            w = Number($('#jrchat-width').text().replace('px', ''));
            if (typeof w == "undefined" || w == null || w > 640 || w < 280) {
                w = 400;
            }
        }
        jrChat_position_fixed_elements(w, 0, w);
        jrChat_set_item('state', 'open');
        jrChat_set_new_indicator(i);
    }
    else {
        jrChat_set_item('state', 'closed');
        jrChat_set_notification_number(Number(i));
        __jrchat_ls = __jrchat_cc;
        jrChat_set_local_item('loop_timer', __jrchat_cc);
    }

    // Get our active room_id and init
    jrChat_get_active_room_id(function(i)
    {
        if (i == 0) {
            jrChat_show_no_chat_rooms();
            if (typeof cb == "function") {
                return cb();
            }
        }
        else {
            if (r) {
                // new browser window
                jrChat_get_messages(0, function()
                {
                    jrChat_init_chat_controls();
                    jrChat_watch_loop();
                });
            }
            else {
                jrChat_watch_loop();
            }
            if (jrChat_get_tab_state() == 'open') {
                $('#jrchat-new-message-input').focus().jrChat_is_typing();
            }
            if (typeof cb == "function") {
                return cb();
            }
        }
    });

}

/**
 * Initialize chat controls
 */
function jrChat_init_chat_controls()
{
    var c;
    if (jrChat_is_admin()) {
        c = '.jrchat-msg';
    }
    else {
        c = '.jrchat-msg-from';
    }
    if (c.length > 0) {
        $('#jrchat-messages').on('mouseenter', c, function()
        {
            $(this).append(jrChat_get_chat_controls()).show();

        }).on('mouseleave', c, function()
        {
            $('.jrchat-controls').remove();

        }).on('click', '.jrchat-controls', function()
        {
            var i = Number($(this).parent().attr('id').replace('m', ''));
            if (i > 0) {
                jrChat_delete_message_id(i);
            }
        });
    }
}

/**
 * Delete a chat message
 * @param i int Message ID
 */
function jrChat_delete_message_id(i)
{
    var url = core_system_url + '/' + jrChat_url + '/delete_message/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: i},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }
            var id = $('#m' + i);
            if (typeof r.ok !== "undefined") {
                id.remove();
            }
            else if (id.length > 0) {
                jrCore_alert(r.error);
            }
        }
    });
}

/**
 * Get chat controls
 * @returns {string}
 */
function jrChat_get_chat_controls()
{
    if (__jrchat_cr.length === 0) {
        var c = $('#jrchat-controls-holder');
        __jrchat_cr = c.html();
        c.remove();
    }
    return __jrchat_cr;
}

/**
 * Get "beginning of chat" message
 * @returns {string}
 */
function jrChat_get_beginning_of_chat()
{
    if (__jrchat_bc.length === 0) {
        var c = $('#jrchat-beginning-holder');
        __jrchat_bc = c.html();
        c.remove();
    }
    return __jrchat_bc;
}

/**
 * Show "no chat rooms" message
 * @returns {string}
 */
function jrChat_show_no_chat_rooms()
{
    if (__jrchat_nr.length === 0 && $('.jrchat-msg').length === 0) {
        var c = $('#jrchat-no-room-holder');
        __jrchat_nr = c.html();
        c.remove();
    }
    var chm = $('#jrchat-messages');
    if (chm.find('#jrchat-no-room-notice').length === 0) {
        chm.append(__jrchat_nr);
    }
}

/**
 * Complete file uploads to a chat
 */
function jrChat_complete_file_uploads()
{
    var tkn = $('#jrchat-new-message').find("input[name='upload_token']").val();
    if (typeof tkn == "undefined") {
        jrCore_alert('error uploading file - unable to determine upload token');
    }
    else {
        var rid = jrChat_get_current_room_id();
        var url = core_system_url + '/' + jrChat_url + '/upload_files/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {upload_token: tkn, room_id: rid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                jrChat_get_new_messages();
            }
        });
    }
}

/**
 * Get the active chat room id
 * @param cb function
 */
function jrChat_get_active_room_id(cb)
{
    var ri = 0;
    var id = jrChat_get_current_room_id();
    if (id > 0) {
        ri = id;
    }
    cb(ri);
}

/**
 * Set the active room title
 * @param t string
 */
function jrChat_set_active_room_title(t)
{
    $('#jrchat-active-title').text(t);
    jrChat_set_local_item('active_room_title', t);
    return true;
}

/**
 * Initialize user live search
 */
function jrChat_init_live_search(id)
{
    setTimeout(function()
    {
        var i = $('#' + id);
        if (i.length > 0) {
            i.liveSearch({url: core_system_url + '/' + jrChat_url + '/search_users/' + id + '/q=', typeDelay: 400});
        }
    }, 200);
}

/**
 * Initialize the previous page pager
 */
function jrChat_init_pager()
{
    var nxp = $('#jrchat-load-next-page');
    if (nxp.length === 0) {
        $('#jrchat-messages').prepend('<div id="jrchat-load-next-page"></div>');
    }
    nxp.off('inview');
    nxp.on('inview', function(e, iv)
    {
        if (iv) {
            var b = jrChat_get_local_item('before_id');
            jrChat_get_messages(b);
        }
    });
}

/**
 * Resize chat interface
 */
function jrChat_set_chat_height()
{
    var c = $('#jrchat-messages');
    if (c.is(':visible')) {
        var h = $(window).height();
        $('#jrchat-room').height(h);

        var m = $('#jrchat-new-message').outerHeight();
        var t = $('#jrchat-title').outerHeight();
        c.css('height', (h - m - t) + 'px').css('overflow', 'scroll').scrollTop(c[0].scrollHeight + 30);
    }
}

/**
 * Set the tab state on the chat pane
 * @param s string
 */
function jrChat_set_tab_state(s)
{
    var h = $('#jrchat-hidden-tabs');
    if (s == 'closed') {
        // Chat is closed - hide all tabs
        h.hide();
        $('#jrchat-open-close').find('span[class$="_chat-close"]').each(function() {
            this.className = this.className.replace(new RegExp('_chat-close', 'g'), '_chat-open');
        });
        jrChat_set_item('state', 'closed');
        jrChat_save_state('closed');
        $('#jrchat-new-message-input').attr('disabled', 'disabled');
    }
    else {
        $('#jrchat-new-message-input').removeAttr('disabled');
        if (h.not(':visible')) {
            // We are not already showing..
            h.show();
            $('#jrchat-open-close').find('span[class$="_chat-open"]').each(function() {
                this.className = this.className.replace(new RegExp('_chat-open', 'g'), '_chat-close');
            });
        }
        jrChat_set_item('state', 'open');
        jrChat_save_state('open');
    }
}

/**
 * Get the current chat tab state
 */
function jrChat_get_tab_state()
{
    return jrChat_get_item('state', 'closed');
}

/**
 * Toggle chat view on/off
 */
function jrChat_toggle(state)
{
    var s = 400;
    var c = $('#jrchat-room');
    var t = $('#jrchat-tabs');
    var w = c.width();
    if (typeof state == "undefined") {
        if (c.css('right').indexOf('-') === 0) {
            state = 'open';
        }
        else {
            state = 'closed';
        }
    }
    if (state == 'open') {
        // We are closed - open
        jrChat_set_tab_state('open');
        $('body').stop().animate({'padding-right': w + 'px'}, s);
        c.stop().animate({'right': '0'}, s);
        t.stop().animate({'right': w + 'px'}, s);
        jrChat_position_fixed_elements(w, s, w);
        if (w >= 640) {
            jrChat_disable_tab('#jrchat-expand-tab');
            jrChat_enable_tab('#jrchat-contract-tab');
        }
        else if (w <= 280) {
            jrChat_enable_tab('#jrchat-expand-tab');
            jrChat_disable_tab('#jrchat-contract-tab');
        }
        else {
            jrChat_enable_tab('#jrchat-expand-tab');
            jrChat_enable_tab('#jrchat-contract-tab');
        }
        $('#jrchat-new-message-input').focus().jrChat_is_typing();
        jrChat_scroll_to_bottom(true);
        jrChat_set_notification_number(0);
        clearTimeout(__jrchat_iv);
        jrChat_get_messages(0, function()
        {
            jrChat_init_chat_controls();
            jrChat_set_local_item('loop_timer', 5000);
            jrChat_set_local_item('loop_number', 0);
            __jrchat_ls = 5000;
            __jrchat_ip = false;
            jrChat_watch_loop();
        });
    }
    else {
        // We are open - close
        jrChat_set_tab_state('closed');

        jrChat_close_room_selector();
        jrChat_close_user_selector();
        jrChat_close_user_settings();
        if (typeof jrSmiley_close_drawer == 'function') {
            jrSmiley_close_drawer();
        }

        var m = $('#jrchat-messages');
        var u = $('#jrchat-room-browser');
        if (u.is(':visible')) {
            u.stop().animate({'width': '0'}, s, function()
            {
                m.removeClass('jrchat-overlay');
                u.hide();
            });
        }

        jrChat_position_fixed_elements(0, s, -w);

        $('body').stop().animate({'padding-right': '0'}, s);
        c.stop().animate({'right': '-' + (w + 1) + 'px'}, s);
        t.stop().animate({'right': '0'}, s);
        if (jrChat_get_active_loop_timer() < __jrchat_cc) {
            jrChat_set_local_item('loop_timer', __jrchat_cc);
            jrChat_set_local_item('loop_number', 5);
            __jrchat_ls = __jrchat_cc;
        }

    }
}

/**
 * Disable a chat tab
 * @param id string DOM ID of tab
 */
function jrChat_disable_tab(id)
{
    $(id + ' .jrchat-tab').css('opacity', 0.2);
    $(id).attr('data-state', 'disabled');
}

/**
 * Enable a chat tab
 * @param id string DOM ID of tab
 */
function jrChat_enable_tab(id)
{
    $(id + ' .jrchat-tab').css('opacity', 1);
    $(id).attr('data-state', 'enabled');
}

/**
 * Check if a tab is disabled
 * @param id string
 * @returns {boolean}
 */
function jrChat_tab_is_disabled(id)
{
    return ($(id).attr('data-state') == 'disabled');
}

/**
 * Set initial tab state on init
 * @param s string
 */
function jrChat_set_initial_tab_state(s)
{
    var w = $('#jrchat-room').width();
    if (w >= 640) {
        jrChat_disable_tab('#jrchat-expand-tab');
        jrChat_enable_tab('#jrchat-contract-tab');
    }
    else if (w <= 280) {
        jrChat_enable_tab('#jrchat-expand-tab');
        jrChat_disable_tab('#jrchat-contract-tab');
    }
    jrChat_set_tab_state(s);
}

/**
 * Set the chat pane width
 */
function jrChat_set_width(a, cb)
{
    var s = 200;
    var c = $('#jrchat-room');
    var w = Number(c.width()) + Number(a);

    var b = $('#jrchat-available-rooms');
    if (b.is(':visible')) {
        b.stop().animate({'width': (w - 60) + 'px'}, s);
    }

    jrChat_position_fixed_elements(w, s, a);

    c.stop().animate({'width': w + 'px'}, s);

    var t = $('#jrchat-tabs');
    t.stop().animate({'right': w + 'px'}, s);

    jrChat_set_local_item('width', w);
    jrChat_save_width(w);
    jrChat_scroll_to_bottom(true);

    // Is our user browser open?
    var u = $('#jrchat-user-control');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat-room-browser');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat-user-settings');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat_smiley_drawer');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    $('body').stop().animate({'padding-right': w + 'px'}, s, function()
    {
        cb(w);
    });
}

/**
 * Store the position of fixed elements
 */
function jrChat_store_fixed_element_positions()
{
    $(':fixed').each(function()
    {
        var id = $(this).attr('id');
        if (id !== null && typeof id !== "undefined" && id.indexOf('jrchat') !== 0) {
            var i = $('#' + id);
            var r = i.data('cssright');
            if (r == 'auto') {
                r = 0;
            }
            if (r === null || typeof r === "undefined") {
                r = i.css('right').replace('px', '');
                i.data('cssright', r);
            }
        }
    });
}

/**
 * Position elements that are FIXED on the page
 * @param w int Width in pixels of chat side bar
 * @param s int animation speed
 * @param a int amount being increment/decremented
 */
function jrChat_position_fixed_elements(w, s, a)
{
    w = Number(w);
    $(':fixed').each(function()
    {
        var l, r, id = $(this).attr('id'), c = $(this).attr('class');
        if (id !== null && typeof id !== "undefined" && id.indexOf('jrchat') !== 0 && (typeof c == "undefined" || c.indexOf('css-fixed') === -1)) {
            var i = $('#' + id);
            if (id == 'simplemodal-container') {
                r = $('body').width() - a;
                l = (r - i.outerWidth()) / 2;
                if (s > 0) {
                    i.stop().animate({'left': l + 'px'}, s);
                }
                else {
                    i.css('left', l + 'px');
                }
            }
            else if (id.indexOf('modal') === -1) {
                r = i.data('cssright');
                if (r == 'auto') {
                    r = 0;
                }
                if (r === null || typeof r === "undefined") {
                    r = i.css('right').replace('px', '');
                    i.data('cssright', r);
                }
                r = Number(r);
                if (w == 0) {
                    if (s > 0) {
                        i.stop().animate({'right': r}, s);
                    }
                    else {
                        i.css('right', r);
                    }
                }
                else {
                    if (s > 0) {
                        if (r > 0) {
                            i.stop().animate({'right': (w + r) + 'px'}, s);
                        }
                        else {
                            i.stop().animate({'right': (w / 2) + 'px'}, s);
                        }
                    }
                    else {
                        if (r > 0) {
                            i.css('right', (w + r) + 'px');
                        }
                        else {
                            i.css('right', (w / 2) + 'px');
                        }
                    }
                }
            }
        }
    });
}

/**
 * Expand the chat pane
 */
function jrChat_expand()
{
    if (jrChat_tab_is_disabled('#jrchat-expand-tab') === false) {
        jrChat_set_width(40, function(w)
        {
            if (w >= 640) {
                jrChat_disable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
            else if (w > 280) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
        });
    }
}

/**
 * Pop out chat into a separate window
 */
function jrChat_popout()
{
    window.open(core_system_url + '/' + jrChat_url + '/mobile');
    jrChat_toggle();
}

/**
 * Contract the chat pane
 */
function jrChat_contract()
{
    if (jrChat_tab_is_disabled('#jrchat-contract-tab') === false) {
        jrChat_set_width(-40, function(w)
        {
            jrChat_scroll_to_bottom(true);
            if (w <= 280) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_disable_tab('#jrchat-contract-tab');
            }
            else if (w < 640) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
        });
    }
}

/**
 * Save chat width to user preferences
 * @param w int Width in pixels
 */
function jrChat_save_width(w)
{
    if (!jrChat_is_mobile_view()) {
        var url = core_system_url + '/' + jrChat_url + '/set_chat_width/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {width: w},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                return true;
            }
        });
    }
}

/**
 * Save chat state to user preferences
 * @param s string
 */
function jrChat_save_state(s)
{
    if (!jrChat_is_mobile_view()) {
        var url = core_system_url + '/' + jrChat_url + '/set_chat_state/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {state: s},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                return true;
            }
        });
    }
}

/**
 * Return TRUE if we are in the mobile view
 * @returns {boolean}
 */
function jrChat_is_mobile_view()
{
    return (window.location.href.indexOf('/' + jrChat_url + '/mobile') > 0);
}

/**
 * Set an item in temp storage
 * @param key string
 * @param val mixed
 */
function jrChat_set_local_item(key, val)
{
    sessionStorage.setItem(key, val);
}

/**
 * Get a temp storage item
 * @param key string
 * @param def string Default to return if not set
 * @returns {boolean}
 */
function jrChat_get_local_item(key, def)
{
    var v = sessionStorage.getItem(key);
    if (typeof v !== "undefined" && v !== null) {
        return v;
    }
    return def;
}

/**
 * Set an item in temp storage
 * @param key string
 * @param val mixed
 */
function jrChat_set_item(key, val)
{
    localStorage.setItem(key, val);
}

/**
 * Get a temp storage item
 * @param key string
 * @param def mixed Default to return if not set
 * @returns {*}
 */
function jrChat_get_item(key, def)
{
    var v = localStorage.getItem(key);
    if (typeof v !== "undefined" && v !== null) {
        return v;
    }
    return def;
}

/**
 * Close the chat room selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_room_selector(cb)
{
    var i = $('#jrchat-available-rooms');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Select a new room from the quick drop down
 */
function jrChat_select_room_id()
{
    var m = $('#jrchat-messages');
    var i = $('#jrchat-available-rooms');
    jrChat_close_user_settings();
    jrChat_close_user_selector();
    if (i.is(':visible')) {
        jrChat_close_room_selector(function()
        {
            m.removeClass('jrchat-overlay');
        });
    }
    else {
        var u = core_system_url + '/' + jrChat_url + '/get_user_rooms/__ajax=1';
        $.ajax({
            type: 'GET',
            url: u,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                var c = $('#jrchat-room');
                var w = c.width();
                i.width(w - 60).css('max-height', m.outerHeight() - 5).html(r).slideDown(100, function()
                {
                    m.addClass('jrchat-overlay');
                });
            }
        });
    }
}

/**
 * Disable enter to post for some msg snippets
 * @param msg string
 * @returns {boolean}
 */
function jrChat_disable_post_on_return(msg)
{
    switch (msg.toLowerCase()) {
        case '[code]':
        case '[quote]':
            return true;
    }
    return false;
}

/**
 * Save a new message to a chat room
 */
function jrChat_save_message(msg)
{
    var frm = $('#jrchat-new-message-input');
    var s = jrChat_get_local_item('search_results', 0);
    if (s == 1) {
        jrChat_close_search_selector();
        jrChat_init(function()
        {
            return jrChat_save_message(frm.val().trim());
        });
    }
    if (__jrchat_lm == false) {
        frm.addClass('form_disabled').attr('disabled', 'disabled');
        __jrchat_lm = true;
        clearTimeout(__jrchat_iv);
        var rid = jrChat_get_current_room_id();
        if (typeof msg == "undefined") {
            msg = frm.val().trim();
        }
        if (msg.indexOf('[code]') == -1) {
            msg = jrChat_strip_tags(msg);
        }
        if (msg.length > 0 && jrChat_disable_post_on_return(msg) === false) {
            var c = $('#jrchat-room').find('#chi');
            c.fadeIn(50, function()
            {
                var now = (new Date).getTime();
                rid = Number(rid);
                jrChat_send_save_request(rid, msg, now, function(s, e)
                {
                    if (s === true) {
                        // Success...
                        var ms = $('#jrchat-mobile-send');
                        if (ms.length > 0) {
                            ms.find('span').removeClass('sprite_icon_hilighted');
                        }
                        frm.removeClass('form_disabled').removeAttr('disabled').val('').focus().jrChat_is_typing();
                        jrChat_get_new_messages();
                        c.fadeOut(500, function()
                        {
                            __jrchat_lm = false;
                            __jrchat_ip = false;
                            jrChat_scroll_to_bottom(1);
                            jrChat_reset_loop_timer();
                            return jrChat_watch_loop(__jrchat_ls);
                        });
                    }
                    else {
                        // Error..
                        setTimeout(function()
                        {
                            jrChat_send_save_request(rid, msg, now, function(s, e)
                            {
                                if (s === true) {
                                    frm.removeClass('form_disabled').removeAttr('disabled').val('').focus().jrChat_is_typing();
                                    jrChat_get_new_messages();
                                    c.fadeOut(500, function()
                                    {
                                        __jrchat_lm = false;
                                        __jrchat_ip = false;
                                        jrChat_scroll_to_bottom(1);
                                        jrChat_reset_loop_timer();
                                        return jrChat_watch_loop(__jrchat_ls);
                                    });
                                }
                                else {
                                    if (e == 'timeout') {
                                        jrCore_alert('the request timed out trying to communicate with the server - please try again');
                                    }
                                    else {
                                        jrCore_alert('there was an error communicating with the server - please try again');
                                    }
                                    frm.removeClass('form_disabled').removeAttr('disabled').focus().jrChat_is_typing();
                                    jrChat_get_new_messages();
                                    c.fadeOut(500, function()
                                    {
                                        __jrchat_lm = false;
                                        __jrchat_ip = false;
                                        jrChat_scroll_to_bottom(1);
                                        jrChat_reset_loop_timer();
                                        return jrChat_watch_loop(__jrchat_ls);
                                    });
                                }
                            });
                        }, 3000);
                    }
                });
            });
        }
        else {
            // zero length message
            frm.removeClass('form_disabled').removeAttr('disabled');
            __jrchat_lm = false;
            jrChat_reset_loop_timer();
            return jrChat_watch_loop(__jrchat_ls);
        }
    }
    return false;
}

/**
 * Send request to save a new message
 * @param rid int Room ID
 * @param msg string Message
 * @param now int epoch time for post
 * @param cb function callback
 */
function jrChat_send_save_request(rid, msg, now, cb)
{
    var url = core_system_url + '/' + jrChat_url + '/new_message/__ajax=1';
    $.ajax({
        type: 'POST',
        url: url,
        data: {room_id: Number(rid), message: msg, unique: now},
        cache: false,
        dataType: 'json',
        timeout: 10000,
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }
            return cb(true, null);
        },
        error: function(x, t, e)
        {
            return cb(false, t);
        }
    });
}


/**
 * Main watch loop for new messages
 */
function jrChat_watch_loop(n)
{
    if (__jrchat_ip === false) {
        __jrchat_ip = true;
        if (typeof n === "undefined" || n === null || n < __jrchat_ls) {
            n = __jrchat_ls;
        }
        jrChat_set_local_item('loop_timer', n);
        __jrchat_iv = setTimeout(function()
        {
            jrChat_get_new_messages(function(s)
            {
                __jrchat_ip = false;
                jrChat_watch_loop(s);
            });
        }, n);
    }
}

/**
 * Scroll to the bottom of the chat window
 * @returns {boolean}
 */
function jrChat_scroll_to_bottom(f)
{
    var c = $('#jrchat-messages');
    if (f == 0) {
        setTimeout(function()
        {
            c.stop().scrollTop(c[0].scrollHeight + 30);
        }, 50);
    }
    else if (f == 1) {
        c.stop().animate({scrollTop: c[0].scrollHeight + 10}, 800);
    }
    else {
        var l = (c.scrollTop() + c.outerHeight());
        if (l >= (c[0].scrollHeight - 120)) {
            c.stop().animate({scrollTop: c[0].scrollHeight + 10}, 800);
        }
    }
    return true;
}

/**
 * Set the last_id flag for getting new messages
 * @param n int Number to set it to
 */
function jrChat_set_last_id(n)
{
    var lid = Number(jrChat_get_last_id());
    if (Number(n) > lid) {
        jrChat_set_local_item('last_id', n);
    }
    return true;
}

/**
 * Get the current last_id value
 * @returns {boolean}
 */
function jrChat_get_last_id()
{
    return jrChat_get_local_item('last_id', 0);
}

/**
 * Get NEW messages in a chat room
 */
function jrChat_get_new_messages(cb)
{
    var rid = jrChat_get_current_room_id();
    var lid = jrChat_get_last_id();
    var url = core_system_url + '/' + jrChat_url + '/new_messages/room_id=' + rid + '/last_id=' + Number(lid) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            // Process messages
            jrChat_set_local_item('user_id', r.uid);
            if (typeof r.lid !== "undefined" && r.lid > 0 && Number(r.lid) > Number(lid)) {
                jrChat_set_last_id(r.lid);
            }

            var n = 0;
            var q = 0;
            var dn = {};
            var chr = $('#jrchat-room');
            var chm = $('#jrchat-messages');
            if (typeof r.new !== "undefined" && typeof r.new == "object" && r.new !== null) {

                var z = Object.keys(r.new).length;
                if (z > 0) {

                    // Update Title to show new message count if on mobile
                    var foc = jrChat_get_local_item('focused', 0);
                    if (foc == 0 && jrChat_is_mobile_view()) {
                        var bnm = z;
                        var ttl = document.title;
                        if (ttl.indexOf('[') === 0) {
                            var brk = ttl.indexOf(']');
                            bnm = Number(ttl.substr(1, (brk - 1)));
                            if (bnm > 0) {
                                bnm = Number(bnm + z);
                            }
                            ttl = ttl.substr(brk + 2);
                        }
                        document.title = '[' + bnm + '] ' + ttl;
                    }

                    var s = '';
                    var c = 0;
                    var lm = '';
                    var ld = '';
                    var gt = '';
                    var lu = '';
                    var li = jrChat_get_local_item('last_notification_id', 0);
                    var nw = ((new Date).getTime() - 86400000);
                    var ps = false;
                    var nn = false;
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {

                            // Are we playing a message sound?
                            if (r.sound == 'on' && r.uid != r.new[m].u && !ps && foc == 0 && jrChat_get_tab_state() != 'closed') {
                                jrChat_new_message_sound();
                                ps = true;
                            }

                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message if we are not already
                                if (chr.find('#m' + r.new[m].i).length === 0) {
                                    r.new[m].c = jrChat_process_message_action(r.new[m].i, r, r.new[m].c);
                                    if (typeof r.new[m].c !== "undefined" && r.new[m].c.length > 0) {
                                        s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                                        dn[r.new[m].u] = 1;
                                        if (r.new[m].i > lid) {
                                            n++;
                                        }
                                    }
                                }

                                // If this user was previously typing, remove their entry
                                var uit = chr.find('#t' + r.new[m].u);
                                if (uit.length) {
                                    uit.remove();
                                }
                            }
                            else {
                                if (r.new[m].i > lid) {
                                    n++;
                                }
                            }
                            if (r.uid != r.new[m].u && r.new[m].i > li) {
                                // Message from another user in the chat room - notification
                                c++;
                                lm = r.new[m].c;
                                ld = r.new[m].u;
                                gt = r.new[m].m;
                                lu = r.new[m].n;
                                li = r.new[m].i;
                            }
                        }
                    }

                    // append all NEW messages
                    chm.append(s);

                    // If any of these are NOT from us, create notification
                    if (jrChat_get_tab_state() == 'open' && !nn) {
                        if (foc == 0 && c > 0 && r.notify == 'on') {
                            // Is API Supported?
                            if ('Notification' in window) {
                                // And have we been granted permissions?
                                if (Notification.permission === "granted") {
                                    if (li != jrChat_get_item('last_notification_id', 0)) {
                                        new Notification("New Chat Message from " + lu, {
                                            icon: jrChat_get_user_image_url(ld, 'icon', gt),
                                            body: jrChat_strip_tags(lm)
                                        });
                                        jrChat_set_item('last_notification_id', li);
                                    }
                                }
                                // Otherwise, we need to ask the user for permission
                                else if (Notification.permission !== 'denied') {
                                    Notification.requestPermission(function(p)
                                    {
                                        // If the user accepts, let's create a notification
                                        if (p === "granted") {
                                            if (li != jrChat_get_item('last_notification_id', 0)) {
                                                new Notification("New Chat Message from " + lu, {
                                                    icon: jrChat_get_user_image_url(ld, 'icon', gt),
                                                    body: jrChat_strip_tags(lm)
                                                });
                                                jrChat_set_item('last_notification_id', li);
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                    else if (n > 0) {
                        q = jrChat_get_notification_number() + n;
                    }
                }
            }

            // Is anyone typing?
            if (jrChat_get_tab_state() == 'open' && typeof r.live !== "undefined") {
                var l = Object.keys(r.live).length;
                if (l > 0) {
                    for (var uid in r.live) {
                        if (r.live.hasOwnProperty(uid) && uid != r.uid && typeof dn[uid] == "undefined") {
                            if (r.live[uid] < 36) {
                                // Show user typing if we did not JUST post a new message from the user
                                if (chr.find('#t' + uid).length === 0) {
                                    var html = jrChat_user_is_typing_html(uid);
                                    chm.append(html);
                                    n++;
                                }
                            }
                            else {
                                var rti = chr.find('#t' + uid);
                                if (rti.length) {
                                    rti.fadeOut(800, function()
                                    {
                                        $(this).remove();
                                    });
                                }
                            }
                        }
                    }
                }
            }

            // If the user is at the bottom, move new message in place
            var lt = __jrchat_ls;
            if (n > 0) {
                // We have new messages
                jrChat_scroll_to_bottom(2);
                var v = jrChat_get_notification_number();
                jrChat_set_new_indicator(v - n);
                jrChat_reset_loop_timer();
            }
            else {
                // No messages - progressive back off
                lt = jrChat_get_active_loop_timer();
            }

            // Do we have any new messages in other chats that are not active?
            if (typeof r.other == "object") {
                var b = 0;
                for (var bn in r.other) {
                    if (r.other.hasOwnProperty(bn) && bn !== rid) {
                        b += r.other[bn];
                    }
                }
                b += q;
                jrChat_set_new_indicator(b);
                if (jrChat_get_tab_state() != 'open' && b > 0) {
                    jrChat_set_notification_number(b);
                }
            }
            else if (jrChat_get_tab_state() != 'open' && q > 0) {
                jrChat_set_notification_number(q);
            }

            if (typeof cb == "function") {
                return cb(lt);
            }
        }
    });
}

/**
 * Update count of users in a chat room
 * @param r object
 * @returns {boolean}
 */
function jrChat_update_room_count(r)
{
    if (typeof r.cnt !== "undefined") {
        $('.jrchat-bubble').text(Number(r.cnt)).fadeIn(100);
    }
    return true;
}

/**
 * Process a message action
 * @param i int Message ID
 * @param r object Response object
 * @param m string Message
 * @returns {*}
 */
function jrChat_process_message_action(i, r, m)
{
    // i.e. ~page:user_id~{
    // i.e. ~page:everyone~{
    // i.e. ~delmsg:5~{
    if (m.indexOf('~') === 0 && m.indexOf('~{') !== -1) {
        var t = m.substr(1).substr(0, m.indexOf('~{') - 1).split(':');
        var f = 'jrChat_action_' + t[0];
        if (typeof window[f] !== "undefined" && typeof window[f] == "function") {
            var n = window[f](i, t, r, m);
            if (typeof n !== "undefined" && n.length > 0) {
                return jrChat_strip_message_action(n);
            }
            return '';
        }
    }
    return m;
}

/**
 * Strip a message action from the action text
 * @param msg string
 * @returns {string}
 */
function jrChat_strip_message_action(msg)
{
    if (msg.indexOf('~{') !== -1) {
        return msg.substr(msg.indexOf('~{') + 2).trim();
    }
    return msg;
}

/**
 * Action: page
 * @param i int message id
 * @param t array action function params
 * @param r object Response object
 * @param m string Message
 * @returns {string}
 */
function jrChat_action_page(i, t, r, m)
{
    // We have to get our username from this message - if it is US,
    // then we need to ring the bell - otherwise just remove the action
    if (typeof t[1] !== "undefined" && (t[1] == 'everyone' || Number(t[1]) == Number(r.uid))) {
        // It is us - notify
        var s = new Audio(core_system_url + '/modules/jrChat/contrib/chime.mp3');
        s.play();
        var n = 0;
        var v = setInterval(function()
        {
            var f = Number(jrChat_get_local_item('focused', 0));
            if (f === 0) {
                s.play();
                n++;
                if (n == 2) {
                    clearInterval(v);
                }
            }
            else {
                clearInterval(v);
            }
        }, 3000);
    }
    return m;
}

/**
 * Action: delmsg
 * @param i int message id
 * @param t array action function params
 * @param r object Response object
 * @param m string Message
 * @returns {string}
 */
function jrChat_action_delmsg(i, t, r, m)
{
    $('#m' + i).remove();
    $('#m' + t[1]).remove();
    return '';
}

/**
 * Play a new message sound
 */
function jrChat_new_message_sound()
{
    new Audio(core_system_url + '/modules/jrChat/contrib/message.mp3').play();
}

/**
 * Reset the loop timer
 */
function jrChat_reset_loop_timer()
{
    jrChat_set_local_item('loop_number', 0);
    jrChat_set_local_item('loop_timer', __jrchat_ls);
}

/**
 * Get the active loop timer
 * @returns {number}
 */
function jrChat_get_active_loop_timer()
{
    var t = __jrchat_ls;
    var n = (Number(jrChat_get_local_item('loop_number', 0)) + 1);
    jrChat_set_local_item('loop_number', n);
    if (n > 5) {
        t = Number(jrChat_get_local_item('loop_timer', __jrchat_ls)) + 1000;
        if (t > __jrchat_cc) {
            if (jrChat_get_tab_state() == 'open') {
                t = __jrchat_cc;
            }
            else if (t > (__jrchat_cc * 2)) {
                t = (__jrchat_cc * 2);
            }
        }
    }
    return t;
}

/**
 * Show notifications for other chat rooms
 * @param rid int Room ID
 * @param r object
 * @param n int Number of new messages in active room
 * @returns {boolean}
 */
function jrChat_show_other_notifications(rid, r, n)
{
    if (typeof r !== "undefined" && typeof r == "object" && r != null) {
        if (Object.keys(r).length > 0) {
            var not = 0;
            rid = Number(rid);
            for (var t in r) {
                if (r.hasOwnProperty(t)) {
                    if (Number(t) != rid && r[t] > 0) {
                        not += r[t];
                    }
                }
            }
            if (not > 0) {
                if (not > 99) {
                    // Show 99 max...
                    not = 99;
                }
                jrChat_set_new_indicator(not);
                jrChat_set_notification_number(not);
                return true;
            }
        }
        else {
            jrChat_set_new_indicator(0);
            jrChat_set_notification_number(0);
        }
    }
    else {
        jrChat_set_new_indicator(0);
        jrChat_set_notification_number(0);
    }
    return true;
}

/**
 * Show new message indicator
 * @param n int Number
 * @returns {boolean}
 */
function jrChat_set_new_indicator(n)
{
    var c = $('#jrchat-select-room').find('.sprite_icon');
    n = Number(n);
    if (n > 0) {
        c.addClass('sprite_icon_hilighted');
    }
    else {
        c.removeClass('sprite_icon_hilighted');
    }
    return true;
}

/**
 * Get the current notification number
 * @returns {mixed}
 */
function jrChat_get_notification_number()
{
    return jrChat_get_item('notification_number', 0);
}

/**
 * Set new number notification in bubble
 * @param n {number}
 */
function jrChat_set_notification_number(n)
{
    var c = $('#jrchat-open-close').children('span');
    n = Number(n);
    if (jrChat_get_tab_state() == 'closed') {
        // Highlight that there are new chats
        if (n > 0) {
            c.addClass('sprite_icon_hilighted');
            jrChat_set_item('notification_number', n);
        }
    }
    else {
        c.removeClass('sprite_icon_hilighted');
        jrChat_set_item('notification_number', 0);
    }
}

/**
 * Return true if a user is an admin user
 * @returns {boolean}
 */
function jrChat_is_admin()
{
    var a = jrChat_get_local_item('is_admin', 0);
    return (Number(a) === 1);

}

/**
 * Get the current room id (as displayed on screen)
 * @returns {*}
 */
function jrChat_get_current_room_id()
{
    var i = jrChat_get_local_item('room_id', 0);
    if (i === 0) {
        i = $('#display-room-id').val();
    }
    return i;
}

/**
 * Set the current room id (as displayed on screen)
 * @param id int Room ID
 * @returns {boolean}
 */
function jrChat_set_current_room_id(id)
{
    jrChat_set_local_item('room_id', id); 
    return true;
}

/**
 * Load Messages for a Chat Room
 */
function jrChat_get_messages(bid, cb)
{
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/messages/room_id=' + rid + '/before_id=' + Number(bid) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            jrChat_set_local_item('user_id', r.uid);
            jrChat_set_local_item('is_admin', r.adm);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            rid = Number(r.rid);
            jrChat_set_current_room_id(rid);
            if (typeof r.new !== "undefined" && r.new !== false) {

                var lid = jrChat_get_last_id();
                if (typeof r.lid !== "undefined" && r.lid > 0 && r.lid > lid) {
                    jrChat_set_last_id(r.lid);
                }
                if (typeof r.bid !== "undefined" && r.bid > 0) {
                    jrChat_set_local_item('before_id', r.bid);
                }

                var htm = jrChat_get_beginning_of_chat();
                var chm = $('#jrchat-messages');
                var nxp = $('#jrchat-load-next-page');
                var n = 0;
                var z = Object.keys(r.new).length;
                if (z > 0) {

                    var s = '';
                    var nw = ((new Date).getTime() - 86400000);
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {
                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message
                                if ($('#jrchat-room #m' + r.new[m].i).length === 0) {
                                    r.new[m].c = jrChat_strip_message_action(r.new[m].c);
                                    s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                                    if (r.new[m].i > lid) {
                                        n++;
                                    }
                                }
                            }
                            else if (r.new[m].i > lid) {
                                // We have a new message that is NOT in our active room
                                n++;
                            }
                        }
                    }

                    if (bid == 0) {
                        // all NEW messages
                        chm.append(s);
                        jrChat_scroll_to_bottom(0);
                    }
                    else {
                        // Adding NEW messages above existing messages
                        // Save old height
                        var old_height = chm[0].scrollHeight;
                        var old_scroll = chm.scrollTop();

                        // Add new messages
                        nxp.after(s);

                        // Reposition
                        var new_height = chm[0].scrollHeight;
                        chm.scrollTop((old_scroll + new_height) - old_height);
                    }

                    if (z < 50) {
                        // We have less than 50 total messages in this room
                        chm.prepend(htm);
                        nxp.off('inview').remove();
                    }
                    else {
                        // There are more messages to show..
                        setTimeout(function()
                        {
                            jrChat_init_pager();
                        }, 2000);
                    }

                    if (bid > 0 && n > 0 && jrChat_get_tab_state() == 'closed') {
                        var c = jrChat_get_notification_number() + n;
                        jrChat_set_notification_number(c);
                    }
                }
                else {
                    // We have run out of previous pages
                    if (jrChat_get_last_id() > 0) {
                        chm.prepend(htm);
                    }
                    else {
                        // No messages in this chat room yet
                        chm.html(htm);
                    }
                    nxp.off('inview').remove();
                }

                if (typeof cb == "function") {
                    return cb(n);
                }
            }
        }
    });
}

/**
 * Show a message in the chat that a user is typing
 * @param uid int User ID
 * @returns {string}
 */
function jrChat_user_is_typing_html(uid)
{
    var u = core_system_url + '/' + jrUser_url + '/image/user_image';
    var c = $('#jrchat-icon-color').text();
    var v = $('#jrchat-version').text();
    return '<div id="t' + uid + '" class="jrchat-msg jrchat-msg-to"><div class="jrchat-msg-img"><img src="' + u + '/' + uid + '/small/crop=portrait" height="24" width="24"></div><div class="jrchat-msg-msg"><img src=\"' + core_system_url + '/' + jrImage_url + '/img/module/jrChat/typing-' + c + '.gif?_v=' + v + '" class="jrchat-typing-img"></div></div>';
}

/**
 * Get Message HTML
 * @param uid int User ID
 * @param msg object Msg
 * @param old int Epoch Time
 * @returns {string}
 */
function jrChat_get_message_html(uid, msg, old)
{
    var d = (msg.t * 1000);
    var t = new Date(d);
    var h = t.getHours();
    var p = 'AM';
    if (h == 0) {
        h = 12;
    }
    else if (h >= 12) {
        if (h > 12) {
            h = (h - 12);
        }
        p = 'PM';
    }
    var m = t.getMinutes();
    if (m < 10) {
        m = '0' + m;
    }
    var l;
    if (d < old) {
        l = t.toDateString() + ', ' + h + ':' + m + ' ' + p;
    }
    else {
        l = h + ':' + m + ' ' + p;
    }
    if (uid == msg.u) {
        return '<div id="m' + msg.i + '" class="jrchat-msg jrchat-msg-from">' + msg.c + '<div class="jrchat-msg-byline">' + msg.n + ', ' + l + '</div></div>';
    }
    return '<div id="m' + msg.i + '" class="jrchat-msg jrchat-msg-to"><div class="jrchat-msg-img"><a onclick="jrChat_start_chat_with_user(' + msg.u + ')"><img src="' + jrChat_get_user_image_url(msg.u, 'small', msg.m) + '" height="24" width="24" title="' + msg.n + '"></a></div><div class="jrchat-msg-msg">' + msg.c + '<div class="jrchat-msg-byline"><a href="' + core_system_url + '/' + jrChat_url + '/profile/' + Number(msg.u) + '" target="_blank">' + msg.n + '</a>, ' + l + '</div></div></div>';
}

/**
 * Get a User image URL
 * @param u int User ID
 * @param s string Size
 * @param m string Gravatar URL
 * @returns {string}
 */
function jrChat_get_user_image_url(u, s, m)
{
    if (m == 1) {
        m = core_system_url + '/' + jrUser_url + '/image/user_image/' + u + '/' + s + '/crop=portrait';
    }
    return m;
}

/**
 * Close the user selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_user_selector(cb)
{
    var i = $('#jrchat-user-control');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Display chat room user control
 * @param r int se to "1" to reload
 */
function jrChat_get_room_users(r)
{
    var s = 200;
    jrChat_close_room_selector();
    jrChat_close_user_settings();
    jrChat_close_search_selector();
    var i = jrChat_get_current_room_id();
    if (i > 0) {
        var c = $('#jrchat-user-control');
        var m = $('#jrchat-messages');
        var u = core_system_url + '/' + jrChat_url + '/users/room_id=' + Number(i) + '/__ajax=1';
        if (c.is(':visible')) {
            if (typeof r !== "undefined" && r === 1) {
                $.ajax({
                    type: 'GET',
                    url: u,
                    cache: false,
                    dataType: 'html',
                    success: function(r)
                    {
                        if (r !== null && typeof r !== "undefined") {
                            jrChat_check_login(r);
                        }
                        if (typeof r !== "undefined" && r.indexOf('ERROR:') === -1) {
                            c.html(r);
                            jrChat_init_live_search('chat_room_user_id');
                        }
                    }
                });
            }
            else {
                c.slideUp(s, function()
                {
                    m.removeClass('jrchat-overlay');
                });
            }
        }
        else {

            var b = $('#jrchat-room-browser');
            if (b.is(':visible')) {
                b.slideUp(s);
            }

            c.width(m.width() - 12).height(m.outerHeight() - 18);
            $.ajax({
                type: 'GET',
                url: u,
                cache: false,
                dataType: 'html',
                success: function(r)
                {
                    if (r !== null && typeof r !== "undefined") {
                        jrChat_check_login(r);
                    }
                    if (typeof r !== "undefined" && r.indexOf('ERROR:') === -1) {
                        m.addClass('jrchat-overlay');
                        c.html(r).slideDown(s, function()
                        {
                            jrChat_init_live_search('chat_room_user_id');
                        });
                    }
                }
            });
        }
    }
}

/**
 * Create a new chat room
 * @returns {boolean}
 */
function jrChat_create_room()
{
    var ttl = $('#jrchat-new-chat-title').val();
    if (ttl.length > 0) {
        var typ = $('#jrchat-new-chat-type').is(':checked') ? 'on' : 'off';
        var url = core_system_url + '/' + jrChat_url + '/create_room/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {title: ttl, type: typ},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                if (typeof r.rid !== "undefined") {
                    jrChat_load_room_id(r.rid);
                }
                else {
                    jrCore_alert(r.error);
                }
            }
        });
    }
    else {
        jrCore_alert('please enter a title for the chat room');
    }
    return true;
}

/**
 * delete a chat room
 * @returns {boolean}
 */
function jrChat_delete_room_id(i)
{
    var url = core_system_url + '/' + jrChat_url + '/delete_room/id=' + Number(i) + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                jrCore_alert(r.error);
            }
            else {
                $('.room-row' + i).remove();
                if (jrChat_get_current_room_id() === i) {
                    // deleting our active room - switch rooms
                    if (typeof r.rid !== "undefined" && r.rid > 0) {
                        jrChat_load_room_id(r.rid);
                    }
                    else {
                        var f = $('.room-row').first().attr('class');
                        var n = f.replace(/[^0-9]/g, '');
                        jrChat_load_room_id(n);
                    }
                }
            }
        }
    });
    return true;
}

/**
 * Load a new chat room by ID
 * @param id int Room ID
 * @param n int New Count
 */
function jrChat_load_room_id(id, n)
{
    var i = $('#jrchat-available-rooms');
    if (i.is(':visible')) {
        i.slideUp(100);
    }
    // Take care of small notification number on new room load
    if (typeof n !== "undefined" && n !== null && n > 0) {
        if (n == 0) {
            jrChat_set_new_indicator(0);
        }
        else {
            var v = jrChat_get_notification_number();
            if (n > v) {
                jrChat_set_new_indicator(0);
            }
            else {
                jrChat_set_new_indicator(v - n);
            }
        }
    }
    $('#jrchat-empty-chat').remove();
    var url = core_system_url + '/' + jrChat_url + '/get_room_info/id=' + Number(id) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                jrCore_alert(r.error);
            }
            else {
                // Reset everything and load new room
                $('#jrchat-messages').empty().removeClass('jrchat-overlay');
                jrChat_set_current_room_id(id);
                jrChat_set_active_room_title(r.room_title);
                if (r.room_public == 0 && r.room_user_count == 1) {
                    $('.jrchat-bubble').text('+').fadeIn(100);
                }
                else {
                    $('.jrchat-bubble').text(r.room_user_count).fadeIn(100);
                }
                jrChat_set_local_item('messages', '');
                jrChat_init_pager();

                var b = $('#jrchat-room-browser');
                if (b.is(':visible')) {
                    b.slideUp(200);
                    jrChat_init();
                }
                else {
                    jrChat_init();
                }
            }
        }
    });
    return true;
}

/**
 * Close the user settings window
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_user_settings(cb)
{
    var i = $('#jrchat-user-settings');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * User setting
 */
function jrChat_user_settings()
{
    var s = 200;
    var c = $('#jrchat-messages');
    var b = $('#jrchat-user-settings');
    jrChat_close_room_selector();
    jrChat_close_user_selector();
    jrChat_close_search_selector();
    if (b.is(':visible')) {
        jrChat_close_user_settings(function()
        {
            c.removeClass('jrchat-overlay');
        })
    }
    else {
        
        var r = $('#jrchat-room-browser');
        if (r.is(':visible')) {
            r.slideUp(s);
        }
        
        c.addClass('jrchat-overlay');
        b.width(c.width() - 12).height(c.outerHeight() - 18);
        var url = core_system_url + '/' + jrChat_url + '/user_config/__ajax=1';
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                if (typeof r.error !== "undefined") {
                    jrCore_alert(r.error);
                }
                else {
                    b.html(r).slideDown(s);
                }
            }
        });
    }
}

/**
 * Save User Settings
 */
function jrChat_save_user_settings()
{
    var fsi = $('#jrChat_user_config').find('#form_submit_indicator');
    var nfy = $('#notifications').is(':checked') ? 'on' : 'off';
    var sos = $('input[name=online_status]:checked').val();
    var snd = $('input[name=message_sound]:checked').val();
    fsi.show(250, function()
    {
        setTimeout(function()
        {
            var url = core_system_url + '/' + jrChat_url + '/user_config_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                url: url,
                data: {notifications: nfy, online_status: sos, message_sound: snd},
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    fsi.hide(300, function()
                    {
                        if (typeof r.error !== "undefined") {
                            jrChat_check_login(r.error);
                            jrCore_alert(r.error);
                        }
                        else {
                            jrChat_set_notification_number(0);
                            jrChat_close_user_settings(function()
                            {
                                $('#jrchat-messages').removeClass('jrchat-overlay');
                            });
                        }
                    });
                }
            });
        }, 800);
    });
}

/**
 * Bring up search
 */
function jrChat_search_room()
{
    var s = 200;
    var b = $('#jrchat-room-search');

    jrChat_close_room_selector();
    jrChat_close_user_selector();
    jrChat_close_user_settings();

    if (b.is(':visible')) {
        b.slideUp(s, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-search-input').val('');
        });
    }
    else {
        b.slideDown(s, function()
        {
            $('#jrchat-search-input').focus();
            jrChat_set_chat_height();
        });
    }
}

/**
 * Reset search results
 */
function jrChat_search_reset()
{
    var c = $('#jrchat-search-input');
    c.val('');
    var s = jrChat_get_local_item('search_results', 0);
    if (s == 1) {
        // We were showing results
        jrChat_init(function()
        {
            c.focus();
        });
    }
}

/**
 * Close the search room selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_search_selector(cb)
{
    jrChat_set_local_item('search_results', 0);
    var i = $('#jrchat-room-search');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-search-input').val('');
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Search messages
 */
function jrChat_search_messages(ss)
{
    jrChat_set_local_item('search_results', 1);
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/messages/room_id=' + rid + '/ss=' + jrE(ss) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            $('#jrchat-search-reset').show();

            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            jrChat_set_local_item('user_id', r.uid);
            jrChat_set_local_item('is_admin', r.adm);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            rid = Number(r.rid);
            jrChat_set_current_room_id(rid);
            if (typeof r.new !== "undefined" && r.new !== false) {

                var htm = jrChat_get_beginning_of_chat();
                var chm = $('#jrchat-messages');
                var nxp = $('#jrchat-load-next-page');
                var z = Object.keys(r.new).length;
                if (z > 0) {

                    var s = '';
                    var nw = ((new Date).getTime() - 86400000);
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {
                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message
                                r.new[m].c = jrChat_strip_message_action(r.new[m].c);
                                s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                            }
                        }
                    }

                    // all NEW messages
                    chm.html(s).removeClass('jrchat-overlay');
                    jrChat_scroll_to_bottom(0);
                    if (z < 50) {
                        // We have less than 50 total messages in this room
                        chm.prepend(htm);
                        nxp.off('inview').remove();
                    }
                    else {
                        // There are more messages to show..
                        setTimeout(function()
                        {
                            jrChat_init_pager();
                        }, 2000);
                    }
                }
                else {
                    // We have run out of previous pages
                    if (jrChat_get_last_id() > 0) {
                        chm.prepend(htm);
                    }
                    else {
                        // No messages in this chat room yet
                        chm.html(htm);
                    }
                    nxp.off('inview').remove();
                }
            }
            else {
                return jrChat_show_no_search_results();
            }
        }
    });
}

function jrChat_show_no_search_results()
{
    jrChat_close_search_selector();
    var c = $('#jrchat-messages');
    c.empty().html('<div id="jrchat-empty-chat">No Search Results Found</div>');
}

/**
 * Bring up the chat room browser
 */
function jrChat_room_browser()
{
    var s = 200;
    var c = $('#jrchat-messages');
    var b = $('#jrchat-room-browser');
    jrChat_close_room_selector();
    jrChat_close_user_settings();
    jrChat_close_search_selector();
    if (b.is(':visible')) {
        b.slideUp(s, function()
        {
            c.removeClass('jrchat-overlay');
        });
    }
    else {

        var u = $('#jrchat-user-control');
        if (u.is(':visible')) {
            u.slideUp(s);
        }

        c.addClass('jrchat-overlay');
        b.width(c.width() - 12).height(c.outerHeight() - 18);

        var url = core_system_url + '/' + jrChat_url + '/get_chats/last_id=' + jrChat_get_last_id() + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                if (typeof r.error !== "undefined") {
                    jrCore_alert(r.error);
                }
                else {
                    b.html(r).slideDown(s, function()
                    {
                        jrChat_init_live_search('chat_user_id');
                    });
                }
            }
        });
    }
}

/**
 * Add a new user to an existing chat room
 */
function jrChat_add_user_to_chat()
{
    var uid = $('#chat_room_user_id_livesearch_value').val();
    if (typeof uid !== "undefined" && uid > 0) {
        var rid = jrChat_get_current_room_id();
        var url = core_system_url + '/' + jrChat_url + '/add_user_to_chat/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {room_id: rid, user_id: uid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                    jrCore_alert(r.error);
                }
                else {

                    clearTimeout(__jrchat_iv);
                    setTimeout(function()
                    {
                        jrChat_get_room_users(1);

                        var b = $('.jrchat-bubble');
                        var v = b.text();
                        if (v == '+') {
                            v = 1;
                        }
                        b.show().text(Number(v) + 1);

                        jrChat_get_new_messages(function()
                        {
                            jrChat_scroll_to_bottom(1);
                            __jrchat_ip = false;
                            jrChat_reset_loop_timer();
                            return jrChat_watch_loop(__jrchat_ls);
                        });
                    }, 300);
                }
            }
        });
    }
}

/**
 * Remove an existing user from chat
 */
function jrChat_remove_user_from_chat(uid, b)
{
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/remove_user_from_chat/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {room_id: rid, user_id: uid, block: b},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                jrCore_alert(r.error);
            }
            else {

                if (r.self == 1) {
                    // user has left the chat
                    jrChat_show_no_chat_selected();
                    var c = $('#jrchat-user-control');
                    c.slideUp(250, function()
                    {
                        m.removeClass('jrchat-overlay');
                    });
                }
                else {

                    clearTimeout(__jrchat_iv);
                    jrChat_get_room_users(1);

                    var b = $('.jrchat-bubble');
                    var v = b.text();
                    if (v == '+') {
                        v = 1;
                    }
                    b.show().text(Number(v) - 1);

                    jrChat_get_new_messages(function()
                    {
                        jrChat_scroll_to_bottom(1);
                        __jrchat_ip = false;
                        jrChat_reset_loop_timer();
                        return jrChat_watch_loop(__jrchat_ls);
                    });
                }
            }
        }
    });
}

/**
 * Start a private chat with a user
 */
function jrChat_start_chat_with_user(uid)
{
    if (typeof uid === "undefined" || typeof uid !== "number") {
        uid = $('#chat_user_id_livesearch_value').val();
    }
    if (typeof uid !== "undefined" && uid > 0) {
        var url = core_system_url + '/' + jrChat_url + '/create_private_room/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {user_id: uid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                    jrCore_alert(r.error);
                }
                else {
                    jrChat_load_room_id(r.rid);
                }
            }
        });
    }
}

/**
 * Show a "no chat selected" message
 */
function jrChat_show_no_chat_selected()
{
    $('.jrchat-bubble').hide();
    jrChat_set_active_room_title('no active chat');
    var c = $('#jrchat-messages');
    c.empty().html('<div id="jrchat-empty-chat">No Chat has been Selected</div>');
}

/**
 * author Christopher Blum
 * - based on the idea of Remy Sharp, http://remysharp.com/2009/01/26/element-in-view-event-plugin/
 * - forked from http://github.com/zuk/jquery.inview/
 */
(function(factory)
{
    if (typeof define == 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    }
    else if (typeof exports === 'object') {
        // Node, CommonJS
        module.exports = factory(require('jquery'));
    }
    else {
        // Browser globals
        factory(jQuery);
    }
}(function($)
{
    var inviewObjects   = [], viewportSize, viewportOffset,
        d               = document,
        w               = window,
        documentElement = d.documentElement, timer;

    $.event.special.inview = {
        add: function(data)
        {
            inviewObjects.push({data: data, $element: $(this), element: this});
            // Use setInterval in order to also make sure this captures elements within
            // "overflow:scroll" elements or elements that appeared in the dom tree due to
            // dom manipulation and reflow
            // old: $(window).scroll(checkInView);
            //
            // By the way, iOS (iPad, iPhone, ...) seems to not execute, or at least delays
            // intervals while the user scrolls. Therefore the inview event might fire a bit late there
            //
            // Don't waste cycles with an interval until we get at least one element that
            // has bound to the inview event.
            if (!timer && inviewObjects.length) {
                timer = setInterval(checkInView, 250);
            }
        },

        remove: function(data)
        {
            for (var i = 0; i < inviewObjects.length; i++) {
                var inviewObject = inviewObjects[i];
                if (inviewObject.element === this && inviewObject.data.guid === data.guid) {
                    inviewObjects.splice(i, 1);
                    break;
                }
            }

            // Clear interval when we no longer have any elements listening
            if (!inviewObjects.length) {
                clearInterval(timer);
                timer = null;
            }
        }
    };

    function getViewportSize()
    {
        var mode, domObject, size = {height: w.innerHeight, width: w.innerWidth};

        // if this is correct then return it. iPad has compat Mode, so will
        // go into check clientHeight/clientWidth (which has the wrong value).
        if (!size.height) {
            mode = d.compatMode;
            if (mode || !$.support.boxModel) { // IE, Gecko
                domObject = mode === 'CSS1Compat' ?
                    documentElement : // Standards
                    d.body; // Quirks
                size = {
                    height: domObject.clientHeight,
                    width: domObject.clientWidth
                };
            }
        }

        return size;
    }

    function getViewportOffset()
    {
        return {
            top: w.pageYOffset || documentElement.scrollTop || d.body.scrollTop,
            left: w.pageXOffset || documentElement.scrollLeft || d.body.scrollLeft
        };
    }

    function checkInView()
    {
        if (!inviewObjects.length) {
            return;
        }

        var i = 0, $elements = $.map(inviewObjects, function(inviewObject)
        {
            var selector = inviewObject.data.selector,
                $element = inviewObject.$element;
            return selector ? $element.find(selector) : $element;
        });

        viewportSize = viewportSize || getViewportSize();
        viewportOffset = viewportOffset || getViewportOffset();

        for (; i < inviewObjects.length; i++) {
            // Ignore elements that are not in the DOM tree
            if (!$.contains(documentElement, $elements[i][0])) {
                continue;
            }

            var $element      = $($elements[i]),
                elementSize   = {height: $element[0].offsetHeight, width: $element[0].offsetWidth},
                elementOffset = $element.offset(),
                inView        = $element.data('inview');

            // Don't ask me why because I haven't figured out yet:
            // viewportOffset and viewportSize are sometimes suddenly null in Firefox 5.
            // Even though it sounds weird:
            // It seems that the execution of this function is interferred by the onresize/onscroll event
            // where viewportOffset and viewportSize are unset
            if (!viewportOffset || !viewportSize) {
                return;
            }

            if (elementOffset.top + elementSize.height > viewportOffset.top &&
                elementOffset.top < viewportOffset.top + viewportSize.height &&
                elementOffset.left + elementSize.width > viewportOffset.left &&
                elementOffset.left < viewportOffset.left + viewportSize.width) {
                if (!inView) {
                    $element.data('inview', true).trigger('inview', [true]);
                }
            }
            else if (inView) {
                $element.data('inview', false).trigger('inview', [false]);
            }
        }
    }

    $(w).on("scroll resize scrollstop", function()
    {
        viewportSize = viewportOffset = null;
    });

    // IE < 9 scrolls to focused elements without firing the "scroll" event
    if (!documentElement.addEventListener && documentElement.attachEvent) {
        documentElement.attachEvent("onfocusin", function()
        {
            viewportOffset = null;
        });
    }
}));

/**
 * Create a small "user is typing" message
 */
(function($)
{
    $.fn.jrChat_is_typing = function()
    {
        function send_message_in_progress(obj)
        {
            var c = $(obj).val().length;
            if (c > 1) {
                var now = (new Date).getTime();
                var old = __jrchat_ls;
                if (now - Number(jrChat_get_local_item('last_send', old)) >= old) {
                    var url = core_system_url + '/' + jrChat_url + '/am_typing/__ajax=1';
                    var rid = jrChat_get_current_room_id();
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {room_id: rid},
                        cache: false,
                        dataType: 'json'
                    });
                    jrChat_set_local_item('last_send', now);
                }
            }
        };
        this.each(function()
        {
            $(this).keyup(function(e)
            {
                if (e.keyCode != 13) {
                    send_message_in_progress(this)
                }
            });
            $(this).change(function(e)
            {
                if (e.keyCode != 13) {
                    send_message_in_progress(this)
                }
            });
        });

    };

})(jQuery);

$.extend($.expr[':'], {
    absolute: function(e)
    {
        return $(e).css('position') === 'absolute';
    },
    fixed: function(e)
    {
        return $(e).css('position') === 'fixed';
    }
});

/**
 * Strip HTML tags from a message
 * @param input
 * @param allowed
 * @returns {*}
 */
function jrChat_strip_tags(input, allowed)
{
    //  discuss at: http://phpjs.org/functions/strip_tags/
    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join('');
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        comm = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(comm, '')
        .replace(tags, function($0, $1)
        {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
        });
}

/**
 * Check for login redirect in error message
 * @param e string result/message
 * @returns {boolean}
 */
function jrChat_check_login(e)
{
    if (e.indexOf('requires you to be logged in') !== -1) {
        window.location = core_system_url + '/' + jrUser_url + '/login';
    }
    return true;
}

/**
 * Follow discussion toggle
 * @param id discussion id
 */
function jrGroupDiscuss_follow_toggle(id)
{
    var url = core_system_url + '/' + jrGroupDiscuss_url + '/toggle_watch/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {
        id: id
    },
    function (rs) {
        if (rs.success) {
            var div = '#discussion_follow_button_' + id + ' span';
            if (rs.following == 'on') {
                $(div).addClass('sprite_icon_hilighted');
            } else {
                $(div).removeClass('sprite_icon_hilighted');
            }
            var bid = $('#discussion_follow_button_' + id);
            var bpr = $(window).width() - bid.offset().left;
            var bpt = bid.offset().top;
            var pid = $('#discussion_follow_drop_' + id);
            pid.appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
            if (pid.is(":visible")) {
                pid.html(rs.tag).fadeOut(2000);
            } else {
                pid.fadeIn().html(rs.tag).fadeOut(2000);
            }
        }
    }, "json");
}

/**
 * Follow discussion toggle
 * @param group_id discussion id
 */
function jrGroupDiscuss_follow_group_toggle(group_id)
{
    var url = core_system_url + '/' + jrGroupDiscuss_url + '/toggle_group_watch/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {
        group_id: group_id
    },
    function (rs) {
        if (rs.success) {
            var div = '#discussion_follow_group_button_' + group_id + ' span';
            if (rs.following == 'on') {
                $(div).addClass('sprite_icon_hilighted');
            } else {
                $(div).removeClass('sprite_icon_hilighted');
            }
            var bid = $('#discussion_follow_group_button_' + group_id);
            var bpr = $(window).width() - bid.offset().left;
            var bpt = bid.offset().top;
            var pid = $('#discussion_follow_group_drop_' + group_id);
            pid.appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
            if (pid.is(":visible")) {
                pid.html(rs.tag).fadeOut(2000);
            } else {
                pid.fadeIn().html(rs.tag).fadeOut(2000);
            }
        }
    }, "json");
}


// Jamroom Gallery Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Load prev/next page in gallery slider
 * @param pid int profile id
 * @param gallery string gallery name
 * @param page int page number
 * @param pagebreak int number of images per page
 */
function jrGallery_slider(pid, gallery, page, pagebreak)
{
    var url = core_system_url + '/' + jrGallery_url + '/slider_images/__ajax=1';
    $.post(url,
        {
            pid: Number(pid),
            gallery: gallery,
            page: Number(page),
            pagebreak: Number(pagebreak)
        },
        function(data)
        {
            if (data.error) {
                $('#gallery_slider').text(data.error);
            }
            else {
                $('#gallery_slider').html(data);
            }
        }
    );
}

/**
 * Set number of images wide
 * @param ct int Number of images wide
 */
function jrGallery_xup(ct)
{
    var w = Math.floor(99.9 / Number(ct));
    jrSetCookie('jr_gallery_xup_width', w, 1);
    $('.sortable > li').css('width', w + '%');
    return false;
}

/**
 * Insert an image into a page via embed
 * @param url string
 * @param title string
 */
function jrGallery_insert_image(url, title)
{
    var ed = top.tinymce.activeEditor, dom = ed.dom;
    var s = $('#imgsizg').val();
    var p = $('#imgposg').val();
    var m = $('#imgmarg').val();
    if (s === '') {
        s = 'icon';
    }
    switch (p) {
        case 'stretch':
            p = 'width:100%;';
            break;
        case 'left':
            p = 'float:left;';
            break;
        case 'right':
            p = 'float:right;';
            break;
        case '':
            p = '';
            break;
    }
    switch (m) {
        case 0:
            m = '';
            break;
        default:
            m = 'margin:' + m + 'px;';
            break;
    }
    ed.insertContent(dom.createHTML('img', {
        src: core_system_url + url + '/' + s,
        alt: title,
        title: title,
        border: 0,
        style: p + m
    }));
    var h = $(ed.getContainer()).height();
    if (h < s) {
        ed.theme.resizeTo('100%', s);
    }
    ed.windowManager.close();
}

/**
 * Toggle between "original" and "cropped" aspect ratio
 * @param a string
 */
function jrGallery_toggle_aspect(a)
{
    if (a === "" || a === 'cropped') {
        jrSetCookie('aspect', JSON.stringify('original'));
    }
    else {
        jrSetCookie('aspect', JSON.stringify('cropped'));
    }
    var p = $('#galpnum').text();
    if (typeof p === "undefined") {
        p = 1;
    }
    jrEmbed_load_module('jrGallery', p, '');
}


/**
 * ajax delete an image from the update screen of the gallery
 * @param item_id string
 */
function jrGallery_update_delete(item_id)
{

    var url = core_system_url + '/' + jrGallery_url + '/delete_image_ajax/id=' + item_id + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $('#del_spin_' + item_id).show(300, function()
    {
        $('#del_btn_' + item_id).fadeOut('fast');
        $.ajax({
            type: 'POST',
            url: url,
            cache: false,
            dataType: 'json',
            success: function(data)
            {
                if (data.OK) {
                    // remove the image
                    $('#gi_' + item_id).fadeOut('slow').remove();
                }
                else {
                    jrCore_alert(data.error);
                }
                return true;
            }
        });
    });
}


/**
 * Save a title to a single image from the gallery update form
 */
function jrGallery_save_title(id)
{
    var i = $('#gallery_update_id').val();
    var l = $('#gallery_update_title').val();
    var s = $('#gallery_title_save');
    s.attr('disabled', 'disabled').addClass('form_button_disabled');
    setTimeout(function()
    {
        var u = core_system_url + '/' + jrGallery_url + '/image_title_save/__ajax=1';
        jrCore_set_csrf_cookie(u);
        $.ajax({
            url: u,
            type: 'POST',
            cache: false,
            data: {'id': Number(i), 'gallery_image_title': l},
            dataType: 'json',
            success: function(data)
            {
                if (data.OK == 1) {
                    $.modal.close();
                    $('#gallery_image_title_' + i).text(data.gallery_image_title);
                }
                else {
                    $.modal.close();
                    jrCore_alert(data.error);
                }
            },
            error: function()
            {
                jrCore_alert('error communicating with server - please try again');
            }
        });
    }, 500);
}

/*
	jQuery live search plugin
	Version 1.0 (05/20/2009)

	Author: Jeremy Herrman (jherrman@sei.cmu.edu)

	About:
		This plugin enables ordinary text inputs to have live seach capabilities.
		As a user types, the plugin calls a specified function.

		Features:
			Query Delay:
				Only executes the callback after a delay (default is 250ms) so that
				fast typers won't drown your website with too many calls.

			Minimum Search Length:
				Specify a minimum search length (default is 3 characters) so that
				your system doesn't get incomplete and broad searches.

			Initial text:
				Display initial text (default is "Search") that is automatically
				cleared when a user focuses on the text input.
				Style this by using the class "inactive_search".

			Multiple Instances:
				This plugin written so that you can have multiple live search text
				inputs on the same page.

	Usage:

		$('#textfield').livesearch({
			searchCallback: searchFunction,
			queryDelay: 250,
			innerText: "Search",
			minimumSearchLength: 3
		});

		function searchFunction(searchTerm) {
			//do the search, update the page, etc.
		}

	Dual licensed under the MIT and GPL licenses:
	http://www.opensource.org/licenses/mit-license.php
	http://www.gnu.org/licenses/gpl.html

*/



(function($) {

	var LiveSearch = function(element, opts)
	{
		element = $(element);
		var obj = this;
		var settings = $.extend({}, $.fn.livesearch.defaults, opts);

		var timer = undefined;
		var prevSearchTerm = element.val();

		element.empty();

		element.bind("keyup", function() {
			// have a timer that gets canceled if a key is pressed before it executes
			if(timer != undefined) {
				clearTimeout(timer);
			}
			timer = setTimeout(DoSearch, settings.queryDelay);
		});

		this.DoSearch = DoSearch;
		function DoSearch() {
			var searchTerm = element.val();
			if(searchTerm != prevSearchTerm) {
				prevSearchTerm = searchTerm;
				if(searchTerm.length >= settings.minimumSearchLength) {
					DisplayResults(searchTerm);
				}
				else if(searchTerm.length == 0) {
					DisplayResults("");
				}
			}
		};

		function DisplayResults(searchTerm) {
			timer = undefined;
			settings.searchCallback(searchTerm);
		};

		if (element.val() == "" || element.val() == settings.innerText) {
			disableSearch();
		}
		else {
			enableSearch();
		}

		element.focus(function() {
			if (element.hasClass("inactive_search")) { enableSearch(); }
		});

		element.blur(function() {
			if (element.val() == "") { disableSearch(); }
		});

		function enableSearch() {
			element.addClass("active_search");
			element.removeClass("inactive_search");
			element.val("");
		};

		function disableSearch() {
			element.addClass("inactive_search");
			element.removeClass("active_search");
			element.val(settings.innerText);
		};

	};

	$.fn.livesearch = function(options)
	{
		this.each(function()
		{
			var element = $(this);

			// Return early if this element already has a plugin instance
			if (element.data('livesearch')) return;

			// pass options to plugin constructor
			var livesearch = new LiveSearch(this, options);

			// Store plugin object in this element's data
			element.data('livesearch', livesearch);
		});
	};


	$.fn.livesearch.defaults = {
		queryDelay: 250,
		innerText: "Search",
		minimumSearchLength: 2
	};

})(jQuery);

// Jamroom Launch Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Signup for Beta Launch
 * @return bool
 */
function jrLaunch_signup()
{
    var lsb = $('.launch_submit_button');
    var lnt = $('#launch_notice');
    lsb.attr("disabled", "disabled").addClass('lsb_disabled');
    var timeout = setTimeout(function()
    {
        var values = $('#launch_form').serializeArray();
        var lc_url = core_system_url + '/' + jrLaunch_url + '/signup_save/__ajax=1';
        jrCore_set_csrf_cookie(lc_url);
        $.ajax({
            type: 'POST',
            url: lc_url,
            data: values,
            cache: false,
            dataType: 'json',
            success: function(data)
            {
                // Check for error
                if (typeof data.error !== "undefined") {
                    $('.launch_email').addClass('launch_email_error').focus();
                    lsb.removeAttr("disabled", "disabled").removeClass('lsb_disabled');
                    lnt.text(data.error).fadeIn(250);
                }
                else {
                    $('#launch_form').hide();
                    lnt.text(data.success).addClass('launch_notice_success').fadeIn(250);
                }
            },
            error: function()
            {
                lsb.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                lnt.text('error communicating with server - please try again').addClass('launch_notice_error').fadeIn(250);
            }
        });
        clearTimeout(timeout);
    }, 1000);
}
// Jamroom jrRating Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * Send a Rating request to the jrRating rate_item view
 * @param id string Unique ID for this rating
 * @param r int 1 = 5 rating number
 * @param mu module URL for item being rated
 * @param ii int Item ID
 * @param x int rating index
 * @param t string Target for result
 * @return null
 */
function jrRating_rate_item(id, r, mu, ii, x, t)
{
    var i = $(id);
    var w = i.parent().width();
    i.parent().animate({width: 0}, 300, function() {
        var url = core_system_url + '/' + mu + '/rate/' + ii + '/' + r + '/' + x + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {},
            dataType: 'json',
            success: function(r) {
                if (typeof t !== "undefined" && t === 'alert') {
                    if (r.OK) {
                        jrCore_alert('Success - Average: ' + r.rating_average + ' (' + r.rating_count + ' Votes)');
                    }
                    else if (r.error) {
                        jrCore_alert(r.error);
                    }
                }
                else {
                    if (typeof r.error !== "undefined") {
                        if (r.error == 'login') {
                            jrCore_window_location(core_system_url + '/' + jrUser_url + '/login/r=1');
                        }
                        else {
                            i.parent().animate({width: w}, 100);
                            jrCore_alert(r.error);
                        }
                    }
                    else {
                        // in place update
                        if (r.OK) {
                            i.css('width', Number(r.rating_average) * 20 + '%').css('background-position', 'left center').css('z-index', '6');
                        }
                        i.parent().animate({width: w}, 100);
                    }
                }
            },
            error: function() {
                jrCore_alert('an error was encountered saving the rating - please try again');
            }
        });
    });
}


/**
 * @copyright 2012 Talldude Networks, LLC.
 */

var like_in_progress = false;

/**
 * jrLike_action
 */
function jrLike_action(module_url, item_id, action, uid)
{
    if (!like_in_progress) {
        like_in_progress = true;
        var iid = null;
        var pri = $('#like-state-' + uid);
        var lid = '#l' + uid;
        var did = '#d' + uid;
        var lcn = '#lc' + uid;
        var dcn = '#dc' + uid;
        if (action == 'like') {
            iid = lid;
        }
        else if (action == 'dislike') {
            iid = did;
        }
        else {
            jrCore_alert("Error: Invalid action argument (like/dislike/neutral)");
            like_in_progress = false;
            return false;
        }
        pri.text(action);
        var url = core_system_url + '/' + module_url + '/like_create/' + item_id + '/' + action + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        var old = $(iid + ' img').height();
        $(iid + ' img').animate({height: 0}, 100, function()
        {
            $.ajax({
                type: 'POST',
                cache: false,
                dataType: 'json',
                url: url,
                success: function(r)
                {
                    if (typeof r.OK !== "undefined" && r.OK == 1) {
                        // Success - Change the images
                        $(lid).find('img').attr('src', r.l_src).attr('title', r.l_ttl);
                        $(did).find('img').attr('src', r.d_src).attr('title', r.d_ttl);
                        $(lcn).text(r.l_cnt);
                        $(dcn).text(r.d_cnt);
                    }
                    else if (r.error) {
                        jrCore_alert(r.error);
                    }
                    $(iid + ' img').animate({height: old}, 50);
                    setTimeout(function()
                    {
                        like_in_progress = false;
                    }, 3000);
                },
                error: function()
                {
                    jrCore_alert('an error was encountered saving the like - please try again');
                    like_in_progress = false;
                }
            });
        });
    }
    like_in_progress = false;
    return false;
}

/**
 * Get users that have liked / disliked an item
 */
function jrLike_get_like_users(e, mod, iid, type, uid)
{
    if (Number($(e).text()) > 0) {
        var url = core_system_url + '/' + jrLike_url + '/get_like_users/m=' + jrE(mod) + '/i=' + Number(iid) + '/t=' + type + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'html',
            url: url,
            success: function(r)
            {
                $('#liker_list_' + uid).html(r);
                $('#likers-' + uid).modal();
            },
            error: function()
            {
                jrCore_alert('an error was encountered getting the user list - please try again');
            }
        });
    }
}

// Jamroom jrEvent Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * jrEventAttending
 */
function jrEventAttend(event_id)
{
    var url = core_system_url +'/'+ jrEvent_url +'/attend/'+ event_id +'/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg) {
            if (typeof _msg.error != "undefined") {
                jrCore_alert(_msg.error);
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function() {
            jrCore_alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
}

/**
 * Get widget module info
 * @param i
 */
function jrSeamless_widget_list_get_module_info(i)
{
    var modules = $(i).val();
    var url = core_system_url + '/' + jrSeamless_url + '/widget_list_get_module_info/m=' + jrE(modules) + '/__ajax=1';
    $.get(url, function(resp)
    {
        $('#ff-row-list_custom_template').hide();
        $('#list_pagebreak').removeClass('form_element_disabled').removeAttr('disabled');
        $('#list_limit').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_op').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_text').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_key').each(function()
        {
            $(this).empty().removeClass('form_element_disabled').removeAttr('disabled');
            for (i = 0; i < resp.length; ++i) {
                $(this).append($('<option></option>').attr('value', resp[i]).text(' ' + resp[i]));
            }
        });
        $('#list_order_by_dir').removeClass('form_element_disabled').removeAttr('disabled');
        var id = $('#list_order_by_key');
        id.empty().removeClass('form_element_disabled').removeAttr('disabled');
        for (i = 0; i < resp.length; ++i) {
            id.append($('<option></option>').attr('value', resp[i]).text(' ' + resp[i]));
        }
        id = $('#list_template');
        id.removeClass('form_element_disabled').removeAttr('disabled');
    });
}


/**
 * Load the default module template code into the custom template editor.
 */
function jrSeamless_load_default_code()
{
    var url = core_system_url + '/' + jrSeamless_url + '/default_tpl/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.get(url, function(r)
    {
        var cm = $('.CodeMirror')[0].CodeMirror;
        var ln = cm.getValue();
        if (ln.length > 0) {
            if (confirm('Reload the template content with the default template?')) {
                cm.setValue(r.code);
            }
        }
        else {
            cm.setValue(r.code);
        }
    });
}
// Jamroom Document Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Submit the docs search form
 */
function jrDocs_search_submit()
{
    $('#doc_search_submit').attr("disabled", "disabled").addClass('form_button_disabled');
    $('#form_submit_indicator').show(300, function () {
        setTimeout(function () {
            $('#doc_search_form').submit();
        }, 500);
    });
}

/**
 * Add a new section to an existing document
 */
function jrDocs_create_section(profile_id, item_id, uid, order)
{
    var pid = '#new_section_' + uid;
    if ($(pid).is(":visible")) {
        jrDocs_hide();
    }
    else {
        if (isNaN(parseFloat(order))) {
            order = 'end';
        }
        $('.overlay').hide();
        var bid = $('#new_section_button_' + uid);
        var bc  = bid.width() / 2;
        var bpr = $(window).width() - (bid.offset().left + bc);
        var bpt = bid.offset().top;
        $(pid).appendTo('body').css({'position': 'absolute', 'right': (bpr - 55) + 'px', 'top': (bpt + 25) + 'px'});
        $(pid).fadeIn(250).load(core_system_url + '/' + jrDocs_url + '/get_sections/id=' + Number(item_id) + '/order=' + order + '/profile_id=' + Number(profile_id) + '/__ajax=1');
    }
}

/**
 * Hide the Create New Section drop down
 */
function jrDocs_hide() {
    $(".new_section_box").fadeOut(100);
}

/**
 * Submit new birthday wish
 */
function jrBirthday_submit()
{
    if ($('#birthday_update').val().length < 1) {
        return false;
    }
    $('#birthday_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    var a = $('#birthday_share_indicator');
    a.show(300, function()
    {
        setTimeout(function()
        {
            var f = $('#birthday_share_form');
            $.post(f.attr('action'), f.serializeArray(), function(r)
            {
                if (typeof r.error !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#birthday_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        jrCore_alert(r.error);
                    });
                }
                else {
                    window.location.reload();
                }
            }, 'json');
        }, 200);
    });
}
/**
 * jrRecommend Javascript functions
 * @copyright 2012 Talldude Networks, LLC.
 */

/**
 * Display a modal recommend form
 */
function jrRecommend_modal_form()
{
    $('#recommendform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn(75, function () {
                dialog.container.slideDown(5, function () {
                    dialog.data.fadeIn(75);
                });
            });
        },
        onClose: function (dialog) {
            dialog.data.fadeOut('fast', function () {
                dialog.container.hide('fast', function () {
                    dialog.overlay.fadeOut('fast', function () {
                        $.modal.close();
                    });
                });
            });
        },
        overlayClose:true
    });
}
// Jamroom Chained Select Module javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * jrChainedSelect_get
 */
function jrChainedSelect_get(field, name, lvl, key, value)
{
    var id = '#' + field + '_' + lvl;
    if (typeof value === "undefined" && lvl == 2) {
        key = $('#'+ field +'_0').val() +'|'+ key;
    }
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: core_system_url + '/' + jrChainedSelect_url + '/get/' + name + '/' + lvl + '/' + key + '/__ajax=1',
        success: function(_msg) {
            if (typeof _msg.error != "undefined") {
                if (_msg.error == 'no_data') {
                    $(id).addClass('form_element_disabled').attr('disabled', 'disabled').find('option').remove().end();
                    return true;
                }
                else {
                    alert(_msg.error);
                }
            }
            else if (_msg.ok == '1' && _msg.value != '[]') {
                $(id).removeClass('form_element_disabled').removeAttr('disabled').find('option').remove().end();
                $.each(_msg.value, function(key, value) {
                    $(id).append($('<option></option>').attr('value', key).text(value));
                });
                if (typeof value != "undefined") {
                    $(id).val(value);
                }
                if (lvl == 1) {
                    $('#' + field +'_2').val('-');
                }
            }
            return true;
        },
        error: function() {
            alert('a system level error was encountered submitting the request - please try again (2)');
            return false;
        }
    });
    return false;
}

function jrChainedSelect_load(name, lvl, key)
{
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: core_system_url + '/' + jrChainedSelect_url + '/get/' + name + '/' + lvl + '/' + key + '/nd=1/__ajax=1',
        success: function(_msg) {
            var id = '#set_options';
            $(id).val('');
            if (typeof _msg.error != "undefined") {
                if (_msg.error == 'no_data') {
                    return true;
                }
                else {
                    alert(_msg.error);
                }
            }
            else if (_msg.ok == '1' && _msg.value != '[]') {
                $(id).val(_msg.value);
            }
            return true;
        },
        error: function() {
            alert('a system level error was encountered submitting the request - please try again (2)');
            return false;
        }
    });
    return false;
}

// Jamroom jrGroupMailer module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * Save an email
 */
function jrGroupMailer_save(gid)
{
    var s = $('#groupmailer_title');
    if (s.val().length == 0) {
        s.addClass('field-hilight');
    }
    else {
        s.removeClass('field-hilight');
        $('#save_indicator').show(300, function()
        {
            setTimeout(function()
            {
                $('.form_editor').each(function()
                {
                    if (tinymce.EditorManager.activeEditor !== 'undefined') {
                        $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
                    }
                });
                var v = $('#jrGroupMailer_compose').serializeArray();
                var u = core_system_url + '/' + jrGroupMailer_url + '/save_draft/__ajax=1';
                jrCore_set_csrf_cookie(u);
                $.ajax({
                    url: u,
                    type: 'POST',
                    cache: false,
                    data: v,
                    dataType: 'json',
                    success: function(r)
                    {
                        $('#save_indicator').hide(150, function()
                        {
                            if (typeof r.error !== "undefined") {
                                alert(r.error);
                            }
                            else {
                                if (window.location.href.indexOf('/draft=') == -1) {
                                    window.location = core_system_url + '/' + jrGroupMailer_url + '/compose' + gid + '/draft=' + Number(r.draft_id);
                                }
                            }
                            return true;
                        });
                    },
                    error: function()
                    {
                        $('#save_indicator').hide(150, function()
                        {
                            alert('unable to communicate with server - please try again');
                            return true;
                        });
                    }
                });
            }, 500);
        });
    }
}

function jrGroupMailer_compose_new()
{
    window.location = core_system_url + '/' + jrGroupMailer_url + '/compose';
}

/**
 * Check that we have a template before saving
 */
function jrGroupMailer_check_template()
{
    $('.form_editor').each(function()
    {
        if (tinymce.EditorManager.activeEditor !== 'undefined') {
            $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
        }
    });
    if ($('#groupmailer_message_editor_contents').val().length > 1) {
        $('#save-as-template').modal();
    }
    else {
        alert('There is no Group Email Content to save as a Template');
    }
}

/**
 * Save updates to a Template
 */
function jrGroupMailer_save_template()
{
    $('#save_indicator').show(300, function()
    {
        setTimeout(function()
        {
            $('.form_editor').each(function()
            {
                if (tinymce.EditorManager.activeEditor !== 'undefined') {
                    $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
                }
            });
            var v = $('#jrGroupMailer_compose').serializeArray();
            var u = core_system_url + '/' + jrGroupMailer_url + '/save_template_update/__ajax=1';
            jrCore_set_csrf_cookie(u);
            $.ajax({
                url: u,
                type: 'POST',
                cache: false,
                data: v,
                dataType: 'json',
                success: function(r)
                {
                    $('#save_indicator').hide(150, function()
                    {
                        if (typeof r.error !== "undefined") {
                            alert(r.error);
                        }
                        return true;
                    });
                },
                error: function()
                {
                    $('#save_indicator').hide(150, function()
                    {
                        alert('unable to communicate with server - please try again');
                        return true;
                    });
                }
            });
        }, 500);
    });
}

/**
 * Save an email as a template
 */
function jrGroupMailer_save_as_template()
{
    $('#template_title').val($('#template-title').val() );
    $('.form_editor').each(function()
    {
        if (tinymce.EditorManager.activeEditor !== 'undefined') {
            $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
        }
    });
    var v = $('#jrGroupMailer_compose').serializeArray();
    var u = core_system_url + '/' + jrGroupMailer_url + '/save_template/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        url: u,
        type: 'POST',
        cache: false,
        data: v,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                $('#template-error').text(r.error).show();
            }
            else {
                window.location = core_system_url + '/' + jrGroupMailer_url + '/edit_email_template/id=' + Number(r.tid);
            }
        },
        error: function()
        {
            alert('unable to communicate with server - please try again');
            return true;
        }
    });
    $('#save-as-template').modal();

}

// Jamroom Invite Module javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * jrInvite_load
 */
function jrInvite_load(module)
{
    if (module != '-') {
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'json',
            url: core_system_url + '/' + jrInvite_url + '/load/' + module + '/__ajax=1',
            success: function(_msg) {
                var id = '#invite_item_id';
                $(id).empty();
                if (typeof _msg.error != "undefined") {
                    if (_msg.error == 'no_data') {
                        return true;
                    }
                    else {
                        alert(_msg.error);
                    }
                }
                else if (_msg.ok == '1' && _msg.value != '[]') {
                    $.each(_msg.value, function( key, value) {
                        $(id).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
                return true;
            },
            error: function() {
                alert('a system level error was encountered submitting the request - please try again');
                return false;
            }
        });
    }
}

// Jamroom Combined Video Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Display Create Video options
 * @return bool
 */
function jrCombinedVideo_create_video()
{
    var bid = $('#create_video_button');
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    var pid = $('#create_video_dropdown');
    pid.css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 20) + 'px'});
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        pid.load(core_system_url +'/'+ jrCombinedVideo_url +'/create_video/__ajax=1', function() {
            pid.fadeIn(250);
        });
    }
    return true;
}
// Jamroom Combined Audio Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Display Create Audio options
 * @return bool
 */
function jrCombinedAudio_create_audio()
{
    var bid = $('#create_audio_button');
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    var pid = $('#create_audio_dropdown');
    pid.css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 20) + 'px'});
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        pid.load(core_system_url +'/'+ jrCombinedAudio_url +'/create_audio/__ajax=1', function() {
            pid.fadeIn(250);
        });
    }
    return true;
}
// Jamroom Video Support Module
// @copyright 2003-2015 by Talldude Networks LLC

// This is a STUB
// Jamroom Comment Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

function jrProduct_show_cat_fields(cat_id, prod_id)
{
    var url = core_system_url + '/' + jrProduct_url + '/get_cat_fields/' + cat_id + '/' + prod_id + '/__ajax=1';
    $.getJSON(url, function(data) {
        if (typeof data.success !== "undefined" && typeof data.success === "object") {
            $.each(data.success, function(k, v) {
                $("#cat_fields_label_" + k).html(v.label);
                $("#cat_fields_detail_" + k).html(v.detail);
            });
        }
        else if (typeof data.error !== "undefined" && typeof data.success === "object") {
            jrCore_alert(data.error);
        }
    });
}


// Jamroom Subscriptions Module Javascript
// @copyright 2003-2017 by Talldude Networks LLC

/**
 * set_cookie
 */
function jrSubscribe_set_cookie(c)
{
    if (typeof c === "undefined" || c === null) {
        c = location.href;
    }
    return jrSetCookie('jr_sub_action', c, 1);
}

/**
 * Get a subscription price in cents
 * @param i number Plan ID
 * @param f bool format
 */
function jrSubscribe_get_sub_price(i,f)
{
    var p = $('#sub-price-' + Number(i)).val();
    if (typeof p !== "undefined") {
        p = Number(p.replace(/\D/g,''));
        if (typeof p === "number") {
            if (f !== null && f === 1) {
                return (p / 100).toFixed(2);
            }
            return p;
        }
    }
    return 0;
}

/**
 * Show modal to get variable subscription price
 * @param i number Plan ID
 * @param s string currency symbol
 */
function jrSubscribe_set_sub_price(i, s)
{
    var p = $('#sub-price-' + Number(i)).val();
    $('#sub-plan-id').val(i);
    $('#sub-currency').val(s);
    $('#sub-price-text').attr('placeholder', p);
    $('#sub-price-modal').modal();
}

/**
 * Save a variable price
 */
function jrSubscribe_save_price()
{
    var p = $('#sub-price-text').val();
    var i = $('#sub-plan-id').val();
    var c = $('#sub-currency').val();
    var url = core_system_url + '/' + jrSubscribe_url + '/check_price/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {price: p},
        cache: false,
        dataType: 'json',
        success: function(r) {
            if (typeof r.error !== "undefined") {
                if (r.error.indexOf('minimum') !== -1) {
                    $('#sub-price-text').val('1.00');
                }
                jrCore_alert(r.error);
            }
            else {
                $('#sub-price-' + Number(i)).val(c + p);
                $.modal.close();
            }
        }
    });
}
/**
 * Display a tab in the Embed window
 * @param m {string} module to display tab for
 * @param p {number} page number
 * @param ss {string} Search String
 */
function jrEmbed_load_module(m, p, ss)
{
    var i = $('#embed_panel');
    var s = $('#embed_spinner');
    i.fadeOut(100, function()
    {
        $('.page_tab').removeClass('page_tab_active');
        $('#t' + m).addClass('page_tab_active');
        s.fadeIn(100, function()
        {
            var u = core_system_url + '/' + jrEmbed_url + '/load_module/m=' + jrE(m) + '/p=' + Number(p) + '/ss=' + jrE(ss) + '/__ajax=1';
            i.load(u, function()
            {
                s.fadeOut(100, function()
                {
                    i.fadeIn(100);
                    $('#jrembed_amod').text(m);
                });
            });
        });
    });
}
/**
 * Get pulse counts for viewer
 * @param cb callback function
 */
function jrProfile_get_pulse_counts(cb)
{
    var url = core_system_url + '/' + jrProfile_url + '/get_pulse_counts/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        cache: false,
        success: function(n)
        {
            if (cb !== null && typeof cb == "function") {
                cb(n);
            }
        }
    });
}

/**
 * Reset Pulse count for a key
 * @param key string Key to reset
 * @param cb function callback
 */
function jrProfile_reset_pulse_key(key, cb)
{
    var url = core_system_url + '/' + jrProfile_url + '/reset_pulse_count/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {key: key}, function()
    {
        if (cb !== null && typeof cb == "function") {
            return cb();
        }
        return false;
    });
}
// Jamroom FoxyCart Bundle Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Display item bundles
 */
function jrBundle_display_bundles(o, id)
{
    var pid = $('.bundle_drop_down');
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        var bid = $(o);
        var bpr = $(window).width() - bid.offset().left;
        var bpt = bid.offset().top;
        pid.css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'html',
            url: core_system_url + '/' + jrBundle_url + '/display/id=' + id,
            success: function(r)
            {
                if (typeof r !== 'undefined' && r !== null && r.indexOf('ERROR') === 0) {
                    if (r.indexOf('not found') !== -1) {
                        window.location.reload();
                    }
                    else {
                        jrCore_alert(r);
                    }
                }
                else {
                    pid.html(r).fadeIn(200);
                }
            }
        });
    }
    return true;
}

/**
 * Close any open Bundle display box
 */
function jrBundle_close()
{
    $('.bundle_box').fadeOut(100);
}

/**
 * show the bundle to add this item to.
 */
function jrBundle_select(id, field, mod, page)
{
    var pid = '#bundle_' + id;
    var url = '';
    if (page === null || typeof page === "undefined") {
        if ($(pid).is(":visible")) {
            $(pid).fadeOut(100);
            jrBundle_close();
        }
        else {
            $('.overlay').hide();
            jrBundle_position(id);
            url = core_system_url + '/' + jrBundle_url + '/select/' + jrE(mod) + '/' + Number(id) + '/field=' + jrE(field) + '/p=1/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $(pid).fadeIn(250).load(url);
        }
    }
    else {
        url = core_system_url + '/' + jrBundle_url + '/select/' + jrE(mod) + '/' + Number(id) + '/field=' + jrE(field) + '/p=' + page + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $(pid).load(url);
    }
}

/**
 * position the bundle on the page
 */
function jrBundle_position(id)
{
    var bid = $('#bundle_button_' + id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#bundle_' + id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a bundle
 * @param {int} bid Bundle id
 * @param {string} mod Module Item belongs to
 * @param {int} id Item ID of item being removed
 */
function jrBundle_remove(bid, mod, id)
{
    var url = core_system_url + '/' + jrBundle_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            bundle_id: bid,
            bundle_module: mod,
            item_id: id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(r)
        {
            if (r.type === 'success') {
                window.location.reload();
            }
            else {
                jrCore_alert('error received trying to remove item: ' + r.note);
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrBundle_inject(bid, id, field, mod)
{
    var dc = 'form_button_disabled';
    var btn = $('.bundle_button');
    btn.prop('disabled', true).addClass(dc);
    var url = core_system_url + '/' + jrBundle_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            bundle_id: bid,
            item_id: id,
            bundle_module: mod,
            field: field
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(r)
        {
            if (r.type === 'success') {
                btn.prop('disabled', false).removeClass(dc);
                jrBundle_close();
            }
            else {
                btn.prop('disabled', false).removeClass(dc);
                $('#bundle_message').addClass('error').text(r.note).show();
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrBundle_new(id, field, mod)
{
    var dc = 'form_button_disabled';
    var bc = $('#bundle_close');
    var bm = $('#bundle_message');
    var bi = $('#bundle_indicator');
    var bb = $('#bundle_button');
    bc.hide();
    bm.hide();
    bi.show();
    $('.bundle_button').attr("disabled", "disabled").addClass(dc);
    $('.field-hilight').removeClass('field-hilight');
    setTimeout(function()
    {
        var ttl = $('#new_bundle_' + id);
        var prc = $('#bundle_price_' + id);
        if (ttl.val().length > 0 && prc.val().length > 0) {
            var url = core_system_url + '/' + jrBundle_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: ttl.val(),
                    bundle_price: prc.val(),
                    item_id: Number(id),
                    bundle_module: mod,
                    field: field
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(r)
                {
                    if (r.type === 'success') {
                        bi.hide();
                        bm.removeClass('error').addClass('success').text(r.note).show();
                        setTimeout(function()
                        {
                            jrBundle_close();
                            bc.show();
                            bb.removeAttr('disabled').removeClass(dc);
                        }, 1000);
                    }
                    else {
                        bc.show();
                        bi.hide();
                        bm.addClass('error').text(r.note).show();
                        bb.removeAttr('disabled').removeClass(dc);
                    }
                }
            });

        }
        else {
            if (ttl.val().length === 0) {
                ttl.addClass('field-hilight');
            }
            if (prc.val().length === 0) {
                prc.addClass('field-hilight');
            }
            bi.hide();
            $('.bundle_button').removeAttr('disabled').removeClass(dc);
        }
    }, 750);
}

// Jamroom Ning Groups Javascript
// @copyright 2003-2011 by Talldude Networks LLC

/**
 * Join a Group
 * @param action string
 * @param group_id int
 * @param gclass string
 * @returns {boolean}
 */
function jrGroupButton(action, group_id, gclass)
{
    if (action == 'join' || action == 'leave' || action == 'cancel') {
        var bid = '#group_button';
        $(bid).attr('disabled', 'disabled');
        var url = core_system_url + '/' + jrGroup_url + '/button/' + action + '/' + group_id + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: url,
            success: function(_msg)
            {
                if (typeof _msg.error != "undefined") {
                    alert(_msg.error);
                }
                else {
                    window.location.reload();
                }
                return true;
            },
            error: function()
            {
                alert('a system level error was encountered submitting the request - please try again');
                return false;
            }
        });
    }
    return false;
}
/*
 * 	Character Count Plugin - jQuery plugin
 * 	Dynamic character count for text areas and input fields
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *	Built for jQuery library
 *	http://jquery.com
 *  @modified for JR5 by the Jamroom Network.
 */
 
(function($) {

	$.fn.charCount = function(options){
	  
		// default configuration properties
		var defaults = {	
			allowed: 140,
			warning: 20,
			cssWarning: 'action_warning',
			cssExceeded: 'action_exceeded'
		};
		options = $.extend(defaults, options);
		
		function calculate(obj){
			var count = $(obj).val().length;
			var available = options.allowed - count;
			if (available <= options.warning && available >= 0){
				$('#action_text_counter').addClass(options.cssWarning);
			} else {
				$('#action_text_counter').removeClass(options.cssWarning);
			}
			if (available < 0){
				$('#action_text_counter').addClass(options.cssExceeded);
			} else {
				$('#action_text_counter').removeClass(options.cssExceeded);
			}
            $('#action_text_num').html(available);
		};
		this.each(function() {
			calculate(this);
			$(this).keyup(function(){calculate(this)});
			$(this).change(function(){calculate(this)});
		});

	};

})(jQuery);

/**
 * Share a timeline item to your timeline
 * @param mod string
 * @param id int
 */
function jrAction_share(mod, id)
{
    if ($('#share_modal').length === 0) {
        $('body').append('<div id="share_modal"></div>');
    }
    var u = core_system_url + '/' + jrAction_url + '/share_msg/' + mod + '/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function(r)
    {
        $('#share_modal').html(r).modal();
        $("#share_update").focus()
    });
    return false;
}

/**
 * Save a new share
 * @returns {boolean}
 */
function jrAction_share_save()
{
    $('#share_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    $('#share_submit_indicator').show(300, function()
    {
        $('#share_form').submit()
    });
}

/**
 * Load a Quick Share form
 * @param t object This
 * @param m string Module
 * @param f string Function
 */
var __ds_title = '';
function jrAction_quick_share(t, m, f)
{
    $('.quick_action_tab_active').removeClass('quick_action_tab_active').children('span').removeClass('sprite_icon_hilighted');
    $(t).addClass('quick_action_tab_active').children('span').addClass('sprite_icon_hilighted');
    $('#jrAction_function').val(f);
    var d = $('#quick_action_default_form');
    if (f == 'jrAction_quick_share_status_update') {
        if (d.is(':hidden')) {
            var q = $('#quick_action_form');
            q.fadeOut(100, function()
            {
                d.fadeIn(100, function()
                {
                    $('#action_update').focus();
                });
            });
            $('#quick_action_title').text(__ds_title);
        }
    }
    else {
        if (__ds_title.length === 0) {
            __ds_title = $('#quick_action_title').text();
        }
        var u = core_system_url + '/' + jrAction_url + '/quick_share_form/__ajax=1';
        $.ajax({
            type: 'POST',
            data: {m: m, function: f},
            cache: false,
            dataType: 'json',
            url: u,
            success: function(r)
            {
                var a = $('#quick_action_form input');
                var q = $('#quick_action_form');
                if (d.is(':visible')) {
                    d.fadeOut(100, function()
                    {
                        q.html(r.html).fadeIn(100, function()
                        {
                            a.first().focus();
                        });
                    });
                }
                else {
                    q.fadeOut(100, function()
                    {
                        q.html(r.html).fadeIn(100, function()
                        {
                            a.first().focus();
                        });
                    });
                }
                $('#quick_action_title').text(r.title);
            }
        });
    }
}

/**
 * Submit new action from timeline form
 */
function jrAction_submit()
{
    if ($('#quick_action_default_form').is(':visible')) {
        var e = $('#action_text_editor_contents');
        if (e.length) {
            // Editor is enabled
            if (tinyMCE.get('eaction_text').getContent().length < 1) {
                return false;
            }
            e.val(tinyMCE.get('eaction_text').getContent());
        }
        else if ($('#action_update').val().length < 1) {
            return false;
        }
    }
    $('#quick_action_form').find('input').removeClass('field-hilight');
    $('#action_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    var a = $('#asi');
    a.show(300, function()
    {
        setTimeout(function()
        {
            var f = $('#action_form');
            $.post(f.attr('action'), f.serializeArray(), function(r)
            {
                if (typeof r.field !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#action_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        $('#quick_action_form').find('#' + r.field).addClass('field-hilight');
                    });
                }
                else if (typeof r.error !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#action_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        jrCore_alert(r.error);
                    });
                }
                else {
                    window.location.reload();
                }
            }, 'json');
        }, 200);
    });
}

/**
 * copy and alteration of the character count plugin
 * Character Count Plugin - jQuery plugin
 * Dynamic character count for text areas and input fields
 * written by Alen Grakalic
 * http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
 * Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * Built for jQuery library
 * http://jquery.com
 * @modified for JR5 by the Jamroom Network.
 */
(function($)
{
    $.fn.shareCharCount = function(options)
    {
        // default configuration properties
        var defaults = {
            allowed: 140,
            warning: 20,
            cssWarning: 'share_warning',
            cssExceeded: 'share_exceeded'
        };
        options = $.extend(defaults, options);

        function calculate(obj)
        {
            var count = $(obj).val().length;
            var available = options.allowed - count;
            if (available <= options.warning && available >= 0) {
                $('#share_text_counter').addClass(options.cssWarning);
            }
            else {
                $('#share_text_counter').removeClass(options.cssWarning);
            }
            if (available < 0) {
                $('#share_text_counter').addClass(options.cssExceeded);
            }
            else {
                $('#share_text_counter').removeClass(options.cssExceeded);
            }
            $('#share_text_num').html(available);
        };
        this.each(function()
        {
            calculate(this);
            $(this).keyup(function()
            {
                calculate(this)
            });
            $(this).change(function()
            {
                calculate(this)
            });
        });
    };
})(jQuery);


// Jamroom Comment Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Submits a new Comment
 * @param {string} uid Unique Form ID
 * @param tpl the name of the template to use to format the returning comments.
 * @param limit the number of results returned in the comments
 * @return bool
 */
function jrPostComment(uid, tpl, limit)
{
    var s = 300;
    var usub = $(uid + '_cm_submit');
    var unot = $(uid + '_cm_notice');
    var ufsi = $(uid + '_fsi');
    if (ufsi.length === 0) {
        //noinspection JSJQueryEfficiency
        $('body').append('<div id="' + uid.substr(1) + '_fsi"></div>');
        //noinspection JSJQueryEfficiency
        ufsi = $('body').find(uid + '_fsi');
        s = 0;
    }

    usub.attr("disabled", "disabled").addClass('form_button_disabled');
    ufsi.show(s, function()
    {
        unot.hide();
        var t = setTimeout(function()
        {
            var val = $(uid + '_form').serializeArray();
            var url = core_system_url + '/' + jrComment_url + '/comment_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                url: url,
                data: val,
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    if (typeof r.error !== "undefined") {
                        unot.text(r.error);
                        ufsi.hide(s, function()
                        {
                            usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                            unot.fadeIn(250);
                        });
                    }
                    else {
                        $(uid + '_form textarea').val('');
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        ufsi.hide(s, function()
                        {
                            var mod = $(uid + '_cm_module').val();
                            var iid = $(uid + '_cm_item_id').val();
                            var ord = $(uid + '_cm_order_by').val();
                            var now = new Date().getTime();
                            $(uid + '_comments').load(core_system_url + '/' + jrComment_url + '/view_comments/item_module=' + jrE(mod) + '/item_id=' + Number(iid) + '/order_by=' + jrE(ord) + '/template=' + tpl + '/limit=' + Number(limit) + '/new=' + Number(r.item_id) + '/__ajax=1/_v=' + now, function()
                            {
                                $('#comment_form_section').slideDown(300);
                                // Go to our comment
                                var cid = '#cm' + r.item_id;
                                if (r.highlight == 'on') {
                                    $('html, body').animate({scrollTop: $(cid).offset().top - 200}, 300);
                                }
                                if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined") {
                                    tinyMCE.activeEditor.setContent('');
                                    $('#comment_reply_to_user').text('');
                                    $('#comment_reply_to').hide();
                                }
                                // Hide any file attachments from post
                                $('.qq-upload-list').html('');
                                $('#comment_parent_id').val('0');
                            });
                        });
                    }
                },
                error: function()
                {
                    ufsi.hide(s, function()
                    {
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        unot.text('Error communicating with server - please try again').show();
                    });
                }
            });
            clearTimeout(t);
        }, (s * 3));
    });
}

/**
 * Append new comments
 * @param module {string} Comment module
 * @param item_id {number} Item_ID
 * @param this_page {number} Current Page Number
 * @param next_page {number} Next Page Number
 * @returns {boolean}
 */
function jrComment_load(module, item_id, this_page, next_page)
{
    $('#cploader').slideDown(300, function()
    {
        setTimeout(function()
        {
            var url = core_system_url + '/' + jrComment_url + '/view_comments/item_module=' + jrE(module) + '/item_id=' + Number(item_id) + '/p=' + Number(next_page) + '/__ajax=1';
            $.get(url, function(res)
            {
                $('#cpholder' + this_page).remove();
                $('.comment_page_section').append(res);
            });
        }, 200);
    });
    return false;
}

/**
 * Reply to a comment (threaded)
 * @param id int Item ID
 * @param un string User Name
 */
function jrComment_reply_to(id, un)
{
    if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined") {
        $('#comment_reply_to_user').text(un);
        $('#comment_reply_to').show();
        $('#comment_parent_id').val(id);
        var h = $('#header').outerHeight();
        $('html, body').animate({scrollTop: $('#cform').offset().top - h}, 200);
        tinyMCE.execCommand('mceFocus', true, 'comment_text');
    }
    else {
        var rid = '#r' + id;
        if ($(rid).is(':visible')) {
            // Already showing - remove
            $(rid).slideUp(100).empty();
            $('#comment_form_section').slideDown(300);
        }
        else {
            var i = '#comment_form_section';
            var ht = $(i).html();
            $(ht).appendTo('#r' + id);
            $(i).slideUp(100);
            $(rid).slideDown(300);
            $('#comment_text').focus();
            $('#comment_parent_id').val(id);
        }
    }
    return false;
}

/**
 * Grab post data for new post
 * @param id int
 */
function jrCommentQuotePost(id)
{
    var u = core_system_url + '/' + jrComment_url + '/quote/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        type: 'GET',
        url: u,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            var i = $('#comment_text');
            var c = ((r.match(/\n/g) || []).length * 15) + 20;
            var l = i.height() + 30;
            if (c < l) {
                c = l;
            }
            i.css('height', c + 'px').val(r);
            $('html, body').animate({scrollTop: i.offset().top}, 'fast');
            i.selectRange(r.length);
        }
    });
}

/**
 * Grab post data for new post (into editor)
 * @param id
 */
function jrCommentEditorQuotePost(id)
{
    var u = core_system_url + '/' + jrComment_url + '/quote/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        type: 'GET',
        url: u,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            tinymce.activeEditor.selection.setContent(r);
            tinymce.activeEditor.focus();
            $('html, body').animate({
                scrollTop: $("#cform").offset().top
            }, 100);
        }
    });
    return false;
}

/**
 * construct the default skin dropdown list from checkbox options in QUOTA CONFIG tab
 */
function jrProfileTweaks_default_skin_options()
{
    var sel = $('#default_skin');
    var hide = true;
    var selected = sel.val();

    sel.empty();

    $('[id^=allow_skin_]:checked').each(function()
    {
        hide = false;
        sel.append($("<option>").attr('value', $(this).data('key')).text($(this).next('.form_option_list_text').text()));

        if ($(this).data('key') == selected) {
            sel.val(selected);
        }
    });

    if (hide) {
        $('#ff-row-default_skin').hide();
    }
    else {
        $('#ff-row-default_skin').show();
    }
}

// Sticky Plugin v1.0.3 for jQuery
// =============
// Author: Anthony Garand
// Improvements by German M. Bravo (Kronuz) and Ruud Kamphuis (ruudk)
// Improvements by Leonardo C. Daronco (daronco)
// Created: 02/14/2011
// Date: 07/20/2015
// Website: http://stickyjs.com/
// Description: Makes an element on the page stick on the screen as you scroll
//              It will only set the 'top' and 'position' of your element, you
//              might need to adjust the width in some cases.

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var slice = Array.prototype.slice; // save ref to original slice()
    var splice = Array.prototype.splice; // save ref to original slice()

  var defaults = {
      topSpacing: 0,
      bottomSpacing: 0,
      className: 'is-sticky',
      wrapperClassName: 'sticky-wrapper',
      center: false,
      getWidthFrom: '',
      widthFromWrapper: true, // works only when .getWidthFrom is empty
      responsiveWidth: false
    },
    $window = $(window),
    $document = $(document),
    sticked = [],
    windowHeight = $window.height(),
    scroller = function() {
      var scrollTop = $window.scrollTop(),
        documentHeight = $document.height(),
        dwh = documentHeight - windowHeight,
        extra = (scrollTop > dwh) ? dwh - scrollTop : 0;

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i],
          elementTop = s.stickyWrapper.offset().top,
          etse = elementTop - s.topSpacing - extra;

        //update height in case of dynamic content
        s.stickyWrapper.css('height', s.stickyElement.outerHeight());

        if (scrollTop <= etse) {
          if (s.currentTop !== null) {
            s.stickyElement
              .css({
                'width': '',
                'position': '',
                'top': ''
              });
            s.stickyElement.parent().removeClass(s.className);
            s.stickyElement.trigger('sticky-end', [s]);
            s.currentTop = null;
          }
        }
        else {
          var newTop = documentHeight - s.stickyElement.outerHeight()
            - s.topSpacing - s.bottomSpacing - scrollTop - extra;
          if (newTop < 0) {
            newTop = newTop + s.topSpacing;
          } else {
            newTop = s.topSpacing;
          }
          if (s.currentTop !== newTop) {
            var newWidth;
            if (s.getWidthFrom) {
                newWidth = $(s.getWidthFrom).width() || null;
            } else if (s.widthFromWrapper) {
                newWidth = s.stickyWrapper.width();
            }
            if (newWidth == null) {
                newWidth = s.stickyElement.width();
            }
            s.stickyElement
              .css('width', newWidth)
              .css('position', 'fixed')
              .css('top', newTop);

            s.stickyElement.parent().addClass(s.className);

            if (s.currentTop === null) {
              s.stickyElement.trigger('sticky-start', [s]);
            } else {
              // sticky is started but it have to be repositioned
              s.stickyElement.trigger('sticky-update', [s]);
            }

            if (s.currentTop === s.topSpacing && s.currentTop > newTop || s.currentTop === null && newTop < s.topSpacing) {
              // just reached bottom || just started to stick but bottom is already reached
              s.stickyElement.trigger('sticky-bottom-reached', [s]);
            } else if(s.currentTop !== null && newTop === s.topSpacing && s.currentTop < newTop) {
              // sticky is started && sticked at topSpacing && overflowing from top just finished
              s.stickyElement.trigger('sticky-bottom-unreached', [s]);
            }

            s.currentTop = newTop;
          }

          // Check if sticky has reached end of container and stop sticking
          var stickyWrapperContainer = s.stickyWrapper.parent();
          var unstick = (s.stickyElement.offset().top + s.stickyElement.outerHeight() >= stickyWrapperContainer.offset().top + stickyWrapperContainer.outerHeight()) && (s.stickyElement.offset().top <= s.topSpacing);

          if( unstick ) {
            s.stickyElement
              .css('position', 'absolute')
              .css('top', '')
              .css('bottom', 0);
          } else {
            s.stickyElement
              .css('position', 'fixed')
              .css('top', newTop)
              .css('bottom', '');
          }
        }
      }
    },
    resizer = function() {
      windowHeight = $window.height();

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i];
        var newWidth = null;
        if (s.getWidthFrom) {
            if (s.responsiveWidth) {
                newWidth = $(s.getWidthFrom).width();
            }
        } else if(s.widthFromWrapper) {
            newWidth = s.stickyWrapper.width();
        }
        if (newWidth != null) {
            s.stickyElement.css('width', newWidth);
        }
      }
    },
    methods = {
      init: function(options) {
        var o = $.extend({}, defaults, options);
        return this.each(function() {
          var stickyElement = $(this);

          var stickyId = stickyElement.attr('id');
          var wrapperId = stickyId ? stickyId + '-' + defaults.wrapperClassName : defaults.wrapperClassName;
          var wrapper = $('<div></div>')
            .attr('id', wrapperId)
            .addClass(o.wrapperClassName);

          stickyElement.wrapAll(wrapper);

          var stickyWrapper = stickyElement.parent();

          if (o.center) {
            stickyWrapper.css({width:stickyElement.outerWidth(),marginLeft:"auto",marginRight:"auto"});
          }

          if (stickyElement.css("float") === "right") {
            stickyElement.css({"float":"none"}).parent().css({"float":"right"});
          }

          o.stickyElement = stickyElement;
          o.stickyWrapper = stickyWrapper;
          o.currentTop    = null;

          sticked.push(o);

          methods.setWrapperHeight(this);
          methods.setupChangeListeners(this);
        });
      },

      setWrapperHeight: function(stickyElement) {
        var element = $(stickyElement);
        var stickyWrapper = element.parent();
        if (stickyWrapper) {
          stickyWrapper.css('height', element.outerHeight());
        }
      },

      setupChangeListeners: function(stickyElement) {
        if (window.MutationObserver) {
          var mutationObserver = new window.MutationObserver(function(mutations) {
            if (mutations[0].addedNodes.length || mutations[0].removedNodes.length) {
              methods.setWrapperHeight(stickyElement);
            }
          });
          mutationObserver.observe(stickyElement, {subtree: true, childList: true});
        } else {
          stickyElement.addEventListener('DOMNodeInserted', function() {
            methods.setWrapperHeight(stickyElement);
          }, false);
          stickyElement.addEventListener('DOMNodeRemoved', function() {
            methods.setWrapperHeight(stickyElement);
          }, false);
        }
      },
      update: scroller,
      unstick: function(options) {
        return this.each(function() {
          var that = this;
          var unstickyElement = $(that);

          var removeIdx = -1;
          var i = sticked.length;
          while (i-- > 0) {
            if (sticked[i].stickyElement.get(0) === that) {
                splice.call(sticked,i,1);
                removeIdx = i;
            }
          }
          if(removeIdx !== -1) {
            unstickyElement.unwrap();
            unstickyElement
              .css({
                'width': '',
                'position': '',
                'top': '',
                'float': ''
              })
            ;
          }
        });
      }
    };

  // should be more efficient than using $window.scroll(scroller) and $window.resize(resizer):
  if (window.addEventListener) {
    window.addEventListener('scroll', scroller, false);
    window.addEventListener('resize', resizer, false);
  } else if (window.attachEvent) {
    window.attachEvent('onscroll', scroller);
    window.attachEvent('onresize', resizer);
  }

  $.fn.sticky = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };

  $.fn.unstick = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.unstick.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };
  $(function() {
    setTimeout(scroller, 0);
  });
}));


/**
 * Skin initialization
 */
function jrAudioPro_init()
{
    $('section#profile_menu').sticky({
        topSpacing: 50,
        'classes': {
            'element': 'jquery-sticky-element',
            'start': 'jquery-sticky-start',
            'sticky': 'jquery-sticky-sticky',
            'stopped': 'jquery-sticky-stopped',
            'placeholder': 'jquery-sticky-placeholder'
        }
    });

    $('.profile_image').hover(function()
    {
        $(this).find(".profile_hoverimage").fadeIn();

    }, function()
    {
        $(this).find(".profile_hoverimage").fadeOut();
    });

    var menu = $("ul#horizontal");

    // Get static values here first
    var vw = 0, ctr = menu.children().length;         // number of children will not change
    menu.children().each(function()
    {
        vw += $(this).outerWidth();  // widths will not change, so just a total
    });

    jrAudioPro_collect();  // fire first collection on page load
    $(window).resize(jrAudioPro_collect); // fire collection on window resize

    function jrAudioPro_collect()
    {
        menu.css({
            visibility: 'collapse',
            'width': "calc(100% - 112px)"
        });

        // Calculate fitCount on the total width this time
        var fc = Math.floor((menu.width() / vw) * ctr) - 1;

        // Reset display and width on all list-items
        menu.children().css({"display": "block", "width": "auto"});

        // Make a set of collected list-items based on fc
        var cs = menu.children(":gt(" + fc + ")");

        $('ul#horizontal .hideshow').remove();

        menu.append($('#pm-drop-opt').html());

        // Empty the more menu and add the collected items
        $("#submenu").empty().append(cs.clone());

        // Set display to none and width to 0 on collection,
        // because they are not visible anyway.
        cs.css({"display": "none", "width": "0"});

        if (cs.length > 0) {
            $('ul#horizontal li.hideshow').css('display', 'block').click(function()
            {
                $(this).children("ul").toggle();
            });
        }
        menu.css({
            visibility: 'visible',
            'width': "100%"
        });
    }

    // Scroll To Top Function
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').addClass('show');
        } else {
            $('.scrollup').removeClass('show');
        }
    });

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
}
/**
 * Open a modal window
 * @param id
 * @param profile_url
 */
function jrAudioPro_modal(id, profile_url)
{
    $(id).modal();
    if (profile_url) {
        $('#action_update').text(profile_url + ' ');
    }
}


$(document).ready(function()
{
    jrAudioPro_init();
});

function jrAudioPro_chart_days(days) {

    var url = core_system_url + '/index_chart_days/days=' + Number(days) + '/__ajax=1';
    if (url.length > 0) {
        $('#chartLoader').show(50, function()
        {
            setTimeout(function()
            {
                $.get(url, function(res)
                {
                    $('#chartLoader').hide();
                    $('#chart').html(res);
                });
            }, 200);
        });
    }
    return false;


}

