
<div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            {* Cart *}
            {if jrCore_module_is_active('jrPayment')}
                <!-- jrPayment_cart_html -->
            {elseif jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li class="desk right">
                    {jrCore_lang skin="jrISkin" id=35 default="Cart" assign="ct"}
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_image image="cart44.png" width=22 height=22 alt=$ct}</a>
                    <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                </li>
            {/if}

            {* Site Search *}
            {if jrCore_module_is_active('jrSearch')}
                {jrCore_lang skin="jrISkin" id=36 default="Search" assign="st"}
                <li class="desk right"><a onclick="jrSearch_modal_form()" title="{$st}">{jrCore_image image="search44.png" width=22 height=22 alt=$st}</a></li>
            {/if}

            {* ACP  / Dashboard *}
            {if jrUser_is_master()}
                {jrCore_module_url module="jrCore" assign="core_url"}
                {jrCore_module_url module="jrMarket" assign="murl"}
                {jrCore_get_module_index module="jrCore" assign="url"}
                <li class="desk right">
                    <a href="{$jamroom_url}/{$core_url}/admin/global">
                        <img title="{jrCore_lang skin="jrISkin" id="69" default="ACP"}" alt="{jrCore_lang skin="jrISkin" id="69" default="ACP"}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/acp.png" />
                    </a>
                    <ul>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin="jrISkin" id="51" default="system tools"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin="jrISkin" id="52" default="activity logs"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin="jrISkin" id="53" default="reset caches"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin="jrISkin" id="54" default="integrity check"}</a></li>
                                <li><a href="{$jamroom_url}/{$murl}/system_update">{jrCore_lang skin="jrISkin" id="55" default="system updates"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin="jrISkin" id="56" default="system check"}</a></li>
                            </ul>
                        </li>
                        <li>
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_module_url module="jrUser" assign="uurl"}
                            <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin="jrISkin" id="57" default="users"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin="jrISkin" id="58" default="quota browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin="jrISkin" id="59" default="profile browser"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin="jrISkin" id="60" default="user accounts"}</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin="jrISkin" id="61" default="users online"}</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrISkin" id="62" default="skin settings"}</a>
                            <ul>
                                <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">{jrCore_lang skin="jrISkin" id="63" default="skin notes"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin="jrISkin" id="64" default="user menu editor"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrISkin" id="65" default="skin images"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrISkin" id="66" default="skin style"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin="jrISkin" id="67" default="skin templates"}</a></li>
                            </ul>
                        </li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrISkin" id="68" default="dashboard"}</a></li>
                    </ul>
                </li>
            {elseif jrUser_is_admin()}
                <a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">
                    <img title="{jrCore_lang skin="jrISkin" id="68" default="dashboard"}" alt="{jrCore_lang skin="jrISkin" id="68" default="dashboard"}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/acp.png" />
                </a>
            {/if}



            {if !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li class="right"><button id="user-create-account" class="form_button" onclick="window.location='{$jamroom_url}/{$uurl}/signup'">
                            {jrCore_lang skin="jrISkin" id="28" default="Sign Up"}
                        </button></li>
                {/if}
                <li class="right"><a href="{$jamroom_url}/{$uurl}/login" title="{jrCore_lang skin="jrISkin" id="25" default="Login"}">
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
                        width=22
                        height=22
                        }
                    </a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>


                {jrCore_module_url module="jrAction" assign="tUrl"}
                <li class="right large" id="feedback">
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/{$tUrl}/feedback" title="{jrCore_lang skin="jrISkin" id=128 default="Feedback"}">
                        {jrCore_icon icon="notifications" size="24" color="ffffff"}
                        <span class="count feedback_count">0</span>
                    </a>
                </li>
                <li class="right small" id="mentions">
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/{$tUrl}/mentions" title="{jrCore_lang skin="jrISkin" id=127 default="Mentions"}">
                        {jrCore_icon icon="mention" size="24" color="ffffff"}
                        <span class="count mentions_count">0</span>
                    </a>
                </li>
                <li class="right small" id="followers">
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}/follow" title="{jrCore_lang skin="jrISkin" id=126 default="Followers"}">
                        {jrCore_icon icon="followers" size="24" color="ffffff"}
                        <span class="count followers_count">0</span>
                    </a>
                </li>
            {/if}

            {jrSiteBuilder_menu class="desk"}
        </ul>
    </nav>
</div>
