{if  strlen($page_template) == 0}
</div>
{/if}
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">
            <div class="row">
                <div class="col12">
                    <div class="social">
                        <h2>&copy;{$smarty.now|date_format:"%Y"} <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a></h2>
                        <ul class="social clearfix">
                            {if strlen($_conf.jrVideoPro_facebook_url) > 0 && $_conf.jrVideoPro_facebook_url != "0"}
                                {if $_conf.jrVideoPro_facebook_url|strpos:"https:" !== false}
                                    <li><a href="{$_conf.jrVideoPro_facebook_url}" class="social-facebook" target="_blank"></a></li>
                                {else}
                                    <li><a href="https://facebook.com/{$_conf.jrVideoPro_facebook_url}" class="social-facebook" target="_blank"></a></li>
                                {/if}
                            {/if}
                            {if strlen($_conf.jrVideoPro_twitter_url) > 0 && $_conf.jrVideoPro_twitter_url != "0"}
                                {if $_conf.jrVideoPro_twitter_url|strpos:"https:" !== false}
                                    <li><a href="{$_conf.jrVideoPro_twitter_url}" class="social-twitter" target="_blank"></a></li>
                                {else}
                                    <li><a href="https://twitter.com/{$_conf.jrVideoPro_twitter_url}" class="social-twitter" target="_blank"></a></li>
                                {/if}
                            {/if}
                            {if strlen($_conf.jrVideoPro_google_url) > 0 && $_conf.jrVideoPro_google_url != "0"}
                                {if $_conf.jrVideoPro_google_url|strpos:"https:" !== false}
                                    <li><a href="{$_conf.jrVideoPro_google_url}" class="social-google" target="_blank"></a></li>
                                {else}
                                    <li><a href="https://plus.google.com/u/0/{$_conf.jrVideoPro_google_url}" class="social-google" target="_blank"></a></li>
                                {/if}
                            {/if}
                            {if strlen($_conf.jrVideoPro_linkedin_url) > 0 && $_conf.jrVideoPro_linkedin_url != "0"}
                                {if $_conf.jrVideoPro_linkedin_url|strpos:"https:" !== false}
                                    <li><a href="{$_conf.jrVideoPro_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                                {else}
                                    <li><a href="https://linkedin.com/in/{$_conf.jrVideoPro_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                                {/if}
                            {/if}
                            {if strlen($_conf.jrVideoPro_youtube_url) > 0 && $_conf.jrVideoPro_youtube_url != "0"}
                                {if $_conf.jrVideoPro_youtube_url|strpos:"https:" !== false}
                                    <li><a href="{$_conf.jrVideoPro_youtube_url}" class="social-youtube" target="_blank"></a></li>
                                {else}
                                    <li><a href="https://www.youtube.com/channel/{$_conf.jrVideoPro_youtube_url}" class="social-youtube" target="_blank"></a></li>
                                {/if}
                            {/if}
                        </ul>
                        {* An auto footer that rotates phrases to help jamroom.net.  If you like jamroom, leave this here. We'd appreciate it.  Thanks. *}
                        {jrCore_powered_by}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<a href="#" id="scrollup" class="scrollup">{jrCore_lang skin="jrVideoPro" id=72 default="Scroll Up"}</a>

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

{* Slidebar Mobile Menu *}
<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            var ms = new $.slidebars();
            $('li#menu_button > a').on('click', function() {
                ms.slidebars.toggle('left');
            });
        });
    }) (jQuery);
</script>

<script src="{$jamroom_url}/skins/jrVideoPro/js/css3-animate-it.js"></script>

</body>
</html>
