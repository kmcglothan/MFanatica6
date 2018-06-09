<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:10:46
  from "/webserver/mf6/data/cache/jrCore/04986494c658cce4296434901cf08769^jrCore^page_table_row.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0738665e2a97_13416072',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '730e0b13954e82c6d57712fda63a9f926f9cb3fc' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/04986494c658cce4296434901cf08769^jrCore^page_table_row.tpl',
      1 => 1527199846,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0738665e2a97_13416072 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['rownum']->value) && $_smarty_tpl->tpl_vars['rownum']->value%2 === 0) {?>
<tr class="page_table_row<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
<?php } else { ?>
<tr class="page_table_row_alt<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
<?php }
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cells']->value, '_cell', false, 'num');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['num']->value => $_smarty_tpl->tpl_vars['_cell']->value) {
?>
  <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
  <td class="page_table_cell <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
"<?php echo $_smarty_tpl->tpl_vars['_cell']->value['colspan'];?>
><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</td>
  <?php } else { ?>
  <td class="page_table_cell"<?php echo $_smarty_tpl->tpl_vars['_cell']->value['colspan'];?>
><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</td>
  <?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

</tr>
<?php }
}
