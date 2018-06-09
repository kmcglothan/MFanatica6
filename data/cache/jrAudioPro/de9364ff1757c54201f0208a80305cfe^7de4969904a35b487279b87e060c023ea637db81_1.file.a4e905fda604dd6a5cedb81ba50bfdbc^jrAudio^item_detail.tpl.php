<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/a4e905fda604dd6a5cedb81ba50bfdbc^jrAudio^item_detail.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f8625a04_31916569',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7de4969904a35b487279b87e060c023ea637db81' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/a4e905fda604dd6a5cedb81ba50bfdbc^jrAudio^item_detail.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f8625a04_31916569 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"iurl"),$_smarty_tpl) : '';?>

<?php $_smarty_tpl->_assignInScope('skin_player_type', ((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."_player_type");
$_smarty_tpl->_assignInScope('player_type', $_smarty_tpl->tpl_vars['_conf']->value[$_smarty_tpl->tpl_vars['skin_player_type']->value]);
?>

<div class="block">

    <div class="title">
        <div class="block_config">

            <?php echo (function_exists('smarty_function_jrCore_item_detail_buttons')) ? smarty_function_jrCore_item_detail_buttons(array('module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>


        </div>
        <h1><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>
</h1>
        <div class="breadcrumbs">
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</a> &raquo;
            <?php if (jrCore_module_is_active('jrCombinedAudio') && $_smarty_tpl->tpl_vars['item']->value['quota_jrCombinedAudio_allowed'] == 'on') {?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCombinedAudio"),$_smarty_tpl) : '';?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCombinedAudio",'id'=>1,'default'=>"Audio"),$_smarty_tpl) : '';?>
</a>
            <?php } else { ?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"41",'default'=>"Audio"),$_smarty_tpl) : '';?>
</a>
            <?php }?>
            &raquo; <?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>

        </div>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['player_type']->value == 'gray_overlay_player' || $_smarty_tpl->tpl_vars['player_type']->value == 'black_overlay_player') {?>

    <div class="block_content">
        <div class="item">
            <div class="container">
                <div class="row">

                    <div class="col3">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['iurl']->value;?>
/audio_image/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/1280/_v=<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_image_time'];?>
" data-lightbox="images" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['audio_title']);?>
">
                        <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrAudio",'type'=>"audio_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'size'=>"xlarge",'crop'=>"square",'class'=>"iloutline img_shadow img_scale",'alt'=>$_smarty_tpl->tpl_vars['item']->value['audio_title']),$_smarty_tpl) : '';?>

                        </a>
                    </div>

                    <div class="col9 last">
                        <div style="position:relative;padding:0 20px;height:203px;">

                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['audio_active']) && $_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'off' && isset($_smarty_tpl->tpl_vars['item']->value['quota_jrAudio_audio_conversions']) && $_smarty_tpl->tpl_vars['item']->value['quota_jrAudio_audio_conversions'] == 'on') {?>
                                <p class="center"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"40",'default'=>"This audio file is currently being processed and will appear here when complete."),$_smarty_tpl) : '';?>
</p>
                            <?php } else { ?>
                                <h1><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>
</h1><br><br>
                                <h3><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"31",'default'=>"album"),$_smarty_tpl) : '';?>
: <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album'];?>
</a></h3><br>
                                <h3><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"12",'default'=>"genre"),$_smarty_tpl) : '';?>
: <?php echo $_smarty_tpl->tpl_vars['item']->value['audio_genre'];?>
</h3>
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                    <br><h3><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"51",'default'=>"streams"),$_smarty_tpl) : '';?>
: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_file_stream_count'])===null||strlen($tmp)===0||$tmp==='' ? "0" : $tmp);?>
</h3>
                                    <?php if (!empty($_smarty_tpl->tpl_vars['item']->value['audio_file_item_price'])) {?>
                                        <br><h3><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"14",'default'=>"audio file"),$_smarty_tpl) : '';?>
: <span style="text-transform:uppercase"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_file_original_extension'];?>
</span>, <?php echo jrCore_format_size($_smarty_tpl->tpl_vars['item']->value['audio_file_original_size']);?>
, <?php echo $_smarty_tpl->tpl_vars['item']->value['audio_file_length'];?>
</h3>
                                    <?php }?>
                                    <br>
                                <?php }?>

                                <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrRating_form",'type'=>"star",'module'=>"jrAudio",'index'=>"1",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'current'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_average_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp),'votes'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp)),$_smarty_tpl) : '';?>


                                <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>
                                    <?php $_smarty_tpl->_assignInScope('ap', ((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."_auto_play");
?>
                                    <?php $_smarty_tpl->_assignInScope('player', "jrAudio_".((string)$_smarty_tpl->tpl_vars['player_type']->value));
?>
                                    <br>
                                    <?php if (jrCore_is_mobile_device()) {?>
                                    <div style="position:absolute;bottom:0;width:88%">
                                    <?php } else { ?>
                                    <div style="position:absolute;bottom:0;width:95%">
                                    <?php }?>
                                        <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>$_smarty_tpl->tpl_vars['player']->value,'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value,'autoplay'=>$_smarty_tpl->tpl_vars['_conf']->value[$_smarty_tpl->tpl_vars['ap']->value]),$_smarty_tpl) : '';?>

                                    </div>
                                <?php }?>
                            <?php }?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        
        <?php echo (function_exists('smarty_function_jrCore_item_detail_features')) ? smarty_function_jrCore_item_detail_features(array('module'=>"jrAudio",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>


    </div>

<?php } else { ?>

    <div class="block_content">

        <div class="item">

            <div class="jraudio_detail_player">
                <div class="jraudio_detail_player_left">

                    
                    <?php if (isset($_smarty_tpl->tpl_vars['item']->value['audio_active']) && $_smarty_tpl->tpl_vars['item']->value['audio_active'] == 'off' && isset($_smarty_tpl->tpl_vars['item']->value['quota_jrAudio_audio_conversions']) && $_smarty_tpl->tpl_vars['item']->value['quota_jrAudio_audio_conversions'] == 'on') {?>

                        <p class="center"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"40",'default'=>"This audio file is currently being processed and will appear here when complete."),$_smarty_tpl) : '';?>
</p>

                    <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['audio_file_extension'] == 'mp3') {?>

                        <?php $_smarty_tpl->_assignInScope('ap', ((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."_auto_play");
?>
                        <?php $_smarty_tpl->_assignInScope('player', "jrAudio_".((string)$_smarty_tpl->tpl_vars['player_type']->value));
?>
                        <?php if (isset($_smarty_tpl->tpl_vars['player_type']->value) && strlen($_smarty_tpl->tpl_vars['player_type']->value) > 0) {?>
                            <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('type'=>$_smarty_tpl->tpl_vars['player']->value,'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value,'autoplay'=>$_smarty_tpl->tpl_vars['_conf']->value[$_smarty_tpl->tpl_vars['ap']->value]),$_smarty_tpl) : '';?>
<br>
                        <?php } else { ?>
                            <?php echo (function_exists('smarty_function_jrCore_media_player')) ? smarty_function_jrCore_media_player(array('module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value,'autoplay'=>$_smarty_tpl->tpl_vars['_conf']->value[$_smarty_tpl->tpl_vars['ap']->value]),$_smarty_tpl) : '';?>
<br>
                        <?php }?>

                        <div style="text-align:left;padding-left:6px">
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
                            <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"51",'default'=>"streams"),$_smarty_tpl) : '';?>
:</span> <span class="info_c"><?php echo number_format((($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_file_stream_count'])===null||strlen($tmp)===0||$tmp==='' ? "0" : $tmp));?>
</span><br>
                            <?php if (!empty($_smarty_tpl->tpl_vars['item']->value['audio_file_item_price'])) {?>
                                <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"54",'default'=>"purchase"),$_smarty_tpl) : '';?>
:</span> <span class="info_c"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_file_original_extension'];?>
, <?php echo jrCore_format_size($_smarty_tpl->tpl_vars['item']->value['audio_file_original_size']);?>
, <?php echo $_smarty_tpl->tpl_vars['item']->value['audio_file_length'];?>
</span>
                            <?php }?>
                            <br><?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrRating_form",'type'=>"star",'module'=>"jrAudio",'index'=>"1",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'current'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_average_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp),'votes'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp)),$_smarty_tpl) : '';?>

                        </div>

                    <?php } else { ?>

                        
                        <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrAudio_block_download']) && $_smarty_tpl->tpl_vars['_conf']->value['jrAudio_block_download'] == 'off') {?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/download/audio_file/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"download"),$_smarty_tpl) : '';?>
</a><br>
                        <?php }?>

                        <div style="text-align:left;padding-left:6px">
                            <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"31",'default'=>"album"),$_smarty_tpl) : '';?>
:</span> <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album_url'];?>
"><span class="info_c"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_album'];?>
</span></a><br>
                            <span class="info"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"12",'default'=>"genre"),$_smarty_tpl) : '';?>
:</span> <span class="info_c"><?php echo $_smarty_tpl->tpl_vars['item']->value['audio_genre'];?>
</span><br>
                            <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrRating_form",'type'=>"star",'module'=>"jrAudio",'index'=>"1",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'current'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_average_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp),'votes'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_rating_1_count'])===null||strlen($tmp)===0||$tmp==='' ? 0 : $tmp)),$_smarty_tpl) : '';?>

                        </div>

                    <?php }?>
                </div>

                <div class="jraudio_detail_player_right">
                    <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrAudio",'type'=>"audio_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'size'=>"large",'class'=>"iloutline img_shadow",'alt'=>$_smarty_tpl->tpl_vars['item']->value['audio_title'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>

                </div>

            </div>

        </div>

        
        <?php echo (function_exists('smarty_function_jrCore_item_detail_features')) ? smarty_function_jrCore_item_detail_features(array('module'=>"jrAudio",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl) : '';?>


    </div>

<?php }?>

</div>
<?php }
}
