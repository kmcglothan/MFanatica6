<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:15
  from "/webserver/mf6/data/cache/jrCore/d259ba8e06932b754f86ac184d56502b^kmSuperFans^index_item_audio.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b07373fa589_84464975',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'de73c522ea732970f3b9fafb3517a010f6ef0f84' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/d259ba8e06932b754f86ac184d56502b^kmSuperFans^index_item_audio.tpl',
      1 => 1528497975,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b07373fa589_84464975 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <div class="list_item">
            <div class="table">
                <div class="table-row">
                    <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['audio_title']) > 0) {?>
                        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

                        <div class="table-cell" style="width:10%;">
                            <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'on' && $_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>"jrAudio_button",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                            <?php } else { ?>
                                &nbsp;
                            <?php }?>
                        </div>
                        <div class="table-cell" style="width: 50%">
                            <span class="index_title"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['audio_title'],20);?>
</a></span>
                            <span class="date"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album'];?>
</a></span>
                        </div>
                        <div class="table-cell">
                            <?php echo (function_exists('smarty_function_jrLike_button')) ? smarty_function_jrLike_button(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'module'=>"jrAudio",'action'=>"like"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrFoxyCart_add_to_cart",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                        </div>
                    <?php } else { ?>
                        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrSoundCloud",'assign'=>"murl"),$_smarty_tpl) : '';?>

                        <div class="table-cell" style="width:10%;">
                            <?php echo (function_exists('smarty_function_jrSoundCloud_player')) ? smarty_function_jrSoundCloud_player(array('params'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                        </div>
                        <div class="table-cell" style="width: 66%">
                            <span class="index_title"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_title_url'];?>
"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['soundcloud_title'],24);?>
</a></span>
                            <span class="date"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_title_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_artist'];?>
</a></span>
                        </div>
                        <div class="table-cell">
                            <?php echo (function_exists('smarty_function_jrLike_button')) ? smarty_function_jrLike_button(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'module'=>"jrSoundCloud",'action'=>"like"),$_smarty_tpl) : '';?>

                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php } else { ?>
    <div class="no-items">
        <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"62",'default'=>"No items found"),$_smarty_tpl) : '';?>
</h1>
        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl) : '';?>

        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_2'] == 'on') {?>
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"63",'default'=>"This list currently requires items to have a price set."),$_smarty_tpl) : '';?>

        <?php }?>
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/section=List+2')"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"64",'default'=>"Edit Configuration"),$_smarty_tpl) : '';?>
</button>
    </div>
<?php }
}
}
