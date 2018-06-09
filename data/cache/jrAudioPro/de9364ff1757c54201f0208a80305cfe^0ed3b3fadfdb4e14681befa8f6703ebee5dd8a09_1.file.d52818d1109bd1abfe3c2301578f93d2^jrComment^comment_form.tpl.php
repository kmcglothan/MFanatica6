<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:09
  from "/webserver/mf6/data/cache/jrCore/d52818d1109bd1abfe3c2301578f93d2^jrComment^comment_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f90364d2_55302864',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0ed3b3fadfdb4e14681befa8f6703ebee5dd8a09' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/d52818d1109bd1abfe3c2301578f93d2^jrComment^comment_form.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f90364d2_55302864 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrComment",'assign'=>"curl"),$_smarty_tpl) : '';?>

<a id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_section"></a>
<a id="comment_section"></a>

<?php if (isset($_smarty_tpl->tpl_vars['jrComment']->value['comment_order_by'])) {?>
    <?php $_smarty_tpl->_assignInScope('dir', $_smarty_tpl->tpl_vars['jrComment']->value['comment_order_by']);
} else { ?>
    <?php $_smarty_tpl->_assignInScope('dir', $_smarty_tpl->tpl_vars['_conf']->value['jrComment_direction']);
}?>

<div id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_comments" class="comment_page_section">

    
    <?php $_smarty_tpl->_assignInScope('profile_owner_id', 0);
?>
    <?php if ($_smarty_tpl->tpl_vars['_user']->value['user_active_profile_id'] == $_smarty_tpl->tpl_vars['_item']->value['_profile_id'] && $_smarty_tpl->tpl_vars['_item']->value['quota_jrComment_profile_delete'] == 'on') {?>
        <?php $_smarty_tpl->_assignInScope('profile_owner_id', $_smarty_tpl->tpl_vars['_item']->value['_profile_id']);
?>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['jrComment']->value['pagebreak'] > 0) {?>
        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrComment",'search'=>"comment_item_ckey = ".((string)$_smarty_tpl->tpl_vars['jrComment']->value['item_id']).":".((string)$_smarty_tpl->tpl_vars['jrComment']->value['module']).":i",'order_by'=>"_item_id ".((string)$_smarty_tpl->tpl_vars['dir']->value),'profile_owner_id'=>$_smarty_tpl->tpl_vars['profile_owner_id']->value,'pagebreak'=>$_smarty_tpl->tpl_vars['_conf']->value['jrComment_pagebreak'],'page'=>1,'pager'=>true,'pager_template'=>"comment_pager.tpl"),$_smarty_tpl) : '';?>

    <?php } else { ?>
        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrComment",'search'=>"comment_item_ckey = ".((string)$_smarty_tpl->tpl_vars['jrComment']->value['item_id']).":".((string)$_smarty_tpl->tpl_vars['jrComment']->value['module']).":i",'order_by'=>"_item_id ".((string)$_smarty_tpl->tpl_vars['dir']->value),'limit'=>500,'profile_owner_id'=>$_smarty_tpl->tpl_vars['profile_owner_id']->value),$_smarty_tpl) : '';?>

    <?php }?>

</div>

<?php if (jrUser_is_logged_in() && $_smarty_tpl->tpl_vars['_user']->value['quota_jrComment_allowed'] == 'on') {?>

    <div id="comment_form_holder">
    <div id="comment_form_section">

        <div id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_notice" class="item error" style="display:none;">
            
        </div>

        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrComment_threading'] == 'on' && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor'] == 'on') {?>
        <div id="comment_reply_to" class="item success" style="display:none;">
            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrComment",'id'=>18,'default'=>"Your Reply To:"),$_smarty_tpl) : '';?>
 <strong><span id="comment_reply_to_user"></span></strong>
        </div>
        <?php }?>

        <div class="item" style="display:table">
            <div style="display:table-row">
                <div class="p5" style="display:table-cell;width:5%;vertical-align:top;">
                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['_user']->value['_user_id'],'size'=>"small",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img iloutline",'_v'=>$_smarty_tpl->tpl_vars['_user']->value['user_image_time']),$_smarty_tpl) : '';?>

                </div>
                <div class="p5" style="display:table-cell;width:95%;padding:5px 12px;">

                    <a id="cform"></a>
                    <form id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_form" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['curl']->value;?>
/comment_save" method="POST" onsubmit="jrPostComment('#<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
', 'undefined', 500);return false">

                        <input type="hidden" id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_module" name="comment_module" value="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['module'];?>
">
                        <input type="hidden" id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_profile_id" name="comment_profile_id" value="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['profile_id'];?>
">
                        <input type="hidden" id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_item_id" name="comment_item_id" value="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['item_id'];?>
">
                        <input type="hidden" id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_order_by" name="comment_order_by" value="<?php echo $_smarty_tpl->tpl_vars['dir']->value;?>
">
                        <input type="hidden" id="comment_parent_id" name="comment_parent_id" value="0">

                        <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor']) && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor'] == 'on' && !jrCore_is_mobile_device()) {?>
                            <?php echo (function_exists('smarty_function_jrCore_editor_field')) ? smarty_function_jrCore_editor_field(array('name'=>"comment_text"),$_smarty_tpl) : '';?>

                        <?php } else { ?>
                            <textarea id="comment_text" name="comment_text" cols="40" rows="5" class="form_textarea <?php echo $_smarty_tpl->tpl_vars['jrComment']->value['class'];?>
" style="height:64px;width:98%;<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['style'];?>
"></textarea><br>
                        <?php }?>
                        <div style="vertical-align:middle">
                            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working...",'assign'=>"working"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"form_spinner.gif",'id'=>((string)$_smarty_tpl->tpl_vars['jrComment']->value['unique_id'])."_fsi",'width'=>"24",'height'=>"24",'alt'=>$_smarty_tpl->tpl_vars['working']->value,'style'=>"margin:8px 8px 0px 8px;display:none"),$_smarty_tpl) : '';?>
<input id="<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['unique_id'];?>
_cm_submit" type="submit" value="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrComment",'id'=>"2",'default'=>"Save Comment"),$_smarty_tpl) : '';?>
" class="form_button <?php echo $_smarty_tpl->tpl_vars['jrComment']->value['class'];?>
" style="margin-top:8px;<?php echo $_smarty_tpl->tpl_vars['jrComment']->value['style'];?>
">
                        </div>

                        <?php if ($_smarty_tpl->tpl_vars['_user']->value['quota_jrComment_attachments'] == 'on') {?>
                        <div class="jrcomment_upload_attachment">
                            <?php echo (function_exists('smarty_function_jrCore_upload_button')) ? smarty_function_jrCore_upload_button(array('module'=>"jrComment",'field'=>"comment_file",'allowed'=>((string)$_smarty_tpl->tpl_vars['_user']->value['quota_jrComment_allowed_file_types']),'multiple'=>"true"),$_smarty_tpl) : '';?>

                        </div>
                        <?php }?>
                        <div style="clear:both"></div>

                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>

<?php } elseif (jrUser_is_logged_in() === false) {?>

    <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"url"),$_smarty_tpl) : '';?>

    <div class="item"><div class="p5"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/login"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrComment",'id'=>16,'default'=>"You must be logged in to post a comment"),$_smarty_tpl) : '';?>
</a></div></div>

<?php }
}
}
