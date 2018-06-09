<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/a3a96a9164e3934a0e4efd619e956ddf^kmSuperFans^menu_main.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b0732392bd5_14306099',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6dcc45e2b1fa4c8f1bccd0e3d4aa6d2bb079804d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/a3a96a9164e3934a0e4efd619e956ddf^kmSuperFans^menu_main.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b0732392bd5_14306099 (Smarty_Internal_Template $_smarty_tpl) {
?>

<div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            
            <?php if (jrCore_module_is_active('jrSearch')) {?>
                <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>10,'default'=>"Search",'assign'=>"st"),$_smarty_tpl) : '';?>

                <li class="desk right"><a onclick="jrSearch_modal_form()" title="<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"search44.png",'width'=>22,'height'=>22,'alt'=>$_smarty_tpl->tpl_vars['st']->value),$_smarty_tpl) : '';?>
</a></li>
            <?php }?>

            
            <?php if (jrCore_module_is_active('jrFoxyCart') && strlen($_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_api_key']) > 0) {?>
                <li class="right">
                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>9,'default'=>"Cart",'assign'=>"ct"),$_smarty_tpl) : '';?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_store_domain'];?>
/cart?cart=view"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"cart44.png",'width'=>22,'height'=>22,'alt'=>$_smarty_tpl->tpl_vars['ct']->value),$_smarty_tpl) : '';?>
</a>
                    <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                </li>
            <?php }?>

            
            <?php if (jrUser_is_master()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl) : '';?>

                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrMarket",'assign'=>"murl"),$_smarty_tpl) : '';?>

                <?php echo (function_exists('smarty_function_jrCore_get_module_index')) ? smarty_function_jrCore_get_module_index(array('module'=>"jrCore",'assign'=>"url"),$_smarty_tpl) : '';?>

                <li class="desk right">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/global">
                        <img title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"29",'default'=>"ACP"),$_smarty_tpl) : '';?>
" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"29",'default'=>"ACP"),$_smarty_tpl) : '';?>
" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/acp.png" />
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/tools"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>11,'default'=>"system tools"),$_smarty_tpl) : '';?>
</a>
                            <ul>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/dashboard/activity"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"12",'default'=>"activity logs"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/cache_reset"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"13",'default'=>"reset caches"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/image/cache_reset"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"68",'default'=>"image caches"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/integrity_check"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"14",'default'=>"integrity check"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/system_update"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"15",'default'=>"system updates"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/system_check"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"16",'default'=>"system check"),$_smarty_tpl) : '';?>
</a></li>
                            </ul>
                        </li>
                        <li>
                            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/admin/tools"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"17",'default'=>"users"),$_smarty_tpl) : '';?>
</a>
                            <ul>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/quota_browser"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"18",'default'=>"quota browser"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/browser"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"19",'default'=>"profile browser"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/browser"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"20",'default'=>"user accounts"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/online"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"21",'default'=>"users online"),$_smarty_tpl) : '';?>
</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"22",'default'=>"skin settings"),$_smarty_tpl) : '';?>
</a>
                            <ul>
                                <li><a onclick="popwin('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/readme.html','readme',600,500,'yes');"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"23",'default'=>"skin notes"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_menu"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"24",'default'=>"user menu editor"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/images/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"25",'default'=>"skin images"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/style/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"26",'default'=>"skin style"),$_smarty_tpl) : '';?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/templates/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"27",'default'=>"skin templates"),$_smarty_tpl) : '';?>
</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl) : '';?>
/dashboard"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"28",'default'=>"dashboard"),$_smarty_tpl) : '';?>
</a></li>
                    </ul>
                </li>
            <?php } elseif (jrUser_is_admin()) {?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl) : '';?>
/dashboard">
                    <img title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"28",'default'=>"dashboard"),$_smarty_tpl) : '';?>
" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"28",'default'=>"dashboard"),$_smarty_tpl) : '';?>
" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/acp.png" />
                </a>
            <?php }?>



            <?php if (!jrUser_is_logged_in()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrCore_maintenance_mode'] != 'on' && $_smarty_tpl->tpl_vars['_conf']->value['jrUser_signup_on'] == 'on') {?>
                    <li class="right"><button id="user-create-account" class="form_button" onclick="window.location='<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/signup'">
                            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"7",'default'=>"Sign Up"),$_smarty_tpl) : '';?>

                        </button></li>
                <?php }?>
                <li class="right"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"3",'default'=>"Log In"),$_smarty_tpl) : '';?>
">
                        <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"login.png",'width'=>"22",'height'=>"22",'alt'=>"login"),$_smarty_tpl) : '';?>

                    </a></li>
            <?php }?>

            
            <?php if (jrUser_is_logged_in()) {?>
                <li class="desk right">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrUser_home_profile_key')) ? smarty_function_jrUser_home_profile_key(array('key'=>"profile_url"),$_smarty_tpl) : '';?>
">
                        <?php echo (function_exists('smarty_function_jrUser_home_profile_key')) ? smarty_function_jrUser_home_profile_key(array('key'=>"profile_name",'assign'=>"profile_name"),$_smarty_tpl) : '';?>

                        <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['_user']->value['_user_id'],'size'=>"small",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'title'=>$_smarty_tpl->tpl_vars['profile_name']->value,'class'=>"menu_user_image",'width'=>22,'height'=>22),$_smarty_tpl) : '';?>

                    </a>
                    <ul>
                        <?php echo (function_exists('smarty_function_jrCore_skin_menu')) ? smarty_function_jrCore_skin_menu(array('template'=>"menu.tpl",'category'=>"user"),$_smarty_tpl) : '';?>

                    </ul>
                </li>
            <?php }?>

            <?php echo (function_exists('smarty_function_jrSiteBuilder_menu')) ? smarty_function_jrSiteBuilder_menu(array('class'=>"desk"),$_smarty_tpl) : '';?>

        </ul>
    </nav>
</div>
<?php }
}
