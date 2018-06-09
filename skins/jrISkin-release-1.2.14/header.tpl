{jrCore_include template="meta.tpl"}
<body>

<div id="header">
    <div class="menu_pad">
        <div id="header_content" style="display: table; width: 100%;">
            <div style="display: table-row">
                <div style="width: 12%; height: 50px; display: table-cell; vertical-align: middle;">
                    <ul>
                        <li class="mobile" id="menu_button"><a href="#menu2"></a></li>
                        <li class="desk"><a href="{$jamroom_url}">{jrCore_image image="logo.png" width="100" height="50" class="jrLogo" alt=$_conf.jrCore_system_name custom="logo"}</a></li>
                    </ul>
                </div>
                <div style="display: table-cell; vertical-align: middle;">
                    {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}

                        {jrCore_include template="menu_main.tpl"}
                    {elseif jrUser_is_logged_in()}
                        <ul id="menu">
                            {jrCore_module_url module="jrAction" assign="tUrl"}
                            <li id="feedback" style="display:inline-block;">
                                <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/{$tUrl}/feedback" title="{jrCore_lang skin="jrISkin" id=128 default="Feedback"}">
                                    {jrCore_icon icon="notifications" size="24" color="999999"}
                                    <span class="count feedback_count">0</span>
                                </a>
                            </li>
                            <li id="mentions" style="display:inline-block;">
                                <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/{$tUrl}/mentions" title="{jrCore_lang skin="jrISkin" id=127 default="Mentions"}">
                                    {jrCore_icon icon="mention" size="24" color="999999"}
                                    <span class="count mentions_count">0</span>
                                </a>
                            </li>
                            <li id="followers" style="display:inline-block;">
                                <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/follow" title="{jrCore_lang skin="jrISkin" id=126 default="Followers"}">
                                    {jrCore_icon icon="followers" size="24" color="999999"}
                                    <span class="count followers_count">0</span>
                                </a>
                            </li>
                        </ul>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{if jrCore_is_mobile_device() && jrCore_is_tablet_device()}
    {jrCore_include template="menu_side.tpl"}
{/if}

{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box {$chameleon_style}" style="display:none;">
    {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
    {jrSearch_form class="form_text" value=$st style="width:70%"}
    <div style="float:right;clear:both;margin-top:3px;">
        <a class="simplemodal-close">{jrCore_icon icon="close" size=20}</a>
    </div>
    <div class="clear"></div>
</div>

<div id="wrapper">

{if  strlen($page_template) == 0}
    <div id="content">
{/if}

<noscript>
    <div class="item error center" style="margin:12px">
        This site requires Javascript to function properly - please enable Javascript in your browser
    </div>
</noscript>

        <!-- end header.tpl -->
