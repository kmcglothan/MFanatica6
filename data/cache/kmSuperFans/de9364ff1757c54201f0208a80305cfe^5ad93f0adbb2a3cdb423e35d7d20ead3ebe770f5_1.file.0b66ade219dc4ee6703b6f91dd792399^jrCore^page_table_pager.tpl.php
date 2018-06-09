<?php
/* Smarty version 3.1.31, created on 2018-05-22 05:45:21
  from "/webserver/mf6/data/cache/jrCore/0b66ade219dc4ee6703b6f91dd792399^jrCore^page_table_pager.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b03a061a72896_30453542',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ad93f0adbb2a3cdb423e35d7d20ead3ebe770f5' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/0b66ade219dc4ee6703b6f91dd792399^jrCore^page_table_pager.tpl',
      1 => 1526964321,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b03a061a72896_30453542 (Smarty_Internal_Template $_smarty_tpl) {
?>
<tr class="nodrag nodrop">
    <td colspan="<?php echo $_smarty_tpl->tpl_vars['colspan']->value;?>
">
        <table class="page_table_pager">
            <tr>

                <td class="page_table_pager_left">
                    <?php if (isset($_smarty_tpl->tpl_vars['prev_page_num']->value) && $_smarty_tpl->tpl_vars['prev_page_num']->value > 0) {?>
                        <input type="button" value="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>26,'default'=>"&lt;"),$_smarty_tpl) : '';?>
" class="form_button" onclick="window.location='<?php echo $_smarty_tpl->tpl_vars['prev_page_url']->value;?>
'">
                    <?php }?>
                </td>

                <td nowrap="nowrap" class="page_table_pager_center">
                    <?php if ($_smarty_tpl->tpl_vars['total_pages']->value > 0) {?>
                        <?php if (strlen($_smarty_tpl->tpl_vars['page_select']->value) > 0) {?>
                            <?php echo $_smarty_tpl->tpl_vars['page_jumper']->value;?>
 / <?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>
 &nbsp;&nbsp;&nbsp; <?php echo $_smarty_tpl->tpl_vars['page_select']->value;?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>143,'default'=>"per page"),$_smarty_tpl) : '';?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->tpl_vars['page_jumper']->value;?>
 / <?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>

                        <?php }?>
                    <?php } else { ?>
                        <?php if (strlen($_smarty_tpl->tpl_vars['page_select']->value) > 0) {?>
                            <?php echo $_smarty_tpl->tpl_vars['page_jumper']->value;?>
 &nbsp; <?php echo $_smarty_tpl->tpl_vars['page_select']->value;?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>143,'default'=>"per page"),$_smarty_tpl) : '';?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->tpl_vars['page_jumper']->value;?>

                        <?php }?>
                    <?php }?>
                </td>

                <td class="page_table_pager_right">
                    <?php if (isset($_smarty_tpl->tpl_vars['next_page_num']->value) && $_smarty_tpl->tpl_vars['next_page_num']->value > 1) {?>
                        <input type="button" value="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>27,'default'=>"&gt;"),$_smarty_tpl) : '';?>
" class="form_button" onclick="window.location='<?php echo $_smarty_tpl->tpl_vars['next_page_url']->value;?>
'">
                    <?php }?>
                </td>

            </tr>
        </table>
    </td>
</tr>
<?php }
}
