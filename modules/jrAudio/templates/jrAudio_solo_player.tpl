{if $template_already_shown == '0'}
{jrCore_module_url module="jrCore" assign="curl"}
{jrCore_module_url module="jrSolo" assign="surl"}
{/if}

{jrCore_module_url module=$params.module assign="murl"}

{assign var="ext" value="`$params.field`_extension"}

<script type="text/javascript">
    $(document).ready(function(){
        new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor: "#jp_container_{$uniqid}"
        },[
            {if is_array($media)}
            {foreach $media as $a}
            {if $a._item.$ext == 'mp3'}
            {
                title: "{$a.title}",
                artist: "{$a.artist}",
                mp3: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.mp3",
                {if strstr($formats, 'oga')}
                oga: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.ogg",
                {/if}
                poster: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/large"
            },
            {/if}
            {/foreach}
            {/if}
        ],{
            error: function(res) { jrCore_stream_url_error(res); },
            playlistOptions: {
                autoPlay: {$autoplay},
                displayTime: 'fast'
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            volume: 0.8,
            wmode: 'window',
            preload: 'none'
        });
    });
</script>

<div class="solo_player">
    <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>

    <div id="jp_container_{$uniqid}" class="jp-audio"{if isset($_post._1) && $_post._1 == 'albums'} style="margin-top:10px;"{/if}>
        <div class="jp-type-playlist">
            <div class="jp-gui">
                <div class="jp-video-play">
                    <a href="javascript:;" class="jp-video-play-icon" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="1" default="play"}</a>
                </div>
                <div class="jp-interface">
                    <div class="jp-seek-holder">
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="jp-current-time"></div>
                    <div class="jp-duration"></div>
                    <div class="jp-controls-holder">
                        <ul class="jp-controls">
                            <li><a href="javascript:" class="jp-previous" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="44" default="previous"}</a></li>
                            <li><a href="javascript:" class="jp-play" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="1" default="play"}</a></li>
                            <li><a href="javascript:" class="jp-pause" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="2" default="pause"}</a></li>
                            <li><a href="javascript:" class="jp-next" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="45" default="next"}</a></li>
                            <li><a href="javascript:" class="jp-stop" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="3" default="stop"}</a></li>
                            <li><a href="javascript:" class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="4" default="mute"}">{jrCore_lang module="jrAudio" id="4" default="mute"}</a></li>
                            <li><a href="javascript:" class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="5" default="unmute"}">{jrCore_lang module="jrAudio" id="5" default="unmute"}</a></li>
                            <li><a href="javascript:" class="jp-volume-max" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="6" default="max volume"}">{jrCore_lang module="jrAudio" id="6" default="max volume"}</a></li>
                        </ul>
                        <div class="jp-volume-bar">
                            <div class="jp-volume-bar-value"></div>
                        </div>
                        <ul class="jp-toggles">
                            <li><a href="javascript:" class="jp-shuffle" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="46" default="shuffle"}">{jrCore_lang module="jrAudio" id="46" default="shuffle"}</a></li>
                            <li><a href="javascript:" class="jp-shuffle-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="47" default="shuffle off"}">{jrCore_lang module="jrAudio" id="47" default="shuffle off"}</a></li>
                            <li><a href="javascript:" class="jp-repeat" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="7" default="repeat"}">{jrCore_lang module="jrAudio" id="7" default="repeat"}</a></li>
                            <li><a href="javascript:" class="jp-repeat-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="8" default="repeat off"}">{jrCore_lang module="jrAudio" id="8" default="repeat off"}</a></li>
                        </ul>
                    </div>
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="jp-playlist left capital">
                <ul>
                    <li></li>
                </ul>
            </div>
            <div class="jp-no-solution">
                <span>{jrCore_lang module="jrAudio" id="48" default="Update Required"}</span>
                {jrCore_lang module="jrAudio" id="49" default="To play the media you will need to either update your browser to a recent version or update your"} <a href="http://get.adobe.com/flashplayer/" target="_blank" rel="noopener noreferrer">{jrCore_lang module="jrAudio" id="50" default="Flash plugin"}</a>.
            </div>
        </div>
    </div>
</div>
