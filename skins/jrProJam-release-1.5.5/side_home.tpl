{* STATS | ONLINE *}
<table class="menu_tab">
    <tr>
        <td>
            <div id="default" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/stats');jrSetActive('#default');">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
        </td>
        <td class="spacer">&nbsp;</td>
        <td>
            <div id="rss_feeds" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/site_feeds');jrSetActive('#rss_feeds');">{jrCore_lang skin=$_conf.jrCore_active_skin id="134" default="Feeds"}</div>
        </td>
        <td class="spacer">&nbsp;</td>
        <td>
            <div id="online" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/online');jrSetActive('#online');">{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="online"}</div>
        </td>
    </tr>
</table>
<div id="stats" class="body_3 mb20">
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

{* TODAY'S FEATURED ARTIST *}
<div id="head-180-big">
{if jrUser_is_master()}
    <div class="block_config" style="padding-top:8px;padding-right:8px;">
        <a onclick="window.location='{$jamroom_url}/core/skin_admin/global/skin={$_conf.jrCore_active_skin}'">{jrCore_icon icon="gear"}</a>
    </div>
{/if}
    <div class="spacer10"></div>
    <i>{jrCore_lang skin=$_conf.jrCore_active_skin id="119" default="Today's"}</i><br><span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="110" default="Artist"}</span>
</div>
<div class="body_3 mb20">
    {* ROW TEMPLATE *}
    {capture name="row_template" assign="featured_today"}
        {literal}
            {if isset($_items)}
            {foreach from=$_items item="row"}
                <div class="center p5">
                    <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                    <div class="spacer10"></div>
                    <a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}"><span class="capital bold">{if strlen($row.profile_name) > 20}{$row.profile_name|truncate:20:"...":false}{else}{$row.profile_name}{/if}</span></a><br>
                    <div class="spacer10"></div>
                    <div class="right"><a href="{$jamroom_url}/{$row.profile_url}" title="View {$row.profile_name}"><div class="button-more">&nbsp;</div></a></div>
                </div>
            {/foreach}
            {/if}
        {/literal}
    {/capture}
    {* FEATURED LIST FUNCTION *}
    {if isset($_conf.jrProJamLight_todays_featured) && $_conf.jrProJamLight_todays_featured > 0}
        {jrCore_list module="jrProfile" limit="1" search1="_profile_id = `$_conf.jrProJamLight_todays_featured`" template=$featured_today}
    {else}
        {if isset($_conf.jrProJamLight_require_images) && $_conf.jrProJamLight_require_images == 'on'}
            {if isset($_conf.jrProJamLight_artist_quota) && $_conf.jrProJamLight_artist_quota > 0}
                {jrCore_list module="jrProfile" order_by="_created random" limit="1" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJamLight_artist_quota`" template=$featured_today require_image="profile_image"}
            {else}
                {jrCore_list module="jrProfile" order_by="_created random" limit="1" search1="profile_active = 1" template=$featured_today require_image="profile_image"}
            {/if}
        {else}
            {if isset($_conf.jrProJamLight_artist_quota) && $_conf.jrProJamLight_artist_quota > 0}
                {jrCore_list module="jrProfile" order_by="_created random" limit="1" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJamLight_artist_quota`" template=$featured_today}
            {else}
                {jrCore_list module="jrProfile" order_by="_created random" limit="1" search1="profile_active = 1" template=$featured_today}
            {/if}
        {/if}
    {/if}
</div>

{* LATEST COMMENTS *}
{if jrCore_module_is_active('jrComment')}
    {jrCore_list module="jrComment" order_by="_created NUMERICAL_DESC" limit="10" template="side_comments.tpl" assign="SIDE_COMMENTS"}
{/if}
{if isset($SIDE_COMMENTS) && strlen($SIDE_COMMENTS) > 0}
    {$SIDE_COMMENTS}
    {elseif jrCore_module_is_active('jrFeed')}
    {if jrCore_module_is_active('jrDisqus')}
        {jrFeed_list name="all disqus site comments" tpl_dir="skin" skin=$_conf.jrCore_active_skin template="rss_list.tpl"}
    {else}
        {jrFeed_list name="jamroom facebook page" tpl_dir="skin" skin=$_conf.jrCore_active_skin template="rss_list.tpl"}
    {/if}
{/if}

{* HOUSE STATION *}
{if isset($selected) && ($selected == 'music' || $selected == 'galleries' || $selected == 'home')}
{if isset($_conf.jrProJam_show_radio) && $_conf.jrProJam_show_radio == 'on'}
    {if isset($_conf.jrProJam_radio_title) && strlen($_conf.jrProJam_radio_title) > 0}
        {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrProJam_radio_title`" limit="1" template="index_radio.tpl"}
    {else}
        {if jrUser_is_logged_in()}
            {if jrUser_is_master()}
                <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="43" default="radio"}</h3>
                <div class="body_2 normal p20 mb20">
                    Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrProJam">Settings</a> <b>"Radio Title"</b> is not set!
                </div>
            {/if}
        {/if}
    {/if}
{/if}
{/if}

{if isset($selected) && ($selected == 'videos' || $selected == 'galleries' || $selected == 'home')}
{* HOUSE CHANNEL *}
{if isset($_conf.jrProJam_show_tv) && $_conf.jrProJam_show_tv == 'on'}
    {if isset($_conf.jrProJam_tv_title) && strlen($_conf.jrProJam_tv_title) > 0}
        {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrProJam_tv_title`" tpl_dir="jrProJam" template="index_channel.tpl"}
    {else}
        {if jrUser_is_logged_in()}
            {if jrUser_is_master()}
                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="44" default="channel"}</div>
                <div class="body_3 mb20">
                    Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrProJam">Settings</a> <b>"Channel Title"</b> is not set!
                </div>
            {/if}
        {/if}
    {/if}
{/if}
{/if}

{if jrCore_module_is_active('jrCharts')}
<a id="t10charts" name="t10charts"></a>
{* TOP 10 CHARTS *}
{jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="365" quota_id=$_conf.jrProJam_artist_quota template="side_charts_row.tpl" pagebreak="10" page=$_post.p assign="TOP_TEN_CHARTS"}
{if isset($TOP_TEN_CHARTS) && strlen($TOP_TEN_CHARTS) > 0}
<h3>Top 10 Charts</h3><br>
<br>
<div id="side_charts" style="height:560px;">
    {$TOP_TEN_CHARTS}
</div>
<div class="clear"> </div>
{/if}
{/if}

{* OUR SPONSORS *}
{if $_conf.jrProJam_ads_off != 'on'}
<div class="body_2 center mt20 mb20">
    {if isset($_conf.jrProJam_google_ads) && $_conf.jrProJam_google_ads == 'on'}
        <script type="text/javascript"><!--
        google_ad_client = "{$_conf.jrProJam_google_id}";
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

    {elseif isset($_conf.jrProJam_side_ad) && strlen($_conf.jrProJam_side_ad) > 0}
        {$_conf.jrProJam_side_ad}
    {else}
        <a href="https://www.jamroom.net" target="_blank">{jrCore_image image="180x150_banner.png" width="180" height="150" alt="180x150 Ad" title="Get Jamroom5!"}</a>
    {/if}
    <br><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="35" default="Advertisment"}</span>
</div>
{/if}

{* site wide tag cloud*}
{jrTags_cloud height="300" assign="tag_cloud"}
{if strlen($tag_cloud) > 0}
    <div class="title">Tag Cloud</div>
    <div class="block_content">
        <div class="item">
            {$tag_cloud}
        </div>
    </div>
{/if}
