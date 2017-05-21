{jrCore_server_protocol assign="prt"}
<script type="text/javascript">
    var disqus_shortname = '{$_conf.jrDisqus_site_name|addslashes}';
    (function () {
        var s = document.createElement('script');
        s.async = true;
        s.type = 'text/javascript';
        s.src = '{$prt}://' + disqus_shortname + '.disqus.com/count.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
</script>
