{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">
            <div class="block_config">
                {jrCore_item_update_button module="jrGroupDiscuss" profile_id=$item._profile_id item_id=$item._item_id}
                {jrCore_item_delete_button module="jrGroupDiscuss" profile_id=$item._profile_id item_id=$item._item_id}
            </div>
            <h2><a href="{$jamroom_url}/{$item._group_data.profile_url}/{$murl}/{$item._item_id}/{$item.discuss_title_url}">{$item.discuss_title}</a></h2>
        </div>
    {/foreach}
{/if}
