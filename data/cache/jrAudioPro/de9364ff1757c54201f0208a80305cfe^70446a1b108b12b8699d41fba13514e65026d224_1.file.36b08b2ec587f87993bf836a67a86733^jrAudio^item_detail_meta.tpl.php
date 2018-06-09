<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/36b08b2ec587f87993bf836a67a86733^jrAudio^item_detail_meta.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f82f67c1_17762241',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '70446a1b108b12b8699d41fba13514e65026d224' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/36b08b2ec587f87993bf836a67a86733^jrAudio^item_detail_meta.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f82f67c1_17762241 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrAudio",'image'=>"facebook_player_skin.jpg",'src_only'=>true,'assign'=>"skin_url"),$_smarty_tpl) : '';?>

<meta property="og:url" content="<?php echo $_smarty_tpl->tpl_vars['current_url']->value;?>
" />
<meta property="og:type" content="movie" />
<meta property="og:title" content="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['audio_title']);?>
" />
<?php if (isset($_smarty_tpl->tpl_vars['item']->value['audio_description'])) {
$_smarty_tpl->_assignInScope('title', ((string)$_smarty_tpl->tpl_vars['profile_name']->value).": ".((string)$_smarty_tpl->tpl_vars['item']->value['audio_title']));
?>
<meta property="og:description" content="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['audio_description']);?>
" />
<?php } else { ?>
<meta property="og:description" content="by <?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
" />
<?php }
if ($_smarty_tpl->tpl_vars['item']->value['audio_image_size'] > 0) {?>
<meta property="og:image" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/image/audio_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/xxlarge/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_image_time'];?>
" />
<?php } else { ?>
<meta property="og:image" content="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage"),$_smarty_tpl) : '';?>
/img/module/jrAudio/facebook_shared_icon.png" />
<?php }?>
<meta property="og:image:width" content="512"/>
<meta property="og:image:height" content="385"/>
<meta property="og:site_name" content="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
" />
<meta property="og:updated_time" content="<?php echo time();?>
" />


<meta name="twitter:card" content="player" />

<meta name="twitter:title" content="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['audio_title']);?>
" />
<?php if (isset($_smarty_tpl->tpl_vars['item']->value['audio_description'])) {?>
<meta name="twitter:description" content="<?php echo jrCore_entity_string(smarty_modifier_truncate(smarty_modifier_jrCore_strip_html($_smarty_tpl->tpl_vars['item']->value['audio_description']),180));?>
" />
<?php }
if ($_smarty_tpl->tpl_vars['item']->value['audio_image_size'] > 0) {?>
<meta name="twitter:image" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/image/audio_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/xxlarge/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_image_time'];?>
" />
<?php }?>
<meta name="twitter:player" content="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['jamroom_url']->value,"http:",((string)$_smarty_tpl->tpl_vars['method']->value).":");?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/embed/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
" />
<meta name="twitter:player:width" content="400" />
<meta name="twitter:player:height" content="215" />

<?php }
}
