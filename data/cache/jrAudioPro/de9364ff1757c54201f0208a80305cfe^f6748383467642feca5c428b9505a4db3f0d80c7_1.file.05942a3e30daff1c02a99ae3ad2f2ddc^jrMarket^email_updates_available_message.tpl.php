<?php
/* Smarty version 3.1.31, created on 2018-05-21 01:07:51
  from "/webserver/mf6/data/cache/jrCore/05942a3e30daff1c02a99ae3ad2f2ddc^jrMarket^email_updates_available_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b020dd7d09956_08289499',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f6748383467642feca5c428b9505a4db3f0d80c7' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/05942a3e30daff1c02a99ae3ad2f2ddc^jrMarket^email_updates_available_message.tpl',
      1 => 1526861271,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b020dd7d09956_08289499 (Smarty_Internal_Template $_smarty_tpl) {
?>
The following Marketplace Updates are available for your system:

<?php if (count($_smarty_tpl->tpl_vars['module']->value) > 0) {?>
modules:
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['module']->value, '_inf', false, 'mod');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['mod']->value => $_smarty_tpl->tpl_vars['_inf']->value) {
?>
    <?php echo $_smarty_tpl->tpl_vars['_inf']->value['module_name'];?>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
if (count($_smarty_tpl->tpl_vars['skin']->value) > 0) {?>
skins:
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['skin']->value, '_inf', false, 'dir');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['dir']->value => $_smarty_tpl->tpl_vars['_inf']->value) {
?>
    <?php echo $_smarty_tpl->tpl_vars['_inf']->value['title'];?>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }?>

You can install these new updates from your Marketplace:

<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrMarket"),$_smarty_tpl) : '';?>
/system_update
<?php }
}
