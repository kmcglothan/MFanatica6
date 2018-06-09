<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:32
  from "/webserver/mf6/data/cache/jrCore/435908d0955452fa9b5d247cd11f0a5a^jrAudioPro^jrPayment_view_cart_button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb54018b5a1_57362193',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '071a338a47477031537c5fba07d82684ec967051' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/435908d0955452fa9b5d247cd11f0a5a^jrAudioPro^jrPayment_view_cart_button.tpl',
      1 => 1527493952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb54018b5a1_57362193 (Smarty_Internal_Template $_smarty_tpl) {
if (jrCore_is_mobile_device() || jrCore_is_tablet_device()) {?>
    <li class="left">
        <a onclick="jrPayment_view_cart()">
            <?php if ($_smarty_tpl->tpl_vars['item_count']->value > 0) {?>
                <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart"),$_smarty_tpl) : '';?>
 <span>(<?php echo $_smarty_tpl->tpl_vars['item_count']->value;?>
)</span></div>
            <?php } else { ?>
                <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart"),$_smarty_tpl) : '';?>
 <span></span></div>
            <?php }?>
        </a>
    </li>
<?php } else { ?>
    <li class="desk right">
        <a onclick="jrPayment_view_cart()">
            <?php if ($_smarty_tpl->tpl_vars['item_count']->value > 0) {?>
                <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart",'assign'=>"ct"),$_smarty_tpl) : '';
echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"cart44.png",'alt'=>$_smarty_tpl->tpl_vars['ct']->value),$_smarty_tpl) : '';?>
 <span>(<?php echo $_smarty_tpl->tpl_vars['item_count']->value;?>
)</span></div>
            <?php } else { ?>
                <div id="payment-view-cart-button"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPayment",'id'=>6,'default'=>"cart",'assign'=>"ct"),$_smarty_tpl) : '';
echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"cart44.png",'alt'=>$_smarty_tpl->tpl_vars['ct']->value),$_smarty_tpl) : '';?>
 <span></span></div>
            <?php }?>
        </a>
    </li>
<?php }
}
}
