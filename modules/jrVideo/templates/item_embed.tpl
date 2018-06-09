<div style="display: block; width:100%" class="video_item_embed">
    {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
    {assign var="player_type" value=$_conf.$skin_player_type}
    {assign var="player" value="jrVideo_`$player_type`"}
    {if isset($player_type) && strlen($player_type) > 0}
        {jrCore_media_player type=$player module="jrVideo" field="video_file" item=$item autoplay=$_post.auto_play}
    {else}
        {jrCore_media_player module="jrVideo" field="video_file" item=$item autoplay=$_post.auto_play}
    {/if}
</div>
