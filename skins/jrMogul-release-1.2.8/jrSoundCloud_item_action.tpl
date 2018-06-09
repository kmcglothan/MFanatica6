{jrCore_module_url module="jrSoundCloud" assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.action_data.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$item.action_data._user_id
        size="icon"
        crop="auto"
        alt=$item.action_data.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item.action_data._profile_id item_id=$item.action_data._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_data.profile_url}" title="{$item.action_data.profile_name|jrCore_entity_string}">{$item.action_data.profile_url}</a></span>

        <span class="action_desc"><a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data._item_id}/{$item.action_data.page_title_url}">{jrCore_lang module="jrSoundCloud" id="62" default="Posted new SoundCloud tracks"}.</a></span><br>

        <span class="action_time">{$item.action_data._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
    {jrSoundCloud_embed item_id=$item.action_item_id auto_play=false}
</div>

