</div>
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                <div class="col12 last">
                    <div id="footer_text">
                        &copy;{$smarty.now|date_format:"%Y"}
                        <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a><br>
                        {* An auto footer that rotates phrases to help jamroom.net.  If you like jamroom, leave this here. We'd appreciate it.  Thanks. *}
                        {jrCore_powered_by}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="pm-drop-opt" style="display:none">
    <li class="hideshow"><a>{jrCore_lang skin="jrElastic2" id=64 default="more"} <span>&#x25BC;</span></a><ul id="submenu"></ul></li>
</div>

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

{if jrCore_is_mobile_device()}

    {* Slidebars *}
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                var ms = new $.slidebars();
                $('#mmt').on('click', function() {
                    ms.slidebars.toggle('left');
                });
            });
        })(jQuery);
    </script>

{else}

    {* Responsive Menu *}
    <script type="text/javascript">
        $(function() {
            $("#menu-trigger").on("click", function() {
                $("#menu").slideToggle();

            });
            var isiPad = navigator.userAgent.match(/iPad/i) != null;
            if (isiPad) $('#menu ul').addClass('no-transition');
        });
    </script>
{/if}

{* setup counter for page view *}
{jrCore_counter module="jrProfile" item_id=$_profile_id name="profile_view"}

</div>
</div>

</body>
</html>

