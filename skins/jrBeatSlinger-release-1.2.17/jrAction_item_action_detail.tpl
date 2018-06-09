{if strlen($item.action_html) > 0}

    {$item.action_html}

{else}
{jrCore_module_url module="jrAction" assign="murl"}
<div class="action" style="margin:0">
    <div class="action_info">
        <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name}
        </div>
        <div class="action_data">
            <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">Posted an update</a></span><br>
            <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
        </div>
    </div>
    <div class="wrap">
        {if isset($item.action_text)}
            {$item.action_text|jrCore_format_string:$item.profile_quota_id}
        {/if}
    </div>
</div>

{/if}