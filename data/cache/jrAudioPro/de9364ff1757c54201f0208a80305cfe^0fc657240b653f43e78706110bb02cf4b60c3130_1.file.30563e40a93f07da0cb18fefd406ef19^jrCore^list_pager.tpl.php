<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:29
  from "/webserver/mf6/data/cache/jrCore/30563e40a93f07da0cb18fefd406ef19^jrCore^list_pager.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53d5eebb7_42358112',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0fc657240b653f43e78706110bb02cf4b60c3130' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/30563e40a93f07da0cb18fefd406ef19^jrCore^list_pager.tpl',
      1 => 1527493949,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53d5eebb7_42358112 (Smarty_Internal_Template $_smarty_tpl) {
?>

<?php if ($_smarty_tpl->tpl_vars['info']->value['total_items'] > 0 && ($_smarty_tpl->tpl_vars['info']->value['prev_page'] > 0 || $_smarty_tpl->tpl_vars['info']->value['next_page'] > 0)) {?>
    <div class="block">
        <table style="width:100%">
            <tr>
                <td style="width:25%">
                    <?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page'] > 0 && $_smarty_tpl->tpl_vars['info']->value['prev_page'] != $_smarty_tpl->tpl_vars['info']->value['this_page']) {?>
                        <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                            <a onclick="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
')"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"previous"),$_smarty_tpl) : '';?>
</a>
                        <?php } else { ?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"previous"),$_smarty_tpl) : '';?>
</a>
                        <?php }?>
                    <?php }?>
                </td>
                <td style="width:50%;text-align:center">
                    <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages'] > 1 && (!isset($_smarty_tpl->tpl_vars['pager_show_jumper']->value) || $_smarty_tpl->tpl_vars['pager_show_jumper']->value == '1')) {?>
                        <form name="form" method="post" action="_self">
                            <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=' + $(this).val());">
                            <?php } else { ?>
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="window.location='<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=' + $(this).val();">
                            <?php }?>
                            <?php
$_smarty_tpl->tpl_vars['pages'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);
$_smarty_tpl->tpl_vars['pages']->value = 1;
if ($_smarty_tpl->tpl_vars['pages']->value <= $_smarty_tpl->tpl_vars['info']->value['total_pages']) {
for ($_foo=true;$_smarty_tpl->tpl_vars['pages']->value <= $_smarty_tpl->tpl_vars['info']->value['total_pages']; $_smarty_tpl->tpl_vars['pages']->value++) {
?>
                                <?php if ($_smarty_tpl->tpl_vars['info']->value['this_page'] == $_smarty_tpl->tpl_vars['pages']->value) {?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['info']->value['this_page'];?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['pages']->value;?>
</option>
                                <?php } else { ?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['pages']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['pages']->value;?>
</option>
                                <?php }?>
                            <?php }
}
?>

                            </select>&nbsp;/&nbsp;<?php echo $_smarty_tpl->tpl_vars['info']->value['total_pages'];?>

                        </form>
                    <?php } else { ?>
                        <?php echo $_smarty_tpl->tpl_vars['info']->value['this_page'];?>

                    <?php }?>
                </td>
                <td style="width:25%;text-align:right">
                    <?php if ($_smarty_tpl->tpl_vars['info']->value['next_page'] > 0) {?>
                        <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                            <a onclick="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
')"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"next"),$_smarty_tpl) : '';?>
</a>
                        <?php } else { ?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"next"),$_smarty_tpl) : '';?>
</a>
                        <?php }?>
                    <?php }?>
                </td>
            </tr>
        </table>
    </div>
<?php }
}
}
