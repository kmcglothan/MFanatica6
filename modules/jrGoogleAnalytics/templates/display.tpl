var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{$_conf.jrGoogleAnalytics_account}']);
{if strlen($_conf.jrGoogleAnalytics_domain) > 0}
_gaq.push(['_setDomainName', '{$_conf.jrGoogleAnalytics_domain}']);
{/if}
_gaq.push(['_trackPageview']);
{literal}
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
{/literal}
