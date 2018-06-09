{jrCore_include module="jrVideo" template="jrVideo_player_javascript_header.tpl"}

{* this background-color must be inline or it does not work on Chrome *}
<div class="jrvideo_black_overlay_player" style="opacity:0.01;background-color:#000000" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
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
                        <a class="jp-video-play-icon" tabindex="{jrCore_next_tabindex}">{jrCore_lang module="jrVideo" id="1" default="play"}</a>
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
                                <li><a class="jp-play" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="1" default="play"}"></a></li>
                                <li><a class="jp-pause" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="2" default="pause"}"></a></li>
                                {if isset($_post._1) && $_post._1 == 'albums'}
                                    <li style="margin-left: 5px;"><a class="jp-previous" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="59" default="previous"}"></a></li>
                                    <li><a class="jp-next" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="54" default="next"}"></a></li>
                                {/if}
                            </ul>
                            {if !jrCore_is_mobile_device()}
                                <ul class="jp-controls" style="width: 16px;margin-right:6px;margin-left:5px;">
                                    <li><a class="jp-mute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="4" default="mute"}"></a></li>
                                    <li><a class="jp-unmute" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="5" default="unmute"}"></a></li>
                                </ul>

                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                                <div class="jp-current-time"></div><span style="float:left;font-size:.64em;font-style:oblique;margin-top:{if $_conf.jrCore_active_skin == 'jrFlashback'}2px{else}-1px{/if};">&nbsp;&nbsp;/&nbsp;</span>
                                <div class="jp-duration"></div>
                            {/if}
                            <div class="jp-right-controls">
                                <ul class="jp-toggles">
                                    <li><a class="jp-full-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="55" default="full screen"}"></a></li>
                                    <li><a class="jp-restore-screen" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="56" default="restore screen"}"></a></li>
                                    {if isset($_post._1) && $_post._1 == 'albums'}
                                        <li><a class="jp-shuffle" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="57" default="shuffle"}"></a></li>
                                        <li><a class="jp-shuffle-off" tabindex="{jrCore_next_tabindex}" title="{jrCore_lang module="jrVideo" id="58" default="shuffle off"}"></a></li>
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