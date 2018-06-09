<script type="text/javascript">
    $(document).ready(function() {

        var jp_volume = 0.8;
        //If there is a cookie and is numeric, get it.


        var volumeCookie = jrReadCookie('jrBeatSlinger_audio_volume')
        var muteCookie = jrReadCookie('jrBeatSlinger_auto_mute');

        if(volumeCookie && volumeCookie.length > 0) {
            jp_volume = volumeCookie;
        }

        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);

        var playlist_{$uniqid} = new jPlayerPlaylist({
            jPlayer:"#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor:"#jp_container_{$uniqid}"
        },[
            {if isset($media) && is_array($media)}
            {jrCore_module_url module="jrVideo" assign="vurl"}
            {foreach $media as $v}
            {
                title: "{$v._item.video_title|htmlentities|truncate:55}",
                artist: "{$v._item.profile_name|htmlentities|truncate:55}",
                artist_url: "{$jamroom_url}/{$v._item.profile_url}",
                album_url: "{$jamroom_url}/{$v._item.profile_url}/{$vurl}/albums/{$v._item.video_album_url}",
                item_url: "{$jamroom_url}/{$v._item.profile_url}/{$v.module_url}/{$v._item_id}/{$v._item.video_album_url}",
                poster: "{$v.poster}?crop=" + tw +':'+ th,
                image: "{$jamroom_url}/{$vurl}/image/video_image/{$v.item_id}/24/crop=auto",
                "{$v._item.video_file_extension}": "{$jamroom_url}/{$vurl}/stream/video_file/{$v.item_id}/key=[jrCore_media_play_key]/file.{$v._item.video_file_extension}",
                {if strstr($formats, 'm4v')}
                m4v: "{$jamroom_url}/{$vurl}/stream/video_file_mobile/{$v.item_id}/key=[jrCore_media_play_key]/file.m4v",
                {/if}
                id: "{$v.item_id}",
                profile_id: "{$v._item.profile_id}",
                module: "{$v.module}",
                field: 'video_file',
                price : {$v._item.video_file_item_price|default:"0"},
                url: "{$v.module_url}",
                prefix : "video"
            },
            {/foreach}
            {/if}
        ],{
            error: function(res) { jrCore_stream_url_error(res); },
            playlistOptions: {
                autoPlay: false
            },
            ready: function(){
                if ((jp_volume && jp_volume == 0) || (muteCookie && muteCookie == 1)) {
                    $(this).jPlayer('volume', 0.5);
                    $(this).jPlayer('mute');
                }

                if (muteCookie && muteCookie == 1) {
                    $('#jp_container_{$uniqid} #jp-auto-mute').prop('checked', true);
                    vBar.hide();
                }
            },
            ended : function(){
                $('#jp_container_{$uniqid} .jp-gui').addClass('start');
                $('#jp_container_{$uniqid} .jp-video-play').show();
                $('#jp_container_{$uniqid} .jp-video-play-icon').addClass('replay').show();
                $('#jp_container_{$uniqid} .jp-play').addClass('replay');
            },
            play : function(){
                gui.removeClass('start');
                $('#jp_container_{$uniqid} .jp-play').removeClass('replay');
                $('#jp_container_{$uniqid} .jp-video-play-icon').removeClass('replay').hide();
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            smoothPlayBar: true,
            keyEnabled: true,
            volume: jp_volume,
            preload:'none',
            mode: 'window',
            remainingDuration: true,
            toggleDuration: true,
            {if jrCore_is_mobile_device()}
            autohide: { hold: 2500 },
            {else}
            autohide: { restored: true, fadein: 50, hold: 2500 },
            {/if}
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
                list.hide();
                title.css({
                    visibility : "visible"
                });
            }
            else {
                list.show();
                title.css({
                    visibility : "collapse"
                });
            }
        });

        vBar.hide();
    });
</script>

<div class="jrBeatSlinger_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_{$uniqid}" class="jp-video">
        <div class="jp-type-playlist">
            <div class="video_container">
                <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>
                <div class="jp-gui start">
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                    <div class="jp-video-play">
                        <a class="jp-video-play-icon" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrVideo" id="1" default="play"}</a>
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