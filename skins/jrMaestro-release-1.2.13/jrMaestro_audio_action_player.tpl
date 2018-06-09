{jrCore_module_url module=$params.module assign="murl"}

{assign var="ext" value="`$params.field`_extension"}

<script type="text/javascript">
$(document).ready(function(){


    var jp_volume = 0.8;

    var autoPlayCookie = jrReadCookie('jrMaestro_audio_autoplay');
    var volumeCookie = jrReadCookie('jrMaestro_audio_volume');

    if(volumeCookie && volumeCookie.length > 0) {
        jp_volume = volumeCookie;
    }

    if (autoPlayCookie && autoPlayCookie == 1) {
        $('#jp_container_{$uniqid} #jp-auto-play').prop('checked', true);
        var autoPlay = true;
    }

    var tw = $('#jp_container_{$uniqid}').width();
    var th = Math.round(tw / 1.778);
    $('#jp_container_{$uniqid} .jp-gui').height(th-30);


    var playlist_{$uniqid} = new jPlayerPlaylist({
        jPlayer: "#jquery_jplayer_{$uniqid}",
        cssSelectorAncestor: "#jp_container_{$uniqid}"
    },[
    {if is_array($media)}
        {foreach $media as $a}
            {if $a._item.$ext == 'mp3'}
            {
                title: "{$a.title|truncate:50}",
                artist: "{$a.artist|truncate:50}",
                mp3: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.mp3",
                {if strstr($formats, 'oga')}
                oga: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.ogg",
                {/if}
                poster: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/xxxlarge/crop=" + tw +':'+ th,
                image: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/24/crop=auto",
                id: "{$a.item_id}",
                profile_id: "{$o.profile_id}",
                album_url: "{$jamroom_url}/{$a._item.profile_url}/{$a.module_url}/albums/{$a._item.audio_album_url}",
                item_url: "{$jamroom_url}/{$a._item.profile_url}/{$a.module_url}/{$a._item_id}/{$a._item.audio_album_url}",
                key: "[jrCore_media_play_key]",
                module: "{$a.module}",
                price : {$a._item.audio_file_item_price|default:"0"},
                field : 'audio_file',
                url: "{$a.module_url}",
                prefix : "audio"
            },
            {/if}
        {/foreach}
    {/if}
    ],{
        ready: function() {
            if (jp_volume == 0) {
                $('#jp_container_{$uniqid}  .jp-mute').hide();
                $('#jp_container_{$uniqid}  .jp-unmute').show();
            }
        },
        error: function(res) { jrCore_stream_url_error(res); },
        playlistOptions: {
            autoPlay: false,
            displayTime: 'fast'
        },
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
        supplied: "{$formats}",
        solution: "{$solution}",
        volume: jp_volume,
        smoothPlayBar: true,
        keyEnabled: true,
        preload:'none',
        mode: 'window',
        remainingDuration: true,
        toggleDuration: true,
        size: { width: "100%", height: th + "px" }
    });

    var holder = $('#jp_container_{$uniqid} .jp-controls-holder');
    var gui = $('#jp_container_{$uniqid}  .jp-gui');
    var settings = $('#jp_container_{$uniqid}  .jp-settings');
    var controls = $('#jp_container_{$uniqid}  .jp-control-settings');
    var vBar = $('#jp_container_{$uniqid}  .jp-volume-bar');
    var list = $('#jp_container_{$uniqid}  .jp-playlist');
    var title = $('#jp_container_{$uniqid}  .jp-title');

    settings.mouseover(function(){
        controls.show();
        vBar.hide();
    });
    $('#jp_container_{$uniqid}  .jp-interface').mouseleave(function(){
        controls.hide();
        vBar.hide();
    });

    $('#jp_container_{$uniqid}  .jp-mute, #jp_container_{$uniqid}  .jp-unmute').mouseover(
            function (){
                vBar.show();
                controls.hide();
            }
    );
    $('#jp_container_{$uniqid}  input#jp-show').click(function() {
        if (!$(this).is(':checked')) {
            list.fadeOut('fast');
            title.css({
                visibility : "visible"
            });
        }
        else {
            list.fadeIn('fast');
            title.css({
                visibility : "collapse"
            });
        }
    });

    vBar.hide();

    $('#jp_container_{$uniqid}').bind($.jPlayer.event.volumechange, function(event){
        jp_volume = event.jPlayer.options.volume;
        if($("#jp_container_{$uniqid}").hasClass('jp-state-muted')) {
            jp_volume = 0
        }
        jrSetCookie('jrMaestro_audio_volume', jp_volume, 31);
    });

    $('#jp_container_{$uniqid} #jp-auto-play').click(function() {
        jrSetCookie('jrMaestro_audio_autoplay', 0, 31);
        if ($(this).is(':checked')) {
            jrSetCookie('jrMaestro_audio_autoplay', 1, 31);
        }
    });
});
</script>

<div class="jrMaestro_player audio" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
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
                                <li><a class="jp-settings" tabindex="{jrCore_next_tabindex}"></a>
                                    <div class="jp-control-settings" style="display: none;">
                                        <ul>
                                            <input type="checkbox" id="jp-show" /> <span>Show Playlist</span><br>
                                        </ul>
                                    </div>
                                </li>
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
