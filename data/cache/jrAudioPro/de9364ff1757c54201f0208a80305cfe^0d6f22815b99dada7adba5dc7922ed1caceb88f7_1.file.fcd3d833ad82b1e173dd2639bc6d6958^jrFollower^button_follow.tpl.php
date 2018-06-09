<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:10
  from "/webserver/mf6/data/cache/jrCore/fcd3d833ad82b1e173dd2639bc6d6958^jrFollower^button_follow.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347fadc97f6_13859436',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0d6f22815b99dada7adba5dc7922ed1caceb88f7' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/fcd3d833ad82b1e173dd2639bc6d6958^jrFollower^button_follow.tpl',
      1 => 1526941690,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347fadc97f6_13859436 (Smarty_Internal_Template $_smarty_tpl) {
?>
<input type="button" id="follow" class="profile_button follow_button follow" name="follow" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" title="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['title']->value)===null||strlen($tmp)===0||$tmp==='' ? '' : $tmp);?>
" onclick="jrFollowProfile('follow',<?php echo $_smarty_tpl->tpl_vars['profile_id']->value;?>
);"><?php }
}
