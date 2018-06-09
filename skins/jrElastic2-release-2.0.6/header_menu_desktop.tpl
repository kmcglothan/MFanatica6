<div id="menu_content" class="clearfix">
    <nav id="menu-wrap" class="clearfix">
       <div id="menu">
           <nav id="nav_menu">
               <ul>

                   {* User menu entries *}
                   {jrSiteBuilder_menu}

                   {* User Settings drop down menu *}
                   {if jrUser_is_logged_in()}
                       <li>
                           <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">{jrUser_home_profile_key key="profile_name"}</a>
                           <ul>
                               {jrCore_skin_menu template="menu.tpl" category="user"}
                           </ul>
                       </li>
                   {/if}

                   {* ACP  / Dashboard *}
                   {if jrUser_is_master()}
                       {jrCore_module_url module="jrCore" assign="core_url"}
                       {jrCore_module_url module="jrMarket" assign="murl"}
                       {jrCore_get_module_index module="jrCore" assign="url"}
                       <li>
                           <a href="{$jamroom_url}/{$core_url}/admin/global">{jrCore_lang skin="jrElastic2" id=16 default="ACP"}</a>
                           <ul>
                               <li>
                                   <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin="jrElastic2" id=56 default="system tools"}</a>
                                   <ul>
                                       <li><a href="{$jamroom_url}/{$core_url}/dashboard/activity">{jrCore_lang skin="jrElastic2" id=57 default="activity logs"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/cache_reset">reset caches</a></li>
                                       <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin="jrElastic2" id=58 default="reset image caches"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin="jrElastic2" id=59 default="integrity check"}</a></li>
                                       <li><a href="{$jamroom_url}/{$murl}/system_update">{jrCore_lang skin="jrElastic2" id=60 default="system updates"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin="jrElastic2" id=61 default="system check"}</a></li>
                                   </ul>
                               </li>
                               <li>
                                   {jrCore_module_url module="jrProfile" assign="purl"}
                                   {jrCore_module_url module="jrUser" assign="uurl"}
                                   <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin="jrElastic2" id=49 default="users"}</a>
                                   <ul>
                                       <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin="jrElastic2" id=50 default="quota browser"}</a></li>
                                       <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin="jrElastic2" id=51 default="profile browser"}</a></li>
                                       <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin="jrElastic2" id=52 default="user accounts"}</a></li>
                                       <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin="jrElastic2" id=53 default="users online"}</a></li>
                                   </ul>
                               </li>
                               <li>
                                   <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=35 default="skin settings"}</a>
                                   <ul>
                                       <li><a onclick="popwin('{$jamroom_url}/skins/jrElastic2/readme.html','readme',600,500,'yes');">{jrCore_lang skin="jrElastic2" id=36 default="skin notes"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/skin_menu">user menu editor</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=37 default="skin images"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=38 default="skin style"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=34 default="Language"}</a></li>
                                       <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin=jrElastic2">{jrCore_lang skin="jrElastic2" id=39 default="skin templates"}</a></li>
                                   </ul>
                               </li>
                               <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrElastic2" id=17 default="dashboard"}</a></li>
                               <li>
                                   <a href="{$jamroom_url}/{$core_url}/support">{jrCore_lang skin="jrElastic2" id=27 default="Help"}</a>
                                   <ul>
                                       <li><a href="https://www.jamroom.net/the-jamroom-network/documentation" target="_blank">{jrCore_lang skin="jrElastic2" id=28 default="Documentation"}</a></li>
                                       <li><a href="https://www.jamroom.net/the-jamroom-network/forum" target="_blank">{jrCore_lang skin="jrElastic2" id=29 default="Community Forum"}</a></li>
                                       <li><a href="https://www.jamroom.net/subscribe" target="_blank">{jrCore_lang skin="jrElastic2" id=30 default="VIP Support"}</a></li>
                                       <li><a href="{$jamroom_url}/{jrCore_module_url module="jrMarket"}/browse">{jrCore_lang skin="jrElastic2" id=31 default="Marketplace"}</a></li>
                                       <li><a href="https://demo.jamroom.net/jrElastic2" target="_blank">{jrCore_lang skin="jrElastic2" id=33 default="View Skin Demo"}</a></li>
                                   </ul>
                               </li>
                           </ul>
                       </li>
                   {elseif jrUser_is_admin()}
                       <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin="jrElastic2" id="17" default="dashboard"}</a></li>
                   {/if}

                   {if !jrUser_is_logged_in()}
                       {jrCore_module_url module="jrUser" assign="uurl"}
                       {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                           <li><a id="user-create-account" href="{$jamroom_url}/{$uurl}/signup">{jrCore_lang skin="jrElastic2" id="2" default="create"}&nbsp;{jrCore_lang skin="jrElastic2" id="3" default="account"}</a></li>
                       {/if}
                       <li><a href="{$jamroom_url}/{$uurl}/login">{jrCore_lang skin="jrElastic2" id=22 default="login"}</a></li>
                   {/if}


                   {* Site Search *}
                   {if jrCore_module_is_active('jrSearch')}
                       {jrCore_lang skin="jrElastic2" id=24 default="Search" assign="st"}
                       <li><a onclick="jrSearch_modal_form()" title="{$st}">{jrCore_image image="search44.png" width=22 height=22 alt=$st style="margin-top:-3px"}</a></li>
                   {/if}

                   {* Cart *}
                   {if jrCore_module_is_active('jrPayment')}
                       <!-- jrPayment_cart_html -->
                   {elseif jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                       <li>
                           {jrCore_lang skin="jrElastic2" id=67 default="Cart" assign="ct"}
                           <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_image image="cart44.png" width=22 height=22 alt=$ct style="margin-top:-3px"}</a>
                           <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                       </li>
                   {/if}

               </ul>
           </nav>
       </div>
    </nav>
</div>
