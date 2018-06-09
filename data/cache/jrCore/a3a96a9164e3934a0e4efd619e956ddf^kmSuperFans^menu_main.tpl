
<div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            {* Site Search *}
            {if jrCore_module_is_active('jrSearch')}
                {jrCore_lang skin="kmSuperFans" id=10 default="Search" assign="st"}
                <li class="desk right"><a onclick="jrSearch_modal_form()" title="{$st}">{jrCore_image image="search44.png" width=22 height=22 alt=$st}</a></li>
            {/if}

            {* Cart *}
            {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li class="right">
                    {jrCore_lang skin="kmSuperFans" id=9 default="Cart" assign="ct"}
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_image image="cart44.png" width=22 height=22 alt=$ct}</a>
                    <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                </li>
            {/if}

            {* ACP  / Dashboard *}
            {if jrUser_is_master()}
                {jrCore_module_url module="jrCore" assign="core_url"}
                {jrCore_module_url module="jrMarket" assign="murl"}
                {jrCore_get_module_index module="jrCore" assign="url"}
                <li class="desk right">
                    <a href="{$jamroom_url}/{$core_url}/admin/global">
                        <img title="{jrCore_lang skin="kmSuperFans" id="29" default="ACP"}" alt="{jrCore_lang skin="kmSuperFans" id="29" default="ACP"}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/acp.png" />
                    </a>
                    <ul>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin="kmSuperFans" id=11 default="system tools"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin="kmSuperFans" id="12" default="activity logs"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin="kmSuperFans" id="13" default="reset caches"}</a></li>
                                <li><a href="{$jamroom_url}/image/cache_reset">{jrCore_lang skin="kmSuperFans" id="68" default="image caches"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin="kmSuperFans" id="14" default="integrity check"}</a></li>
                                <li><a href="{$jamroom_url}/{$murl}/system_update">{jrCore_lang skin="kmSuperFans" id="15" default="system updates"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin="kmSuperFans" id="16" default="system check"}</a></li>
                            </ul>
                        </li>
                        <li>
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_module_url module="jrUser" assign="uurl"}
                            <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin="kmSuperFans" id="17" default="users"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin="kmSuperFans" id="18" default="quota browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin="kmSuperFans" id="19" default="profile browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin="kmSuperFans" id="20" default="user accounts"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin="kmSuperFans" id="21" default="users online"}</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="kmSuperFans" id="22" default="skin settings"}</a>
                            <ul>
                                <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin="kmSuperFans" id="23" default="skin notes"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin="kmSuperFans" id="24" default="user menu editor"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="kmSuperFans" id="25" default="skin images"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="kmSuperFans" id="26" default="skin style"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="kmSuperFans" id="27" default="skin templates"}</a></li>
                            </ul>
                        </li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="kmSuperFans" id="28" default="dashboard"}</a></li>
                    </ul>
                </li>
            {elseif jrUser_is_admin()}
                <a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">
                    <img title="{jrCore_lang skin="kmSuperFans" id="28" default="dashboard"}" alt="{jrCore_lang skin="kmSuperFans" id="28" default="dashboard"}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/acp.png" />
                </a>
            {/if}



            {if !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li class="right"><button id="user-create-account" class="form_button" onclick="window.location='{$jamroom_url}/{$uurl}/signup'">
                            {jrCore_lang skin="kmSuperFans" id="7" default="Sign Up"}
                        </button></li>
                {/if}
                <li class="right"><a href="{$jamroom_url}/{$uurl}/login" title="{jrCore_lang skin="kmSuperFans" id="3" default="Log In"}">
                        {jrCore_image image="login.png" width="22" height="22" alt="login"}
                    </a></li>
            {/if}

            {* User Settings drop down menu *}
            {if jrUser_is_logged_in()}
                <li class="desk right">
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">
                        {jrUser_home_profile_key key="profile_name" assign="profile_name"}
                        {jrCore_module_function
                        function="jrImage_display"
                        module="jrUser"
                        type="user_image"
                        item_id=$_user._user_id
                        size="small"
                        crop="auto"
                        alt=$profile_name
                        title=$profile_name
                        class="menu_user_image"
                        width=22
                        height=22
                        }
                    </a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>
            {/if}

            {jrSiteBuilder_menu class="desk"}
        </ul>
    </nav>
</div>
