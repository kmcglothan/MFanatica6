<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:32
  from "/webserver/mf6/data/cache/jrCore/a86eca6625c7384a39f1eb17313ec995^jrAudioPro^profile_menu.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb54037d1f2_54635664',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '21e69e263b61d495125b86b8cbc5be7b3f275fe8' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/a86eca6625c7384a39f1eb17313ec995^jrAudioPro^profile_menu.tpl',
      1 => 1527493952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb54037d1f2_54635664 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <ul id="horizontal">

        <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['option']) && strlen($_smarty_tpl->tpl_vars['_post']->value['option']) > 0) {?>
            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>1,'default'=>"Home"),$_smarty_tpl) : '';?>
</a></li>
        <?php } else { ?>
            <li class="active"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>1,'default'=>"Home"),$_smarty_tpl) : '';?>
</a></li>
        <?php }?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'entry', false, 'module');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['module']->value => $_smarty_tpl->tpl_vars['entry']->value) {
?>
            <?php if ($_smarty_tpl->tpl_vars['entry']->value['active'] == '1') {?>
                <li class="active t<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
')"><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['label'];?>
</a></li>
            <?php } else { ?>
                <li class="t<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
')"><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['label'];?>
</a></li>
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


    </ul>
<?php }?>

<?php }
}
