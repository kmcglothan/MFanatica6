{jrCore_module_url module=$item.action_data.rating_module assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" class="img_scale" alt=$item.user_name}
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
        {jrCore_lang skin="jrISkin" id=152 default="rated"} <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url}'s</a></span>
        {jrCore_lang module=$item.action_data.rating_module id="menu"} {jrCore_lang skin="jrISkin" id=153 default="with a"} &quot;{$item.action_data.rating_value}&quot;
        <br>
        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="item_media action_detail">
    <div class="wrap">
        {$item.action_original_html|jrUrlScan_replace_urls}
    </div>
</div>
