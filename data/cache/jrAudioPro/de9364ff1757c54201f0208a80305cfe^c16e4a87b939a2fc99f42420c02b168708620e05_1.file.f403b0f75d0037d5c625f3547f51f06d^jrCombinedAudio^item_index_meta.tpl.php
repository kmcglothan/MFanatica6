<?php
/* Smarty version 3.1.31, created on 2018-05-21 07:30:35
  from "/webserver/mf6/data/cache/jrCore/f403b0f75d0037d5c625f3547f51f06d^jrCombinedAudio^item_index_meta.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02678b21c282_76288041',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c16e4a87b939a2fc99f42420c02b168708620e05' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/f403b0f75d0037d5c625f3547f51f06d^jrCombinedAudio^item_index_meta.tpl',
      1 => 1526884235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02678b21c282_76288041 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.replace.php';
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl) : '';?>



<meta property="og:url" content="<?php echo $_smarty_tpl->tpl_vars['current_url']->value;?>
"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="Audio - <?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
"/>
<meta property="og:description" content="Check out the full list here"/>
<?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_image_size']) && $_smarty_tpl->tpl_vars['item']->value['profile_image_size'] > 100) {?>
<meta property="og:image" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/image/profile_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/xxlarge/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_image_time'];?>
"/>
<?php } else { ?>
<meta property="og:image" content="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage"),$_smarty_tpl) : '';?>
/img/module/jrAudio/facebook_shared_icon.png"/>
<?php }?>
<meta property="og:image:width" content="256"/>
<meta property="og:image:height" content="256"/>
<meta property="og:site_name" content="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
"/>
<meta property="og:updated_time" content="<?php echo $_smarty_tpl->tpl_vars['item']->value['_updated'];?>
"/>


<meta name="twitter:title" content="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
"/>
<meta name="twitter:description" content="Check out the full list here"/>
<?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_image_size']) && $_smarty_tpl->tpl_vars['item']->value['profile_image_size'] > 100) {?>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:image" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/image/profile_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/xxlarge/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_image_time'];?>
"/>
<meta name="twitter:image:src" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/image/profile_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/xxlarge/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_image_time'];?>
"/>
<meta name="twitter:image:alt" content="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
"/>
<?php } else { ?>
<meta name="twitter:card" content="summary"/>
<?php }
}
}
