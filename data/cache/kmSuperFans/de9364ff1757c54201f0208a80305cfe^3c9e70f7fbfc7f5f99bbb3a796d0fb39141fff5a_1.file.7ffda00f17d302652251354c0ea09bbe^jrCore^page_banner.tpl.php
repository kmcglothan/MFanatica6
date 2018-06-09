<?php
/* Smarty version 3.1.31, created on 2018-05-26 08:18:42
  from "/webserver/mf6/data/cache/jrCore/7ffda00f17d302652251354c0ea09bbe^jrCore^page_banner.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b090a52450c31_78541733',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3c9e70f7fbfc7f5f99bbb3a796d0fb39141fff5a' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/7ffda00f17d302652251354c0ea09bbe^jrCore^page_banner.tpl',
      1 => 1527319122,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b090a52450c31_78541733 (Smarty_Internal_Template $_smarty_tpl) {
?>
<tr>
    <td colspan="2" class="page_banner_box">
        <table class="page_banner">
            <tr>
                <?php if (strlen($_smarty_tpl->tpl_vars['icon_url']->value) > 0) {?>
                    <?php if (jrUser_is_master()) {?>
                        <?php echo (function_exists('smarty_function_jrCore_get_module_index')) ? smarty_function_jrCore_get_module_index(array('module'=>$_smarty_tpl->tpl_vars['_post']->value['module'],'assign'=>"url"),$_smarty_tpl) : '';?>

                        <td class="page_banner_icon"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_post']->value['module_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="icon" height="32" width="32"></a></td>
                    <?php } else { ?>
                        <td class="page_banner_icon"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="icon" height="32" width="32"></td>
                    <?php }?>
                    <td class="page_banner_left"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</td>
                    <td class="page_banner_right" style="width:69%"><?php echo $_smarty_tpl->tpl_vars['subtitle']->value;?>
</td>
                <?php } else { ?>
                    <td class="page_banner_left"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</td>
                    <td class="page_banner_right"><?php echo $_smarty_tpl->tpl_vars['subtitle']->value;?>
</td>
                <?php }?>
            </tr>
        </table>
    </td>
</tr>
<?php }
}
