{if  strlen($page_template) == 0}
</div>
{/if}
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                {* Logo *}
                <div class="col4">
                    <div class="table">
                        <div class="table-row">
                            <div class="table-cell">
                                <ul class="social clearfix">
                                    {if strlen($_conf.kmSuperFans_facebook_url) > 0 && $_conf.kmSuperFans_facebook_url != "0"}
                                        <li><a href="{$_conf.kmSuperFans_facebook_url}" class="social-facebook" target="_blank"></a></li>
                                    {/if}
                                    {if strlen($_conf.kmSuperFans_twitter_url) > 0 && $_conf.kmSuperFans_twitter_url != "0"}
                                        <li><a href="{$_conf.kmSuperFans_twitter_url}" class="social-twitter" target="_blank"></a></li>
                                    {/if}
                                    {if strlen($_conf.kmSuperFans_google_url) > 0 && $_conf.kmSuperFans_google_url != "0"}
                                        <li><a href="{$_conf.kmSuperFans_google_url}" class="social-google" target="_blank"></a></li>
                                    {/if}
                                    {if strlen($_conf.kmSuperFans_linkedin_url) > 0 && $_conf.kmSuperFans_linkedin_url != "0"}
                                        <li><a href="{$_conf.kmSuperFans_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                                    {/if}
                                    {if strlen($_conf.kmSuperFans_youtube_url) > 0 && $_conf.kmSuperFans_youtube_url != "0"}
                                        <li><a href="{$_conf.kmSuperFans_youtube_url}" class="social-youtube" target="_blank"></a></li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col4">
                    <div align="center"><a href='http://www.musicrewards.net' target="_blank">{jrCore_image image="rewardsnetwork2.png" alt="rewardsnetwork" width="170" height="90"}</a>
                    </div>
                </div>
                {* Text *}
                <div class="col4 last">
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

</div>

<a href="#" id="scrollup" class="scrollup">{jrCore_icon icon="arrow-up"}</a>


{* Slidebar Mobile Menu *}
<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            var ms = new $.slidebars();
            $('li#menu_button > a').on('click', function() {
                ms.slidebars.open('left');
            });
        });
    }) (jQuery);
</script>

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


<script src="{$jamroom_url}/skins/kmSuperFans/js/css3-animate-it.js"></script>

</body>
</html>
