{jrCore_module_url module="jrVimeo" assign="murl"}

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
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_lang module="jrVimeo" id="22" default="Posted a new Vimeo Video"}.</a></span><br>

        {elseif $item.action_mode == 'search'}

            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrVimeo" id="45" default="Posted new Vimeo videos"}.</a></span><br>

        {else}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_lang module="jrVimeo" id="43" default="Updated a Vimeo Video"}.</a></span><br>

        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="media">
    {jrVimeo_embed item_id=$item.action_item_id auto_play=false width="100%" height="360"}
</div>
