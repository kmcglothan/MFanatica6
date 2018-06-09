{* SITEWIDE TAG CLOUD *}
{jrTags_cloud height="300" assign="tag_cloud"}
{if strlen($tag_cloud) > 0}
    <div class="title">Tag Cloud</div>
    <div class="body_3 center mb10">
        {$tag_cloud}
    </div>
{/if}

{* SITE RADIO *}
{if isset($_conf.jrFlashback_show_radio) && $_conf.jrFlashback_show_radio == 'on'}
    {if isset($_conf.jrFlashback_radio_title) && strlen($_conf.jrFlashback_radio_title) > 0}
        {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrFlashback_radio_title`" limit="1" template="index_radio.tpl"}
    {else}
        {if jrUser_is_master()}
            <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="43" default="radio"}</div>
            <div class="body_2 normal p20">
                Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrFlashback">Settings</a> <b>"Radio Title"</b> is not set!
            </div>
        {/if}
    {/if}
{/if}

{* WHO IS ONLINE *}
<div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="45" default="whos online"}</div>
<div class="body_3 mb10">
    {jrUser_whos_online template="whos_online.tpl" assign="WHOS_ONLINE"}
    {if isset($WHOS_ONLINE) && strlen($WHOS_ONLINE) > 0}
        {$WHOS_ONLINE}
    {else}
        <div style="text-align:center;">
            <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="128" default="Sorry, No Users Online!"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="129" default="You Can Help Make This Site"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="130" default="Active By Logging In!"}</h4><br>
            <br>
            <input type="button" class="form_button" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="6" default="login"}" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/login');"><br>
            <br>
            {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="131" default="OR"}</h4><br>
                <br>
                <input type="button" class="form_button" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup');" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="3" default="account"}"><br>
                <br>
            {/if}
        </div>
    {/if}
</div>

{* SITE CHANNEL *}
{if isset($_conf.jrFlashback_show_tv) && $_conf.jrFlashback_show_tv == 'on'}
    {if isset($_conf.jrFlashback_tv_title) && strlen($_conf.jrFlashback_tv_title) > 0}
        {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrFlashback_tv_title`" template="index_channel.tpl"}
    {else}
        {if jrUser_is_master()}
            <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="44" default="channel"}</div>
            <div class="normal p20">
                Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrFlashback">Settings</a> <b>"Radio Title"</b> is not set!
            </div>
        {/if}
    {/if}
{/if}

{* LATEST COMMENTS *}
{if jrCore_module_is_active('jrComment')}
    {jrCore_list module="jrComment" order_by="_created desc" template="side_comments.tpl" assign="SIDE_COMMENTS" limit="10"}
{/if}
{if isset($SIDE_COMMENTS) && strlen($SIDE_COMMENTS) > 0}
    {$SIDE_COMMENTS}
{elseif jrCore_module_is_active('jrFeed')}
    {if jrCore_module_is_active('jrDisqus')}
        {jrFeed_list name="all disqus site comments" template="rss_list.tpl"}
    {else}
        {jrFeed_list name="jamroom facebook page" template="rss_list.tpl"}
    {/if}
{/if}

{* STATS *}
<div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
<div class="body_3 mb10">
    <div style="width:90%;display:table;margin:0 auto;">

        {capture name="template" assign="stats_tpl"}
        {literal}
            {foreach $_stats as $title => $_stat}
            <div style="display:table-row">
                <div class="capital bold" style="display:table-cell">{$title}</div>
                <div class="hilite" style="width:5%;display:table-cell;text-align:right;">{$_stat.count}</div>
            </div>
            {/foreach}
        {/literal}
        {/capture}

        {jrCore_stats template=$stats_tpl}

    </div>
</div>

{* ADVERTISEMENT *}
{if $_conf.jrFlashback_ads_off != 'on'}
    <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="35" default="our sponsors"}</div>
    <div class="body_3 center mb10">
        {if isset($_conf.jrFlashback_google_ads) && $_conf.jrFlashback_google_ads == 'on'}
            <script type="text/javascript"><!--
                google_ad_client = "{$_conf.jrFlashback_google_id}";
                google_ad_width = 180;
                google_ad_height = 150;
                google_ad_format = "180x150_as";
                google_ad_type = "text_image";
                google_ad_channel ="";
                google_color_border = "CCCCCC";
                google_color_bg = "CCCCCC";
                google_color_link = "FF9900";
                google_color_text = "333333";
                google_color_url = "333333";
                //--></script>
            <script type="text/javascript"
                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>

        {elseif isset($_conf.jrFlashback_side_ad) && strlen($_conf.jrFlashback_side_ad) > 0}
            {$_conf.jrFlashback_side_ad}
        {else}
            <a href="https://www.jamroom.net" target="_blank">{jrCore_image image="180x150_banner.png" width="180" height="150" alt="180x150 Ad" title="Get Jamroom5!"}</a>
        {/if}
    </div>
{/if}

