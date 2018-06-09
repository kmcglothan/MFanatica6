{jrCore_include template="jrVideo_player_javascript_header.tpl"}

<div class="jrAudioPro_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
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
                            <ul class="jp-controls desk" id="mute-unmute">
                                <li><a class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="4" default="mute"}"></a></li>
                                <li><a class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="5" default="unmute"}"></a></li>
                            </ul>
                            <div class="jp-volume-bar desk" style="display: none;">
                                <div class="jp-volume-bar-value"></div>
                            </div>
                            <ul class="jp-toggles">
                                <li><a class="jp-settings" tabindex="{jrCore_next_tabindex}"></a>
                                    <div class="jp-control-settings" style="display: none;">
                                        <ul>
                                            <input type="checkbox" id="jp-show" /> <span>{jrCore_lang skin="jrAudioPro" id=32 default="Show Playlist"}</span><br>
                                            <input type="checkbox" id="jp-auto-play" /> <span>{jrCore_lang skin="jrAudioPro" id=39 default="Auto Play"}</span><br>
                                            <input type="checkbox" id="jp-auto-mute" /> <span>{jrCore_lang skin="jrAudioPro" id=40 default="Auto Mute"}</span>
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