{if $item.action_data.rating_module == "jrProfile" || $item.action_data.rating_module == 'jrUser'}

    <span class="action_item_desc">{jrCore_lang module="jrRating" id=11 default="Rated with a"} &quot;{$item.action_data.rating_value}&quot;:</span><br>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.rating_profile_url}">@{$item.action_data.rating_title}</a></span>

{else}

    {jrCore_module_url module=$item.action_data.rating_module assign="murl"}
    <span class="action_item_desc">{jrCore_lang module="jrRating" id=16 default="Rated"} <a href="{$jamroom_url}/{$item.action_data.rating_profile_url}">@{$item.action_data.rating_profile_url}</a>'s {jrCore_lang module=$item.action_data.rating_module id="menu"} {jrCore_lang module="jrRating" id=17 default="a"} &quot;{$item.action_data.rating_value}&quot;:</span><br>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.rating_profile_url}/{$murl}/{$item.action_data.rating_item_id}">{$item.action_data.rating_title}</a></span>

{/if}
