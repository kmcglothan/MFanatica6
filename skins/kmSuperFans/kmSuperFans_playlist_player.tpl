{jrCore_module_url module=$params.module assign="murl"}
<script type="text/javascript">
    $(document).ready(function() {

        var jp_volume = 0.8;
        var volumeCookie = jrReadCookie('kmSuperFans_audio_volume');
        if(volumeCookie && volumeCookie.length > 0) {
            jp_volume = volumeCookie;
        }

        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);
        $('#jp_container_{$uniqid} .jp-gui').height(th-30);

        new jPlayerPlaylist( {
            jPlayer: "#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor: "#jp_container_{$uniqid}"
        } , [
            {if isset($media) && is_array($media)}
            {foreach $media as $item}
            {

                {$album_url = "`$jamroom_url`/`$item._item.profile_url`/`$item.module_url`/albums/`$item._item.audio_album_url`"}
                {$field = 'audio_file'}
                {$image = 'audio_image'}
                {if isset($item._item.video_title)}
                {$album_url = "`$jamroom_url`/`$item._item.profile_url`/`$item.module_url`/albums/`$item._item.video_album_url`"}
                {$field = 'field_file'}
                {$image = 'video_image'}
                {/if}

                title: "{$item.title|truncate:55}",
                artist: "{$item.artist|truncate:55}",
                item_id: "{$item.item_id}",
                poster: "{$item.poster}?crop=auto",

        {foreach $item.formats as $format => $url}
        {$format}: "{$url}",
        {/foreach}
    } ,
    {/foreach}
    {/if}
    ] , {
        play : function() {
            $("#jquery_jplayer_{$uniqid} .jp-gui").removeClass('start');
        },
        error: function(res) { jrCore_stream_url_error(res); },
        playlistOptions: {
            autoPlay: {$autoplay},
            displayTime: 'fast'
        },
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
                supplied: "{$formats}",
                solution: "{$solution}",
                volume: jp_volume,
                preload: 'none',
                wmode: 'opaque',
                size: { width: "100", height: "100px" },
                smoothPlayBar: true,
                keyEnabled: true,
                remainingDuration: true,
                toggleDuration: true

    });

    var holder = $('#jp_container_{$uniqid} .jp-controls-holder');
    var gui = $('#jp_container_{$uniqid}  .jp-gui');
    var title = $('#jp_container_{$uniqid}  .jp-title');

        $('#jp_container_{$uniqid}').bind($.jPlayer.event.volumechange, function(event){
            jp_volume = event.jPlayer.options.volume;
            if($("#jp_container_{$uniqid}").hasClass('jp-state-muted')) {
                jp_volume = 0
            }
            jrSetCookie('kmSuperFans_audio_volume', jp_volume, 31);
        });

})
</script>

<div class="kmSuperFans_audio playlist" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_{$uniqid}" class="jp-audio">
        <div class="jp-type-playlist">
            <div class="video_container">
                <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>
                <div class="jp-gui start">
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                    <div class="jp-interface">
                        <div class="jp-controls-holder">
                            <ul class="jp-controls" id="play-pause">
                                <li><a class="jp-play" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="1" default="play"}"></a></li>
                                <li><a class="jp-pause" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="2" default="pause"}"></a></li>
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
                                <li><a class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="4" default="mute"}"></a></li>
                                <li><a class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="5" default="unmute"}"></a></li>
                            </ul>
                            <div class="jp-volume-bar" style="display: none;">
                                <div class="jp-volume-bar-value"></div>
                            </div>
                            <ul class="jp-toggles">
                                <li><a class="jp-full-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="55" default="full screen"}"></a></li>
                                <li><a class="jp-restore-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="56" default="restore screen"}"></a></li>
                            </ul>
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
</div>
