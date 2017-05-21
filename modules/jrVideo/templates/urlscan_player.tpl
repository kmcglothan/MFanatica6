{if isset($video_active) && $video_active == 'off' && isset($quota_jrVideo_video_conversions) && $quota_jrVideo_video_conversions == 'on'}
    <p class="center">{jrCore_lang module="jrVideo" id="38" default="This video file is currently being processed and will appear here when complete."}</p>
{else}
    {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
    {assign var="player_type" value=$_conf.$skin_player_type}
    {assign var="player" value="jrVideo_`$player_type`"}
    {if isset($player_type) && strlen($player_type) > 0}
        {jrCore_media_player type=$player module="jrVideo" field="video_file" item_id=$_item_id autoplay=$autoplay width="100%" height="300"}
    {else}
        {jrCore_media_player module="jrVideo" field="video_file" item_id=$_item_id autoplay=$autoplay width="100%" height="300"}
    {/if}
{/if}