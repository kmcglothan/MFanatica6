{jrCore_module_url module="jrVideo" assign="murl"}

{if $item.action_mode == 'create'}
    {if isset($item.action_data.video_active) && $item.action_data.video_active == 'off' && isset($item.action_data.quota_jrVideo_video_conversions) && $item.action_data.quota_jrVideo_video_conversions == 'on'}
        <p class="center action_item_text">{jrCore_lang module="jrVideo" id=38 default="This video file is currently being processed and will appear here when complete."}</p>
    {else}
        <span class="action_item_desc">{jrCore_lang module="jrVideo" id=65 default="Created a new Video"}:</span><br>
        <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data._item_id}/{$item.action_data.video_title_url}">{$item.action_data.video_title}</a></span>
    {/if}

{elseif $item.action_mode == 'create_album'}
    {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.video_album_url}

{elseif $item.action_mode == 'update_album'}
    {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.video_album_url}

{else}
    {if isset($item.action_data.video_active) && $item.action_data.video_active == 'off' && isset($item.action_data.quota_jrVideo_video_conversions) && $item.action_data.quota_jrVideo_video_conversions == 'on'}
        <p class="center action_item_text">{jrCore_lang module="jrVideo" id=38 default="This video file is currently being processed and will appear here when complete."}</p>
    {else}
        {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data._item_id}/{$item.action_data.video_title_url}
    {/if}

{/if}


