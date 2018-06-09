<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/c7362ee9fca7667256cb556a997322c5^jrPlaylist^playlist_button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f86a90d0_71134213',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '622b678730b9857f9db29b61459623a562a36156' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c7362ee9fca7667256cb556a997322c5^jrPlaylist^playlist_button.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f86a90d0_71134213 (Smarty_Internal_Template $_smarty_tpl) {
?>

<div style="display: inline-block;" id="playlist_button_<?php echo $_smarty_tpl->tpl_vars['playlist_for']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['item_id']->value;?>
">
    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrPlaylist",'id'=>"2",'default'=>"add to playlist",'assign'=>"alt"),$_smarty_tpl) : '';?>

    <?php echo $_smarty_tpl->tpl_vars['icon_html']->value;?>

    <div id="playlist_<?php echo $_smarty_tpl->tpl_vars['playlist_for']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['item_id']->value;?>
" class="overlay playlist_box"><!-- playlist loads here --></div>
</div><?php }
}
