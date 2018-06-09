<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:10:46
  from "/webserver/mf6/data/cache/jrCore/fe8c5f05e57213a5e917dda76312b91b^jrCore^page_table_footer.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b073866626885_29805879',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4074057f212c1b66b27526002ab4fd2441627990' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/fe8c5f05e57213a5e917dda76312b91b^jrCore^page_table_footer.tpl',
      1 => 1527199846,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b073866626885_29805879 (Smarty_Internal_Template $_smarty_tpl) {
if (is_array($_smarty_tpl->tpl_vars['cells']->value)) {?>
<tr class="nodrag nodrop">
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cells']->value, '_cell');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_cell']->value) {
?>
    <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
        <th class="page_table_footer <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
    <?php } else { ?>
        <th class="page_table_footer" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
    <?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

</tr>
<?php }?>

</table>
</td>
</tr>    
<?php }
}
