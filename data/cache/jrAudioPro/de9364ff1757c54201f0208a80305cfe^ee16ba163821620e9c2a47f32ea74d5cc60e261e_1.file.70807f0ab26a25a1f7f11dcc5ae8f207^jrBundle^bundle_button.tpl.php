<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/70807f0ab26a25a1f7f11dcc5ae8f207^jrBundle^bundle_button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f8744203_33852447',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ee16ba163821620e9c2a47f32ea74d5cc60e261e' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/70807f0ab26a25a1f7f11dcc5ae8f207^jrBundle^bundle_button.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f8744203_33852447 (Smarty_Internal_Template $_smarty_tpl) {
?>

<div style="display: inline-block;" id="bundle_button_<?php echo $_smarty_tpl->tpl_vars['item_id']->value;?>
">
    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrBundle",'id'=>"25",'default'=>"add to bundle",'assign'=>"alt"),$_smarty_tpl) : '';?>

    <?php echo $_smarty_tpl->tpl_vars['icon_html']->value;?>

    <div id="bundle_<?php echo $_smarty_tpl->tpl_vars['item_id']->value;?>
" class="overlay bundle_box" style="display:none"><!-- bundle loads here --></div>
</div><?php }
}
