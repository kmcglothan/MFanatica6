{jrCore_module_url module="jrPlaylist" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrPlaylist" id="1" default="Created a new Playlist"}:
    {else}
        {jrCore_lang module="jrPlaylist" id="20" default="Updated Playlist"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.playlist_title_url}" title="{$item.action_data.playlist_title|jrCore_entity_string}">{$item.action_data.playlist_title|truncate:60:"..."}</a>
    </span>
</div>
