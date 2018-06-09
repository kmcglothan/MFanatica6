<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">
            <li><a href="{$jamroom_url}">{jrCore_lang skin="jrElastic2" id="1" default="Home"}</a></li>

            {if jrCore_module_is_active('jrSearch')}
                {jrCore_lang skin="jrElastic2" id="24" default="search" assign="st"}
                <li><a onclick="jrSearch_modal_form();" title="{$st}">{$st}</a></li>
            {/if}

            {* Add in Cart link if jrFoxyCart module is installed *}
            {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li>
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_lang skin="jrElastic2" id=67 default="Cart"}</a>
                    <span id="fc_minicart"><span id="fc_quantity"></span></span>
                </li>
            {/if}

            {* User menu entries *}
            {jrSiteBuilder_mobile_menu}

            {if jrUser_is_logged_in()}
                {if jrUser_is_admin()}
                    <li>
                        <a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrElastic2" id="17" default="dashboard"}</a>
                    </li>
                {/if}
                <li>
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">{jrUser_home_profile_key key="profile_name"}</a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>
            {/if}


            {if jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrChat_allowed') == 'on'}
                <li><a href="{$jamroom_url}/chat/mobile"
                       target="_blank">{jrCore_lang skin="jrElastic2" id=32 default="User Chat"}</a></li>
            {/if}

            {* Add additional menu categories here *}

            {jrCore_module_url module="jrUser" assign="uurl"}
            {if jrUser_is_logged_in()}
                {if jrUser_is_master()}
                    {jrCore_module_url module="jrCore" assign="core_url"}
                    {jrCore_module_url module="jrMarket" assign="murl"}
                    {jrCore_get_module_index module="jrCore" assign="url"}
                    <li>
                        <a href="{$jamroom_url}/{$core_url}/admin/global">{jrCore_lang skin="jrElastic2" id=16 default="16ACP"}</a>
                        <ul>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin="jrElastic2" id=56 default="system tools"}</a>
                                <ul>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin="jrElastic2" id=57 default="activity logs"}</a>
                                    </li>
                                    <li><a href="{$jamroom_url}/{$core_url}/cache_reset">reset caches</a></li>
                                    <li>
                                        <a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin="jrElastic2" id=58 default="reset image caches"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin="jrElastic2" id=59 default="integrity check"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$murl}/system_update">{jrCore_lang skin="jrElastic2" id=60 default="system updates"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin="jrElastic2" id=61 default="system check"}</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_module_url module="jrUser" assign="uurl"}
                                <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin="jrElastic2" id=49 default="users"}</a>
                                <ul>
                                    <li>
                                        <a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin="jrElastic2" id=50 default="quota browser"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin="jrElastic2" id=51 default="profile browser"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin="jrElastic2" id=52 default="user accounts"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin="jrElastic2" id=53 default="users online"}</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=35 default="skin settings"}</a>
                                <ul>
                                    <li>
                                        <a onclick="popwin('{$jamroom_url}/skins/jrElastic2/readme.html','readme',600,500,'yes');">{jrCore_lang skin="jrElastic2" id=36 default="skin notes"}</a>
                                    </li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_menu">user menu editor</a></li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=37 default="skin images"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=38 default="skin style"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=34 default="Language"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=39 default="skin templates"}</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrElastic2" id=17 default="dashboard"}</a>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/support">{jrCore_lang skin="jrElastic2" id=27 default="Help"}</a>
                                <ul>
                                    <li><a href="https://www.jamroom.net/the-jamroom-network/documentation"
                                           target="_blank">{jrCore_lang skin="jrElastic2" id=28 default="Documentation"}</a>
                                    </li>
                                    <li><a href="https://www.jamroom.net/the-jamroom-network/forum"
                                           target="_blank">{jrCore_lang skin="jrElastic2" id=29 default="Community Forum"}</a>
                                    </li>
                                    <li><a href="https://www.jamroom.net/subscribe"
                                           target="_blank">{jrCore_lang skin="jrElastic2" id=30 default="VIP Support"}</a>
                                    </li>
                                    <li>
                                        <a href="{$jamroom_url}/{jrCore_module_url module="jrMarket"}/browse">{jrCore_lang skin="jrElastic2" id=31 default="Marketplace"}</a>
                                    </li>
                                    <li><a href="http://elastic.n8flex.com"
                                           target="_blank">{jrCore_lang skin="jrElastic2" id=33 default="View Skin Demo"}</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                {/if}
                    <li><a href="{$jamroom_url}/{$uurl}/logout">{jrCore_lang skin="jrElastic2" id="5" default="logout"}</a>
                </li>
            {else}
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li><a href="{$jamroom_url}/{$uurl}/signup">{jrCore_lang skin="jrElastic2" id="2" default="create"}
                            &nbsp;{jrCore_lang skin="jrElastic2" id="3" default="account"}</a></li>
                {/if}
                <li><a href="{$jamroom_url}/{$uurl}/login">{jrCore_lang skin="jrElastic2" id=22 default="login"}</a>
                </li>
            {/if}
        </ul>
    </nav>
</div>

<div id="sb-site">