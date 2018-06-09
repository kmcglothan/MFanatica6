<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:29
  from "/webserver/mf6/data/cache/jrCore/7264cb91993a1273b59fa7c6400d6a1e^jrAudioPro^profile_sidebar.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53db26b55_07178550',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c3e6fcc126ac4fb39b1691492bd14f29e0c1949a' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/7264cb91993a1273b59fa7c6400d6a1e^jrAudioPro^profile_sidebar.tpl',
      1 => 1527493949,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53db26b55_07178550 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_modifier_replace')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.replace.php';
?>
<div class="col4 sidebar">
    <div>
        <div style="padding: 1.5em 1em 0;">
            <div>
                <div class="head">
                    <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"info",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>

                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>2,'default'=>"About"),$_smarty_tpl) : '';?>
</span>
                </div>
                <div class="profile_information">
                    <?php if (jrCore_module_is_active("jrFollower")) {?>
                        <div class="profile_data">
                            <?php echo (function_exists('smarty_function_jrAudioPro_stats')) ? smarty_function_jrAudioPro_stats(array('assign'=>"action_stats",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrFollower",'assign'=>"furl"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl) : '';?>

                            <?php if (isset($_smarty_tpl->tpl_vars['profile']->value['profile_url']) && strlen($_smarty_tpl->tpl_vars['profile']->value['profile_url']) > 0) {?>
                                <?php $_smarty_tpl->_assignInScope('purl', $_smarty_tpl->tpl_vars['profile']->value['profile_url']);
?>
                            <?php } else { ?>
                                <?php $_smarty_tpl->_assignInScope('purl', $_smarty_tpl->tpl_vars['profile_url']->value);
?>
                            <?php }?>
                            <ul class="clearfix">
                                <li onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['furl']->value;?>
')">
                                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>35,'default'=>"Followers"),$_smarty_tpl) : '';?>
</span>
                                    <?php echo $_smarty_tpl->tpl_vars['action_stats']->value['followers'];?>
</li>
                                <li onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['furl']->value;?>
/profiles_followed')">
                                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>45,'default'=>"Following"),$_smarty_tpl) : '';?>
</span>
                                    <?php echo $_smarty_tpl->tpl_vars['action_stats']->value['following'];?>
</li>
                                <li onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/timeline')">
                                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>44,'default'=>"Updates"),$_smarty_tpl) : '';?>
</span>
                                    <?php echo $_smarty_tpl->tpl_vars['action_stats']->value['actions'];?>
</li>
                            </ul>
                        </div>
                    <?php }?>

                    <?php if (strlen($_smarty_tpl->tpl_vars['profile_location']->value) > 0) {?>
                        <span><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"location",'size'=>"16",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['profile_location']->value,40);?>
</span>
                    <?php }?>
                    <?php if (strlen($_smarty_tpl->tpl_vars['profile_website']->value) > 0) {?>
                        <span><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"link",'size'=>"16",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['profile_website']->value;?>
"
                                                                                    target="_blank"><?php echo smarty_modifier_truncate(smarty_modifier_replace(smarty_modifier_replace($_smarty_tpl->tpl_vars['profile_website']->value,"http://",''),"https://",''),40);?>
</a></span>
                    <?php }?>
                    <span><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"calendar",'size'=>"16",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>36,'default'=>"Joined"),$_smarty_tpl) : '';?>
 <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['_created']->value,"%B %d, %Y");?>
</span>
                    <?php echo (function_exists('smarty_function_jrUser_online_status')) ? smarty_function_jrUser_online_status(array('profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl) : '';?>

                </div>
            </div>

            <?php if (strlen($_smarty_tpl->tpl_vars['profile_bio']->value) > 0) {?>
                <div class="wrap">
                    <div class="head">
                        <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"profile",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>

                        <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>37,'default'=>"Biography"),$_smarty_tpl) : '';?>
</span>
                    </div>
                    <div class="bio">
                        <?php echo smarty_modifier_truncate(smarty_modifier_jrCore_strip_html(smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['profile_bio']->value)),160);?>

                    </div>
                    <div class="bio-more">
                        <?php if (strlen($_smarty_tpl->tpl_vars['profile_bio']->value) > 160) {?>
                            <a class="full_bio"
                               onclick="jrAudioPro_modal('#bio_modal')"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>38,'default'=>"Read Full Biography"),$_smarty_tpl) : '';?>
</a>
                        <?php }?>
                    </div>
                    <div class="modal" id="bio_modal" style="display: none">
                        <div style="padding: 1em 1em 0">
                            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>37,'default'=>"Biography"),$_smarty_tpl) : '';?>

                            <div style="float: right;">
                                <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"close",'size'=>"22",'class'=>'simplemodal-close'),$_smarty_tpl) : '';?>

                            </div>
                        </div>
                        <div class="wrap">
                            <div style="max-height: 400px; overflow: auto">
                                <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['profile_bio']->value,$_smarty_tpl->tpl_vars['profile_quota_id']->value);?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php }?>

            <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrFollower",'search1'=>"follow_profile_id = ".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'search2'=>"follow_active = 1",'order_by'=>"_item_id desc",'limit'=>24,'assign'=>"followers"),$_smarty_tpl) : '';?>

            <?php if (strlen($_smarty_tpl->tpl_vars['followers']->value) > 0) {?>
                <div class="wrap">
                    <div class="head">
                        <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"followers",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>

                        <span><?php echo $_smarty_tpl->tpl_vars['action_stats']->value['followers'];?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>35,'default'=>"followers"),$_smarty_tpl) : '';?>
</span>
                    </div>
                    <div class="followers">
                        <?php echo $_smarty_tpl->tpl_vars['followers']->value;?>

                    </div>
                </div>
            <?php }?>

            <?php if (!jrCore_is_mobile_device()) {?>
                <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrRating",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'search1'=>"rating_image_size > 0",'order_by'=>"_updated desc",'limit'=>"24",'assign'=>"rated"),$_smarty_tpl) : '';?>

                <?php if (strlen($_smarty_tpl->tpl_vars['rated']->value) > 0) {?>
                    <div class="wrap">
                        <div class="head">
                            <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"star",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>

                            <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>"47",'default'=>"Rating"),$_smarty_tpl) : '';?>
</span>
                        </div>
                        <div class="followers">
                            <?php echo $_smarty_tpl->tpl_vars['rated']->value;?>

                        </div>
                    </div>
                <?php }?>
            <?php }?>

            <?php if (!jrCore_is_mobile_device()) {?>

                
                <?php if (strlen($_smarty_tpl->tpl_vars['tag_cloud']->value) > 0) {?>
                    <div class="wrap">
                        <div class="head">
                            <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"tag",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>

                            <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>"42",'default'=>"Tag"),$_smarty_tpl) : '';?>
</span>
                        </div>
                        <div class="followers">
                            <?php echo $_smarty_tpl->tpl_vars['tag_cloud']->value;?>

                        </div>
                    </div>
                <?php }?>
            <?php }?>
        </div>
    </div>
</div><?php }
}
