{jrCore_module_url module="jrGroupPage" assign="murl"}
{if isset($_post.option) && $_post.option > 0}
    {if isset($_items)}
        {foreach from=$_items item="item"}
            <div class="item">
                <h2><a href="{$jamroom_url}/{$item._group_data.profile_url}/{$murl}/{$item._item_id}/{$item.npage_title_url}">{$item.npage_title}</a></h2>
                <br><br>
                {$item.npage_body}
            </div>
        {/foreach}
    {/if}
{else}
    {if isset($_items)}
        {foreach from=$_items item="item"}
            <div class="item">
                <div class="block_config">
                    {jrCore_item_list_buttons module="jrGroupPage" item=$item}
                </div>
                <h2><a href="{$jamroom_url}/{$item._group_data.profile_url}/{$murl}/{$item._item_id}/{$item.npage_title_url}">{$item.npage_title}</a></h2>
                <br>
                {$item.npage_body|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:280}
            </div>
        {/foreach}
    {/if}
{/if}
