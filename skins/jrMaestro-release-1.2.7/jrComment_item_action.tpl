{jrCore_module_url module=$item.action_original_module assign="murl"}
<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" class="img_scale" alt=$item.user_name}
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
        {jrCore_lang skin="jrMaestro" id=129 default="commented on"}
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url}'s</a></span>
        {if $item.action_data.comment_module == 'jrProfile'}
            {jrCore_lang module="jrProfile" id=2 default="Profile"}
        {else}
            {jrCore_lang module=$item.action_original_module id="menu"}
        {/if}
        <br>
        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
    </div>
</div>

<div class="item_media">
    {if isset($item.action_data.comment_text)}
        <div class="action_text clearfix">
            <div style="padding: 1em">
                {$item.action_data.comment_text|jrCore_format_string:$item.profile_quota_id}
            </div>
        </div>
    {/if}

    {if isset($item.action_original_title)}
        {jrCore_module_url module=$item.action_original_module assign="curl"}
        {if jrCore_module_is_active('jrUrlScan') && is_file("{$jamroom_dir}/modules/{$item.action_original_module}/templates/urlscan_player.tpl")}
            {$item_url = "{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}/{$item.action_original_title_url}"}
            {$item_url|jrUrlScan_replace_urls}
        {else}
            <div class="wrap">
                <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}/{$item.action_original_title_url}#comment_section">{$item.action_original_title}</a></span>
            </div>
        {/if}
    {/if}
</div>
