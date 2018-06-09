<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:29
  from "/webserver/mf6/data/cache/jrCore/ad4c83ddc8cbef6ab80ac1b076cac029^jrAction^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53d28f6d0_39516688',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9d7b54de34b4f5549c9a5e0e938b5c115f4645b2' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/ad4c83ddc8cbef6ab80ac1b076cac029^jrAction^item_list.tpl',
      1 => 1527493948,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53d28f6d0_39516688 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>

    <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl) : '';?>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>

        <div id="action-item<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media">
                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"icon",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img img_shadow img_scale"),$_smarty_tpl) : '';?>

                        </div>
                    </div>

                    <div class="col10 last" style="position:relative">

                        <?php echo '<script'; ?>
 type="text/javascript">
                            $(function() {
                                var d = $('#action-controls<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
');
                                $('#action-item<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').hover(function()
                                {
                                    d.show();
                                }, function()
                                {
                                    d.hide();
                                });
                            });
                        <?php echo '</script'; ?>
>

                        <div id="action-controls<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_delete">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"link"),$_smarty_tpl) : '';?>
</a>
                            <?php echo (function_exists('smarty_function_jrCore_item_delete_button')) ? smarty_function_jrCore_item_delete_button(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>

                        </div>

                        <div>

                            <span class="action_item_title"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a></span>

                            <span class="action_item_actions">
                                &bull; <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created'],"relative");?>

                                <?php if (jrUser_is_logged_in() && $_smarty_tpl->tpl_vars['_user']->value['_user_id'] != $_smarty_tpl->tpl_vars['item']->value['_user_id'] && $_smarty_tpl->tpl_vars['item']->value['action_shared_by_user'] != '1') {?>
                                    &bull; <a onclick="jrAction_share('jrAction','<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
')"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"10",'default'=>"Share This"),$_smarty_tpl) : '';?>
</a>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['_post']->value['module_url'] == $_smarty_tpl->tpl_vars['_user']->value['profile_url'] && $_smarty_tpl->tpl_vars['item']->value['action_shared_by_user'] == '1') {?>
                                    &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"26",'default'=>"shared by you"),$_smarty_tpl) : '';?>
</a>
                                <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['action_shared_by_count'] > 0) {?>
                                    &bull; <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"24",'default'=>"shared by"),$_smarty_tpl) : '';?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['action_shared_by_count'];?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"25",'default'=>"follower(s)"),$_smarty_tpl) : '';?>
</a>
                                <?php }?>

                                
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['action_module'] != 'jrFollower') {?>
                                    <?php if (isset($_smarty_tpl->tpl_vars['item']->value['action_original_item_comment_count'])) {?>
                                        &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_item_url'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"22",'default'=>"Comments"),$_smarty_tpl) : '';?>
: <?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_item_comment_count'];?>
</a>
                                    <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_item_comment_count'])) {?>
                                        &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['action_item_url'];?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"22",'default'=>"Comments"),$_smarty_tpl) : '';?>
: <?php echo $_smarty_tpl->tpl_vars['item']->value['action_item_comment_count'];?>
</a>
                                    <?php }?>
                                <?php }?>

                            </span>
                            <br>

                            
                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['action_mode']) && $_smarty_tpl->tpl_vars['item']->value['action_mode'] == 'mention') {?>

                                <?php echo smarty_modifier_truncate(smarty_modifier_jrCore_strip_html(smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['action_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id'])),160);?>


                            
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_shared'])) {?>

                                <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['action_text']) > 0) {?>
                                <div class="action_item_text">
                                    <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['action_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']);?>

                                </div>
                                <?php }?>

                                <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['action_original_html']) > 0) {?>
                                <div class="action_item_shared">
                                    <?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_html'];?>

                                </div>
                                <?php }?>

                            
                            <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['action_module'] == 'jrAction' && isset($_smarty_tpl->tpl_vars['item']->value['action_text'])) {?>

                                <div class="action_item_text">
                                    <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['action_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']);?>

                                </div>

                            
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_html'])) {?>

                                <?php echo $_smarty_tpl->tpl_vars['item']->value['action_html'];?>


                            <?php }?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
}
