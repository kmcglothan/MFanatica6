{if $item.action_original_module == 'jrProfile'}
    {if $item.action_mode == 'like'}
        {jrCore_lang module="jrLike" id=15 default="Liked" assign="act"}
    {else}
        {jrCore_lang module="jrLike" id=16 default="Disliked" assign="act"}
    {/if}
    <span class="action_item_desc">{$act}</span> <a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url|rawurldecode}</a>
{else}
    {jrCore_lang module=$item.action_original_module id="menu" assign="item_type"}
    {assign var="profile_url" value=$item.action_original_data.profile_url|rawurldecode}
    <span class="action_item_desc">
        {$vowels = array('a','e','i','o')}
        {if in_array(substr($item_type, 0, 1), $vowels)}
            {if $item.action_mode == 'like'}
                {jrCore_lang module="jrLike" id=23 default="Liked an %1 item created by %2:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>"}
            {else}
                {jrCore_lang module="jrLike" id=24 default="Disliked an %1 item created by %2:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>"}
            {/if}
        {else}
            {if $item.action_mode == 'like'}
                {jrCore_lang module="jrLike" id=21 default="Liked a %1 item created by %2:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>"}
            {else}
                {jrCore_lang module="jrLike" id=22 default="Disliked a %1 item created by %2:" 1=$item_type 2="<a href='`$jamroom_url`/`$item.action_original_data.profile_url`'>@`$profile_url`</a>"}
            {/if}
        {/if}
    </span>
    <br>
{/if}

{if $item.action_original_data.action_module == 'jrAction'}

    {$item.action_original_data.action_html}

{elseif isset($item.action_original_item_url)}

    <span class="action_item_title">
        <a href="{$item.action_original_item_url}">{$item.action_original_title}</a>
    </span>

{elseif isset($item.action_original_title_url)}

    <span class="action_item_title">
        {jrCore_module_url module=$item.action_original_module assign="url"}
        <a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$url}/{$item.action_original_data._item_id}/{$item.action_original_title_url}">{$item.action_original_title}</a>
    </span>

{elseif isset($item.action_original_html)}

    {* like on item detail *}
    <div class="action_item_shared">
        {$item.action_original_html}
    </div>

{/if}
