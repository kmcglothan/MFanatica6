<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:30
  from "/webserver/mf6/data/cache/jrCore/a974f1cab3967820bca66b2c2c97db7d^jrFollower^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53e11abd8_69683810',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c1f5502f670f48902e3b43cd58e1548516417f03' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/a974f1cab3967820bca66b2c2c97db7d^jrFollower^item_list.tpl',
      1 => 1527493950,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53e11abd8_69683810 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php ob_start();
echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['user_name']);
$_prefixVariable1=ob_get_clean();
ob_start();
echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['user_name']);
$_prefixVariable2=ob_get_clean();
echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"small",'crop'=>"auto",'class'=>"img_shadow",'width'=>"40",'height'=>"40",'style'=>"padding:2px;margin-bottom:4px;",'alt'=>$_prefixVariable1,'title'=>$_prefixVariable2),$_smarty_tpl) : '';?>
</a>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
}
