<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:10:46
  from "/webserver/mf6/data/cache/jrCore/f7b6fad3b964b0aefffb7003ed167299^jrCore^page_table_header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b07386659d0f3_96487416',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f536ac8755492cfb62c2c250dc23994c5918d858' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/f7b6fad3b964b0aefffb7003ed167299^jrCore^page_table_header.tpl',
      1 => 1527199846,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b07386659d0f3_96487416 (Smarty_Internal_Template $_smarty_tpl) {
if (!$_smarty_tpl->tpl_vars['inline']->value) {?>
<tr>
    <td colspan="2">
        <table class="page_table<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
<?php }?>
        <?php if (count($_smarty_tpl->tpl_vars['cells']->value) > 0) {?>
            <tr class="nodrag nodrop">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cells']->value, '_cell');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_cell']->value) {
?>
            <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
                <th class="page_table_header <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
            <?php } else { ?>
                <th class="page_table_header" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
            <?php }?>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </tr>
        <?php }
}
}
