<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:30
  from "/webserver/mf6/data/cache/jrCore/90e097d39c7e221e4474ab89a314e4c2^jrAudioPro^profile_header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53e59d185_43534129',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ed73f9d2f72a37896cef98a4388615d26a89ceff' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/90e097d39c7e221e4474ab89a314e4c2^jrAudioPro^profile_header.tpl',
      1 => 1527493950,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53e59d185_43534129 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"header.tpl"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"murl"),$_smarty_tpl) : '';?>


<section id="profile">

    
    <?php $_smarty_tpl->_assignInScope('crop', "4:1");
?>
    <?php if (jrCore_is_mobile_device()) {?>
        <?php $_smarty_tpl->_assignInScope('crop', "3:2");
?>
    <?php }?>

    <div id="profile_header">
        <div class="clearfix" style="position: relative;">
            <?php if ($_smarty_tpl->tpl_vars['profile_header_image_size']->value > 0) {?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/image/profile_header_image/<?php echo $_smarty_tpl->tpl_vars['_profile_id']->value;?>
/1280" data-lightbox="profile_header" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>8,'default'=>"Click to see full image"),$_smarty_tpl) : '';?>
">
                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_header_image",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'size'=>"1280",'class'=>"img_scale",'crop'=>$_smarty_tpl->tpl_vars['crop']->value,'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'_v'=>$_smarty_tpl->tpl_vars['profile_header_image_time']->value),$_smarty_tpl) : '';?>

                </a>
            <?php } else { ?>
                <?php if (jrCore_is_mobile_device()) {?>
                    <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"profile_header_image.jpg",'width'=>"800",'class'=>"img_scale",'height'=>"auto"),$_smarty_tpl) : '';?>

                <?php } else { ?>
                    <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"profile_header_image_large.jpg",'width'=>"1280",'class'=>"img_scale",'height'=>"auto"),$_smarty_tpl) : '';?>

                <?php }?>
            <?php }?>
            <div class="profile_hover"></div>
            <?php if (jrProfile_is_profile_owner($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
                <div class="profile_admin_buttons">
                    <div class="row">
                        <div class="col6">
                            <div class="wrap">
                                <a class="camera" href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_base_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/settings/profile_id=<?php echo $_smarty_tpl->tpl_vars['_profile_id']->value;?>
">
                                    <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"camera2",'size'=>"32",'color'=>"ffffff"),$_smarty_tpl) : '';?>

                                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>67,'default'=>"Update Cover Image"),$_smarty_tpl) : '';?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }?>
            <div class="profile_info">
                <div class="wrap">
                    <div class="table">
                        <div class="table-row">
                            <div class="table-cell profile-image">
                                <div class="profile_image">
                                    <?php if (jrProfile_is_profile_owner($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
                                        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl) : '';?>

                                        <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>5,'default'=>"Edit",'assign'=>"hover"),$_smarty_tpl) : '';?>

                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_base_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/settings/profile_id=<?php echo $_smarty_tpl->tpl_vars['_profile_id']->value;?>
">
                                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'size'=>"xlarge",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'crop'=>"auto",'title'=>$_smarty_tpl->tpl_vars['hover']->value,'width'=>false,'height'=>false),$_smarty_tpl) : '';?>
</a>
                                        <div class="profile_hoverimage">
                                            <span class="normal"><?php echo $_smarty_tpl->tpl_vars['hover']->value;?>
</span><br>
                                            <?php echo (function_exists('smarty_function_jrCore_item_update_button')) ? smarty_function_jrCore_item_update_button(array('module'=>"jrProfile",'view'=>"settings/profile_id=".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Edit",'color'=>"ffffff"),$_smarty_tpl) : '';?>

                                        </div>
                                    <?php } else { ?>
                                        <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'size'=>"xxlarge",'crop'=>"auto",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'width'=>false,'height'=>false),$_smarty_tpl) : '';?>

                                    <?php }?>
                                </div>
                            </div>
                            <div class="table-cell">
                                <div class="profile_name">
                                    <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['profile_name']->value,55);?>
<br>
                                    <span><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
">@<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
</a> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="profile_menu" style="overflow: visible">
        <div class="menu_banner clearfix">
            <div class="table">
                <div class="table-row">
                    <div class="table-cell">
                        <?php $_smarty_tpl->_assignInScope('menu_template', "profile_menu.tpl");
?>
                        <?php if (jrCore_is_mobile_device() || jrCore_is_tablet_device()) {?>
                            <?php $_smarty_tpl->_assignInScope('menu_template', "profile_menu_mobile.tpl");
?>
                        <?php }?>
                        <?php echo (function_exists('smarty_function_jrProfile_menu')) ? smarty_function_jrProfile_menu(array('template'=>$_smarty_tpl->tpl_vars['menu_template']->value,'profile_quota_id'=>$_smarty_tpl->tpl_vars['profile_quota_id']->value,'profile_url'=>$_smarty_tpl->tpl_vars['profile_url']->value,'order'=>"jrAction,jrBlog,jrCombinedAudio,jrAudio,jrCombinedVideo,jrVideo,jrGallery,jrGroup,jrEvent,jrYouTube,jrVimeo,jrFlickr"),$_smarty_tpl) : '';?>

                    </div>
                    <div class="table-cell" style="width: 20px; white-space: nowrap; padding: 0 10px;">
                        <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('id'=>5,'skin'=>"jrAudioPro",'default'=>"Follow",'assign'=>"Follow"),$_smarty_tpl) : '';?>

                        <?php echo (function_exists('smarty_function_jrFollower_button')) ? smarty_function_jrFollower_button(array('profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>$_smarty_tpl->tpl_vars['follow']->value),$_smarty_tpl) : '';?>

                        <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>5,'default'=>"Edit",'assign'=>"edit"),$_smarty_tpl) : '';?>

                        <?php echo (function_exists('smarty_function_jrCore_item_update_button')) ? smarty_function_jrCore_item_update_button(array('module'=>"jrProfile",'view'=>"settings/profile_id=".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>$_smarty_tpl->tpl_vars['edit']->value),$_smarty_tpl) : '';?>

                        <?php if (jrUser_is_admin() || jrUser_is_power_user()) {?>
                            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>"6",'default'=>"Create Profile",'assign'=>"create"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_item_create_button')) ? smarty_function_jrCore_item_create_button(array('module'=>"jrProfile",'view'=>"create",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>$_smarty_tpl->tpl_vars['create']->value),$_smarty_tpl) : '';?>

                        <?php }?>
                        <?php echo (function_exists('smarty_function_jrProfile_delete_button')) ? smarty_function_jrProfile_delete_button(array('profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl) : '';?>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row profile_body">

<?php }
}
