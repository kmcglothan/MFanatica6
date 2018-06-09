{jrCore_module_url module="jrGuestBook" assign="murl"}
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

        {if isset($item.action_data.guestbook_profile_url)}
            {jrCore_lang module="jrGuestBook" id="24" default="Signed"} <a href="{$jamroom_url}/{$item.action_data.guestbook_profile_url}/{$murl}">@{$item.action_data.guestbook_profile_name}'s</a> {jrCore_lang module="jrGuestBook" id="20" default="Guestbook"}. <br>
        {else}
            {jrCore_lang module="jrGuestBook" id="1" default="Posted a new guestbook entry"}. <br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
   <div class="wrap">
       {$item.action_data.guestbook_text|jrCore_strip_html|truncate:160}
   </div>
</div>
