{if $item.action_data.rating_module == "jrProfile" || $item.action_data.rating_module == 'jrUser'}

    <span class="action_item_desc">{jrCore_lang module="jrRating" id=11 default="Rated with a"} &quot;{$item.action_data.rating_value}&quot;:</span><br>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.rating_profile_url}">@{$item.action_data.rating_title}</a></span>

{else}
    {jrCore_lang module=$item.action_original_module id="menu" assign="item_type"}
    {assign var="profile_url" value=$item.action_original_data.profile_url|rawurldecode}
    <span class="action_item_desc">
        {$vowels = array('a','e','i','o')}
        {if in_array(substr($item_type, 0, 1), $vowels)}
            {jrCore_lang module="jrRating" id=16 default="Rated an %1 item created by %2 with a %3:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>" 3=$item.action_data.rating_value}
        {else}
            {jrCore_lang module="jrRating" id=17 default="Rated a %1 item created by %2 with a %3:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>" 3=$item.action_data.rating_value}
        {/if}
    </span>
    <br>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.rating_profile_url}/{jrCore_module_url module=$item.action_data.rating_module}/{$item.action_data.rating_item_id}">{$item.action_data.rating_title}</a></span>





{***
 {jrCore_module_url module="jrPlaylist" assign="murl"}
    {jrCore_module_url module=$item.action_data.rating_module assign="murl"}
    <span class="action_item_desc">{jrCore_lang module="jrRating" id=16 default="Rated"} <a href="{$jamroom_url}/{$item.action_data.rating_profile_url}">@{$item.action_data.rating_profile_url}</a>'s {jrCore_lang module=$item.action_data.rating_module id="menu"} {jrCore_lang module="jrRating" id=17 default="a"} &quot;{$item.action_data.rating_value}&quot;:</span><br>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_data.rating_profile_url}/{$murl}/{$item.action_data.rating_item_id}">{$item.action_data.rating_title}</a></span>
***}
{/if}
