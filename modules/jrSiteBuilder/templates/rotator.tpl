<script type="text/javascript">
    $(function() {
        $("#s{$unique_id}").responsiveSlides({
            auto: true,          {* Boolean: Animate automatically, true or false *}
            speed: 400,          {* Integer: Speed of the transition, in milliseconds *}
            timeout: 4000,       {* Integer: Time between slide transitions, in milliseconds *}
            pager: true,         {* Boolean: Show pager, true or false *}
            random: true,        {* Boolean: Randomize the order of the slides, true or false *}
            pause: true,         {* Boolean: Pause on hover, true or false *}
            maxwidth: 0,         {* Integer: Max-width of the slideshow, in pixels *}
            namespace: "rslides" {* String: change the default namespace used *}
        });
    });
</script>
<div class="block_content">
    <div class="ck_rotator">
        <div class="callbacks_container">
            <div class="ioutline">
                <ul id="s{$unique_id}" class="rslides callbacks">
                    {*{jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" template=$default_template require_image="profile_image"}*}
                    {$row_output}
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>