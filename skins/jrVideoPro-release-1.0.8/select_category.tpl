{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    <select class="form_select cats" onchange="jrCore_window_location('{$jamroom_url}/{$murl}/' + this.value)">
        <option value="">{jrCore_lang skin="jrVideoPro" id=68 default="All"}</option>
        {foreach from=$_items item="item"}
            {if $_post.category == $item.video_category}
                <option selected="selected" value="category={$item.video_category|urlencode}">{$item.video_category}</option>
            {else}
                <option value="category={$item.video_category|urlencode}">{$item.video_category}</option>
            {/if}
        {/foreach}
    </select>
{else}
{/if}