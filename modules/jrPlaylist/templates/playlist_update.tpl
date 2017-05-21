{* take all the types of possible playlists and display them, if they exist in this playlist, check the checkbox.*}
{if is_array($playlist_possilbles)}
    {foreach $playlist_possilbles as $list}
    <h2>{$list.heading}</h2><br>
        {if is_array($list.items)}
            {foreach $list.items as $item}
                {if in_array($item._item_id, $checked[$list.module])}
                    <label><input name="playlist_{$list.module}[]" type="checkbox" value="{$item._item_id}" checked="checked">{$item.title}</label><br>
                {else}
                    {*<label><input name="playlist_{$list.module}[]" type="checkbox" value="{$item._item_id}">{$item.title}</label><br>*}
                {/if}
            {/foreach}
        {/if}
    {/foreach}
{/if}