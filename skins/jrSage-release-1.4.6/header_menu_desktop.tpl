<div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            {* Site Builder menu entries *}
            {if jrCore_module_is_active('jrSiteBuilder')}
                {jrSiteBuilder_default_menu title="Home" url="" weight="0"}
                {jrSiteBuilder_default_menu title="Community" url="community" weight="1"}
                {jrSiteBuilder_default_menu title="Forum" url="admin/forum" parent="community" weight="2"}
                {jrSiteBuilder_default_menu title="Documentation" url="admin/documentation" parent="community" weight="3"}
                {jrSiteBuilder_default_menu title="Charts" url="music_charts" weight="4"}
                {jrSiteBuilder_default_menu title="Music" url="music_charts" parent="music_charts" weight="5"}
                {jrSiteBuilder_default_menu title="Weekly" url="music_charts" parent="music_charts" weight="6"}
                {jrSiteBuilder_default_menu title="Monthly" url="music_charts_monthly" parent="music_charts" weight="7"}
                {jrSiteBuilder_default_menu title="Yearly" url="music_charts_yearly" parent="music_charts" weight="8"}
                {jrSiteBuilder_default_menu title="Video" url="video_charts" parent="music_charts" weight="9"}
                {jrSiteBuilder_default_menu title="Weekly" url="video_charts" parent="video_charts" weight="10"}
                {jrSiteBuilder_default_menu title="Monthly" url="video_charts_monthly" parent="video_charts" weight="11"}
                {jrSiteBuilder_default_menu title="Yearly" url="video_charts_yearly" parent="video_charts" weight="12"}
                {jrSiteBuilder_default_menu title="Lists" url="artists" weight="13"}
                {jrSiteBuilder_default_menu title="Artists" url="artists" parent="artists" weight="14"}
                {jrSiteBuilder_default_menu title="Newest Artists" url="artists/by_newest" parent="artists" weight="15"}
                {jrSiteBuilder_default_menu title="Top Artists" url="artists/most_viewed" parent="artists" weight="16"}
                {jrSiteBuilder_default_menu title="Members" url="members" parent="artists" weight="17"}
                {jrSiteBuilder_default_menu title="Newest Members" url="members/by_newest" parent="members" weight="18"}
                {jrSiteBuilder_default_menu title="Top Members" url="members/most_viewed" parent="members" weight="19"}
                {jrSiteBuilder_default_menu title="Music" url="music" parent="artists" weight="20"}
                {jrSiteBuilder_default_menu title="By Plays" url="music/by_plays" parent="music" weight="21"}
                {jrSiteBuilder_default_menu title="By Rating" url="music/by_ratings" parent="music" weight="22"}
                {jrSiteBuilder_default_menu title="SoundCloud" url="sound_cloud" parent="music" weight="23"}
                {jrSiteBuilder_default_menu title="Videos" url="videos" parent="artists" weight="24"}
                {jrSiteBuilder_default_menu title="By Plays" url="videos/by_plays" parent="videos" weight="25"}
                {jrSiteBuilder_default_menu title="By Rating" url="videos/by_ratings" parent="videos" weight="26"}
                {jrSiteBuilder_default_menu title="YouTube" url="you_tube" parent="videos" weight="27"}
                {jrSiteBuilder_default_menu title="Vimeo" url="vimeo_videos" parent="videos" weight="28"}
                {jrSiteBuilder_default_menu title="Galleries" url="galleries" weight="29"}
                {jrSiteBuilder_default_menu title="Events" url="events" weight="30"}
                {jrSiteBuilder_default_menu title="Upcoming" url="events/by_upcoming" parent="events" weight="31"}
                {jrSiteBuilder_default_menu title="By Rating" url="events/by_ratings" parent="events" weight="32"}
                {jrSiteBuilder_default_menu title="Articles" url="articles" weight="33"}

                {jrSiteBuilder_menu}
            {else}

                {if $_conf.jrCore_maintenance_mode != 'on' || jrUser_is_master() || jrUser_is_admin()}
                    <li><a href="{$jamroom_url}"{if isset($selected) && $selected == 'home'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</a></li>
                    <li>
                        {if isset($_conf.jrSage_forum_profile_url) && strlen($_conf.jrSage_forum_profile_url) > 0}
                            {assign var="forum_url" value=$_conf.jrSage_forum_profile_url}
                            {assign var="forum_uri" value="/`$_conf.jrSage_forum_profile_url`/forum"}
                        {/if}
                        {if isset($_conf.jrSage_docs_profile_url) && strlen($_conf.jrSage_docs_profile_url) > 0}
                            {assign var="doc_url" value=$_conf.jrSage_docs_profile_url}
                            {assign var="doc_uri" value="/`$_conf.jrSage_docs_profile_url`/documentation"}
                        {/if}
                        <a href="{$jamroom_url}/community"{if isset($selected) && $selected == 'community' || (isset($_post._uri) && $_post._uri == $forum_uri) || (isset($_post._uri) && $_post._uri == $doc_uri)} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="26" default="community"}</a>
                        <ul>
                            {if jrCore_module_is_active('jrForum') && isset($forum_url)}
                                {jrCore_module_url module="jrForum" assign="furl"}
                                <li><a href="{$jamroom_url}/{$forum_url}/{$furl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a></li>
                            {/if}

                            {if jrCore_module_is_active('jrDocs') && isset($doc_url)}
                                {jrCore_module_url module="jrDocs" assign="durl"}
                                <li><a href="{$jamroom_url}/{$doc_url}/{$durl}">{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a></li>
                            {/if}
                        </ul>
                    </li>
                    {if jrCore_module_is_active('jrCharts')}
                        <li>
                            <a href="{$jamroom_url}/music_charts"{if isset($selected) && $selected == 'charts'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}</a>
                            <ul>
                                <li>
                                    <a href="{$jamroom_url}/music_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="Music"}</a>
                                    <ul>
                                        <li><a href="{$jamroom_url}/music_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Weekly"}</a></li>
                                        <li><a href="{$jamroom_url}/music_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="Monthly"}</a></li>
                                        <li><a href="{$jamroom_url}/music_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="Yearly"}</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video"}</a>
                                    <ul>
                                        <li><a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Weekly"}</a></li>
                                        <li><a href="{$jamroom_url}/video_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="Monthly"}</a></li>
                                        <li><a href="{$jamroom_url}/video_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="Yearly"}</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    {/if}
                    <li>
                        <a href="{$jamroom_url}/artists"{if isset($selected) && $selected == 'lists'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="28" default="lists"}</a>
                        <ul>
                            <li>
                                <a href="{$jamroom_url}/artists">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/artists/by_newest">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a></li>
                                    <li><a href="{$jamroom_url}/artists/most_viewed">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a></li>
                                </ul>
                            </li>

                            {if isset($_conf.jrSage_member_quota) && $_conf.jrSage_member_quota > 0}
                                <li>
                                    <a href="{$jamroom_url}/members">{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</a>
                                    <ul>
                                        <li><a href="{$jamroom_url}/members/by_newest">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</a></li>
                                        <li><a href="{$jamroom_url}/members/most_viewed">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</a></li>
                                    </ul>
                                </li>
                            {/if}

                            <li>
                                <a href="{$jamroom_url}/music">{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/music/by_plays">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="By Plays"}</a></li>
                                    <li><a href="{$jamroom_url}/music/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                                    {if jrCore_module_is_active('jrSoundCloud')}
                                        <li><a href="{$jamroom_url}/sound_cloud">{jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="SoundCloud"}</a></li>
                                    {/if}
                                </ul>
                            </li>

                            <li>
                                <a href="{$jamroom_url}/videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/videos/by_plays">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="By Plays"}</a></li>
                                    <li><a href="{$jamroom_url}/videos/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                                    {if jrCore_module_is_active('jrYouTube')}
                                        <li><a href="{$jamroom_url}/you_tube">{jrCore_lang skin=$_conf.jrCore_active_skin id="73" default="YouTube"}</a></li>
                                    {/if}
                                    {if jrCore_module_is_active('jrVimeo')}
                                        <li><a href="{$jamroom_url}/vimeo_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Vimeo"}</a></li>
                                    {/if}
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="{$jamroom_url}/galleries"{if isset($selected) && $selected == 'galleries'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="galleries"}</a></li>
                    <li>
                        <a href="{$jamroom_url}/events"{if isset($selected) && $selected == 'events'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="30" default="events"}</a>
                        <ul>
                            <li><a href="{$jamroom_url}/events/by_upcoming">{jrCore_lang skin=$_conf.jrCore_active_skin id="68" default="upcoming"}</a></li>
                            <li><a href="{$jamroom_url}/events/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                        </ul>
                    </li>
                    <li><a href="{$jamroom_url}/articles"{if isset($selected) && $selected == 'articles'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="articles"}</a></li>
                {/if}
            {/if}


            {* Add additional menu categories here *}


            {if jrUser_is_logged_in()}
                {if jrUser_is_master()}
                    {jrCore_module_url module="jrCore" assign="core_url"}
                    {jrCore_get_module_index module="jrCore" assign="url"}
                    {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes' || $_post._uri == '/poll/votes'}
                        {assign var="acp_menu_item" value="no"}
                    {else}
                        {assign var="acp_menu_item" value="yes"}
                    {/if}
                    <li>
                        <a href="{$jamroom_url}/{$core_url}/admin/global"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="16" default="ACP"}</a>
                        <ul>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="75" default="System Tools"}</a>
                                <ul>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin=$_conf.jrCore_active_skin id="91" default="Activity Logs"}</a>
                                        <ul>
                                            <li><a href="{$jamroom_url}/{$core_url}/debug_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="119" default="Debug Logs"}</a></li>
                                            <li><a href="{$jamroom_url}/{$core_url}/php_error_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="120" default="PHP Error Logs"}</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="92" default="Reset Cache"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="104" default="Reset Image Cache"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="93" default="Integrity Check"}</a></li>
                                    <li><a href="{$jamroom_url}/marketplace/system_update">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="System Updates"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="97" default="System Check"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrBanned"}/browse">{jrCore_lang skin=$_conf.jrCore_active_skin id="94" default="Banned Items"}</a></li>
                                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrSitemap"}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="96" default="Create Sitemap"}</a></li>
                                </ul>
                            </li>
                            <li>
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_module_url module="jrUser" assign="uurl"}
                                <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="116" default="Users"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="111" default="Profile Quota Browser"}</a></li>
                                    <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="114" default="Profile Browser"}</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="112" default="User Accounts"}</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Who's Online"}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="74" default="Skin Settings"}</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="122" default="Skin Style"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="123" default="Skin Images"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="126" default="Skin Language"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="124" default="Skin Templates"}</a></li>
                                    <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin=$_conf.jrCore_active_skin id="121" default="Skin Notes"}</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin=$_conf.jrCore_active_skin id="95" default="Skin Menu Editor"}</a></li>
                                </ul>
                            </li>
                            <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
                        </ul>
                    </li>
                {elseif jrUser_is_admin()}
                    {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes' || $_post._uri == '/poll/votes'}
                        {assign var="acp_menu_item" value="no"}
                    {else}
                        {assign var="acp_menu_item" value="yes"}
                    {/if}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
                {/if}
            {else}
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup"{if isset($_post._uri) && $_post._uri == '/user/signup'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="3" default="account"}</a></li>
                {/if}
                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/login"{if isset($_post._uri) && $_post._uri == '/user/login'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="login"}</a></li>
            {/if}

            {if jrUser_is_logged_in()}
                {if isset($from_profile) && $from_profile == 'yes' && $_post._uri != $forum_uri && $_post._uri != $doc_uri || ($_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes' || $_post._uri == '/poll/votes')}
                    {assign var="artist_menu_item" value="yes"}
                {else}
                    {assign var="artist_menu_item" value="no"}
                {/if}
                <li>
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}"{if isset($artist_menu_item) && $artist_menu_item == 'yes'} class="menu_active" style="color:#E6F0E6;"{/if}>{jrUser_home_profile_key key="profile_name"}</a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>

            {/if}

            {* Add in Cart link if jrFoxyCart module is installed *}
            {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li>
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view"><div class="h_cart">&nbsp;</div><span id="fc_minicart"><span id="fc_quantity"></span></span></a>
                </li>
            {/if}

            {if jrCore_module_is_active('jrSearch')}
                <li><a onclick="jrSearch_modal_form();" title="Site Search"><div class="h_mag_glass">&nbsp;</div></a></li>
            {/if}

        </ul>
    </nav>


</div>
