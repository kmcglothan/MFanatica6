<script type="text/javascript">
    $(document).ready(function() {

        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);

        new jPlayerPlaylist( {
            jPlayer: "#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor: "#jp_container_{$uniqid}"
        } , [
            {if isset($media) && is_array($media)}
            {foreach $media as $item}
            {
                title: "[{$item.prefix}] {$item.title}",
                artist: "{$item.artist}",
                module: "{$item.module}",
                item_id: "{$item.item_id}",
                poster: "{$item.poster}",
        {foreach $item.formats as $format => $url}
        {$format}: "{$url}",
        {/foreach}
    } ,
    {/foreach}
    {/if}
    ] , {
        error: function(res) { jrCore_stream_url_error(res); },
        playlistOptions: {
            autoPlay: {$autoplay},
            displayTime: 'fast'
        },
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
                supplied: "{$formats}",
                solution: "{$solution}",
                wmode: 'window',
                {if jrCore_is_mobile_device() || isset($item.module_url) && ($item.module_url == 'uploaded_audio' || $item.module_url == 'audio')}
                autohide: { hold: 2500 },
        {else}
        autohide: { restored: true, fadein: 10, hold: 2500 },
        {/if}
        volume: 0.8,
                preload: 'none',
                size: { width: "100%", height: th + "px" }
    } );
    } );
</script>

<div class="jrplaylist_black_overlay_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
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
                        <a class="jp-video-play-icon" tabindex="1">{jrCore_lang module="jrPlaylist" id="23" default="play"}</a>
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
                            <ul class="jp-controls">
                                <li><a class="jp-play" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="23" default="play"}"></a></li>
                                <li><a class="jp-pause" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="24" default="pause"}"></a></li>
                                <li style="margin-left:5px;"><a class="jp-previous" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="22" default="previous"}" style="margin-top:0;"></a></li>
                                <li><a class="jp-next" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="25" default="next"}" style="margin-top:0;"></a></li>
                            </ul>
                            {if !jrCore_is_mobile_device()}
                                <ul class="jp-controls" style="width: 16px;margin-right:6px;margin-left:5px;">
                                    <li><a class="jp-mute" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="27" default="mute"}"></a></li>
                                    <li><a class="jp-unmute" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="28" default="unmute"}"></a></li>
                                </ul>
                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                                <div class="jp-current-time"></div><span style="float:left;font-size:.64em;font-style:oblique;margin-top:{if $_conf.jrCore_active_skin == 'jrFlashback'}2px{else}-1px{/if};">&nbsp;&nbsp;/&nbsp;</span>
                                <div class="jp-duration"></div>
                            {/if}
                            <div class="jp-right-controls">
                                <ul class="jp-toggles">
                                    {if isset($item.module_url) && ($item.module_url == 'uploaded_video' || $item.module_url == 'video')}
                                        <li><a class="jp-full-screen" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="30" default="full screen"}"></a></li>
                                        <li><a class="jp-restore-screen" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="31" default="restore screen"}"></a></li>
                                        <li><a class="jp-shuffle" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="32" default="shuffle"}"></a></li>
                                        <li><a class="jp-shuffle-off" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="33" default="shuffle off"}"></a></li>
                                    {/if}
                                    {if isset($item.module_url) && ($item.module_url == 'uploaded_audio' || $item.module_url == 'audio')}
                                        <li><a class="jp-shuffle" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="32" default="shuffle"}"></a></li>
                                        <li><a class="jp-shuffle-off" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="33" default="shuffle off"}"></a></li>
                                        <li><a class="jp-repeat" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="34" default="repeat"}"></a></li>
                                        <li><a class="jp-repeat-off" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="35" default="repeat off"}"></a></li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="listcontainer{$uniqid}" class="jp-playlist">
                <ul id="list{$uniqid}">
                    <li></li>
                </ul>
            </div>
        </div>
    </div>
</div>