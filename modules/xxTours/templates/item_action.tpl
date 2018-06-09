{jrCore_module_url module="xxTours" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="xxTours" id="11" default="Posted a new tours"}:
    {else}
        {jrCore_lang module="xxTours" id="12" default="Updated a tours"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.tours_title_url}" title="{$item.action_data.tours_title|jrCore_entity_string}">{$item.action_data.tours_title}</a>
    </span>
</div>
