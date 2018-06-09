<?php
/* Smarty version 3.1.31, created on 2018-05-21 01:48:30
  from "/webserver/mf6/data/cache/jrCore/8bc6dd16ecdc96d4967489b47f3a673c^jrPayment^email_system_sold_items_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02175e10fea6_12196867',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '79ede8500b494bca15da286bd453fb6c49a7f0cb' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/8bc6dd16ecdc96d4967489b47f3a673c^jrPayment^email_system_sold_items_message.tpl',
      1 => 1526863710,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02175e10fea6_12196867 (Smarty_Internal_Template $_smarty_tpl) {
?>
Your site has sold items:

Items:
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
 - <?php echo $_smarty_tpl->tpl_vars['item']->value['cart_module_url'];?>
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

Details on the transaction can be found at the following URL:
<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrPayment",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/txn_detail/<?php echo $_smarty_tpl->tpl_vars['_cart']->value['txn_id'];?>


<?php }
}
