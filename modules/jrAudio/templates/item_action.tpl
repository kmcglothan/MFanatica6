{jrCore_module_url module="jrAudio" assign="murl"}

{if $item.action_mode == 'create'}
    {if isset($item.action_data.audio_active) && $item.action_data.audio_active == 'off' && isset($item.action_data.quota_jrAudio_audio_conversions) && $item.action_data.quota_jrAudio_audio_conversions == 'on'}
        <p class="center action_item_text">{jrCore_lang module="jrAudio" id=40 default="This audio file is currently being processed and will appear here when complete."}</p>
    {else}
        <span class="action_item_desc">{jrCore_lang module="jrAudio" id=68 default="Created a new Audio File"}:</span><br>
        <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data._item_id}/{$item.action_data.audio_title_url}">{$item.action_data.audio_title}</a></span>
    {/if}

{elseif $item.action_mode == 'create_album'}
    {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.audio_album_url}

{elseif $item.action_mode == 'update_album'}
    {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.audio_album_url}

{else}
    {if isset($item.action_data.audio_active) && $item.action_data.audio_active == 'off' && isset($item.action_data.quota_jrAudio_audio_conversions) && $item.action_data.quota_jrAudio_audio_conversions == 'on'}
        <p class="center p5">{jrCore_lang module="jrAudio" id=40 default="This audio file is currently being processed and will appear here when complete."}</p>
    {else}
        {$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data._item_id}/{$item.action_data.audio_title_url}
    {/if}

{/if}

