{jrCore_include module="jrVideo" template="jrVideo_player_javascript_header.tpl"}

<div class="jrvideo_player_dark" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_{$uniqid}" class="jp-video jp-video-270p">
        <div class="jp-type-playlist">
            <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>
            <div class="jp-gui">
                <div class="jp-video-play">
                    <a href="javascript:" class="jp-video-play-icon" tabindex="{jrCore_next_tabindex}"></a>
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
                            <li><a href="javascript:" class="jp-previous" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="59" default="previous"}"></a></li>
                            <li><a href="javascript:" class="jp-play" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="1" default="play"}"></a></li>
                            <li><a href="javascript:" class="jp-pause" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="2" default="pause"}"></a></li>
                            <li><a href="javascript:" class="jp-next" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="54" default="next"}"></a></li>
                            <li><a href="javascript:" class="jp-stop" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="3" default="stop"}"></a></li>
                            <li><a href="javascript:" class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="4" default="mute"}"></a></li>
                            <li><a href="javascript:" class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="5" default="unmute"}"></a></li>
                            <li><a href="javascript:" class="jp-volume-max" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="6" default="max volume"}"></a></li>
                        </ul>
                        <div class="jp-volume-bar">
                            <div class="jp-volume-bar-value"></div>
                        </div>
                        <ul class="jp-toggles">
                            <li><a href="javascript:" class="jp-full-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="55" default="full screen"}"></a></li>
                            <li><a href="javascript:" class="jp-restore-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="56" default="restore screen"}"></a></li>
                            <li><a href="javascript:" class="jp-shuffle" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="57" default="shuffle"}"></a></li>
                            <li><a href="javascript:" class="jp-shuffle-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="58" default="shuffle off"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="7" default="repeat"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="8" default="repeat off"}"></a></li>
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