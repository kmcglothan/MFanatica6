<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/b618882f0d4ab7be3df640cb3221b4b3^kmSuperFans^index_item_1.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b0732b03fa8_28847357',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95862b43ce76cccf5052e26e909a6bf1b005de6e' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/b618882f0d4ab7be3df640cb3221b4b3^kmSuperFans^index_item_1.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b0732b03fa8_28847357 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php $_smarty_tpl->_assignInScope('rank', 0);
?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <?php $_smarty_tpl->_assignInScope('rank', $_smarty_tpl->tpl_vars['rank']->value+1);
?>
        <?php if ($_smarty_tpl->tpl_vars['rank']->value%8 == 1) {?>
            <div class="row">
        <?php }?>

        <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['audio_title']) > 0) {?>
            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

            <div class="index_item">
                <div class="wrap">
                    <div class="image">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
">
                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrAudio",'type'=>"audio_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'size'=>"xlarge",'crop'=>"auto",'class'=>"img_scale",'alt'=>$_smarty_tpl->tpl_vars['item']->value['audio_title'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>
</a>
                    </div>
                    <span class="item_title"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['audio_title'],20);?>
</span>
                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"by"),$_smarty_tpl) : '';?>
 <?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</span>
                    <span><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['audio_album'],20);?>
</span>
                    <span><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_genre'];?>
</span>
                    <ul class="index_buttons">
                        <li><?php if ($_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'on' && $_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>"jrAudio_button",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                            <?php }?>
                        </li>
                        <li>
                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrFoxyCart_add_to_cart",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                        </li>
                        <li><?php echo (function_exists('smarty_function_jrLike_button')) ? smarty_function_jrLike_button(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'module'=>"jrAudio",'action'=>"like",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>
</li>
                    </ul>
                </div>
            </div>
        <?php } else { ?>
            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrSoundCloud",'assign'=>"murl"),$_smarty_tpl) : '';?>

            <div class="index_item">
                <div class="wrap">
                    <div class="image">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_title_url'];?>
">
                            <img class="img_scale" src="<?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_artwork_url'];?>
">
                        </a>
                    </div>
                    <span class="item_title"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['soundcloud_title'],20);?>
</span>
                    <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"34",'default'=>"by"),$_smarty_tpl) : '';?>
 <?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</span>
                    <span><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['soundcloud_artist'],20);?>
</span>
                    <span><?php echo $_smarty_tpl->tpl_vars['item']->value['soundcloud_genre'];?>
</span>
                    <ul class="index_buttons">
                        <li>
                            <?php echo (function_exists('smarty_function_jrSoundCloud_player')) ? smarty_function_jrSoundCloud_player(array('params'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                        </li>

                        <li><?php echo (function_exists('smarty_function_jrLike_button')) ? smarty_function_jrLike_button(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'module'=>"jrSoundCloud",'action'=>"like",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl) : '';?>
</li>
                    </ul>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['rank']->value%8 == 0 || $_smarty_tpl->tpl_vars['rank']->value == $_smarty_tpl->tpl_vars['info']->value['total_items']) {?>
            </div>
        <?php }?>

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

        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price'] == 'on') {?>
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"63",'default'=>"This list currently requires items to have a price set."),$_smarty_tpl) : '';?>

        <?php }?>
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/section=List+1')"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"64",'default'=>"Edit Configuration"),$_smarty_tpl) : '';?>
</button>
    </div>
<?php }
}
}
