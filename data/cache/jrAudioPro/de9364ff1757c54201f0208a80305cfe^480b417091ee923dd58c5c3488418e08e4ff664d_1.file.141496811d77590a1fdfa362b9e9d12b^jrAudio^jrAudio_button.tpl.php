<?php
/* Smarty version 3.1.31, created on 2018-05-21 07:30:35
  from "/webserver/mf6/data/cache/jrCore/141496811d77590a1fdfa362b9e9d12b^jrAudio^jrAudio_button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b02678bcffd02_91969962',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '480b417091ee923dd58c5c3488418e08e4ff664d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/141496811d77590a1fdfa362b9e9d12b^jrAudio^jrAudio_button.tpl',
      1 => 1526884235,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b02678bcffd02_91969962 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript">
$(document).ready(function(){
    var pl = $('#<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
');
    pl.jPlayer({
        swfPath: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/jplayer",
        ready: function() { return true; },
        cssSelectorAncestor: "#jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
",
        supplied: '<?php echo $_smarty_tpl->tpl_vars['formats']->value;?>
',
        solution: "<?php echo $_smarty_tpl->tpl_vars['solution']->value;?>
",
        volume: 0.8,
        wmode: 'window',
        consoleAlerts: true,
        preload: 'none',
        error: function(r) { jrCore_stream_url_error(r); },
        play: function() {
            pl.jPlayer("pauseOthers");
        }
    });
    var ps = $('#<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
_play');
    ps.click(function(e) {
        pl.jPlayer("clearMedia");
        pl.jPlayer("setMedia", {
            <?php if (strstr($_smarty_tpl->tpl_vars['formats']->value,'oga')) {?>
            oga: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['media']->value[0]['module_url'];?>
/stream/<?php echo $_smarty_tpl->tpl_vars['params']->value['field'];?>
/<?php echo $_smarty_tpl->tpl_vars['media']->value[0]['item_id'];?>
/key=[jrCore_media_play_key]/file.ogg",
            <?php }?>
            mp3: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['media']->value[0]['module_url'];?>
/stream/<?php echo $_smarty_tpl->tpl_vars['params']->value['field'];?>
/<?php echo $_smarty_tpl->tpl_vars['media']->value[0]['item_id'];?>
/key=[jrCore_media_play_key]/file.mp3"
        });
        pl.jPlayer("play");
        e.preventDefault();
    });
});
<?php echo '</script'; ?>
>

<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"1",'default'=>"play",'assign'=>"play"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAudio",'id'=>"2",'default'=>"pause",'assign'=>"pause"),$_smarty_tpl) : '';?>


<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php if (isset($_smarty_tpl->tpl_vars['params']->value['image'])) {?>
    <?php $_smarty_tpl->_assignInScope('play_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/".((string)$_smarty_tpl->tpl_vars['params']->value['image'])."_play.png");
?>
    <?php $_smarty_tpl->_assignInScope('play_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/".((string)$_smarty_tpl->tpl_vars['params']->value['image'])."_play_hover.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/".((string)$_smarty_tpl->tpl_vars['params']->value['image'])."_pause.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/".((string)$_smarty_tpl->tpl_vars['params']->value['image'])."_pause_hover.png");
} elseif (is_file(((string)$_smarty_tpl->tpl_vars['jamroom_dir']->value)."/skins/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/img/button_player_play.png")) {?>
    <?php $_smarty_tpl->_assignInScope('play_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/button_player_play.png");
?>
    <?php $_smarty_tpl->_assignInScope('play_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/button_player_play_hover.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/button_player_pause.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/skin/".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])."/button_player_pause_hover.png");
} else { ?>
    <?php $_smarty_tpl->_assignInScope('play_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/module/jrAudio/button_player_play.png");
?>
    <?php $_smarty_tpl->_assignInScope('play_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/module/jrAudio/button_player_play_hover.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_i', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/module/jrAudio/button_player_pause.png");
?>
    <?php $_smarty_tpl->_assignInScope('pause_h', ((string)$_smarty_tpl->tpl_vars['jamroom_url']->value)."/".((string)$_smarty_tpl->tpl_vars['murl']->value)."/img/module/jrAudio/button_player_pause_hover.png");
}?>

<div class="button_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
" class="jp-jplayer"></div>
    <div id="jp_container_<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
" class="jp-audio">
        <div class="jp-type-single">
            <div class="jp-gui jp-interface">
                <ul class="jp-controls">
                    <li><a id="<?php echo $_smarty_tpl->tpl_vars['uniqid']->value;?>
_play" class="jp-play" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['play_i']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['play']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['play']->value;?>
" onmouseover="$(this).attr('src','<?php echo $_smarty_tpl->tpl_vars['play_h']->value;?>
');" onmouseout="$(this).attr('src','<?php echo $_smarty_tpl->tpl_vars['play_i']->value;?>
');"></a></li>
                    <li><a class="jp-pause" tabindex="<?php echo (function_exists('smarty_function_jrCore_next_tabindex')) ? smarty_function_jrCore_next_tabindex(array(),$_smarty_tpl) : '';?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['pause_i']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['pause']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['pause']->value;?>
" onmouseover="$(this).attr('src','<?php echo $_smarty_tpl->tpl_vars['pause_h']->value;?>
');" onmouseout="$(this).attr('src','<?php echo $_smarty_tpl->tpl_vars['pause_i']->value;?>
');"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php }
}
