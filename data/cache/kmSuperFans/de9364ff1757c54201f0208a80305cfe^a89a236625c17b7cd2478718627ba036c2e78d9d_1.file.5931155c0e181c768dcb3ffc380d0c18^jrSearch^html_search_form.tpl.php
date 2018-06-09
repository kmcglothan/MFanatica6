<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/5931155c0e181c768dcb3ffc380d0c18^jrSearch^html_search_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b073273eb05_08312279',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a89a236625c17b7cd2478718627ba036c2e78d9d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/5931155c0e181c768dcb3ffc380d0c18^jrSearch^html_search_form.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b073273eb05_08312279 (Smarty_Internal_Template $_smarty_tpl) {
?>


<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"7",'default'=>"Search",'assign'=>"st"),$_smarty_tpl) : '';?>


<?php $_smarty_tpl->_assignInScope('form_name', "jrSearch");
?>
<div style="white-space:nowrap">
    <form action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/search/results/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['module'];?>
/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['page'];?>
/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['pagebreak'];?>
" method="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['method'];?>
" style="margin-bottom:0">
    <input id="search_input" type="text" name="search_string" style="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['style'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['class'];?>
" placeholder="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['jrSearch']->value['value']);?>
" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { $(this).closest('form').submit(); }">&nbsp;<input type="submit" class="form_button" value="<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
">
    </form>
</div>
<?php }
}
