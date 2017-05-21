<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">

            {if $_conf.jrCore_maintenance_mode != 'on' || jrUser_is_master() || jrUser_is_admin()}
            {* Site Builder menu entries *}
            {if jrCore_module_is_active('jrSiteBuilder')}

                {jrSiteBuilder_mobile_menu}

            {else}

                <li><a href="{$jamroom_url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</a></li>
                <li>
                    <a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}">{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="Band"}</a>
                    <ul>
                        <li><a href="{$jamroom_url}/blogs">Blogs</a></li>
                        <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/gallery">{jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="Gallery"}</a></li>
                    </ul>
                </li>
                <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/event">{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="Tour"}</a></li>
                <li>
                    <a href="{$jamroom_url}/{* $_conf.jrSoloArtist_main_profile_url|replace:' ':'-'/audio *}music">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="music"}</a>
                    {if jrCore_module_is_active('jrSoundCloud')}
                        <ul>
                            <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/soundcloud">{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="SoundCloud"}</a></li>
                        </ul>
                    {/if}
                </li>

                <li>
                    {if jrCore_module_is_active('jrVideo')}
                        <a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/video">{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="video"}</a>
                        {if jrCore_module_is_active('jrYouTube') || jrCore_module_is_active('jrVimeo')}
                            <ul>
                                {if jrCore_module_is_active('jrVimeo')}
                                    <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/vimeo">{jrCore_lang skin=$_conf.jrCore_active_skin id="41" default="Vimeo"}</a></li>
                                {/if}
                                {if jrCore_module_is_active('jrYouTube')}
                                    <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/youtube">{jrCore_lang skin=$_conf.jrCore_active_skin id="15" default="YouTube"}</a></li>
                                {/if}
                            </ul>
                        {/if}
                    {elseif jrCore_module_is_active('jrYouTube')}
                        <a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/youtube">{jrCore_lang skin=$_conf.jrCore_active_skin id="15" default="YouTube"}</a>
                        {if jrCore_module_is_active('jrVimeo')}
                            <ul>
                                <li><a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/vimeo">{jrCore_lang skin=$_conf.jrCore_active_skin id="41" default="Vimeo"}</a></li>
                            </ul>
                        {/if}
                    {/if}
                </li>

                <li><a href="{$jamroom_url}/fans">{jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="Our Fans"}</a></li>
                {if jrCore_module_is_active('jrCustomForm')}
                    <li><a href="{$jamroom_url}/form/contact_us">{jrCore_lang skin=$_conf.jrCore_active_skin id="82" default="Contact Us"}</a></li>
                {else}
                    {capture name="footer_contact" assign="footer_contact_row"}
                        {literal}
                            {if isset($_items)}
                                {foreach from=$_items item="item"}
                                    <li><a href="mailto:{$item.user_email}?subject={$_conf.jrCore_system_name} Contact">{jrCore_lang skin=$_conf.jrCore_active_skin id="82" default="Contact Us"}</a></li>
                               {/foreach}
                            {/if}
                        {/literal}
                    {/capture}
                    {jrCore_list module="jrUser" limit="1" profile_id="1" template=$footer_contact_row}
                {/if}

            {/if}

            {/if}
            <div id="menu_content_right">
                {if jrUser_is_logged_in()}
                    {if jrUser_is_master()}
                        {jrCore_module_url module="jrCore" assign="core_url"}
                        {jrCore_get_module_index module="jrCore" assign="url"}
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/admin/global">{jrCore_lang skin=$_conf.jrCore_active_skin id="16" default="ACP"}</a>
                            <ul>
                                <li>
                                    <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="37" default="System Tools"}</a>
                                    <ul>
                                        <li>
                                            <a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin=$_conf.jrCore_active_skin id="28" default="Activity Logs"}</a>
                                            <ul>
                                                <li><a href="{$jamroom_url}/{$core_url}/debug_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="92" default="Debug Logs"}</a></li>
                                                <li><a href="{$jamroom_url}/{$core_url}/php_error_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="93" default="PHP Error Logs"}</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="Reset Cache"}</a></li>
                                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="30" default="Reset Image Cache"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="31" default="Integrity Check"}</a></li>
                                        <li><a href="{$jamroom_url}/marketplace/system_update">{jrCore_lang skin=$_conf.jrCore_active_skin id="91" default="System Updates"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="35" default="System Check"}</a></li>
                                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrBanned"}/browse">{jrCore_lang skin=$_conf.jrCore_active_skin id="32" default="Banned Items"}</a></li>
                                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrSitemap"}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="34" default="Create Sitemap"}</a></li>
                                    </ul>
                                </li>
                                <li>
                                    {jrCore_module_url module="jrProfile" assign="purl"}
                                    {jrCore_module_url module="jrUser" assign="uurl"}
                                    <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Users"}</a>
                                    <ul>
                                        <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="76" default="Profile Quota Browser"}</a></li>
                                        <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="79" default="Profile Browser"}</a></li>
                                        <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="User Accounts"}</a></li>
                                        <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Who's Online"}</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="38" default="Skin Settings"}</a>
                                    <ul>
                                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="95" default="Skin Styles"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="96" default="Skin Images"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="98" default="Skin Language"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="97" default="Skin Templates"}</a></li>
                                        <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin=$_conf.jrCore_active_skin id="94" default="Skin Notes"}</a></li>
                                        <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin=$_conf.jrCore_active_skin id="33" default="Skin Menu Editor"}</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    {/if}
                {else}
                    {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup">{jrCore_lang skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="3" default="account"}</a></li>
                    {/if}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/login">{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="login"}</a></li>
                {/if}

                {if jrUser_is_logged_in()}
                    <li>
                        <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">{jrUser_home_profile_key key="profile_name"}</a>
                        <ul>
                            {jrCore_skin_menu template="menu.tpl" category="user"}
                        </ul>
                    </li>
                {/if}
                {if jrUser_is_admin()}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
                {/if}
            </div>

            {if jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrChat_allowed') == 'on'}
                <li><a href="{$jamroom_url}/chat/mobile" target="_blank">User Chat</a></li>
            {/if}

            {* Add additional menu categories here *}

        </ul>
    </nav>
</div>

<div id="sb-site">