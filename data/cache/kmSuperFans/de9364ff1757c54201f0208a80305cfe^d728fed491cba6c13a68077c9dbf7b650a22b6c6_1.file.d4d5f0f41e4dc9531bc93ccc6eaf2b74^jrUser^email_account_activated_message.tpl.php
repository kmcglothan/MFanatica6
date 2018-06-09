<?php
/* Smarty version 3.1.31, created on 2018-05-23 04:09:57
  from "/webserver/mf6/data/cache/jrCore/d4d5f0f41e4dc9531bc93ccc6eaf2b74^jrUser^email_account_activated_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b04db851889a3_47646842',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd728fed491cba6c13a68077c9dbf7b650a22b6c6' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/d4d5f0f41e4dc9531bc93ccc6eaf2b74^jrUser^email_account_activated_message.tpl',
      1 => 1527044997,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b04db851889a3_47646842 (Smarty_Internal_Template $_smarty_tpl) {
?>
Thank you for signing up with <?php echo $_smarty_tpl->tpl_vars['system_name']->value;?>
!

Your account has been activated, and you can now log in:

<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser"),$_smarty_tpl) : '';?>
/login<?php }
}
