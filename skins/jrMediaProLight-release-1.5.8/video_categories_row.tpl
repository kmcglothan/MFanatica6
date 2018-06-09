{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {foreach from=$_items item="item"}
        {if isset($item.video_category) && strlen($item.video_category) > 0}
            <option value="{$item.video_category}">{$item.video_category}</option>
        {/if}
    {/foreach}
{/if}
