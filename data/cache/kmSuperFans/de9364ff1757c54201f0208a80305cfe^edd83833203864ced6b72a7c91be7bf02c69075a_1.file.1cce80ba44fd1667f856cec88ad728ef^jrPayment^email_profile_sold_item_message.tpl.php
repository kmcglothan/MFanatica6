<?php
/* Smarty version 3.1.31, created on 2018-05-21 01:48:29
  from "/webserver/mf6/data/cache/jrCore/1cce80ba44fd1667f856cec88ad728ef^jrPayment^email_profile_sold_item_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02175dd3c692_33867126',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'edd83833203864ced6b72a7c91be7bf02c69075a' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/1cce80ba44fd1667f856cec88ad728ef^jrPayment^email_profile_sold_item_message.tpl',
      1 => 1526863709,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02175dd3c692_33867126 (Smarty_Internal_Template $_smarty_tpl) {
?>
Congratulations - you have sold items!

Items:
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
echo $_smarty_tpl->tpl_vars['item']->value['cart_module_url'];?>
 - <?php echo $_smarty_tpl->tpl_vars['item']->value['item_name'];?>

<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

Buyer:
<?php echo $_smarty_tpl->tpl_vars['_buyer']->value['user_name'];?>
 (@<?php echo $_smarty_tpl->tpl_vars['_buyer']->value['profile_name'];?>
)

You can view the transaction online for more details:

<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrPayment",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_items']->value[0]['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/payments
<?php }
}
