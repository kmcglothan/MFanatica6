<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/c0dc923018552f9b4ba90d792a0c1f72^jrAudioPro^jrAudioPro_audio_player.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f88882a1_16013907',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1f411272071ab3b629dfbf4d956d49e443eef1d9' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c0dc923018552f9b4ba90d792a0c1f72^jrAudioPro^jrAudioPro_audio_player.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f88882a1_16013907 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>$_smarty_tpl->tpl_vars['params']->value['module'],'assign'=>"murl"),$_smarty_tpl) : '';?>


<?php $_smarty_tpl->_assignInScope('ext', ((string)$_smarty_tpl->tpl_vars['params']->value['field'])."_extension");
?>

<?php echo '<script'; ?>
 type="text/javascript">
$(document).ready(function(){

    var jp_volume = 0.8;
    /* If there is a cookie and is numeric, get it. */
    var volumeCookie = jrReadCookie('jrAudioPro_audio_volume');
    if(volumeCookie && volumeCookie.length > 0) {
        jp_volume = volumeCookie;
    }

    var tw = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
').width();
    var th = Math.round(tw / 1.778);
    $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
 .jp-gui').height(th-30);


    new jPlayerPlaylist({
        jPlayer: "#jquery_jplayer_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
",
        cssSelectorAncestor: "#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
"
    },[
    <?php if (is_array($_smarty_tpl->tpl_vars['media']->value)) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['media']->value, 'a');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['a']->value) {
?>
            <?php if ($_smarty_tpl->tpl_vars['a']->value['_item'][$_smarty_tpl->tpl_vars['ext']->value] == 'mp3') {?>
            {
                title: "<?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['a']->value['title'],50);?>
",
                artist: "<?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['a']->value['artist'],50);?>
",
                mp3: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['a']->value['module_url'];?>
/stream/<?php echo $_smarty_tpl->tpl_vars['params']->value['field'];?>
/<?php echo $_smarty_tpl->tpl_vars['a']->value['item_id'];?>
/key=[jrCore_media_play_key]/file.mp3",
                <?php if (strstr($_smarty_tpl->tpl_vars['formats']->value,'oga')) {?>
                oga: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['a']->value['module_url'];?>
/stream/<?php echo $_smarty_tpl->tpl_vars['params']->value['field'];?>
/<?php echo $_smarty_tpl->tpl_vars['a']->value['item_id'];?>
/key=[jrCore_media_play_key]/file.ogg",
                <?php }?>
                poster: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['a']->value['module_url'];?>
/image/audio_image/<?php echo $_smarty_tpl->tpl_vars['a']->value['item_id'];?>
/large/crop=audio"
            },
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    <?php }?>
    ],{
        ready: function(){
            if (jp_volume && jp_volume == 0) {
                $(this).jPlayer('volume', 0.5);
                $(this).jPlayer('mute');
            }
        },
        error: function(res) { jrCore_stream_url_error(res); },
        playlistOptions: {
            autoPlay: <?php echo $_smarty_tpl->tpl_vars['autoplay']->value;?>
,
            displayTime: 'fast'
        },
        swfPath: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/jplayer",
        supplied: "<?php echo $_smarty_tpl->tpl_vars['formats']->value;?>
",
        solution: "<?php echo $_smarty_tpl->tpl_vars['solution']->value;?>
",
        volume: jp_volume,
        smoothPlayBar: true,
        keyEnabled: true,
        preload:'none',
        mode: 'window',
        remainingDuration: true,
        toggleDuration: true
    });

    var holder = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
 .jp-controls-holder');
    var gui = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
  .jp-gui');
    var settings = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
  .jp-settings');
    var controls = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
  .jp-control-settings');
    var title = $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
  .jp-title');


    $('#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
').bind($.jPlayer.event.volumechange, function(event){
        jp_volume = event.jPlayer.options.volume;
        if($("#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
").hasClass('jp-state-muted')) {
            jp_volume = 0
        }
        /* Store the volume in a cookie. */
        jrSetCookie('jrAudioPro_audio_volume', jp_volume, 31);
    });

});
<?php echo '</script'; ?>
>

<div class="jrAudioPro_audio" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
" class="jp-audio">
        <div class="jp-type-playlist">
            <div id="jquery_jplayer_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
" class="jp-jplayer"></div>
            <div class="jp-gui">
                <div class="jp-title">
                    <ul>
                        <li></li>
                    </ul>
                </div>
                <div class="jp-interface">
                    <div class="jp-controls-holder">
                        <ul class="jp-controls" id="play-pause">
                            <li><a class="jp-play" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrVideo",'id'=>"1",'default'=>"play"),$_smarty_tpl) : '';?>
"></a></li>
                            <li><a class="jp-pause" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrVideo",'id'=>"2",'default'=>"pause"),$_smarty_tpl) : '';?>
"></a></li>
                        </ul>
                        <div class="jp-progress-holder">
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="jp-duration"></div>
                        <ul class="jp-controls" id="mute-unmute">
                            <li><a class="jp-mute" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrVideo",'id'=>"4",'default'=>"mute"),$_smarty_tpl) : '';?>
"></a></li>
                            <li><a class="jp-unmute" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrVideo",'id'=>"5",'default'=>"unmute"),$_smarty_tpl) : '';?>
"></a></li>
                        </ul>
                        <div class="jp-volume-bar" style="display: none;">
                            <div class="jp-volume-bar-value"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="jp-playlist" style="display: none;">
                <ul>
                    <li></li>
                </ul>
            </div>
        </div>
    </div>
</div><?php }
}
