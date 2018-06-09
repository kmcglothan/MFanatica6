<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:30
  from "/webserver/mf6/data/cache/jrCore/1645c1d400940e7158fb688fc3b6e025^jrRating^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53e4720a0_75486908',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6e366a514d6cb9d007b325286bc7a95470ee1d12' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/1645c1d400940e7158fb688fc3b6e025^jrRating^item_list.tpl',
      1 => 1527493950,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53e4720a0_75486908 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>$_smarty_tpl->tpl_vars['item']->value['rating_module'],'assign'=>"murl"),$_smarty_tpl) : '';?>

        <?php echo (function_exists('smarty_function_jrCore_get_datastore_prefix')) ? smarty_function_jrCore_get_datastore_prefix(array('module'=>$_smarty_tpl->tpl_vars['item']->value['rating_module'],'assign'=>"prefix"),$_smarty_tpl) : '';?>

        <?php $_smarty_tpl->_assignInScope('item_title', ((string)$_smarty_tpl->tpl_vars['prefix']->value)."_title");
?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['rating_data']['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['rating_item_id'];?>
/<?php echo jrCore_url_string($_smarty_tpl->tpl_vars['item']->value['rating_data'][$_smarty_tpl->tpl_vars['item_title']->value]);?>
">
            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>$_smarty_tpl->tpl_vars['item']->value['rating_module'],'type'=>((string)$_smarty_tpl->tpl_vars['prefix']->value)."_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['rating_item_id'],'size'=>"xsmall",'crop'=>"auto",'class'=>"img_shadow",'style'=>"padding:2px;margin-bottom:4px;",'title'=>((string)$_smarty_tpl->tpl_vars['item']->value['rating_data'][$_smarty_tpl->tpl_vars['item_title']->value])." rated a ".((string)$_smarty_tpl->tpl_vars['item']->value['rating_value']),'alt'=>((string)$_smarty_tpl->tpl_vars['item']->value['rating_data'][$_smarty_tpl->tpl_vars['item_title']->value])." rated a ".((string)$_smarty_tpl->tpl_vars['item']->value['rating_value']),'width'=>false,'height'=>false),$_smarty_tpl) : '';?>
</a>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
}
