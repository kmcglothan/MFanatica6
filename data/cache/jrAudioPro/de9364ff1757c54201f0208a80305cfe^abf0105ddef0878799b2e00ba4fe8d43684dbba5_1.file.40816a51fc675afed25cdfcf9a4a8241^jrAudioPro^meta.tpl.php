<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:30
  from "/webserver/mf6/data/cache/jrCore/40816a51fc675afed25cdfcf9a4a8241^jrAudioPro^meta.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53e7a1f76_64641119',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'abf0105ddef0878799b2e00ba4fe8d43684dbba5' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/40816a51fc675afed25cdfcf9a4a8241^jrAudioPro^meta.tpl',
      1 => 1527493950,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53e7a1f76_64641119 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_capitalize')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.capitalize.php';
?>
<!doctype html>
<html lang="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"lang",'default'=>"en"),$_smarty_tpl) : '';?>
" dir="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"direction",'default'=>"ltr"),$_smarty_tpl) : '';?>
">
<head><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>"1",'assign'=>"default_title"),$_smarty_tpl) : '';?>

<title><?php echo smarty_modifier_capitalize((($tmp = @$_smarty_tpl->tpl_vars['page_title']->value)===null||strlen($tmp)===0||$tmp==='' ? ((string)$_smarty_tpl->tpl_vars['default_title']->value) : $tmp));?>
 | <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if (isset($_smarty_tpl->tpl_vars['meta']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['meta']->value, 'mvalue', false, 'mname');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['mname']->value => $_smarty_tpl->tpl_vars['mvalue']->value) {
?>
<meta name="<?php echo $_smarty_tpl->tpl_vars['mname']->value;?>
" content="<?php echo $_smarty_tpl->tpl_vars['mvalue']->value;?>
">
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }?>
<link rel="stylesheet" href="<?php echo (function_exists('smarty_function_jrCore_server_protocol')) ? smarty_function_jrCore_server_protocol(array(),$_smarty_tpl) : '';?>
://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700" type="text/css">
<link rel="stylesheet" href="<?php echo (function_exists('smarty_function_jrCore_css_src')) ? smarty_function_jrCore_css_src(array(),$_smarty_tpl) : '';?>
" media="screen" type="text/css">
<?php if (isset($_smarty_tpl->tpl_vars['css_href']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['css_href']->value, '_css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_css']->value) {
?>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['_css']->value['source'];?>
" media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_css']->value['media'])===null||strlen($tmp)===0||$tmp==='' ? "screen" : $tmp);?>
" type="text/css">
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
if (isset($_smarty_tpl->tpl_vars['css_embed']->value)) {?>
<style type="text/css">
<?php echo $_smarty_tpl->tpl_vars['css_embed']->value;?>
</style>
<?php }
if (isset($_smarty_tpl->tpl_vars['javascript_embed']->value)) {
echo '<script'; ?>
 type="text/javascript">
<?php echo $_smarty_tpl->tpl_vars['javascript_embed']->value;
echo '</script'; ?>
>
<?php }
echo '<script'; ?>
 type="text/javascript" src="<?php echo (function_exists('smarty_function_jrCore_javascript_src')) ? smarty_function_jrCore_javascript_src(array(),$_smarty_tpl) : '';?>
"><?php echo '</script'; ?>
>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_href']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['javascript_href']->value, '_js');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_js']->value) {
echo '<script'; ?>
 type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_js']->value['type'])===null||strlen($tmp)===0||$tmp==='' ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['_js']->value['source'];?>
"><?php echo '</script'; ?>
>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
if (isset($_smarty_tpl->tpl_vars['javascript_ready_function']->value)) {
echo '<script'; ?>
 type="text/javascript">
$(document).ready(function(){
<?php echo $_smarty_tpl->tpl_vars['javascript_ready_function']->value;?>
return true;
});
<?php echo '</script'; ?>
>
<?php }?>

</head>
<?php }
}
