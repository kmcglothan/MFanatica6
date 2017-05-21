{jrCore_module_url module="jrStore" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item['action_mode'] == 'create'}
        {jrCore_lang module="jrStore" id="18" default="Created a new Product"}:
    {else}
        {jrCore_lang module="jrStore" id="126" default="Updated a Product"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.product_title_url}" title="{$item.action_data.product_title|jrCore_entity_string}">{$item.action_data.product_title}</a>
    </span>
</div>
