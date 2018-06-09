{if  strlen($page_template) == 0}
</div>
{/if}
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                {* Logo *}
                <div class="col6">
                    <div id="footer_sn">

                        {* Social Network Linkup *}
                        {if strlen($_conf.jrElastic2_twitter_url) > 1}
                            {if $_conf.jrElastic2_twitter_url|strpos:"https:" !== false}
                                <a href="{$_conf.jrElastic2_twitter_url}" target="_blank">{jrCore_image image="sn-twitter.png" width="40" height="40" class="social-img" alt="twitter" title="Follow @{$_conf.jrElastic2_twitter_url}"}</a>
                            {else}
                                <a href="https://twitter.com/{$_conf.jrElastic2_twitter_url}" target="_blank">{jrCore_image image="sn-twitter.png" width="40" height="40" class="social-img" alt="twitter" title="Follow @{$_conf.jrElastic2_twitter_url}"}</a>
                            {/if}
                        {/if}

                        {if strlen($_conf.jrElastic2_facebook_url) > 1}
                            {if $_conf.jrElastic2_facebook_url|strpos:"https:" !== false}
                                <a href="{$_conf.jrElastic2_facebook_url}" target="_blank">{jrCore_image image="sn-facebook.png" width="40" height="40" class="social-img" alt="facebook" title="Like {$_conf.jrElastic2_facebook_url} on Facebook"}</a>
                            {else}
                                <a href="https://facebook.com/{$_conf.jrElastic2_facebook_url}" target="_blank">{jrCore_image image="sn-facebook.png" width="40" height="40" class="social-img" alt="facebook" title="Like {$_conf.jrElastic2_facebook_url} on Facebook"}</a>
                            {/if}
                        {/if}

                        {if strlen($_conf.jrElastic2_linkedin_url) > 1}
                            {if $_conf.jrElastic2_linkedin_url|strpos:"https:" !== false}
                                <a href="{$_conf.jrElastic2_linkedin_url}" target="_blank">{jrCore_image image="sn-linkedin.png" width="40" height="40" class="social-img" alt="linkedin" title="Link up with {$_conf.jrElastic2_linkedin_url} on LinkedIn"}</a>
                            {else}
                                <a href="https://linkedin.com/in/{$_conf.jrElastic2_linkedin_url}" target="_blank">{jrCore_image image="sn-linkedin.png" width="40" height="40" class="social-img" alt="linkedin" title="Link up with {$_conf.jrElastic2_linkedin_url} on LinkedIn"}</a>
                            {/if}
                        {/if}

                        {if strlen($_conf.jrElastic2_google_url) > 1}
                            {if $_conf.jrElastic2_google_url|strpos:"https:" !== false}
                                <a href="{$_conf.jrElastic2_google_url}" target="_blank">{jrCore_image image="sn-google-plus.png" width="40" height="40" class="social-img" alt="google+" title="Follow {$_conf.jrElastic2_google_url} on Google+"}</a>
                            {else}
                                <a href="https://plus.google.com/u/0/{$_conf.jrElastic2_google_url}" target="_blank">{jrCore_image image="sn-google-plus.png" width="40" height="40" class="social-img" alt="google+" title="Follow {$_conf.jrElastic2_google_url} on Google+"}</a>
                            {/if}
                        {/if}

                        {if strlen($_conf.jrElastic2_youtube_url) > 1}
                            {if $_conf.jrElastic2_youtube_url|strpos:"https:" !== false}
                                <a href="{$_conf.jrElastic2_youtube_url}" target="_blank">{jrCore_image image="sn-youtube.png" width="40" height="40" class="social-img" alt="youtube" title="Subscribe to {$_conf.jrElastic2_youtube_url} on YouTube"}</a>
                            {else}
                                <a href="https://www.youtube.com/channel/{$_conf.jrElastic2_youtube_url}" target="_blank">{jrCore_image image="sn-youtube.png" width="40" height="40" class="social-img" alt="youtube" title="Subscribe to {$_conf.jrElastic2_youtube_url} on YouTube"}</a>
                            {/if}
                        {/if}

                    </div>
                </div>

                {* Text *}
                <div class="col6 last">
                    <div id="footer_text">
                        &copy;{$smarty.now|date_format:"%Y"} <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a><br>
                        {* An auto footer that rotates phrases to help jamroom.net.  If you like jamroom, leave this here. We'd appreciate it.  Thanks. *}
                        {jrCore_powered_by}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{if jrCore_is_mobile_device()}
</div>
    {* Slidebars *}
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                var ms = new $.slidebars();
                $('#mmt').on('click', function() {
                    ms.slidebars.toggle('left');
                });
            });
        }) (jQuery);
    </script>

{/if}

<a href="#" id="scrollup" class="scrollup">{jrCore_icon icon="arrow-up"}</a>

{if isset($css_footer_href)}
    {foreach from=$css_footer_href item="_css"}
        <link rel="stylesheet" href="{$_css.source}" media="{$_css.media|default:"screen"}"/>
    {/foreach}
{/if}
{if isset($javascript_footer_href)}
    {foreach from=$javascript_footer_href item="_js"}
        <script type="{$_js.type|default:"text/javascript"}" src="{$_js.source}"></script>
    {/foreach}
{/if}
{if isset($javascript_footer_function)}
    <script type="text/javascript">
        {$javascript_footer_function}
    </script>
{/if}

{* do not remove this hidden div *}
<div id="jr_temp_work_div" style="display:none"></div>


{if !jrUser_is_logged_in()}
<script type="text/javascript">
    $(document).ready(function() {ldelim}
        $('.index_form #user_email_or_url').prop('placeholder', '{jrCore_lang skin="jrElastic2" id=73 default="user name or email"}');
        $('.index_form #user_password').prop('placeholder', '{jrCore_lang skin="jrElastic2" id=74 default="password"}');
        $('.clearfix .user_remember_element_right').append('{jrCore_lang skin="jrElastic2" id=75 default="remember me"}');
        {rdelim});
</script>
{/if}


</body>
</html>
