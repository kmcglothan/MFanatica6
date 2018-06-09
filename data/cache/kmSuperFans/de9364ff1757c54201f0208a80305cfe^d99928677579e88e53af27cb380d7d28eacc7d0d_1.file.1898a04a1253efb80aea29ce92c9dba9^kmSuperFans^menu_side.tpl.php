<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/1898a04a1253efb80aea29ce92c9dba9^kmSuperFans^menu_side.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b073264b796_07041096',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd99928677579e88e53af27cb380d7d28eacc7d0d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/1898a04a1253efb80aea29ce92c9dba9^kmSuperFans^menu_side.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b073264b796_07041096 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">

            <li class="left"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
">Home</a></li>

            
            <?php echo (function_exists('smarty_function_jrSiteBuilder_menu')) ? smarty_function_jrSiteBuilder_menu(array(),$_smarty_tpl) : '';?>


            <?php if (jrCore_module_is_active('jrSearch')) {?>
                <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>36,'default'=>"Search",'assign'=>"st"),$_smarty_tpl) : '';?>

                <li><a onclick="jrSearch_modal_form()" title="<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
">Search</a></li>
            <?php }?>

            
            <?php if (jrCore_module_is_active('jrFoxyCart') && strlen($_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_api_key']) > 0) {?>
                <li>
                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>35,'default'=>"Cart",'assign'=>"ct"),$_smarty_tpl) : '';?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_store_domain'];?>
/cart?cart=view"><?php echo $_smarty_tpl->tpl_vars['ct']->value;?>
</a>
                    <span id="fc_minicart" style="display:none"><span id="fc_quantity"></span></span>
                </li>
            <?php }?>

            
            <?php if (jrUser_is_master()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl) : '';?>

                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrMarket",'assign'=>"murl"),$_smarty_tpl) : '';?>

                <?php echo (function_exists('smarty_function_jrCore_get_module_index')) ? smarty_function_jrCore_get_module_index(array('module'=>"jrCore",'assign'=>"url"),$_smarty_tpl) : '';?>

                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/global">
                        <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>29,'default'=>"Admin Control Panel"),$_smarty_tpl) : '';?>

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
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl) : '';?>
/dashboard"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"28",'default'=>"dashboard"),$_smarty_tpl) : '';?>
</a>
                </li>
            <?php }?>

            <?php if (jrUser_is_logged_in() && jrCore_module_is_active('jrChat') && jrUser_get_profile_home_key('quota_jrChat_allowed') == 'on') {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrChat",'assign'=>"curl"),$_smarty_tpl) : '';?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['curl']->value;?>
/mobile"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrChat",'id'=>46,'default'=>"Chat"),$_smarty_tpl) : '';?>
</a>
            <?php }?>

            
            <?php if (jrUser_is_logged_in()) {?>
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrUser_home_profile_key')) ? smarty_function_jrUser_home_profile_key(array('key'=>"profile_url"),$_smarty_tpl) : '';?>
">
                        <?php echo (function_exists('smarty_function_jrUser_home_profile_key')) ? smarty_function_jrUser_home_profile_key(array('key'=>"profile_name"),$_smarty_tpl) : '';?>

                    </a>
                    <ul>
                        <?php echo (function_exists('smarty_function_jrCore_skin_menu')) ? smarty_function_jrCore_skin_menu(array('template'=>"menu.tpl",'category'=>"user"),$_smarty_tpl) : '';?>

                    </ul>
                </li>
            <?php }?>

        </ul>
    </nav>
</div>

<div id="sb-site">
<?php }
}
