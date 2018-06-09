<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:09
  from "/webserver/mf6/data/cache/jrCore/abf52d9a834744a2f2177c82358de462^jrAudioPro^profile_item_detail.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f932f2f3_11260461',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6657227728d49c7fdc90138ce61451525037ccd1' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/abf52d9a834744a2f2177c82358de462^jrAudioPro^profile_item_detail.tpl',
      1 => 1526941689,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f932f2f3_11260461 (Smarty_Internal_Template $_smarty_tpl) {
?>

<?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_profile_side'] == 'left') {?>
    <?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"profile_sidebar.tpl"),$_smarty_tpl) : '';?>

    <?php $_smarty_tpl->_assignInScope('last', 'last');
}?>

<div class="col8 <?php echo $_smarty_tpl->tpl_vars['last']->value;?>
">
    <div class="wrap">
        <?php echo $_smarty_tpl->tpl_vars['profile_item_detail_content']->value;?>

    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_profile_side'] != 'left') {?>
    <?php $_smarty_tpl->_assignInScope('last', 'last');
?>
    <?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"profile_sidebar.tpl"),$_smarty_tpl) : '';?>

<?php }?>



<?php }
}
