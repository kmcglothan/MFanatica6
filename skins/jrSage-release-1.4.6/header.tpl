{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

<div id="wrapper">

    <div id="header">
        <div id="header_content">

            <div class="container">

                <div class="row">

                    <div class="{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}col12 last{else}col6{/if}">
                    {* Logo *}
                        <div id="main_logo">
                        {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
                            {jrCore_image id="mmt" skin="jrSage" image="menu.png" alt="menu" style="max-width:28px;max-height:28px;"}
                            {jrCore_image image="logo.png" class="jlogo" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:225px;max-height:52px;cursor:pointer;" custom="logo"}
                        {else}
                            <a href="{$jamroom_url}">{jrCore_image image="logo.png" class="jlogo" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:325px;max-height:75px;" custom="logo"}</a>
                        {/if}
                        </div>
                    </div>

                    {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
                    <div class="col6 last">
                        {if $_conf.jrSage_ads_off != 'on'}
                            <div class="logo-ads">
                                {if isset($_conf.jrSage_google_ads) && $_conf.jrSage_google_ads == 'on'}
                                    <script type="text/javascript"><!--
                                        google_ad_client = "{$_conf.jrSage_google_id}";
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
                                {elseif isset($_conf.jrSage_top_ad) && strlen($_conf.jrSage_top_ad) > 0}
                                    {$_conf.jrSage_top_ad}
                                {else}
                                    <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" width="468" height="60" alt="468x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    {/if}

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
        <h2>{$_conf.jrCore_system_name} {$st}</h2><br><br>
        {jrSearch_form class="form_text" value=$st style="width:70%"}
        <div class="clear"></div>
    </div>

    <div id="content">
        <!-- end header.tpl -->

        {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
        {if $_conf.jrFlashback_ads_off != 'on'}
        <div class="container">
            <div class="row">
                <div class="col12 last">
                    <div class="mobile-ads" style="float:none;padding:10px;text-align:center;">
                        {if isset($_conf.jrSage_google_ads) && $_conf.jrSage_google_ads == 'on'}
                            <script type="text/javascript"><!--
                                google_ad_client = "{$_conf.jrSage_google_id}";
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
                        {elseif isset($_conf.jrSage_top_ad) && strlen($_conf.jrSage_top_ad) > 0}
                            {$_conf.jrSage_top_ad}
                        {else}
                            <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" width="468" height="60" alt="468x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        {/if}
        {/if}

        {if isset($_post.module) && $_post.option != 'admin' && ($_post.module == 'jrRecommend' || $_post.module == 'jrSearch')}
        <div class="container">

            <div class="row">

                <div class="col9">
                    <div class="body_1 mr5">

                        <div class="title">Search Results</div>
                            <div class="body_3">
        {/if}
