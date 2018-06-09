{*

TEMPLATE SECTIONS

1. HEADER LOGO
2. HEADER AD
3. MAIN MENU
4. MODAL SEARCH FORM
5. HEADER SPOTLIGHT

 *}
{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

<div id="wrapper">
    <div id="content">

        {* HEADER LOGO/HEADER AD/MAIN MENU  *}
        <div class="outer mb8 mt10">
            <div class="head_inner logo">
                <div class="container">

                    {* HEADER LOGO AND AD *}
                    <div class="row">

                        {* HEADER LOGO *}
                        <div class="{if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}col5{else}col12 last{/if}">
                            <div id="main_logo">
                            {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
                                {jrCore_image id="mmt" skin="jrNovaLight" image="menu.png" alt="menu" style="max-width:28px;max-height:28px;"}
                                {jrCore_image image="logo.png" width="225" height="52" class="img_scale" alt=$_conf.jrCore_system_name style="max-width:225px;max-height:52px;cursor:pointer;" custom="logo"}
                            {else}
                                <a href="{$jamroom_url}">{jrCore_image image="logo.png" width="325" height="75" class="img_scale" alt=$_conf.jrCore_system_name style="max-width:325px;max-height:75px;" custom="logo"}</a>
                            {/if}
                            </div>
                        </div>

                        {* HEADER AD *}
                        {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
                        <div class="col7 last">
                            {if $_conf.jrNovaLight_ads_off != 'on'}
                            <div class="top_ad">
                                {if isset($_conf.jrNovaLight_google_ads) && $_conf.jrNovaLight_google_ads == 'on'}
                                    <script type="text/javascript"><!--
                                        google_ad_client = "{$_conf.jrNovaLight_google_id}";
                                        google_ad_width = 468;
                                        google_ad_height = 60;
                                        google_ad_format = "468x60_as";
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
                                {elseif isset($_conf.jrNovaLight_top_ad) && strlen($_conf.jrNovaLight_top_ad) > 0}
                                    {$_conf.jrNovaLight_top_ad}
                                {else}
                                    <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" width="468" height="60" alt="486x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                                {/if}
                            </div>
                            {/if}
                        </div>
                        {/if}

                    </div>

                </div>
            </div>

            {* MAIN MENU *}
            {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
                <div class="menu_inner">
                    {jrCore_include template="header_menu_desktop.tpl"}
                </div>
            {else}
                <div class="menu_inner" style="height: auto;text-align: center;">
                    {if $_conf.jrNovaLight_ads_off != 'on'}
                        {if isset($_conf.jrNovaLight_google_ads) && $_conf.jrNovaLight_google_ads == 'on'}
                            <script type="text/javascript"><!--
                                google_ad_client = "{$_conf.jrNovaLight_google_id}";
                                google_ad_width = 468;
                                google_ad_height = 60;
                                google_ad_format = "468x60_as";
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
                        {elseif isset($_conf.jrNovaLight_top_ad) && strlen($_conf.jrNovaLight_top_ad) > 0}
                            {$_conf.jrNovaLight_top_ad}
                        {else}
                            <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" width="468" height="60" alt="486x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                        {/if}
                    {/if}
                </div>
            {/if}

        </div>

    {* SET SPOTLIGHT MODULE *}
    {if isset($selected) && $selected == 'home'}

        {assign var="spot_module" value="jrProfile"}
        {assign var="spot_require_image" value="profile_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_index_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_spotlight_ids}

    {elseif isset($selected) && $selected == 'charts'}

        {if isset($tab_selected) && $tab_selected == 'v_charts'}

            {assign var="spot_module" value="jrVideo"}
            {assign var="spot_require_image" value="video_image"}
            {assign var="spot_onoff" value=$_conf.jrNovaLight_video_chart_spotlight}
            {assign var="spot_ids" value=$_conf.jrNovaLight_video_chart_spotlight_ids}

        {else}

            {assign var="spot_module" value="jrAudio"}
            {assign var="spot_require_image" value="audio_image"}
            {assign var="spot_onoff" value=$_conf.jrNovaLight_audio_chart_spotlight}
            {assign var="spot_ids" value=$_conf.jrNovaLight_audio_chart_spotlight_ids}

        {/if}

    {elseif isset($selected) && $selected == 'artists'}

        {assign var="spot_module" value="jrProfile"}
        {assign var="spot_require_image" value="profile_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_artists_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_artists_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'songs'}

        {assign var="spot_module" value="jrAudio"}
        {assign var="spot_require_image" value="audio_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_songs_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_songs_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'soundcloud'}

        {assign var="spot_module" value="jrSoundCloud"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_soundcloud_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_soundcloud_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'videos'}

        {assign var="spot_module" value="jrVideo"}
        {assign var="spot_require_image" value="video_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_video_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_video_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'youtube'}

        {assign var="spot_module" value="jrYouTube"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_youtube_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_youtube_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'vimeo'}

        {assign var="spot_module" value="jrVimeo"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_vimeo_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_vimeo_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'events'}

        {assign var="spot_module" value="jrEvent"}
        {assign var="spot_require_image" value="event_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_event_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_event_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'members'}

        {assign var="spot_module" value="jrProfile"}
        {assign var="spot_require_image" value="profile_image"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_members_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_members_page_spotlight_ids}

    {elseif isset($selected) && $selected == 'blogs'}

        {assign var="spot_module" value="jrBlog"}
        {assign var="spot_onoff" value=$_conf.jrNovaLight_blog_page_spotlight}
        {assign var="spot_ids" value=$_conf.jrNovaLight_blog_page_spotlight_ids}

    {/if}

    {* SET SPOTLIGHT ORDER_BY *}
    {if (isset($selected)) && ($selected == 'artists' || $selected == 'home' || $selected == 'members')}

        {assign var="spot_order_by" value="_profile_id random"}

    {else}

        {assign var="spot_order_by" value="_item_id random"}

    {/if}

    {* SET SPOTLIGHT QUOTA ID'S *}
    {if isset($selected) && $selected == 'blog'}

        {assign var="spot_quota_id" value="`$_conf.jrNovaLight_artist_quota`,`$_conf.jrNovaLight_member_quota`"}

    {elseif isset($selected) && $selected == 'members'}

        {assign var="spot_quota_id" value=$_conf.jrNovaLight_member_quota}

    {else}

        {assign var="spot_quota_id" value=$_conf.jrNovaLight_artist_quota}

    {/if}

    {* HEADER SPOTLIGHT *}
    {if isset($spot_onoff) && $spot_onoff == 'on'}

        <div class="outer mb8">
            <div class="inner" style="padding-left:0;">
                {if isset($spot_ids) && strlen($spot_ids) > 0}
                    {if (isset($selected)) && ($selected == 'artists' || $selected == 'home' || $selected == 'members')}
                        {jrCore_list module=$spot_module limit="4" search1="_profile_id in `$spot_ids`" search2="profile_active = 1" template="spotlight_row.tpl"}
                    {else}
                        {jrCore_list module=$spot_module limit="4" search1="_item_id in `$spot_ids`" search2="profile_active = 1" template="spotlight_row.tpl"}
                    {/if}
                {else}
                    {if (isset($spot_require_image) && strlen($spot_require_image) > 0) && (isset($_conf.jrNovaLight_require_images) && $_conf.jrNovaLight_require_images == 'on')}
                        {jrCore_list module=$spot_module order_by=$spot_order_by search1="profile_active = 1" quota_id=$spot_quota_id limit="4" template="spotlight_row.tpl" require_image=$spot_require_image}
                    {else}
                        {jrCore_list module=$spot_module order_by=$spot_order_by quota_id=$spot_quota_id limit="4" template="spotlight_row.tpl"}
                    {/if}
                 {/if}
            </div>
        </div>

    {/if}

        <div class="outer mb8">
        {if !isset($no_inner_div)}
        <div class="inner">
        {/if}
            <!-- end header.tpl -->
