{jrCore_module_url module="jrPlaylist" assign="murl"}
{jrCore_module_url module="jrPlaylist" assign="murl"}
<div class="block">
    <div id="jrplaylist_title">
        <div class="block_config">
            <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}'">{jrCore_icon icon="gear"}</a>
        </div>
    </div>
</div>

{if jrCore_module_is_active('jrMediaPlayer')}
    {jrCore_module_url module="jrMediaPlayer" assign="mpurl"}
    <div class="block">
        {jrMediaPlayer_player type="media_blue_monday" playlist=$item.playlist_items playlist_id=$item._item_id}
    </div>
{/if}
