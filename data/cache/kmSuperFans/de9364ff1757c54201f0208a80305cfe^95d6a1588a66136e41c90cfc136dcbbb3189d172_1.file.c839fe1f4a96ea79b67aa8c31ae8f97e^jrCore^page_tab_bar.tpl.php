<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:10:46
  from "/webserver/mf6/data/cache/jrCore/c839fe1f4a96ea79b67aa8c31ae8f97e^jrCore^page_tab_bar.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b073866518a82_96931587',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95d6a1588a66136e41c90cfc136dcbbb3189d172' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c839fe1f4a96ea79b67aa8c31ae8f97e^jrCore^page_tab_bar.tpl',
      1 => 1527199846,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b073866518a82_96931587 (Smarty_Internal_Template $_smarty_tpl) {
?>
<tr>
    <td colspan="2" class="page_tab_bar_holder">
        <ul class="page_tab_bar">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tabs']->value, 'tab');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tab']->value) {
?>
                <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['onclick'])) {?>
                    <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['active']) && $_smarty_tpl->tpl_vars['tab']->value['active'] == '1') {?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
 page_tab_active" onclick="<?php echo $_smarty_tpl->tpl_vars['tab']->value['onclick'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</li>
                    <?php } else { ?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
" onclick="<?php echo $_smarty_tpl->tpl_vars['tab']->value['onclick'];?>
"><a href=""><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a></li>
                    <?php }?>
                <?php } else { ?>
                    <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['active']) && $_smarty_tpl->tpl_vars['tab']->value['active'] == '1') {?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
 page_tab_active"><a href="<?php echo $_smarty_tpl->tpl_vars['tab']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a>
                        </li>
                    <?php } else { ?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['tab']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a></li>
                    <?php }?>
                <?php }?>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </ul>
    </td>
</tr>
<tr>
    <td colspan="2" class="page_tab_bar_spacer"></td>
</tr>
<?php }
}
