{assign var="menu_active_style" value="color:#F7DD4F;"}
<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">

            {if $_conf.jrCore_maintenance_mode != 'on' || jrUser_is_master() || jrUser_is_admin()}
                {* Site Builder menu entries *}
                {if jrCore_module_is_active('jrSiteBuilder')}

                    {jrSiteBuilder_default_menu title="Home" url="" weight="1"}
                    {jrSiteBuilder_default_menu title="Charts" url="song_chart" weight="2"}
                    {jrSiteBuilder_default_menu title="Artists" url="artists" weight="3"}
                    {jrSiteBuilder_default_menu title="Songs" url="songs" weight="4"}
                    {jrSiteBuilder_default_menu title="SoundCloud" url="soundcloud" weight="5"}
                    {jrSiteBuilder_default_menu title="Videos" url="videos" weight="6"}
                    {jrSiteBuilder_default_menu title="YouTube" url="you_tube" weight="7"}
                    {jrSiteBuilder_default_menu title="Vimeo" url="vimeo_videos" weight="8"}
                    {jrSiteBuilder_default_menu title="Concerts" url="concerts" weight="9"}
                    {jrSiteBuilder_default_menu title="Members" url="members" weight="10"}
                    {jrSiteBuilder_default_menu title="Blogs" url="blogs" weight="11"}

                    {jrSiteBuilder_menu}

                {else}

                    <li><a href="{$jamroom_url}"{if isset($selected) && $selected == 'home'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="1" default="home"}</a></li>
                    {if jrCore_module_is_active('jrCharts')}
                        <li><a href="{$jamroom_url}/song_chart"{if isset($selected) && $selected == 'charts'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="27" default="charts"}</a></li>
                    {/if}
                    <li><a href="{$jamroom_url}/artists"{if isset($selected) && $selected == 'artists'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="12" default="artists"}</a></li>
                    {if jrCore_module_is_active('jrAudio')}
                        <li><a href="{$jamroom_url}/songs"{if isset($selected) && $selected == 'songs'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="13" default="songs"}</a></li>
                    {/if}
                    {if jrCore_module_is_active('jrSoundCloud')}
                        <li><a href="{$jamroom_url}/sound_cloud"{if isset($selected) && $selected == 'soundcloud'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="61" default="SoundCloud"}</a></li>
                    {/if}
                    {if jrCore_module_is_active('jrVideo')}
                        <li><a href="{$jamroom_url}/videos"{if isset($selected) && $selected == 'videos'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="14" default="videos"}</a></li>
                    {/if}
                    {if jrCore_module_is_active('jrYouTube')}
                        <li><a href="{$jamroom_url}/you_tube"{if isset($selected) && $selected == 'youtube'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" default="YouTube"}</a></li>
                    {/if}
                    {if jrCore_module_is_active('jrVimeo')}
                        <li><a href="{$jamroom_url}/vimeo_videos"{if isset($selected) && $selected == 'vimeo'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="63" default="Vimeo"}</a></li>
                    {/if}
                    <li><a href="{$jamroom_url}/concerts"{if isset($selected) && $selected == 'events'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="30" default="concerts"}</a></li>
                    {if isset($_conf.jrNovaLight_member_quota) && $_conf.jrNovaLight_member_quota > 0}
                        <li><a href="{$jamroom_url}/members"{if isset($selected) && $selected == 'members'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="Members"}</a></li>
                    {/if}
                    {if jrCore_module_is_active('jrBlog')}
                        <li><a href="{$jamroom_url}/blogs"{if isset($selected) && $selected == 'blogs'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="93" default="Blogs"}</a></li>
                    {/if}
                    {if jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrChat_allowed') == 'on'}
                        <li><a href="{$jamroom_url}/chat/mobile" target="_blank">User Chat</a></li>
                    {/if}

                    {* Add additional menu categories here *}

                {/if}

                {* Add in Cart link if jrFoxyCart module is installed *}
                {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                    <li>
                        <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_lang  skin=$_conf.jrCore_active_skin id="40" default="cart"}</a>
                        <span id="fc_minicart"><span id="fc_quantity"></span></span>
                    </li>
                {/if}

                {if jrCore_module_is_active('jrSearch')}
                    <li id="search_link"><a href="{$jamroom_url}/site-search" title="Site Search"><span class="capital">{jrCore_lang  skin=$_conf.jrCore_active_skin id="24" default="search"}</span></a></li>
                {/if}
            {/if}

            {if jrUser_is_logged_in()}

                {if jrUser_is_master()}
                    {jrCore_module_url module="jrCore" assign="core_url"}
                    {jrCore_get_module_index module="jrCore" assign="url"}
                    {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes'}
                        {assign var="acp_menu_item" value="no"}
                    {else}
                        {assign var="acp_menu_item" value="yes"}
                    {/if}
                    <li>
                        <a href="{$jamroom_url}/{$core_url}/admin/global"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="26" default="ACP"}</a>
                        <ul>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang  skin=$_conf.jrCore_active_skin id="65" default="System Tools"}</a>
                                <ul>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang  skin=$_conf.jrCore_active_skin id="78" default="Activity Logs"}</a>
                                        <ul>
                                            <li><a href="{$jamroom_url}/{$core_url}/debug_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="107" default="Debug Logs"}</a></li>
                                            <li><a href="{$jamroom_url}/{$core_url}/php_error_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="108" default="PHP Error Logs"}</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang  skin=$_conf.jrCore_active_skin id="79" default="Reset Cache"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang  skin=$_conf.jrCore_active_skin id="92" default="Reset Image Cache"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang  skin=$_conf.jrCore_active_skin id="80" default="Integrity Check"}</a></li>
                                    <li><a href="{$jamroom_url}/marketplace/system_update">{jrCore_lang skin=$_conf.jrCore_active_skin id="106" default="System Updates"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang  skin=$_conf.jrCore_active_skin id="84" default="System Check"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrBanned"}/browse">{jrCore_lang  skin=$_conf.jrCore_active_skin id="81" default="Banned Items"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrSitemap"}/admin/tools">{jrCore_lang  skin=$_conf.jrCore_active_skin id="83" default="Create Sitemap"}</a></li>
                                </ul>
                            </li>
                            <li>
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_module_url module="jrUser" assign="uurl"}
                                <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang  skin=$_conf.jrCore_active_skin id="101" default="Users"}</a>
                                <ul>
                                    <li style="padding-left: 20px;padding-right: 20px;"><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang  skin=$_conf.jrCore_active_skin id="96" default="Quota Browser"}</a></li>
                                    <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang  skin=$_conf.jrCore_active_skin id="99" default="Profile Browser"}</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang  skin=$_conf.jrCore_active_skin id="97" default="User Accounts"}</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang  skin=$_conf.jrCore_active_skin id="100" default="Who's Online"}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang  skin=$_conf.jrCore_active_skin id="102" default="Skin Settings"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="110" default="Skin Style"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="111" default="Skin Images"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin={$_conf.jrCore_active_skin}">&nbsp;&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="Skin Language"}&nbsp;&nbsp;</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="112" default="Skin Templates"}</a></li>
                                    <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin=$_conf.jrCore_active_skin id="109" default="Skin Notes"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang  skin=$_conf.jrCore_active_skin id="82" default="Skin Menu Editor"}</a></li>
                                </ul>
                            </li>
                            <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang  skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
                        </ul>
                    </li>
                {elseif jrUser_is_admin()}
                    {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes'}
                        {assign var="acp_menu_item" value="no"}
                    {else}
                        {assign var="acp_menu_item" value="yes"}
                    {/if}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} style="{$menu_active_style}"{/if}>{jrCore_lang  skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
                {/if}

            {else}

                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup"><span class="capital">{jrCore_lang  skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="3" default="account"}</span></a></li>
                {/if}
                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/login"><span class="capital">{jrCore_lang  skin=$_conf.jrCore_active_skin id="6" default="login"}</span></a></li>

            {/if}

            {if jrUser_is_logged_in()}
                {if isset($from_profile) && $from_profile == 'yes' && $_post.option != 'forum' && $_post.option != 'documentation' || ($_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes')}
                    {assign var="artist_menu_item" value="yes"}
                {else}
                    {assign var="artist_menu_item" value="no"}
                {/if}
                <li>
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}"{if isset($artist_menu_item) && $artist_menu_item == 'yes'} style="{$menu_active_style}"{/if}>{jrUser_home_profile_key key="profile_name"}</a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>
            {/if}

        </ul>
    </nav>
</div>

<div id="sb-site">