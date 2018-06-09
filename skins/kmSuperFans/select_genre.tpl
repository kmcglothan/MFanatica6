{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <select class="form_select cats" onchange="jrCore_window_location('{$jamroom_url}/{$murl}/' + this.value)">
        <option value="">{jrCore_lang skin="kmSuperFans" id=66 default="All"}</option>
        {foreach from=$_items item="item"}
            {if $_post.genre == $item.audio_genre}
                <option selected="selected" value="genre={$item.audio_genre|urlencode}">{$item.audio_genre}</option>
            {else}
                <option value="genre={$item.audio_genre|urlencode}">{$item.audio_genre}</option>
            {/if}
        {/foreach}
    </select>
{else}
{/if}