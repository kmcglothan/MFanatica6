<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">

            <li class="left"><a href="{$jamroom_url}">{jrCore_lang skin="jrVideoPro" id="1" default="Home"}</a></li>

            {if !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <li class="right"><a href="{$jamroom_url}/{$uurl}/login">
                        {jrCore_lang skin="jrVideoPro" id="3" default="Log In"}
                    </a></li>
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li><a href="{$jamroom_url}/{$uurl}/signup">{jrCore_lang skin="jrVideoPro" id="7" default="Sign Up"}</a></li>
                {/if}
            {/if}

            {if jrCore_module_is_active('jrSearch')}
                {jrCore_lang skin="jrVideoPro" id=36 default="Search" assign="st"}
                <li><a onclick="jrSearch_modal_form()" title="{$st}">{jrCore_lang skin="jrVideoPro" id=10 default="Search"}</a></li>
            {/if}

            {* Cart *}
            {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li>
                    {jrCore_lang skin="jrVideoPro" id=9 default="Cart" assign="ct"}
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{$ct}</a>
                    <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                </li>
            {/if}

            {* ACP  / Dashboard *}
            {if jrUser_is_master()}
                {jrCore_module_url module="jrCore" assign="core_url"}
                {jrCore_module_url module="jrMarket" assign="murl"}
                {jrCore_get_module_index module="jrCore" assign="url"}
                <li>
                    <a href="{$jamroom_url}/{$core_url}/admin/global">
                        {jrCore_lang skin="jrVideoPro" id=29 default="Admin Control Panel"}
                    </a>
                    <ul>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin="jrVideoPro" id=11 default="system tools"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin="jrVideoPro" id="12" default="activity logs"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin="jrVideoPro" id="13" default="reset caches"}</a></li>
                                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="Image Caches"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin="jrVideoPro" id="14" default="integrity check"}</a></li>
                                <li><a href="{$jamroom_url}/{$murl}/system_update">{jrCore_lang skin="jrVideoPro" id="15" default="system updates"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin="jrVideoPro" id="16" default="system check"}</a></li>
                            </ul>
                        </li>
                        <li>
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_module_url module="jrUser" assign="uurl"}
                            <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin="jrVideoPro" id="17" default="users"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin="jrVideoPro" id="18" default="quota browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin="jrVideoPro" id="19" default="profile browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin="jrVideoPro" id="20" default="user accounts"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin="jrVideoPro" id="21" default="users online"}</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrVideoPro" id="22" default="skin settings"}</a>
                            <ul>
                                <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin="jrVideoPro" id="23" default="skin notes"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin="jrVideoPro" id="24" default="user menu editor"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrVideoPro" id="25" default="skin images"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrVideoPro" id="26" default="skin style"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrVideoPro" id="27" default="skin templates"}</a></li>
                            </ul>
                        </li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrVideoPro" id="28" default="dashboard"}</a></li>
                    </ul>
                </li>
            {elseif jrUser_is_admin()}
                <li>
                    <a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrVideoPro" id="28" default="dashboard"}</a>
                </li>
            {/if}

            {if jrUser_is_logged_in() && jrCore_module_is_active('jrChat') && jrUser_get_profile_home_key('quota_jrChat_allowed') == 'on'}
                {jrCore_module_url module="jrChat" assign="curl"}
                <li><a href="{$jamroom_url}/{$curl}/mobile">{jrCore_lang module="jrChat" id=73 default="Chat"}</a></li>
            {/if}

            {* User menu entries *}
            {jrSiteBuilder_menu}

            {* User Settings drop down menu *}
            {if jrUser_is_logged_in()}
                <li>
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">
                        {jrUser_home_profile_key key="profile_name"}
                    </a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>
            {/if}

        </ul>
    </nav>
</div>
<div id="sb-site">
