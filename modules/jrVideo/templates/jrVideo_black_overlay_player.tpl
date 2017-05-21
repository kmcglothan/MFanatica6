<script type="text/javascript">
    $(document).ready(function() {

        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);

        new jPlayerPlaylist({
            jPlayer:"#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor:"#jp_container_{$uniqid}"
        },[
            {if isset($media) && is_array($media)}
            {jrCore_module_url module="jrVideo" assign="vurl"}
            {foreach $media as $v}
            {
                title: "{$v._item.video_title|htmlentities}",
                artist: "{$v._item.profile_name|htmlentities}",
                artist_url: "{$jamroom_url}/{$v._item.profile_url}",
                album: "{$v._item.video_album|htmlentities}",
                album_url: "{$jamroom_url}/{$v._item.profile_url}/{$vurl}/albums/{$v._item.video_album_url}",
                poster: "{$v.poster}/crop=" + tw +':'+ th,
                "{$v._item.video_file_extension}": "{$jamroom_url}/{$vurl}/stream/video_file/{$v.item_id}/key=[jrCore_media_play_key]/file.{$v._item.video_file_extension}"
                {if strstr($formats, 'm4v')}
                , m4v: "{$jamroom_url}/{$vurl}/stream/video_file_mobile/{$v.item_id}/key=[jrCore_media_play_key]/file.m4v"
                {/if}
            },
            {/foreach}
            {/if}
        ],{
            error: function(res) { jrCore_stream_url_error(res); },
            playlistOptions: {
                autoPlay: {$autoplay}
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            smoothPlayBar: true,
            keyEnabled: true,
            volume: 0.8,
            preload:'none',
            mode: 'window',
            {if jrCore_is_mobile_device()}
            autohide: { hold: 2500 },
            {else}
            autohide: { restored: true, fadein: 50, hold: 2500 },
            {/if}
            size: { width: "100%", height: th + "px" }
        });
    });
</script>

<div class="jrvideo_black_overlay_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
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
                        <a class="jp-video-play-icon" tabindex="1">{jrCore_lang module="jrVideo" id="1" default="play"}</a>
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
                                <li><a class="jp-play" tabindex="1" title="{jrCore_lang module="jrVideo" id="1" default="play"}"></a></li>
                                <li><a class="jp-pause" tabindex="1" title="{jrCore_lang module="jrVideo" id="2" default="pause"}"></a></li>
                                {if isset($_post._1) && $_post._1 == 'albums'}
                                    <li style="margin-left: 5px;"><a class="jp-previous" tabindex="1" title="{jrCore_lang module="jrVideo" id="59" default="previous"}"></a></li>
                                    <li><a class="jp-next" tabindex="1" title="{jrCore_lang module="jrVideo" id="54" default="next"}"></a></li>
                                {/if}
                            </ul>
                            {if !jrCore_is_mobile_device()}
                                <ul class="jp-controls" style="width: 16px;margin-right:6px;margin-left:5px;">
                                    <li><a class="jp-mute" tabindex="1" title="{jrCore_lang module="jrVideo" id="4" default="mute"}"></a></li>
                                    <li><a class="jp-unmute" tabindex="1" title="{jrCore_lang module="jrVideo" id="5" default="unmute"}"></a></li>
                                </ul>

                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                                <div class="jp-current-time"></div><span style="float:left;font-size:.64em;font-style:oblique;margin-top:{if $_conf.jrCore_active_skin == 'jrFlashback'}2px{else}-1px{/if};">&nbsp;&nbsp;/&nbsp;</span>
                                <div class="jp-duration"></div>
                            {/if}
                            <div class="jp-right-controls">
                                <ul class="jp-toggles">
                                    <li><a class="jp-full-screen" tabindex="1" title="{jrCore_lang module="jrVideo" id="55" default="full screen"}"></a></li>
                                    <li><a class="jp-restore-screen" tabindex="1" title="{jrCore_lang module="jrVideo" id="56" default="restore screen"}"></a></li>
                                    {if isset($_post._1) && $_post._1 == 'albums'}
                                        <li><a class="jp-shuffle" tabindex="1" title="{jrCore_lang module="jrVideo" id="57" default="shuffle"}"></a></li>
                                        <li><a class="jp-shuffle-off" tabindex="1" title="{jrCore_lang module="jrVideo" id="58" default="shuffle off"}"></a></li>
                                    {/if}
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