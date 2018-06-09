<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/ebb103512e522d32ab0ada06a4d450ba^jrPayment^view_cart_button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b07324c8a76_31420872',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e19eb7aac73b81c53048605ede9d2cfbadea7c2f' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/ebb103512e522d32ab0ada06a4d450ba^jrPayment^view_cart_button.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b07324c8a76_31420872 (Smarty_Internal_Template $_smarty_tpl) {
?>
<li>
<a onclick="jrPayment_view_cart()">
    <?php if ($_smarty_tpl->tpl_vars['item_count']->value > 0) {?>
        <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart"),$_smarty_tpl) : '';?>
 <span><?php echo $_smarty_tpl->tpl_vars['item_count']->value;?>
</span></div>
    <?php } else { ?>
        <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart"),$_smarty_tpl) : '';?>
 <span style="display:none"></span></div>
    <?php }?>
</a>
</li>
<?php }
}
