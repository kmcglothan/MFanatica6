<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:15
  from "/webserver/mf6/data/cache/jrCore/137eed14f092993774effe6d8e0acb1e^kmSuperFans^index_chart_item.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b073773aee7_84670679',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cdfe58ae280c1e4b4c79714a775df2a001d5bdc0' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/137eed14f092993774effe6d8e0acb1e^kmSuperFans^index_chart_item.tpl',
      1 => 1528497975,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b073773aee7_84670679 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php $_smarty_tpl->_assignInScope('rank', 0);
?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
        <?php $_smarty_tpl->_assignInScope('class', '');
?>
        <?php $_smarty_tpl->_assignInScope('rank', $_smarty_tpl->tpl_vars['rank']->value+1);
?>
        <?php if ($_smarty_tpl->tpl_vars['rank']->value%2 == 0) {?>
            <?php $_smarty_tpl->_assignInScope('class', ' odd');
?>
        <?php }?>
        <div class="list_item<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
            <div class="table">
                <div class="table-row">
                    <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['audio_title']) > 0) {?>
                        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

                        <div class="table-cell" style="width: 30px; text-align: center">
                            <?php echo $_smarty_tpl->tpl_vars['item']->value['chart_position'];?>

                        </div>
                        <div class="table-cell" style="width: 28px; text-align: center;">
                            <?php $_smarty_tpl->_assignInScope('color', '777777');
?>
                            <?php $_smarty_tpl->_assignInScope('icon', "chart_same");
?>

                            <?php if ($_smarty_tpl->tpl_vars['item']->value['chart_new_entry'] == 'yes') {?>
                                <?php $_smarty_tpl->_assignInScope('color', '339933');
?>
                                <?php $_smarty_tpl->_assignInScope('icon', 'chart_up');
?>
                            <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['chart_direction'] == 'same') {?>
                                <?php $_smarty_tpl->_assignInScope('icon', "chart_same");
?>
                            <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['chart_direction'] == 'up') {?>
                                <?php $_smarty_tpl->_assignInScope('icon', 'chart_up');
?>
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['chart_change'] > 5) {?>
                                    <?php $_smarty_tpl->_assignInScope('color', 'FF5500');
?>
                                <?php }?>
                            <?php } else { ?>
                                <?php $_smarty_tpl->_assignInScope('icon', 'chart_down');
?>
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['chart_change'] > 5) {?>
                                    <?php $_smarty_tpl->_assignInScope('color', '3393ff');
?>
                                <?php }?>
                            <?php }?>

                            <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>$_smarty_tpl->tpl_vars['icon']->value,'size'=>"24",'color'=>$_smarty_tpl->tpl_vars['color']->value,'title'=>'hi'),$_smarty_tpl) : '';?>

                        </div>
                        <div class="table-cell" style="width: 30px; text-align: center">

                            <?php if ($_smarty_tpl->tpl_vars['item']->value['chart_new_entry'] == 'yes') {?>
                                &mdash;
                            <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['chart_direction'] == 'same') {?>
                                <?php echo $_smarty_tpl->tpl_vars['item']->value['chart_position'];?>

                            <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['chart_direction'] == 'up') {?>
                                <?php echo $_smarty_tpl->tpl_vars['item']->value['chart_position']+$_smarty_tpl->tpl_vars['item']->value['chart_change'];?>

                            <?php } else { ?>
                                <?php echo $_smarty_tpl->tpl_vars['item']->value['chart_position']-$_smarty_tpl->tpl_vars['item']->value['chart_change'];?>

                            <?php }?>

                        </div>
                        <div class="table-cell desk" style="width: 30px; text-align: center">
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
                        </div>
                        <div class="table-cell" style="width: 22px">
                            <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'on' && $_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>"jrAudio_button",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                            <?php } else { ?>
                                &nbsp;
                            <?php }?>
                        </div>
                        <div class="table-cell">
                                <span class="index_title"><a
                                            href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['audio_title'],40);?>
</a></span>
                        </div>
                        <div class="table-cell desk" style="width:200px;">
                                <span class="date"><a
                                            href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_name'],24);?>
</a></span>
                        </div>
                        <div class="table-cell desk" style="width: 100px">
                                <span class="date"><a
                                            href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_genre'];?>
</a></span>
                        </div>
                        <div class="table-cell desk" style="width: 60px; text-align: right">
                            <span class="date"><?php echo jrCore_number_format($_smarty_tpl->tpl_vars['item']->value['chart_count']);?>
</span>
                        </div>
                        <div class="table-cell chart_buttons" style="width: 130px">
                            <?php echo (function_exists('smarty_function_jrLike_button')) ? smarty_function_jrLike_button(array('item'=>$_smarty_tpl->tpl_vars['item']->value,'module'=>"jrAudio",'action'=>"like"),$_smarty_tpl) : '';?>

                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrFoxyCart_add_to_cart",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

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

        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_3'] == 'on') {?>
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
