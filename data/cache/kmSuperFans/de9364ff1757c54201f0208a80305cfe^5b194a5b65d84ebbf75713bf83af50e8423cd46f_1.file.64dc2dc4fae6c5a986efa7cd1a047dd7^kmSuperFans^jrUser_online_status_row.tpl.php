<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:33
  from "/webserver/mf6/data/cache/jrCore/64dc2dc4fae6c5a986efa7cd1a047dd7^kmSuperFans^jrUser_online_status_row.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb5419a3ac2_75524961',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5b194a5b65d84ebbf75713bf83af50e8423cd46f' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/64dc2dc4fae6c5a986efa7cd1a047dd7^kmSuperFans^jrUser_online_status_row.tpl',
      1 => 1527493953,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb5419a3ac2_75524961 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['_items']->value) && is_array($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['user_is_online'] == '1') {?>
            <?php $_smarty_tpl->_assignInScope('online', '1');
?>
        <?php }?>
        <?php if (jrCore_checktype($_smarty_tpl->tpl_vars['item']->value['user_birthdate'],'number_nz')) {?>
            <span><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"birthday",'size'=>"16",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>59,'default'=>"Birthday"),$_smarty_tpl) : '';?>
 <?php echo smarty_modifier_jrCore_date_birthday_format($_smarty_tpl->tpl_vars['item']->value['user_birthdate'],"%B %d");?>
</span>
        <?php }?>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    <?php if ($_smarty_tpl->tpl_vars['online']->value == '1') {?>
         <span class="online_status"> <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"online",'size'=>"16",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrUser",'id'=>"101",'default'=>"online"),$_smarty_tpl) : '';?>
</span>
    <?php } else { ?>
        <span class="online_status"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"online",'size'=>"16",'color'=>"333333"),$_smarty_tpl) : '';?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrUser",'id'=>"102",'default'=>"offline"),$_smarty_tpl) : '';?>
</span>
    <?php }
}
}
}
