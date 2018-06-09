<?php
/* Smarty version 3.1.31, created on 2018-05-21 01:48:29
  from "/webserver/mf6/data/cache/jrCore/a3cf8167142393697ecf0ec11d1da37b^jrPayment^email_purchase_receipt_message.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02175debcac1_11920698',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29c2ae0b9d40dec27a7f3f5502d2a8693a864993' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/a3cf8167142393697ecf0ec11d1da37b^jrPayment^email_purchase_receipt_message.tpl',
      1 => 1526863709,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02175debcac1_11920698 (Smarty_Internal_Template $_smarty_tpl) {
?>
Thank you for your <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
 purchase!

You will find your downloadable items have been loaded into the "Your Purchases" section:
<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrPayment",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/purchases

<?php if ($_smarty_tpl->tpl_vars['ship_notice']->value === true) {?>
You will be receiving a separate email with shipping instructions that also contain details on the progress of your order and how to contact the seller.
<?php }?>

Thank you for your purchase!
<?php }
}
