<script type="text/javascript">
    $(document).ready(function(){

        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);

        new jPlayerPlaylist( {
            jPlayer: "#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor: "#jp_container_{$uniqid}"
        } , [
            {foreach $media as $item}
            {
                title: "[{$item.prefix}] {$item.title}",
                artist: "{$item._item.profile_name}",
                module: "{$item.module}",
                item_id: "{$item.item_id}",
        {foreach $item.formats as $format => $url}
        {$format}: "{$url}",
                {/foreach}
                poster: "{$item.poster}"
    } ,
    {/foreach}
    ] , {
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
                preload: 'none',
                size: { width: "100%", height: th + "px" }
    } );
    } );
</script>

<div class="media_blue_monday_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_{$uniqid}" class="jp-video jp-video-270p">
        <div class="jp-type-playlist">
            <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>
            <div class="jp-gui">
                <div class="jp-video-play">
                    <a href="javascript:" class="jp-video-play-icon" tabindex="1"></a>
                </div>
                <div class="jp-interface">
                    <div class="jp-progress">
                        <div class="jp-seek-bar">
                            <div class="jp-play-bar"></div>
                        </div>
                    </div>
                    <div class="jp-current-time"></div>
                    <div class="jp-duration"></div>
                    <div class="jp-controls-holder">
                        <ul class="jp-controls">
                            <li><a href="javascript:" class="jp-previous" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="22" default="previous"}"></a></li>
                            <li><a href="javascript:" class="jp-play" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="23" default="play"}"></a></li>
                            <li><a href="javascript:" class="jp-pause" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="24" default="pause"}"></a></li>
                            <li><a href="javascript:" class="jp-next" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="25" default="next"}"></a></li>
                            <li><a href="javascript:" class="jp-stop" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="26" default="stop"}"></a></li>
                            <li><a href="javascript:" class="jp-mute" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="27" default="mute"}"></a></li>
                            <li><a href="javascript:" class="jp-unmute" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="28" default="unmute"}"></a></li>
                            <li><a href="javascript:" class="jp-volume-max" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="29" default="max volume"}"></a></li>
                        </ul>
                        <div class="jp-volume-bar">
                            <div class="jp-volume-bar-value"></div>
                        </div>
                        <ul class="jp-toggles">
                            <li><a href="javascript:" class="jp-full-screen" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="30" default="full screen"}"></a></li>
                            <li><a href="javascript:" class="jp-restore-screen" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="31" default="restore screen"}"></a></li>
                            <li><a href="javascript:" class="jp-shuffle" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="32" default="shuffle"}"></a></li>
                            <li><a href="javascript:" class="jp-shuffle-off" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="33" default="shuffle off"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="34" default="repeat"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat-off" tabindex="1" title="{jrCore_lang module="jrPlaylist" id="35" default="repeat off"}"></a></li>
                        </ul>
                    </div>
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
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