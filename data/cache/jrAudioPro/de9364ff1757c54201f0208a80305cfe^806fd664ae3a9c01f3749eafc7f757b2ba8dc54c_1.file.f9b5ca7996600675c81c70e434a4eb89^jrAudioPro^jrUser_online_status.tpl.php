<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:30
  from "/webserver/mf6/data/cache/jrCore/f9b5ca7996600675c81c70e434a4eb89^jrAudioPro^jrUser_online_status.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53e04db86_51687989',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '806fd664ae3a9c01f3749eafc7f757b2ba8dc54c' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/f9b5ca7996600675c81c70e434a4eb89^jrAudioPro^jrUser_online_status.tpl',
      1 => 1527493950,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53e04db86_51687989 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo '<script'; ?>
 type="text/javascript">
$(document).ready(function(){
    $.get('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/online_status/<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['seconds']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/__ajax=1', function(res) { $('#<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
').html(res); });
});
<?php echo '</script'; ?>
>
<div id="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"></div><?php }
}
