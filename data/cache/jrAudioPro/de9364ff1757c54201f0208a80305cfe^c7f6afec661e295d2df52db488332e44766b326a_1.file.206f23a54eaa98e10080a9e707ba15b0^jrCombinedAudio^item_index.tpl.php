<?php
/* Smarty version 3.1.31, created on 2018-05-21 07:30:35
  from "/webserver/mf6/data/cache/jrCore/206f23a54eaa98e10080a9e707ba15b0^jrCombinedAudio^item_index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02678b45fe76_68345396',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c7f6afec661e295d2df52db488332e44766b326a' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/206f23a54eaa98e10080a9e707ba15b0^jrCombinedAudio^item_index.tpl',
      1 => 1526884235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02678b45fe76_68345396 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCombinedAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

<div class="block">

    <div class="title">
        <div class="block_config">
            <?php echo (function_exists('smarty_function_jrCore_item_index_buttons')) ? smarty_function_jrCore_item_index_buttons(array('module'=>"jrCombinedAudio",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl) : '';?>

        </div>
        <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCombinedAudio",'id'=>1,'default'=>"Audio"),$_smarty_tpl) : '';?>
</h1><br>
        <div class="breadcrumbs">
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/"><?php echo $_smarty_tpl->tpl_vars['profile_name']->value;?>
</a> &raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCombinedAudio",'id'=>1,'default'=>"Audio"),$_smarty_tpl) : '';?>
</a>
        </div>
    </div>

    <div class="block_content">
        <?php echo (function_exists('smarty_function_jrCombinedAudio_get_active_modules')) ? smarty_function_jrCombinedAudio_get_active_modules(array('assign'=>"mods"),$_smarty_tpl) : '';?>

        <?php if (strlen($_smarty_tpl->tpl_vars['mods']->value) > 0) {?>
            <?php echo (function_exists('smarty_function_jrSeamless_list')) ? smarty_function_jrSeamless_list(array('modules'=>$_smarty_tpl->tpl_vars['mods']->value,'search'=>"_profile_id = ".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'order_by'=>"*_display_order numerical_asc",'pagebreak'=>6,'page'=>$_smarty_tpl->tpl_vars['_post']->value['p'],'pager'=>true),$_smarty_tpl) : '';?>

        <?php } elseif (jrUser_is_admin()) {?>
            No active audio modules found!
        <?php }?>
    </div>

</div>
<?php }
}
