<?php
/* Smarty version 3.1.31, created on 2018-06-08 05:13:03
  from "/webserver/mf6/data/cache/jrCore/1d17d9c901bc2dc022736322409719ba^jrMarket^email_updates_available_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1a024fbb9c48_12681295',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0e2b0a0b58a08e9e6b7d858b2c1e419749ff521d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/1d17d9c901bc2dc022736322409719ba^jrMarket^email_updates_available_message.tpl',
      1 => 1528431183,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1a024fbb9c48_12681295 (Smarty_Internal_Template $_smarty_tpl) {
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
