<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:12
  from "/webserver/mf6/data/cache/jrCore/12605135ef79f91e3d4005b8d42fe3ae^kmSuperFans^index_item_2.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b07347ddc05_28181016',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '340f65dfdd39b8f00d7846a1d9033c4297e6d0b2' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/12605135ef79f91e3d4005b8d42fe3ae^kmSuperFans^index_item_2.tpl',
      1 => 1528497972,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b07347ddc05_28181016 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>


    <div class="row">

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['list_rank'] == 1) {?>

            <div class="col6">
                <div style="padding: 5px;">
                    <div class="featured_item">
                        <div class="cover_image">

                            <?php if ($_smarty_tpl->tpl_vars['item']->value['profile_header_image_size'] > 0) {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"  title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"Click to view"),$_smarty_tpl) : '';?>
">
                                    <?php if (jrCore_is_mobile_device()) {?>
                                        <?php $_smarty_tpl->_assignInScope('crop', "2:1");
?>
                                    <?php } else { ?>
                                        <?php $_smarty_tpl->_assignInScope('crop', "4:1");
?>
                                    <?php }?>
                                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_header_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"1280",'class'=>"img_scale",'crop'=>$_smarty_tpl->tpl_vars['crop']->value,'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'_v'=>$_smarty_tpl->tpl_vars['item']->value['profile_header_image_time']),$_smarty_tpl) : '';?>

                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"  title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"Click to view"),$_smarty_tpl) : '';?>
">
                                    <?php if (jrCore_is_mobile_device()) {?>
                                        <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"profile_header_image.jpg",'width'=>"1140",'class'=>"img_scale",'height'=>"auto"),$_smarty_tpl) : '';?>

                                    <?php } else { ?>
                                        <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"profile_header_image_large.jpg",'width'=>"1140",'class'=>"img_scale",'height'=>"auto"),$_smarty_tpl) : '';?>

                                    <?php }?>
                                </a>
                            <?php }?>

                            <div class="profile_info">
                                <div class="wrap">
                                    <div class="table">
                                        <div class="table-row">
                                            <div class="table-cell profile-image">
                                                <div class="profile_image">
                                                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"xxlarge",'crop'=>"auto",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>

                                                </div>
                                            </div>
                                            <div class="table-cell">
                                                <div class="profile_name">
                                                    <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_name'],55);?>
<br>
                                                    <span><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrap">

                            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_2'] == 'on') {?>
                                <?php $_smarty_tpl->_assignInScope('s1', "audio_file_item_price > 0");
?>
                            <?php }?>
                            <?php if (jrCore_module_is_active('jrCombinedAudio') && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_2_soundcloud'] == 'on') {?>
                                <?php echo (function_exists('smarty_function_jrCombinedAudio_get_active_modules')) ? smarty_function_jrCombinedAudio_get_active_modules(array('assign'=>"mods"),$_smarty_tpl) : '';?>

                                <?php if (strlen($_smarty_tpl->tpl_vars['mods']->value) > 0) {?>
                                    <?php echo (function_exists('smarty_function_jrSeamless_list')) ? smarty_function_jrSeamless_list(array('modules'=>$_smarty_tpl->tpl_vars['mods']->value,'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'order_by'=>"_created numerical_desc",'limit'=>"5",'template'=>"index_item_audio_large.tpl"),$_smarty_tpl) : '';?>

                                <?php } elseif (jrUser_is_admin()) {?>
                                    No active audio modules found!
                                <?php }?>
                            <?php } else { ?>
                                <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAudio",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'search'=>$_smarty_tpl->tpl_vars['s1']->value,'order_by'=>"audio_display_order desc",'limit'=>"5",'template'=>"index_item_audio_large.tpl"),$_smarty_tpl) : '';?>

                            <?php }?>

                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="col3">
                <div style="padding: 5px;">
                    <div class="featured_item">
                        <div class="cover_image">

                            <?php if ($_smarty_tpl->tpl_vars['item']->value['profile_header_image_size'] > 0) {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"  title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"Click to view"),$_smarty_tpl) : '';?>
">
                                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_header_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"1280",'class'=>"img_scale",'crop'=>"2:1",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'_v'=>$_smarty_tpl->tpl_vars['item']->value['profile_header_image_time']),$_smarty_tpl) : '';?>

                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"  title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"Click to view"),$_smarty_tpl) : '';?>
">
                                    <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"profile_header_image.jpg",'width'=>"1140",'class'=>"img_scale",'height'=>"auto",'crop'=>"3:1"),$_smarty_tpl) : '';?>

                                </a>
                            <?php }?>

                            <div class="profile_info">
                                <div class="wrap">
                                    <div class="table">
                                        <div class="table-row">
                                            <div class="table-cell profile-image">
                                                <div class="profile_image">
                                                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"xxlarge",'crop'=>"auto",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>

                                                </div>
                                            </div>
                                            <div class="table-cell">
                                                <div class="profile_name">
                                                    <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_name'],55);?>
<br>
                                                    <span><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrap">
                            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_2'] == 'on') {?>
                                <?php $_smarty_tpl->_assignInScope('s2', "audio_file_item_price > 0");
?>
                            <?php }?>
                            <?php if (jrCore_module_is_active('jrCombinedAudio') && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_2_soundcloud'] == 'on') {?>
                                <?php echo (function_exists('smarty_function_jrCombinedAudio_get_active_modules')) ? smarty_function_jrCombinedAudio_get_active_modules(array('assign'=>"mods"),$_smarty_tpl) : '';?>

                                <?php if (strlen($_smarty_tpl->tpl_vars['mods']->value) > 0) {?>
                                    <?php echo (function_exists('smarty_function_jrSeamless_list')) ? smarty_function_jrSeamless_list(array('modules'=>$_smarty_tpl->tpl_vars['mods']->value,'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'order_by'=>"*_display_order desc",'limit'=>"5",'template'=>"index_item_audio.tpl"),$_smarty_tpl) : '';?>

                                <?php } elseif (jrUser_is_admin()) {?>
                                    No active audio modules found!
                                <?php }?>
                            <?php } else { ?>
                                <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAudio",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'search'=>$_smarty_tpl->tpl_vars['s1']->value,'order_by'=>"audio_display_order desc",'limit'=>"5",'template'=>"index_item_audio.tpl"),$_smarty_tpl) : '';?>

                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['item']->value['list_rank'] == 3) {?>
            </div><div class="row">
        <?php }?>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </div>
<?php } else { ?>
    <div class="no-items">
        <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"62",'default'=>"No items found"),$_smarty_tpl) : '';?>
</h1>
        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl) : '';?>

        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/section=List+2')"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"64",'default'=>"Edit Configuration"),$_smarty_tpl) : '';?>
</button>
    </div>
<?php }?>


<?php }
}
