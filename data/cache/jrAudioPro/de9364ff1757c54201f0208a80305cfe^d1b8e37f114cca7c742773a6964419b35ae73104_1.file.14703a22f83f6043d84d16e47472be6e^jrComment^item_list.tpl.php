<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:09
  from "/webserver/mf6/data/cache/jrCore/14703a22f83f6043d84d16e47472be6e^jrComment^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f92a2029_47753336',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd1b8e37f114cca7c742773a6964419b35ae73104' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/14703a22f83f6043d84d16e47472be6e^jrComment^item_list.tpl',
      1 => 1526941689,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f92a2029_47753336 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrComment",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>

<?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrComment_threading'] == 'on' && isset($_smarty_tpl->tpl_vars['item']->value['comment_thread_level']) && $_smarty_tpl->tpl_vars['item']->value['comment_thread_level'] > 0) {?>
    <?php if ($_smarty_tpl->tpl_vars['item']->value['comment_thread_level'] > 7) {?>
        <div id="cm<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="item comment-level-last">
    <?php } else { ?>
        <div id="cm<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="item comment-level-<?php echo $_smarty_tpl->tpl_vars['item']->value['comment_thread_level'];?>
">
    <?php }
} else { ?>
    <div id="cm<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="item comment-level-0">
<?php }?>

    <div class="container">
        <div class="row">
            <div class="col1">
                <div class="block_image p5">
                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"small",'crop'=>"portrait",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img iloutline img_scale",'style'=>"max-width:70px;max-height:70px;margin:8px;"),$_smarty_tpl) : '';?>
                </div>
            </div>
            <div class="col11 last" style="position:relative">

                <div id="c<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="p5" style="margin-left:12px">

                    <span class="info" style="display:inline-block;"><?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created']);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><span style="text-transform:lowercase">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</span></a>:</span><br>
                    <span class="normal comment_text">
                    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor']) && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor'] == 'on') {?>
                        <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['comment_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id'],null,"nl2br");?>

                    <?php } else { ?>
                        <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['comment_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id'],null,"html");?>

                    <?php }?>
                    </span>

                    <?php if (jrUser_is_logged_in() && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_threading'] == 'on') {?>
                        <br><a onclick="jrComment_reply_to(<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
, '<?php echo addslashes($_smarty_tpl->tpl_vars['item']->value['user_name']);?>
')"><span class="comment-reply">reply</span></a>
                        <div id="r<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" style="display:none">
                            
                        </div>
                    <?php }?>

                    <br/>

                    
                    <?php echo (function_exists('smarty_function_jrCore_get_uploaded_files')) ? smarty_function_jrCore_get_uploaded_files(array('module'=>"jrComment",'item'=>$_smarty_tpl->tpl_vars['item']->value,'field'=>"comment_file"),$_smarty_tpl) : '';?>


                </div>

                <?php if (jrUser_is_logged_in()) {?>
                <div id="bc<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="block_config" style="position:absolute;top:0;right:0;display:none">
                    <?php echo '<script'; ?>
>$(function() { var bc = $('#bc<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
'); $('#cm<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').hover(function() { bc.show(); }, function() { bc.hide(); } ); }); <?php echo '</script'; ?>
>

                    <?php if ($_smarty_tpl->tpl_vars['item']->value['comment_locked'] != 'on') {?>
                        <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrComment_quote_button']) && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_quote_button'] == 'on') {?>
                            <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor']) && $_smarty_tpl->tpl_vars['_conf']->value['jrComment_editor'] == 'on') {?>
                                <a onclick="jrCommentEditorQuotePost(<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
);" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrComment",'id'=>26,'default'=>"quote this"),$_smarty_tpl) : '';?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"quote"),$_smarty_tpl) : '';?>
</a>
                            <?php } else { ?>
                                <a onclick="jrCommentQuotePost(<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
);" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrComment",'id'=>26,'default'=>"quote this"),$_smarty_tpl) : '';?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"quote"),$_smarty_tpl) : '';?>
</a>
                            <?php }?>
                        <?php }?>
                     <?php }?>

                    <?php if (jrUser_is_admin() || !isset($_smarty_tpl->tpl_vars['item']->value['comment_locked'])) {?>
                        <?php if ($_smarty_tpl->tpl_vars['_params']->value['profile_owner_id'] > 0) {?>
                            
                            <?php echo (function_exists('smarty_function_jrCore_item_update_button')) ? smarty_function_jrCore_item_update_button(array('module'=>"jrComment",'profile_id'=>$_smarty_tpl->tpl_vars['_params']->value['profile_owner_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_item_delete_button')) ? smarty_function_jrCore_item_delete_button(array('module'=>"jrComment",'profile_id'=>$_smarty_tpl->tpl_vars['_params']->value['profile_owner_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>

                        <?php } else { ?>
                            
                            <?php echo (function_exists('smarty_function_jrCore_item_update_button')) ? smarty_function_jrCore_item_update_button(array('module'=>"jrComment",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_item_delete_button')) ? smarty_function_jrCore_item_delete_button(array('module'=>"jrComment",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>

                        <?php }?>
                    <?php }?>

                </div>
                <?php }?>


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
