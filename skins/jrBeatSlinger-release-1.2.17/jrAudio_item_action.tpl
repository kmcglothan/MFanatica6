{jrCore_module_url module="jrAudio" assign="murl"}

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
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}">{jrCore_lang module="jrAudio" id="33" default="Posted a new Audio File"}.</a></span><br>

        {elseif $item.action_mode == 'create_album'}

            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.action_data.audio_album_url}" title="{$item.action_data.audio_album|jrCore_entity_string}">{jrCore_lang module="jrAudio" id="59" default="Created a new Audio Album"}.</a></span><br>

        {elseif $item.action_mode == 'update_album'}

            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}">{jrCore_lang module="jrAudio" id="63" default="Updated an Audio Album"}.</a></span><br>

        {else}

            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}" title="{$item.action_data.audio_title|jrCore_entity_string}"> {jrCore_lang module="jrAudio" id="55" default="Updated an Audio File"}:<br></a><br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
    {if $item.action_mode == 'create_album'}
        {jrCore_media_player
        type="jrBeatSlinger_audio_action_player"
        module="jrAudio"
        field="audio_file"
        search1="_profile_id = `$item.action_data._profile_id`"
        search2="audio_album = `$item.action_data.audio_album`"
        order_by="audio_file_track numerical_asc"
        limit="24" override="action"
        autoplay=false}
    {else}
        {jrCore_media_player
        type="jrBeatSlinger_audio_action_player"
        module="jrAudio"
        field="audio_file"
        search1="_item_id = `$item.action_item_id`"
        limit="1"
        autoplay=false}
    {/if}
</div>