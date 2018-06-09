<?php
/* Smarty version 3.1.31, created on 2018-05-26 08:19:04
  from "/webserver/mf6/data/cache/jrCore/2c8f419f9a940f1d3c2e11d3e212168e^jrAudioPro^menu.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b090a68115f06_43575132',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97858e6dae593b48893ff627c5fc4251879c15d2' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/2c8f419f9a940f1d3c2e11d3e212168e^jrAudioPro^menu.tpl',
      1 => 1527319144,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b090a68115f06_43575132 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'entry', false, NULL, 'loop', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['entry']->value) {
?>
    <?php if (isset($_smarty_tpl->tpl_vars['entry']->value['menu_function_result']) && strlen($_smarty_tpl->tpl_vars['entry']->value['menu_function_result']) > 0) {?>
        <?php if (is_numeric($_smarty_tpl->tpl_vars['entry']->value['menu_function_result'])) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
 [<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_function_result'];?>
]</a></li>
            <?php } else { ?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
 <img src="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_function_result'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
"></a></li>
        <?php }?>
        <?php } else { ?>
    <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
</a></li>
    <?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
