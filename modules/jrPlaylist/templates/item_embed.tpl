<div style="display: inline-block; width:100%;max-width: 800px">
    {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
    {assign var="player_type" value=$_conf.$skin_player_type}
    {assign var="player" value="jrPlaylist_`$player_type`"}
    {if isset($player_type) && strlen($player_type) > 0}
        {jrCore_media_player type=$player module="jrPlaylist" items=$item.playlist_items}
    {else}
        {jrCore_media_player module="jrPlaylist" items=$item.playlist_items}
    {/if}
</div>
