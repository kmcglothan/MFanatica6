{if isset($_items)}
    {foreach from=$_items item="item"}
        {if strlen($item.audio_genre_0) > 1 && $item.audio_genre_0 != 'All Genres'}
            {if $_post.option == $item.audio_genre_0}
                <option selected="selected" value="{$item.audio_genre_0|urlencode}">{$item.audio_genre_0}</option>
            {else}
                <option value="{$item.audio_genre_0|urlencode}">{$item.audio_genre_0}</option>
            {/if}
        {/if}
    {/foreach}
{/if}
