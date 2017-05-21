<div class="item" id="shareThis" style="display: none">
{foreach $chicklets as $ck => $cn}
    <span class='st_{$ck}_large' displayText='{$cn}'></span>
{/foreach}

</div>

<script type="text/javascript">
$(document).ready(function() {
    $(function() {
        var switchTo5x = true;
        stLight.options( { publisher: "{$_conf.jrShareThis_pub_key}", doNotHash: false, doNotCopy: false, hashAddressBar: false, shorten: false } );
    } );
} );
</script>