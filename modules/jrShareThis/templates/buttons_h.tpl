{if isset($_conf.jrShareThis_pub_key) && strlen($_conf.jrShareThis_pub_key) > 0}
<div class="item">
<div class="ml10">
    <h3>{jrCore_lang module="jrShareThis" id="1" default="Share This"}</h3>
</div>

<div class="mt10">
{foreach $chicklets as $ck => $cn}
    <span class='st_{$ck}_hcount'></span>
{/foreach}
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $(function() {
       try { stLight.options( { publisher: "{$_conf.jrShareThis_pub_key}", hashAddressBar: false, shorten: false{$copy_share} } ); } catch(e) { };
    } );
} );
</script>
{/if}