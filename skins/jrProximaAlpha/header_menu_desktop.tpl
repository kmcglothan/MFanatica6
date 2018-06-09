<div id="menu_content">

    <nav id="menu-wrap">
        <ul id="menu">
        {jrCore_module_url module="jrUser" assign="uurl"}

        {if $_conf.jrProximaAlpha_show_search == 'on' && jrCore_module_is_active('jrSearch')}
            {jrCore_lang skin=$_conf.jrCore_active_skin id="8" default="Search" assign="search_label"}
            <li><a onclick="jrSearch_modal_form();" title="{$search_label}">{$search_label}</a></li>
        {/if}

        {* Add in Cart link if jrFoxyCart module is installed *}
        {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
            <li>
                <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Cart"}</a>
                <span id="fc_minicart"><span id="fc_quantity"></span></span>
            </li>
        {/if}

        {if jrUser_is_logged_in()}
            {if jrUser_is_master()}
                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrProximaStat"}/view_graph">Stats</a></li>
            {/if}
            {if jrUser_is_admin()}
                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">Dashboard</a></li>
            {/if}
            <li>
                {if $_conf.jrProximaCore_enable_profiles == 'on' || jrUser_is_admin()}
                <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">Account</a>
                {else}
                <a href="{$jamroom_url}/{$uurl}/account">Account</a>
                {/if}
                <ul>
                    {jrCore_skin_menu template="menu.tpl" category="user"}
                </ul>
            </li>
        {/if}


        {* Add additional menu categories here *}

        {if jrUser_is_logged_in()}

            {if jrUser_is_master()}
                {jrCore_module_url module="jrCore" assign="core_url"}
                {jrCore_module_url module="jrProximaCore" assign="pxc_url"}
                {jrCore_module_url module="jrMarket" assign="m_url"}
                <li>
                    <a href="{$jamroom_url}/{$pxc_url}/app_browser">Admin</a>
                    <ul>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/admin/tools">System Tools</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$core_url}/dashboard/activity">Activity Logs</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/cache_reset">Reset Caches</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/integrity_check">Integrity Check</a></li>
                                <li><a href="{$jamroom_url}/{$m_url}/system_update">System Updates</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/system_check">System Check</a></li>
                            </ul>
                        </li>
                        <li>
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_module_url module="jrUser" assign="uurl"}
                            <a href="{$jamroom_url}/{$purl}/admin/tools">Users</a>
                            <ul>
                                {if $_conf.jrProximaCore_enable_profiles == 'on'}
                                <li><a href="{$jamroom_url}/{$purl}/browser">Profile Browser</a></li>
                                {/if}
                                <li><a href="{$jamroom_url}/{$uurl}/browser">User Browser</a></li>
                                <li><a href="{$jamroom_url}/{$uurl}/online">Who's Online</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">Skin Settings</a>
                        </li>
                    </ul>
                </li>
            {/if}

        {else}

            {jrCore_module_url module="jrUser" assign="uurl"}
            {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                <li><a href="{$jamroom_url}/{$uurl}/signup">{jrCore_lang skin=$_conf.jrCore_active_skin id="2" default="Create Account"}</a></li>
            {/if}
            <li><a href="{$jamroom_url}/{$uurl}/login">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Login"}</a></li>

        {/if}

        {if jrCore_module_is_active('jrBlog') && strlen($_conf.jrProximaAlpha_blog_profile_ids) > 0}
            {jrCore_module_url module="jrBlog" assign="burl"}
            <li><a href="{$jamroom_url}/{$burl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="Blog"}</a></li>
        {/if}
        </ul>
    </nav>

    {* This is the search form - shows as a modal window when the search icon is clicked on *}
    <div id="searchform" class="search_box" style="display:none;">
        {jrSearch_form class="form_text" value="{$search_label}" style="width:70%"}
        <div style="float:right;clear:both;margin-top:3px;">
            <a class="simplemodal-close">{jrCore_icon icon="close" size="16"}</a>
        </div>
        <div class="clear"></div>
    </div>

</div>