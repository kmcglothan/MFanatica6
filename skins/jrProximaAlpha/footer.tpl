{if $show_main_box == 1}
    </div>
{/if}

</div>
</div>

<div id="footer">
    <div class="container">
        <div class="row">
            <div class="col9">
                <div id="footer_logo">

                    {* Social Network Linkup *}

                    {if strlen($_conf.jrProximaAlpha_twitter_name) > 0}
                        <a href="https://twitter.com/{$_conf.jrProximaAlpha_twitter_name}">{jrCore_image image="sn-twitter.png" width="40" height="40" class="social-img" alt="twitter" title="Follow @{$_conf.jrProximaAlpha_twitter_name}"}</a>
                    {/if}

                    {if strlen($_conf.jrProximaAlpha_facebook_name) > 0}
                        <a href="https://facebook.com/{$_conf.jrProximaAlpha_facebook_name}">{jrCore_image image="sn-facebook.png" width="40" height="40" class="social-img" alt="facebook" title="Like {$_conf.jrProximaAlpha_facebook_name} on Facebook"}</a>
                    {/if}

                    {if strlen($_conf.jrProximaAlpha_linkedin_name) > 0}
                        <a href="https://linkedin.com/{$_conf.jrProximaAlpha_linkedin_name}">{jrCore_image image="sn-linkedin.png" width="40" height="40" class="social-img" alt="linkedin" title="Link up with {$_conf.jrProximaAlpha_linkedin_name} on LinkedIn"}</a>
                    {/if}

                    {if strlen($_conf.jrProximaAlpha_google_name) > 0}
                        <a href="https://google.com/{$_conf.jrProximaAlpha_google_name}">{jrCore_image image="sn-google-plus.png" width="40" height="40" class="social-img" alt="google+" title="Follow {$_conf.jrProximaAlpha_google_name} on Google+"}</a>
                    {/if}

                </div>
            </div>

            <div class="col3 last">
                <div id="footer_text">
                    &copy;{$smarty.now|date_format:"%Y"} <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a><br>
                    <span style="font-size:11px">Powered by</span> <span style="font-size:11px;"><a href="https://www.jamroom.net/proxima"><strong>Proxima</strong></a> and <a href="https://www.jamroom.net"><strong>Jamroom</strong></a></span>
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

{* do not remove this hidden div *}
<div id="jr_temp_work_div" style="display:none"></div>

{* Slidebars *}
{if jrCore_is_mobile_device()}

    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            var ms = new $.slidebars();
            $('#main_logo').on('click', function() {
                ms.toggle('left');
            });
        });
    }) (jQuery);
    </script>

</div>

{else}

    {* Responsive Menu *}
    <script type="text/javascript">
        $(function () {
            /* Mobile */
            $('#menu-wrap').prepend('<div id="menu-trigger">{jrCore_lang skin=$_conf.jrCore_active_skin id="20" default="menu"}</div>');
            $("#menu-trigger").on("click", function () {
                $("#menu").slideToggle();

            });
            // iPad
            var isiPad = navigator.userAgent.match(/iPad/i) != null;
            if (isiPad) $('#menu ul').addClass('no-transition');
        });
    </script>

{/if}

</body>
</html>
