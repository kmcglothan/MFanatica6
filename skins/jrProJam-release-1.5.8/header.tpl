{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

{* TOP BAR *}
{if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
<div id="top-bar">
    <div class="top-bar-wrapper">
        <div class="container">
            <div class="row">
                <div class="col8">
                    <div class="welcome">

                    {if jrUser_is_logged_in()}

                        {jrCore_lang skin=$_conf.jrCore_active_skin id="102" default="Welcome"}&nbsp;&nbsp;<b>{jrUser_home_profile_key key="profile_name"}</b>&nbsp;|&nbsp;
                        {if jrCore_module_is_active('jrPrivateNote')}
                            {jrCore_module_url module="jrPrivateNote" assign="nurl"}
                            {if isset($_user.user_jrPrivateNote_unread_count) && $_user.user_jrPrivateNote_unread_count > 0}
                                <a href="{$jamroom_url}/{$nurl}/notes" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="103" default="In Box"}</span></a> ({$_user.user_jrPrivateNote_unread_count}) |
                            {else}
                                <a href="{$jamroom_url}/{$nurl}/notes" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="103" default="In Box"}</span></a> |
                            {/if}
                        {/if}
                        <a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/logout" target="_top" onclick="if (!confirm('Are you Sure you want to Log out?')) return false;"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="5" default="Logout"}</span></a> |
                        <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="104" default="Your Home"}</span></a>

                    {else}

                        <b>{jrCore_lang skin=$_conf.jrCore_active_skin id="7" default="Welcome Guest"}!</b> | <a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/login" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="Login"}</span></a>

                    {/if}

                    </div>
                </div>
                <div class="col4 last">
                    <div class="flags">
                        <a href="?set_user_language=es-ES"><img src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/flags/es.png" alt="ES" title="Spanish"></a>
                        <a href="?set_user_language=en-US"><img src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/flags/us.png" alt="US" title="English US"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}

{* LOGO AND AD HEADER *}
<div id="header">

    <div id="header_content">

        <div class="container">

            <div class="row">

                <div class="col6">
                    {* Logo *}
                    <div id="main_logo">
                        {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
                            {jrCore_image id="mmt" skin="jrProJam" image="menu.png" alt="menu" style="max-width:28px;max-height:28px;"}
                            {jrCore_image image="logo.png" class="img_scale" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:225px;max-height:48px;" custom="logo"}
                        {else}
                            <a href="{$jamroom_url}">{jrCore_image image="logo.png" class="img_scale" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:375px;max-height:80px;" custom="logo"}</a>
                        {/if}
                    </div>
                </div>
                <div class="col6 last">
                    {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
                    <div class="logo-ads">
                        {if $_conf.jrProJam_ads_off != 'on'}
                            {if isset($_conf.jrProJam_google_ads) && $_conf.jrProJam_google_ads == 'on'}
                                <script type="text/javascript"><!--
                                    google_ad_client = "{$_conf.jrProJam_google_id}";
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
                            {elseif isset($_conf.jrProJam_top_ad) && strlen($_conf.jrProJam_top_ad) > 0}
                                {$_conf.jrProJam_top_ad}
                            {else}
                                <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" alt="468x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                            {/if}
                        {/if}
                    </div>
                    {/if}
                </div>

            </div>

        </div>

    </div>

</div>

{* MAIN MENU *}
{if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
    {jrCore_include template="header_menu_desktop.tpl"}
{/if}

{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box" style="display:none;">
    <div class="float-right ml10"><input type="button" class="simplemodal-close form_button" value="x"></div>

    {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
    <span class="media_title">{$_conf.jrCore_system_name} {$st}</span><br><br>
    {jrSearch_form class="form_text" value=$st style="width:70%"}
    <div class="clear"></div>
</div>

<div id="wrapper">

    {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
        <div class="logo-ads" style="float:none;padding:10px;text-align:center;">
            {if $_conf.jrProJam_ads_off != 'on'}
                {if isset($_conf.jrProJam_google_ads) && $_conf.jrProJam_google_ads == 'on'}
                    <script type="text/javascript"><!--
                        google_ad_client = "{$_conf.jrProJam_google_id}";
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
                {elseif isset($_conf.jrProJam_top_ad) && strlen($_conf.jrProJam_top_ad) > 0}
                    {$_conf.jrProJam_top_ad}
                {else}
                    <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" alt="468x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                {/if}
            {/if}
        </div>
    {/if}

    {* FEATURED ARTIST SLIDER *}
    {if isset($selected) && $selected == 'home'}
        <script>
            $(function () {

                // Slideshow 1
                $("#slider1").responsiveSlides({
                    auto: true,          // Boolean: Animate automatically, true or false
                    speed: 400,          // Integer: Speed of the transition, in milliseconds
                    timeout: 8000,       // Integer: Time between slide transitions, in milliseconds
                    pager: true,         // Boolean: Show pager, true or false
                    random: true,        // Boolean: Randomize the order of the slides, true or false
                    pause: true,         // Boolean: Pause on hover, true or false
                    maxwidth: 460,       // Integer: Max-width of the slideshow, in pixels
                    namespace: "rslides" // String: change the default namespace used
                 });

             });
        </script>
    <div id="fadeout-carousel" class="button-toggle"></div>
    <div class="toggle-carousel">
    <div class="slider_content">
        <div class="container">
            <div class="row">
                <div class="col5">
                    <div class="swrapper" style="padding-top:10px;">
                        <div class="callbacks_container">
                            <div class="ioutline" style="max-width:400px;margin:0 auto;">
                                <ul id="slider1" class="rslides callbacks" style="max-height:400px;">
                                    {if isset($_conf.jrProJam_profile_ids) && strlen($_conf.jrProJam_profile_ids) > 0}
                                        {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="_profile_id in `$_conf.jrProJam_profile_ids`" search2="profile_active = 1" template="index_featured_slider.tpl"}
                                    {else}
                                        {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
                                            {if isset($_conf.jrProJam_artist_quota) && strlen($_conf.jrProJam_artist_quota) > 0}
                                                {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJam_artist_quota`" template="index_featured_slider.tpl" require_image="profile_image"}
                                            {else}
                                                {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" template="index_featured_slider.tpl" require_image="profile_image"}
                                            {/if}
                                        {else}
                                            {if isset($_conf.jrProJam_artist_quota) && strlen($_conf.jrProJam_artist_quota) > 0}
                                                {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJam_artist_quota`" template="index_featured_slider.tpl"}
                                            {else}
                                                {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" template="index_featured_slider.tpl"}
                                            {/if}
                                        {/if}
                                    {/if}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {* FAVORITE ARTIST AND SONG *}
                <div class="col7 last">
                    <div class="clear fav_body center">
                        <div id="fav-div">
                            {capture name="row_template" assign="fav_artist_row"}
                                {literal}
                                    {if isset($_items)}
                                    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="123" default="Our Favorite Artist"}</h2><br>
                                    <br>
                                    <div class="container">
                                        {foreach from=$_items item="row"}
                                        <div class="row">
                                            <div class="col6">
                                                <div class="center">
                                                    <h2><a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a></h2>
                                                    <br>
                                                    <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="large" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow img_scale" style="max-width:256px;max-height:256px;"}</a><br>
                                                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Songs"}: <span class="highlight-txt bold">{$row.profile_jrAudio_item_count}</span></h4>&nbsp;
                                                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="Views"}: <span class="highlight-txt bold">{$row.profile_view_count}</span></h4>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="col6 last">
                                                <div class="left p5">
                                                    {if strlen($row.profile_bio) > 0}
                                                    <br>
                                                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}</h4>:
                                                    <div class="normal">{$row.profile_bio|truncate:500:"...":false|jrCore_format_string:$row.profile_quota_id}</div>
                                                    {/if}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col12 last">
                                                    <div style="float:right; padding-top:10px; margin-top: 10px;">
                                                        <a href="{$jamroom_url}/{$row.profile_url}" title="More"><div class="button-more">&nbsp;</div></a>
                                                    </div>
                                                    <div class="clear">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                        {/foreach}
                                    </div>
                                    {/if}
                                {/literal}
                            {/capture}

                            {if isset($_conf.jrProJam_favorite_artist) && strlen($_conf.jrProJam_favorite_artist) > 0}
                                {jrCore_list module="jrProfile" limit="1" profile_id=$_conf.jrProJam_favorite_artist template=$fav_artist_row}
                            {else}
                                {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="profile_name random" quota_id=$_conf.jrProJam_artist_quota limit="1" template=$fav_artist_row require_image="profile_image"}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="profile_name random" quota_id=$_conf.jrProJam_artist_quota limit="1" template=$fav_artist_row}
                                {/if}
                            {/if}
                        </div>
                        <div class="fav_buttons">
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="110" default="Artist" assign="fvrt_artst_bttn_ttl"}
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="111" default="Song" assign="fvrt_sng_bttn_ttl"}
                            <a onclick="jrLoad('#fav-div','{$jamroom_url}/favorite_artist');">{jrCore_image image="button_prev.png" alt=$fvrt_artst_bttn_ttl title=$fvrt_artst_bttn_ttl} {jrCore_lang skin=$_conf.jrCore_active_skin id="110" default="Artist"}</a> |
                            <a onclick="jrLoad('#fav-div','{$jamroom_url}/favorite_song');">{jrCore_lang skin=$_conf.jrCore_active_skin id="111" default="Song"} {jrCore_image image="button_next.png" alt=$fvrt_sng_bttn_ttl title=$fvrt_sng_bttn_ttl}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <br>
    </div>
    </div>
    {/if}

    <div id="content" class="mt10">
        <!-- end header.tpl -->

        {* SEARCH PAGE HEADER *}
        {if isset($_post.module) && $_post.option != 'admin' && ($_post.module == 'jrRecommend' || $_post.module == 'jrSearch')}
        <div class="container">

            <div class="row">

                <div class="col3 last">
                    <div class="body_1">
                        {jrCore_include template="side_home.tpl"}
                    </div>
                </div>
                <div class="col9">
                    <div class="body_1 mr5">

                        <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="124" default="Search Results"}</div>
                            <div class="body_3">
        {/if}
