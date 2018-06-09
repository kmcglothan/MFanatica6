{if  strlen($page_template) == 0}
</div>
{/if}

<div class="footer">
    <div class="row">
        <div class="col12">
            <div class="social">
                <h2>{jrCore_lang id=33 skin="jrISkin" default="Visit Us on Social Media"}</h2>
                <ul class="social clearfix">
                    {if strlen($_conf.jrISkin_facebook_url) > 1}
                        {if $_conf.jrISkin_facebook_url|strpos:"https:" !== false}
                            <li><a href="{$_conf.jrISkin_facebook_url}" class="social-facebook" target="_blank"></a></li>
                        {else}
                            <li><a href="https://facebook.com/{$_conf.jrISkin_facebook_url}" class="social-facebook" target="_blank"></a></li>
                        {/if}
                    {/if}
                    {if strlen($_conf.jrISkin_twitter_url) > 1}
                        {if $_conf.jrISkin_twitter_url|strpos:"https:" !== false}
                            <li><a href="{$_conf.jrISkin_twitter_url}" class="social-twitter" target="_blank"></a></li>
                        {else}
                            <li><a href="https://twitter.com/{$_conf.jrISkin_twitter_url}" class="social-twitter" target="_blank"></a></li>
                        {/if}
                    {/if}
                    {if strlen($_conf.jrISkin_google_url) > 1}
                        {if $_conf.jrISkin_google_url|strpos:"https:" !== false}
                            <li><a href="{$_conf.jrISkin_google_url}" class="social-google" target="_blank"></a></li>
                        {else}
                            <li><a href="https://plus.google.com/u/0/{$_conf.jrISkin_google_url}" class="social-google" target="_blank"></a></li>
                        {/if}
                    {/if}
                    {if strlen($_conf.jrISkin_linkedin_url) > 1}
                        {if $_conf.jrISkin_linkedin_url|strpos:"https:" !== false}
                            <li><a href="{$_conf.jrISkin_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                        {else}
                            <li><a href="https://linkedin.com/in/{$_conf.jrISkin_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                        {/if}
                    {/if}
                    {if strlen($_conf.jrISkin_youtube_url) > 1}
                        {if $_conf.jrISkin_youtube_url|strpos:"https:" !== false}
                            <li><a href="{$_conf.jrISkin_youtube_url}" class="social-youtube" target="_blank"></a></li>
                        {else}
                            <li><a href="https://www.youtube.com/channel/{$_conf.jrISkin_youtube_url}" class="social-youtube" target="_blank"></a></li>
                        {/if}
                    {/if}
                </ul>
                <div><span>&copy; {$_conf.jrCore_system_name} {$smarty.now|jrCore_date_format:"%m/%d/%Y"}</span></div>
            </div>
        </div>
    </div>
</div>


</div>
</div>


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

<script src="{$jamroom_url}/skins/jrISkin/js/css3-animate-it.js"></script>

</body>
</html>