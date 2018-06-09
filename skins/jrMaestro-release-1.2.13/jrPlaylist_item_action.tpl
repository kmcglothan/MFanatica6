{jrCore_module_url module="jrPlaylist" assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$item._user_id
        size="icon"
        crop="auto"
        alt=$item.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>
        {if $item.action_mode == 'create'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.playlist_title_url}" title="{$item.action_data.playlist_title|jrCore_entity_string}">
                    {jrCore_lang module="jrPlaylist" id="1" default="Created a new Playlist"}.</a></span><br>
        {else}
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.playlist_title_url}" title="{$item.action_data.playlist_title|jrCore_entity_string}">
                {jrCore_lang module="jrPlaylist" id="20" default="Updated Playlist"}.<br></a><br>
        {/if}
        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
    </div>
</div>



<div class="media">
    {$_item = jrCore_db_get_item('jrPlaylist', $item.action_item_id)}
    {if isset($_item) && isset($_item._item_id)}
        {jrCore_media_player
            module="jrPlaylist"
            type="jrMaestro_playlist_action_player"
            item=$_item
            autoplay=false
        }
    {else}
        Item deleted.
    {/if}
</div>
