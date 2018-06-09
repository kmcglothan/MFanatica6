<?php
/* Smarty version 3.1.31, created on 2018-05-21 07:30:35
  from "/webserver/mf6/data/cache/jrCore/6c63950229a2b261e9fc078c95824c0c^jrSeamless^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02678b556565_80489596',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61a29e0d0877e427e15e5390df94fa6a21fff38f' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/6c63950229a2b261e9fc078c95824c0c^jrSeamless^item_list.tpl',
      1 => 1526884235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02678b556565_80489596 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
    <?php if (is_file(((string)$_smarty_tpl->tpl_vars['jamroom_dir']->value)."/modules/".((string)$_smarty_tpl->tpl_vars['item']->value['seamless_module_name'])."/templates/item_list.tpl")) {?>
        <?php echo (function_exists('smarty_function_jrSeamless_parse_template')) ? smarty_function_jrSeamless_parse_template(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'template'=>"item_list.tpl",'module'=>$_smarty_tpl->tpl_vars['item']->value['seamless_module_name']),$_smarty_tpl) : '';?>

    <?php } elseif (jrUser_is_admin()) {?>
        item_list.tpl for <?php echo $_smarty_tpl->tpl_vars['item']->value['seamless_module_name'];?>
 not found<br>
    <?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
}
