{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}


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
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}">{jrCore_lang module="jrFoxyCartBundle" id="40" default="Created a new Bundle"}.</a></span><br>

        {else}

            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}" title="{$item.action_data.audio_title|jrCore_entity_string}">{jrCore_lang module="jrFoxyCartBundle" id="41" default="Updated a Bundle"}.<br></a><br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
   <div class="wrap">
       <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.bundle_title_url|jrCore_entity_string}">{$item.action_data.bundle_title}</a>
   </div>
</div>

