<?php
/* Smarty version 3.1.31, created on 2018-05-23 04:19:00
  from "/webserver/mf6/data/cache/jrCore/9dd46fd04b2b01968e4435a384ff2a94^jrUser^email_notify_signup_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b04dda4d1e754_63198765',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fb31f3d6d84d196cb84237fc7787892e4eb308fa' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/9dd46fd04b2b01968e4435a384ff2a94^jrUser^email_notify_signup_message.tpl',
      1 => 1527045540,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b04dda4d1e754_63198765 (Smarty_Internal_Template $_smarty_tpl) {
?>
A new user has just signed up on <?php echo $_smarty_tpl->tpl_vars['system_name']->value;?>
:

user name: <?php echo $_smarty_tpl->tpl_vars['user_name']->value;?>

email address: <?php echo $_smarty_tpl->tpl_vars['user_email']->value;?>

ip address: <?php echo $_smarty_tpl->tpl_vars['ip_address']->value;?>


You can view the new User Profile here:

<?php echo $_smarty_tpl->tpl_vars['new_profile_url']->value;?>


<?php if (isset($_smarty_tpl->tpl_vars['signup_method']->value) && $_smarty_tpl->tpl_vars['signup_method']->value == 'admin') {?>
Pending User Dashboard:

<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_base_url'];?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl) : '';?>
/dashboard/pending
<?php }?>



<?php }
}
