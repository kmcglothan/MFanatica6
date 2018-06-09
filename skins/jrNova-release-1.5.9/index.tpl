{assign var="selected" value="home"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrLoad('#sm',core_system_url +'/index_artists');
        jrLoad('#top_artists',core_system_url +'/top_artists');
        jrLoad('#top_songs',core_system_url +'/top_songs');
        jrSetActive('#default');
    });
</script>
{*

TEMPLATE SECTIONS

1. FEATURED TABS
2. TOP ARTISTS
3. SITE NEWS
4. TOP SONGS
5. USERS ONLINE
6. SITE ARTICLES
7. OUR SPONSORS
8. COMMUNITY RADIO
9. COMMUNITY TV
10. SITE WIDE TAG CLOUD
11. SITE STATS

 *}
<div class="container">

    {* FEATURED TABS *}
    <div class="row">

        <div class="col12 last">

            <div class="menu_tab">
                <div id="default" class="p_choice fartist" onclick="jrLoad('#sm','{$jamroom_url}/index_artists');jrSetActive('#default');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="12" default="artists"}</div>
                <div id="s_song" class="p_choice fsong" onclick="jrLoad('#sm','{$jamroom_url}/index_songs');jrSetActive('#s_song');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="13" default="songs"}</div>
                <div id="s_video" class="p_choice fvideo" onclick="jrLoad('#sm','{$jamroom_url}/index_videos');jrSetActive('#s_video');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="14" default="videos"}</div>
                <div class="clear"></div>
            </div>
            <div class="finner mb8">
                <div id="sm"></div>
            </div>

        </div>

    </div>

    {* TOP ARTISTS/SITE NEWS/TOP SONGS *}
    <div class="row">

        {* TOP ARTISTS *}
        <a id="tartists" name="tartists"></a>
        <div class="col3">
            <div class="inner leader mb8 mr8">
                <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="31" default="top"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="12" default="artists"}</span>
            </div>
            <div class="inner mb8 mr8" style="height:450px;overflow:auto;">
                <div id="top_artists">
                </div>
            </div>
        </div>

        {* SITE NEWS *}
        <div class="col6">
            {if isset($_conf.jrNova_site_news_ids) && $_conf.jrNova_site_news_ids > 0}
                {jrCore_list module="jrBlog" order_by="blog_display_order numerical_asc" search1="blog_category = site news" search2="_profile_id in `$_conf.jrNova_site_news_ids`" limit="2" template="index_site_news.tpl" assign="SITENEWS"}
            {else}
                {jrCore_list module="jrBlog" order_by="blog_display_order numerical_asc" search1="blog_category = site news" search2="_profile_id = 1" limit="2" template="index_site_news.tpl" assign="SITENEWS"}
            {/if}
            {if isset($SITENEWS) && strlen($SITENEWS) > 0}
                {$SITENEWS}
            {else}
                <div class="inner leader blogpost mb8 mr8">
                    <h2 style="text-transform:capitalize;">{jrCore_lang  skin=$_conf.jrCore_active_skin id="8" default="Site"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="9" default="News"}</h2>
                </div>
                <div class="inner mb8 mr8" style="height:450px;overflow:auto;">
                    <div class="item">
                        {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$smarty.now|date_format}</span><br>
                        <div class="p5">
                            <h2>Welcome to Jamroom5!</h2><br>
                            <br>
                        <span class="normal">
                            Jamroom 5 is a powerful flexible PHP Content Management Framework, great for building community based sites and custom apps.<br><br>
                            {if jrUser_is_logged_in()}
                                {if jrUser_is_master()}
                                    <b>Admin Note:</b> Go to the Admin profile blog section to create site news for this section, make sure to set the category to "<b>site news</b>".<br>
                                    <br><br>
                                {/if}
                            {/if}
                            <br><br><br>
                        </span>
                        </div>
                        <hr>
                    </div>
                </div>
            {/if}
        </div>

        {* TOP SONGS *}
        <a id="tsongs" name="tsongs"></a>
        <div class="col3 last">
            <div class="mb8">
                <div class="inner leader mb8">
                    <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="31" default="top"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="13" default="songs"}</span>
                </div>
                <div class="inner mb8" style="height:450px;overflow:auto;">
                    <div id="top_songs">
                    </div>
                </div>
            </div>
        </div>

    </div>

    {* ONLINE/ARTICLES/SPONSORS *}
    <div class="row">

        {* USERS ONLINE *}
        <div class="col3">
            <div class="mb8">
                <div class="inner leader mb8 mr8">
                    <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="38" default="users"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="39" default="online"}</span>
                </div>
                <div class="finner mb8 mr8" style="overflow:auto;">
                    {jrUser_whos_online template="whos_online.tpl" assign="WHOS_ONLINE"}
                    {if isset($WHOS_ONLINE) && strlen($WHOS_ONLINE) > 0}
                        {$WHOS_ONLINE}
                    {else}
                        <div style="text-align:center;">
                            <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Sorry, No Users Online!"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="116" default="You Can Help Make This Site"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="117" default="Active By Logging In!"}</h4><br>
                            <br>
                            <input type="button" class="form_button" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="6" default="login"}" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/login');"><br>
                            <br>
                            {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                                <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="OR"}</h4><br>
                                <br>
                                <input type="button" class="form_button" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup');" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="3" default="account"}"><br>
                                <br>
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        {* SITE ARTICLES *}
        <div class="{if $_conf.jrNova_ads_off != 'on'}col6{else}col9 last{/if}">
            <div class="mb8">
                <div class="inner leader mb8{if $_conf.jrNova_ads_off != 'on'} mr8{/if}">
                    <div class="float-right">
                        <span class="normal"><a href="{$jamroom_url}/articles">{jrCore_lang  skin=$_conf.jrCore_active_skin id="52" default="Archives"}&nbsp;&raquo;</a></span>&nbsp;
                        {if jrUser_is_logged_in()}
                            {if jrUser_is_master()}
                                {capture name="row_template" assign="article_add_template"}
                                {literal}
                                    {if isset($_items)}
                                    {foreach from=$_items item="item"}
                                    <a onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/page');">{jrCore_icon icon="plus" size="24"}</a>
                                    {/foreach}
                                    {/if}
                                {/literal}
                                {/capture}
                                {jrCore_list module="jrProfile" order_by="profile_name asc" limit="1" search1="_profile_id = 1" template=$article_add_template}
                            {/if}
                        {/if}
                    </div>
                    <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="36" default="latest articles"}</span>
                </div>
                <div class="finner mb8{if $_conf.jrNova_ads_off != 'on'} mr8{/if}">
                    {jrCore_list module="jrPage" order_by="_created desc" limit="3" search1="page_location = 0" search2="_profile_id = 1" template="index_content.tpl"}
                </div>
            </div>
        </div>

        {* OUR SPONSORS *}
        {if $_conf.jrNova_ads_off != 'on'}
            <div class="col3 last">
                <div class="inner leader mb8">
                    <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="37" default="our sponsors"}</span>
                </div>
                <div class="finner mb8 center">
                    <div class="p10">
                        {if isset($_conf.jrNova_google_ads) && $_conf.jrNova_google_ads == 'on'}
                            <script type="text/javascript"><!--
                                google_ad_client = "{$_conf.jrNova_google_id}";
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
                        {elseif isset($_conf.jrNova_bottom_ad) && strlen($_conf.jrNova_bottom_ad) > 0}
                            {$_conf.jrNova_bottom_ad}
                        {else}
                            <a href="{$jamroom_url}">{jrCore_image image="180x150_banner.png" width="180" height="150" alt="180x150 Ad" title="Get Jamroom5!"}</a>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}

    </div>

    {* COMMUNITY RADIO AND TV *}
    <div class="row">

        {* COMMUNITY RADIO *}
        <div class="col6">
            {if isset($_conf.jrNova_show_radio) && $_conf.jrNova_show_radio == 'on'}
                <div class="inner mb8 mr8">
                    <div id="cr">
                        {if isset($_conf.jrNova_radio_title) && strlen($_conf.jrNova_radio_title) > 0}
                            {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_title = `$_conf.jrNova_radio_title`" search2="_profile_id = 1" template="index_radio.tpl"}
                        {else}
                            {if jrUser_is_logged_in()}
                                {if jrUser_is_master()}
                                    <div class="media_title p20">
                                        Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrNova"><span style="color:#FFF;text-decoration:underline;">Settings</span></a> <b>"Radio Title"</b> is not set!
                                    </div>
                                {/if}
                            {/if}
                        {/if}
                    </div>
                </div>
            {else}
                &nbsp;
            {/if}
        </div>

        {* COMMUNITY TV *}
        <div class="col6 last">
            {if isset($_conf.jrNova_show_tv) && $_conf.jrNova_show_tv == 'on'}
                <div class="inner mb8">
                    <div id="ctv">
                        {if isset($_conf.jrNova_tv_title) && strlen($_conf.jrNova_tv_title) > 0}
                            {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_title = `$_conf.jrNova_tv_title`" search2="_profile_id = 1" template="index_channel.tpl"}
                        {else}
                            {if jrUser_is_logged_in()}
                                {if jrUser_is_master()}
                                    <div class="media_title p20">
                                        Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrNova"><span style="color:#FFF;text-decoration:underline;">Settings</span></a> <b>"TV Title"</b> is not set!
                                    </div>
                                {/if}
                            {/if}
                        {/if}
                    </div>
                </div>
            {else}
                &nbsp;
            {/if}
        </div>

    </div>

    {* SITE WIDE TAG CLOUD *}
    {jrTags_cloud height="350" assign="tag_cloud"}
    {if strlen($tag_cloud) > 0}
        <div class="row">
            <div class="col12 last">
                <div class="inner mb10">
                    <div class="inner leader">Tag Cloud</div>
                    <div class="item">{$tag_cloud}</div>
                </div>
            </div>
        </div>
    {/if}

    {* SITE STATS *}
    {if isset($_conf.jrNova_show_stats) && $_conf.jrNova_show_stats == 'on'}
        <div class="row">
            <div class="col12 last">
                <div class="inner">
                    <div class="inner leader">
                        <span class="capital">{jrCore_lang  skin=$_conf.jrCore_active_skin id="42" default="stats"}</span>&nbsp;-
                        {capture name="template" assign="stats_tpl"}
                        {literal}
                            {foreach $_stats as $title => $_stat}
                            <span class="media_title">&bull;&nbsp;{$title}:&nbsp;{$_stat.count}</span>
                            {/foreach}
                        {/literal}
                        {/capture}
                        {jrCore_stats template=$stats_tpl}
                    </div>
                </div>
            </div>
        </div>
    {/if}

</div>

{jrCore_include template="footer.tpl"}

