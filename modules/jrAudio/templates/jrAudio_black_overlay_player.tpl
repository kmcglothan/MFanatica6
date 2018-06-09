{jrCore_module_url module=$params.module assign="murl"}

{assign var="ext" value="`$params.field`_extension"}

<script type="text/javascript">
    $(document).ready(function(){
        $("[data-slider]")
                .bind("slider:ready slider:changed",
                function(event, data) {
                    $("#jquery_jplayer_{$uniqid}").jPlayer("volume", data.value.toFixed(3));
                });

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

<div class="jr_audio_black_overlay_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_{$uniqid}" class="jp-video">
        <div class="jp-type-playlist">
            <div class="video_container">
                <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>
                <div class="jp-gui">
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                    <div class="jp-video-play">
                        <a class="jp-video-play-icon" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrAudio" id="1" default="play"}</a>
                    </div>
                    <div class="jp-interface">

                        {if jrCore_is_mobile_device()}
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                            <div class="jp-current-time"></div>
                            <div class="jp-duration"></div>
                        {/if}

                        {if !jrCore_is_mobile_device()}
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                        {/if}

                        <div class="jp-controls-holder">
                            <ul class="jp-controls"{if isset($_post._1) && $_post._1 == 'albums'} style="width:55px;"{/if}>
                                <li><a class="jp-play" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="1" default="play"}"></a></li>
                                <li><a class="jp-pause" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="2" default="pause"}"></a></li>
                                {if isset($_post._1) && $_post._1 == 'albums'}
                                    <li style="margin-left: 5px;"><a class="jp-previous" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="44" default="previous"}"></a></li>
                                    <li><a class="jp-next" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="45" default="next"}"></a></li>
                                {/if}
                            </ul>
                            {if !jrCore_is_mobile_device()}
                                <ul class="jp-controls" style="width: 16px;margin-right:6px;margin-left:5px;">
                                    <li><a class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="4" default="mute"}"></a></li>
                                    <li><a class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="5" default="unmute"}"></a></li>
                                </ul>
                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                                <div class="jp-current-time"></div><span style="float:left;font-size:.64em;font-style:oblique;margin-top:{if $_conf.jrCore_active_skin == 'jrFlashback'}2px{else}-1px{/if};">&nbsp;&nbsp;/&nbsp;</span>
                                <div class="jp-duration"></div>
                            {/if}
                            <div class="jp-right-controls">
                                <ul class="jp-toggles">
                                    {if isset($_post._1) && $_post._1 == 'albums'}
                                        <li><a class="jp-shuffle" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="46" default="shuffle"}"></a></li>
                                        <li><a class="jp-shuffle-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="47" default="shuffle off"}"></a></li>
                                    {/if}
                                    <li><a class="jp-repeat" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="7" default="repeat"}"></a></li>
                                    <li><a class="jp-repeat-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrAudio" id="8" default="repeat off"}"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="jp-playlist"{if isset($_post._1) && $_post._1 == 'albums'} style="max-height:150px;overflow:auto;"{else} style="display: none;"{/if}>
                <ul>
                    <li></li>
                </ul>
            </div>
        </div>
    </div>
</div>

