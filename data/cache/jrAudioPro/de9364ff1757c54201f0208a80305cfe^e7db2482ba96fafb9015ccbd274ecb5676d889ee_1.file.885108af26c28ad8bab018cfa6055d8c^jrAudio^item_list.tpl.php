<?php
/* Smarty version 3.1.31, created on 2018-05-21 07:30:36
  from "/webserver/mf6/data/cache/jrCore/885108af26c28ad8bab018cfa6055d8c^jrAudio^item_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02678c019328_71624222',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e7db2482ba96fafb9015ccbd274ecb5676d889ee' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/885108af26c28ad8bab018cfa6055d8c^jrAudio^item_list.tpl',
      1 => 1526884235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02678c019328_71624222 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>

    <div class="item">
        <div class="container">
            <div class="row">

                <div class="col2">
                    <div class="block_image" style="position:relative">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
"><?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrAudio",'type'=>"audio_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'size'=>"xlarge",'crop'=>"auto",'class'=>"iloutline img_scale",'alt'=>$_smarty_tpl->tpl_vars['item']->value['audio_title'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>
</a>
                        <div style="position:absolute;bottom:8px;right:5px">
                            <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'on' && $_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>"jrAudio_button",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                            <?php } else { ?>
                                &nbsp;
                            <?php }?>
                        </div>
                    </div>
                </div>

                <div class="col5">
                    <div class="p5">
                        <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>
</a></h2><br>
                        <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"31",'default'=>"album"),$_smarty_tpl) : '';?>
:</span> <span class="info_c"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album'];?>
</a></span><br>
                        <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"12",'default'=>"genre"),$_smarty_tpl) : '';?>
:</span> <span class="info_c"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_genre'];?>
</span><br>
                        <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrRating_form",'type'=>"star",'module'=>"jrAudio",'index'=>"1",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'current'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_average_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp),'votes'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp)),$_smarty_tpl) : '';?>

                    </div>
                </div>

                <div class="col5 last">
                    <div class="block_config">
                        <?php echo (function_exists('smarty_function_jrCore_item_list_buttons')) ? smarty_function_jrCore_item_list_buttons(array('module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


<?php }
}
}
