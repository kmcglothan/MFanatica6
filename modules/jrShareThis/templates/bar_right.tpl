{if isset($_conf.jrShareThis_pub_key) && strlen($_conf.jrShareThis_pub_key) > 0}
<script type="text/javascript">
$(document).ready(function() {
    $(function() {
       try { stLight.options( { publisher: "{$_conf.jrShareThis_pub_key}", hashAddressBar: false, shorten: false{$copy_share} } ); } catch(e) { } ;
    } );
    try {
        var options = {
            "publisher": "{$_conf.jrShareThis_pub_key}",
            "position": "right",
            "ad": { "visible": false, "openDelay": 2, "closeDelay": 0 },
            "chicklets": { "items": [{$chicklets_cs}] }
        };
        var st_hover_widget = new sharethis.widgets.hoverbuttons(options);
    }
    catch(e) { };

} );
</script>
{/if}