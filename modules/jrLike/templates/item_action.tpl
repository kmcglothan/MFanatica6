{if $item.action_mode == 'like'}
    {jrCore_lang module="jrLike" id=15 default="Liked" assign="act"}
{else}
    {jrCore_lang module="jrLike" id=16 default="Disliked" assign="act"}
{/if}

{if $item.action_original_module == 'jrProfile'}
    <span class="action_item_desc">{$act}</span> <a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url|rawurldecode}</a>
{else}
    <span class="action_item_desc">{$act}</span> <a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url|rawurldecode}</a>'s {jrCore_lang module=$item.action_original_module id="menu"}:<br>
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
