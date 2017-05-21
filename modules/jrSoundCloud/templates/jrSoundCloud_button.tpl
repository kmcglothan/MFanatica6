{if isset($jrSoundCloud.params.soundcloud_id) && is_numeric($jrSoundCloud.params.soundcloud_id)}

{if $template_already_shown == '0'}
<link rel="stylesheet" href="{$jamroom_url}/modules/jrSoundCloud/css/jrSoundCloud_button.css" media="screen" />
{/if}

<script type="text/javascript">
$(document).ready(function(){
    $("#jquery_jplayer_{$jrSoundCloud.params._item_id}").jPlayer({
        ready: function (event) {
            $(this).jPlayer(
                "setMedia", { mp3: "https://api.soundcloud.com/tracks/{$jrSoundCloud.params.soundcloud_id}/stream.json?callback=%3F&consumer_key={$_conf['jrSoundCloud_client_id']}" }
            );
        },
        cssSelectorAncestor: "#jp_container_{$jrSoundCloud.params._item_id}",
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
        supplied: "mp3",
        wmode: "window",
        play: function() {
            $('#jquery_jplayer_{$jrSoundCloud.params._item_id}').jPlayer("pauseOthers");
        }
    });
});
</script>

{jrCore_lang module="jrSoundCloud" id="55" default="play" assign="play"}
{jrCore_lang module="jrSoundCloud" id="56" default="pause" assign="pause"}
{jrCore_module_url module="jrImage" assign="murl"}
{if isset($params.image)}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_pause_hover.png"}
{elseif is_file("`$jamroom_dir`/skins/`$_conf.jrCore_active_skin`/img/button_player_play.png")}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_pause_hover.png"}
{else}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/module/jrSoundCloud/button_player_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/module/jrSoundCloud/button_player_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/module/jrSoundCloud/button_player_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/module/jrSoundCloud/button_player_pause_hover.png"}
{/if}

<div class="sc_button_player">

    <div id="jquery_jplayer_{$jrSoundCloud.params._item_id}" class="jp-jplayer"></div>
    <div id="jp_container_{$jrSoundCloud.params._item_id}" class="jp-audio">
        <div class="jp-type-single">
            <div class="jp-gui jp-interface">
                <ul class="jp-controls">
                    <li><a href="javascript:" class="jp-play" tabindex="1"><img src="{$play_i}" alt="{$play}" title="{$play}" onmouseover="$(this).attr('src','{$play_h}');" onmouseout="$(this).attr('src','{$play_i}');"></a></li>
                    <li><a href="javascript:" class="jp-pause" tabindex="1"><img src="{$pause_i}" alt="{$pause}" title="{$pause}" onmouseover="$(this).attr('src','{$pause_h}');" onmouseout="$(this).attr('src','{$pause_i}');"></a></li>
                </ul>
            </div>
            <div class="jp-no-solution">
                <span><a href="http://get.adobe.com/flashplayer/" target="_blank">{jrCore_lang module="jrSoundCloud" id="57" default="Flash Required"}</a></span>
            </div>
        </div>
    </div>

</div>
{/if}
